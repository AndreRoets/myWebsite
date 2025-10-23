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
}
.btn-logout {
    background-color: #e53e3e; /* A red color for logout */
    color: #fff;
}
.btn-logout:hover {
    background-color: #c53030;
}
</style>
@endsection