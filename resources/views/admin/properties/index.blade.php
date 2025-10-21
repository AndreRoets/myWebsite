@extends('admin.layouts.app')

@section('title', 'Manage Properties')
@section('page-title', 'Manage Properties')

@section('content')
    @push('styles')
    {{-- Page-specific styles can go here if needed --}}
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