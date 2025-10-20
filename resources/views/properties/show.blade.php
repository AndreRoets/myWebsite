@extends('layouts.app')

@section('title', $property->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/property-show.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
@endpush

@section('content')
    <div class="property-show-page">
        <!-- LEFT COLUMN: Property Details -->
        <section class="property-details-main">

            @php
                // Build gallery from /public/storage/properties/{id} plus hero first
                $galleryImages = [];

                // 1) Hero image first (normalize to web path under /storage)
                if (!empty($property->hero_image)) {
                    $hero = str_replace('\\', '/', $property->hero_image);
                    // If only a filename was stored, prepend folder; if already has 'properties/', leave it
                    if (!str_starts_with($hero, 'properties/')) {
                        $hero = 'properties/' . $property->id . '/' . ltrim($hero, '/');
                    }
                    $galleryImages[] = $hero;
                }

                // 2) Read all images from /public/storage/properties/{id}
                $folder = public_path('storage/properties/' . $property->id);
                if (is_dir($folder)) {
                    $allowed = ['jpg','jpeg','png','webp','gif','bmp','avif'];
                    // scandir returns filenames on this folder
                    $files = @scandir($folder) ?: [];
                    foreach ($files as $f) {
                        if ($f === '.' || $f === '..') continue;
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        if (!in_array($ext, $allowed, true)) continue;

                        // Build web path like "properties/{id}/image.jpg"
                        $webPath = 'properties/' . $property->id . '/' . $f;

                        // Avoid duplicating hero if identical
                        if (!in_array($webPath, $galleryImages, true)) {
                            $galleryImages[] = $webPath;
                        }
                    }
                }

                // 3) Sort for stable order, keep hero first if present
                if (!empty($galleryImages)) {
                    $heroFirst = $galleryImages[0];
                    $rest = array_slice($galleryImages, 1);
                    sort($rest, SORT_NATURAL | SORT_FLAG_CASE);
                    $galleryImages = array_merge([$heroFirst], $rest);
                }
            @endphp

            {{-- Gallery Carousel --}}
            @if(count($galleryImages) > 0)
                <div class="carousel-container">
                    <div class="carousel-slides">
                        @foreach($galleryImages as $index => $img)
                            <div class="carousel-slide @if($index === 0) active @endif">
                                {{-- assets are under /public/storage/... --}}
                                <img src="{{ asset('storage/' . $img) }}" alt="Property image {{ $index + 1 }}">
                            </div>
                        @endforeach
                        <button class="carousel-control prev" aria-label="Previous">&lsaquo;</button>
                        <button class="carousel-control next" aria-label="Next">&rsaquo;</button>
                    </div>
                </div>
            @endif

            <div class="content-container">
                <!-- Title and Price Row -->
                <div class="title-price-row">
                    <div>
                        <div class="location">{{ $property->suburb }}, {{ $property->city }}</div>
                        <h1 class="property-title">{{ $property->title }}</h1>
                        <div class="price">R {{ number_format($property->price, 0, ',', ' ') }}</div>
                    </div>
                </div>
                <div class="summary-text">
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
                            <p class="stat-value">{{ number_format($property->floor_size, 0) }}</p>
                            <p class="stat-label">m²</p>
                        </div>
                    </div>
                </div>

                <hr class="divider">

                <!-- Description -->
                <div class="property-description">
                    <p>{{ $property->description }}</p>
                </div>
            </div>
        </section>

        <!-- RIGHT COLUMN: Sidebar -->
        <aside class="property-sidebar">

            <a href="mailto:info@hfcoastal.co.za?subject={{ urlencode('Enquiry: '.$property->title) }}" class="schedule-btn">
                Schedule a Private Viewing
            </a>

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
                    <img src="https://placehold.co/400x400/111827/FFFFFF?text=Interactive+Map" alt="Location Map Placeholder">
                </div>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.carousel-container').forEach(setupCarousel);
    });

    function setupCarousel(carousel) {
        const prevButton = carousel.querySelector('.carousel-control.prev');
        const nextButton = carousel.querySelector('.carousel-control.next');
        const slides = carousel.querySelectorAll('.carousel-slide');
        let currentIndex = 0;
        let autoPlayInterval;
        let inactivityTimer;
        const slideSpeed = 5000; // 5 seconds per slide
        const inactivityTimeout = 10000; // Resume after 10s of inactivity

        if (slides.length <= 1) {
            if (prevButton) prevButton.style.display = 'none';
            if (nextButton) nextButton.style.display = 'none';
            return;
        }

        const showSlide = (index) => {
            if (index === currentIndex && slides[index].classList.contains('active')) return;
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
            currentIndex = index;
        };

        const prevSlide = () => {
            const newIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(newIndex);
        };

        const nextSlide = () => {
            const newIndex = (currentIndex + 1) % slides.length;
            showSlide(newIndex);
        };

        const stopSlideshow = () => {
            clearInterval(autoPlayInterval);
            clearTimeout(inactivityTimer);
        };

        const startSlideshow = () => {
            stopSlideshow(); // Ensure no multiple intervals are running
            autoPlayInterval = setInterval(nextSlide, slideSpeed);
        };

        // Manual navigation
        prevButton.addEventListener('click', () => {
            prevSlide();
            stopSlideshow(); // Stop autoplay on manual interaction
            inactivityTimer = setTimeout(startSlideshow, inactivityTimeout); // Resume after a delay
        });

        nextButton.addEventListener('click', () => {
            nextSlide();
            stopSlideshow();
            inactivityTimer = setTimeout(startSlideshow, inactivityTimeout);
        });

        // Auto-play with pause on hover
        carousel.addEventListener('mouseenter', stopSlideshow);
        carousel.addEventListener('mouseleave', startSlideshow);

        startSlideshow(); // Start the slideshow initially
    }
</script>
@endpush
