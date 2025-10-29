<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Property;
use App\Models\PropertyImage;
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
        $agents = Agent::orderBy('name')->get();
        return view('admin.properties.create', [
            'property' => new Property(), // Keep one instance and ensure it has a comma
            'agents' => $agents,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_id'     => 'required|exists:agents,id',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'price'        => 'required|numeric|min:0',
            'type'         => 'required|in:house,apartment,townhouse,vacant_land,commercial',
            'bedrooms'     => 'required|integer|min:0',
            'bathrooms'    => 'required|integer|min:0',
            'floor_size'   => 'nullable|numeric|min:0',
            'erf_size'     => 'nullable|numeric|min:0',
            'city'         => 'required|string|max:255',
            'suburb'       => 'required|string|max:255',
            'is_visible'   => 'required|boolean',
            'is_exclusive' => 'required|boolean',
            'special_type' => 'nullable|string',
            'images'       => 'nullable|array',
            'images.*'     => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'dawn_image'   => 'nullable|array',
            'dawn_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'noon_image'   => 'nullable|array',
            'noon_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'dusk_image'   => 'nullable|array',
            'dusk_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = 'for_sale'; // Default status

        $property = Property::create($validated);

        $directory = 'properties/' . $property->id;

        $imageGroups = [
            'images'     => 'general',
            'dawn_image' => 'dawn',
            'noon_image' => 'noon',
            'dusk_image' => 'dusk',
        ];

        foreach ($imageGroups as $inputName => $timeOfDay) {
            if ($request->hasFile($inputName)) {
                foreach ($request->file($inputName) as $file) {
                    $path = $file->store($directory, 'public');
                    $property->images()->create([
                        'path'        => $path,
                        'time_of_day' => $timeOfDay,
                    ]);
                }
            }
        }
    
        return redirect()->route('admin.properties.list')
            ->with('success', 'Property created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        $agents = Agent::orderBy('name')->get();
        return view('admin.properties.edit', compact('property', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        // Handle image deletion first if that's the requested action
        if ($request->input('action') === 'delete_images') {
            $request->validate(['delete_images' => 'nullable|array']);

            if ($request->has('delete_images')) {
                $imagesToDelete = PropertyImage::whereIn('id', $request->input('delete_images'))
                    ->where('property_id', $property->id) // Ensure images belong to the property
                    ->get();

                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
            return redirect()->route('admin.properties.edit', $property)->with('success', 'Selected images have been deleted.');
        }

        // Proceed with full property update
        $validated = $request->validate([
            'agent_id'    => 'required|exists:agents,id',
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

            'status'      => 'required|in:for_sale,for_rent,sold,rented',
            // allow gallery additions on update
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'dawn_image'   => 'nullable|array',
            'dawn_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'noon_image'   => 'nullable|array',
            'noon_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'dusk_image'   => 'nullable|array',
            'dusk_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'is_visible'  => 'required|boolean',
            'is_exclusive' => 'required|boolean',
            'special_type' => 'nullable|string',
            'delete_images' => 'nullable|array',
        ]);

        // Slug
        $validated['slug'] = Str::slug($validated['title']);

        // Handle checkboxes explicitly
        $validated['is_visible'] = $request->boolean('is_visible');
        $validated['is_exclusive'] = $request->boolean('is_exclusive');
        $property->update($validated);

        // Define the image groups and their corresponding time_of_day value
        $imageGroups = [
            'images'     => 'general',
            'dawn_image' => 'dawn',
            'noon_image' => 'noon',
            'dusk_image' => 'dusk',
        ];

        $directory = 'properties/' . $property->id;
        
        foreach ($imageGroups as $inputName => $timeOfDay) {
            if ($request->hasFile($inputName)) {
                foreach ($request->file($inputName) as $file) {
                    $path = $file->store($directory, 'public');
                    $property->images()->create([
                        'path'        => $path,
                        'time_of_day' => $timeOfDay,
                    ]);
                }
            }
        }

        return redirect()->route('admin.properties.edit', $property)
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
