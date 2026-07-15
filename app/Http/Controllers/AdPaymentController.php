<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ad;
use App\Models\PaymentPackage;
use App\Models\AdPayment;
use App\Services\StripeService;
use App\Services\SmsPaymentService;
use Illuminate\Support\Str;


class AdPaymentController extends Controller
{
    protected $stripeService;
    protected $smsPaymentService;

    public function __construct(StripeService $stripeService, SmsPaymentService $smsPaymentService)
    {
        $this->stripeService = $stripeService;
        $this->smsPaymentService = $smsPaymentService;
    }
    /**
     * Zobrazenie balíčkov pre konkrétny inzerát
     */
    public function showPackages($adId)
    {
        $ad = Ad::where('user_id', Auth::id())->findOrFail($adId);
        
        $classicPackages = PaymentPackage::active()
            ->byType('classic')
            ->ordered()
            ->get();
            
        $premiumPackages = PaymentPackage::active()
            ->byType('premium')
            ->ordered()
            ->get();

        return view('ads.payment.packages', compact('ad', 'classicPackages', 'premiumPackages'));
    }

    /**
     * API endpoint pre načítanie balíčkov (pre admin)
     */
    public function getPackages($ad)
    {
        try {
            \Log::info('getPackages called', ['ad' => $ad, 'user' => auth()->id()]);
            
            $ad = Ad::findOrFail($ad);
            \Log::info('Ad found', ['ad_id' => $ad->id, 'nickname' => $ad->nickname]);
            
            $classicPackages = PaymentPackage::active()
                ->byType('classic')
                ->ordered()
                ->get();
                
            $premiumPackages = PaymentPackage::active()
                ->byType('premium')
                ->ordered()
                ->get();

            \Log::info('Packages loaded', [
                'classic_count' => $classicPackages->count(),
                'premium_count' => $premiumPackages->count()
            ]);

            $allPackages = $classicPackages->merge($premiumPackages)->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'price' => $package->price,
                    'formatted_price' => $package->formatted_price,
                    'duration_days' => $package->duration_days,
                    'duration_label' => $package->duration_label,
                    'type' => $package->type,
                    'is_featured' => $package->is_featured,
                    'is_top_ad' => $package->is_top_ad,
                    'description' => $package->description
                ];
            });

            \Log::info('Response prepared', ['packages_count' => $allPackages->count()]);

            return response()->json([
                'success' => true,
                'packages' => $allPackages,
                'ad' => [
                    'id' => $ad->id,
                    'title' => $ad->nickname ?: "Inzerát #{$ad->id}"
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('getPackages error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => 'Chyba pri načítavaní balíčkov: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint na získanie aktuálneho stavu inzerátu
     */
    public function getAdStatus($adId)
    {
        try {
            $ad = Ad::findOrFail($adId);
            
            return response()->json([
                'success' => true,
                'id' => $ad->id,
                'status' => $ad->status,
                'subscription_status' => $ad->subscription_status,
                'subscription_expires_at' => $ad->subscription_expires_at ? $ad->subscription_expires_at->format('d.m.Y H:i') : null,
                'is_featured' => $ad->is_featured,
                'is_top_ad' => $ad->is_top_ad,
                'updated_at' => $ad->updated_at->toISOString()
            ]);
        } catch (\Exception $e) {
            \Log::error('getAdStatus error', [
                'ad_id' => $adId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Inzerát sa nenašiel'
            ], 404);
        }
    }

    /**
     * Vytvorenie platby
     */
    public function createPayment(Request $request, $adId)
    {
        try {
            // Debug logging
            \Log::info('createPayment called', [
                'request_data' => $request->all(),
                'ad_id' => $adId,
                'user_id' => Auth::id(),
                'is_admin' => Auth::user() ? Auth::user()->isAdmin() : false,
                'is_authenticated' => Auth::check()
            ]);

            $request->validate([
                'package_id' => 'required|exists:payment_packages,id',
                // Zadarmo balíček (napr. Classic) nepotrebuje spôsob platby - viď vetva nižšie.
                'payment_method' => 'nullable|in:bank_transfer,qr_code,sms',
                'customer_data' => 'sometimes|array',
                'customer_data.firstName' => 'sometimes|string|max:255',
                'customer_data.lastName' => 'sometimes|string|max:255',
                'customer_data.email' => 'sometimes|email|max:255',
                'customer_data.address' => 'sometimes|string|max:255',
                'customer_data.city' => 'sometimes|string|max:255',
                'customer_data.postalCode' => 'sometimes|string|max:20',
                'customer_data.country' => 'sometimes|string|max:2'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in createPayment', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Chyba validácie údajov',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception in createPayment start', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri spracovaní požiadavky: ' . $e->getMessage()
            ], 500);
        }

        try {
            // Pre admin povoliť platbu pre akýkoľvek inzerát, pre user len vlastné
        // Pre neprihlásených užívateľov povoliť platbu za akýkoľvek inzerát
        if (Auth::user() && Auth::user()->isAdmin()) {
            $ad = Ad::findOrFail($adId);
        } elseif (Auth::check()) {
            // Prihlásení užívatelia môžu platiť len za vlastné inzeráty
            $ad = Ad::where('user_id', Auth::id())->findOrFail($adId);
        } else {
            // Neprihlásení užívatelia môžu platiť za akýkoľvek inzerát
            $ad = Ad::findOrFail($adId);
        }
        
        $package = PaymentPackage::active()->findOrFail($request->package_id);

        \Log::info('Payment creation data', [
            'ad_id' => $ad->id,
            'package_id' => $package->id,
            'package_name' => $package->name,
            'payment_method' => $request->payment_method,
            'customer_data' => $request->customer_data
        ]);

        // Zadarmo balíček (napr. Classic) - aktivujeme rovno, bez Stripe/SMS/prevodu.
        if ($package->is_free) {
            $payment = AdPayment::create([
                'user_id' => $ad->user_id,
                'ad_id' => $ad->id,
                'payment_package_id' => $package->id,
                'payment_id' => str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
                'amount' => 0,
                'currency' => 'EUR',
                'payment_method' => 'free',
                'status' => 'pending',
                'duration_days' => $package->duration_days,
                'is_featured' => $package->is_featured,
                'is_top_ad' => $package->is_top_ad,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'package_name' => $package->name,
                    'package_type' => $package->type,
                    'created_by_admin' => (Auth::user() && Auth::user()->isAdmin()) ? true : false
                ]
            ]);

            $payment->markAsCompleted();

            \Log::info('Free package activated', [
                'ad_id' => $ad->id,
                'payment_id' => $payment->payment_id,
                'package_id' => $package->id
            ]);

            return response()->json([
                'success' => true,
                'payment_id' => $payment->payment_id,
                'amount' => $payment->formatted_amount,
                'payment_method' => $payment->payment_method,
                'payment_method_label' => $payment->payment_method_label,
                'package_name' => $package->name,
                'ad_title' => $ad->title ?? "Inzerát #{$ad->id}",
                'free' => true
            ]);
        }

        if (!$request->payment_method) {
            return response()->json([
                'success' => false,
                'message' => 'Chyba validácie údajov',
                'errors' => ['payment_method' => ['Vyberte spôsob platby.']]
            ], 422);
        }

        // Pripravíme metadata s údajmi zákazníka
        $metadata = [
            'package_name' => $package->name,
            'package_type' => $package->type,
            'created_by_admin' => (Auth::user() && Auth::user()->isAdmin()) ? true : false
        ];

        // Pridáme údaje zákazníka ak sú poskytnuté
        if ($request->has('customer_data')) {
            $metadata['customer_data'] = $request->customer_data;
        }

        // Vytvorenie platby
        $payment = AdPayment::create([
            'user_id' => $ad->user_id, // Použiť user_id z inzerátu
            'ad_id' => $ad->id,
            'payment_package_id' => $package->id,
            'payment_id' => str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'amount' => $package->price,
            'currency' => 'EUR',
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'duration_days' => $package->duration_days,
            'is_featured' => $package->is_featured,
            'is_top_ad' => $package->is_top_ad,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => $metadata
        ]);

        // Generuj QR kód ak je potrebný
        $qrCodeData = null;
        if ($request->payment_method === 'qr_code') {
            try {
                $qrCodeData = $this->generateQRCode($payment);
                \Log::info('QR Code generated successfully', ['qr_data' => $qrCodeData]);
            } catch (\Exception $e) {
                \Log::error('QR Code generation failed: ' . $e->getMessage());
                // QR kód sa nepodarilo vygenerovať, ale platba pokračuje
            }
        }

        // Pre Stripe platby vytvoríme Payment Intent
        if ($request->payment_method === 'stripe') {
            try {
                $result = $this->stripeService->createPaymentIntent($payment, $request->customer_data);
                
                if (!$result['success']) {
                    \Log::error('Stripe payment intent creation failed', [
                        'payment_id' => $payment->payment_id,
                        'error' => $result['error'] ?? 'Unknown error',
                        'error_code' => $result['error_code'] ?? null
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Chyba pri vytváraní Stripe platby: ' . ($result['error'] ?? 'Neznáma chyba')
                    ], 500);
                }
                
                return response()->json([
                    'success' => true,
                    'payment_id' => $payment->payment_id,
                    'amount' => $payment->formatted_amount,
                    'payment_method' => $payment->payment_method,
                    'payment_method_label' => $payment->payment_method_label,
                    'package_name' => $package->name,
                    'ad_title' => $ad->title ?? "Inzerát #{$ad->id}",
                    'client_secret' => $result['client_secret'],
                    'publishable_key' => $result['publishable_key']
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Stripe service exception', [
                    'payment_id' => $payment->payment_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe platby sú dočasne nedostupné. Skúste inú metódu platby alebo to neskôr.'
                ], 500);
            }
        }

        // Pre SMS platby vytvoríme presmerovanie na PlatbaMobilom.sk
        if ($request->payment_method === 'sms') {
            if (!$this->smsPaymentService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'error' => 'SMS platby nie sú nakonfigurované'
                ], 500);
            }

            $returnUrl = route('ads.payment.sms.return', $payment->payment_id);
            $description = $package->name; // Napr. "1 den EROFOX"
            
            $smsParams = $this->smsPaymentService->preparePaymentData(
                $ad->id,
                $description,
                $package->price,
                $returnUrl,
                $ad->user->email
            );

            // Uložíme SMS parametre do platby
            $payment->update([
                'gateway_payment_id' => $smsParams['ID'],
                'gateway_response' => $smsParams
            ]);

            return response()->json([
                'success' => true,
                'payment_id' => $payment->payment_id,
                'payment_method' => 'sms',
                'sms_redirect_url' => $this->smsPaymentService->getPaymentUrl(),
                'sms_params' => $smsParams
            ]);
        }

            // Vrátime JSON response pre popup
            return response()->json([
                'success' => true,
                'payment_id' => $payment->payment_id,
                'amount' => $payment->formatted_amount,
                'payment_method' => $payment->payment_method,
                'payment_method_label' => $payment->payment_method_label,
                'package_name' => $package->name,
                'ad_title' => $ad->title ?? "Inzerát #{$ad->id}",
                'qr_code' => $qrCodeData
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in createPayment main', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ad_id' => $adId,
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Chyba pri vytváraní platby: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Zobrazenie stavu platby
     */
    public function showPaymentStatus($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->with(['ad', 'paymentPackage'])
            ->firstOrFail();

        return view('ads.payment.status', compact('payment'));
    }

    /**
     * Callback pre úspešnú platbu
     */
    public function paymentSuccess(Request $request, $paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($payment->status === 'pending') {
            // Automaticky aktivuj len SMS a Stripe platby
            // Bank transfer musí schváliť admin
            if (in_array($payment->payment_method, ['sms', 'stripe'])) {
                DB::transaction(function () use ($payment) {
                    $payment->markAsCompleted();
                    $payment->generateInvoiceNumber();
                });
                
                \Log::info('Payment auto-activated', [
                    'payment_id' => $payment->payment_id,
                    'payment_method' => $payment->payment_method
                ]);
            } else {
                \Log::info('Payment waiting for admin approval', [
                    'payment_id' => $payment->payment_id,
                    'payment_method' => $payment->payment_method
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $payment->payment_method === 'bank_transfer' 
                ? 'Platba bola zaznamenaná. Čaká na schválenie administrátorom.'
                : 'Platba bola úspešne spracovaná!',
            'payment_id' => $payment->payment_id,
            'status' => $payment->status,
            'requires_admin_approval' => $payment->payment_method === 'bank_transfer'
        ]);
    }

    /**
     * Callback pre neúspešnú platbu
     */
    public function paymentFailed(Request $request, $paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $payment->markAsFailed();

        return redirect()->route('ads.payment.status', $payment->payment_id)
            ->with('error', 'Platba sa nepodarila. Skúste to znovu.');
    }

    /**
     * Zrušenie platby
     */
    public function cancelPayment($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $payment->markAsCancelled();

        return redirect()->route('ads.index')
            ->with('info', 'Platba bola zrušená.');
    }

    /**
     * Stiahnuť faktúru
     */
    public function downloadInvoice($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with(['ad', 'paymentPackage', 'user'])
            ->firstOrFail();

        if (!$payment->invoice_number) {
            $payment->generateInvoiceNumber();
        }

        // Generovanie faktúry (zatiaľ jednoduchý text)
        $content = $this->generateInvoiceContent($payment);

        return response($content)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="faktura-' . $payment->invoice_number . '.txt"');
    }

    /**
     * Generovanie obsahu faktúry
     */
    private function generateInvoiceContent(AdPayment $payment): string
    {
        return "
FAKTÚRA: {$payment->invoice_number}
========================================

Dodávateľ:
DiskretneDnes.sk
Bratislava, Slovensko

Odberateľ:
{$payment->user->name}
{$payment->user->email}

Dátum vystavenia: {$payment->created_at->format('d.m.Y')}
Dátum splatnosti: {$payment->created_at->format('d.m.Y')}

Položky:
----------------------------------------
{$payment->paymentPackage->name}
Inzerát ID: {$payment->ad->id}
Trvanie: {$payment->duration_label}
Cena: {$payment->formatted_amount}

Celková suma: {$payment->formatted_amount}

Spôsob platby: {$payment->payment_method_label}
Stav: {$payment->status_label}

Ďakujeme za využitie našich služieb!
";
    }

    /**
     * Stripe úspešná platba
     */
    public function stripeSuccess(Request $request, $paymentId)
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return redirect()->route('ads.payment.status', $paymentId)
                ->with('error', 'Chýba session ID');
        }

        $result = $this->stripeService->handleSuccessfulPayment($sessionId);
        
        if ($result['success']) {
            return redirect()->route('ads.payment.status', $paymentId)
                ->with('success', 'Platba bola úspešne spracovaná!');
        } else {
            return redirect()->route('ads.payment.status', $paymentId)
                ->with('error', 'Chyba pri spracovaní platby: ' . $result['error']);
        }
    }

    /**
     * Stripe zrušená platba
     */
    public function stripeCancel($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $payment->update(['status' => 'cancelled']);

        return redirect()->route('ads.payment.status', $paymentId)
            ->with('error', 'Platba bola zrušená');
    }

    /**
     * Stripe webhook
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        $result = $this->stripeService->handleWebhook($payload, $signature);

        return response()->json(['status' => $result['success'] ? 'ok' : 'error']);
    }

    /**
     * Spracovanie návratu z SMS platby
     */
    public function smsReturn(Request $request, $paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->with(['ad', 'paymentPackage'])
            ->firstOrFail();

        $id = $request->get('ID');
        $res = $request->get('RES');
        $phone = $request->get('PHONE', '');
        $sign = $request->get('SIGN');

        // Overíme podpis
        if (!$this->smsPaymentService->verifySignature($id, $res, $phone, $sign)) {
            \Log::error('SMS Payment: Invalid signature', [
                'payment_id' => $paymentId,
                'received_params' => $request->all()
            ]);
            
            return redirect()->route('ads.payment.status', $paymentId)
                ->with('error', 'Neplatný podpis platby');
        }

        // Spracujeme výsledok platby
        if ($res === 'OK') {
            // Platba bola úspešná
            if ($payment->status === 'pending') {
                DB::transaction(function () use ($payment, $phone) {
                    $payment->update([
                        'status' => 'completed',
                        'gateway_response' => array_merge(
                            $payment->gateway_response ?? [],
                            ['phone' => $phone, 'result' => 'OK']
                        )
                    ]);
                    $payment->markAsCompleted();
                    $payment->generateInvoiceNumber();
                });

                \Log::info('SMS Payment completed', [
                    'payment_id' => $paymentId,
                    'phone' => $phone
                ]);

                return redirect()->route('ads.payment.status', $paymentId)
                    ->with('success', 'SMS platba bola úspešne spracovaná!');
            }
        } elseif ($res === 'FAIL') {
            // Platba zlyhala
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge(
                    $payment->gateway_response ?? [],
                    ['result' => 'FAIL']
                )
            ]);

            \Log::warning('SMS Payment failed', ['payment_id' => $paymentId]);

            return redirect()->route('ads.payment.status', $paymentId)
                ->with('error', 'SMS platba sa nepodarila');
        } elseif ($res === 'TIMEOUT') {
            // Platba vypršala
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge(
                    $payment->gateway_response ?? [],
                    ['result' => 'TIMEOUT']
                )
            ]);

            \Log::warning('SMS Payment timeout', ['payment_id' => $paymentId]);

            return redirect()->route('ads.payment.status', $paymentId)
                ->with('error', 'SMS platba vypršala');
        }

        return redirect()->route('ads.payment.status', $paymentId);
    }

    /**
     * Webhook pre externé platobné brány
     */
    public function webhook(Request $request)
    {
        // Tu by bola logika pre spracovanie webhookov
        // z rôznych platobných brán (SMS brány, atď.)
        
        \Log::info('Payment webhook received', $request->all());
        
        return response()->json(['status' => 'ok']);
    }



    /**
     * Získanie package ID na základe typu a dĺžky
     */
    public function getPackageId($type, $duration)
    {
        // Debug logging
        \Log::info('getPackageId called', [
            'type' => $type,
            'duration' => $duration
        ]);

        // Validácia parametrov
        if (!in_array($type, ['classic', 'premium'])) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        if (!in_array((int)$duration, [0, 10, 30])) {
            return response()->json(['error' => 'Invalid duration'], 400);
        }

        $package = PaymentPackage::active()
            ->where('type', $type)
            ->where('duration_days', (int)$duration)
            ->first();

        \Log::info('Package search result', [
            'type' => $type,
            'duration' => $duration,
            'package_found' => $package ? true : false,
            'package_id' => $package ? $package->id : null
        ]);

        if (!$package) {
            \Log::warning('Package not found', [
                'type' => $type,
                'duration' => $duration,
                'all_packages' => PaymentPackage::active()->get(['id', 'type', 'duration_days'])->toArray()
            ]);
            return response()->json(['error' => 'Package not found'], 404);
        }

        return response()->json([
            'package_id' => $package->id,
            'price' => $package->formatted_price,
            'name' => $package->name
        ]);
    }

    /**
     * Generovanie QR kódu pre platbu pomocou QRGenerator.sk
     */
    private function generateQRCode(AdPayment $payment): array
    {
        // Vytvoríme platobné údaje pre QR kód
        $paymentData = [
            'iban' => config('app.payment_iban', 'SK8975000000000012345671'),  // Konfigurovateľný IBAN
            'amount' => number_format($payment->amount, 2, '.', ''),
            'currency' => $payment->currency,
            'variable_symbol' => $payment->payment_id,
            'message' => "Inzerat ID:{$payment->ad_id} - DiskretneDnes.sk",
            'recipient_name' => 'DiskretneDnes.sk',
            'due_date' => now()->addDays(7)->format('Y-m-d')
        ];

        // Použijeme QRGenerator.sk API pre Pay By Square QR kód
        // Minimálne parametre pre maximálnu kompatibilitu
        $qrParams = [
            'iban' => $paymentData['iban'],
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'],
            'vs' => $paymentData['variable_symbol']
        ];

        $qrCodeUrl = 'https://api.qrgenerator.sk/by-square/pay/qr.png?' . http_build_query($qrParams);
        
        return [
            'qr_image' => $qrCodeUrl,
            'payment_data' => $paymentData,
            'qr_type' => 'pay_by_square',
            'qr_params' => $qrParams
        ];
    }

    /**
     * Generovanie QR kódu s rozšírenými parametrami (fallback)
     */
    private function generateQRCodeExtended(AdPayment $payment): array
    {
        // Vytvoríme platobné údaje pre QR kód
        $paymentData = [
            'iban' => config('app.payment_iban', 'SK8975000000000012345671'),
            'amount' => number_format($payment->amount, 2, '.', ''),
            'currency' => $payment->currency,
            'variable_symbol' => $payment->payment_id,
            'message' => "Inzerat ID:{$payment->ad_id} - DiskretneDnes.sk",
            'recipient_name' => 'DiskretneDnes.sk',
            'due_date' => now()->addDays(7)->format('Y-m-d')
        ];

        // Rozšírené parametre
        $qrParams = [
            'iban' => $paymentData['iban'],
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'],
            'vs' => $paymentData['variable_symbol'],
            'payment_note' => $paymentData['message'],
            'beneficiary_name' => $paymentData['recipient_name'],
            'due_date' => $paymentData['due_date']
        ];

        $qrCodeUrl = 'https://api.qrgenerator.sk/by-square/pay/qr.png?' . http_build_query($qrParams);
        
        return [
            'qr_image' => $qrCodeUrl,
            'payment_data' => $paymentData,
            'qr_type' => 'pay_by_square_extended',
            'qr_params' => $qrParams
        ];
    }

    /**
     * Získanie QR kódu pre existujúcu platbu
     */
    public function getQRCode($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            $qrCodeData = $this->generateQRCode($payment);

            return response()->json([
                'success' => true,
                'qr_code' => $qrCodeData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Nepodarilo sa vygenerovať QR kód'
            ], 500);
        }
    }

    /**
     * Stiahnuť QR kód ako obrázok pomocou QRGenerator.sk
     */
    public function downloadQRCode($paymentId)
    {
        $payment = AdPayment::where('payment_id', $paymentId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            // Vygenerujeme QR kód pomocou QRGenerator.sk
            $qrParams = [
                'iban' => config('app.payment_iban', 'SK8975000000000012345671'),
                'amount' => number_format($payment->amount, 2, '.', ''),
                'currency' => $payment->currency,
                'vs' => $payment->payment_id,
                'size' => '512'
            ];
            
            // Použijeme QRGenerator.sk API pre stiahnutie QR kódu
            $qrCodeUrl = 'https://api.qrgenerator.sk/by-square/pay/qr.png?' . http_build_query($qrParams);
            
            // Stiahneme obrázok z API
            $qrCodeContent = file_get_contents($qrCodeUrl);
            
            if ($qrCodeContent === false) {
                throw new \Exception('Nepodarilo sa stiahnuť QR kód z QRGenerator.sk');
            }

            return response($qrCodeContent)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="qr-kod-' . $payment->payment_id . '.png"');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Nepodarilo sa vygenerovať QR kód: ' . $e->getMessage());
        }
    }
}
