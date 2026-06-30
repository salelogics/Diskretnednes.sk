<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EroticClub extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wp_id',
        'name',
        'slug',
        'logo_path',
        'image_path',
        'description',
        'address',
        'city',
        'phone',
        'email',
        'website',
        'working_hours',
        'hours_weekdays',
        'hours_weekend',
        'hours_sunday',
        'services',
        'rating',
        'reviews_count',
        'pricing',
        'views',
        'phone_views',
        'latitude',
        'longitude',
        'status',
        'is_featured',
        'is_active',
        'position',
        'wp_modified_at',
    ];

    protected $casts = [
        'services' => 'array',
        'is_active' => 'boolean',
        'position' => 'integer',
        'wp_modified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($club) {
            if (empty($club->slug)) {
                $slug = self::createSlug($club->name);
                $club->slug = $slug;
            }
        });

        static::updating(function ($club) {
            if ($club->isDirty('name') && empty($club->slug)) {
                $slug = self::createSlug($club->name, $club->id);
                $club->slug = $slug;
            }
        });
    }

    private static function createSlug($name, $ignoreId = null)
    {
        // Konverzia slovenských znakov na URL friendly formát
        $slug = strtr($name, [
            'á' => 'a', 'ä' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e',
            'í' => 'i', 'ĺ' => 'l', 'ľ' => 'l', 'ň' => 'n', 'ó' => 'o',
            'ô' => 'o', 'ŕ' => 'r', 'š' => 's', 'ť' => 't', 'ú' => 'u',
            'ý' => 'y', 'ž' => 'z',
            'Á' => 'A', 'Ä' => 'A', 'Č' => 'C', 'Ď' => 'D', 'É' => 'E',
            'Í' => 'I', 'Ĺ' => 'L', 'Ľ' => 'L', 'Ň' => 'N', 'Ó' => 'O',
            'Ô' => 'O', 'Ŕ' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ú' => 'U',
            'Ý' => 'Y', 'Ž' => 'Z'
        ]);

        // Vytvorenie základného slugu
        $slug = Str::slug($slug);

        // Kontrola unikátnosti
        $count = 1;
        $originalSlug = $slug;
        
        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count++;
            $query = static::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    public function getLogoUrlAttribute()
    {
        if (!$this->logo_path) {
            return null;
        }

        // Ak je to už lokálna cesta, preferuj storage prefix
        if (str_starts_with($this->logo_path, 'storage/clubs/') || str_starts_with($this->logo_path, 'storage/images/uploads/clubs/')) {
            return asset($this->logo_path);
        }
        if (str_starts_with($this->logo_path, 'images/') || str_starts_with($this->logo_path, 'clubs/')) {
            return asset('storage/' . ltrim($this->logo_path, '/'));
        }

        // Ak je to WordPress URL, pokúsim sa namapovať na lokálny súbor
        if (str_contains($this->logo_path, 'wp-content') || str_starts_with($this->logo_path, 'http')) {
            // Extraktujem názov súboru z URL
            $filename = basename($this->logo_path);
            
            // Možné lokálne cesty kde môže byť logo (podpora starých aj nových ciest)
            $possiblePaths = [
                'storage/clubs/logos/' . $filename,
                'storage/clubs/' . $filename,
                'storage/images/uploads/clubs/logos/' . $filename,
                'storage/images/uploads/clubs/' . $filename,
            ];
            
            foreach ($possiblePaths as $localPath) {
                if (file_exists(public_path($localPath))) {
                    return asset($localPath);
                }
            }
            
            // Skús nájsť súbor podľa názvu klubu (slug)
            $clubSlug = $this->slug;
            $possibleFiles = [
                $clubSlug . '.jpg',
                $clubSlug . '.png',
                $clubSlug . '.webp',
                str_replace('-', '_', $clubSlug) . '.jpg',
            ];
            
            foreach ($possibleFiles as $possibleFile) {
                $possibleFilePaths = [
                    'storage/clubs/logos/' . $possibleFile,
                    'storage/clubs/' . $possibleFile,
                    'storage/images/uploads/clubs/logos/' . $possibleFile,
                    'storage/images/uploads/clubs/' . $possibleFile,
                ];
                
                foreach ($possibleFilePaths as $possiblePath) {
                    if (file_exists(public_path($possiblePath))) {
                        return asset($possiblePath);
                    }
                }
            }
        }

        // Fallback na originálnu cestu
        return $this->logo_path ? asset($this->logo_path) : null;
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        // Ak je to už lokálna cesta, preferuj storage prefix
        if (str_starts_with($this->image_path, 'storage/clubs/') || str_starts_with($this->image_path, 'storage/images/uploads/clubs/')) {
            return asset($this->image_path);
        }
        if (str_starts_with($this->image_path, 'images/') || str_starts_with($this->image_path, 'clubs/')) {
            return asset('storage/' . ltrim($this->image_path, '/'));
        }

        // Ak je to WordPress URL, pokúsim sa namapovať na lokálny súbor
        if (str_contains($this->image_path, 'wp-content') || str_starts_with($this->image_path, 'http')) {
            // Extraktujem názov súboru z URL
            $filename = basename($this->image_path);
            
            // Možné lokálne cesty kde môže byť obrázok (podpora starých aj nových ciest)
            $possiblePaths = [
                'storage/clubs/' . $filename,
                'storage/clubs/logos/' . $filename,
                'storage/images/uploads/clubs/' . $filename,
                'storage/images/uploads/clubs/logos/' . $filename,
            ];
            
            foreach ($possiblePaths as $localPath) {
                if (file_exists(public_path($localPath))) {
                    return asset($localPath);
                }
            }
            
            // Skús nájsť súbor podľa názvu klubu (slug)
            $clubSlug = $this->slug;
            $possibleFiles = [
                $clubSlug . '.jpg',
                $clubSlug . '.png',
                $clubSlug . '.webp',
                str_replace('-', '_', $clubSlug) . '.jpg',
            ];
            
            foreach ($possibleFiles as $possibleFile) {
                $possibleFilePaths = [
                    'storage/clubs/' . $possibleFile,
                    'storage/clubs/logos/' . $possibleFile,
                    'storage/images/uploads/clubs/' . $possibleFile,
                    'storage/images/uploads/clubs/logos/' . $possibleFile,
                ];
                
                foreach ($possibleFilePaths as $possiblePath) {
                    if (file_exists(public_path($possiblePath))) {
                        return asset($possiblePath);
                    }
                }
            }
        }

        // Fallback na originálnu cestu
        return $this->image_path ? asset($this->image_path) : null;
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
} 