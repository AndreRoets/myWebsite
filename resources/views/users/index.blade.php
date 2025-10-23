@extends('layouts.app')

@section('title', 'Our Users')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <h1 style="text-align: center; margin-bottom: 2rem; font-family: 'Playfair Display', serif;">Our Community</h1>

    <div class="users-grid">
        @forelse($users as $user)
            <div class="user-card">
                <div class="user-card-content">
                    <h3>{{ $user->name }}</h3>
                    <p class="email">{{ $user->email }}</p>
                    <p class="joined">Joined: {{ $user->created_at->format('F j, Y') }}</p>
                </div>
            </div>
        @empty
            <p>No users have registered yet.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="pagination" style="margin-top:2rem;">
        {{ $users->links() }}
    </div>
</div>

<style>
.users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}
.user-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}
.user-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}
.user-card-content {
    padding: 1.5rem;
}
.user-card-content h3 {
    margin-top: 0;
    font-family: "Playfair Display", serif;
}
.user-card-content .email {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}
</style>
@endsection