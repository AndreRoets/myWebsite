@extends('layouts.app')

@section('title', $listing->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/property-show.css') }}">
    <style>
        .image-group { margin-bottom: 2rem; }
        .image-group h2 { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.75rem; color: #555; text-transform: uppercase; letter-spacing: 0.05em; }
        .image-group-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 0.75rem; }
        .image-group-grid img { width: 100%; height: 200px; object-fit: cover; border-radius: 6px; display: block; }
    </style>
@endpush

@section('content')
<div class="property-show-page">

    <section class="property-details-main">

        @php
            $imageGroups = [
                'Dawn'    => $listing->dawn_images    ?? [],
                'Day'     => $listing->noon_images    ?? [],
                'Dusk'    => $listing->dusk_images    ?? [],
                'Gallery' => $listing->gallery_images ?? [],
            ];
            // Fallback: if all groups are empty, use images_json as "Gallery"
            $anyGroup = array_filter($imageGroups, fn($g) => !empty($g));
            if (empty($anyGroup)) {
                $imageGroups = ['Gallery' => $listing->images_json ?? []];
            }

            // Helper to extract URL from an image entry (string or array)
            $imgUrl = fn($img) => is_string($img) ? $img : ($img['url'] ?? $img['path'] ?? null);
        @endphp

        {{-- Primary hero carousel: first non-empty group --}}
        @php
            $heroImages = [];
            foreach ($imageGroups as $groupImages) {
                if (!empty($groupImages)) { $heroImages = $groupImages; break; }
            }
        @endphp

        @if(!empty($heroImages))
            <div class="carousel-container">
                <div class="carousel-slides">
                    @foreach($heroImages as $index => $img)
                        @php $url = $imgUrl($img); @endphp
                        @if($url)
                            <div class="carousel-slide @if($index === 0) active @endif">
                                <img src="{{ $url }}" alt="{{ $listing->title }} image {{ $index + 1 }}">
                            </div>
                        @endif
                    @endforeach
                    <button class="carousel-control prev" aria-label="Previous">&lsaquo;</button>
                    <button class="carousel-control next" aria-label="Next">&rsaquo;</button>
                </div>
            </div>
        @endif

        <div class="content-container">
            <div class="title-price-row">
                <div>
                    <div class="location">
                        {{ $listing->suburb }}{{ $listing->region ? ', ' . $listing->region : '' }}
                    </div>
                    <h1 class="property-title">{{ $listing->title }}</h1>
                    @if($listing->price)
                        <div class="price">R {{ number_format($listing->price, 0, ',', ' ') }}</div>
                    @endif
                </div>
            </div>

            <hr class="divider">

            <div class="property-stats">
                @if($listing->beds)
                    <div class="stat-item">
                        <i data-lucide="bed-double"></i>
                        <div>
                            <p class="stat-value">{{ $listing->beds }}</p>
                            <p class="stat-label">Beds</p>
                        </div>
                    </div>
                @endif
                @if($listing->baths)
                    <div class="stat-item">
                        <i data-lucide="bath"></i>
                        <div>
                            <p class="stat-value">{{ $listing->baths }}</p>
                            <p class="stat-label">Baths</p>
                        </div>
                    </div>
                @endif
                @if($listing->size_m2)
                    <div class="stat-item">
                        <i data-lucide="ruler"></i>
                        <div>
                            <p class="stat-value">{{ number_format($listing->size_m2, 0) }}</p>
                            <p class="stat-label">m²</p>
                        </div>
                    </div>
                @endif
            </div>

            @if($listing->excerpt)
                <hr class="divider">
                <div class="property-description">
                    <p><em>{{ $listing->excerpt }}</em></p>
                </div>
            @endif

            @if($listing->description)
                <hr class="divider">
                <div class="property-description">
                    <p>{{ $listing->description }}</p>
                </div>
            @endif

            {{-- Additional image groups (all groups except the hero one shown above) --}}
            @php
                $shownFirst = false;
            @endphp
            @foreach($imageGroups as $label => $groupImages)
                @if(!empty($groupImages))
                    @if(!$shownFirst)
                        @php $shownFirst = true; @endphp
                        @continue
                    @endif
                    <hr class="divider">
                    <div class="image-group">
                        <h2>{{ $label }} Photos</h2>
                        <div class="image-group-grid">
                            @foreach($groupImages as $img)
                                @php $url = $imgUrl($img); @endphp
                                @if($url)
                                    <img src="{{ $url }}" alt="{{ $label }} photo of {{ $listing->title }}">
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    <aside class="property-sidebar">
        <div class="sidebar-header">
            <i data-lucide="home"></i>
        </div>

        @php
            $agent = $listing->agent_json;
        @endphp

        @if(!empty($agent))
            <div class="agent-sidebar-card">
                @if(!empty($agent['image']))
                    <div class="agent-image-wrapper">
                        <img src="{{ $agent['image'] }}" alt="{{ $agent['name'] ?? 'Agent' }}">
                    </div>
                @endif
                <div class="agent-details">
                    @if(!empty($agent['name']))
                        <h4 class="agent-name">{{ $agent['name'] }}</h4>
                    @endif
                    @if(!empty($agent['title']))
                        <p class="agent-title">{{ $agent['title'] }}</p>
                    @endif
                    <div class="agent-contact-links">
                        @if(!empty($agent['email']))
                            <a href="mailto:{{ $agent['email'] }}?subject={{ urlencode('Enquiry: ' . $listing->title) }}">
                                <i data-lucide="mail" style="width:16px;height:16px;vertical-align:middle;margin-right:8px;"></i> Email Agent
                            </a>
                        @endif
                        @if(!empty($agent['phone']))
                            <a href="tel:{{ $agent['phone'] }}">
                                <i data-lucide="phone" style="width:16px;height:16px;vertical-align:middle;margin-right:8px;"></i> Call Agent
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <a href="mailto:info@hfcoastal.co.za?subject={{ urlencode('Enquiry: ' . $listing->title) }}" class="schedule-btn">
                Enquire about this property
            </a>
        @endif
    </aside>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    document.addEventListener('DOMContentLoaded', function () {
        const carousel = document.querySelector('.carousel-container');
        if (!carousel) return;

        const slides = carousel.querySelectorAll('.carousel-slide');
        if (slides.length <= 1) return;

        let current = 0;
        const show = (i) => {
            slides.forEach((s, idx) => s.classList.toggle('active', idx === i));
            current = i;
        };

        carousel.querySelector('.carousel-control.prev')
            .addEventListener('click', () => show((current - 1 + slides.length) % slides.length));
        carousel.querySelector('.carousel-control.next')
            .addEventListener('click', () => show((current + 1) % slides.length));

        setInterval(() => show((current + 1) % slides.length), 5000);
    });
</script>
@endpush
