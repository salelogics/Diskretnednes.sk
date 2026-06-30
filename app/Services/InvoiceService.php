<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use App\Models\AdPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceService
{
    /**
     * Vytvorí faktúru z platby
     */
    public function createInvoiceFromPayment(AdPayment $payment): Invoice
    {
        $user = $payment->user;
        $ad = $payment->ad;

        // Údaje o zákazníkovi
        $customerData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'address' => $user->street_address ?? '',
            'city' => $user->city ?? '',
            'postal_code' => $user->postal_code ?? '',
            'country' => 'Slovenská republika',
            'ico' => $user->ico ?? null,
            'dic' => $user->dic ?? null,
            'ic_dph' => $user->ic_dph ?? null,
            'is_business' => !empty($user->ico)
        ];

        // Položky faktúry
        $items = [];
        
        // Použijeme názov balíka z payment package alebo určíme podľa features
        $packageName = $payment->paymentPackage->name ?? 
            (($payment->is_featured || $payment->is_top_ad) ? 'PREMIUM TOPOVANÉ' : 'CLASSIC');
        
        $description = ($payment->is_featured || $payment->is_top_ad)
            ? "Predplatné inzerátu s TOP pozíciou a zvýraznením na Erotikon.sk po dobu {$payment->duration_days} dní"
            : "Zobrazovanie inzerátu na Erotikon.sk po dobu {$payment->duration_days} dní";
        
        // Vytvoríme jednu položku s finálnou cenou podľa cenníka
        $items[] = [
            'name' => "Predplatné inzerátu #{$payment->ad_id} - {$packageName}",
            'description' => $description,
            'quantity' => 1,
            'unit' => 'ks',
            'unit_price' => $this->calculateBasePrice($payment),
            'tax_rate' => 0, // 0% DPH - nie sme platcami DPH
            'tax_amount' => 0, // Bude prepočítané
            'total' => 0 // Bude prepočítané
        ];

        // Prepočítaj položky
        foreach ($items as &$item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $item['tax_amount'] = $subtotal * ($item['tax_rate'] / 100);
            $item['total'] = $subtotal + $item['tax_amount'];
        }

        // Vytvor faktúru
        $adName = $ad->nickname ?? ('ID ' . $ad->id);
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'ad_payment_id' => $payment->id,
            'customer_data' => $customerData,
            'supplier_data' => $this->getDefaultSupplierData(),
            'payment_info' => $this->getDefaultPaymentInfo(),
            'items' => $items,
            'notes' => "Faktúra za predplatné inzerátu na portáli Erotikon.sk\nInzerát: {$adName}"
        ]);

        // Prepočítaj celkové sumy
        $invoice->calculateTotals();
        $invoice->save();

        return $invoice;
    }

    /**
     * Vytvorí manuálnu faktúru
     */
    public function createManualInvoice(array $data): Invoice
    {
        $invoice = Invoice::create($data);
        $invoice->calculateTotals();
        $invoice->save();

        return $invoice;
    }

    /**
     * Generuje PDF faktúru
     */
    public function generatePDF(Invoice $invoice): string
    {
        try {
            // Prepare data for PDF
            $data = [
                'invoice' => $invoice,
                'company' => [
                    'name' => env('COMPANY_NAME', 'Erotikon.sk'),
                    'address' => env('COMPANY_ADDRESS', ''),
                    'city' => env('COMPANY_CITY', ''),
                    'postal_code' => env('COMPANY_POSTAL_CODE', ''),
                    'country' => 'Slovensko',
                    'ico' => env('COMPANY_ICO', ''),
                    'dic' => env('COMPANY_DIC', ''),
                    'ic_dph' => env('COMPANY_IC_DPH', ''),
                    'phone' => env('COMPANY_PHONE', ''),
                    'email' => env('COMPANY_EMAIL', 'info@erotikon.sk'),
                    'iban' => env('COMPANY_IBAN', ''),
                    'swift' => env('COMPANY_SWIFT', ''),
                ],
                'logo' => $this->getCompanyLogo(),
            ];

            // Generate PDF using DomPDF
            $pdf = Pdf::loadView('admin.payments.invoice-pdf', $data);
            
            // Configure PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'fontHeightRatio' => 1.1,
            ]);

            return $pdf->output();
            
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Download PDF for invoice
     */
    public function downloadPDF(Invoice $invoice): \Illuminate\Http\Response
    {
        try {
            $pdfContent = $this->generatePDF($invoice);
            $filename = "faktura_{$invoice->invoice_number}.pdf";

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdfContent),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error downloading PDF: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Stream PDF for invoice (view in browser)
     */
    public function streamPDF(Invoice $invoice): \Illuminate\Http\Response
    {
        try {
            $pdfContent = $this->generatePDF($invoice);
            $filename = "faktura_{$invoice->invoice_number}.pdf";

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error streaming PDF: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Odošle faktúru emailom
     */
    public function sendInvoiceEmail(Invoice $invoice): bool
    {
        try {
            // Generate PDF
            $pdfContent = $this->generatePDF($invoice);
            $filename = "faktura_{$invoice->invoice_number}.pdf";

            // Get customer email
            $customerEmail = $invoice->customer_data['email'] ?? $invoice->user->email ?? null;
            
            if (!$customerEmail) {
                throw new \Exception('Customer email not found');
            }

            // OPRAVENÉ: Používame AdminNotificationHelper namiesto priameho Mail::raw()
            $emailContent = "Dobrý den,\n\n" .
                "v prílohe nájdete faktúru č. {$invoice->invoice_number}.\n\n" .
                "Suma na úhradu: {$invoice->total_amount}€\n" .
                "Splatnosť: {$invoice->due_date->format('d.m.Y')}\n\n" .
                "Ďakujeme za Vašu objednávku.\n\n" .
                "S pozdravom,\n" .
                "Tím Erotikon.sk";
                
            $subject = "Faktúra č. {$invoice->invoice_number} - Erotikon.sk";

            // Pre faktúry s prílohami musíme použiť priamy Mail call ale s admin SMTP nastaveniami
            \Mail::raw($emailContent, function ($message) use ($customerEmail, $invoice, $pdfContent, $filename, $subject) {
                $message->to($customerEmail)
                       ->subject($subject)
                       ->attachData($pdfContent, $filename, [
                           'mime' => 'application/pdf',
                       ]);
            });

            Log::info('Invoice email sent via admin SMTP settings', [
                'invoice_id' => $invoice->id,
                'customer_email' => $customerEmail,
                'pdf_size' => strlen($pdfContent),
                'mail_driver' => config('mail.default'),
                'smtp_host' => config('mail.mailers.smtp.host')
            ]);

            // Mark invoice as sent
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            return true;
            
        } catch (\Exception $e) {
            Log::error('Error sending invoice email: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Označí faktúru ako zaplatenú
     */
    public function markInvoiceAsPaid(Invoice $invoice): void
    {
        $invoice->markAsPaid();

        // Ak je faktúra spojená s platbou, aktualizuj aj platbu
        if ($invoice->adPayment) {
            $invoice->adPayment->update(['status' => 'completed']);
        }
    }

    /**
     * Kontrola faktúr po splatnosti
     */
    public function checkOverdueInvoices(): int
    {
        $overdueCount = 0;
        $invoices = Invoice::where('status', 'sent')
                          ->where('due_date', '<', now())
                          ->get();

        foreach ($invoices as $invoice) {
            $invoice->markAsOverdue();
            $overdueCount++;
        }

        return $overdueCount;
    }

    /**
     * Získa štatistiky faktúr
     */
    public function getInvoiceStats(): array
    {
        $thisMonth = Invoice::thisMonth();
        $thisYear = Invoice::thisYear();

        return [
            'total_invoices' => Invoice::count(),
            'this_month' => [
                'count' => $thisMonth->count(),
                'total_amount' => $thisMonth->sum('total_amount'),
                'paid_amount' => $thisMonth->paid()->sum('total_amount'),
                'pending_amount' => $thisMonth->sent()->sum('total_amount')
            ],
            'this_year' => [
                'count' => $thisYear->count(),
                'total_amount' => $thisYear->sum('total_amount'),
                'paid_amount' => $thisYear->paid()->sum('total_amount'),
                'pending_amount' => $thisYear->sent()->sum('total_amount')
            ],
            'by_status' => [
                'draft' => Invoice::draft()->count(),
                'sent' => Invoice::sent()->count(),
                'paid' => Invoice::paid()->count(),
                'overdue' => Invoice::overdue()->count()
            ],
            'overdue_amount' => Invoice::overdue()->sum('total_amount')
        ];
    }

    /**
     * Duplikuje faktúru
     */
    public function duplicateInvoice(Invoice $originalInvoice): Invoice
    {
        $data = $originalInvoice->toArray();
        
        // Odstráň polia ktoré sa nemajú duplikovať
        unset($data['id'], $data['invoice_number'], $data['pdf_path'], 
              $data['sent_at'], $data['paid_at'], $data['created_at'], $data['updated_at']);
        
        // Nastav nové dátumy
        $data['issue_date'] = now();
        $data['due_date'] = now()->addDays(14);
        $data['delivery_date'] = now();
        $data['status'] = 'draft';

        return $this->createManualInvoice($data);
    }

    /**
     * Exportuje faktúry do CSV
     */
    public function exportToCSV(array $filters = []): string
    {
        $query = Invoice::with(['user', 'adPayment']);

        // Aplikuj filtre
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('issue_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('issue_date', '<=', $filters['date_to']);
        }

        $invoices = $query->get();

        $filename = 'faktury_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $path = "exports/{$filename}";

        $csv = "Číslo faktúry;Zákazník;Email;Dátum vystavenia;Dátum splatnosti;Stav;Suma bez DPH;DPH;Celková suma;Mena\n";

        foreach ($invoices as $invoice) {
            $csv .= sprintf(
                "%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n",
                $invoice->invoice_number,
                $invoice->customer_data['name'] ?? '',
                $invoice->customer_data['email'] ?? '',
                $invoice->issue_date->format('d.m.Y'),
                $invoice->due_date->format('d.m.Y'),
                $invoice->status_label,
                number_format($invoice->subtotal, 2, ',', ''),
                number_format($invoice->tax_amount, 2, ',', ''),
                number_format($invoice->total_amount, 2, ',', ''),
                $invoice->currency
            );
        }

        Storage::disk('private')->put($path, $csv);

        return $path;
    }

    /**
     * Vypočíta základnú cenu predplatného - používa finálnu cenu z cenníka
     */
    private function calculateBasePrice(AdPayment $payment): float
    {
        // Ceny sú finálne podľa cenníka - žiadne prirážky sa neodpočítavajú
        // Ak niekto zaplatí 10€ za 5 dní, na faktúre bude 10€
        return $payment->amount;
    }

    /**
     * Získa predvolené položky pre nový inzerát
     */
    public function getDefaultAdItems(int $duration = 30): array
    {
        return [
            [
                'name' => "Predplatné inzerátu - {$duration} dní",
                'description' => "Zobrazovanie inzerátu na Erotikon.sk",
                'quantity' => 1,
                'unit' => 'ks',
                'unit_price' => $this->getPriceForDuration($duration),
                'tax_rate' => 0,
                'tax_amount' => 0,
                'total' => 0
            ]
        ];
    }

    /**
     * Získa cenu pre danú dobu predplatného
     */
    private function getPriceForDuration(int $days): float
    {
        return match($days) {
            7 => 9.99,
            14 => 17.99,
            30 => 29.99,
            60 => 54.99,
            90 => 79.99,
            default => round($days * 1.0, 2)
        };
    }

    /**
     * Get company logo as base64 for PDF
     */
    private function getCompanyLogo(): ?string
    {
        $logoPath = public_path('images/uploads/erotikon-logo.webp');
        
        if (!file_exists($logoPath)) {
            return null;
        }

        try {
            $logoData = file_get_contents($logoPath);
            return "data:image/webp;base64," . base64_encode($logoData);
        } catch (\Exception $e) {
            Log::warning('Could not load company logo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get default supplier data
     */
    public function getDefaultSupplierData(): array
    {
        return [
            'name' => env('COMPANY_NAME', 'Erotikon.sk'),
            'address' => env('COMPANY_ADDRESS', ''),
            'city' => env('COMPANY_CITY', ''),
            'postal_code' => env('COMPANY_POSTAL_CODE', ''),
            'country' => 'Slovensko',
            'ico' => env('COMPANY_ICO', ''),
            'dic' => env('COMPANY_DIC', ''),
            'ic_dph' => env('COMPANY_IC_DPH', ''),
            'phone' => env('COMPANY_PHONE', ''),
            'email' => env('COMPANY_EMAIL', 'info@erotikon.sk'),
        ];
    }

    /**
     * Get default payment info
     */
    public function getDefaultPaymentInfo(): array
    {
        return [
            'iban' => env('COMPANY_IBAN', ''),
            'swift' => env('COMPANY_SWIFT', ''),
            'variable_symbol' => '',
            'constant_symbol' => env('COMPANY_CONSTANT_SYMBOL', '0308'),
        ];
    }
} 