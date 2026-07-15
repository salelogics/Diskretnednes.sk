<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\AdPayment;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminPaymentsController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index()
    {
        // Získaj platby s pagináciou
        $payments = AdPayment::with(['user', 'ad', 'paymentPackage', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Štatistiky pre view
        $totalInvoices = AdPayment::count();
        $paidInvoices = AdPayment::where('status', 'completed')->count();
        $pendingInvoices = AdPayment::where('status', 'pending')->count();
        $overdueInvoices = AdPayment::where('status', 'failed')->count();
        $totalRevenue = AdPayment::where('status', 'completed')->sum('amount');
        $pendingRevenue = AdPayment::where('status', 'pending')->sum('amount');
        
        // Použijeme priamo payments ako invoices pre kompatibilitu s view
        $invoices = $payments;
        
        return view('admin.payments.index', compact(
            'payments', 
            'invoices',
            'totalInvoices',
            'paidInvoices', 
            'pendingInvoices',
            'overdueInvoices',
            'totalRevenue',
            'pendingRevenue'
        ));
    }

    public function downloadInvoice(AdPayment $platby)
    {
        try {
            // SMS platby nemajú faktúru
            if ($platby->payment_method === 'sms') {
                return response()->json([
                    'success' => false,
                    'message' => 'SMS platby nemajú faktúru'
                ], 400);
            }
            
            // Ak platba má faktúru, stiahni ju
            if ($platby->invoice) {
                return $this->invoiceService->downloadPDF($platby->invoice);
            }
            
            // Ak nemá faktúru, vygeneruj ju
            $this->invoiceService->createInvoiceFromPayment($platby);
            
            // Refresh platbu aby sme dostali faktúru
            $platby->refresh();
            
            if ($platby->invoice) {
                return $this->invoiceService->downloadPDF($platby->invoice);
            }
            
            throw new \Exception('Nepodarilo sa vytvoriť faktúru');
            
        } catch (\Exception $e) {
            Log::error('Error downloading invoice: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri sťahovaní faktúry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function regenerateInvoice(Invoice $invoice)
    {
        try {
            // Test PDF generation
            $pdfContent = $this->invoiceService->generatePDF($invoice);
            
            if (strlen($pdfContent) > 1000) {
                Log::info('Invoice regenerated successfully', ['invoice_id' => $invoice->id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Faktúra bola úspešne pregenerovaná.'
                ]);
            } else {
                throw new \Exception('Generated PDF is too small');
            }
            
        } catch (\Exception $e) {
            Log::error('Error regenerating invoice: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri pregenerovaní faktúry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendInvoice(Invoice $invoice)
    {
        try {
            $success = $this->invoiceService->sendInvoiceByEmail($invoice);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Faktúra bola úspešne odoslaná na email zákazníka.'
                ]);
            } else {
                throw new \Exception('Failed to send invoice email');
            }
            
        } catch (\Exception $e) {
            Log::error('Error sending invoice: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri odosielaní faktúry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(AdPayment $platby)
    {
        $platby->load(['user', 'ad', 'ad.photos', 'paymentPackage', 'invoice']);
        
        return view('admin.payments.show', [
            'payment' => $platby
        ]);
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.payments.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'due_days' => 'required|integer|min:1|max:365',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Calculate totals
        $subtotal = 0;
        $taxAmount = 0;
        
        foreach ($validated['items'] as $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $itemTax = $itemSubtotal * ($item['tax_percentage'] / 100);
            
            $subtotal += $itemSubtotal;
            $taxAmount += $itemTax;
        }

        $totalAmount = $subtotal + $taxAmount;

        // Generate invoice number
        $year = now()->year;
        $lastInvoice = Invoice::whereYear('issue_date', $year)
                             ->orderBy('invoice_number', 'desc')
                             ->first();

        if (!$lastInvoice) {
            $invoiceNumber = $year . '001';
        } else {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -3);
            $newNumber = $lastNumber + 1;
            $invoiceNumber = $year . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        }

        // Create invoice
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $user->id,
            'issue_date' => now(),
            'due_date' => now()->addDays($validated['due_days']),
            'delivery_date' => now(),
            'status' => 'draft',
            'supplier_data' => [
                'name' => env('COMPANY_NAME', 'DiskretneDnes.sk'),
                'address' => env('COMPANY_ADDRESS', ''),
                'city' => env('COMPANY_CITY', ''),
                'postal_code' => env('COMPANY_POSTAL_CODE', ''),
                'country' => 'Slovensko',
                'ico' => env('COMPANY_ICO', ''),
                'dic' => env('COMPANY_DIC', ''),
                'ic_dph' => env('COMPANY_IC_DPH', ''),
                'phone' => env('COMPANY_PHONE', ''),
                'email' => env('COMPANY_EMAIL', 'info@diskretnednes.sk'),
            ],
            'customer_data' => [
                'name' => $user->name,
                'email' => $user->email,
                'address' => $user->address ?? '',
                'city' => $user->city ?? '',
                'postal_code' => $user->postal_code ?? '',
                'country' => 'Slovensko',
                'phone' => $user->phone ?? '',
            ],
            'items' => $validated['items'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'currency' => 'EUR',
            'notes' => $validated['notes'],
            'payment_info' => [
                'iban' => env('COMPANY_IBAN', ''),
                'swift' => env('COMPANY_SWIFT', ''),
                'variable_symbol' => str_replace(['/', '-', ' '], '', $invoiceNumber),
                'constant_symbol' => env('COMPANY_CONSTANT_SYMBOL', '0308'),
            ],
        ]);

        return redirect()->route('admin.payments.index')
                        ->with('success', 'Faktúra bola úspešne vytvorená.');
    }

    /**
     * Schváliť platbu
     */
    public function approvePayment(AdPayment $payment)
    {
        try {
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Platba už bola spracovaná'
                ], 400);
            }

            // Automaticky sa aktivujú len SMS a Stripe platby
            // Bankové prevody musia byť schválené administrátorom
            if ($payment->payment_method === 'bank_transfer') {
                Log::info('Admin approving bank transfer payment', [
                    'payment_id' => $payment->payment_id,
                    'admin_id' => auth()->id(),
                    'amount' => $payment->amount
                ]);
            }

            $payment->markAsCompleted();
            $payment->generateInvoiceNumber();
            
            // Logujeme kto platbu potvrdil
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'approved_by_admin' => auth()->id(),
                    'approved_at' => now()->toISOString(),
                    'approval_note' => 'Platba potvrdená administrátorom'
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Platba bola úspešne potvrdená a inzerát aktivovaný!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri potvrdzovaní platby: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Zamietnuť platbu
     */
    public function rejectPayment(AdPayment $payment)
    {
        try {
            if ($payment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Platba už bola spracovaná'
                ], 400);
            }

            $payment->markAsFailed();
            
            // Logujeme kto platbu zamietol
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'rejected_by_admin' => auth()->id(),
                    'rejected_at' => now()->toISOString(),
                    'rejection_note' => 'Platba zamietnutá administrátorom'
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Platba bola úspešne zamietnutá!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Chyba pri zamietaní platby: ' . $e->getMessage()
            ], 500);
        }
    }
} 