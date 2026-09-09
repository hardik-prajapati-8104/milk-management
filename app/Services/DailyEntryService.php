<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\DailyEntry;
use App\Models\MilkRate;
use App\Models\MilkStockLedger;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DailyEntryService
{
    /**
     * Days older than this are considered "locked" automatically unless the
     * entry is already billed (in which case it's locked regardless of age).
     */
    protected const AUTO_LOCK_AFTER_DAYS = 2;

    public function __construct(protected MilkStockService $stock)
    {
    }

    /**
     * Upsert a full grid of entries for one date + shift in a single transaction.
     * One row per customer; a row with is_absent/is_holiday zeroes the quantity.
     * Rate is auto-resolved from the active MilkRate card unless the customer
     * has a custom per-customer rate (customers.morning_rate / evening_rate),
     * which — when set above zero — always takes precedence.
     *
     * Every non-zero quantity is also posted to the shared milk stock ledger as
     * a "card_sale" outgoing movement (spec: Card Customer sales decrement the
     * same stock pool that Cash and Dairy sales draw from). Each row is wrapped
     * in its own nested transaction (a DB savepoint) so a single customer
     * hitting insufficient stock only blocks that customer, not the whole batch.
     *
     * @param  array<int, array{customer_id:int, quantity?:float, is_absent?:bool, is_holiday?:bool, remarks?:string}>  $rows
     * @return array{created:int, updated:int, blocked:array<int,string>}
     */
    public function bulkSave(string $date, string $shift, array $rows): array
    {
        $created = 0;
        $updated = 0;
        $blocked = [];

        DB::transaction(function () use ($date, $shift, $rows, &$created, &$updated, &$blocked) {
            $customerIds = array_column($rows, 'customer_id');
            $customers = Customer::whereIn('id', $customerIds)->get()->keyBy('id');

            $existing = DailyEntry::where('entry_date', $date)
                ->where('shift', $shift)
                ->whereIn('customer_id', $customerIds)
                ->get()
                ->keyBy('customer_id');

            foreach ($rows as $row) {
                $customer = $customers->get($row['customer_id']);
                if (! $customer) {
                    continue;
                }

                $existingEntry = $existing->get($row['customer_id']);

                if ($existingEntry && $existingEntry->is_locked && ! (auth()->user()?->hasRole('Admin'))) {
                    $blocked[] = $customer->name;
                    continue;
                }

                $isAbsent = (bool) ($row['is_absent'] ?? false);
                $isHoliday = (bool) ($row['is_holiday'] ?? false);
                $quantity = $isAbsent || $isHoliday ? 0 : (float) ($row['quantity'] ?? 0);

                $rate = $this->resolveRateForCustomer($customer, $date, $shift);

                $payload = [
                    'milk_type' => $customer->milk_type,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'is_absent' => $isAbsent,
                    'is_holiday' => $isHoliday,
                    'is_manual_override' => false,
                    'remarks' => $row['remarks'] ?? null,
                    'updated_by' => auth()->id(),
                ];

                try {
                    DB::transaction(function () use ($existingEntry, $payload, $customer, $date, $shift, &$created, &$updated) {
                        if ($existingEntry) {
                            $existingEntry->update($payload);
                            $this->syncCardSaleStock($existingEntry->fresh());
                            $updated++;
                        } else {
                            $entry = DailyEntry::create($payload + [
                                'customer_id' => $customer->id,
                                'entry_date' => $date,
                                'shift' => $shift,
                                'entered_by' => auth()->id(),
                            ]);
                            $this->syncCardSaleStock($entry);
                            $created++;
                        }
                    });
                } catch (\RuntimeException $e) {
                    $blocked[] = "{$customer->name} ({$e->getMessage()})";
                }
            }
        });

        ActivityLog::record('bulk-save', description: "Daily entries saved for {$date} ({$shift}): {$created} created, {$updated} updated");

        return compact('created', 'updated', 'blocked');
    }

    /**
     * Keep the shared milk stock ledger in sync with a card customer's entry:
     * posts the delta between what's already been deducted for this entry and
     * its current quantity. A larger quantity posts an additional "card_sale"
     * outgoing movement (validated against available stock); a smaller one
     * (e.g. corrected downward) posts a small "adjustment" crediting the
     * difference back, so edits never leave the ledger out of sync.
     *
     * @throws \RuntimeException when the additional quantity exceeds available stock
     */
    protected function syncCardSaleStock(DailyEntry $entry): void
    {
        $alreadyPosted = MilkStockLedger::where('source_type', DailyEntry::class)
            ->where('source_id', $entry->id)
            ->get()
            ->sum(fn (MilkStockLedger $l) => $l->direction === 'out' ? (float) $l->quantity : -(float) $l->quantity);

        $delta = round((float) $entry->quantity - $alreadyPosted, 3);

        if (abs($delta) < 0.0005) {
            return;
        }

        if ($delta > 0) {
            $this->stock->recordMovement(
                code: MilkStockLedger::generateOutgoingNo(),
                date: $entry->entry_date->toDateString(),
                shift: $entry->shift,
                direction: 'out',
                movementType: 'card_sale',
                quantity: $delta,
                rate: (float) $entry->rate,
                source: $entry,
                remarks: "Card sale to {$entry->customer->name}",
            );
        } else {
            $this->stock->recordMovement(
                code: 'ADJ-DE' . $entry->id . '-' . now()->format('YmdHis'),
                date: $entry->entry_date->toDateString(),
                shift: $entry->shift,
                direction: 'in',
                movementType: 'adjustment',
                quantity: abs($delta),
                rate: (float) $entry->rate,
                source: $entry,
                remarks: "Card sale quantity corrected downward for {$entry->customer->name}",
            );
        }
    }

    /**
     * Custom per-customer rate wins if set above zero; otherwise fall back
     * to the dated rate card resolved for the customer's milk type + shift.
     */
    public function resolveRateForCustomer(Customer $customer, string $date, string $shift): float
    {
        $customRate = $customer->rateForShift($shift);
        if ($customRate > 0) {
            return $customRate;
        }

        return MilkRate::resolveRate($date, $customer->milk_type, $shift);
    }

    /**
     * Copy the previous day's quantities (same shift) into today's grid for
     * customers who don't yet have an entry today. Returns the number copied.
     * Each copied row is posted to the stock ledger like any other card sale;
     * a customer whose copied quantity would oversell available stock is
     * simply skipped (not force-copied), since stock may differ day to day.
     */
    public function copyPreviousDay(string $date, string $shift): int
    {
        $previousDate = date('Y-m-d', strtotime($date . ' -1 day'));

        $previousEntries = DailyEntry::where('entry_date', $previousDate)
            ->where('shift', $shift)
            ->where('is_absent', false)
            ->where('is_holiday', false)
            ->get();

        $alreadyEntered = DailyEntry::where('entry_date', $date)
            ->where('shift', $shift)
            ->pluck('customer_id')
            ->flip();

        $count = 0;

        foreach ($previousEntries as $prev) {
            if ($alreadyEntered->has($prev->customer_id)) {
                continue;
            }

            try {
                DB::transaction(function () use ($prev, $date, $shift, &$count) {
                    $entry = DailyEntry::create([
                        'customer_id' => $prev->customer_id,
                        'entry_date' => $date,
                        'shift' => $shift,
                        'milk_type' => $prev->milk_type,
                        'quantity' => $prev->quantity,
                        'rate' => $prev->rate,
                        'entered_by' => auth()->id(),
                    ]);

                    $this->syncCardSaleStock($entry);
                    $count++;
                });
            } catch (\RuntimeException $e) {
                // Insufficient stock for this customer's copied quantity — skip and move on.
                continue;
            }
        }

        return $count;
    }

    /**
     * Build the green/yellow/red completion status for a calendar month view.
     * Green = every active customer has both shifts entered; yellow = partial;
     * red = nothing entered that day.
     *
     * @return array<string, string> date => status
     */
    public function monthCompletionStatus(int $year, int $month): array
    {
        $totalActiveCustomers = Customer::active()->count();
        if ($totalActiveCustomers === 0) {
            return [];
        }

        $counts = DailyEntry::selectRaw('entry_date, shift, COUNT(DISTINCT customer_id) as cnt')
            ->whereYear('entry_date', $year)
            ->whereMonth('entry_date', $month)
            ->groupBy('entry_date', 'shift')
            ->get()
            ->groupBy(fn ($row) => $row->entry_date->format('Y-m-d'));

        $status = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $dayRows = $counts->get($dateStr, collect());

            $morning = $dayRows->firstWhere('shift', 'morning')?->cnt ?? 0;
            $evening = $dayRows->firstWhere('shift', 'evening')?->cnt ?? 0;

            if ($morning === 0 && $evening === 0) {
                $status[$dateStr] = 'red';
            } elseif ($morning >= $totalActiveCustomers && $evening >= $totalActiveCustomers) {
                $status[$dateStr] = 'green';
            } else {
                $status[$dateStr] = 'yellow';
            }
        }

        return $status;
    }
}
