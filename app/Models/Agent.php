<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description',
        'email',
        'phone',
        'image',
    ];

    /**
     * Get the full URL to the agent's image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    /**
     * Get the properties for the agent.
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}