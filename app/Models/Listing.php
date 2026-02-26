<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'external_id',
        'title',
        'description',
        'excerpt',
        'price',
        'suburb',
        'region',
        'city',
        'beds',
        'baths',
        'garages',
        'size_m2',
        'erf_size_m2',
        'property_type',
        'mandate_type',
        'status',
        'images_json',
        'agent_json',
        'agency_json',
        'dawn_images',
        'noon_images',
        'dusk_images',
        'gallery_images',
        'published_at',
        'synced_at',
    ];

    protected $casts = [
        'images_json'   => 'array',
        'agent_json'    => 'array',
        'agency_json'   => 'array',
        'dawn_images'   => 'array',
        'noon_images'   => 'array',
        'dusk_images'   => 'array',
        'gallery_images' => 'array',
        'published_at'  => 'datetime',
        'synced_at'     => 'datetime',
    ];

    /**
     * Scope: active listings that have not been soft-deleted.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: filter by suburb (case-insensitive).
     */
    public function scopeBySuburb(Builder $query, string $suburb): Builder
    {
        return $query->where('suburb', $suburb);
    }

    // -------------------------------------------------------------------------
    // Normalized accessors — allow Listing instances to be used in views and
    // templates designed for the Property model (e.g. the property index card).
    // -------------------------------------------------------------------------

    public function getBedroomsAttribute(): int   { return $this->beds  ?? 0; }
    public function getBathroomsAttribute(): int  { return $this->baths ?? 0; }
    public function getCityAttribute(): ?string   { return $this->attributes['city'] ?? $this->region; }
    public function getFloorSizeAttribute(): ?int { return $this->size_m2; }
    public function getIsVisibleAttribute(): bool  { return true; }
    public function getIsExclusiveAttribute(): bool { return false; }
    public function getTypeAttribute(): ?string   { return $this->property_type; }

    /**
     * Return the first image URL as a hero image.
     * Priority: dawn_images → noon_images → dusk_images → gallery_images → images_json.
     * Nexus sends absolute URLs.
     */
    public function getHeroImageAttribute(): ?string
    {
        foreach (['dawn_images', 'noon_images', 'dusk_images', 'gallery_images', 'images_json'] as $group) {
            $images = $this->$group;
            if (!empty($images) && is_array($images)) {
                $first = $images[0] ?? null;
                $url = is_string($first) ? $first : ($first['url'] ?? $first['path'] ?? null);
                if ($url) {
                    return $url;
                }
            }
        }
        return null;
    }

    /**
     * Canonical URL for the listing detail page.
     */
    public function getShowUrlAttribute(): string
    {
        return route('listings.show', $this->external_id);
    }
}
