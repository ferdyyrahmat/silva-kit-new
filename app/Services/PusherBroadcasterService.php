<?php

namespace App\Services;

use App\Jobs\BroadcastPusherEventJob;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PusherBroadcasterService
{
    /**
     * Get active WebSocket & Pusher configuration from system_settings.
     */
    public function getPusherSettings(): array
    {
        return [
            'enabled'     => (bool) SystemSetting::getByKey('websocket_enabled', false),
            'app_id'      => SystemSetting::getByKey('pusher_app_id', ''),
            'app_key'     => SystemSetting::getByKey('pusher_app_key', ''),
            'app_secret'  => SystemSetting::getByKey('pusher_app_secret', ''),
            'app_cluster' => SystemSetting::getByKey('pusher_app_cluster', 'ap1'),
            'host'        => SystemSetting::getByKey('pusher_host', ''),
            'port'        => SystemSetting::getByKey('pusher_port', '443'),
            'use_tls'     => (bool) SystemSetting::getByKey('pusher_use_tls', true),
        ];
    }

    /**
     * Save WebSocket & Pusher settings to system_settings.
     */
    public function savePusherSettings(array $data): void
    {
        SystemSetting::setByKey('websocket_enabled', !empty($data['websocket_enabled']));
        SystemSetting::setByKey('pusher_app_id', $data['pusher_app_id'] ?? '');
        SystemSetting::setByKey('pusher_app_key', $data['pusher_app_key'] ?? '');
        if (!empty($data['pusher_app_secret'])) {
            SystemSetting::setByKey('pusher_app_secret', $data['pusher_app_secret']);
        }
        SystemSetting::setByKey('pusher_app_cluster', $data['pusher_app_cluster'] ?? 'ap1');
        SystemSetting::setByKey('pusher_host', $data['pusher_host'] ?? '');
        SystemSetting::setByKey('pusher_port', $data['pusher_port'] ?? '443');
        SystemSetting::setByKey('pusher_use_tls', !empty($data['pusher_use_tls']));
    }

    /**
     * Queue a Pusher broadcast so it doesn't block the current request.
     */
    public function broadcast(string $channel, string $event, array $data): array
    {
        $settings = $this->getPusherSettings();

        if (!$settings['enabled']) {
            return ['success' => false, 'message' => 'WebSocket is currently disabled.'];
        }

        $appId     = trim($settings['app_id']);
        $appKey    = trim($settings['app_key']);
        $appSecret = trim($settings['app_secret']);

        if (empty($appId) || empty($appKey) || empty($appSecret)) {
            return ['success' => false, 'message' => 'Pusher App ID, Key, and Secret must be configured.'];
        }

        try {
            if (config('queue.default') === 'sync') {
                return $this->broadcastNow($channel, $event, $data);
            }

            BroadcastPusherEventJob::dispatch($channel, $event, $data);

            return ['success' => true, 'message' => "Event {$event} queued for broadcast to channel {$channel}!"];
        } catch (\Throwable $e) {
            Log::error("Pusher Broadcast Queue Failed: " . $e->getMessage());
            return ['success' => false, 'message' => "Pusher Queue Exception: " . $e->getMessage()];
        }
    }

    /**
     * Send the broadcast immediately (used by queued job).
     */
    public function broadcastNow(string $channel, string $event, array $data): array
    {
        $settings = $this->getPusherSettings();

        if (!$settings['enabled']) {
            return ['success' => false, 'message' => 'WebSocket is currently disabled.'];
        }

        $appId     = trim($settings['app_id']);
        $appKey    = trim($settings['app_key']);
        $appSecret = trim($settings['app_secret']);
        $cluster   = trim($settings['app_cluster']) ?: 'ap1';
        $host      = trim($settings['host']);

        if (empty($appId) || empty($appKey) || empty($appSecret)) {
            return ['success' => false, 'message' => 'Pusher App ID, Key, and Secret must be configured.'];
        }

        try {
            $payload = [
                'name'     => $event,
                'channels' => [$channel],
                'data'     => json_encode($data)
            ];

            $bodyJson  = json_encode($payload);
            $bodyMd5   = md5($bodyJson);
            $timestamp = time();

            $params = [
                'auth_key'       => $appKey,
                'auth_timestamp' => $timestamp,
                'auth_version'   => '1.0',
                'body_md5'       => $bodyMd5
            ];

            ksort($params);
            $queryString = http_build_query($params);
            $stringToSign = "POST\n/apps/{$appId}/events\n{$queryString}";
            $signature    = hash_hmac('sha256', $stringToSign, $appSecret);

            $baseUrl = !empty($host)
                ? ($settings['use_tls'] ? 'https://' : 'http://') . $host . ':' . $settings['port']
                : "https://api-{$cluster}.pusher.com";

            $fullUrl = "{$baseUrl}/apps/{$appId}/events?{$queryString}&auth_signature={$signature}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->withBody($bodyJson, 'application/json')->post($fullUrl);

            if ($response->successful()) {
                return ['success' => true, 'message' => "Event {$event} broadcasted to channel {$channel}!"];
            }

            Log::error("Pusher Broadcast API Error [{$response->status()}]: " . $response->body());
            return ['success' => false, 'message' => "Pusher API Error {$response->status()}: " . $response->body()];
        } catch (\Throwable $e) {
            Log::error("Pusher Broadcast Failed: " . $e->getMessage());
            return ['success' => false, 'message' => "Pusher Exception: " . $e->getMessage()];
        }
    }
}
