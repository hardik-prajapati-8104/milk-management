<?php

namespace App\Console\Commands;

use App\Models\DailyEntry;
use Illuminate\Console\Command;

class LockPastDailyEntries extends Command
{
    protected $signature = 'daily-entries:lock-past {--days=2 : Entries older than this many days get locked}';

    protected $description = 'Lock daily entries older than N days so only Admins can edit them (data integrity safeguard).';

    public function handle(): int
    {
        $cutoff = now()->subDays((int) $this->option('days'))->toDateString();

        $count = DailyEntry::where('entry_date', '<', $cutoff)
            ->where('is_locked', false)
            ->update(['is_locked' => true]);

        $this->info("Locked {$count} daily entries older than {$cutoff}.");

        return self::SUCCESS;
    }
}
