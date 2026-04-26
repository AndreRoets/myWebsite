<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Town extends Model
{
    protected $fillable = ['region_id', 'name', 'slug', 'is_active', 'created_via'];
    protected $casts = ['is_active' => 'boolean'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function suburbs(): HasMany
    {
        return $this->hasMany(Suburb::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
