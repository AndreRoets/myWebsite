@extends('layouts.app')

@section('title', $property->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/property-show.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
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

            <button class="schedule-btn">
                Schedule a Private Viewing
            </button>

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
