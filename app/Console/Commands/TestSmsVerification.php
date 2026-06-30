<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SmsPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestSmsVerification extends Command
{
    protected $signature = 'sms:test-123456';
    protected $description = 'EXPRESS test SMS verifikácie s kódom 123456';

    public function handle()
    {
        $this->info('🧪 EXPRESS TEST SMS verifikácie 123456');
        $this->line('==========================================');
        
        // Simulujeme prihláseného používateľa
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Test User',
                'email' => 'test@test.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]);
            $this->info('✅ Vytvorený test user: ' . $user->email);
        }
        
        Auth::login($user);
        $this->info('✅ Prihlásený user: ' . $user->name . ' (ID: ' . $user->id . ')');
        
        // Vytvoríme test inzerát ak neexistuje
        $testAd = \App\Models\Ad::where('user_id', $user->id)->first();
        if (!$testAd) {
            $testAd = \App\Models\Ad::create([
                'user_id' => $user->id,
                'nickname' => 'Test SMS Inzerát',
                'ad_type' => 'zena',
                'nationality' => 'SK',
                'age' => 25,
                'phone' => '0903123456',
                'city' => 'bratislava',
                'offer_type' => ['ponukam-escort'],
                'girl_selection' => 'som-uplne-sama',
                'experience' => 'skusena',
                'description' => 'Test inzerát pre SMS',
                'status' => 'draft',
                'subscription_status' => 'inactive'
            ]);
            $this->info('✅ Vytvorený test inzerát ID: ' . $testAd->id);
        } else {
            $this->info('✅ Existujúci test inzerát ID: ' . $testAd->id);
        }
        
        $this->line('Status pred testom: ' . $testAd->status);
        $this->line('Subscription pred testom: ' . $testAd->subscription_status);
        $this->newLine();
        
        // Test SMS controller
        $controller = app(SmsPaymentController::class);
        $request = new Request();
        $request->merge(['verification_code' => '123456']);
        
        try {
            $this->info('🚀 Testujem kód 123456...');
            
            $response = $controller->verifyCode($request);
            $data = $response->getData(true);
            
            $this->line('Status Code: ' . $response->getStatusCode());
            $this->line('Response: ' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Refresh inzerát
            $testAd->refresh();
            
            $this->newLine();
            $this->info('📊 Stav po teste:');
            $this->line('Status: ' . $testAd->status);
            $this->line('Subscription: ' . $testAd->subscription_status);
            $this->line('Expires: ' . ($testAd->subscription_expires_at ?? 'Nenastavené'));
            
            if ($data['success'] ?? false) {
                $this->info('✅ SMS verifikácia ÚSPEŠNÁ!');
            } else {
                $this->error('❌ SMS verifikácia ZLYHALA!');
                $this->line('Chyba: ' . ($data['message'] ?? 'Neznáma chyba'));
            }
            
        } catch (\Exception $e) {
            $this->error('💥 EXCEPTION: ' . $e->getMessage());
            $this->line('File: ' . $e->getFile() . ':' . $e->getLine());
            $this->line('Trace:');
            $this->line($e->getTraceAsString());
        }
        
        return 0;
    }
} 