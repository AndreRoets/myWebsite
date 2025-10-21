@extends('admin.layouts.app')

@section('title', 'Manage Agents')
@section('page-title', 'Manage Agents')

@section('content')
    @push('styles')
    <style>
        .alert-success { background-color: #d4edda; color: #155724; padding: 1rem; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 1rem; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
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
        img { width: 60px; height: 60px; object-fit: cover; border-radius: 50%; }
    </style>
    @endpush

    <div>
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('admin.agents.create') }}" class="btn btn-create">Create New Agent</a>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Title</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agents as $agent)
                        <tr>
                            <td><img src="{{ $agent->image_url ?? asset('images/placeholder-agent.png') }}" alt="{{ $agent->name }}"></td>
                            <td>{{ $agent->name }}</td>
                            <td>{{ $agent->title }}</td>
                            <td>{{ $agent->email }}</td>
                            <td>{{ $agent->phone }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-primary">Edit</a>
                                <form action="{{ route('admin.agents.destroy', $agent) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this agent?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No agents found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $agents->links() }}</div>
    </div>
@endsection