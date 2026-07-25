<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'user_id',
        'assigned_developer_id',
        'name',
        'email',
        'phone',
        'subject',
        'category',
        'priority',
        'status',
        'description',
        'attachments',
        'resolved_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'resolved_at' => 'datetime',
    ];

    public static function generateTicketCode(): string
    {
        do {
            $code = 'TCK-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        } while (static::where('ticket_code', $code)->exists());

        return $code;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedDeveloper()
    {
        return $this->belongsTo(Developer::class, 'assigned_developer_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at', 'asc');
    }
}
