<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'title',
        'description',
        'bio',
        'email',
        'phone',
        'image',
        'photo_source_url',
        'agency_external_id',
        'agency_name',
        'agency_branch',
        'created_via',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        return Storage::disk('public')->url($this->image);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
