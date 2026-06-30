<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdPhotoManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Vytvoríme fake storage disk pre testy
        Storage::fake('public');
    }

    /** @test */
    public function user_can_create_ad_with_photos()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Vytvoríme fake fotky
        $verificationPhoto = UploadedFile::fake()->image('verification.jpg', 800, 600);
        $galleryPhoto1 = UploadedFile::fake()->image('gallery1.jpg', 800, 600);
        $galleryPhoto2 = UploadedFile::fake()->image('gallery2.jpg', 800, 600);

        $adData = [
            'nickname' => 'Test User',
            'ad_type' => 'zena',
            'nationality' => 'Slovenka',
            'age' => 25,
            'city' => 'Bratislava',
            'offer_type' => ['ponukam-privat'],
            'girl_selection' => 'vip',
            'experience' => 'profesionalka',
            'phone' => '0901234567',
            'description' => 'Test popis inzerátu s dostatočnou dĺžkou aby prešiel validáciou minimálne 50 znakov.',
            'verification_photo' => $verificationPhoto,
            'gallery_photos' => [$galleryPhoto1, $galleryPhoto2],
        ];

        // Act
        $response = $this->post(route('ads.store'), $adData);

        // Assert
        $response->assertRedirect(route('ads.index'));
        $response->assertSessionHas('success');

        $ad = Ad::where('user_id', $user->id)->first();
        $this->assertNotNull($ad);
        
        // Overiť že sa fotky uložili do Storage
        $this->assertNotNull($ad->verification_photo);
        $this->assertTrue(Storage::disk('public')->exists($ad->verification_photo));
        
        $this->assertIsArray($ad->gallery_photos);
        $this->assertCount(2, $ad->gallery_photos);
        
        foreach ($ad->gallery_photos as $photo) {
            $this->assertTrue(Storage::disk('public')->exists($photo));
        }
    }

    /** @test */
    public function user_can_edit_ad_and_add_new_photos()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Vytvoríme inzerát s jednou fotkou v galérii
        $originalPhoto = UploadedFile::fake()->image('original.jpg', 800, 600);
        $originalVerification = UploadedFile::fake()->image('verification.jpg', 800, 600);
        
        $ad = Ad::factory()->create([
            'user_id' => $user->id,
            'verification_photo' => $originalVerification->storeAs('ads/verification', 'verification_test.jpg', 'public'),
            'gallery_photos' => [$originalPhoto->storeAs('ads/gallery', 'gallery_test.jpg', 'public')]
        ]);

        // Vytvoríme nové fotky na pridanie
        $newGalleryPhoto = UploadedFile::fake()->image('new_gallery.jpg', 800, 600);

        $updateData = [
            'nickname' => $ad->nickname,
            'ad_type' => $ad->ad_type,
            'nationality' => $ad->nationality,
            'age' => $ad->age,
            'city' => $ad->city,
            'offer_type' => $ad->offer_type,
            'girl_selection' => $ad->girl_selection,
            'experience' => $ad->experience,
            'phone' => $ad->phone,
            'description' => $ad->description,
            'gallery_photos' => [$newGalleryPhoto],
        ];

        // Act
        $response = $this->put(route('ads.update', $ad->id), $updateData);

        // Assert
        $response->assertRedirect(route('ads.index'));
        
        $ad->refresh();
        
        // Galéria by mala teraz obsahovať 2 fotky (pôvodnú + novú)
        $this->assertCount(2, $ad->gallery_photos);
        
        // Overiť že obidve fotky existujú v storage
        foreach ($ad->gallery_photos as $photo) {
            $this->assertTrue(Storage::disk('public')->exists($photo));
        }
    }

    /** @test */
    public function user_can_remove_photos_from_gallery()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Vytvoríme inzerát s 3 fotkami v galérii
        $photo1 = UploadedFile::fake()->image('photo1.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('photo2.jpg', 800, 600);
        $photo3 = UploadedFile::fake()->image('photo3.jpg', 800, 600);
        
        $galleryPaths = [
            $photo1->storeAs('ads/gallery', 'photo1_test.jpg', 'public'),
            $photo2->storeAs('ads/gallery', 'photo2_test.jpg', 'public'),
            $photo3->storeAs('ads/gallery', 'photo3_test.jpg', 'public')
        ];
        
        $ad = Ad::factory()->create([
            'user_id' => $user->id,
            'gallery_photos' => $galleryPaths
        ]);

        // Označíme prvú a tretiu fotku na vymazanie (indexy 0 a 2)
        $updateData = [
            'nickname' => $ad->nickname,
            'ad_type' => $ad->ad_type,
            'nationality' => $ad->nationality,
            'age' => $ad->age,
            'city' => $ad->city,
            'offer_type' => $ad->offer_type,
            'girl_selection' => $ad->girl_selection,
            'experience' => $ad->experience,
            'phone' => $ad->phone,
            'description' => $ad->description,
            'remove_gallery_photos' => [0, 2], // Vymazať prvú a tretiu fotku
        ];

        // Act
        $response = $this->put(route('ads.update', $ad->id), $updateData);

        // Assert
        $response->assertRedirect(route('ads.index'));
        
        $ad->refresh();
        
        // Galéria by mala teraz obsahovať len 1 fotku (prostredná)
        $this->assertCount(1, $ad->gallery_photos);
        $this->assertEquals($galleryPaths[1], $ad->gallery_photos[0]);
        
        // Overiť že vymazané fotky už neexistujú v storage
        $this->assertFalse(Storage::disk('public')->exists($galleryPaths[0]));
        $this->assertFalse(Storage::disk('public')->exists($galleryPaths[2]));
        
        // Overiť že zostávajúca fotka stále existuje
        $this->assertTrue(Storage::disk('public')->exists($galleryPaths[1]));
    }

    /** @test */
    public function user_can_replace_verification_photo()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Vytvoríme inzerát s verifikačnou fotkou
        $originalVerification = UploadedFile::fake()->image('original_verification.jpg', 800, 600);
        $originalPath = $originalVerification->storeAs('ads/verification', 'original_verification_test.jpg', 'public');
        
        $ad = Ad::factory()->create([
            'user_id' => $user->id,
            'verification_photo' => $originalPath
        ]);

        // Vytvoríme novú verifikačnú fotku
        $newVerification = UploadedFile::fake()->image('new_verification.jpg', 800, 600);

        $updateData = [
            'nickname' => $ad->nickname,
            'ad_type' => $ad->ad_type,
            'nationality' => $ad->nationality,
            'age' => $ad->age,
            'city' => $ad->city,
            'offer_type' => $ad->offer_type,
            'girl_selection' => $ad->girl_selection,
            'experience' => $ad->experience,
            'phone' => $ad->phone,
            'description' => $ad->description,
            'verification_photo' => $newVerification,
        ];

        // Act
        $response = $this->put(route('ads.update', $ad->id), $updateData);

        // Assert
        $response->assertRedirect(route('ads.index'));
        
        $ad->refresh();
        
        // Verifikačná fotka sa zmenila
        $this->assertNotEquals($originalPath, $ad->verification_photo);
        
        // Nová fotka existuje
        $this->assertTrue(Storage::disk('public')->exists($ad->verification_photo));
        
        // Stará fotka bola vymazaná
        $this->assertFalse(Storage::disk('public')->exists($originalPath));
    }

    /** @test */
    public function deleting_ad_removes_all_photos()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Vytvoríme inzerát s fotkami
        $verificationPhoto = UploadedFile::fake()->image('verification.jpg', 800, 600);
        $galleryPhoto1 = UploadedFile::fake()->image('gallery1.jpg', 800, 600);
        $galleryPhoto2 = UploadedFile::fake()->image('gallery2.jpg', 800, 600);
        
        $verificationPath = $verificationPhoto->storeAs('ads/verification', 'verification_delete_test.jpg', 'public');
        $galleryPaths = [
            $galleryPhoto1->storeAs('ads/gallery', 'gallery1_delete_test.jpg', 'public'),
            $galleryPhoto2->storeAs('ads/gallery', 'gallery2_delete_test.jpg', 'public')
        ];
        
        $ad = Ad::factory()->create([
            'user_id' => $user->id,
            'verification_photo' => $verificationPath,
            'gallery_photos' => $galleryPaths
        ]);

        // Act
        $response = $this->delete(route('ads.destroy', $ad->id));

        // Assert
        $response->assertRedirect(route('ads.index'));
        
        // Inzerát bol vymazaný
        $this->assertDatabaseMissing('ads', ['id' => $ad->id]);
        
        // Všetky fotky boli vymazané
        $this->assertFalse(Storage::disk('public')->exists($verificationPath));
        foreach ($galleryPaths as $path) {
            $this->assertFalse(Storage::disk('public')->exists($path));
        }
    }
} 