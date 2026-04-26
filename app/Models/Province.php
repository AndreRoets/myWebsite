<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'created_via'];
    protected $casts = ['is_active' => 'boolean'];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
