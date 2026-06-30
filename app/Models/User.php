<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'username',
        'about',
        'photo',
        'first_name',
        'last_name',
        'phone',
        'country',
        'street_address',
        'city',
        'region',
        'postal_code',
        'google_id',
        'facebook_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Mutátor pre email - automaticky konvertuje na malé písmená
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        return false;
    }

    public function hasAnyRole($roles): bool
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getIsAdminAttribute($value): bool
    {
        return (bool) $value;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }

    public function activeAds(): HasMany
    {
        return $this->hasMany(Ad::class)->where('status', 'active');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function customerReports(): HasMany
    {
        return $this->hasMany(CustomerReport::class);
    }

    public function adPayments(): HasMany
    {
        return $this->hasMany(AdPayment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public function adminNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_admin', true);
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_admin', false);
    }

    /**
     * Get the user's profile photo URL.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->photo) {
            // Serve from private storage via route
            return route('profile-photos.private', ['path' => $this->photo]);
        }
        
        // Default avatar
        return asset('images/default-avatar.svg');
    }
}
