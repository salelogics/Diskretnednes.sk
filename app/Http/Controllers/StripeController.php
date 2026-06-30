<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AdPayment;
use App\Services\StripeService;

class StripeController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Zobrazenie Stripe platobného formulára
     */
    public function showPaymentForm($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->with(['ad', 'paymentPackage', 'user'])
            ->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('ads.payment.status', $payment->payment_id)
                ->with('error', 'Táto platba už bola spracovaná.');
        }

        // Vytvoríme Payment Intent ak ešte neexistuje
        if (!$payment->gateway_payment_id) {
            $result = $this->stripeService->createPaymentIntent($payment);
            
            if (!$result['success']) {
                return redirect()->route('ads.payment.packages', $payment->ad_id)
                    ->with('error', 'Nepodarilo sa vytvoriť platbu: ' . $result['error']);
            }
        }

        return view('ads.payment.stripe', compact('payment'));
    }

    /**
     * Vytvorenie Payment Intent pre AJAX požiadavku
     */
    public function createPaymentIntent(Request $request, $paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'error' => 'Platba už bola spracovaná'
            ], 400);
        }

        $result = $this->stripeService->createPaymentIntent($payment);

        return response()->json($result);
    }

    /**
     * Vytvorenie Checkout Session a presmerovanie na Stripe
     */
    public function createCheckoutSession($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($payment->status !== 'pending') {
            return redirect()->route('ads.payment.status', $payment->payment_id)
                ->with('error', 'Táto platba už bola spracovaná.');
        }

        $result = $this->stripeService->createCheckoutSession($payment);

        if (!$result['success']) {
            return redirect()->route('ads.payment.packages', $payment->ad_id)
                ->with('error', 'Nepodarilo sa vytvoriť platbu: ' . $result['error']);
        }

        // Presmerujeme na Stripe Checkout
        return redirect($result['checkout_url']);
    }

    /**
     * Spracovanie úspešnej platby z Stripe Checkout
     */
    public function handleSuccess(Request $request, $paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $sessionId = $request->get('session_id');

        if ($sessionId) {
            // Overíme platbu cez Stripe API
            $result = $this->stripeService->verifyCheckoutSession($sessionId);

            if ($result['success'] && $result['payment_status'] === 'paid') {
                // Označíme platbu ako dokončenú ak ešte nie je
                if ($payment->status === 'pending') {
                    $payment->markAsCompleted();
                    $payment->generateInvoiceNumber();

                    Log::info('Payment completed via Stripe Checkout success page', [
                        'payment_id' => $payment->payment_id,
                        'session_id' => $sessionId,
                    ]);
                }

                return redirect()->route('ads.payment.status', $payment->payment_id)
                    ->with('success', 'Platba bola úspešne spracovaná!');
            }
        }

        return redirect()->route('ads.payment.status', $payment->payment_id)
            ->with('warning', 'Platba sa spracováva. Stav bude aktualizovaný čoskoro.');
    }

    /**
     * Spracovanie zrušenej platby z Stripe Checkout
     */
    public function handleCancel($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return redirect()->route('ads.payment.packages', $payment->ad_id)
            ->with('info', 'Platba bola zrušená. Môžete skúsiť znovu.');
    }

    /**
     * Webhook endpoint pre Stripe udalosti
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$signature) {
            Log::warning('Stripe webhook received without signature');
            return response()->json(['error' => 'No signature'], 400);
        }

        $result = $this->stripeService->handleWebhook(
            json_decode($payload, true),
            $signature
        );

        if ($result['success']) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['error' => $result['error']], 400);
        }
    }

    /**
     * AJAX endpoint pre overenie stavu platby
     */
    public function checkPaymentStatus(Request $request, $paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Ak má payment intent ID, overíme stav cez Stripe
        if ($payment->gateway_payment_id) {
            $result = $this->stripeService->verifyPaymentIntent($payment->gateway_payment_id);
            
            if ($result['success']) {
                $stripeStatus = $result['status'];
                
                // Aktualizujeme stav platby podľa Stripe stavu
                if ($stripeStatus === 'succeeded' && $payment->status === 'pending') {
                    $payment->markAsCompleted();
                    $payment->generateInvoiceNumber();
                } elseif ($stripeStatus === 'canceled' && $payment->status === 'pending') {
                    $payment->markAsCancelled();
                }
            }
        }

        return response()->json([
            'status' => $payment->status,
            'status_label' => $payment->status_label,
            'is_completed' => $payment->isCompleted(),
            'is_failed' => $payment->isFailed(),
        ]);
    }

    /**
     * Potvrdenie úspešnej Stripe platby z frontend-u
     */
    public function confirmPayment(Request $request, $paymentId)
    {
        $request->validate([
            'payment_intent_id' => 'required|string'
        ]);

        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Overíme platbu cez Stripe API
        $result = $this->stripeService->verifyPaymentIntent($request->payment_intent_id);

        if ($result['success'] && $result['status'] === 'succeeded') {
            // Označíme platbu ako dokončenú ak ešte nie je
            if ($payment->status === 'pending') {
                $payment->markAsCompleted();
                $payment->generateInvoiceNumber();

                // Uložíme Stripe Payment Intent ID
                $payment->update([
                    'gateway_payment_id' => $request->payment_intent_id,
                    'gateway_response' => array_merge(
                        $payment->gateway_response ?? [],
                        ['confirmed_payment_intent_id' => $request->payment_intent_id]
                    )
                ]);

                Log::info('Payment completed via Stripe inline form', [
                    'payment_id' => $payment->payment_id,
                    'payment_intent_id' => $request->payment_intent_id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Platba bola úspešne spracovaná!'
            ]);
        } else {
            Log::warning('Stripe payment confirmation failed', [
                'payment_id' => $payment->payment_id,
                'payment_intent_id' => $request->payment_intent_id,
                'stripe_result' => $result
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Platba sa nepodarila overiť'
            ], 400);
        }
    }

    /**
     * Simulácia úspešnej platby (iba pre development)
     */
    public function simulateSuccess($paymentId)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($payment->status === 'pending') {
            $payment->markAsCompleted();
            $payment->generateInvoiceNumber();

            Log::info('Payment simulated as successful', [
                'payment_id' => $payment->payment_id,
            ]);
        }

        return redirect()->route('ads.payment.status', $payment->payment_id)
            ->with('success', 'Platba bola simulovaná ako úspešná!');
    }
} 