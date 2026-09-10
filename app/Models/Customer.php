<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'consumer_id', 'name', 'photo', 'mobile', 'alternative_mobile', 'email',
        'address', 'village_id', 'area_id', 'city', 'state', 'pincode', 'route_id',
        'milk_type', 'morning_rate', 'evening_rate', 'default_qty_morning', 'default_qty_evening',
        'customer_category_id', 'status', 'notes', 'joining_date',
        'identity_proof_type', 'identity_proof_number', 'qr_code_path', 'barcode',
        'opening_balance', 'advance_balance', 'outstanding_balance', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'morning_rate' => 'decimal:2',
            'evening_rate' => 'decimal:2',
            'default_qty_morning' => 'decimal:3',
            'default_qty_evening' => 'decimal:3',
            'opening_balance' => 'decimal:2',
            'advance_balance' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->consumer_id)) {
                $customer->consumer_id = static::generateConsumerId();
            }
            if (empty($customer->barcode)) {
                $customer->barcode = $customer->consumer_id;
            }
        });
    }

    /**
     * Generate a unique, sequential consumer ID e.g. MILK00001.
     * Uses a DB lock on the last record to avoid race conditions under concurrent inserts.
     */
    public static function generateConsumerId(): string
    {
        $prefix = setting('consumer_prefix', 'MILK');

        return \DB::transaction(function () use ($prefix) {
            $last = static::withTrashed()
                ->where('consumer_id', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $nextNumber = 1;
            if ($last && preg_match('/(\d+)$/', $last->consumer_id, $m)) {
                $nextNumber = ((int) $m[1]) + 1;
            }

            return $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    // Relationships
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dailyEntries(): HasMany
    {
        return $this->hasMany(DailyEntry::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('consumer_id', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('mobile', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%");
        });
    }

    public function rateForShift(string $shift): float
    {
        return $shift === 'evening' ? (float) $this->evening_rate : (float) $this->morning_rate;
    }

    public function defaultQtyForShift(string $shift): float
    {
        return $shift === 'evening' ? (float) $this->default_qty_evening : (float) $this->default_qty_morning;
    }
}
