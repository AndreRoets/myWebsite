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
        $query = Property::query()->latest()->whereIn('status', ['for_sale', 'for_rent']);

        // Apply filters if they are present in the request
        $query->when($request->filled('type'), fn($q) => $q->where('type', $request->type));
        $query->when($request->filled('beds'), fn($q) => $q->where('bedrooms', '>=', $request->beds));
        $query->when($request->filled('min'), fn($q) => $q->where('price', '>=', $request->min));
        $query->when($request->filled('max'), fn($q) => $q->where('price', '<=', $request->max));

        // Paginate the results and append query string values to pagination links
        $properties = $query->paginate(9)->withQueryString();

        return view('properties.index', compact('properties'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        // Check if the property is restricted and if the user is allowed to see it.
        $isRestrictedForUser = !$property->is_visible && (!auth()->check() || !auth()->user()->isApproved());

        if ($isRestrictedForUser) {
            // Find related properties (same type, different from the current one)
            // You can either show a 403 Forbidden page or redirect with a message.
            // Redirecting is often more user-friendly.
            return redirect()->route('login')->with('status', 'You must be an approved user to view this exclusive property.');
        }

        // Fetch related properties (example: same type, different property)
        $related = Property::where('type', $property->type)
            ->where('id', '!=', $property->id)
            ->latest()
            ->where('is_visible', true) // Only show visible related properties
            ->limit(3)
            ->get();

        return view('properties.show', compact('property', 'related'));
    }
}
