@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <div class="profile-card">
        <h1>My Profile</h1>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Joined:</strong> {{ $user->created_at->format('F j, Y') }}</p>

        <div class="profile-actions">
            {{-- <a href="#" class="btn">Edit Profile</a> --}}
            <a href="{{ route('logout') }}" class="btn btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="saved-searches-section" style="margin-top: 3rem;">
        <h2>My Saved Searches</h2>
        <a href="{{ route('properties.search') }}" class="btn btn-primary">Advanced Property Search</a>

        @if (session('status'))
            <div class="alert alert-success" style="margin-top: 1rem;">
                {{ session('status') }}
            </div>
        @endif

        @if ($savedSearches->isEmpty())
            <p>You have no saved searches yet.</p>
        @else
            <div class="saved-searches-list">
                @foreach ($savedSearches as $search)
                    <div class="saved-search-item">
                        <h3>{{ $search->name }}</h3>
                        <div class="search-filters">
                            <strong>Filters:</strong>
                            @foreach($search->filters as $key => $value)
                                @if($value)
                                    <span class="filter-tag">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                                @endif
                            @endforeach
                        </div>
                        <div class="search-item-actions">
                            <a href="{{ route('saved-searches.execute', $search) }}" class="btn btn-info">Re-run</a>
                            <form action="{{ route('saved-searches.destroy', $search) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
.profile-card {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: 2rem auto;
}
.profile-card h1 {
    margin-top: 0;
    border-bottom: 1px solid #eee;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}
.profile-actions {
    margin-top: 1.5rem;
    text-align: right;
}
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s;
    cursor: pointer;
}
.btn-logout {
    background-color: #e53e3e; /* A red color for logout */
    color: #fff;
}
.btn-logout:hover {
    background-color: #c53030;
}
.btn-primary {
    background-color: #007bff;
    color: #fff;
}
.btn-primary:hover {
    background-color: #0056b3;
}
.btn-info {
    background-color: #17a2b8;
    color: #fff;
}
.btn-info:hover {
    background-color: #117a8b;
}
.btn-warning {
    background-color: #ffc107;
    color: #212529;
}
.btn-warning:hover {
    background-color: #e0a800;
}
.btn-danger {
    background-color: #dc3545;
    color: #fff;
}
.btn-danger:hover {
    background-color: #bd2130;
}
.btn-success {
    background-color: #28a745;
    color: #fff;
}
.btn-success:hover {
    background-color: #218838;
}
.alert.alert-success {
    background-color: #d4edda;
    color: #155724;
    padding: .75rem 1.25rem;
    border: 1px solid #c3e6cb;
    border-radius: .25rem;
}

.saved-searches-section {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    max-width: 900px;
    margin: 2rem auto;
}
.saved-searches-section h2 {
    margin-top: 0;
    border-bottom: 1px solid #eee;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}
.saved-search-item {
    border: 1px solid #eee;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 5px;
}
.saved-search-item h3 {
    margin-top: 0;
    margin-bottom: 0.5rem;
}
.search-filters {
    margin-bottom: 1rem;
    color: #666;
}
.filter-tag {
    display: inline-block;
    background-color: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    margin-top: 0.25rem;
}

.search-item-actions button, .search-item-actions a {
    margin-right: 0.5rem;
}

/* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 800px;
    border-radius: 8px;
    position: relative;
}
.close-button {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    right: 20px;
    top: 10px;
}
.close-button:hover,
.close-button:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>


@endsection