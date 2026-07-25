<?php

namespace App\Jobs;

use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue::InteractsWithQueue;
use Illuminate\Queue::SerializesModels;

class SendAsyncNotificationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public int $userId;
    public string $title;
    public string $message;
    public string $type;
    public ?string $url;
    public ?string $icon;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $title, string $message, string $type = 'info', ?string $url = null, ?string $icon = null)
    {
        $this->userId  = $userId;
        $this->title   = $title;
        $this->message = $message;
        $this->type    = $type;
        $this->url     = $url;
        $this->icon    = $icon;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SystemNotification::create([
            'user_id' => $this->userId,
            'title'   => $this->title,
            'message' => $this->message,
            'type'    => $this->type,
            'url'     => $this->url,
            'icon'    => $this->icon ?? 'mdi-bell-outline',
            'is_read' => false,
        ]);
    }
}