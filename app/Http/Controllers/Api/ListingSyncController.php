<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListingSyncController extends Controller
{
    /**
     * Upsert a listing from the Nexus sync payload.
     */
    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate([
            'external_id'    => ['required', 'string'],
            'title'          => ['required', 'string', 'max:500'],
            'price'          => ['required', 'integer', 'min:0'],
            'status'         => ['required', 'string'],
            'description'    => ['nullable', 'string'],
            'excerpt'        => ['nullable', 'string'],
            'suburb'         => ['nullable', 'string'],
            'region'         => ['nullable', 'string'],
            'city'           => ['nullable', 'string'],
            'beds'           => ['nullable', 'integer'],
            'baths'          => ['nullable', 'integer'],
            'garages'        => ['nullable', 'integer'],
            'size_m2'        => ['nullable', 'integer'],
            'erf_size_m2'    => ['nullable', 'integer'],
            'property_type'  => ['nullable', 'string'],
            'mandate_type'   => ['nullable', 'string'],
            'images'         => ['nullable', 'array'],
            'agent'          => ['nullable', 'array'],
            'agency'         => ['nullable', 'array'],
            'dawn_images'    => ['nullable', 'array'],
            'noon_images'    => ['nullable', 'array'],
            'dusk_images'    => ['nullable', 'array'],
            'gallery_images' => ['nullable', 'array'],
            'published_at'   => ['nullable', 'string'],
        ]);

        Listing::withTrashed()
            ->updateOrCreate(
                ['external_id' => $data['external_id']],
                [
                    'title'          => $data['title'],
                    'description'    => $data['description'] ?? null,
                    'excerpt'        => $data['excerpt'] ?? null,
                    'price'          => $data['price'],
                    'suburb'         => $data['suburb'] ?? '',
                    'region'         => $data['region'] ?? null,
                    'city'           => $data['city'] ?? null,
                    'beds'           => $data['beds'] ?? 0,
                    'baths'          => $data['baths'] ?? 0,
                    'garages'        => $data['garages'] ?? 0,
                    'size_m2'        => $data['size_m2'] ?? null,
                    'erf_size_m2'    => $data['erf_size_m2'] ?? null,
                    'property_type'  => $data['property_type'] ?? '',
                    'mandate_type'   => $data['mandate_type'] ?? null,
                    'status'         => $data['status'],
                    'images_json'    => $data['images'] ?? null,
                    'agent_json'     => $data['agent'] ?? null,
                    'agency_json'    => $data['agency'] ?? null,
                    'dawn_images'    => $data['dawn_images'] ?? null,
                    'noon_images'    => $data['noon_images'] ?? null,
                    'dusk_images'    => $data['dusk_images'] ?? null,
                    'gallery_images' => $data['gallery_images'] ?? null,
                    'published_at'   => $data['published_at'] ?? null,
                    'synced_at'      => now(),
                    'deleted_at'     => null, // restore if previously soft-deleted
                ]
            );

        return response()->json(['ok' => true]);
    }

    /**
     * Soft-delete a listing by its external ID.
     */
    public function destroy(string $externalId): JsonResponse
    {
        $listing = Listing::where('external_id', $externalId)->firstOrFail();
        $listing->delete();

        return response()->json(['ok' => true]);
    }
}
