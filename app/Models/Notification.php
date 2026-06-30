<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'data',
        'action_url',
        'action_text',
        'is_read',
        'is_admin',
        'priority',
        'expires_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_admin' => 'boolean',
        'expires_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeForUsers($query)
    {
        return $query->where('is_admin', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now());
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'payment' => 'Platba',
            'ad' => 'Inzerát',
            'support' => 'Podpora',
            'system' => 'Systém',
            'moderation' => 'Moderácia',
            default => 'Všeobecné'
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low' => 'Nízka',
            'normal' => 'Normálna',
            'high' => 'Vysoká',
            'urgent' => 'Urgentná',
            default => 'Normálna'
        };
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Helper methods
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function markAsUnread(): void
    {
        $this->update(['is_read' => false]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    public function isHigh(): bool
    {
        return $this->priority === 'high';
    }

    // Static methods pre vytvorenie notifikácií
    public static function createForUser($userId, $type, $title, $message, $options = [])
    {
        return self::create(array_merge([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_admin' => false
        ], $options));
    }

    public static function createForAdmin($userId, $type, $title, $message, $options = [])
    {
        return self::create(array_merge([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_admin' => true
        ], $options));
    }

    public static function createForAllAdmins($type, $title, $message, $options = [])
    {
        $admins = User::where('is_admin', true)->get();
        $notifications = [];

        foreach ($admins as $admin) {
            $notifications[] = self::createForAdmin($admin->id, $type, $title, $message, $options);
        }

        return $notifications;
    }

    // Bulk operations
    public static function markAllAsReadForUser($userId, $isAdmin = false)
    {
        return self::where('user_id', $userId)
                  ->where('is_admin', $isAdmin)
                  ->where('is_read', false)
                  ->update(['is_read' => true]);
    }

    public static function deleteExpired()
    {
        return self::expired()->delete();
    }

    public static function getUnreadCountForUser($userId, $isAdmin = false)
    {
        return self::where('user_id', $userId)
                  ->where('is_admin', $isAdmin)
                  ->where('is_read', false)
                  ->notExpired()
                  ->count();
    }
} 