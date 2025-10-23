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
        // Start with a query builder instance
        $query = Property::query()->latest();

        // Apply filters if they are present in the request
        $query->when($request->filled('status'), fn($q) => $q->where('status', $request->status));
        $query->when($request->filled('type'), fn($q) => $q->where('type', $request->type));
        $query->when($request->filled('beds'), fn($q) => $q->where('bedrooms', '>=', $request->beds));
        $query->when($request->filled('min'), fn($q) => $q->where('price', '>=', $request->min));
        $query->when($request->filled('max'), fn($q) => $q->where('price', '<=', $request->max));

        // Paginate the results and append query string values to pagination links
        $properties = $query->paginate(9)->withQueryString();

        return view('properties.index', ['properties' => $properties]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        // Eager load the agent relationship
        $property->load('agent');

        // Find related properties (same type, different from the current one)
        $related = Property::where('type', $property->type)
            ->where('id', '!=', $property->id)
            ->latest()
            ->limit(3)
            ->get();

        return view('properties.show', [
            'property' => $property,
            'related' => $related,
        ]);
    }
}
