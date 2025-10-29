@extends('layouts.app')

@section('title', $agent->name . ' - Agent Profile')

@push('styles')
    {{-- Reusing some styles from agents and properties pages for consistency --}}
    <link rel="stylesheet" href="{{ asset('css/agents.css') }}">
    <style>
        .agent-profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            margin-bottom: 3rem;
            padding: 2rem;
            background: var(--navy-800);
            border: 1px solid rgba(192, 168, 127, 0.35);
            text-align: center;
        }

        @media (min-width: 768px) {
            .agent-profile-header {
                flex-direction: row;
                text-align: left;
            }
        }

        .agent-profile-image img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--gold-500);
        }

        .agent-profile-info h1 {
            font-family: "Playfair Display", serif;
            font-size: 2.5rem;
            color: var(--text-100);
            margin: 0 0 0.25rem;
        }

        .agent-profile-info .title {
            font-size: 1rem;
            color: var(--gold-500);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 1rem;
        }

        .agent-profile-info .contact-details a {
            color: var(--text-300);
            text-decoration: none;
            margin-right: 1.5rem;
        }
        .agent-profile-info .contact-details a:hover {
            color: var(--gold-500);
        }

        .agent-profile-description {
            margin-top: 1.5rem;
            color: var(--text-300);
            max-width: 75ch;
        }

        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .property-card {
            background: var(--navy-800);
            border: 1px solid rgba(192, 168, 127, 0.2);
            color: var(--text-100);
            text-decoration: none;
        }
        .property-card img { width: 100%; height: 200px; object-fit: cover; }
        .property-card-content { padding: 1rem; }
        .property-card-title { font-family: "Playfair Display", serif; font-size: 1.25rem; margin: 0 0 0.5rem; }
        .property-card-price { color: var(--gold-500); font-weight: bold; }
    </style>
@endpush

@section('content')
<div class="agents-page-container">
    <div class="agent-profile-header">
        <div class="agent-profile-image">
            <img src="{{ $agent->image ? asset('storage/' . $agent->image) : asset('Image/agent-placeholder.webp') }}" alt="{{ $agent->name }}">
        </div>
        <div class="agent-profile-info">
            <h1>{{ $agent->name }}</h1>
            <p class="title">{{ $agent->title }}</p>
            <div class="contact-details">
                <a href="mailto:{{ $agent->email }}"><i class="fas fa-envelope"></i> {{ $agent->email }}</a>
                <a href="tel:{{ $agent->phone }}"><i class="fas fa-phone"></i> {{ $agent->phone }}</a>
            </div>
            @if($agent->description)
                <p class="agent-profile-description">{{ $agent->description }}</p>
            @endif
        </div>
    </div>

    <div class="agent-properties-section">
        <h2 class="agent-properties-heading">Properties Listed by {{ $agent->name }}</h2>

        <div class="agent-properties-grid">
            @forelse($agent->properties as $property)
            @php
                // A property is visually restricted if:
                // 1. It's not visible AND the user is a guest or not approved.
                // 2. It's exclusive AND the user is a guest.
                $isVisuallyRestricted = (!$property->is_visible && (!auth()->check() || !auth()->user()->isApproved()))
                                        || ($property->is_exclusive && !auth()->check());
                $isClickable = !$isVisuallyRestricted;
            @endphp

            @if($isClickable)
                <a class="property-card-link" href="{{ route('properties.show', $property) }}">
            @else
                <div class="property-card-link"> {{-- Non-clickable wrapper to maintain layout --}}
            @endif

                <article class="property-card @if($isVisuallyRestricted) is-restricted @endif">
                    <div class="property-image-container">
                        <div class="property-image" style="background-image: url('{{ $property->hero_image ? asset('storage/' . $property->hero_image) : asset('images/property-placeholder.jpg') }}'); {{ $isVisuallyRestricted ? 'filter: blur(8px);' : '' }}">
                            {{-- Status badge --}}
                            @if($property->status)
                                <span class="property-status-badge">{{ $property->status }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="property-card-content">
                        <h3>{{ $property->title }}</h3>

                        <p>{{ $property->address ?? $property->location ?? 'Address not available' }}</p>

                        <p>
                            {{ $property->bedrooms ?? '—' }} Bed ·
                            {{ $property->bathrooms ?? '—' }} Bath ·
                            {{ $property->garages ?? '—' }} Garage
                        </p>

                        <p class="price">{{ $property->display_price }}</p>

                        @if($isVisuallyRestricted)
                            <div class="restricted-cta" style="margin-top: 16px;">
                                @php
                                    $needsApproval = !$property->is_visible && (!auth()->check() || !auth()->user()->isApproved());
                                    $needsLogin = $property->is_exclusive && !auth()->check();
                                @endphp

                                @if ($needsApproval && $needsLogin)
                                    <span class="btn" style="background-color: #4a5568; border-color: #4a5568; color: #a0aec0; cursor: not-allowed;">Login as an approved user to view.</span>
                                @elseif ($needsApproval)
                                    <span class="btn" style="background-color: #4a5568; border-color: #4a5568; color: #a0aec0; cursor: not-allowed;">Only approved users can view this property.</span>
                                @elseif ($needsLogin)
                                    <span class="btn" style="background-color: #4a5568; border-color: #4a5568; color: #a0aec0; cursor: not-allowed;">Login to view this property.</span>
                                @else
                                    {{-- Fallback, though should not be reached if $isVisuallyRestricted is true --}}
                                    <span class="btn" style="background-color: #4a5568; border-color: #4a5568; color: #a0aec0; cursor: not-allowed;">Details Restricted</span>
                                @endif
                            </div>
                        @else
                            <span class="btn">View Details</span>
                        @endif
                    </div>
                </article>

            @if($isClickable)
                </a>
            @else
                </div>
            @endif
        @empty
            <p style="color: var(--text-300);">This agent currently has no properties listed that are visible to you.</p>
        @endforelse
        </div>
    </div>
</div>
@endsection