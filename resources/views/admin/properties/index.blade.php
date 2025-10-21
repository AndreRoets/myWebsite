@extends('admin.layouts.app')

@section('title', 'Manage Properties')
@section('page-title', 'Manage Properties')

@section('content')
    @push('styles')
    <style>
        .alert-success { background-color: #d4edda; color: #155724; padding: 1rem; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 1rem; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background-color: #e9ecef; }
        .actions { display: flex; gap: 0.5rem; }
        .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; color: white; border: none; cursor: pointer; font-size: 0.875rem; }
        .btn-primary { background-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-create { display: inline-block; margin-bottom: 1rem; background-color: #28a745; }
        .btn-create:hover { background-color: #218838; }
        .pagination { margin-top: 1.5rem; }
        img { max-width: 100px; height: auto; border-radius: 4px; }
    </style>
    @endpush

    @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('admin.properties.create') }}" class="btn btn-create">Create New Property</a>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($properties as $property)
                        <tr>
                            <td>
                                @if($property->hero_image)
                                    <img src="{{ asset('storage/' . $property->hero_image) }}" alt="{{ $property->title }}">
                                @else
                                    No Image
                                @endif
                            </td>
                            <td>{{ $property->title }}</td>
                            <td>R {{ number_format($property->price, 2) }}</td>
                            <td>{{ Str::title(str_replace('_', ' ', $property->status)) }}</td>
                            <td>{{ Str::title($property->type) }}</td>
                            <td>{{ $property->city }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-primary">Edit</a>
                                <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this property? This will delete all associated images.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No properties found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $properties->links() }}
        </div>
@endsection