<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'problem_type',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'admin_response',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'new' => 'Nový',
            'in_progress' => 'V riešení',
            'resolved' => 'Vyriešený',
            'closed' => 'Uzavretý',
            default => 'Neznámy',
        };
    }

    public function getProblemTypeLabelAttribute(): string
    {
        return match($this->problem_type) {
            'inzercia' => 'Inzercia',
            'predplatne' => 'Predplatné',
            'ina_chyba' => 'Iná chyba',
            default => 'Neznámy',
        };
    }
}
