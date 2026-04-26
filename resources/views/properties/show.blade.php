@extends('layouts.app')

@section('title', $property->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/property-show.css') }}">
    <style>
        .gallery-switcher {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .gallery-switcher button {
            background: var(--navy-800);
            color: var(--gold-500);
            border: 1px solid var(--gold-500);
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s;
        }
        .gallery-switcher button.active, .gallery-switcher button:hover { background-color: var(--gold-500); color: var(--navy-900); }
        /* Agent Card Styles for Public Property Show Page Sidebar */
        .gallery-switcher {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .gallery-switcher button {
            background: var(--navy-800);
            color: var(--gold-500);
            border: 1px solid var(--gold-500);
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s;
        }
        .gallery-switcher button.active, .gallery-switcher button:hover { background-color: var(--gold-500); color: var(--navy-900); }
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
                 // Prepare image galleries using the new accessors
                 $heroImage = $property->hero_image ? [$property->hero_image] : [];
                 $dawnImages = $property->dawn_image;
                 $noonImages = $property->noon_image;
                 $duskImages = $property->dusk_image;
                 $generalImages = $property->general_images;
 
                 // Determine if we should show the time-based switcher
                 $hasTimeBasedGalleries = !empty($dawnImages) || !empty($noonImages) || !empty($duskImages);
 
                 // The default gallery is noon if it exists, otherwise dawn, then dusk, then general
                 $defaultGallery = $noonImages;
                 $defaultGalleryName = 'noon';
                 if (empty($defaultGallery)) { $defaultGallery = $dawnImages; $defaultGalleryName = 'dawn'; }
                 if (empty($defaultGallery)) { $defaultGallery = $duskImages; $defaultGalleryName = 'dusk'; }
                 if (empty($defaultGallery)) { $defaultGallery = $generalImages; $defaultGalleryName = 'general'; }
 
                 // Prepend hero image to the default gallery and ensure uniqueness
                 $initialGallery = $defaultGallery;
             @endphp
 
             {{-- Gallery Switcher Buttons --}}
             @if($hasTimeBasedGalleries)
                 <div class="gallery-switcher">
                     @if(!empty($dawnImages))
                         <button data-gallery="dawn" class="{{ $defaultGalleryName === 'dawn' ? 'active' : '' }}">Dawn</button>
                     @endif
                     @if(!empty($noonImages))
                         <button data-gallery="noon" class="{{ $defaultGalleryName === 'noon' ? 'active' : '' }}">Noon</button>
                     @endif
                     @if(!empty($duskImages))
                         <button data-gallery="dusk" class="{{ $defaultGalleryName === 'dusk' ? 'active' : '' }}">Dusk</button>
                     @endif
                 </div>
             @endif

            {{-- Gallery Carousel --}}
            @if(!empty($initialGallery))
                <div class="carousel-container">
                    <div class="carousel-slides">
                        @foreach($initialGallery as $index => $img)
                            <div class="carousel-slide @if($index === 0) active @endif">
                                @php $isFull = is_string($img) && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')); @endphp
                                <img src="{{ $isFull ? $img : asset('storage/' . $img) }}" alt="Property image {{ $index + 1 }}" data-gallery-image>
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
                        <div class="location">
                            @php
                                $crumbs = array_filter([$property->suburb, $property->town ?? $property->city, $property->region]);
                            @endphp
                            {{ implode(' › ', $crumbs) }}
                        </div>
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

                {{-- Features --}}
                @if(!empty($property->features_json))
                    <hr class="divider">
                    <div class="property-features">
                        <h3 style="margin-bottom:.75rem;">Features</h3>
                        <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
                            @foreach($property->features_json as $key => $value)
                                @php
                                    if (is_bool($value)) {
                                        if (!$value) continue;
                                        $label = Str::headline((string) $key);
                                    } elseif (is_array($value)) {
                                        $label = Str::headline((string) $key) . ': ' . implode(', ', array_map(fn($v) => is_scalar($v) ? (string) $v : '', $value));
                                    } else {
                                        $label = Str::headline((string) $key) . ': ' . $value;
                                    }
                                @endphp
                                <span style="background:var(--navy-800,#1a2845);color:var(--gold-500,#c0a87f);padding:.35rem .75rem;border-radius:999px;font-size:.85rem;border:1px solid rgba(192,168,127,.4);">{{ $label }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Video --}}
                @if($property->youtube_video_id)
                    <hr class="divider">
                    <div class="property-video">
                        <h3 style="margin-bottom:.75rem;">Video Tour</h3>
                        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                            <iframe src="https://www.youtube.com/embed/{{ $property->youtube_video_id }}"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
                                allowfullscreen loading="lazy" title="Video tour"></iframe>
                        </div>
                    </div>
                @endif

                {{-- Matterport --}}
                @if($property->matterport_id)
                    <hr class="divider">
                    <div class="property-matterport">
                        <h3 style="margin-bottom:.75rem;">3D Walkthrough</h3>
                        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
                            <iframe src="https://my.matterport.com/show/?m={{ $property->matterport_id }}"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
                                allowfullscreen loading="lazy" title="3D walkthrough"></iframe>
                        </div>
                    </div>
                @endif
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
                        <img src="{{ $property->agent->image_url ?: asset('Image/agent-placeholder.webp') }}" alt="{{ $property->agent->name ?? 'Agent' }}">
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

    // Store galleries in a JS variable
    const galleries = {
        dawn: @json($dawnImages),
        noon: @json($noonImages),
        dusk: @json($duskImages),
        general: @json($generalImages)
    };

    const assetBaseUrl = @json(asset('storage/'));

    // Helper to get full URL — synced images already include scheme + host.
    function getImageUrl(path) {
        if (typeof path === 'string' && (path.startsWith('http://') || path.startsWith('https://'))) {
            return path;
        }
        return `${assetBaseUrl}/${path}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const slideSpeed = 5000;
        const inactivityTimeout = 10000;
        let currentCarouselState = {}; // To hold state like intervals

        function setupCarousel(carousel) {
            // Clear any existing slideshow intervals
            if (currentCarouselState.autoPlayInterval) clearInterval(currentCarouselState.autoPlayInterval);
            if (currentCarouselState.inactivityTimer) clearTimeout(currentCarouselState.inactivityTimer);

            const prevButton = carousel.querySelector('.carousel-control.prev');
            const nextButton = carousel.querySelector('.carousel-control.next');
            const slides = carousel.querySelectorAll('.carousel-slide');
            let currentIndex = 0;

            // Detach old event listeners by cloning the buttons
            const newPrevButton = prevButton.cloneNode(true);
            const newNextButton = nextButton.cloneNode(true);
            prevButton.parentNode.replaceChild(newPrevButton, prevButton);
            nextButton.parentNode.replaceChild(newNextButton, nextButton);

            if (slides.length <= 1) {
                newPrevButton.style.display = 'none';
                newNextButton.style.display = 'none';
                return;
            } else {
                newPrevButton.style.display = 'block';
                newNextButton.style.display = 'block';
            }

            const showSlide = (index) => {
                if (index === currentIndex && slides[index]?.classList.contains('active')) return;
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
                clearInterval(currentCarouselState.autoPlayInterval);
                clearTimeout(currentCarouselState.inactivityTimer);
            };

            const startSlideshow = () => {
                stopSlideshow();
                currentCarouselState.autoPlayInterval = setInterval(nextSlide, slideSpeed);
            };

            // Attach events to the new buttons
            newPrevButton.addEventListener('click', () => {
                prevSlide();
                stopSlideshow();
                currentCarouselState.inactivityTimer = setTimeout(startSlideshow, inactivityTimeout);
            });

            newNextButton.addEventListener('click', () => {
                nextSlide();
                stopSlideshow();
                currentCarouselState.inactivityTimer = setTimeout(startSlideshow, inactivityTimeout);
            });

            // Auto-play with pause on hover
            carousel.addEventListener('mouseenter', stopSlideshow);
            carousel.addEventListener('mouseleave', startSlideshow);

            startSlideshow();
        }

        // --- Initial Setup ---
        const initialCarousel = document.querySelector('.carousel-container');
        if (initialCarousel) {
            setupCarousel(initialCarousel);
        }

        // --- Gallery Switcher Logic ---
        const switcherButtons = document.querySelectorAll('.gallery-switcher button');
        switcherButtons.forEach(button => {
            button.addEventListener('click', function() {
                const galleryName = this.dataset.gallery;
                let newImages = galleries[galleryName];

                // Normalize in case it's an object like { "0": "...", "2": "..." }
                if (!Array.isArray(newImages)) {
                    newImages = Object.values(newImages || {});
                }

                if (!newImages.length) return;

                // Update active button
                switcherButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Rebuild carousel slides
                const carousel = document.querySelector('.carousel-container');
                const slidesContainer = carousel.querySelector('.carousel-slides');
                slidesContainer.innerHTML = `
                    ${newImages.map((img, index) => `
                        <div class="carousel-slide ${index === 0 ? 'active' : ''}">
                            <img src="${getImageUrl(img)}" alt="Property image ${index + 1}" data-gallery-image>
                        </div>
                    `).join('')}
                    <button class="carousel-control prev" aria-label="Previous">&lsaquo;</button>
                    <button class="carousel-control next" aria-label="Next">&rsaquo;</button>
                `;

                // Re-initialize the carousel logic for the new slides
                setupCarousel(carousel);
            });
        });
    });
</script>
@endpush
