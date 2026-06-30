<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'wp_id',
        'title',
        'slug',
        'image_path',
        'excerpt',
        'content',
        'is_published',
        'published_at',
        'author_id',
        'wp_author_id',
        'wp_featured_media_id',
        'wp_modified_at',
        'wp_categories',
        'wp_tags'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'wp_modified_at' => 'datetime',
        'wp_categories' => 'array',
        'wp_tags' => 'array'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');
    }

    /**
     * Získa správnu URL pre obrázok (lokálny vs externý)
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        // Ak je to plná URL (WordPress import), použije sa priamo
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        // Preferuj storage prefix (nový aj starý tvar)
        if (str_starts_with($this->image_path, 'storage/blog/images/') || str_starts_with($this->image_path, 'storage/images/blog/')) {
            return asset($this->image_path);
        }

        // Backward: ak obsahuje "images/blog" alebo "blog/images"
        if (str_starts_with($this->image_path, 'images/blog')) {
            return asset('storage/' . $this->image_path);
        }
        if (str_starts_with($this->image_path, 'blog/images')) {
            return asset('storage/' . $this->image_path);
        }

        // Ak je to relatívna cesta, pridá sa storage/blog/images prefix  
        return asset('storage/blog/images/' . $this->image_path);
    }

    /**
     * Zistí či je obrázok externý
     */
    public function getIsExternalImageAttribute(): bool
    {
        return $this->image_path && filter_var($this->image_path, FILTER_VALIDATE_URL);
    }
}
