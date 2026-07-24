<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'event',
        'action_description',
        'module',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $event, string $description, string $module = 'system', ?array $properties = null, ?User $user = null): self
    {
        $actor = $user ?? Auth::user();

        return self::create([
            'user_id'            => $actor?->id,
            'user_name'          => $actor?->name ?? 'System/Guest',
            'event'              => $event,
            'action_description' => $description,
            'module'             => $module,
            'ip_address'         => Request::ip(),
            'user_agent'         => Request::userAgent(),
            'properties'         => $properties,
        ]);
    }
}
