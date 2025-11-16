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
        $query = Property::with('images')->latest()->whereIn('status', ['for_sale', 'for_rent']);

        // Apply filters if they are present in the request
        $query->when($request->filled('property_type'), fn($q) => $q->where('type', $request->property_type));
        $query->when($request->filled('price_min'), fn($q) => $q->where('price', '>=', $request->price_min));
        $query->when($request->filled('price_max'), fn($q) => $q->where('price', '<=', $request->price_max));
        $query->when($request->filled('bedrooms'), fn($q) => $q->where('bedrooms', '>=', $request->bedrooms));
        $query->when($request->filled('bathrooms'), fn($q) => $q->where('bathrooms', '>=', $request->bathrooms));
        $query->when($request->filled('location'), fn($q) => $q->where('location', 'like', '%' . $request->location . '%'));

        $properties = $query->paginate(9);

        if ($request->expectsJson()) {
            return response()->json($properties->items());
        }

        return view('properties.index', compact('properties'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        $property->load('images');
        // Check if the property is restricted and if the user is allowed to see it.
        $isRestrictedForUser = !$property->is_visible && (!auth()->check() || !auth()->user()->isApproved());

        if ($isRestrictedForUser) {
            // Find related properties (same type, different from the current one)
            // You can either show a 403 Forbidden page or redirect with a message.
            // Redirecting is often more user-friendly.
            return redirect()->route('login')->with('status', 'You must be an approved user to view this exclusive property.');
        }

        // Fetch related properties (example: same type, different property)
        $related = Property::with('images')->where('type', $property->type)
            ->where('id', '!=', $property->id)
            ->latest()
            ->where('is_visible', true) // Only show visible related properties
            ->limit(3)
            ->get();

        return view('properties.show', compact('property', 'related'));
    }

    public function search()
    {
        return view('properties.search');
    }

    public function results(Request $request)
    {
        $query = Property::with('images')->latest()->whereIn('status', ['for_sale', 'for_rent']);

        // Apply filters if they are present in the request
        $query->when($request->filled('property_type'), fn($q) => $q->where('type', $request->property_type));
        $query->when($request->filled('price_min'), fn($q) => $q->where('price', '>=', $request->price_min));
        $query->when($request->filled('price_max'), fn($q) => $q->where('price', '<=', $request->price_max));
        $query->when($request->filled('bedrooms'), fn($q) => $q->where('bedrooms', '>=', $request->bedrooms));
        $query->when($request->filled('bathrooms'), fn($q) => $q->where('bathrooms', '>=', $request->bathrooms));
        $query->when($request->filled('location'), fn($q) => $q->where('location', 'like', '%' . $request->location . '%'));

        $properties = $query->paginate(9);

        return view('properties.results', compact('properties'));
    }
}
