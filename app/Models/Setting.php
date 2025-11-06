<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value by key
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $type
     * @param string|null $description
     * @return bool
     */
    public static function set(string $key, $value, ?string $type = null, ?string $description = null): bool
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type ?? 'string',
                'description' => $description,
            ]
        );

        // Clear cache
        Cache::forget("setting_{$key}");

        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    /**
     * Check if maintenance mode is enabled
     *
     * @return bool
     */
    public static function isMaintenanceMode(): bool
    {
        return self::get('maintenance_mode', false) === true;
    }

    /**
     * Get maintenance ETA
     *
     * @return string
     */
    public static function getMaintenanceETA(): string
    {
        return self::get('maintenance_eta', '00:00');
    }

    /**
     * Get maintenance message
     *
     * @return string
     */
    public static function getMaintenanceMessage(): string
    {
        return self::get('maintenance_message', 'The system is currently under maintenance.');
    }

    /**
     * Cast value to appropriate type
     *
     * @param string $value
     * @param string $type
     * @return mixed
     */
    private static function castValue(string $value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public static function clearCache(): void
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
    }
}
