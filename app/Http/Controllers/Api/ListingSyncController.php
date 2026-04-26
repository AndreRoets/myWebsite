<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\ListingSyncLog;
use App\Models\Property;
use App\Models\Province;
use App\Models\Region;
use App\Models\Suburb;
use App\Models\Town;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingSyncController extends Controller
{
    private const IMAGE_BUCKETS = [
        'images'         => 'primary_images_json',
        'gallery_images' => 'gallery_images_json',
        'dawn_images'    => 'dawn_images_json',
        'noon_images'    => 'noon_images_json',
        'dusk_images'    => 'dusk_images_json',
    ];

    public function sync(Request $request): JsonResponse
    {
        $start    = microtime(true);
        $body     = $request->all();
        $extId    = $body['external_id'] ?? null;
        $loggable = $this->stripBytesForLog($body);

        if (!$extId || !is_string($extId)) {
            return $this->fail($extId, 422, 'external_id is required', $loggable, $start);
        }
        if (empty($body['title'])) {
            return $this->fail($extId, 422, 'title is required', $loggable, $start);
        }
        if (!isset($body['price']) || !is_numeric($body['price'])) {
            return $this->fail($extId, 422, 'price is required (integer)', $loggable, $start);
        }

        try {
            $result = DB::transaction(function () use ($body) {
                $loc = is_array($body['location'] ?? null) ? $body['location'] : [];
                [$province, $region, $town, $suburb] = $this->resolveLocation($loc);

                $agent = null;
                if (!empty($body['agent']) && is_array($body['agent'])) {
                    $agent = $this->upsertAgent($body['agent'], $body['agency'] ?? null);
                }

                $property = Property::withTrashed()->updateOrCreate(
                    ['external_id' => $extId],
                    $this->propertyAttributes($body, $extId, $loc, $province, $region, $town, $suburb, $agent)
                );

                // Wipe any existing image dir for this property — idempotent re-sync.
                Storage::disk('public')->deleteDirectory("properties/{$property->id}");

                $bucketCols   = [];
                $writtenPaths = [];
                $primaryPaths = [];

                foreach (self::IMAGE_BUCKETS as $payloadKey => $column) {
                    $items = $body[$payloadKey] ?? [];
                    if (!is_array($items) || empty($items)) {
                        $bucketCols[$column] = null;
                        continue;
                    }
                    $paths = $this->writeBucket($property->id, $payloadKey, $items);
                    $bucketCols[$column] = $paths ?: null;
                    $writtenPaths        = array_merge($writtenPaths, $paths);
                    if ($payloadKey === 'images') $primaryPaths = $paths;
                }

                // Confirm files actually exist on disk; only count the survivors.
                $confirmed = array_values(array_filter(
                    $writtenPaths,
                    fn ($p) => Storage::disk('public')->exists($p)
                ));

                $hero = $primaryPaths[0] ?? ($confirmed[0] ?? null);

                $bucketCols['hero_image'] = $hero;
                $bucketCols['display']    = 1;
                $bucketCols['is_visible'] = true;

                $property->update($bucketCols);

                // Legacy `images` column collides with the hasMany relation, so write it raw.
                DB::table('properties')->where('id', $property->id)->update([
                    'images' => $confirmed ? json_encode($confirmed) : null,
                ]);

                return [
                    'property'     => $property->fresh(),
                    'agent'        => $agent,
                    'suburb'       => $suburb,
                    'images_saved' => count($confirmed),
                ];
            });

            $resp = [
                'ok'           => true,
                'property_id'  => $result['property']->id,
                'agent_id'     => $result['agent']?->id,
                'suburb_id'    => $result['suburb']?->id,
                'images_saved' => $result['images_saved'],
            ];
            $this->log('sync', $extId, 200, $start, $loggable, $resp);
            return response()->json($resp);
        } catch (\Throwable $e) {
            Log::error('Listing sync failed', [
                'external_id' => $extId,
                'error'       => $e->getMessage(),
                'file'        => $e->getFile() . ':' . $e->getLine(),
            ]);
            return $this->fail($extId, 500, $e->getMessage(), $loggable, $start);
        }
    }

    public function destroy(Request $request, string $externalId): JsonResponse
    {
        $start = microtime(true);
        $property = Property::where('external_id', $externalId)->first();
        if (!$property) {
            $resp = ['ok' => true, 'message' => 'not found, treated as deleted'];
            $this->log('delete', $externalId, 200, $start, [], $resp);
            return response()->json($resp);
        }
        $property->delete();
        $resp = ['ok' => true, 'property_id' => $property->id];
        $this->log('delete', $externalId, 200, $start, [], $resp);
        return response()->json($resp);
    }

    private function propertyAttributes(
        array $body, string $extId, array $loc,
        ?Province $province, ?Region $region, ?Town $town, ?Suburb $suburb,
        ?Agent $agent
    ): array {
        return [
            'agent_id'      => $agent?->id,
            'title'         => (string) $body['title'],
            'slug'          => $this->uniqueSlug($body['title'], $extId),
            'excerpt'       => $body['excerpt']     ?? null,
            'description'   => $body['description'] ?? null,
            'price'         => (int) $body['price'],
            'status'        => (string) ($body['status'] ?? 'for_sale'),
            'type'          => (string) ($body['property_type'] ?? 'house'),
            'listing_type'  => $body['listing_type'] ?? null,
            'mandate_type'  => $body['mandate_type'] ?? null,
            'category'      => $body['category']     ?? null,

            'unit_number'   => $loc['unit_number']   ?? null,
            'street_number' => $loc['street_number'] ?? null,
            'street_name'   => $loc['street_name']   ?? null,
            'complex_name'  => $loc['complex_name']  ?? null,
            'suburb'        => $loc['suburb']        ?? null,
            'town'          => $loc['town']          ?? null,
            'city'          => $loc['city']          ?? null,
            'region'        => $loc['region']        ?? null,
            'province'      => $loc['province']      ?? null,
            'postal_code'   => $loc['postal_code']   ?? null,
            'latitude'      => $loc['latitude']      ?? null,
            'longitude'     => $loc['longitude']     ?? null,

            'province_id'   => $province?->id,
            'region_id'     => $region?->id,
            'town_id'       => $town?->id,
            'suburb_id'     => $suburb?->id,

            'bedrooms'      => $body['beds']        ?? null,
            'bathrooms'     => isset($body['baths']) ? (int) $body['baths'] : null,
            'garages'       => $body['garages']     ?? null,
            'floor_size'    => $body['size_m2']     ?? null,
            'erf_size'      => $body['erf_size_m2'] ?? null,

            'listed_date'   => $body['listed_date']  ?? null,
            'expiry_date'   => $body['expiry_date']  ?? null,
            'published_at'  => $body['published_at'] ?? now(),
            'synced_at'     => now(),

            'youtube_video_id' => $body['youtube_video_id'] ?? null,
            'matterport_id'    => $body['matterport_id']    ?? null,

            'features_json' => $body['features'] ?? null,
            'agency_json'   => $body['agency']   ?? null,

            'display'       => 1,
            'is_visible'    => true,
            'deleted_at'    => null,
        ];
    }

    /**
     * @return array{0: ?Province, 1: ?Region, 2: ?Town, 3: ?Suburb}
     */
    private function resolveLocation(array $loc): array
    {
        $provinceName = $this->clean($loc['province'] ?? null);
        $regionName   = $this->clean($loc['region']   ?? null);
        $townName     = $this->clean($loc['town']     ?? $loc['city'] ?? null);
        $suburbName   = $this->clean($loc['suburb']   ?? null);

        $province = $region = $town = $suburb = null;

        if ($provinceName) {
            $province = Province::firstOrCreate(
                [DB::raw('LOWER(name)') => strtolower($provinceName)],
                ['name' => $provinceName, 'slug' => $this->taxonomySlug(Province::class, $provinceName), 'created_via' => 'sync']
            );
        }
        if ($regionName && $province) {
            $region = Region::firstOrCreate(
                ['province_id' => $province->id, 'name' => $regionName],
                ['slug' => $this->taxonomySlug(Region::class, $regionName, ['province_id' => $province->id]), 'created_via' => 'sync']
            );
        }
        if ($townName && $region) {
            $town = Town::firstOrCreate(
                ['region_id' => $region->id, 'name' => $townName],
                ['slug' => $this->taxonomySlug(Town::class, $townName, ['region_id' => $region->id]), 'created_via' => 'sync']
            );
        }
        if ($suburbName && $town) {
            $suburb = Suburb::firstOrCreate(
                ['town_id' => $town->id, 'name' => $suburbName],
                [
                    'slug'        => $this->taxonomySlug(Suburb::class, $suburbName, ['town_id' => $town->id]),
                    'postal_code' => $loc['postal_code'] ?? null,
                    'latitude'    => $loc['latitude']    ?? null,
                    'longitude'   => $loc['longitude']   ?? null,
                    'created_via' => 'sync',
                ]
            );
        }

        return [$province, $region, $town, $suburb];
    }

    private function upsertAgent(array $input, ?array $agency): Agent
    {
        $agent = null;
        if (!empty($input['external_id'])) {
            $agent = Agent::where('external_id', $input['external_id'])->first();
        }
        if (!$agent && !empty($input['email'])) {
            $agent = Agent::where('email', $input['email'])->first();
        }

        $attrs = [
            'external_id'        => $input['external_id'] ?? ($agent->external_id ?? null),
            'name'               => $input['name']  ?? ($agent->name  ?? 'Unknown Agent'),
            'email'              => $input['email'] ?? ($agent->email ?? null),
            'phone'              => $input['phone'] ?? ($agent->phone ?? null),
            'bio'                => $input['bio']   ?? ($agent->bio   ?? null),
            'agency_external_id' => $agency['external_id'] ?? ($agent->agency_external_id ?? null),
            'agency_name'        => $agency['name']        ?? ($agent->agency_name        ?? null),
            'agency_branch'      => $agency['branch']      ?? ($agent->agency_branch      ?? null),
        ];

        if (!$agent) {
            $agent = Agent::create(array_merge($attrs, ['title' => 'Agent', 'created_via' => 'sync']));
        } else {
            $agent->fill($attrs)->save();
        }

        $photo = $input['photo'] ?? null;
        if (is_array($photo) && !empty($photo['bytes'])) {
            $this->writeAgentPhoto($agent, $photo);
        }

        return $agent;
    }

    private function writeAgentPhoto(Agent $agent, array $photo): void
    {
        $bytes = base64_decode($photo['bytes'], true);
        if (!$bytes) {
            Log::warning('Agent photo bytes invalid', ['agent_id' => $agent->id]);
            return;
        }

        $ext      = $this->extFromMime($photo['mime'] ?? null, $photo['filename'] ?? null);
        $path     = "agents/{$agent->id}.{$ext}";
        $sourceId = 'md5:' . md5($bytes);

        if ($agent->photo_source_url === $sourceId
            && $agent->image === $path
            && Storage::disk('public')->exists($path)) {
            return; // unchanged
        }

        // Remove any prior file with a different extension.
        foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $other) {
            $stale = "agents/{$agent->id}.{$other}";
            if ($stale !== $path && Storage::disk('public')->exists($stale)) {
                Storage::disk('public')->delete($stale);
            }
        }

        Storage::disk('public')->put($path, $bytes);

        $agent->forceFill([
            'image'            => $path,
            'photo_source_url' => $sourceId,
        ])->save();
    }

    /**
     * Decode + write one image bucket. Returns the relative paths that ended up on disk.
     *
     * @return array<string>
     */
    private function writeBucket(int $propertyId, string $bucket, array $items): array
    {
        usort($items, fn ($a, $b) =>
            ($a['sort_order'] ?? PHP_INT_MAX) <=> ($b['sort_order'] ?? PHP_INT_MAX)
        );

        $paths = [];
        foreach ($items as $i => $item) {
            $b64 = $item['bytes'] ?? null;
            if (!is_string($b64) || $b64 === '') continue;

            $bytes = base64_decode($b64, true);
            if (!$bytes) {
                Log::warning('Sync image decode failed', compact('propertyId', 'bucket') + ['index' => $i]);
                continue;
            }

            $sort = $item['sort_order'] ?? $i;
            $orig = $item['filename'] ?? "image-{$i}.jpg";
            $ext  = $this->extFromMime($item['mime'] ?? null, $orig);
            $stem = Str::slug(pathinfo($orig, PATHINFO_FILENAME)) ?: 'image';
            $path = "properties/{$propertyId}/{$bucket}/{$sort}-{$stem}.{$ext}";

            if (Storage::disk('public')->put($path, $bytes)) {
                $paths[] = $path;
            }
        }
        return $paths;
    }

    private function uniqueSlug(string $title, string $extId): string
    {
        $base      = (Str::slug($title) ?: 'property') . '-' . substr(sha1($extId), 0, 8);
        $candidate = $base;
        $i = 1;
        while (Property::withTrashed()
            ->where('slug', $candidate)
            ->where('external_id', '!=', $extId)
            ->exists()
        ) {
            $candidate = $base . '-' . (++$i);
        }
        return $candidate;
    }

    private function taxonomySlug(string $modelClass, string $name, array $scope = []): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $i    = 1;
        $q    = $modelClass::query();
        foreach ($scope as $k => $v) $q->where($k, $v);
        while ((clone $q)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }

    private function extFromMime(?string $mime, ?string $filename = null): string
    {
        $mime = strtolower((string) $mime);
        $byMime = match (true) {
            str_contains($mime, 'jpeg'), str_contains($mime, 'jpg') => 'jpg',
            str_contains($mime, 'png')  => 'png',
            str_contains($mime, 'webp') => 'webp',
            str_contains($mime, 'gif')  => 'gif',
            default => null,
        };
        if ($byMime) return $byMime;

        $fromFile = strtolower((string) pathinfo((string) $filename, PATHINFO_EXTENSION));
        return in_array($fromFile, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)
            ? ($fromFile === 'jpeg' ? 'jpg' : $fromFile)
            : 'jpg';
    }

    private function clean(?string $v): ?string
    {
        if ($v === null) return null;
        $v = trim($v);
        return $v === '' ? null : $v;
    }

    private function stripBytesForLog(array $body): array
    {
        foreach (array_keys(self::IMAGE_BUCKETS) as $bucket) {
            if (!empty($body[$bucket]) && is_array($body[$bucket])) {
                foreach ($body[$bucket] as $i => $item) {
                    if (isset($item['bytes'])) {
                        $body[$bucket][$i]['bytes'] = '<' . strlen((string) $item['bytes']) . ' base64 bytes>';
                    }
                }
            }
        }
        if (!empty($body['agent']['photo']['bytes'])) {
            $body['agent']['photo']['bytes'] = '<' . strlen((string) $body['agent']['photo']['bytes']) . ' base64 bytes>';
        }
        return $body;
    }

    private function fail(?string $extId, int $status, string $message, array $loggable, float $start): JsonResponse
    {
        $resp = ['ok' => false, 'error' => $message];
        $this->log('sync', $extId, $status, $start, $loggable, $resp, $message);
        return response()->json($resp, $status);
    }

    private function log(string $action, ?string $extId, int $status, float $start, array $body, array $resp, ?string $error = null): void
    {
        try {
            ListingSyncLog::create([
                'action'        => $action,
                'external_id'   => $extId,
                'status_code'   => $status,
                'latency_ms'    => (int) round((microtime(true) - $start) * 1000),
                'request_body'  => $body,
                'response_body' => $resp,
                'error'         => $error,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write listing_sync_log', ['error' => $e->getMessage()]);
        }
    }
}
