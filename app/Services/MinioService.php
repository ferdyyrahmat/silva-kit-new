<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MinioService
{
    /**
     * Get active MinIO / S3 configurations from system_settings.
     */
    public function getSettings(): array
    {
        return [
            'enabled'                 => (bool) SystemSetting::getByKey('minio_enabled', false),
            'endpoint'                => SystemSetting::getByKey('minio_endpoint', 'http://127.0.0.1:9000'),
            'key'                     => SystemSetting::getByKey('minio_key', ''),
            'secret'                  => SystemSetting::getByKey('minio_secret', ''),
            'region'                  => SystemSetting::getByKey('minio_region', 'us-east-1'),
            'bucket'                  => SystemSetting::getByKey('minio_bucket', 'silva-kit'),
            'use_path_style_endpoint' => (bool) SystemSetting::getByKey('minio_use_path_style_endpoint', true),
        ];
    }

    /**
     * Save MinIO configurations to system_settings.
     */
    public function saveSettings(array $data): void
    {
        SystemSetting::setByKey('minio_enabled', !empty($data['minio_enabled']));
        SystemSetting::setByKey('minio_endpoint', $data['minio_endpoint'] ?? 'http://127.0.0.1:9000');
        SystemSetting::setByKey('minio_key', $data['minio_key'] ?? '');
        if (!empty($data['minio_secret'])) {
            SystemSetting::setByKey('minio_secret', $data['minio_secret']);
        }
        SystemSetting::setByKey('minio_region', $data['minio_region'] ?? 'us-east-1');
        SystemSetting::setByKey('minio_bucket', $data['minio_bucket'] ?? 'silva-kit');
        SystemSetting::setByKey('minio_use_path_style_endpoint', !empty($data['minio_use_path_style_endpoint']));
    }

    /**
     * Get dynamic Filesystem Disk (MinIO S3 or Local fallback).
     */
    public function getDisk(): Filesystem
    {
        $s = $this->getSettings();

        if ($s['enabled'] && !empty($s['key']) && !empty($s['bucket'])) {
            try {
                return Storage::build([
                    'driver'                  => 's3',
                    'key'                     => $s['key'],
                    'secret'                  => $s['secret'],
                    'region'                  => $s['region'],
                    'bucket'                  => $s['bucket'],
                    'endpoint'                => $s['endpoint'],
                    'use_path_style_endpoint' => $s['use_path_style_endpoint'],
                    'throw'                   => false,
                ]);
            } catch (\Throwable $e) {
                Log::error("MinIO Storage Build Exception: " . $e->getMessage());
            }
        }

        // Fallback to local storage disk
        return Storage::disk('local');
    }
}
