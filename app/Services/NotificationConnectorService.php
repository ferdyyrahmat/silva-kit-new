<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationConnectorService
{
    /**
     * Get all active connector configurations from system_settings.
     */
    public function getConnectorSettings(): array
    {
        return [
            'email' => [
                'enabled'      => (bool) SystemSetting::getByKey('connector_email_enabled', false),
                'host'         => SystemSetting::getByKey('connector_email_host', 'smtp.mailtrap.io'),
                'port'         => SystemSetting::getByKey('connector_email_port', '587'),
                'username'     => SystemSetting::getByKey('connector_email_username', ''),
                'password'     => SystemSetting::getByKey('connector_email_password', ''),
                'encryption'   => SystemSetting::getByKey('connector_email_encryption', 'tls'),
                'from_name'    => SystemSetting::getByKey('connector_email_from_name', config('app.name')),
                'from_address' => SystemSetting::getByKey('connector_email_from_address', 'no-reply@silvakit.com'),
            ],
            'whatsapp' => [
                'enabled'      => (bool) SystemSetting::getByKey('connector_whatsapp_enabled', false),
                'provider'     => SystemSetting::getByKey('connector_whatsapp_provider', 'fonnte'),
                'api_url'      => SystemSetting::getByKey('connector_whatsapp_api_url', 'https://api.fonnte.com/send'),
                'token'        => SystemSetting::getByKey('connector_whatsapp_token', ''),
            ],
            'telegram' => [
                'enabled'      => (bool) SystemSetting::getByKey('connector_telegram_enabled', false),
                'bot_token'    => SystemSetting::getByKey('connector_telegram_bot_token', ''),
                'chat_id'      => SystemSetting::getByKey('connector_telegram_chat_id', ''),
            ]
        ];
    }

    /**
     * Save updated connector configurations to system_settings.
     */
    public function saveConnectorSettings(array $data): void
    {
        // Email Settings
        SystemSetting::setByKey('connector_email_enabled', !empty($data['email_enabled']));
        SystemSetting::setByKey('connector_email_host', $data['email_host'] ?? '');
        SystemSetting::setByKey('connector_email_port', $data['email_port'] ?? '587');
        SystemSetting::setByKey('connector_email_username', $data['email_username'] ?? '');
        if (!empty($data['email_password'])) {
            SystemSetting::setByKey('connector_email_password', $data['email_password']);
        }
        SystemSetting::setByKey('connector_email_encryption', $data['email_encryption'] ?? 'tls');
        SystemSetting::setByKey('connector_email_from_name', $data['email_from_name'] ?? config('app.name'));
        SystemSetting::setByKey('connector_email_from_address', $data['email_from_address'] ?? 'no-reply@silvakit.com');

        // WhatsApp Settings
        SystemSetting::setByKey('connector_whatsapp_enabled', !empty($data['whatsapp_enabled']));
        SystemSetting::setByKey('connector_whatsapp_provider', $data['whatsapp_provider'] ?? 'fonnte');
        SystemSetting::setByKey('connector_whatsapp_api_url', $data['whatsapp_api_url'] ?? 'https://api.fonnte.com/send');
        if (!empty($data['whatsapp_token'])) {
            SystemSetting::setByKey('connector_whatsapp_token', $data['whatsapp_token']);
        }

        // Telegram Settings
        SystemSetting::setByKey('connector_telegram_enabled', !empty($data['telegram_enabled']));
        if (!empty($data['telegram_bot_token'])) {
            SystemSetting::setByKey('connector_telegram_bot_token', $data['telegram_bot_token']);
        }
        SystemSetting::setByKey('connector_telegram_chat_id', $data['telegram_chat_id'] ?? '');
    }

    /**
     * Send Email to recipient using active connector settings.
     */
    public function sendEmail(string $recipientEmail, string $subject, string $body): array
    {
        $settings = $this->getConnectorSettings()['email'];

        if (!$settings['enabled'] || empty($settings['host']) || empty($settings['from_address'])) {
            return ['success' => false, 'message' => 'Email connector is disabled or unconfigured.'];
        }

        try {
            config([
                'mail.mailers.dynamic_smtp' => [
                    'transport'  => 'smtp',
                    'host'       => $settings['host'],
                    'port'       => (int) $settings['port'],
                    'encryption' => $settings['encryption'] === 'none' ? null : $settings['encryption'],
                    'username'   => $settings['username'],
                    'password'   => $settings['password'],
                    'timeout'    => 10,
                ],
                'mail.from' => [
                    'address' => $settings['from_address'],
                    'name'    => $settings['from_name'],
                ]
            ]);

            Mail::mailer('dynamic_smtp')->raw($body, function ($msg) use ($recipientEmail, $subject) {
                $msg->to($recipientEmail)->subject($subject);
            });

            return ['success' => true, 'message' => "Email sent to {$recipientEmail}"];
        } catch (\Throwable $e) {
            Log::error("Send Email Connector Failed: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Test SMTP Email Connector.
     */
    public function testEmailConnection(string $recipientEmail): array
    {
        return $this->sendEmail($recipientEmail, "Test Email Connection - Silva Kit", "Hello! This is a test email sent from Silva Kit Notification Connector Engine.");
    }

    /**
     * Send WhatsApp message to recipient phone.
     */
    public function sendWhatsApp(string $phone, string $message): array
    {
        $settings = $this->getConnectorSettings()['whatsapp'];
        if (!$settings['enabled'] || empty($settings['token']) || empty($settings['api_url'])) {
            return ['success' => false, 'message' => 'WhatsApp connector is disabled or unconfigured.'];
        }

        try {
            if ($settings['provider'] === 'fonnte') {
                $response = Http::asForm()->withHeaders([
                    'Authorization' => trim($settings['token']),
                ])->post($settings['api_url'], [
                    'target'      => $phone,
                    'message'     => $message,
                    'countryCode' => '62',
                ]);

                $json = $response->json();
                return ['success' => ($response->successful() && isset($json['status']) && $json['status'] === true)];
            } else {
                $response = Http::asForm()->withHeaders([
                    'Authorization' => trim($settings['token']),
                ])->post($settings['api_url'], [
                    'phone'   => $phone,
                    'target'  => $phone,
                    'message' => $message,
                ]);

                return ['success' => $response->successful()];
            }
        } catch (\Throwable $e) {
            Log::error("Send WhatsApp Connector Failed: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Test WhatsApp Gateway API Connector (Accepts live override tokens from form inputs).
     */
    public function testWhatsAppConnection(string $phone, ?string $overrideToken = null, ?string $overrideApiUrl = null, ?string $overrideProvider = null): array
    {
        $settings = $this->getConnectorSettings()['whatsapp'];
        $token    = !empty($overrideToken) ? $overrideToken : $settings['token'];
        $apiUrl   = !empty($overrideApiUrl) ? $overrideApiUrl : $settings['api_url'];
        $provider = !empty($overrideProvider) ? $overrideProvider : $settings['provider'];

        if (empty($token) || empty($apiUrl)) {
            return ['success' => false, 'message' => 'WhatsApp API Token and Endpoint URL must be configured. Please type your API Token and URL in the form first.'];
        }

        try {
            $message = "Test WhatsApp Notification from Silva Kit Connector Engine.";

            if ($provider === 'fonnte') {
                $response = Http::asForm()->withHeaders([
                    'Authorization' => trim($token),
                ])->post($apiUrl, [
                    'target'      => $phone,
                    'message'     => $message,
                    'countryCode' => '62',
                ]);

                $json = $response->json();
                if ($response->successful() && isset($json['status']) && $json['status'] === true) {
                    return ['success' => true, 'message' => "WhatsApp test message successfully queued & sent to {$phone} via Fonnte Gateway!"];
                } else {
                    $reason = $json['reason'] ?? $json['detail'] ?? $response->body();
                    return ['success' => false, 'message' => "Fonnte API Error: " . (is_array($reason) ? json_encode($reason) : $reason)];
                }
            } else {
                // Generic Webhook POST / Wablas
                $response = Http::asForm()->withHeaders([
                    'Authorization' => trim($token),
                ])->post($apiUrl, [
                    'phone'   => $phone,
                    'target'  => $phone,
                    'message' => $message,
                ]);

                if ($response->successful()) {
                    return ['success' => true, 'message' => "WhatsApp test message successfully sent to {$phone}!"];
                } else {
                    return ['success' => false, 'message' => "WhatsApp Gateway returned HTTP {$response->status()}: " . $response->body()];
                }
            }
        } catch (\Throwable $e) {
            Log::error("Test WhatsApp Connector Failed: " . $e->getMessage());
            return ['success' => false, 'message' => "WhatsApp Gateway Error: " . $e->getMessage()];
        }
    }

    /**
     * Send Telegram message to chat ID.
     */
    public function sendTelegram(string $chatId, string $message): array
    {
        $settings = $this->getConnectorSettings()['telegram'];
        if (!$settings['enabled'] || empty($settings['bot_token'])) {
            return ['success' => false, 'message' => 'Telegram connector is disabled or unconfigured.'];
        }

        try {
            $url = "https://api.telegram.org/bot{$settings['bot_token']}/sendMessage";
            $response = Http::post($url, [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'HTML',
            ]);

            return ['success' => $response->successful(), 'message' => $response->body()];
        } catch (\Throwable $e) {
            Log::error("Send Telegram Connector Failed: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Test Telegram Bot Connector.
     */
    public function testTelegramConnection(string $chatId, ?string $overrideBotToken = null): array
    {
        $settings = $this->getConnectorSettings()['telegram'];
        $botToken = !empty($overrideBotToken) ? $overrideBotToken : $settings['bot_token'];

        if (empty($botToken) || empty($chatId)) {
            return ['success' => false, 'message' => 'Telegram Bot Token and Chat ID must be configured.'];
        }

        return $this->sendTelegram($chatId, "<b>Silva Kit Test Notification</b>\nTest connection from Silva Kit Telegram Connector Engine.");
    }

    /**
     * Send Multi-Channel Notification to a Single User.
     */
    public function sendToUser(User $user, string $title, string $message, array $channels, string $type = 'info', ?string $url = null, string $icon = 'mdi-bell-outline'): array
    {
        $results = ['bell' => false, 'email' => false, 'whatsapp' => false, 'telegram' => false];
        $settings = $this->getConnectorSettings();

        // 1. In-App Bell Notification
        if (in_array('bell', $channels)) {
            try {
                SystemNotification::send($user, $title, $message, $type, $icon, $url);
                $results['bell'] = true;
            } catch (\Throwable $e) {
                Log::error("Bell dispatch failed: " . $e->getMessage());
            }
        }

        // 2. Email Channel
        if (in_array('email', $channels) && $settings['email']['enabled'] && !empty($user->email)) {
            try {
                $res = $this->sendEmail($user->email, $title, $message);
                $results['email'] = $res['success'];
            } catch (\Throwable $e) {
                Log::error("Email dispatch failed for {$user->email}: " . $e->getMessage());
            }
        }

        // 3. WhatsApp Channel
        if (in_array('whatsapp', $channels) && $settings['whatsapp']['enabled'] && !empty($user->phone)) {
            try {
                $res = $this->sendWhatsApp($user->phone, "*{$title}*\n\n{$message}");
                $results['whatsapp'] = $res['success'];
            } catch (\Throwable $e) {
                Log::error("WhatsApp dispatch failed for {$user->phone}: " . $e->getMessage());
            }
        }

        // 4. Telegram Channel
        if (in_array('telegram', $channels) && $settings['telegram']['enabled'] && !empty($settings['telegram']['chat_id'])) {
            try {
                $res = $this->sendTelegram($settings['telegram']['chat_id'], "<b>{$title}</b>\n\n{$message}");
                $results['telegram'] = $res['success'];
            } catch (\Throwable $e) {
                Log::error("Telegram dispatch failed: " . $e->getMessage());
            }
        }

        return $results;
    }
}
