<?php

namespace App\Http\Controllers\System\Queue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class QueueManagerController extends Controller
{
    public function index()
    {
        $failedJobs = [];
        try {
            $failedJobs = DB::table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            $failedJobs = collect([]);
        }

        $redisStatus = 'Offline';
        try {
            Redis::connection()->ping();
            $redisStatus = 'Connected';
        } catch (\Throwable $e) {
            $redisStatus = 'Disabled / Offline';
        }

        $queueConnection = config('queue.default', 'sync');

        return view('admin.queues.index', compact('failedJobs', 'redisStatus', 'queueConnection'));
    }

    public function retryJob(string $id)
    {
        try {
            Artisan::call('queue:retry', ['id' => [$id]]);
            audit_log('Retried failed queue job #' . $id, 'update', 'queue');
            return response()->json([
                'success'  => true,
                'message'  => 'Queue job queued for retry!',
                'redirect' => route('admin.queues.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message'  => $e->getMessage(),
                'redirect' => route('admin.queues.index')
            ], 500);
        }
    }

    public function deleteJob(string $id)
    {
        try {
            DB::table('failed_jobs')->where('id', $id)->delete();
            audit_log('Deleted failed queue job #' . $id, 'delete', 'queue');
            return response()->json([
                'success'  => true,
                'message'  => 'Failed job deleted.',
                'redirect' => route('admin.queues.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message'  => $e->getMessage(),
                'redirect' => route('admin.queues.index')
            ], 500);
        }
    }

    public function purgeAll()
    {
        try {
            Artisan::call('queue:flush');
            audit_log('Purged all failed queue jobs', 'delete', 'queue');
            return response()->json([
                'success'  => true,
                'message'  => 'All failed jobs purged successfully.',
                'redirect' => route('admin.queues.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message'  => $e->getMessage(),
                'redirect' => route('admin.queues.index')
            ], 500);
        }
    }
}
