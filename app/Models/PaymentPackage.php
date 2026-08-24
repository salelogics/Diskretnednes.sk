<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentPackage extends Model
{
    protected $fillable = [
        'name',
        'type',
        'duration_days',
        'price',
        'is_featured',
        'is_top_ad',
        'description',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_top_ad' => 'boolean',
        'features' => 'array',
        'is_active' => 'boolean',
        'duration_days' => 'integer',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function payments(): HasMany
    {
        return $this->hasMany(AdPayment::class);
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'classic' => 'Classic',
            'premium' => 'Premium Topované',
            default => 'Neznámy'
        };
    }

    public function getDurationLabelAttribute(): string
    {
        return match($this->duration_days) {
            0 => 'Bez časového limitu',
            1 => '1 deň',
            5 => '5 dní',
            7 => '7 dní',
            10 => '10 dní',
            30 => '30 dní',
            90 => '90 dní',
            365 => '365 dní',
            default => $this->duration_days . ' dní'
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        if ((float) $this->price === 0.0) {
            return 'Zadarmo';
        }

        return number_format($this->price, 2) . ' €';
    }

    public function getIsFreeAttribute(): bool
    {
        return (float) $this->price === 0.0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('duration_days');
    }

    // Helper methods
    public function isClassic(): bool
    {
        return $this->type === 'classic';
    }

    public function isPremium(): bool
    {
        return $this->type === 'premium';
    }
}
