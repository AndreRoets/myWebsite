@extends('layouts.app')

@section('title', $property->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/property-show.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Agent Card Styles for Public Property Show Page Sidebar */
        .agent-sidebar-card {
            background: var(--navy-900); /* Darker background for contrast */
            border: 1px solid rgba(192, 168, 127, 0.35); /* Gold accent border */
            padding: 1.5rem;
            margin-top: 1.5rem; /* Space below sidebar header */
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .agent-sidebar-card .agent-image-wrapper {
            width: 100px; /* Fixed size for agent image */
            height: 100px;
            border-radius: 50%; /* Circular image */
            overflow: hidden;
            margin-bottom: 1rem;
            border: 2px solid var(--gold-500); /* Accent border */
            flex-shrink: 0;
        }
        .agent-sidebar-card .agent-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .agent-sidebar-card .agent-name {
            font-family: "Playfair Display", serif;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
            color: var(--text-100);
        }
        .agent-sidebar-card .agent-title {
            font-size: 0.85rem;
            color: var(--gold-500);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 1rem;
        }
        .agent-sidebar-card .agent-contact-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
            margin-top: 1rem;
        }
        .agent-sidebar-card .agent-contact-links a {
            display: block;
            padding: 10px 15px;
            border: 1px solid var(--gold-500);
            color: var(--gold-500);
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        .agent-sidebar-card .agent-contact-links a:hover {
            background-color: var(--gold-500);
            color: var(--navy-900);
        }
        .agent-sidebar-card .agent-description {
            font-size: 0.9rem;
            color: var(--text-300);
            margin-top: 1rem;
            text-align: left;
        }
    </style>
@endpush

@section('content')
    <main class="property-show-page">

        <!-- LEFT COLUMN: Property Details -->
        <section class="property-details-main">
            
            <!-- Hero Image -->
            <div class="property-hero-image">
                <img src="{{ asset('storage/' . $property->hero_image) }}" 
                     alt="{{ $property->name }}" 
                     class="w-full h-full object-cover opacity-70">
                <div class="gradient-overlay"></div>
            </div>

            <div class="content-container">
                <!-- Title and Price Row -->
                <div class="title-price-row">
                    <div>
                        <p class="location">{{ $property->location }}</p>
                        <h1 class="property-title">
                            {{ strtoupper($property->name) }}
                        </h1>
                        <p class="price">
                            €{{ number_format($property->price, 0, ',', ',') }}
                        </p>
                    </div>
                    <p class="summary-text">
                        Discover luxury living and unparalleled coastal serenity with this exclusive listing.
                    </p>
                </div>
                
                <hr class="divider">

                <!-- Property Stats -->
                <div class="property-stats">
                    <div class="stat-item">
                        <i data-lucide="bed-double"></i>
                        <div>
                            <p class="stat-value">{{ $property->bedrooms }}</p>
                            <p class="stat-label">Beds</p>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <i data-lucide="bath"></i>
                        <div>
                            <p class="stat-value">{{ $property->bathrooms }}</p>
                            <p class="stat-label">Baths</p>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <i data-lucide="ruler"></i>
                        <div>
                            <p class="stat-value">{{ number_format($property->square_feet, 0) }}</p>
                            <p class="stat-label">SQ FT</p>
                        </div>
                    </div>
                </div>

                <hr class="divider">

                <!-- Description -->
                <div class="property-description">
                    <p class="mb-4">
                        {{ $property->description }}
                    </p>
                </div>

            </div>

        </section>

        <!-- RIGHT COLUMN: Sidebar -->
        <aside class="property-sidebar">
            
            <div class="sidebar-header">
                <i data-lucide="home"></i>
            </div>

            {{-- Agent Information Section --}}
            @if($property->agent)
                <div class="agent-sidebar-card">
                    <div class="agent-image-wrapper">
                        <img src="{{ $property->agent->image_url ?? asset('Image/agent-placeholder.webp') }}" alt="{{ $property->agent->name ?? 'Agent' }}">
                    </div>
                    <div class="agent-details">
                        <h4 class="agent-name">{{ $property->agent->name }}</h4>
                        <p class="agent-title">{{ $property->agent->title }}</p>
                        <div class="agent-contact-links">
                            <a href="mailto:{{ $property->agent->email }}">
                                <i data-lucide="mail" style="width:18px; height:18px; vertical-align: middle; margin-right: 5px;"></i> Email {{ $property->agent->name }}
                            </a>
                            <a href="tel:{{ $property->agent->phone }}">
                                <i data-lucide="phone" style="width:18px; height:18px; vertical-align: middle; margin-right: 5px;"></i> Call {{ $property->agent->name }}
                            </a>
                        </div>
                        @if($property->agent->description)
                            <p class="agent-description">{{ $property->agent->description }}</p>
                        @endif
                        <a href="{{ route('agents.show', $property->agent) }}" class="agent-contact-links" style="margin-top: 1.5rem;">
                            <i data-lucide="user" style="width:18px; height:18px; vertical-align: middle; margin-right: 5px;"></i> View Agent Profile
                        </a>
                    </div>
                </div>
            @else
                <button class="schedule-btn">Schedule a Private Viewing</button>
            @endif

            <!-- Media Links -->
            <div class="media-links">
                
                <div class="media-link-item">
                    <p class="media-label">Virtual Tour</p>
                    <div class="media-card">
                        <div class="media-thumbnail">
                            <i data-lucide="play"></i>
                        </div>
                        <p class="media-title">3D Interactive Model</p>
                    </div>
                </div>

                <div class="media-link-item">
                    <p class="media-label">Video Walkthrough</p>
                    <div class="media-card">
                        <div class="media-thumbnail">
                            <i data-lucide="video"></i>
                        </div>
                        <p class="media-title">Cinematic Property Film</p>
                    </div>
                </div>
            </div>

            <!-- Map Placeholder -->
            <div class="map-container">
                <p class="map-label">Location Map</p>
                <div class="map-placeholder">
                    <img src="https://placehold.co/400x400/111827/FFFFFF?text=Interactive+Map" 
                         alt="Location Map Placeholder">
                </div>
            </div>

        </aside>

    </main>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
