<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','reference','price','currency',
        'city','suburb','province','country',
        'bedrooms','bathrooms','garages','floor_size','erf_size',
        'type','status','excerpt','description',
        'images','hero_image','lat','lng','is_featured','listed_at'
    ];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'listed_at' => 'datetime',
    ];

    // Accessors
    public function getDisplayPriceAttribute(): string
    {
        if (is_null($this->price)) return 'Price on request';
        return 'R ' . number_format($this->price, 0, '.', ' ');
    }

    public function getDisplayStatusAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getDisplayTypeAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    public function routeKeyName(): string
    {
        return 'slug'; // enables implicit route model binding by slug
    }
}
