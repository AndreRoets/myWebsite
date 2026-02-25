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
        'price',
        'suburb',
        'region',
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
        'published_at',
        'synced_at',
    ];

    protected $casts = [
        'images_json' => 'array',
        'agent_json'  => 'array',
        'agency_json' => 'array',
        'published_at' => 'datetime',
        'synced_at'    => 'datetime',
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
}
