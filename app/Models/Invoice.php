<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'ad_payment_id',
        'issue_date',
        'due_date',
        'delivery_date',
        'status',
        'supplier_data',
        'customer_data',
        'items',
        'subtotal',
        'tax_amount',
        'total_amount',
        'currency',
        'notes',
        'payment_info',
        'pdf_path',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'delivery_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'supplier_data' => 'array',
        'customer_data' => 'array',
        'items' => 'array',
        'payment_info' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adPayment()
    {
        return $this->belongsTo(AdPayment::class);
    }

    // Status helpers
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    public function markAsOverdue()
    {
        $this->update(['status' => 'overdue']);
    }

    // Status checks
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue' || 
               ($this->status === 'sent' && $this->due_date < now());
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    // Calculate totals from items
    public function calculateTotals()
    {
        $subtotal = 0;
        $taxAmount = 0;

        if (is_array($this->items)) {
            foreach ($this->items as $item) {
                $itemSubtotal = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? $item['tax_percentage'] ?? 23) / 100);
                
                $subtotal += $itemSubtotal;
                $taxAmount += $itemTax;
            }
        }

        $this->subtotal = $subtotal;
        $this->tax_amount = $taxAmount;
        $this->total_amount = $subtotal + $taxAmount;
    }

    // Boot method for auto-generating invoice number and dates
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            // Generate invoice number if not set
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }

            // Set default dates if not set
            if (empty($invoice->issue_date)) {
                $invoice->issue_date = now();
            }

            if (empty($invoice->due_date)) {
                $invoice->due_date = now()->addDays(14); // 14 days default
            }

            if (empty($invoice->delivery_date)) {
                $invoice->delivery_date = now();
            }

            // Set default status
            if (empty($invoice->status)) {
                $invoice->status = 'draft';
            }

            // Set default currency
            if (empty($invoice->currency)) {
                $invoice->currency = 'EUR';
            }
        });
    }

    // Generate unique invoice number
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $lastInvoice = static::whereYear('issue_date', $year)
                           ->orderBy('invoice_number', 'desc')
                           ->first();

        if (!$lastInvoice) {
            return $year . '001';
        }

        // Extract number from invoice number (assuming format YYYY###)
        $lastNumber = (int) substr($lastInvoice->invoice_number, -3);
        $newNumber = $lastNumber + 1;
        
        return $year . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Formatted attributes
    public function getFormattedTotalAttribute()
    {
        return '€' . number_format($this->total_amount, 2, ',', ' ');
    }

    public function getFormattedSubtotalAttribute()
    {
        return '€' . number_format($this->subtotal, 2, ',', ' ');
    }

    public function getFormattedTaxAmountAttribute()
    {
        return '€' . number_format($this->tax_amount, 2, ',', ' ');
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Koncept',
            'sent' => 'Odoslaná',
            'paid' => 'Zaplatená',
            'overdue' => 'Po splatnosti',
            'cancelled' => 'Zrušená',
            default => 'Neznámy',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'paid' => 'green',
            'overdue' => 'red',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
