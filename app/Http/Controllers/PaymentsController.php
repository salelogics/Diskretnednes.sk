<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AdPayment;
use App\Models\Ad;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;

class PaymentsController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Získaj platby používateľa s pagináciou (25 na stránku)
        $payments = AdPayment::with(['ad', 'paymentPackage'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Transformuj dáta pre view
        $paymentsData = $payments->map(function ($payment) {
            // Logika pre možnosť predplatenia znovu
            $can_renew = false;
            if ($payment->status === 'completed' && $payment->subscription_ends_at) {
                // Môže predplatiť znovu len ak sa predplatné skončilo
                $can_renew = $payment->subscription_ends_at->isPast();
            } elseif (in_array($payment->status, ['failed', 'cancelled'])) {
                // Môže predplatiť znovu ak platba zlyhala
                $can_renew = true;
            }
            
            return [
                'id' => 'PAY-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
                'ad_id' => 'AD-' . str_pad($payment->ad_id, 6, '0', STR_PAD_LEFT),
                'date' => $payment->created_at->format('Y-m-d H:i:s'),
                'method' => $this->getPaymentMethodLabel($payment->payment_method),
                'duration' => $this->getDurationLabel($payment->paymentPackage),
                'amount' => $payment->amount,
                'status' => $payment->status,
                'invoice_number' => $payment->invoice ? $payment->invoice->invoice_number : null,
                'payment_id' => $payment->id,
                'ad_title' => $payment->ad ? ($payment->ad->nickname ?: 'Inzerát #' . $payment->ad->id) : 'Neznámy inzerát',
                'has_invoice' => in_array($payment->payment_method, ['bank_transfer', 'stripe']) && $payment->status === 'completed',
                'payment_method' => $payment->payment_method,
                'is_sms' => $payment->payment_method === 'sms',
                'can_renew' => $can_renew,
                'subscription_ends_at' => $payment->subscription_ends_at ? $payment->subscription_ends_at->format('d.m.Y H:i') : null,
                'subscription_expired' => $payment->subscription_ends_at ? $payment->subscription_ends_at->isPast() : false
            ];
        });

        // Štatistiky
        $allPayments = AdPayment::where('user_id', $user->id)->get();
        $stats = [
            'total_spent' => $allPayments->where('status', 'completed')->sum('amount'),
            'total_payments' => $allPayments->count(),
            'last_payment' => [
                'amount' => $allPayments->where('status', 'completed')->sortByDesc('created_at')->first()?->amount ?? 0,
                'date' => $allPayments->where('status', 'completed')->sortByDesc('created_at')->first()?->created_at ?? now()
            ],
            'most_used_method' => $this->getMostUsedMethod($allPayments)
        ];

        return view('payments.index', [
            'payments' => $paymentsData,
            'stats' => $stats,
            'pagination' => $payments
        ]);
    }

    private function getPaymentMethodLabel($method)
    {
        return match($method) {
            'card' => 'Karta',
            'bank_transfer' => 'Prevodom',
            'sms' => 'SMS',
            default => 'Neznámy'
        };
    }

    private function getDurationLabel($package)
    {
        if (!$package) return 'Neznáme';
        
        $days = $package->duration_days;
        if ($days == 1) return '1 deň';
        if ($days < 5) return $days . ' dni';
        if ($days < 30) return $days . ' dní';
        if ($days == 30) return '1 mesiac';
        if ($days < 365) return round($days / 30) . ' mesiacov';
        return '1 rok';
    }

    private function getMostUsedMethod($payments)
    {
        $methods = $payments->groupBy('payment_method')->map->count()->sortDesc();
        $mostUsed = $methods->keys()->first();
        return $this->getPaymentMethodLabel($mostUsed);
    }

    public function downloadInvoice($payment_id)
    {
        $user = Auth::user();
        
        // Nájdi platbu podľa ID a overi, že patrí používateľovi
        $payment = AdPayment::with('invoice')
            ->where('id', $payment_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$payment) {
            abort(404, 'Platba nebola nájdená');
        }

        // SMS platby nemajú faktúru
        if ($payment->payment_method === 'sms') {
            abort(404, 'SMS platby nemajú faktúru');
        }

        // Faktúra je dostupná len pre dokončené platby
        if ($payment->status !== 'completed') {
            abort(404, 'Faktúra je dostupná len pre dokončené platby');
        }

        try {
            // Ak platba má faktúru, stiahni ju
            if ($payment->invoice) {
                return $this->invoiceService->downloadPDF($payment->invoice);
            }
            
            // Ak nemá faktúru, vytvor ju
            $payment->generateInvoiceNumber();
            $this->invoiceService->createInvoiceFromPayment($payment);
            
            // Refresh platbu aby sme dostali faktúru
            $payment->refresh();
            
            if ($payment->invoice) {
                return $this->invoiceService->downloadPDF($payment->invoice);
            }
            
            throw new \Exception('Nepodarilo sa vytvoriť faktúru');
            
        } catch (\Exception $e) {
            \Log::error('Error downloading invoice PDF: ' . $e->getMessage());
            
            // Fallback - vráť chybovú správu
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri sťahovaní faktúry'
            ], 500);
        }
    }

    public function renewSubscription(Request $request)
    {
        // Presmeruj na subscription modal s predvyplnenými údajmi
        $adId = $request->input('ad_id');
        $duration = $request->input('duration');
        
        return redirect()->route('ads.index')->with([
            'success' => 'Presmerovaný na obnovenie predplatného',
            'open_subscription_modal' => true,
            'modal_ad_id' => $adId,
            'modal_duration' => $duration
        ]);
    }
} 