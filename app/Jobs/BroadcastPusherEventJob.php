<?php

namespace App\Jobs;

use App\Services\PusherBroadcasterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastPusherEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        protected string $channel,
        protected string $event,
        protected array $data,
    ) {}

    public function handle(PusherBroadcasterService $broadcaster): void
    {
        $broadcaster->broadcastNow($this->channel, $this->event, $this->data);
    }
}
