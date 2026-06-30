<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\User;
use App\Models\AdPayment;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Získaj prvého používateľa
        $user = User::first();
        if (!$user) {
            $this->command->error('Žiadni používatelia v databáze. Spustite najprv UserSeeder.');
            return;
        }

        // Skúsim nájsť existujúci AdPayment
        $payment = AdPayment::first();

        $this->command->info('Vytváram testovací faktúry...');

        // 1. Faktúra v stave "draft"
        $invoice1 = Invoice::create([
            'user_id' => $user->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'delivery_date' => now(),
            'status' => 'draft',
            'customer_data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '+421 900 123 456',
                'address' => 'Testovacia 123',
                'city' => 'Bratislava',
                'postal_code' => '811 01',
                'country' => 'Slovenská republika',
                'ico' => null,
                'dic' => null,
                'ic_dph' => null,
                'is_business' => false
            ],
            'supplier_data' => Invoice::getDefaultSupplierData(),
            'payment_info' => Invoice::getDefaultPaymentInfo(),
            'items' => [
                [
                    'name' => 'Predplatné inzerátu - 30 dní',
                    'description' => 'Zobrazovanie inzerátu na Erotikon.sk po dobu 30 dní',
                    'quantity' => 1,
                    'unit' => 'ks',
                    'unit_price' => 24.99,
                    'tax_rate' => 20,
                    'tax_amount' => 4.998,
                    'total' => 29.988
                ]
            ],
            'subtotal' => 24.99,
            'tax_amount' => 4.998,
            'total_amount' => 29.988,
            'currency' => 'EUR',
            'notes' => 'Testovacia faktúra v stave koncept'
        ]);

        // 2. Faktúra v stave "sent"
        $invoice2 = Invoice::create([
            'user_id' => $user->id,
            'issue_date' => now()->subDays(5),
            'due_date' => now()->addDays(9),
            'delivery_date' => now()->subDays(5),
            'status' => 'sent',
            'sent_at' => now()->subDays(5),
            'customer_data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '+421 900 123 456',
                'address' => 'Testovacia 123',
                'city' => 'Bratislava',
                'postal_code' => '811 01',
                'country' => 'Slovenská republika',
                'ico' => '12345678',
                'dic' => '1234567890',
                'ic_dph' => 'SK1234567890',
                'is_business' => true
            ],
            'supplier_data' => Invoice::getDefaultSupplierData(),
            'payment_info' => Invoice::getDefaultPaymentInfo(),
            'items' => [
                [
                    'name' => 'Predplatné inzerátu - 30 dní',
                    'description' => 'Zobrazovanie inzerátu na Erotikon.sk',
                    'quantity' => 1,
                    'unit' => 'ks',
                    'unit_price' => 24.99,
                    'tax_rate' => 20,
                    'tax_amount' => 4.998,
                    'total' => 29.988
                ],
                [
                    'name' => 'Zvýraznenie inzerátu',
                    'description' => 'Zvýraznenie inzerátu farebným pozadím',
                    'quantity' => 1,
                    'unit' => 'ks',
                    'unit_price' => 5.00,
                    'tax_rate' => 20,
                    'tax_amount' => 1.00,
                    'total' => 6.00
                ]
            ],
            'subtotal' => 29.99,
            'tax_amount' => 5.998,
            'total_amount' => 35.988,
            'currency' => 'EUR',
            'notes' => 'Faktúra odoslaná zákazníkovi'
        ]);

        // 3. Faktúra v stave "paid"
        $invoice3 = Invoice::create([
            'user_id' => $user->id,
            'ad_payment_id' => $payment ? $payment->id : null,
            'issue_date' => now()->subDays(20),
            'due_date' => now()->subDays(6),
            'delivery_date' => now()->subDays(20),
            'status' => 'paid',
            'sent_at' => now()->subDays(20),
            'paid_at' => now()->subDays(10),
            'customer_data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '+421 900 123 456',
                'address' => 'Testovacia 123',
                'city' => 'Bratislava',
                'postal_code' => '811 01',
                'country' => 'Slovenská republika',
                'ico' => null,
                'dic' => null,
                'ic_dph' => null,
                'is_business' => false
            ],
            'supplier_data' => Invoice::getDefaultSupplierData(),
            'payment_info' => Invoice::getDefaultPaymentInfo(),
            'items' => [
                [
                    'name' => 'Predplatné inzerátu #1 - 30 dní',
                    'description' => 'Zobrazovanie inzerátu na Erotikon.sk po dobu 30 dní',
                    'quantity' => 1,
                    'unit' => 'ks',
                    'unit_price' => 19.99,
                    'tax_rate' => 20,
                    'tax_amount' => 3.998,
                    'total' => 23.988
                ],
                [
                    'name' => 'Zvýraznenie inzerátu #1',
                    'description' => 'Zvýraznenie inzerátu farebným pozadím',
                    'quantity' => 1,
                    'unit' => 'ks',
                    'unit_price' => 5.00,
                    'tax_rate' => 20,
                    'tax_amount' => 1.00,
                    'total' => 6.00
                ]
            ],
            'subtotal' => 24.99,
            'tax_amount' => 4.998,
            'total_amount' => 29.988,
            'currency' => 'EUR',
            'notes' => 'Faktúra za predplatné inzerátu na portáli Erotikon.sk'
        ]);

        // 4. Faktúra v stave "overdue"
        $invoice4 = Invoice::create([
            'user_id' => $user->id,
            'issue_date' => now()->subDays(30),
            'due_date' => now()->subDays(16),
            'delivery_date' => now()->subDays(30),
            'status' => 'overdue',
            'sent_at' => now()->subDays(30),
            'customer_data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '+421 900 123 456',
                'address' => 'Testovacia 123',
                'city' => 'Bratislava',
                'postal_code' => '811 01',
                'country' => 'Slovenská republika',
                'ico' => '87654321',
                'dic' => '0987654321',
                'ic_dph' => 'SK0987654321',
                'is_business' => true
            ],
            'supplier_data' => Invoice::getDefaultSupplierData(),
            'payment_info' => Invoice::getDefaultPaymentInfo(),
            'items' => [
                [
                    'name' => 'Predplatné inzerátu - 60 dní',
                    'description' => 'Zobrazovanie inzerátu na Erotikon.sk po dobu 60 dní',
                    'quantity' => 1,
                    'unit' => 'ks',
                    'unit_price' => 45.83,
                    'tax_rate' => 20,
                    'tax_amount' => 9.166,
                    'total' => 54.996
                ],
                [
                    'name' => 'TOP pozícia inzerátu',
                    'description' => 'Zobrazovanie inzerátu na TOP pozíciách',
                    'quantity' => 1,
                    'unit' => 'ks',
                    'unit_price' => 10.00,
                    'tax_rate' => 20,
                    'tax_amount' => 2.00,
                    'total' => 12.00
                ]
            ],
            'subtotal' => 55.83,
            'tax_amount' => 11.166,
            'total_amount' => 66.996,
            'currency' => 'EUR',
            'notes' => 'Faktúra po splatnosti - urgentne riešiť'
        ]);

        $this->command->info('Vytvorené 4 testovací faktúry.');
    }
}
