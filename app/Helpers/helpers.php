<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get a settings value with a fallback default.
     * Cached in-memory for the request lifecycle via Setting::all().
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }
}

if (! function_exists('money')) {
    /**
     * Format a numeric value as currency using the configured currency symbol.
     */
    function money(float|int|string|null $amount): string
    {
        $symbol = setting('currency_symbol', '₹');

        return $symbol . number_format((float) $amount, 2);
    }
}

if (! function_exists('current_shift')) {
    /**
     * Guess the "current" milk shift based on the time of day (before/after noon).
     * Used as a UI default when opening the daily entry screen.
     */
    function current_shift(): string
    {
        return now()->hour < 12 ? 'morning' : 'evening';
    }
}
