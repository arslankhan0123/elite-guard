<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key with caching.
     */
    public static function get(string $key, $default = null)
    {
        try {
            return Cache::remember('setting_' . $key, 86400, function () use ($key, $default) {
                $setting = static::where('key', $key)->first();
                return $setting !== null && $setting->value !== null ? $setting->value : $default;
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set a setting value by key and refresh cache.
     */
    public static function set(string $key, $value): static
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('setting_' . $key);
        Cache::put('setting_' . $key, $value, 86400);

        return $setting;
    }
}
