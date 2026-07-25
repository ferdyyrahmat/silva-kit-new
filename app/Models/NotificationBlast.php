<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationBlast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'channels',
        'target_type',
        'target_id',
        'type',
        'status',
        'sent_count',
        'failed_count',
        'created_by',
    ];

    protected $casts = [
        'channels' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
