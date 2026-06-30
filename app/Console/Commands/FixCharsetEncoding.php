<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\EroticClub;
use App\Models\Ad;
use App\Models\User;

class FixCharsetEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:charset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Oprava charset problémov v databáze';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Začínam opravu charset problémov...');

        // Konkrétne opravy problémových textov
        $this->fixSpecificTexts();
        
        $this->info('Oprava charset problémov dokončená!');
        return 0;
    }

    private function fixSpecificTexts()
    {
        // Oprava konkrétnych problémových textov v erotických kluboch
        DB::table('erotic_clubs')
            ->where('name', 'like', '%?%')
            ->orWhere('description', 'like', '%?%')
            ->orderBy('id')
            ->chunk(100, function ($clubs) {
                foreach ($clubs as $club) {
                    $newName = $this->fixText($club->name);
                    $newDescription = $this->fixText($club->description);
                    
                    if ($newName !== $club->name || $newDescription !== $club->description) {
                        DB::table('erotic_clubs')
                            ->where('id', $club->id)
                            ->update([
                                'name' => $newName,
                                'description' => $newDescription
                            ]);
                        $this->info("Opravený klub: {$club->name} -> {$newName}");
                    }
                }
            });

        // Oprava inzerátov (len nickname a city)
        DB::table('ads')
            ->where('nickname', 'like', '%?%')
            ->orWhere('city', 'like', '%?%')
            ->orderBy('id')
            ->chunk(100, function ($ads) {
                foreach ($ads as $ad) {
                    $newNickname = $this->fixText($ad->nickname ?? '');
                    $newCity = $this->fixText($ad->city ?? '');
                    
                    if ($newNickname !== ($ad->nickname ?? '') || $newCity !== ($ad->city ?? '')) {
                        DB::table('ads')
                            ->where('id', $ad->id)
                            ->update([
                                'nickname' => $newNickname,
                                'city' => $newCity
                            ]);
                        $this->info("Opravený inzerát: {$ad->id}");
                    }
                }
            });
    }

    private function fixText($text)
    {
        if (empty($text)) return $text;
        
        // Konkrétne náhrady pre najčastejšie problémy
        $replacements = [
            'Erotick?' => 'Erotické',
            'erotick?' => 'erotické',
            'Hr??n?' => 'Hrádza',
            'hr??n?' => 'hrádza',
            'Ko?ice' => 'Košice',
            'ko?ice' => 'košice',
            'Pre?ov' => 'Prešov',
            'pre?ov' => 'prešov',
            '?ilina' => 'Žilina',
            '?ilina' => 'žilina',
            'Bansk? Bystrica' => 'Banská Bystrica',
            'bansk? bystrica' => 'banská bystrica',
            'Tren?ín' => 'Trenčín',
            'tren?ín' => 'trenčín',
            'Bratislavsk?' => 'Bratislavské',
            'bratislavsk?' => 'bratislavské',
            'Nov? M?sto' => 'Nové Mesto',
            'nov? m?sto' => 'nové mesto',
            'Star? M?sto' => 'Staré Mesto',
            'star? m?sto' => 'staré mesto',
            '?lub' => 'klub',
            'masá?' => 'masáž',
            'Masá?' => 'Masáž',
            'privát' => 'privát',
            'Privát' => 'Privát',
            'escort' => 'escort',
            'Escort' => 'Escort'
        ];

        foreach ($replacements as $wrong => $correct) {
            $text = str_replace($wrong, $correct, $text);
        }

        return $text;
    }
}
