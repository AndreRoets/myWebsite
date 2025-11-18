@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="container profile-page">

    @if (session('status'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid #c3e6cb; border-radius: .25rem;">
            {{ session('status') }}
        </div>
    @endif

    <h1 class="page-title">Your Personal Hub</h1>
    <p class="page-subtitle">Manage your details and saved property searches.</p>

    <div class="profile-grid">

        {{-- Left Sidebar: User Details --}}
        <aside class="user-details-card">
            <div class="user-avatar">
                <span class="initials">{{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->surname, 0, 1)) }}</span>
            </div>
            <h2 class="user-name">{{ $user->name }} {{ $user->surname }}</h2>
            <p class="user-email">{{ $user->email }}</p>

            @if($user->isApproved())
                <span class="user-status is-approved">
                    <i data-lucide="check-circle" style="width: 14px; height: 14px; vertical-align: text-top;"></i>
                    Approved User
                </span>
            @else
                <span class="user-status is-pending">
                    <i data-lucide="clock" style="width: 14px; height: 14px; vertical-align: text-top;"></i>
                    Approval Pending
                </span>
            @endif

            {{-- Profile Actions --}}
            <div class="profile-actions" style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">

                {{-- NEW REQUIRED BUTTON --}}
                <button id="edit-info-btn" class="btn-profile" style="width: 100%;">
                    <i class="fas fa-user-edit"></i>
                    <span>Edit Personal Info</span>
                </button>

                {{-- LOGOUT BUTTON --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="width: 100%; margin: 0;">
                    @csrf
                    <button type="submit" class="btn-profile btn-profile-logout" style="width: 100%;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>

            </div>
        </aside>

        {{-- Right Column --}}
        <main class="profile-main-content">

            {{-- Saved Searches Section --}}
            <section class="content-section">
                <div class="section-header">
                    <div class="section-header-title">
                        <i data-lucide="save"></i>
                        <h2>Saved Searches</h2>
                    </div>
                    <a href="{{ route('properties.search') }}" class="btn btn-sm btn-outline-primary">New Search</a>
                </div>

                @if($savedSearches->isNotEmpty())
                    <ul class="saved-searches-list">
                        @foreach($savedSearches as $search)
                            <li class="saved-search-item">
                                <div class="search-details">
                                    <p class="search-name">{{ $search->name }}</p>
                                    <small class="text-muted">Saved on {{ $search->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="search-actions">
                                    <a href="{{ route('saved-searches.execute', $search) }}" class="btn btn-sm btn-outline-primary">View Results</a>
                                    <form action="{{ route('saved-searches.destroy', $search) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this saved search?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state">
                        <p>You have no saved searches yet.</p>
                        <a href="{{ route('properties.search') }}" class="btn btn-primary">Start a New Search</a>
                    </div>
                @endif
            </section>

        </main>
    </div>
</div>

{{-- NEW CUSTOM MODAL YOU ASKED TO ADD --}}
<div id="edit-info-modal" class="modal-backdrop" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-address-card"></i> Edit Personal Information</h2>
            <button class="modal-close" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="modal_name">Name</label>
                    <input type="text" id="modal_name" name="name" class="form-control" value="{{ auth()->user()->name }}">
                    @error('name')
                        <div class="text-danger" style="font-size: 0.875em; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="modal_surname">Surname</label>
                    <input type="text" id="modal_surname" name="surname" class="form-control" value="{{ auth()->user()->surname }}">
                    @error('surname')
                        <div class="text-danger" style="font-size: 0.875em; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn-profile" style="border-color: var(--gold-accent); color: var(--light-gold);">
                        <i class="fas fa-save"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/profile-actions.js') }}"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    // Custom modal toggle script
    const editBtn = document.getElementById('edit-info-btn');
    const customModal = document.getElementById('edit-info-modal');
    const closeModal = customModal.querySelector('.modal-close');

    @if($errors->any())
        // If there are validation errors, show the modal on page load.
        customModal.style.display = 'flex';
    @endif

    editBtn.addEventListener('click', () => {
        customModal.style.display = 'flex';
    });

    closeModal.addEventListener('click', () => {
        customModal.style.display = 'none';
    });

    document.addEventListener('click', e => {
        if (e.target === customModal) customModal.style.display = 'none';
    });

    // URL Status Update Message
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('status') && urlParams.get('status') === 'search-updated') {
            const alertDiv = document.querySelector('.alert');
            if (alertDiv) {
                alertDiv.textContent = 'Search updated successfully!';
            }
        }
    });
</script>
@endpush
