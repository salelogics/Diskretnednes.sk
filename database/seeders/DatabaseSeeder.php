<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\PaymentPackage;
use App\Services\InvoiceService;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Vytvorenie admin používateľa ak neexistuje
        $admin = User::firstOrCreate(
            ['email' => 'admin@diskretnednes.sk'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Spustenie PaymentPackageSeeder pre vytvorenie všetkých platobných balíčkov
        $this->call([
            PaymentPackageSeeder::class,
        ]);

        // Vytvorenie testovacích používateľov
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $users[] = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "Testovací používateľ {$i}",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
        }

        // Vytvorenie testovacích inzerátov
        $ads = [];
        foreach ($users as $user) {
            for ($j = 1; $j <= 2; $j++) {
                $ads[] = Ad::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'nickname' => "Inzerát {$j} od {$user->name}"
                    ],
                    [
                        'description' => "Popis inzerátu {$j} od používateľa {$user->name}",
                        'city' => 'bratislava',
                        'status' => 'active',
                        'ad_type' => 'zena',
                        'age' => rand(18, 35),
                        'phone' => '+421900123456',
                        'offer_type' => ['stretnutie-u-mna'], // Opravené - musí byť array
                        'nationality' => 'slovenska',
                        'girl_selection' => 'som-uplne-sama',
                        'experience' => 'stredne-skusena',
                        'contact_methods' => ['telefon', 'sms'], // Opravené - musí byť array
                        'hours' => [
                            'monday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                            'tuesday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                            'wednesday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                            'thursday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                            'friday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                            'saturday' => ['status' => 'available', 'from' => '10:00', 'to' => '22:00'],
                            'sunday' => ['status' => 'not_working', 'from' => '', 'to' => '']
                        ],
                        'practices' => ['klasicky-sex', 'oralne'], // Opravené - musí byť array
                        'height' => rand(160, 180),
                        'weight' => rand(50, 70),
                        'breast_size' => '75C',
                        'eye_color' => 'hnede',
                        'hair_color' => 'blond',
                        'orientation' => 'heterosexualna',
                        'subscription_status' => 'active',
                        'subscription_expires_at' => now()->addDays(30),
                        'phone_verified' => true,
                    ]
                );
            }
        }

        // Vytvorenie vzorových platieb
        $paymentMethods = ['bank_transfer', 'stripe', 'qr_code'];
        $statuses = ['completed', 'pending', 'failed'];
        $packages = PaymentPackage::all();

        foreach ($ads as $index => $ad) {
            // Vytvoríme 1-3 platby pre každý inzerát
            $paymentCount = rand(1, 3);
            
            for ($k = 0; $k < $paymentCount; $k++) {
                $package = $packages->random();
                $status = $statuses[array_rand($statuses)];
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                
                $createdAt = Carbon::now()->subDays(rand(1, 90));
                
                $payment = AdPayment::create([
                    'user_id' => $ad->user_id,
                    'ad_id' => $ad->id,
                    'payment_package_id' => $package->id,
                    'payment_id' => 'PAY_' . strtoupper(uniqid()),
                    'amount' => $package->price,
                    'currency' => 'EUR',
                    'payment_method' => $paymentMethod,
                    'status' => $status,
                    'duration_days' => $package->duration_days,
                    'subscription_starts_at' => $status === 'completed' ? $createdAt : null,
                    'subscription_ends_at' => $status === 'completed' ? $createdAt->copy()->addDays($package->duration_days) : null,
                    'is_featured' => $package->is_featured,
                    'is_top_ad' => $package->is_top_ad,
                    'gateway_payment_id' => 'GW_' . strtoupper(uniqid()),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 Test Browser',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Pre dokončené platby vytvoríme faktúru
                if ($status === 'completed') {
                    try {
                        $invoiceService = app(InvoiceService::class);
                        $invoiceService->createInvoiceFromPayment($payment);
                    } catch (\Exception $e) {
                        // Ignorujeme chyby pri vytváraní faktúr v seederi
                        echo "Chyba pri vytváraní faktúry pre platbu {$payment->id}: " . $e->getMessage() . "\n";
                    }
                }
            }
        }

        echo "Vzorové dáta boli úspešne vytvorené!\n";
        echo "Admin: admin@diskretnednes.sk / admin123\n";
        echo "Používatelia: user1@example.com až user5@example.com / password\n";
        echo "Vytvorené: " . User::count() . " používateľov, " . Ad::count() . " inzerátov, " . AdPayment::count() . " platieb\n";
        echo "Platobné balíčky: " . PaymentPackage::count() . "\n";
    }
} 