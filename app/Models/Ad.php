<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Ad extends Model
{
    protected $fillable = [
        'user_id',
        'nickname',
        'ad_type',
        'nationality',
        'age',
        'city',
        'street',
        'offer_type',
        'girl_selection',
        'experience',
        'phone',
        'contact_methods',
        'hours',
        'practices',
        'description',
        'verification_photo',
        'gallery_photos',
        'video',
        'height',
        'weight',
        'breast_size',
        'eye_color',
        'hair_color',
        'tattoos',
        'piercing',
        'orientation',
        'status',
        'subscription_status',
        'subscription_expires_at',
        'verification_code',
        'verification_expires_at',
        'featured',
        'top_ad',
        'phone_verified',
        'views',
        'clicks',
        // WordPress fields
        'wp_id',
        'wp_modified_at',
        'wp_slug',
        'wp_status',
        'wp_email',
        'wp_services_for',
        'wp_weekly_hours',
        'wp_extra_services',
        'wp_subscription_type',
        'wp_verification_photo_id',
        'wp_gallery_photo_ids',
        'wp_video_ids'
    ];

    protected $casts = [
        'contact_methods' => 'array',
        'hours' => 'array',
        'practices' => 'array',
        'offer_type' => 'array',
        'gallery_photos' => 'array',
        'subscription_expires_at' => 'datetime',
        'verification_expires_at' => 'datetime',
        'featured' => 'boolean',
        'top_ad' => 'boolean',
        'phone_verified' => 'boolean',
        'views' => 'integer',
        'clicks' => 'integer',
        // WordPress fields
        'wp_modified_at' => 'datetime',
        'wp_services_for' => 'array',
        'wp_weekly_hours' => 'array',
        'wp_extra_services' => 'array',
        'wp_gallery_photo_ids' => 'array',
        'wp_video_ids' => 'array'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function payments()
    {
        return $this->hasMany(AdPayment::class);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Koncept',
            'active' => 'Aktívny',
            'inactive' => 'Neaktívny',
            'suspended' => 'Pozastavený',
            default => 'Neznámy'
        };
    }

    public function getSubscriptionStatusLabelAttribute(): string
    {
        return match($this->subscription_status) {
            'inactive' => 'Neaktívne',
            'active' => 'Aktívne',
            'expired' => 'Vypršané',
            default => 'Neznámy'
        };
    }

    public function getAdTypeLabelAttribute(): string
    {
        return match($this->ad_type) {
            'zena' => 'Žena',
            'muz' => 'Muž',
            'par' => 'Pár',
            'trans' => 'Trans',
            'klub' => 'Masážny salón',
            'individual' => 'Individuálny',
            default => 'Neznámy'
        };
    }

    public function getOfferTypeLabelAttribute(): string
    {
        if (!is_array($this->offer_type) || empty($this->offer_type)) {
            return 'Neznámy';
        }
        
        $labels = [];
        foreach ($this->offer_type as $type) {
            $labels[] = match($type) {
                'stretnutie-u-mna' => 'Stretnutie u mňa',
                'stretnutie-u-teba' => 'Stretnutie u teba',
                'masaz' => 'Masáž',
                // Legacy hodnoty spred premenovania na "Typ stretnutia" (35c6151) -
                // staršie inzeráty ich stále majú uložené, bez tohto ukazovali "Neznámy".
                'ponukam-privat' => 'Ponúkam privát',
                'ponukam-escort' => 'Ponúkam escort',
                'ponukam-masaz' => 'Ponúkam masáž',
                'hladam-privat' => 'Hľadám privát',
                'hladam-escort' => 'Hľadám escort',
                'hladam-masaz' => 'Hľadám masáž',
                default => 'Neznámy'
            };
        }
        
        return implode(', ', $labels);
    }

    public function getCityLabelAttribute(): string
    {
        return match($this->city) {
            'bratislava' => 'Bratislava',
            'kosice' => 'Košice',
            'presov' => 'Prešov',
            'zilina' => 'Žilina',
            'banska-bystrica' => 'Banská Bystrica',
            'nitra' => 'Nitra',
            'trnava' => 'Trnava',
            'martin' => 'Martin',
            'trencin' => 'Trenčín',
            'poprad' => 'Poprad',
            'ine' => 'Iné',
            default => ucfirst($this->city)
        };
    }

    // Helper methods
    public function isSubscriptionActive(): bool
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_expires_at && 
               $this->subscription_expires_at->isFuture();
    }

    public function isSubscriptionExpired(): bool
    {
        return $this->subscription_status === 'expired' || 
               ($this->subscription_expires_at && $this->subscription_expires_at->isPast());
    }

    public function getDaysUntilExpiration(): ?int
    {
        if (!$this->subscription_expires_at) {
            return null;
        }
        
        return $this->subscription_expires_at->diffInDays(now(), false);
    }

    // Query Scopes pre lepšie filtrovanie inzerátov
    public function scopeActiveSubscription($query)
    {
        return $query->where('subscription_status', 'active')
                    ->where(function($q) {
                        $q->whereNull('subscription_expires_at')
                          ->orWhere('subscription_expires_at', '>', now());
                    });
    }

    public function scopeExpiredSubscription($query)
    {
        return $query->where(function($q) {
            $q->where('subscription_status', 'expired')
              ->orWhere(function($subQ) {
                  $subQ->where('subscription_status', 'active')
                       ->where('subscription_expires_at', '<', now());
              });
        });
    }

    public function scopePubliclyVisible($query)
    {
        return $query->where('status', 'active')
                    ->activeSubscription();
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    public function getCtrAttribute(): float
    {
        if ($this->views === 0) {
            return 0;
        }
        
        return round(($this->clicks / $this->views) * 100, 2);
    }

    // Dostupnosť helper metódy
    public function getCurrentAvailabilityAttribute(): ?string
    {
        try {
            if (!$this->hours || !is_array($this->hours)) {
                return null;
            }

            $currentDay = strtolower(now()->format('l')); // monday, tuesday, etc.
            $dayMapping = [
                'monday' => 'monday',
                'tuesday' => 'tuesday', 
                'wednesday' => 'wednesday',
                'thursday' => 'thursday',
                'friday' => 'friday',
                'saturday' => 'saturday',
                'sunday' => 'sunday'
            ];

            $mappedDay = $dayMapping[$currentDay] ?? null;
            if (!$mappedDay || !isset($this->hours[$mappedDay])) {
                return null;
            }

            $dayHours = $this->hours[$mappedDay];
            
            // Ak je to string namiesto array, skúsime to parsovať
            if (is_string($dayHours)) {
                if (str_contains($dayHours, 'dostupná') || str_contains($dayHours, 'available')) {
                    return 'Zavolaj (dohoda)';
                } elseif (str_contains($dayHours, 'obsadená') || str_contains($dayHours, 'busy')) {
                    return 'Mám čas celý deň';
                } elseif (str_contains($dayHours, 'nepracuje') || str_contains($dayHours, 'not_working')) {
                    return 'Nemám v tento deň čas';
                }
                return null;
            }

            if (!is_array($dayHours) || !isset($dayHours['status'])) {
                return null;
            }

            return match($dayHours['status']) {
                'available' => 'Zavolaj (dohoda)',
                'busy' => 'Mám čas celý deň',
                'not_working' => 'Nemám v tento deň čas',
                default => null
            };
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getAvailabilityColorAttribute(): string
    {
        try {
            $availability = $this->current_availability;
            
            return match($availability) {
                'Zavolaj (dohoda)' => 'bg-blue-500',
                'Mám čas celý deň' => 'bg-green-500',
                'Nemám v tento deň čas' => 'bg-red-500',
                default => 'bg-gray-400'
            };
        } catch (\Exception $e) {
            return 'bg-gray-400';
        }
    }

    public function isNewAttribute(): bool
    {
        return $this->created_at->isToday();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->where('subscription_status', 'active')
                    ->where(function($q) {
                        $q->whereNull('subscription_expires_at')
                          ->orWhere('subscription_expires_at', '>', now());
                    });
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByAdType($query, $type)
    {
        return $query->where('ad_type', $type);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeTopAd($query)
    {
        return $query->where('top_ad', true);
    }

    // Verification helper methods
    public function generateVerificationCode(): string
    {
        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'verification_code' => $code,
            'verification_expires_at' => now()->addMinutes(15)
        ]);
        
        return $code;
    }

    public function isVerificationCodeValid(string $code): bool
    {
        return $this->verification_code === $code && 
               $this->verification_expires_at && 
               $this->verification_expires_at->isFuture();
    }

    public function clearVerificationCode(): void
    {
        $this->update([
            'verification_code' => null,
            'verification_expires_at' => null
        ]);
    }

    public function isVerificationCodeExpired(): bool
    {
        return $this->verification_expires_at && 
               $this->verification_expires_at->isPast();
    }

    // Helper methods pre obrázky
    public function getVerificationImageUrlAttribute(): ?string
    {
        try {
            if (!$this->verification_photo) {
                return null;
            }
            
            $photo = $this->verification_photo;
            
            // Ak je to externá URL (WordPress), vráť ju priamo
            if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
                return $photo;
            }
            
            // Určenie fyzickej cesty súboru
            $physicalPath = null;
            $assetUrl = null;
            
            // Preferuj storage prefix (nový aj starý)
            if (str_starts_with($photo, 'storage/ads/verification/') || str_starts_with($photo, 'storage/images/uploads/')) {
                $physicalPath = public_path($photo);
                $assetUrl = asset($photo);
            }
            // Backward compatibility: images/uploads -> storage/images/uploads
            elseif (str_starts_with($photo, 'images/uploads/')) {
                $photo = 'storage/' . $photo;
                $physicalPath = public_path($photo);
                $assetUrl = asset($photo);
            }
            // Ak je to len názov súboru, pridaj cestu do images/uploads
            elseif (str_starts_with($photo, 'ads/verification/')) {
                // nový tvar: storage/ads/verification/...; starý fallback ostáva
                $physicalPath = public_path('storage/ads/verification/' . basename($photo));
                $assetUrl = asset('storage/ads/verification/' . basename($photo));
            }
            // Pre backward compatibility - ak cesta nie je prefixovaná
            else {
                $photo = str_replace(['images/uploads/ads/verification/', 'storage/images/uploads/ads/verification/', 'storage/ads/verification/', 'storage/', 'ads/verification/'], '', $photo);
                // preferuj nový tvar
                $physicalPath = public_path('storage/ads/verification/' . $photo);
                $assetUrl = asset('storage/ads/verification/' . $photo);
            }
            
            // KONTROLA EXISTENCIE SÚBORU + LEGACY FALLBACK
            if ($physicalPath && file_exists($physicalPath)) {
                return $assetUrl;
            }

            // Fallback na legacy umiestnenia pod public/images/... vrátane zmeny prípon
            $originalBasename = basename((string) $this->verification_photo);
            $namesToTry = [];
            $namesToTry[] = $originalBasename;
            $nameNoExt = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $originalBasename);
            if ($nameNoExt) {
                foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                    $namesToTry[] = $nameNoExt . '.' . $ext;
                }
            }
            $namesToTry = array_values(array_unique($namesToTry));

            $directoriesToTry = [
                'storage/ads/verification/',
                'storage/images/uploads/ads/verification/',
                'images/uploads/ads/verification/',
                'images/uploads/ads/',
                'images/ads/verification/',
                'images/uploads/',
            ];
            foreach ($directoriesToTry as $dir) {
                foreach ($namesToTry as $n) {
                    $legacyPath = public_path($dir . $n);
                    if (file_exists($legacyPath)) {
                        return asset($dir . $n);
                    }
                }
            }

            \Log::warning('Verification photo file missing (no fallback found)', [
                'ad_id' => $this->id,
                'verification_photo' => $this->verification_photo,
                'expected_path' => $physicalPath,
                'asset_url' => $assetUrl
            ]);

            return null;
            
        } catch (\Exception $e) {
            \Log::error('Error getting verification image URL', [
                'ad_id' => $this->id,
                'verification_photo' => $this->verification_photo,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    public function getGalleryImageUrlsAttribute(): array
    {
        try {
            if (!$this->gallery_photos || !is_array($this->gallery_photos)) {
                return [];
            }
            
            $validPhotos = [];
            $missingPhotos = [];
            $cleanedGallery = [];
            
            foreach ($this->gallery_photos as $photo) {
                if (!is_string($photo) || empty($photo)) {
                    continue;
                }
                
                // Ak je to externá URL (WordPress), vráť ju priamo
                if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
                    $validPhotos[] = $photo;
                    $cleanedGallery[] = $photo;
                    continue;
                }
                
                // Určenie fyzickej cesty súboru
                $physicalPath = null;
                $assetUrl = null;
                
            // Preferuj storage prefix (nový aj starý)
            if (str_starts_with($photo, 'storage/ads/gallery/') || str_starts_with($photo, 'storage/images/uploads/')) {
                    $physicalPath = public_path($photo);
                    $assetUrl = asset($photo);
                }
                // Backward compatibility: images/uploads -> storage/images/uploads
                elseif (str_starts_with($photo, 'images/uploads/')) {
                    $photo_path = 'storage/' . $photo;
                    $physicalPath = public_path($photo_path);
                    $assetUrl = asset($photo_path);
                }
                // Ak je to len názov súboru, pridaj cestu do images/uploads
            elseif (str_starts_with($photo, 'ads/gallery/')) {
                $physicalPath = public_path('storage/ads/gallery/' . basename($photo));
                $assetUrl = asset('storage/ads/gallery/' . basename($photo));
                }
                // Pre backward compatibility - očistíme cestu a použijeme asset()
                else {
                    $cleaned_photo = str_replace(['storage/ads/gallery/', 'storage/images/uploads/ads/gallery/', 'storage/', 'ads/gallery/'], '', $photo);
                    
                    // Ak je to len číselný názov (napr. 50759.jpg), pridaj prefix gallery_
                    if (preg_match('/^\d+\.jpg$/', $cleaned_photo)) {
                        $cleaned_photo = 'gallery_' . $cleaned_photo;
                    }
                    
                    // Odstráň duplicitný prefix ak už existuje
                    $cleaned_photo = preg_replace('/^gallery_gallery_/', 'gallery_', $cleaned_photo);
                    
                    // preferuj nový tvar
                    $physicalPath = public_path('storage/ads/gallery/' . $cleaned_photo);
                    $assetUrl = asset('storage/ads/gallery/' . $cleaned_photo);
                }
                
                // KONTROLA EXISTENCIE SÚBORU + LEGACY FALLBACK
                if ($physicalPath && file_exists($physicalPath)) {
                    $validPhotos[] = $assetUrl;
                    $cleanedGallery[] = $photo; // Zachovaj originálnu cestu v databáze
                } else {
                    // Skús legacy cesty v public/images/... s rôznymi príponami
                    $basename = basename($photo);
                    $namesToTry = [];
                    $namesToTry[] = $basename;
                    // Bez/so prefixom "gallery_"
                    if (str_starts_with($basename, 'gallery_')) {
                        $namesToTry[] = substr($basename, strlen('gallery_'));
                    } else {
                        $namesToTry[] = 'gallery_' . $basename;
                    }
                    // Varianty prípon
                    $baseNoExtCandidates = [];
                    foreach ($namesToTry as $nv) {
                        $baseNoExtCandidates[] = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $nv);
                    }
                    foreach (array_unique($baseNoExtCandidates) as $b) {
                        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                            $namesToTry[] = $b . '.' . $ext;
                        }
                    }

                    // Deduplicate
                    $namesToTry = array_values(array_unique(array_filter($namesToTry)));

                    $directoriesToTry = [
                        'storage/ads/gallery/',
                        'storage/images/uploads/ads/gallery/',
                        'images/uploads/ads/gallery/',
                        'images/uploads/ads/',
                        'images/ads/gallery/',
                        'images/uploads/',
                    ];

                    $fallbackFound = null;
                    foreach ($directoriesToTry as $dir) {
                        foreach ($namesToTry as $name) {
                            $legacyPath = public_path($dir . $name);
                            if (file_exists($legacyPath)) {
                                $fallbackFound = asset($dir . $name);
                                break 2;
                            }
                        }
                    }

                    if ($fallbackFound) {
                        $validPhotos[] = $fallbackFound;
                        $cleanedGallery[] = $photo; // necháme pôvodnú hodnotu v DB
                    } else {
                        $missingPhotos[] = [
                            'photo' => $photo,
                            'expected_path' => $physicalPath,
                            'asset_url' => $assetUrl
                        ];
                    }
                }
            }
            
            // Ak sú chýbajúce fotky, iba zalogujeme – DB nemeníme (bezpečný fallback)
            if (!empty($missingPhotos)) {
                \Log::warning('Gallery photo files missing', [
                    'ad_id' => $this->id,
                    'missing_photos' => $missingPhotos,
                    'total_original' => count($this->gallery_photos),
                    'total_valid' => count($validPhotos)
                ]);
            }
            
            return $validPhotos;
            
        } catch (\Exception $e) {
            \Log::error('Error getting gallery image URLs', [
                'ad_id' => $this->id,
                'gallery_photos' => $this->gallery_photos,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
