<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
        'type'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Získa hodnotu nastavenia
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Nastaví hodnotu nastavenia
     */
    public static function set(string $key, $value, string $description = null, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description ?? $key,
                'type' => $type
            ]
        );

        // Vyčistí cache
        Cache::forget("setting_{$key}");
    }

    /**
     * Získa všetky nastavenia ako array
     */
    public static function getAllSettings(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Vyčistí cache pre nastavenia
     */
    public static function clearCache(): void
    {
        Cache::forget('all_settings');
        // Vyčistí všetky individuálne cache kľúče
        foreach (static::pluck('key') as $key) {
            Cache::forget("setting_{$key}");
        }
    }
} 