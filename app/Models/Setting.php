<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('app_settings');
        });

        static::deleted(function () {
            Cache::forget('app_settings');
        });
    }

    /**
     * Get all application settings.
     */
    public static function allSettings(): Collection
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return static::query()
                ->pluck('value', 'key')
                ->toArray();
        });

        return collect($settings);
    }

    /**
     * Get a setting value by key.
     */
    public static function getValue(
        string $key,
        mixed $default = null
    ): mixed {
        return static::allSettings()->get($key, $default);
    }

    /**
     * Create or update a setting.
     */
    public static function setValue(
        string $key,
        mixed $value,
        string $group = 'general',
        string $type = 'text'
    ): void {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
            ]
        );

        Cache::forget('app_settings');
    }
}