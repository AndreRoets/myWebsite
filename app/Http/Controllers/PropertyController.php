<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Listing;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // --- Nexus listings (primary source) ---
        $listingQuery = Listing::active();

        $listingQuery->when($request->filled('title'),
            fn($q) => $q->where('title', 'like', '%' . $request->title . '%'));
        $listingQuery->when($request->filled('min_price'),
            fn($q) => $q->where('price', '>=', $request->min_price));
        $listingQuery->when($request->filled('max_price'),
            fn($q) => $q->where('price', '<=', $request->max_price));
        $listingQuery->when($request->filled('suburb'),
            fn($q) => $q->where('suburb', 'like', '%' . $request->suburb . '%'));
        $listingQuery->when($request->filled('bedrooms'),
            fn($q) => $q->where('beds', '>=', $request->bedrooms));
        $listingQuery->when($request->filled('bathrooms'),
            fn($q) => $q->where('baths', '>=', $request->bathrooms));
        // Map the status filter to mandate_type on listings (e.g. 'for_sale', 'for_rent')
        $listingQuery->when($request->filled('status'),
            fn($q) => $q->where('mandate_type', $request->status));
        $listingQuery->when($request->filled('type'),
            fn($q) => $q->where('property_type', $request->type));

        $allListings = $listingQuery->latest('synced_at')->get();

        // --- Manually managed properties ---
        $query = Property::query()->with('agent', 'images');

        $query->when($request->filled('title'),
            fn($q) => $q->where('title', 'like', '%' . $request->title . '%'));
        $query->when($request->filled('agent_id'),
            fn($q) => $q->where('agent_id', $request->agent_id));
        $query->when($request->filled('min_price'),
            fn($q) => $q->where('price', '>=', $request->min_price));
        $query->when($request->filled('max_price'),
            fn($q) => $q->where('price', '<=', $request->max_price));
        $query->when($request->filled('status'),
            fn($q) => $q->where('status', $request->status));
        $query->when($request->filled('type'),
            fn($q) => $q->where('type', $request->type));
        $query->when($request->filled('special_type'),
            fn($q) => $q->where('special_type', $request->special_type));
        $query->when($request->filled('city'),
            fn($q) => $q->where('city', $request->city));
        $query->when($request->filled('suburb'),
            fn($q) => $q->where('suburb', $request->suburb));
        $query->when($request->filled('bedrooms'),
            fn($q) => $q->where('bedrooms', '>=', $request->bedrooms));
        $query->when($request->filled('bathrooms'),
            fn($q) => $q->where('bathrooms', '>=', $request->bathrooms));
        $query->when($request->filled('garages'),
            fn($q) => $q->where('garages', '>=', $request->garages));
        $query->when($request->filled('min_floor_size'),
            fn($q) => $q->where('floor_size', '>=', $request->min_floor_size));
        $query->when($request->filled('max_floor_size'),
            fn($q) => $q->where('floor_size', '<=', $request->max_floor_size));
        $query->when($request->filled('min_erf_size'),
            fn($q) => $q->where('erf_size', '>=', $request->min_erf_size));
        $query->when($request->filled('max_erf_size'),
            fn($q) => $q->where('erf_size', '<=', $request->max_erf_size));

        $allProperties = $query->latest()->get();

        // --- Merge: Nexus listings first, then manually managed properties ---
        $merged = $allListings->concat($allProperties);

        $perPage  = 12;
        $page     = LengthAwarePaginator::resolveCurrentPage();
        $properties = new LengthAwarePaginator(
            $merged->forPage($page, $perPage)->values(),
            $merged->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Define the filters for the view to render the form dynamically.
        $filters = [
            ['name' => 'title', 'label' => 'Title/Keyword', 'type' => 'text'],
            ['name' => 'min_price', 'label' => 'Min Price', 'type' => 'number'],
            ['name' => 'max_price', 'label' => 'Max Price', 'type' => 'number'],
            ['name' => 'bedrooms', 'label' => 'Min Beds', 'type' => 'number'],
            ['name' => 'bathrooms', 'label' => 'Min Baths', 'type' => 'number'],
            [
                'name' => 'status', 'label' => 'Status', 'type' => 'select',
                'options' => ['For Sale', 'For Rent', 'Sold']
            ],
            [
                'name' => 'type', 'label' => 'Type', 'type' => 'select',
                'options' => ['House', 'Apartment', 'Condo', 'Townhouse', 'Land']
            ],
            ['name' => 'city', 'label' => 'City', 'type' => 'text'],
            ['name' => 'suburb', 'label' => 'Suburb', 'type' => 'text'],
        ];

        // Pass the request input to the view to repopulate search fields on the results page
        return view('properties.index', [
            'properties' => $properties,
            'input' => $request->all(),
            'filters' => $filters,
        ]);
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
        $propertyTypes = Property::select('type')->distinct()->whereNotNull('type')->pluck('type');
        return view('properties.search', compact('propertyTypes'));
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

        // We only want to pass valid filter keys to the paginator's appends method.
        $validFilterKeys = ['property_type', 'price_min', 'price_max', 'bedrooms', 'bathrooms', 'location', 'saved_search_id'];
        $filters = $request->only($validFilterKeys);

        $properties = $query->paginate(9)->appends($filters);

        $propertyTypes = Property::select('type')->distinct()->whereNotNull('type')->pluck('type');

        return view('properties.results', compact('properties', 'filters', 'propertyTypes'));
    }

    /**
     * Toggle the is_visible flag on a property.
     */
    public function toggleDisplay(Property $property)
    {
        $property->is_visible = !$property->is_visible;
        $property->save();

        $status = $property->is_visible ? 'visible' : 'hidden';
        return back()->with('status', "Property is now {$status}.");
    }

    /**
     * Get all the options for the property search filters.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilterOptions()
    {
        // Static lists that are always available
        $statuses = ['For Sale', 'For Rent', 'Sold'];
        $types = ['House', 'Apartment', 'Condo', 'Townhouse', 'Land'];
        $special_types = ['Waterfront', 'Penthouse', 'Exclusive Villa', 'Gated Community'];

        // Dynamic lists from the database
        $agents = Agent::select('id', 'name')->orderBy('name')->get();
        $cities = Property::select('city')->distinct()->whereNotNull('city')->orderBy('city')->pluck('city');
        $suburbs = Property::select('suburb')->distinct()->whereNotNull('suburb')->orderBy('suburb')->pluck('suburb');

        return response()->json([
            'agents' => $agents,
            'statuses' => $statuses,
            'types' => $types,
            'special_types' => $special_types,
            'cities' => $cities,
            'suburbs' => $suburbs,
        ]);
    }
}
