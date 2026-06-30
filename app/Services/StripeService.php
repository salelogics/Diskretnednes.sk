<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use App\Models\AdPayment;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        // Načítame Stripe secret key z admin nastavení (fallback na .env)
        $secretKey = \App\Models\Setting::get('stripe_secret_key') ?? config('services.stripe.secret');
        
        if (!$secretKey) {
            throw new \Exception('Stripe secret key nie je nastavený. Nastavte ho v administrácii alebo .env súbore.');
        }
        
        Stripe::setApiKey($secretKey);
    }

    /**
     * Vytvorenie Payment Intent pre platbu
     */
    public function createPaymentIntent(AdPayment $payment, array $customerData = null): array
    {
        try {
            $paymentIntentData = [
                'amount' => $this->convertToStripeAmount($payment->amount),
                'currency' => strtolower($payment->currency),
                'payment_method_types' => ['card'], // Explicitne povolíme karty
                'metadata' => [
                    'payment_id' => $payment->payment_id,
                    'ad_id' => $payment->ad_id,
                    'user_id' => $payment->user_id,
                    'package_name' => $payment->paymentPackage->name ?? '',
                ],
                'description' => "Predplatné inzerátu #{$payment->ad_id} - {$payment->paymentPackage->name}",
                'receipt_email' => $payment->user->email,
                'confirmation_method' => 'automatic',
                'confirm' => false,
            ];

            // Pridáme údaje zákazníka ak sú poskytnuté
            if ($customerData) {
                // Pridáme email zákazníka ak je poskytnutý
                if (!empty($customerData['email'])) {
                    $paymentIntentData['receipt_email'] = $customerData['email'];
                }

                // Pridáme údaje zákazníka do metadata
                $paymentIntentData['metadata']['customer_name'] = trim(($customerData['firstName'] ?? '') . ' ' . ($customerData['lastName'] ?? ''));
                $paymentIntentData['metadata']['customer_email'] = $customerData['email'] ?? '';
                $paymentIntentData['metadata']['customer_address'] = $customerData['address'] ?? '';
                $paymentIntentData['metadata']['customer_city'] = $customerData['city'] ?? '';
                $paymentIntentData['metadata']['customer_postal_code'] = $customerData['postalCode'] ?? '';
                $paymentIntentData['metadata']['customer_country'] = $customerData['country'] ?? '';

                // Aktualizujeme popis s menom zákazníka
                if (!empty($customerData['firstName']) || !empty($customerData['lastName'])) {
                    $customerName = trim(($customerData['firstName'] ?? '') . ' ' . ($customerData['lastName'] ?? ''));
                    $paymentIntentData['description'] = "Predplatné inzerátu #{$payment->ad_id} - {$payment->paymentPackage->name} pre {$customerName}";
                }
            }

            $paymentIntent = PaymentIntent::create($paymentIntentData);

            // Uložíme Stripe Payment Intent ID
            $payment->update([
                'gateway_payment_id' => $paymentIntent->id,
                'gateway_response' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'status' => $paymentIntent->status,
                    'customer_data' => $customerData
                ]
            ]);

            Log::info('Stripe Payment Intent created', [
                'payment_id' => $payment->payment_id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $payment->amount,
                'customer_name' => $customerData ? trim(($customerData['firstName'] ?? '') . ' ' . ($customerData['lastName'] ?? '')) : null
            ]);

            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'publishable_key' => \App\Models\Setting::get('stripe_publishable_key') ?? config('services.stripe.key'),
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent creation failed', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
            ];
        }
    }

    /**
     * Vytvorenie Checkout Session pre presmerovanie na Stripe
     */
    public function createCheckoutSession(AdPayment $payment): array
    {
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($payment->currency),
                        'product_data' => [
                            'name' => $payment->paymentPackage->name,
                            'description' => "Predplatné inzerátu #{$payment->ad_id} na {$payment->duration_label}",
                        ],
                        'unit_amount' => $this->convertToStripeAmount($payment->amount),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('ads.payment.stripe.success', $payment->payment_id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('ads.payment.stripe.cancel', $payment->payment_id),
                'metadata' => [
                    'payment_id' => $payment->payment_id,
                    'ad_id' => $payment->ad_id,
                    'user_id' => $payment->user_id,
                ],
                'customer_email' => $payment->user->email,
                'billing_address_collection' => 'auto',
            ]);

            // Uložíme Stripe Session ID
            $payment->update([
                'gateway_session_id' => $session->id,
                'gateway_response' => [
                    'session_id' => $session->id,
                    'checkout_url' => $session->url,
                    'status' => $session->status,
                ]
            ]);

            Log::info('Stripe Checkout Session created', [
                'payment_id' => $payment->payment_id,
                'stripe_session_id' => $session->id,
                'checkout_url' => $session->url,
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Session creation failed', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
            ];
        }
    }

    /**
     * Overenie platby cez Payment Intent
     */
    public function verifyPaymentIntent(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount_received' => $paymentIntent->amount_received,
                'payment_intent' => $paymentIntent,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent verification failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Overenie platby cez Checkout Session
     */
    public function verifyCheckoutSession(string $sessionId): array
    {
        try {
            $session = Session::retrieve($sessionId);

            return [
                'success' => true,
                'status' => $session->status,
                'payment_status' => $session->payment_status,
                'amount_total' => $session->amount_total,
                'session' => $session,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Session verification failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Spracovanie webhook udalosti
     */
    public function handleWebhook(array $payload, string $signature): array
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                json_encode($payload),
                $signature,
                config('services.stripe.webhook_secret')
            );

            Log::info('Stripe webhook received', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event->data->object);

                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($event->data->object);

                case 'checkout.session.completed':
                    return $this->handleCheckoutSessionCompleted($event->data->object);

                default:
                    Log::info('Unhandled Stripe webhook event', ['event_type' => $event->type]);
                    return ['success' => true, 'message' => 'Event not handled'];
            }

        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Spracovanie úspešnej platby
     */
    private function handlePaymentIntentSucceeded($paymentIntent): array
    {
        $paymentId = $paymentIntent->metadata->payment_id ?? null;

        if (!$paymentId) {
            Log::warning('Payment Intent succeeded but no payment_id in metadata', [
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);
            return ['success' => false, 'error' => 'No payment_id in metadata'];
        }

        $payment = AdPayment::where('payment_id', $paymentId)->first();

        if (!$payment) {
            Log::warning('Payment Intent succeeded but payment not found', [
                'payment_id' => $paymentId,
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);
            return ['success' => false, 'error' => 'Payment not found'];
        }

        if ($payment->status !== 'completed') {
            $payment->markAsCompleted();
            $payment->generateInvoiceNumber();

            Log::info('Payment marked as completed via Stripe webhook', [
                'payment_id' => $payment->payment_id,
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);
        }

        return ['success' => true, 'message' => 'Payment completed'];
    }

    /**
     * Spracovanie neúspešnej platby
     */
    private function handlePaymentIntentFailed($paymentIntent): array
    {
        $paymentId = $paymentIntent->metadata->payment_id ?? null;

        if (!$paymentId) {
            return ['success' => false, 'error' => 'No payment_id in metadata'];
        }

        $payment = AdPayment::where('payment_id', $paymentId)->first();

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }

        $payment->markAsFailed();

        Log::info('Payment marked as failed via Stripe webhook', [
            'payment_id' => $payment->payment_id,
            'stripe_payment_intent_id' => $paymentIntent->id,
        ]);

        return ['success' => true, 'message' => 'Payment marked as failed'];
    }

    /**
     * Spracovanie dokončenej Checkout Session
     */
    private function handleCheckoutSessionCompleted($session): array
    {
        $paymentId = $session->metadata->payment_id ?? null;

        if (!$paymentId) {
            return ['success' => false, 'error' => 'No payment_id in metadata'];
        }

        $payment = AdPayment::where('payment_id', $paymentId)->first();

        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }

        if ($payment->status !== 'completed') {
            $payment->markAsCompleted();
            $payment->generateInvoiceNumber();

            Log::info('Payment marked as completed via Stripe Checkout webhook', [
                'payment_id' => $payment->payment_id,
                'stripe_session_id' => $session->id,
            ]);
        }

        return ['success' => true, 'message' => 'Payment completed'];
    }

    /**
     * Konverzia sumy na Stripe formát (v centoch)
     */
    private function convertToStripeAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Konverzia sumy zo Stripe formátu
     */
    private function convertFromStripeAmount(int $amount): float
    {
        return $amount / 100;
    }
} 