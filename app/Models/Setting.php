<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
            return Cache::remember(static::cacheKey($key), 86400, function () use ($key, $default) {
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

        Cache::forget(static::cacheKey($key));
        Cache::put(static::cacheKey($key), $value, 86400);

        return $setting;
    }

    private static function cacheKey(string $key): string
    {
        $connection = DB::connection();
        $scope = $connection->getName() . ':' . $connection->getDatabaseName();

        return 'setting_' . sha1($scope) . '_' . $key;
    }
}
