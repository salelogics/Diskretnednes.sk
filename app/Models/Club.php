<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'phone',
        'email',
        'website',
        'logo_path',
        'image_path',
        'hours_weekdays',
        'hours_weekend', 
        'hours_sunday',
        'services',
        'slug',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'services' => 'array',
    ];
}
