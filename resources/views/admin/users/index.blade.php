@extends('admin.layouts.app')

@section('title', 'Manage Users')
@section('page-title', 'Manage Users')

@section('content')
    @push('styles')
    <style>
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
        .pagination { margin-top: 1.5rem; }
    </style>
    @endpush

    <div>
        <div class="table-container">
            @if($users->isEmpty())
                <p>No users found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="actions">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection