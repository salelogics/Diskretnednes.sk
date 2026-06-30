<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'to_email',
        'from_email',
        'subject',
        'body',
        'type',
        'status',
        'error_message',
        'headers',
        'metadata',
        'sent_at'
    ];

    protected $casts = [
        'headers' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime'
    ];

    /**
     * Scope pre filtrovanie podľa typu emailu
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pre filtrovanie podľa statusu
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pre vyhľadávanie podľa príjemcu
     */
    public function scopeForRecipient($query, $email)
    {
        return $query->where('to_email', 'like', '%' . $email . '%');
    }

    /**
     * Accessor pre skrátený subject
     */
    protected function shortSubject(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strlen($this->subject) > 50 
                ? substr($this->subject, 0, 50) . '...' 
                : $this->subject,
        );
    }

    /**
     * Accessor pre skrátený body
     */
    protected function shortBody(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strlen($this->body) > 100 
                ? substr(strip_tags($this->body), 0, 100) . '...' 
                : strip_tags($this->body),
        );
    }

    /**
     * Získanie typu emailu v slovenčine
     */
    public function getTypeNameAttribute()
    {
        return match($this->type) {
            'welcome' => 'Uvítací email',
            'notification' => 'Notifikácia',
            'support' => 'Support ticket',
            'payment' => 'Platba',
            'ad_created' => 'Nový inzerát',
            'ad_updated' => 'Aktualizácia inzerátu',
            'subscription_expiring' => 'Expirujúce predplatné',
            'subscription_expired' => 'Expirované predplatné',
            'admin_notification' => 'Admin notifikácia',
            'contact' => 'Kontaktný formulár',
            'test' => 'Test email',
            default => 'Všeobecný email'
        };
    }

    /**
     * Získanie statusu v slovenčine
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'sent' => 'Odoslaný',
            'failed' => 'Neúspešný',
            'queued' => 'Vo fronte',
            default => 'Neznámy'
        };
    }

    /**
     * Statická metóda pre logovanie emailu
     */
    public static function logEmail($to, $subject, $body = null, $type = 'general', $metadata = [])
    {
        return self::create([
            'to_email' => $to,
            'from_email' => config('mail.from.address'),
            'subject' => $subject,
            'body' => $body,
            'type' => $type,
            'status' => 'sent',
            'metadata' => $metadata,
            'sent_at' => now()
        ]);
    }

    /**
     * Statická metóda pre logovanie neúspešného emailu
     */
    public static function logFailedEmail($to, $subject, $error, $type = 'general', $metadata = [])
    {
        return self::create([
            'to_email' => $to,
            'from_email' => config('mail.from.address'),
            'subject' => $subject,
            'type' => $type,
            'status' => 'failed',
            'error_message' => $error,
            'metadata' => $metadata,
            'sent_at' => now()
        ]);
    }
}
