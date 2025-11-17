<?php

namespace App\Http\Controllers;

use App\Models\SavedSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedSearchController extends Controller
{
    /**
     * Store a newly created saved search in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'filters' => ['required', 'array'],
        ]);

        $savedSearch = SavedSearch::create([
            'name' => $validatedData['name'],
            'filters' => $validatedData['filters'],
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Search saved successfully!',
            'data' => $savedSearch
        ], 201);
    }

    /**
     * Display a listing of the saved searches for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        $savedSearches = $user->savedSearches()->latest()->get();

        return view('profile.show', compact('user', 'savedSearches'));
    }

    /**
     * Execute a saved search.
     */
    public function execute(SavedSearch $savedSearch)
    {
        // Ensure the authenticated user owns the saved search
        if (Auth::id() !== $savedSearch->user_id) {
            abort(403);
        }

        // The filters are likely stored as a JSON string or cast to an array.
        // We ensure it's an array before using it.
        $filters = is_array($savedSearch->filters) ? $savedSearch->filters : json_decode($savedSearch->filters, true);

        // This is the key change: we add the saved_search_id to the query parameters.
        // This will make the "Update This Search" button appear on the results page.
        $queryParams = array_merge($filters, ['saved_search_id' => $savedSearch->id]);

        // Redirect to the property search page with the filters applied
        return redirect()->route('properties.results', $queryParams);
    }

    /**
     * Update the specified saved search in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SavedSearch  $savedSearch
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, SavedSearch $savedSearch)
    {
        // Ensure the user can only update their own searches
        if (Auth::id() !== $savedSearch->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'filters' => 'required|array',
        ]);

        $savedSearch->filters = $validated['filters'];
        $savedSearch->save();

        return response()->json(['success' => true, 'message' => 'Search updated successfully!']);
    }
    /**
     * Remove the specified saved search from storage.
     */
    public function destroy(SavedSearch $savedSearch)
    {
        // Ensure the authenticated user owns the saved search
        if (Auth::id() !== $savedSearch->user_id) {
            abort(403);
        }

        $savedSearch->delete();

        return redirect()->route('profile.show')->with('status', 'Search deleted successfully!');
    }
}
