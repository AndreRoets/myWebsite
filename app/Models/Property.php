<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'external_id', 'agent_id', 'title', 'slug', 'excerpt', 'description',
        'price', 'status', 'type', 'special_type', 'listing_type', 'mandate_type', 'category',

        'unit_number', 'street_number', 'street_name', 'complex_name',
        'city', 'town', 'suburb', 'region', 'province', 'postal_code',
        'latitude', 'longitude',
        'suburb_id', 'town_id', 'region_id', 'province_id',

        'bedrooms', 'bathrooms', 'garages', 'floor_size', 'erf_size',
        'is_visible', 'is_exclusive', 'listed_at', 'display', 'hero_image',
        'listed_date', 'expiry_date', 'published_at', 'synced_at',

        'gallery_images_json', 'dawn_images_json', 'noon_images_json',
        'dusk_images_json', 'primary_images_json',
        'youtube_video_id', 'matterport_id',

        'features_json', 'agency_json',
    ];

    protected $casts = [
        'is_visible'           => 'boolean',
        'is_exclusive'         => 'boolean',
        'latitude'             => 'float',
        'longitude'            => 'float',
        'listed_date'          => 'date',
        'expiry_date'          => 'date',
        'published_at'         => 'datetime',
        'synced_at'            => 'datetime',
        'listed_at'            => 'datetime',
        'gallery_images_json'  => 'array',
        'dawn_images_json'     => 'array',
        'noon_images_json'     => 'array',
        'dusk_images_json'     => 'array',
        'primary_images_json'  => 'array',
        'features_json'        => 'array',
        'agency_json'          => 'array',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function suburbModel(): BelongsTo
    {
        return $this->belongsTo(Suburb::class, 'suburb_id');
    }

    public function townModel(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'town_id');
    }

    public function regionModel(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function provinceModel(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    protected function heroImage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!empty($this->primary_images_json[0])) {
                    return $this->primary_images_json[0];
                }
                $generalImage = $this->images()->where('time_of_day', 'general')->first();
                if ($generalImage) {
                    return $generalImage->path;
                }
                return $this->images()->first()?->path;
            }
        );
    }

    protected function dawnImage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dawn_images_json
                ?: array_values($this->images()->where('time_of_day', 'dawn')->pluck('path')->all())
        );
    }

    protected function noonImage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->noon_images_json
                ?: array_values($this->images()->where('time_of_day', 'noon')->pluck('path')->all())
        );
    }

    protected function duskImage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dusk_images_json
                ?: array_values($this->images()->where('time_of_day', 'dusk')->pluck('path')->all())
        );
    }

    protected function generalImages(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->primary_images_json
                ?: $this->gallery_images_json
                ?: array_values($this->images()->where('time_of_day', 'general')->pluck('path')->all())
        );
    }

    public function getShowUrlAttribute(): string
    {
        return route('properties.show', $this);
    }
}
