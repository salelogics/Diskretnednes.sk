<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'sms_id',
        'type',
        'description',
        'price',
        'msisdn',
        'status',
        'result',
        'sms_text',
        'response_message',
        'return_url',
        'email',
        'raw_data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'raw_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Konštanty pre typy
    const TYPE_ONLINE = 'online';
    const TYPE_OFFLINE = 'offline';

    // Konštanty pre stavy
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_TIMEOUT = 'timeout';

    // Konštanty pre výsledky
    const RESULT_OK = 'OK';
    const RESULT_FAIL = 'FAIL';
    const RESULT_TIMEOUT = 'TIMEOUT';

    /**
     * Scope pre úspešné platby
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    /**
     * Scope pre neúspešné platby
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', [self::STATUS_FAILED, self::STATUS_TIMEOUT]);
    }

    /**
     * Scope pre online platby
     */
    public function scopeOnline($query)
    {
        return $query->where('type', self::TYPE_ONLINE);
    }

    /**
     * Scope pre offline platby
     */
    public function scopeOffline($query)
    {
        return $query->where('type', self::TYPE_OFFLINE);
    }

    /**
     * Accessor pre formátovanú cenu
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    /**
     * Accessor pre formátované telefónne číslo
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->msisdn) {
            return null;
        }

        // Formát: +421 903 123 456
        $phone = $this->msisdn;
        if (str_starts_with($phone, '421')) {
            return '+421 ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 3) . ' ' . substr($phone, 9);
        }

        return $phone;
    }

    /**
     * Accessor pre status badge
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_SUCCESS => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Úspešná</span>',
            self::STATUS_FAILED => '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Neúspešná</span>',
            self::STATUS_TIMEOUT => '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Timeout</span>',
            self::STATUS_PENDING => '<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Čaká</span>',
            default => '<span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Neznámy</span>',
        };
    }

    /**
     * Mutator pre telefónne číslo
     */
    public function setMsisdnAttribute($value)
    {
        // Odstránenie všetkých nečíselných znakov okrem +
        $this->attributes['msisdn'] = preg_replace('/[^0-9+]/', '', $value);
    }

    /**
     * Označí platbu ako úspešnú
     */
    public function markAsSuccessful($phone = null)
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'result' => self::RESULT_OK,
            'msisdn' => $phone ?: $this->msisdn,
        ]);
    }

    /**
     * Označí platbu ako neúspešnú
     */
    public function markAsFailed($result = self::RESULT_FAIL)
    {
        $status = $result === self::RESULT_TIMEOUT ? self::STATUS_TIMEOUT : self::STATUS_FAILED;
        
        $this->update([
            'status' => $status,
            'result' => $result,
        ]);
    }

    /**
     * Vráti či je platba úspešná
     */
    public function isSuccessful()
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Vráti či je platba neúspešná
     */
    public function isFailed()
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_TIMEOUT]);
    }

    /**
     * Vráti či je platba čakajúca
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
}
