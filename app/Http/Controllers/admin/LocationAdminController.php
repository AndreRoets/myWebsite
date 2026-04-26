<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Region;
use App\Models\Suburb;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationAdminController extends Controller
{
    private array $models = [
        'provinces' => Province::class,
        'regions'   => Region::class,
        'towns'     => Town::class,
        'suburbs'   => Suburb::class,
    ];

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'provinces');
        if (!isset($this->models[$tab])) $tab = 'provinces';

        $search   = trim($request->query('q', ''));
        $parentId = $request->query('parent');

        $query = $this->models[$tab]::query()->withCount('properties');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($parentId && $tab !== 'provinces') {
            $parentKey = match ($tab) {
                'regions' => 'province_id',
                'towns'   => 'region_id',
                'suburbs' => 'town_id',
            };
            $query->where($parentKey, $parentId);
        }

        // eager load parents for display
        $with = match ($tab) {
            'regions' => ['province'],
            'towns'   => ['region.province'],
            'suburbs' => ['town.region.province'],
            default   => [],
        };
        if ($with) $query->with($with);

        $items = $query->orderBy('name')->paginate(25)->withQueryString();

        $parents = match ($tab) {
            'regions' => Province::orderBy('name')->get(),
            'towns'   => Region::with('province')->orderBy('name')->get(),
            'suburbs' => Town::with('region.province')->orderBy('name')->get(),
            default   => collect(),
        };

        return view('admin.settings.locations.index', compact('tab', 'items', 'search', 'parentId', 'parents'));
    }

    public function store(Request $request)
    {
        $tab = $request->input('tab', 'provinces');
        abort_unless(isset($this->models[$tab]), 404);

        $rules = ['name' => ['required', 'string', 'max:255']];
        $parentField = $this->parentField($tab);
        if ($parentField) $rules[$parentField] = ['required', 'integer'];
        if ($tab === 'suburbs') {
            $rules['postal_code'] = ['nullable', 'string', 'max:20'];
            $rules['latitude']    = ['nullable', 'numeric'];
            $rules['longitude']   = ['nullable', 'numeric'];
        }
        $data = $request->validate($rules);
        $data['slug'] = $this->uniqueSlug($this->models[$tab], $data['name'],
            $parentField ? [$parentField => $data[$parentField]] : []);
        $data['created_via'] = 'manual';

        $this->models[$tab]::create($data);

        return redirect()->route('admin.settings.locations.index', ['tab' => $tab])
            ->with('success', ucfirst(Str::singular($tab)) . ' created.');
    }

    public function update(Request $request, string $tab, int $id)
    {
        abort_unless(isset($this->models[$tab]), 404);
        $model = $this->models[$tab]::findOrFail($id);

        $rules = ['name' => ['required', 'string', 'max:255']];
        if ($tab === 'suburbs') {
            $rules['postal_code'] = ['nullable', 'string', 'max:20'];
            $rules['latitude']    = ['nullable', 'numeric'];
            $rules['longitude']   = ['nullable', 'numeric'];
        }
        $data = $request->validate($rules);

        if ($model->name !== $data['name']) {
            $parentField = $this->parentField($tab);
            $data['slug'] = $this->uniqueSlug($this->models[$tab], $data['name'],
                $parentField ? [$parentField => $model->{$parentField}] : [], $model->id);
        }

        $model->update($data);

        return redirect()->route('admin.settings.locations.index', ['tab' => $tab])
            ->with('success', 'Updated.');
    }

    public function toggle(Request $request, string $tab, int $id)
    {
        abort_unless(isset($this->models[$tab]), 404);
        $model = $this->models[$tab]::findOrFail($id);
        $model->update(['is_active' => !$model->is_active]);
        return back()->with('success', 'Status toggled.');
    }

    public function destroy(string $tab, int $id)
    {
        abort_unless(isset($this->models[$tab]), 404);
        $model = $this->models[$tab]::findOrFail($id);
        $model->update(['is_active' => false]);
        return back()->with('success', 'Deactivated.');
    }

    public function merge(Request $request, string $tab)
    {
        abort_unless(isset($this->models[$tab]), 404);
        $data = $request->validate([
            'source_id' => ['required', 'integer', 'different:target_id'],
            'target_id' => ['required', 'integer'],
        ]);

        $cls    = $this->models[$tab];
        $source = $cls::findOrFail($data['source_id']);
        $target = $cls::findOrFail($data['target_id']);

        $fkOnProperty = match ($tab) {
            'provinces' => 'province_id',
            'regions'   => 'region_id',
            'towns'     => 'town_id',
            'suburbs'   => 'suburb_id',
        };

        \App\Models\Property::where($fkOnProperty, $source->id)->update([$fkOnProperty => $target->id]);

        // Re-parent direct children to the target as well, if applicable.
        $childRelation = match ($tab) {
            'provinces' => ['model' => Region::class, 'fk' => 'province_id'],
            'regions'   => ['model' => Town::class,   'fk' => 'region_id'],
            'towns'     => ['model' => Suburb::class, 'fk' => 'town_id'],
            default     => null,
        };
        if ($childRelation) {
            $childRelation['model']::where($childRelation['fk'], $source->id)
                ->update([$childRelation['fk'] => $target->id]);
        }

        $source->delete();

        return back()->with('success', 'Merged ' . $source->name . ' into ' . $target->name . '.');
    }

    private function parentField(string $tab): ?string
    {
        return match ($tab) {
            'regions' => 'province_id',
            'towns'   => 'region_id',
            'suburbs' => 'town_id',
            default   => null,
        };
    }

    private function uniqueSlug(string $modelClass, string $name, array $scope = [], ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $i = 1;
        while (true) {
            $q = $modelClass::query()->where('slug', $slug);
            foreach ($scope as $k => $v) $q->where($k, $v);
            if ($ignoreId) $q->where('id', '!=', $ignoreId);
            if (!$q->exists()) return $slug;
            $slug = $base . '-' . (++$i);
        }
    }
}
