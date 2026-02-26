<?php

namespace App\Http\Controllers;

use App\Models\Listing;

class ListingController extends Controller
{
    public function show(string $externalId)
    {
        $listing = Listing::active()->where('external_id', $externalId)->firstOrFail();
        return view('listings.show', compact('listing'));
    }
}
