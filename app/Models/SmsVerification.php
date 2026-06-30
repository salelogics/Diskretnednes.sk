<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SmsVerification extends Model
{
    use HasFactory;

    protected $table = 'sms_verifications';

    protected $fillable = [
        'ad_id',
        'msisdn',
        'sms_id',
        'verification_code',
        'sms_code',
        'package_code',
        'package_type',
        'package_duration',
        'price',
        'status',
        'is_verified',
        'is_payment_confirmed',
        'expires_at',
        'verified_at',
        'used_at',
        'sms_keyword',
        'phone_number',
        'transaction_id',
        'user_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'package_duration' => 'integer',
        'is_verified' => 'boolean',
        'is_payment_confirmed' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'used_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Generuje náhodný 6-miestny verifikačný kód
     */
    public static function generateVerificationCode(): string
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generuje SMS kľúčové slovo
     */
    public static function generateSmsKeyword(): string
    {
        $keywords = ['VERIFY', 'CHECK', 'CONFIRM', 'VALID', 'CODE', 'AUTH'];
        return $keywords[array_rand($keywords)] . random_int(10, 99);
    }

    /**
     * Overí či verifikačný kód ešte neexpiroval
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Overí či je verifikácia aktívna
     */
    public function isActive(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Označí verifikáciu ako overenú
     */
    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now()
        ]);
    }

    /**
     * Označí platbu ako potvrdenú
     */
    public function markPaymentConfirmed(): void
    {
        $this->update([
            'is_payment_confirmed' => true
        ]);
    }

    /**
     * Označí verifikáciu ako neúspešnú
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed'
        ]);
    }

    /**
     * Označí verifikáciu ako expirovanú
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired'
        ]);
    }

    /**
     * Scope pre aktívne verifikácie
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope pre overené verifikácie
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope pre neexpirované verifikácie
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope pre konkrétne telefónne číslo
     */
    public function scopeForPhone($query, string $msisdn)
    {
        return $query->where('msisdn', $msisdn);
    }
}
