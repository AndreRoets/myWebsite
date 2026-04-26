@extends('admin.layouts.app')

@section('title', 'Locations')
@section('page-title', 'Locations')

@push('styles')
<style>
    .alert-success { background: #d4edda; color: #155724; padding: .75rem 1rem; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 1rem; }
    .tabs { display: flex; gap: .25rem; border-bottom: 1px solid #dee2e6; margin-bottom: 1.25rem; }
    .tabs a { padding: .6rem 1rem; text-decoration: none; color: #495057; border-bottom: 2px solid transparent; }
    .tabs a.active { color: #007bff; border-bottom-color: #007bff; font-weight: 600; }
    .toolbar { display: flex; gap: .75rem; align-items: end; flex-wrap: wrap; margin-bottom: 1rem; }
    .toolbar input, .toolbar select { padding: .5rem; border: 1px solid #ccc; border-radius: 4px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: .6rem; border-bottom: 1px solid #dee2e6; text-align: left; vertical-align: middle; }
    th { background: #e9ecef; }
    .btn { padding: .35rem .7rem; border-radius: 4px; border: none; color: #fff; cursor: pointer; font-size: .8rem; text-decoration: none; display: inline-block; }
    .btn-primary { background: #007bff; }
    .btn-warn { background: #ffc107; color: #212529; }
    .btn-danger { background: #dc3545; }
    .btn-success { background: #28a745; }
    .badge { display: inline-block; padding: .15rem .5rem; border-radius: 10px; font-size: .7rem; }
    .badge-sync { background: #cce5ff; color: #004085; }
    .badge-off { background: #f8d7da; color: #721c24; }
    .badge-on  { background: #d4edda; color: #155724; }
    .row-actions { display: flex; gap: .35rem; flex-wrap: wrap; }
    details.create { margin-bottom: 1.25rem; padding: .75rem 1rem; background: #f8f9fa; border-radius: 4px; }
    details.create summary { cursor: pointer; font-weight: 600; }
    details.create form { margin-top: .75rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: .5rem; align-items: end; }
    details.create label { display: block; font-size: .85rem; }
    details.create input, details.create select { width: 100%; padding: .5rem; border: 1px solid #ccc; border-radius: 4px; }
    .inline-edit input { width: 100%; padding: .35rem; border: 1px solid #ccc; border-radius: 4px; }
</style>
@endpush

@section('content')
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-success" style="background:#f8d7da;color:#721c24;border-color:#f5c6cb;">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="tabs">
        @foreach(['provinces','regions','towns','suburbs'] as $t)
            <a href="{{ route('admin.settings.locations.index', ['tab' => $t]) }}" class="{{ $tab === $t ? 'active' : '' }}">
                {{ ucfirst($t) }}
            </a>
        @endforeach
    </div>

    <details class="create">
        <summary>+ New {{ Str::singular($tab) }}</summary>
        <form action="{{ route('admin.settings.locations.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tab" value="{{ $tab }}">

            <label>Name<input type="text" name="name" required></label>

            @if($tab === 'regions')
                <label>Province
                    <select name="province_id" required>
                        <option value="">—</option>
                        @foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                    </select>
                </label>
            @elseif($tab === 'towns')
                <label>Region
                    <select name="region_id" required>
                        <option value="">—</option>
                        @foreach($parents as $r)<option value="{{ $r->id }}">{{ $r->name }} ({{ $r->province?->name }})</option>@endforeach
                    </select>
                </label>
            @elseif($tab === 'suburbs')
                <label>Town
                    <select name="town_id" required>
                        <option value="">—</option>
                        @foreach($parents as $t2)<option value="{{ $t2->id }}">{{ $t2->name }} ({{ $t2->region?->name }})</option>@endforeach
                    </select>
                </label>
                <label>Postal code<input type="text" name="postal_code"></label>
                <label>Latitude<input type="number" step="any" name="latitude"></label>
                <label>Longitude<input type="number" step="any" name="longitude"></label>
            @endif

            <button type="submit" class="btn btn-success">Create</button>
        </form>
    </details>

    <form method="GET" class="toolbar">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <label>Search<br><input type="text" name="q" value="{{ $search }}" placeholder="Name…"></label>
        @if($tab !== 'provinces')
            <label>Parent<br>
                <select name="parent">
                    <option value="">All</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ (string) $parentId === (string) $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                            @if($tab === 'towns') ({{ $p->province?->name }})
                            @elseif($tab === 'suburbs') ({{ $p->region?->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </label>
        @endif
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                @if($tab !== 'provinces')<th>Parent</th>@endif
                <th>Properties</th>
                <th>Status</th>
                <th>Origin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>
                        <form class="inline-edit" action="{{ route('admin.settings.locations.update', [$tab, $item->id]) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $item->name }}">
                            @if($tab === 'suburbs')
                                <div style="display:flex;gap:.25rem;margin-top:.25rem;">
                                    <input type="text" name="postal_code" placeholder="postal" value="{{ $item->postal_code }}">
                                    <input type="number" step="any" name="latitude" placeholder="lat" value="{{ $item->latitude }}">
                                    <input type="number" step="any" name="longitude" placeholder="lng" value="{{ $item->longitude }}">
                                </div>
                            @endif
                            <button type="submit" class="btn btn-warn" style="margin-top:.25rem;">Save</button>
                        </form>
                    </td>
                    <td>{{ $item->slug }}</td>
                    @if($tab !== 'provinces')
                        <td>
                            @if($tab === 'regions') {{ $item->province?->name }}
                            @elseif($tab === 'towns') {{ $item->region?->name }} / {{ $item->region?->province?->name }}
                            @elseif($tab === 'suburbs') {{ $item->town?->name }} / {{ $item->town?->region?->name }}
                            @endif
                        </td>
                    @endif
                    <td>{{ $item->properties_count }}</td>
                    <td>
                        @if($item->is_active)
                            <span class="badge badge-on">Active</span>
                        @else
                            <span class="badge badge-off">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($item->created_via === 'sync')
                            <span class="badge badge-sync">Sync</span>
                        @else
                            <span class="badge">Manual</span>
                        @endif
                    </td>
                    <td>
                        <div class="row-actions">
                            <form action="{{ route('admin.settings.locations.toggle', [$tab, $item->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warn">{{ $item->is_active ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                            <form action="{{ route('admin.settings.locations.destroy', [$tab, $item->id]) }}" method="POST" onsubmit="return confirm('Soft-delete (mark inactive)?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No {{ $tab }} found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem;">{{ $items->links() }}</div>

    <details class="create" style="margin-top:1.5rem;">
        <summary>Merge duplicates</summary>
        <form action="{{ route('admin.settings.locations.merge', $tab) }}" method="POST">
            @csrf
            <label>Source (will be removed)
                <select name="source_id" required>
                    <option value="">—</option>
                    @foreach($items as $i)<option value="{{ $i->id }}">{{ $i->name }}</option>@endforeach
                </select>
            </label>
            <label>Target (kept)
                <select name="target_id" required>
                    <option value="">—</option>
                    @foreach($items as $i)<option value="{{ $i->id }}">{{ $i->name }}</option>@endforeach
                </select>
            </label>
            <button type="submit" class="btn btn-danger" onclick="return confirm('Merge: source will be deleted, properties and children re-pointed at target.');">Merge</button>
        </form>
    </details>
@endsection
