<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Club;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Club::create([
            'name' => 'Erotický klub Paradise',
            'description' => 'Luxusný erotický klub v centre Bratislavy s najkrajšími dievčatami.',
            'address' => 'Hlavná 123, Bratislava',
            'city' => 'Bratislava',
            'phone' => '+421 900 123 456',
            'email' => 'info@paradise.sk',
            'website' => 'https://paradise.sk',
            'hours_weekdays' => '20:00 - 04:00',
            'hours_weekend' => '20:00 - 06:00',
            'hours_sunday' => 'Zatvorené',
            'is_active' => true,
            'slug' => 'paradise-bratislava'
        ]);

        Club::create([
            'name' => 'Club Desire',
            'description' => 'Moderný erotický klub s diskrétnym prostredím a profesionálnymi službami.',
            'address' => 'Košická 45, Košice',
            'city' => 'Košice',
            'phone' => '+421 905 987 654',
            'email' => 'contact@desire.sk',
            'website' => 'https://desire.sk',
            'hours_weekdays' => '19:00 - 03:00',
            'hours_weekend' => '19:00 - 05:00',
            'hours_sunday' => '19:00 - 02:00',
            'is_active' => true,
            'slug' => 'desire-kosice'
        ]);

        Club::create([
            'name' => 'Velvet Lounge',
            'description' => 'Exkluzívny klub pre náročných klientov s VIP službami.',
            'address' => 'Trenčianska 78, Trenčín',
            'city' => 'Trenčín',
            'phone' => '+421 910 555 777',
            'email' => 'vip@velvet.sk',
            'website' => 'https://velvet.sk',
            'hours_weekdays' => '21:00 - 04:00',
            'hours_weekend' => '21:00 - 06:00',
            'hours_sunday' => 'Zatvorené',
            'is_active' => false,
            'slug' => 'velvet-trencin'
        ]);
    }
}
