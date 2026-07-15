<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Services\NotificationService;

class AdPayment extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'payment_package_id',
        'payment_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'duration_days',
        'subscription_starts_at',
        'subscription_ends_at',
        'is_featured',
        'is_top_ad',
        'gateway_payment_id',
        'gateway_session_id',
        'gateway_response',
        'stripe_payment_intent_id',
        'stripe_checkout_session_id',
        'stripe_customer_id',
        'stripe_metadata',
        'invoice_number',
        'invoice_sent_at',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'duration_days' => 'integer',
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_top_ad' => 'boolean',
        'gateway_response' => 'array',
        'stripe_metadata' => 'array',
        'invoice_sent_at' => 'datetime',
        'metadata' => 'array'
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

    public function paymentPackage(): BelongsTo
    {
        return $this->belongsTo(PaymentPackage::class);
    }

    public function invoice(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Invoice::class, 'ad_payment_id');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'completed' => 'Dokončená',
            'pending' => 'Čakajúca',
            'failed' => 'Neúspešná',
            'cancelled' => 'Zrušená',
            default => 'Neznámy'
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return '€' . number_format($this->amount, 2, ',', ' ');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'stripe' => 'Karta',
            'bank_transfer' => 'Prevodom',
            'sms' => 'SMS',
            'qr_code' => 'QR kód',
            'free' => 'Zadarmo',
            'admin_free' => 'Administrátorom zadarmo',
            default => 'Neznámy'
        };
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->duration_days === null) {
            return 'Neznámy';
        }

        return match($this->duration_days) {
            0 => 'Bez časového limitu',
            1 => '1 deň',
            7 => '1 týždeň',
            10 => '10 dní',
            30 => '1 mesiac',
            90 => '3 mesiace',
            365 => '1 rok',
            default => $this->duration_days . ' dní'
        };
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAd($query, $adId)
    {
        return $query->where('ad_id', $adId);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeActive($query)
    {
        return $query->where('subscription_ends_at', '>', now());
    }

    // Helper methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isActive(): bool
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isPast();
    }

    public function getDaysUntilExpiration(): ?int
    {
        if (!$this->subscription_ends_at) {
            return null;
        }
        
        return $this->subscription_ends_at->diffInDays(now(), false);
    }

    public function generateInvoiceNumber(): string
    {
        if ($this->invoice_number) {
            return $this->invoice_number;
        }

        $year = $this->created_at->format('Y');
        $month = $this->created_at->format('m');
        $number = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        
        $invoiceNumber = "INV-{$year}{$month}-{$number}";
        
        $this->update([
            'invoice_number' => $invoiceNumber,
            'invoice_sent_at' => now()
        ]);
        
        return $invoiceNumber;
    }

    public function markAsCompleted(): void
    {
        // duration_days = 0 znamená bez časového limitu (napr. bezplatný Classic balíček) -
        // subscription_expires_at ostáva null, čo scopeActive/scopeActiveSubscription
        // už interpretujú ako trvalo aktívne predplatné.
        $expiresAt = $this->duration_days > 0 ? now()->addDays($this->duration_days) : null;

        $this->update([
            'status' => 'completed',
            'subscription_starts_at' => now(),
            'subscription_ends_at' => $expiresAt
        ]);

        // Aktualizuj inzerát
        $this->ad->update([
            'status' => 'active',
            'subscription_status' => 'active',
            'subscription_expires_at' => $expiresAt,
            'featured' => $this->is_featured,
            'top_ad' => $this->is_top_ad
        ]);

        // Vytvor faktúru len pre platby kartou a bankovým prevodom
        // SMS platby účtuje operátor, takže faktúra nie je potrebná
        if (in_array($this->payment_method, ['bank_transfer', 'stripe'])) {
            $this->generateInvoiceNumber();
            
            // Vytvor Invoice záznam ak neexistuje
            if (!$this->invoice) {
                $invoiceService = app(\App\Services\InvoiceService::class);
                $invoiceService->createInvoiceFromPayment($this);
            }
        }

        // Vytvor notifikácie
        $notificationService = app(NotificationService::class);
        $notificationService->paymentCompleted($this);
        $notificationService->newPayment($this);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);

        // Vytvor notifikáciu o neúspešnej platbe
        $notificationService = app(NotificationService::class);
        $notificationService->paymentFailed($this);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
