<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\SmsVerification;

class SmsPaymentService
{
    /**
     * Normalizuje telefónne číslo na slovenský formát
     */
    private function normalizePhoneNumber($phone)
    {
        // Odstránime všetky nečíselné znaky
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ak začína na 421, konvertujeme na 0xxx formát
        if (substr($phone, 0, 3) === '421') {
            $phone = '0' . substr($phone, 3);
        }
        
        // Ak začína na 4219, konvertujeme na 09xx formát  
        if (substr($phone, 0, 4) === '4219') {
            $phone = '09' . substr($phone, 4);
        }
        
        return $phone;
    }

    /**
     * Získame SMS balíčky
     */
    public function getSmsPackages(): array
    {
        return [
            [
                'code' => 'FXO',
                'type' => 'classic',
                'duration' => 1,
                'price' => 5.00,
                'description' => 'Classic 1 den'
            ],
            [
                'code' => 'FXW',
                'type' => 'classic',
                'duration' => 7,
                'price' => 13.00,
                'description' => 'Classic 7 dni'
            ],
            [
                'code' => 'FXM',
                'type' => 'classic',
                'duration' => 30,
                'price' => 25.00,
                'description' => 'Classic 30 dni'
            ]
        ];
    }

    /**
     * Spracuje prijatú SMS - JEDNODUCHÝ SYSTÉM
     */
    public function processSmsReceived($msisdn, $text, $smsId)
    {
        try {
            // Normalizujeme telefónne číslo
            $normalizedPhone = $this->normalizePhoneNumber($msisdn);
            
            Log::info('SMS RECEIVED', [
                'msisdn' => $msisdn,
                'normalized_phone' => $normalizedPhone,
                'text' => $text,
                'sms_id' => $smsId,
                'timestamp' => now()
            ]);

            if (!preg_match('/^ERO\s+([A-Z]{3})$/', $text, $matches)) {
                Log::warning('Invalid SMS format', ['text' => $text]);
                return [
                    'price' => 0,
                    'message' => 'Nespravny format SMS. Pouzite: ERO FXO'
                ];
            }

            $packageCode = $matches[1];
            $packages = $this->getSmsPackages();
            $package = collect($packages)->firstWhere('code', $packageCode);

            if (!$package) {
                Log::warning('Invalid package code', ['code' => $packageCode]);
                return [
                    'price' => 0,
                    'message' => 'Neplatny balicek. Pouzite: FXO, FXW, FXM'
                ];
            }

            // NAJPRV VYTVORÍME ZÁZNAM V DATABÁZE
            $verificationCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            Log::info('Creating SMS verification BEFORE response', [
                'code' => $verificationCode,
                'package' => $packageCode,
                'step' => 'database_first'
            ]);

            // KRITICKÉ: Nájdeme ad_id na základe pending platby
            $adId = $this->findAdIdForSmsPayment($normalizedPhone, $package);

            // Pokúsime sa vytvoriť záznam v databáze
            $verification = null;
            try {
                $verification = SmsVerification::create([
                    'ad_id' => $adId, // ✅ PRIDANÉ!
                    'verification_code' => $verificationCode,
                    'package_code' => $packageCode,
                    'package_type' => $package['type'],
                    'package_duration' => $package['duration'],
                    'price' => $package['price'],
                    'sms_code' => $packageCode,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(25),
                    'msisdn' => $normalizedPhone,
                    'sms_id' => $smsId,
                    'sms_keyword' => 'VERIFY',
                    'is_verified' => false,
                    'is_payment_confirmed' => false,
                    'phone_number' => $normalizedPhone,
                    'transaction_id' => $smsId
                ]);

                Log::info('SMS verification created SUCCESSFULLY', [
                    'id' => $verification->id,
                    'code' => $verificationCode,
                    'step' => 'database_success'
                ]);

            } catch (\Exception $dbError) {
                Log::error('Database error, trying alternative approach', [
                    'error' => $dbError->getMessage(),
                    'step' => 'database_error'
                ]);

                // ALTERNATÍVNY PRÍSTUP
                try {
                    $verification = new SmsVerification();
                    $verification->verification_code = $verificationCode;
                    $verification->package_code = $packageCode;
                    $verification->package_type = $package['type'];
                    $verification->package_duration = $package['duration'];
                    $verification->price = $package['price'];
                    $verification->sms_code = $packageCode;
                    $verification->status = 'pending';
                    $verification->expires_at = now()->addMinutes(25);
                    $verification->msisdn = $normalizedPhone;
                    $verification->sms_id = $smsId;
                    $verification->sms_keyword = 'VERIFY';
                    $verification->is_verified = false;
                    $verification->is_payment_confirmed = false;
                    $verification->phone_number = $normalizedPhone;
                    $verification->transaction_id = $smsId;
                    $verification->save();

                    Log::info('SMS verification created with alternative method', [
                        'id' => $verification->id,
                        'code' => $verificationCode,
                        'step' => 'alternative_success'
                    ]);
                } catch (\Exception $secondError) {
                    Log::error('CRITICAL: Both database methods failed', [
                        'first_error' => $dbError->getMessage(),
                        'second_error' => $secondError->getMessage(),
                        'step' => 'both_failed'
                    ]);
                    
                    // Ak sa nepodarí uložiť do databázy, vrátime chybu
                    return [
                        'price' => 0,
                        'message' => 'Chyba systemu. Skuste to neskor.'
                    ];
                }
            }

            // AK SA PODARILO ULOŽIŤ, VRÁTIME ODPOVEĎ S KÓDOM
            Log::info('Returning SMS response', [
                'verification_id' => $verification ? $verification->id : 'unknown',
                'code' => $verificationCode,
                'step' => 'response_ready'
            ]);

            return [
                'price' => $package['price'],
                'message' => "Aktivacia {$package['type']} {$package['duration']} dni za {$package['price']} EUR. Vas verifikacny kod: {$verificationCode}. Zadajte tento kod na webovej stranke do 25 min."
            ];

        } catch (\Exception $e) {
            Log::error('SMS processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'step' => 'general_error'
            ]);
            return [
                'price' => 0,
                'message' => 'Chyba pri spracovani SMS'
            ];
        }
    }

    /**
     * Potvrdí platbu
     */
    public function confirmPayment($smsId, $result)
    {
        try {
            Log::info('Payment confirmed', [
                'sms_id' => $smsId,
                'result' => $result
            ]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('Payment confirmation error: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Prepare SMS payment data with ad connection
     */
    public function preparePaymentData($adId, $description, $price, $returnUrl, $email)
    {
        $paymentId = 'SMS-' . strtoupper(\Illuminate\Support\Str::random(8));
        
        // Uložíme si ad_id pre neskôr
        \Cache::put("sms_payment_{$paymentId}_ad_id", $adId, now()->addHours(2));
        
        Log::info('SMS payment prepared', [
            'ad_id' => $adId,
            'payment_id' => $paymentId,
            'price' => $price
        ]);
        
        return [
            'ID' => $paymentId,
            'DESC' => $description,
            'PRICE' => $price,
            'RETURN_URL' => $returnUrl,
            'EMAIL' => $email,
            'ad_id' => $adId // Pre debug
        ];
    }

    /**
     * Get payment URL for SMS
     */
    public function getPaymentUrl()
    {
        return 'https://www.platbamobilom.sk/pay'; // Placeholder
    }

    /**
     * Check if SMS payment is configured
     */
    public function isConfigured()
    {
        return true; // Placeholder - add real config check
    }

    /**
     * Overí kód - JEDNODUCHÝ SYSTÉM
     */
    public function verifyCode($code)
    {
        try {
            Log::info('Verifying code', ['code' => $code, 'current_time' => now()->toDateTimeString()]);

            // NAJPRV POZRIEME VŠETKY KÓDY PRE DEBUG
            $allCodes = SmsVerification::where('verification_code', $code)->get();
            Log::info('All matching codes found', [
                'code' => $code,
                'total_found' => $allCodes->count(),
                'codes_data' => $allCodes->map(function($v) {
                    return [
                        'id' => $v->id,
                        'status' => $v->status,
                        'expires_at' => $v->expires_at?->toDateTimeString(),
                        'is_expired' => $v->expires_at ? $v->expires_at->isPast() : 'null',
                        'created_at' => $v->created_at?->toDateTimeString()
                    ];
                })->toArray()
            ]);

            // HĽADÁME KÓD V SMS_VERIFICATIONS TABUĽKE
            $verification = SmsVerification::where('verification_code', $code)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->first();

            if (!$verification) {
                Log::warning('Code not found or expired', [
                    'code' => $code,
                    'current_time' => now()->toDateTimeString(),
                    'searched_criteria' => [
                        'verification_code' => $code,
                        'status' => 'pending',
                        'expires_at_greater_than' => now()->toDateTimeString()
                    ]
                ]);
                return [
                    'success' => false,
                    'message' => 'Neplatný alebo expirovaný verifikačný kód'
                ];
            }

            Log::info('Code found', [
                'id' => $verification->id,
                'code' => $code
            ]);

            // Označíme ako overený
            $verification->update([
                'status' => 'verified',
                'is_verified' => true,
                'verified_at' => now()
            ]);

            Log::info('Code verified successfully', [
                'code' => $code,
                'verification_id' => $verification->id,
                'verification_updated' => true
            ]);

            // AKTIVOVANIE INZERÁTU - KRITICKÉ PRE PRODUKCIU
            if ($verification->ad_id) {
                Log::info('Activating ad via AdPayment system', [
                    'ad_id' => $verification->ad_id,
                    'package_type' => $verification->package_type,
                    'package_duration' => $verification->package_duration
                ]);
                
                try {
                    $payment = $this->activateAdViaPayment($verification);
                    Log::info('✅ AD ACTIVATED SUCCESSFULLY', [
                        'ad_id' => $verification->ad_id,
                        'payment_id' => $payment->payment_id,
                        'verification_id' => $verification->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('❌ CRITICAL: Failed to activate ad', [
                        'verification_id' => $verification->id,
                        'ad_id' => $verification->ad_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // BACKUP AKTIVÁCIA - priamo aktivujeme inzerát
                    try {
                        $ad = \App\Models\Ad::find($verification->ad_id);
                        if ($ad) {
                            $ad->update([
                                'status' => 'active',
                                'subscription_status' => 'active',
                                'subscription_expires_at' => now()->addDays($verification->package_duration),
                                'featured' => $verification->package_type === 'premium',
                                'top_ad' => false
                            ]);
                            
                            Log::info('✅ AD ACTIVATED VIA BACKUP METHOD', [
                                'ad_id' => $verification->ad_id,
                                'verification_id' => $verification->id
                            ]);
                        }
                    } catch (\Exception $backupError) {
                        Log::error('❌ BACKUP ACTIVATION FAILED', [
                            'ad_id' => $verification->ad_id,
                            'error' => $backupError->getMessage()
                        ]);
                    }
                }
            } else {
                Log::warning('No ad_id found in verification - ad will not be activated', [
                    'verification_id' => $verification->id
                ]);
            }

            return [
                'success' => true,
                'message' => 'Predplatné úspešne aktivované!',
                'package' => [
                    'type' => $verification->package_type,
                    'duration' => $verification->package_duration,
                    'price' => $verification->price
                ],
                'debug' => [
                    'verification_id' => $verification->id,
                    'ad_id' => $verification->ad_id,
                    'will_activate_ad' => (bool)$verification->ad_id
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Code verification error', [
                'error' => $e->getMessage(),
                'code' => $code
            ]);
            return [
                'success' => false,
                'message' => 'Chyba pri overovaní kódu'
            ];
        }
    }

    /**
     * Aktivuje inzerát cez AdPayment systém
     */
    private function activateAdViaPayment(SmsVerification $verification)
    {
        // Nájdeme matching payment package podľa typu a dĺžky
        $package = \App\Models\PaymentPackage::where('type', $verification->package_type)
            ->where('duration_days', $verification->package_duration)
            ->where('is_active', true)
            ->first();

        if (!$package) {
            Log::warning('No matching PaymentPackage found', [
                'package_type' => $verification->package_type,
                'package_duration' => $verification->package_duration
            ]);
            
            // Vytvoríme default package ak neexistuje
            $package = \App\Models\PaymentPackage::create([
                'name' => $verification->package_type . ' ' . $verification->package_duration . ' dni',
                'type' => $verification->package_type,
                'duration_days' => $verification->package_duration,
                'price' => $verification->price,
                'is_active' => true,
                'is_featured' => $verification->package_type === 'premium',
                'is_top_ad' => false,
                'description' => 'SMS balíček'
            ]);
        }

        // Nájdeme ad a user
        $ad = \App\Models\Ad::find($verification->ad_id);
        if (!$ad) {
            throw new \Exception("Ad not found: {$verification->ad_id}");
        }

        // Vytvoríme alebo nájdeme existujúci AdPayment
        $payment = \App\Models\AdPayment::firstOrCreate(
            [
                'ad_id' => $verification->ad_id,
                'payment_method' => 'sms',
                'status' => 'pending',
                'gateway_payment_id' => $verification->sms_id
            ],
            [
                'user_id' => $ad->user_id,
                'payment_package_id' => $package->id,
                'payment_id' => 'SMS-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'amount' => $verification->price,
                'currency' => 'EUR',
                'duration_days' => $verification->package_duration,
                'is_featured' => $package->is_featured,
                'is_top_ad' => $package->is_top_ad,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => [
                    'sms_verification_id' => $verification->id,
                    'sms_code' => $verification->verification_code,
                    'phone' => $verification->msisdn,
                    'created_via' => 'sms_verification_system'
                ]
            ]
        );

        // Aktivujeme platbu a inzerát
        if ($payment->status === 'pending') {
            \DB::transaction(function () use ($payment, $verification) {
                $payment->markAsCompleted();
                
                // Označíme verifikáciu ako použitú
                $verification->update([
                    'used_at' => now(),
                    'is_payment_confirmed' => true
                ]);
            });

            Log::info('Ad activated successfully via SMS verification', [
                'ad_id' => $verification->ad_id,
                'payment_id' => $payment->payment_id,
                'verification_id' => $verification->id
            ]);
        }

        return $payment;
    }

    /**
     * Nájde ad_id pre SMS platbu na základe telefónneho čísla a pending platieb
     */
    private function findAdIdForSmsPayment($phone, $package)
    {
        try {
            // 1. Pokúsime sa nájsť pending SMS platbu s týmto telefónom
            $recentPayment = \App\Models\AdPayment::where('payment_method', 'sms')
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subHours(2)) // Posledné 2 hodiny
                ->whereHas('paymentPackage', function($query) use ($package) {
                    $query->where('type', $package['type'])
                          ->where('duration_days', $package['duration']);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            if ($recentPayment) {
                Log::info('Found matching AdPayment for SMS', [
                    'ad_id' => $recentPayment->ad_id,
                    'payment_id' => $recentPayment->payment_id,
                    'phone' => $phone
                ]);
                return $recentPayment->ad_id;
            }

            // 2. Ako fallback - nájdeme najnovší inzerát typu, ktorý čaká na platbu
            $fallbackAd = \App\Models\Ad::where('status', 'draft')
                ->orWhere('subscription_status', 'inactive')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($fallbackAd) {
                Log::warning('Using fallback ad for SMS payment', [
                    'ad_id' => $fallbackAd->id,
                    'phone' => $phone,
                    'reason' => 'no_matching_payment_found'
                ]);
                return $fallbackAd->id;
            }

            Log::error('No ad found for SMS payment', [
                'phone' => $phone,
                'package' => $package
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Error finding ad_id for SMS payment', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
} 