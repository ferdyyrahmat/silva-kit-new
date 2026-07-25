<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class SystemHealthService
{
    /**
     * Get real-time server health stats.
     */
    public function getHealthMetrics(): array
    {
        // DB Latency check
        $dbStart = microtime(true);
        $dbStatus = 'Healthy';
        $dbLatency = 0;
        try {
            DB::select('SELECT 1');
            $dbLatency = round((microtime(true) - $dbStart) * 1000, 2);
        } catch (\Exception $e) {
            $dbStatus = 'Unhealthy: ' . $e->getMessage();
        }

        // Redis Engine check
        $redisStatus = 'Connected';
        $redisLatency = 0;
        try {
            $redisStart = microtime(true);
            Redis::connection()->ping();
            $redisLatency = round((microtime(true) - $redisStart) * 1000, 2);
        } catch (\Throwable $e) {
            $redisStatus = 'Disabled / Offline';
        }

        // Failed Queue Jobs count
        $failedJobsCount = 0;
        try {
            $failedJobsCount = DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            $failedJobsCount = 0;
        }

        // MinIO / Storage connectivity
        $storageStatus = 'Connected';
        try {
            Storage::disk(config('filesystems.default', 'local'))->exists('health-check.txt');
        } catch (\Exception $e) {
            $storageStatus = 'Error: ' . $e->getMessage();
        }

        // RAM Memory Usage
        $memoryUsageMB = round(memory_get_usage(true) / 1024 / 1024, 2);
        $memoryPeakMB  = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        // Disk Usage
        $diskFreeBytes  = @disk_free_space(base_path()) ?: 0;
        $diskTotalBytes = @disk_total_space(base_path()) ?: 1;
        $diskUsedBytes  = $diskTotalBytes - $diskFreeBytes;
        $diskUsedPercent = round(($diskUsedBytes / $diskTotalBytes) * 100, 1);

        $diskFreeGB  = round($diskFreeBytes / 1024 / 1024 / 1024, 2);
        $diskTotalGB = round($diskTotalBytes / 1024 / 1024 / 1024, 2);

        // PHP Version & Environment
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();

        return [
            'db_status'         => $dbStatus,
            'db_latency_ms'     => $dbLatency,
            'redis_status'      => $redisStatus,
            'redis_latency_ms'  => $redisLatency,
            'failed_jobs_count' => $failedJobsCount,
            'storage_status'    => $storageStatus,
            'memory_used_mb'    => $memoryUsageMB,
            'memory_peak_mb'    => $memoryPeakMB,
            'disk_used_percent' => $diskUsedPercent,
            'disk_free_gb'      => $diskFreeGB,
            'disk_total_gb'     => $diskTotalGB,
            'php_version'       => $phpVersion,
            'laravel_version'   => $laravelVersion,
        ];
    }
}
