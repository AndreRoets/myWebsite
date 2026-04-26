<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = ['province_id', 'name', 'slug', 'is_active', 'created_via'];
    protected $casts = ['is_active' => 'boolean'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function towns(): HasMany
    {
        return $this->hasMany(Town::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
