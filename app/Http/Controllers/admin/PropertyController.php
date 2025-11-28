<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Property::query()->where('is_visible', true)->latest();

        // Example filtering logic
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->input('bedrooms'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        $properties = $query->paginate(9)->withQueryString();

        // Define the filters to be passed to the view
        $filters = [
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => ['house', 'apartment', 'townhouse', 'vacant_land']],
            ['name' => 'bedrooms', 'label' => 'Min Beds', 'type' => 'select', 'options' => [1, 2, 3, 4, 5]],
            ['name' => 'price_max', 'label' => 'Max Price', 'type' => 'text'],
        ];

        return view('properties.index', compact('properties', 'filters'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        // Basic authorization check
        if ((!$property->is_visible && (!auth()->check() || !auth()->user()->isApproved())) || ($property->is_exclusive && !auth()->check())) {
            abort(403, 'You do not have permission to view this property.');
        }
        return view('properties.show', compact('property'));
    }
}