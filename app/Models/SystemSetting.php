<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description'
    ];

    public static function getByKey(string $key, $default = null)
    {
        $setting = Cache::rememberForever("setting.{$key}", function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        switch ($setting->type) {
            case 'boolean':
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $setting->value;
            case 'json':
                return json_decode($setting->value, true);
            default:
                return $setting->value;
        }
    }

    public static function setByKey(string $key, $value, string $type = 'string', string $description = null)
    {
        $valStr = is_array($value) ? json_encode($value) : (string) $value;

        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $valStr,
                'type' => $type,
                'description' => $description
            ]
        );

        Cache::forget("setting.{$key}");

        return $setting;
    }
}
