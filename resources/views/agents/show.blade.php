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

    <h2 class="page-header__title" style="text-align: left; margin-bottom: 2rem;">Properties Listed by {{ $agent->name }}</h2>

    <div class="properties-grid">
        @forelse($agent->properties as $property)
            <a href="{{ route('properties.show', $property) }}" class="property-card">
                <img src="{{ $property->hero_image ? asset('storage/' . $property->hero_image) : asset('images/property-placeholder.jpg') }}" alt="{{ $property->title }}">
                <div class="property-card-content">
                    <h3 class="property-card-title">{{ $property->title }}</h3>
                    <p class="property-card-price">{{ $property->display_price }}</p>
                </div>
            </a>
        @empty
            <p>This agent currently has no properties listed.</p>
        @endforelse
    </div>
</div>
@endsection