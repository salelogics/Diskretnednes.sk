<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerReport;
use Carbon\Carbon;

class CustomerReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vytvoríme testovací dáta pre rôzne úrovne nebezpečenstva
        
        // Veľmi nebezpečné čísla (20+ nahlásení)
        $this->createReports('0944836016', 41, 'Agresívny zákazník, vyhrážky, neplatí');
        $this->createReports('0952018861', 38, 'Nevhodné správanie, sexuálne obťažovanie');
        
        // Nebezpečné čísla (10-19 nahlásení)
        $this->createReports('0903456789', 23, 'Neplatí za služby, hrubé správanie');
        $this->createReports('0907123456', 19, 'Agresívne správanie, vyhrážky');
        $this->createReports('0948765432', 15, 'Nevhodné požiadavky, neplatí');
        
        // Podozrivé čísla (menej ako 10 nahlásení)
        $this->createReports('0904321098', 9, 'Podozrivé správanie');
        $this->createReports('0951185942', 3, 'Neplatí za služby');
        $this->createReports('0905987654', 7, 'Hrubé správanie');
    }
    
    private function createReports($phoneNumber, $count, $reason)
    {
        for ($i = 0; $i < $count; $i++) {
            CustomerReport::create([
                'phone_number' => $phoneNumber,
                'reason' => $reason . ' - nahlásenie #' . ($i + 1),
                'anonymous' => true,
                'user_id' => null,
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30))
            ]);
        }
    }
}
