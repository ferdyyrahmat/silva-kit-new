<?php

namespace App\Services;

use App\Models\Developer;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TicketNotificationService
{
    protected NotificationConnectorService $connector;

    public function __construct(NotificationConnectorService $connector)
    {
        $this->connector = $connector;
    }

    /**
     * Notify Developers & User when a new ticket is submitted.
     */
    public function notifyTicketCreated(Ticket $ticket): void
    {
        try {
            $trackingUrl = route('v1.tickets.show', $ticket->ticket_code);
            $title = "New Ticket #{$ticket->ticket_code}: {$ticket->subject}";

            // 1. Notify Assigned Developer or All Active Developers
            $developers = $ticket->assignedDeveloper 
                ? collect([$ticket->assignedDeveloper])
                : Developer::where('is_active', true)->get();

            foreach ($developers as $dev) {
                $channels = $dev->notify_channels ?? ['in_app', 'email'];

                // In-App Bell Notification if linked to a User
                if ($dev->user && in_array('in_app', $channels)) {
                    try {
                        \App\Models\SystemNotification::send(
                            $dev->user,
                            "New Support Ticket #{$ticket->ticket_code}",
                            "{$ticket->subject} (Priority: " . strtoupper($ticket->priority) . ")",
                            'ticket',
                            'mdi-ticket-account',
                            route('admin.tickets.show', $ticket->id)
                        );
                    } catch (\Throwable $ex) {
                        Log::error("Ticket In-App Bell Notification Error: " . $ex->getMessage());
                    }
                }

                // Email
                if (in_array('email', $channels) && !empty($dev->email)) {
                    $this->connector->sendEmail(
                        $dev->email,
                        $title,
                        "Hello {$dev->name},\n\nA new support ticket has been created:\n\nSubject: {$ticket->subject}\nSubmitter: {$ticket->name} ({$ticket->email})\nPriority: {$ticket->priority}\nDescription:\n{$ticket->description}\n\nView Ticket: " . route('admin.tickets.show', $ticket->id)
                    );
                }

                // WhatsApp
                if (in_array('whatsapp', $channels) && !empty($dev->phone)) {
                    $waMsg = "🚨 *NEW SUPPORT TICKET #{$ticket->ticket_code}*\n\nSubject: {$ticket->subject}\nSubmitter: {$ticket->name}\nPriority: *" . strtoupper($ticket->priority) . "*\n\nLink: " . route('admin.tickets.show', $ticket->id);
                    $this->connector->sendWhatsApp($dev->phone, $waMsg);
                }

                // Telegram
                if (in_array('telegram', $channels) && !empty($dev->telegram_chat_id)) {
                    $tgMsg = "🚨 <b>NEW SUPPORT TICKET #{$ticket->ticket_code}</b>\n\n<b>Subject:</b> {$ticket->subject}\n<b>Priority:</b> " . strtoupper($ticket->priority) . "\n\n<a href='" . route('admin.tickets.show', $ticket->id) . "'>View Ticket</a>";
                    $this->connector->sendTelegram($dev->telegram_chat_id, $tgMsg);
                }
            }

            // 2. Notify Submitter (User)
            if ($ticket->user) {
                try {
                    \App\Models\SystemNotification::send(
                        $ticket->user,
                        "Ticket Submitted #{$ticket->ticket_code}",
                        "Your ticket '{$ticket->subject}' has been submitted. Tracking Code: {$ticket->ticket_code}",
                        'ticket',
                        'mdi-ticket-confirmation-outline',
                        $trackingUrl
                    );
                } catch (\Throwable $ex) {
                    Log::error("Ticket Submitter Bell Error: " . $ex->getMessage());
                }
            }

            if (!empty($ticket->email)) {
                $userMailBody = "Hello {$ticket->name},\n\nThank you for submitting ticket #{$ticket->ticket_code}.\n\nSubject: {$ticket->subject}\nStatus: " . strtoupper($ticket->status) . "\n\nYou can track and reply to your ticket here: {$trackingUrl}\n\nOur team is reviewing your ticket.";
                $this->connector->sendEmail($ticket->email, "Ticket Received [#{$ticket->ticket_code}]", $userMailBody);
            }

            if (!empty($ticket->phone)) {
                $userWaMsg = "✅ *TICKET RECEIVED #{$ticket->ticket_code}*\n\nHello {$ticket->name}, your support request '{$ticket->subject}' has been logged.\n\nTrack status & reply here:\n{$trackingUrl}";
                $this->connector->sendWhatsApp($ticket->phone, $userWaMsg);
            }
        } catch (\Throwable $e) {
            Log::error("notifyTicketCreated Error: " . $e->getMessage());
        }
    }

    /**
     * Notify Developer or User when a ticket reply is posted.
     */
    public function notifyTicketReplied(Ticket $ticket, TicketReply $reply): void
    {
        try {
            // Broadcast live chat reply to Pusher WebSocket channel
            app(PusherBroadcasterService::class)->broadcast(
                "ticket-{$ticket->ticket_code}",
                "reply-created",
                [
                    'id'               => $reply->id,
                    'ticket_code'      => $ticket->ticket_code,
                    'sender_type'      => $reply->sender_type,
                    'sender_name'      => $reply->sender_name,
                    'sender_email'     => $reply->sender_email,
                    'message'          => $reply->message,
                    'is_internal_note' => (bool) $reply->is_internal_note,
                    'created_at'       => $reply->created_at->format('Y-m-d H:i'),
                    'status'           => $ticket->status,
                ]
            );

            // If internal note, do not notify user via external email/wa
            if ($reply->is_internal_note) {
                return;
            }

            $trackingUrl = route('v1.tickets.show', $ticket->ticket_code);
            $adminUrl = route('admin.tickets.show', $ticket->id);

            if (in_array($reply->sender_type, ['developer', 'admin'])) {
                // Replied by Developer/Admin -> Notify Ticket Submitter (User)
                if ($ticket->user) {
                    try {
                        \App\Models\SystemNotification::send(
                            $ticket->user,
                            "New Reply on Ticket #{$ticket->ticket_code}",
                            "{$reply->sender_name}: " . \Illuminate\Support\Str::limit($reply->message, 80),
                            'ticket',
                            'mdi-reply-all-outline',
                            $trackingUrl
                        );
                    } catch (\Throwable $ex) {
                        Log::error("Ticket Reply Bell Error: " . $ex->getMessage());
                    }
                }

                if (!empty($ticket->email)) {
                    $userMail = "Hello {$ticket->name},\n\nA new response was posted on ticket #{$ticket->ticket_code} by {$reply->sender_name}:\n\n\"{$reply->message}\"\n\nView & Reply: {$trackingUrl}";
                    $this->connector->sendEmail($ticket->email, "Re: [#{$ticket->ticket_code}] {$ticket->subject}", $userMail);
                }

                if (!empty($ticket->phone)) {
                    $userWa = "💬 *REPLY ON TICKET #{$ticket->ticket_code}*\n\nFrom: {$reply->sender_name}\n\n{$reply->message}\n\nReply here:\n{$trackingUrl}";
                    $this->connector->sendWhatsApp($ticket->phone, $userWa);
                }
            } else {
                // Replied by User -> Notify Assigned Developer or All Active Developers
                $developers = $ticket->assignedDeveloper 
                    ? collect([$ticket->assignedDeveloper])
                    : Developer::where('is_active', true)->get();

                foreach ($developers as $dev) {
                    $channels = $dev->notify_channels ?? ['in_app', 'email'];

                    if ($dev->user && in_array('in_app', $channels)) {
                        try {
                            \App\Models\SystemNotification::send(
                                $dev->user,
                                "User Replied on Ticket #{$ticket->ticket_code}",
                                "{$ticket->name}: " . \Illuminate\Support\Str::limit($reply->message, 80),
                                'ticket',
                                'mdi-comment-account-outline',
                                $adminUrl
                            );
                        } catch (\Throwable $ex) {
                            Log::error("Dev Reply Bell Error: " . $ex->getMessage());
                        }
                    }

                    if (in_array('email', $channels) && !empty($dev->email)) {
                        $devMail = "Hello {$dev->name},\n\n{$ticket->name} replied on ticket #{$ticket->ticket_code}:\n\n\"{$reply->message}\"\n\nView Ticket: {$adminUrl}";
                        $this->connector->sendEmail($dev->email, "User Reply [#{$ticket->ticket_code}]", $devMail);
                    }

                    if (in_array('whatsapp', $channels) && !empty($dev->phone)) {
                        $devWa = "💬 *USER REPLIED ON TICKET #{$ticket->ticket_code}*\n\nFrom: {$ticket->name}\n\n{$reply->message}\n\nLink: {$adminUrl}";
                        $this->connector->sendWhatsApp($dev->phone, $devWa);
                    }

                    if (in_array('telegram', $channels) && !empty($dev->telegram_chat_id)) {
                        $devTg = "💬 <b>USER REPLIED ON TICKET #{$ticket->ticket_code}</b>\nFrom: {$ticket->name}\n\n<i>{$reply->message}</i>\n\n<a href='{$adminUrl}'>View Ticket</a>";
                        $this->connector->sendTelegram($dev->telegram_chat_id, $devTg);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("notifyTicketReplied Error: " . $e->getMessage());
        }
    }
}
