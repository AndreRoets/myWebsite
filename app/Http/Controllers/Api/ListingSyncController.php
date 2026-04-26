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
use Illuminate\Validation\ValidationException;

class ListingSyncController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $start  = microtime(true);
        $body   = $request->all();
        $extId  = $body['external_id'] ?? null;
        $loggable = $this->stripBytesForLog($body);

        try {
            $data = $request->validate([
                'external_id'    => ['required', 'string'],
                'title'          => ['required', 'string', 'max:500'],
                'excerpt'        => ['nullable', 'string'],
                'description'    => ['nullable', 'string'],
                'listing_type'   => ['nullable', 'string'],
                'mandate_type'   => ['nullable', 'string'],
                'status'         => ['required', 'string'],
                'price'          => ['required', 'integer', 'min:0'],
                'category'       => ['nullable', 'string'],
                'property_type'  => ['nullable', 'string'],

                'location'           => ['nullable', 'array'],
                'location.unit_number'   => ['nullable', 'string'],
                'location.street_number' => ['nullable', 'string'],
                'location.street_name'   => ['nullable', 'string'],
                'location.complex_name'  => ['nullable', 'string'],
                'location.suburb'        => ['nullable', 'string'],
                'location.town'          => ['nullable', 'string'],
                'location.city'          => ['nullable', 'string'],
                'location.region'        => ['nullable', 'string'],
                'location.province'      => ['nullable', 'string'],
                'location.postal_code'   => ['nullable', 'string'],
                'location.latitude'      => ['nullable', 'numeric'],
                'location.longitude'     => ['nullable', 'numeric'],

                'beds'           => ['nullable', 'integer'],
                'baths'          => ['nullable', 'numeric'],
                'garages'        => ['nullable', 'integer'],
                'size_m2'        => ['nullable', 'integer'],
                'erf_size_m2'    => ['nullable', 'integer'],

                'listed_date'    => ['nullable', 'date'],
                'expiry_date'    => ['nullable', 'date'],
                'published_at'   => ['nullable', 'date'],

                'images'              => ['nullable', 'array'],
                'images.*.bytes'      => ['nullable', 'string'],
                'images.*.mime'       => ['nullable', 'string'],
                'images.*.filename'   => ['nullable', 'string'],
                'images.*.sort_order' => ['nullable', 'integer'],

                'dawn_images'              => ['nullable', 'array'],
                'dawn_images.*.bytes'      => ['nullable', 'string'],
                'dawn_images.*.mime'       => ['nullable', 'string'],
                'dawn_images.*.filename'   => ['nullable', 'string'],
                'dawn_images.*.sort_order' => ['nullable', 'integer'],

                'noon_images'              => ['nullable', 'array'],
                'noon_images.*.bytes'      => ['nullable', 'string'],
                'noon_images.*.mime'       => ['nullable', 'string'],
                'noon_images.*.filename'   => ['nullable', 'string'],
                'noon_images.*.sort_order' => ['nullable', 'integer'],

                'dusk_images'              => ['nullable', 'array'],
                'dusk_images.*.bytes'      => ['nullable', 'string'],
                'dusk_images.*.mime'       => ['nullable', 'string'],
                'dusk_images.*.filename'   => ['nullable', 'string'],
                'dusk_images.*.sort_order' => ['nullable', 'integer'],

                'gallery_images'              => ['nullable', 'array'],
                'gallery_images.*.bytes'      => ['nullable', 'string'],
                'gallery_images.*.mime'       => ['nullable', 'string'],
                'gallery_images.*.filename'   => ['nullable', 'string'],
                'gallery_images.*.sort_order' => ['nullable', 'integer'],

                'youtube_video_id' => ['nullable', 'string'],
                'matterport_id'    => ['nullable', 'string'],

                'features'       => ['nullable', 'array'],

                'agent'              => ['nullable', 'array'],
                'agent.external_id'  => ['nullable', 'string'],
                'agent.name'         => ['nullable', 'string'],
                'agent.email'        => ['nullable', 'email'],
                'agent.phone'        => ['nullable', 'string'],
                'agent.bio'          => ['nullable', 'string'],
                'agent.photo'              => ['nullable', 'array'],
                'agent.photo.bytes'        => ['required_with:agent.photo', 'string'],
                'agent.photo.mime'         => ['nullable', 'string'],
                'agent.photo.filename'     => ['nullable', 'string'],

                'agency'             => ['nullable', 'array'],
            ]);
        } catch (ValidationException $e) {
            $resp = ['error' => 'validation', 'fields' => $e->errors()];
            $this->log('sync', $extId, 422, $start, $loggable, $resp);
            return response()->json($resp, 422);
        }

        try {
            $result = DB::transaction(function () use ($data) {
                $created = [];

                // Location taxonomy
                $loc = $data['location'] ?? [];
                [$province, $region, $town, $suburb, $locCreated] = $this->resolveLocation($loc);
                $created = array_merge($created, $locCreated);

                // Agent
                $agentInput = $data['agent'] ?? null;
                $agencyInput = $data['agency'] ?? null;
                $agent = null;
                if ($agentInput) {
                    [$agent, $agentCreated] = $this->resolveAgent($agentInput, $agencyInput);
                    if ($agentCreated) $created[] = 'agent';
                }

                // Property upsert by external_id
                $property = Property::withTrashed()->updateOrCreate(
                    ['external_id' => $data['external_id']],
                    [
                        'agent_id'      => $agent?->id,
                        'title'         => $data['title'],
                        'slug'          => $this->uniqueSlug($data['title'], $data['external_id']),
                        'excerpt'       => $data['excerpt'] ?? null,
                        'description'   => $data['description'] ?? null,
                        'price'         => $data['price'],
                        'status'        => $data['status'],
                        'type'          => $data['property_type'] ?? 'house',
                        'listing_type'  => $data['listing_type'] ?? null,
                        'mandate_type'  => $data['mandate_type'] ?? null,
                        'category'      => $data['category'] ?? null,

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

                        'bedrooms'      => $data['beds']        ?? null,
                        'bathrooms'     => isset($data['baths']) ? (int) $data['baths'] : null,
                        'garages'       => $data['garages']     ?? null,
                        'floor_size'    => $data['size_m2']     ?? null,
                        'erf_size'      => $data['erf_size_m2'] ?? null,

                        'listed_date'   => $data['listed_date']  ?? null,
                        'expiry_date'   => $data['expiry_date']  ?? null,
                        'published_at'  => $data['published_at'] ?? null,
                        'synced_at'     => now(),

                        // Image bucket paths are filled in below, after the property has an id.
                        'primary_images_json' => null,
                        'gallery_images_json' => null,
                        'dawn_images_json'    => null,
                        'noon_images_json'    => null,
                        'dusk_images_json'    => null,

                        'youtube_video_id' => $data['youtube_video_id'] ?? null,
                        'matterport_id'    => $data['matterport_id']    ?? null,

                        'features_json' => $data['features'] ?? null,
                        'agency_json'   => $agencyInput ?? null,

                        'is_visible'    => true,
                        'deleted_at'    => null,
                    ]
                );

                // Wipe existing image directory for this property (idempotent re-sync).
                $this->wipeImageDir($property->id);

                $bucketMap = [
                    'images'         => 'primary_images_json',
                    'gallery_images' => 'gallery_images_json',
                    'dawn_images'    => 'dawn_images_json',
                    'noon_images'    => 'noon_images_json',
                    'dusk_images'    => 'dusk_images_json',
                ];

                $totalSaved = 0;
                $writtenPaths = [];   // tracked so we can clean up on rollback
                $bucketPaths = [];

                foreach ($bucketMap as $payloadKey => $column) {
                    $items = $data[$payloadKey] ?? [];
                    if (!is_array($items) || empty($items)) {
                        $bucketPaths[$column] = null;
                        continue;
                    }
                    [$paths, $saved, $written] = $this->writeImageBucket($property->id, $payloadKey, $items);
                    $writtenPaths = array_merge($writtenPaths, $written);
                    $totalSaved  += $saved;
                    $bucketPaths[$column] = $paths ?: null;
                }

                try {
                    $property->update($bucketPaths);
                } catch (\Throwable $e) {
                    foreach ($writtenPaths as $p) Storage::disk('public')->delete($p);
                    throw $e;
                }

                return [
                    'property'      => $property,
                    'agent'         => $agent,
                    'suburb'        => $suburb,
                    'created'       => array_values(array_unique($created)),
                    'images_saved'  => $totalSaved,
                    'written_paths' => $writtenPaths,
                ];
            });

            $resp = [
                'ok'           => true,
                'property_id'  => $result['property']->id,
                'agent_id'     => $result['agent']?->id,
                'suburb_id'    => $result['suburb']?->id,
                'images_saved' => $result['images_saved'],
                'created'      => $result['created'],
            ];
            $this->log('sync', $extId, 200, $start, $loggable, $resp);
            return response()->json($resp);
        } catch (\Throwable $e) {
            Log::error('Listing sync failed', ['error' => $e->getMessage(), 'external_id' => $extId]);
            $resp = ['error' => 'server', 'message' => $e->getMessage()];
            $this->log('sync', $extId, 500, $start, $loggable, $resp, $e->getMessage());
            return response()->json($resp, 500);
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

    /**
     * @return array{0: ?Province, 1: ?Region, 2: ?Town, 3: ?Suburb, 4: array<string>}
     */
    private function resolveLocation(array $loc): array
    {
        $created = [];
        $provinceName = $this->clean($loc['province'] ?? null);
        $regionName   = $this->clean($loc['region']   ?? null);
        $townName     = $this->clean($loc['town']     ?? $loc['city'] ?? null);
        $suburbName   = $this->clean($loc['suburb']   ?? null);

        $province = $regionModel = $town = $suburb = null;

        if ($provinceName) {
            $province = Province::whereRaw('LOWER(name) = ?', [strtolower($provinceName)])->first();
            if (!$province) {
                $province = Province::create([
                    'name'        => $provinceName,
                    'slug'        => $this->uniqueTaxonomySlug(Province::class, $provinceName),
                    'created_via' => 'sync',
                ]);
                $created[] = 'province';
            }
        }

        if ($regionName && $province) {
            $regionModel = Region::where('province_id', $province->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($regionName)])->first();
            if (!$regionModel) {
                $regionModel = Region::create([
                    'province_id' => $province->id,
                    'name'        => $regionName,
                    'slug'        => $this->uniqueTaxonomySlug(Region::class, $regionName, ['province_id' => $province->id]),
                    'created_via' => 'sync',
                ]);
                $created[] = 'region';
            }
        }

        if ($townName && $regionModel) {
            $town = Town::where('region_id', $regionModel->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($townName)])->first();
            if (!$town) {
                $town = Town::create([
                    'region_id'   => $regionModel->id,
                    'name'        => $townName,
                    'slug'        => $this->uniqueTaxonomySlug(Town::class, $townName, ['region_id' => $regionModel->id]),
                    'created_via' => 'sync',
                ]);
                $created[] = 'town';
            }
        }

        if ($suburbName && $town) {
            $suburb = Suburb::where('town_id', $town->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($suburbName)])->first();
            if (!$suburb) {
                $suburb = Suburb::create([
                    'town_id'     => $town->id,
                    'name'        => $suburbName,
                    'slug'        => $this->uniqueTaxonomySlug(Suburb::class, $suburbName, ['town_id' => $town->id]),
                    'postal_code' => $loc['postal_code'] ?? null,
                    'latitude'    => $loc['latitude']    ?? null,
                    'longitude'   => $loc['longitude']   ?? null,
                    'created_via' => 'sync',
                ]);
                $created[] = 'suburb';
            }
        }

        return [$province, $regionModel, $town, $suburb, $created];
    }

    /**
     * @return array{0: Agent, 1: bool}
     */
    private function resolveAgent(array $input, ?array $agency): array
    {
        $created = false;
        $agent = null;

        if (!empty($input['external_id'])) {
            $agent = Agent::where('external_id', $input['external_id'])->first();
        }
        if (!$agent && !empty($input['email'])) {
            $agent = Agent::where('email', $input['email'])->first();
        }

        $attrs = [
            'external_id'        => $input['external_id'] ?? null,
            'name'               => $input['name']  ?? ($agent->name  ?? 'Unknown Agent'),
            'email'              => $input['email'] ?? ($agent->email ?? null),
            'phone'              => $input['phone'] ?? ($agent->phone ?? null),
            'bio'                => $input['bio']   ?? ($agent->bio   ?? null),
            'agency_external_id' => $agency['external_id'] ?? null,
            'agency_name'        => $agency['name']        ?? null,
            'agency_branch'      => $agency['branch']      ?? null,
        ];

        if (!$agent) {
            $agent = Agent::create(array_merge($attrs, [
                'title'       => 'Agent',
                'created_via' => 'sync',
            ]));
            $created = true;
        } else {
            $agent->fill($attrs)->save();
        }

        // Photo: spec says null → leave existing photo alone, present → replace.
        if (!empty($input['photo']) && is_array($input['photo']) && !empty($input['photo']['bytes'])) {
            $this->writeAgentPhoto($agent, $input['photo']);
        }

        return [$agent, $created];
    }

    private function writeAgentPhoto(Agent $agent, array $photo): void
    {
        $bytes = base64_decode($photo['bytes'], true);
        if ($bytes === false || $bytes === '') {
            Log::warning('Agent photo bytes invalid', ['agent_id' => $agent->id]);
            return;
        }

        $ext = $this->extFromMime($photo['mime'] ?? null, $photo['filename'] ?? null);
        $path = "agents/{$agent->id}.{$ext}";

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
            'photo_source_url' => 'embedded:' . sha1($bytes),
        ])->save();
    }

    /**
     * Decode + write one image bucket. Returns [paths[], saved count, all-paths-written-on-disk].
     *
     * @param array $items raw payload items: [{filename, mime, bytes, sort_order}]
     * @return array{0: array<string>, 1: int, 2: array<string>}
     */
    private function writeImageBucket(int $propertyId, string $bucket, array $items): array
    {
        // Sort by sort_order ascending; missing values go last but stable.
        usort($items, function ($a, $b) {
            $sa = $a['sort_order'] ?? PHP_INT_MAX;
            $sb = $b['sort_order'] ?? PHP_INT_MAX;
            return $sa <=> $sb;
        });

        $paths = [];
        $written = [];
        foreach ($items as $i => $item) {
            $b64 = $item['bytes'] ?? null;
            if (!$b64 || !is_string($b64)) {
                Log::warning('Sync image item missing bytes', ['property_id' => $propertyId, 'bucket' => $bucket, 'index' => $i]);
                continue;
            }
            $bytes = base64_decode($b64, true);
            if ($bytes === false || $bytes === '') {
                Log::warning('Sync image bytes failed to decode', ['property_id' => $propertyId, 'bucket' => $bucket, 'index' => $i]);
                continue;
            }

            $sort = $item['sort_order'] ?? $i;
            $orig = $item['filename'] ?? "image-{$i}.jpg";
            $ext  = $this->extFromMime($item['mime'] ?? null, $orig);
            $stem = Str::slug(pathinfo($orig, PATHINFO_FILENAME)) ?: 'image';
            $name = sprintf('%03d-%s.%s', $sort, $stem, $ext);
            $path = "properties/{$propertyId}/{$bucket}/{$name}";

            Storage::disk('public')->put($path, $bytes);
            $paths[]   = $path;
            $written[] = $path;
        }

        return [$paths, count($paths), $written];
    }

    private function wipeImageDir(int $propertyId): void
    {
        $dir = "properties/{$propertyId}";
        if (Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->deleteDirectory($dir);
        }
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

    /**
     * Return a copy of the request body with binary `bytes` fields replaced by
     * their length, so the listing_sync_logs table doesn't balloon.
     */
    private function stripBytesForLog(array $body): array
    {
        foreach (['images', 'dawn_images', 'noon_images', 'dusk_images', 'gallery_images'] as $bucket) {
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

    private function clean(?string $v): ?string
    {
        if ($v === null) return null;
        $v = trim($v);
        return $v === '' ? null : $v;
    }

    private function uniqueSlug(string $title, string $extId): string
    {
        $base = Str::slug($title) ?: 'property';
        $slug = $base . '-' . substr(sha1($extId), 0, 8);
        // Property::updateOrCreate uses external_id as key, so the slug only needs to differ from
        // any *other* property's slug.
        $i = 1;
        $candidate = $slug;
        while (Property::withTrashed()
            ->where('slug', $candidate)
            ->where('external_id', '!=', $extId)
            ->exists()) {
            $candidate = $slug . '-' . (++$i);
        }
        return $candidate;
    }

    private function uniqueTaxonomySlug(string $modelClass, string $name, array $scope = []): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $i = 1;
        $query = $modelClass::query();
        foreach ($scope as $k => $v) $query->where($k, $v);
        while ((clone $query)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
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
