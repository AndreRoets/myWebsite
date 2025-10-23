@extends('layouts.app')

@section('title', $property->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/property-show.css') }}">
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
            margin-bottom: 0.75rem;
        }
    </style>
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

            <div class="sidebar-header">
                <i data-lucide="home"></i>
            </div>

            {{-- Agent Information Section --}}
            @if($property->agent)
                <div class="agent-sidebar-card">
                    <div class="agent-image-wrapper">
                        <img src="{{ $property->agent->image ? asset('storage/' . $property->agent->image) : asset('Image/agent-placeholder.webp') }}" alt="{{ $property->agent->name ?? 'Agent' }}">
                    </div>
                    <div class="agent-details">
                        <h4 class="agent-name">{{ $property->agent->name }}</h4>
                        <p class="agent-title">{{ $property->agent->title }}</p>
                        <div class="agent-contact-links">
                            <a href="mailto:{{ $property->agent->email }}?subject={{ urlencode('Enquiry: '.$property->title) }}">
                                <i data-lucide="mail" style="width:16px; height:16px; vertical-align: middle; margin-right: 8px;"></i> Email Agent
                            </a>
                            <a href="tel:{{ $property->agent->phone }}">
                                <i data-lucide="phone" style="width:16px; height:16px; vertical-align: middle; margin-right: 8px;"></i> Call Agent
                            </a>
                            <a href="{{ route('agents.show', $property->agent) }}" style="text-align: center;">
                                <i data-lucide="user" style="width:16px; height:16px; vertical-align: middle; margin-right: 8px;"></i> View Profile
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <a href="mailto:info@hfcoastal.co.za?subject={{ urlencode('Enquiry: '.$property->title) }}" class="schedule-btn">Enquire about this property</a>
            @endif

        </aside>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
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
