<?php

namespace Database\Factories;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ad>
 */
class AdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nickname' => fake()->firstName(),
            'ad_type' => fake()->randomElement(['zena', 'muz', 'trans', 'par', 'klub']),
            'nationality' => fake()->randomElement(['Slovenka', 'Češka', 'Maďarka', 'Ukrajinka', 'Rumunka']),
            'age' => fake()->numberBetween(18, 50),
            'city' => fake()->randomElement(['Bratislava', 'Košice', 'Prešov', 'Žilina', 'Banská Bystrica']),
            'street' => fake()->optional()->streetName(),
            'offer_type' => [fake()->randomElement(['ponukam-privat', 'ponukam-escort', 'ponukam-masaz'])],
            'girl_selection' => fake()->randomElement(['standard', 'vip', 'premium']),
            'experience' => fake()->randomElement(['začiatočníčka', 'skúsená', 'profesionálka']),
            'phone' => '09' . fake()->randomNumber(8, true),
            'contact_methods' => fake()->randomElements(['telefon', 'sms', 'whatsapp', 'viber'], rand(1, 3)),
            'hours' => [
                'monday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                'tuesday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                'wednesday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                'thursday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                'friday' => ['status' => 'available', 'from' => '09:00', 'to' => '22:00'],
                'saturday' => ['status' => 'available', 'from' => '10:00', 'to' => '20:00'],
                'sunday' => ['status' => 'not_working']
            ],
            'practices' => fake()->randomElements([
                'klasicky-sex', 'oralne', 'analne', 'grupovy', 'bdsm', 'toys', 'role-play'
            ], rand(2, 4)),
            'description' => fake()->paragraphs(3, true) . ' Minimálne 50 znakov pre validáciu.',
            'verification_photo' => null, // Nastavené v testoch
            'gallery_photos' => [], // Nastavené v testoch
            'video' => null,
            'height' => fake()->optional()->numberBetween(150, 185),
            'weight' => fake()->optional()->numberBetween(45, 75),
            'breast_size' => fake()->optional()->randomElement(['A', 'B', 'C', 'D', 'DD', 'E']),
            'eye_color' => fake()->optional()->randomElement(['hnedé', 'modré', 'zelené', 'šedé']),
            'hair_color' => fake()->optional()->randomElement(['blond', 'hnedé', 'čierne', 'červené']),
            'tattoos' => fake()->optional()->randomElement(['žiadne', 'malé', 'veľké']),
            'piercing' => fake()->optional()->randomElement(['žiadny', 'uši', 'nos', 'iné']),
            'orientation' => fake()->optional()->randomElement(['hetero', 'bi', 'gay']),
            'status' => fake()->randomElement(['draft', 'active', 'inactive']),
            'subscription_status' => fake()->randomElement(['active', 'expired', 'inactive']),
            'subscription_expires_at' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'verification_code' => null,
            'verification_expires_at' => null,
            'featured' => fake()->boolean(10), // 10% šanca na featured
            'top_ad' => fake()->boolean(5), // 5% šanca na top ad
            'phone_verified' => fake()->boolean(80), // 80% šanca na verified phone
            'views' => fake()->numberBetween(0, 1000),
            'clicks' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the ad is active with valid subscription.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'subscription_status' => 'active',
            'subscription_expires_at' => fake()->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the ad is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'subscription_status' => 'inactive',
            'subscription_expires_at' => null,
        ]);
    }

    /**
     * Indicate that the ad has expired subscription.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
            'subscription_status' => 'expired',
            'subscription_expires_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the ad is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    /**
     * Indicate that the ad is a top ad.
     */
    public function topAd(): static
    {
        return $this->state(fn (array $attributes) => [
            'top_ad' => true,
        ]);
    }
}
