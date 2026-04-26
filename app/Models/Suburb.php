<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suburb extends Model
{
    protected $fillable = [
        'town_id', 'name', 'slug', 'postal_code',
        'latitude', 'longitude', 'is_active', 'created_via',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function town(): BelongsTo
    {
        return $this->belongsTo(Town::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
