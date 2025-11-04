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

        // Redirect to the property search page with the filters applied
        return redirect()->route('properties.results', $savedSearch->filters);
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
