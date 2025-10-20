<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::whereIn('status', ['for_sale', 'for_rent'])
            ->latest()
            ->paginate(10);
        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.properties.create', [
            'property' => new Property()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'type'        => 'required|in:house,apartment,townhouse,vacant_land,commercial',
            'bedrooms'    => 'required|integer|min:0',
            'bathrooms'   => 'required|integer|min:0',
            'floor_size'  => 'nullable|numeric|min:0',
            'erf_size'    => 'nullable|numeric|min:0',
            'city'        => 'required|string|max:255',
            'suburb'      => 'required|string|max:255',

            // Accept either key on create:
            'hero_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'hero'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',

            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        // Slug
        $validated['slug'] = Str::slug($validated['title']);

        // Remove temp file inputs from mass assignment
        unset($validated['hero_image'], $validated['hero'], $validated['images']);

        // Create first to get ID
        $property = Property::create($validated);

        $directory = 'properties/' . $property->id;

        // Hero upload (prefer hero_image, fallback to hero)
        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store($directory, 'public');
            $property->hero_image = $path;
        } elseif ($request->hasFile('hero')) {
            $path = $request->file('hero')->store($directory, 'public');
            $property->hero_image = $path;
        }

        // Gallery images
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store($directory, 'public');
            }
            $property->images = $imagePaths; // requires casts on model
        }

        $property->save();

        return redirect()->route('admin.properties.list')
            ->with('success', 'Property created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        return view('admin.properties.edit', compact('property'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'type'        => 'required|in:house,apartment,townhouse,vacant_land,commercial',
            'bedrooms'    => 'required|integer|min:0',
            'bathrooms'   => 'required|integer|min:0',
            'floor_size'  => 'nullable|numeric|min:0',
            'erf_size'    => 'nullable|numeric|min:0',
            'city'        => 'required|string|max:255',
            'suburb'      => 'required|string|max:255',

            'hero_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'status'      => 'required|in:for_sale,for_rent,sold,rented',
            // allow gallery additions on update
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        // Slug
        $validated['slug'] = Str::slug($validated['title']);

        $directory = 'properties/' . $property->id;

        // Replace hero image (delete old file only)
        if ($request->hasFile('hero_image')) {
            if ($property->hero_image) {
                Storage::disk('public')->delete($property->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store($directory, 'public');
        }

        // Remove temp gallery input before update()
        unset($validated['images']);

        $property->update($validated);

        // Append new gallery images
        if ($request->hasFile('images')) {
            $existing = $property->images ?? [];
            foreach ($request->file('images') as $file) {
                $existing[] = $file->store($directory, 'public');
            }
            $property->images = $existing;
            $property->save();
        }

        return redirect()->route('admin.properties.list')
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        // Remove all files for this property
        if ($property->id) {
            Storage::disk('public')->deleteDirectory('properties/' . $property->id);
        }

        $property->delete();

        return redirect()->route('admin.properties.list')
            ->with('success', 'Property deleted successfully.');
    }
}
