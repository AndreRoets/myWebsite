<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id', 'title', 'slug', 'excerpt', 'description', 'price', 'status', 
        'type', 'special_type', 'city', 'suburb', 'bedrooms', 'bathrooms', 'garages', 
        'floor_size', 'erf_size', 'is_visible', 'is_exclusive', 'listed_at'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_exclusive' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    /**
     * Get the path of the first image marked as 'general' or the very first image if none are.
     * This will serve as the primary thumbnail/hero image.
     */
    protected function heroImage(): Attribute
    {
        return Attribute::make(
            get: function () {
                $generalImage = $this->images()->where('time_of_day', 'general')->first();
                if ($generalImage) {
                    return $generalImage->path;
                }
                // Fallback to the very first image of any type
                return $this->images()->first()?->path;
            }
        );
    }

    protected function dawnImage(): Attribute
    {
        return Attribute::make(
            get: fn () => array_values($this->images()->where('time_of_day', 'dawn')->pluck('path')->all())
        );
    }

    protected function noonImage(): Attribute
    {
        return Attribute::make(
            get: fn () => array_values($this->images()->where('time_of_day', 'noon')->pluck('path')->all())
        );
    }

    protected function duskImage(): Attribute
    {
        return Attribute::make(
            get: fn () => array_values($this->images()->where('time_of_day', 'dusk')->pluck('path')->all())
        );
    }

    protected function generalImages(): Attribute
    {
        return Attribute::make(
            get: fn () => array_values($this->images()->where('time_of_day', 'general')->pluck('path')->all())
        );
    }
}