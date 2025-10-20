@extends('layouts.app')

@push('styles')
<style>
    /* New Gallery Styles */
    .gallery-container {
        width: 100%;
        max-width: 900px;
        margin: 2rem auto;
    }
    .gallery-main-image {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        background-color: #0e111d;
    }
    .gallery-main-image img {
        width: 100%;
        display: block;
        aspect-ratio: 16 / 9;
        object-fit: cover;
        transition: opacity 0.3s ease-in-out;
    }
    .gallery-thumbnails {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
        justify-content: center;
    }
    .thumbnail-item {
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 6px;
        overflow: hidden;
        transition: border-color 0.2s ease;
        width: 120px; /* Adjust size as needed */
        height: 67.5px; /* 16:9 aspect ratio */
    }
    .thumbnail-item:hover {
        border-color: #aaa;
    }
    .thumbnail-item.active {
        border-color: #007bff; /* Active thumbnail highlight */
    }
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
</style>
@endpush

@section('title', $property->title)

@section('content')
<div class="container property-show">
  {{-- HERO --}}
  <div class="detail-hero" style="background-image:url('{{ $property->hero_image ? asset('storage/'.$property->hero_image) : asset('Image/Hero.webp') }}')">
    <div class="overlay">
      <h1 class="title">{{ $property->title }}</h1>
      @if(!empty($property->display_status))
        <div class="pill">{{ $property->display_status }}</div>
      @endif
      <div class="price">
        @if($property->price)
          R {{ number_format($property->price, 0, ',', ' ') }}
        @elseif(!empty($property->display_price))
          {{ $property->display_price }}
        @else
          Price on request
        @endif
      </div>
      <div class="loc">{{ $property->suburb }}, {{ $property->city }}</div>
    </div>
  </div>

  {{-- BODY --}}
  <div class="detail-body">
    @php
        // 1. Get the hero image path, normalized.
        $heroWebPath = null;
        if (!empty($property->hero_image)) {
            $heroWebPath = str_replace('\\', '/', $property->hero_image);
            if (!str_starts_with($heroWebPath, 'properties/')) {
                $heroWebPath = 'properties/' . $property->id . '/' . ltrim($heroWebPath, '/');
            }
        }

        // 2. Scan the directory for all image files.
        $allImageFiles = [];
        $directoryPath = public_path('storage/properties/' . $property->id);
        if (is_dir($directoryPath)) {
            // Use glob to get only image files, which is safer than scandir + extension check.
            $files = glob($directoryPath . '/*.{jpg,jpeg,png,gif,webp,avif,bmp}', GLOB_BRACE | GLOB_NOSORT);
            if ($files) {
                // Create web paths and sort them
                $allImageFiles = array_map(fn($file) => 'properties/' . $property->id . '/' . basename($file), $files);
                sort($allImageFiles, SORT_NATURAL | SORT_FLAG_CASE);
            }
        }

        // 3. Build the final gallery array.
        $galleryImages = [];
        if ($heroWebPath) {
            // Add hero first.
            $galleryImages[] = $heroWebPath;
        }
        // Add all other images from the directory, ensuring no duplicates.
        foreach ($allImageFiles as $imageFile) {
            if ($imageFile !== $heroWebPath) {
                $galleryImages[] = $imageFile;
            }
        }
    @endphp

    {{-- New Thumbnail Gallery --}}
    @if(count($galleryImages) > 0)
        <div class="gallery-container">
            {{-- Main Image Display --}}
            <div class="gallery-main-image">
                <img src="{{ asset('storage/' . $galleryImages[0]) }}" alt="Property image 1" id="main-gallery-image">
            </div>

            {{-- Thumbnails --}}
            @if(count($galleryImages) > 1)
                <div class="gallery-thumbnails">
                    @foreach($galleryImages as $index => $img)
                        <img src="{{ asset('storage/' . $img) }}"
                             alt="Property image thumbnail {{ $index + 1 }}"
                             class="thumbnail-item @if($index === 0) active @endif">
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Specs --}}
    <div class="specs">
      @if($property->bedrooms)<div><strong>{{ $property->bedrooms }}</strong> Bedrooms</div>@endif
      @if($property->bathrooms)<div><strong>{{ $property->bathrooms }}</strong> Bathrooms</div>@endif
      @if($property->garages)<div><strong>{{ $property->garages }}</strong> Garages</div>@endif
      @if($property->floor_size)<div>{{ $property->floor_size }} m² Floor</div>@endif
      @if($property->erf_size)<div>{{ $property->erf_size }} m² Erf</div>@endif
      @if(!empty($property->display_type))<div>Type: {{ $property->display_type }}</div>@endif
      @if($property->reference)<div>Ref: {{ $property->reference }}</div>@endif
    </div>

    {{-- Description --}}
    @if(!empty($property->description))
      <div class="description">
        {!! nl2br(e($property->description)) !!}
      </div>
    @endif

    {{-- Simple CTA (optional) --}}
    <div class="cta">
      <a class="hero-btn" href="mailto:info@hfcoastal.co.za?subject={{ urlencode('Enquiry: '.$property->title) }}">Enquire about this property</a>
      <a class="ghost-btn" href="{{ route('properties.index') }}">← Back to listings</a>
    </div>
  </div>

  {{-- RELATED --}}
  @if($related->isNotEmpty())
    <h3 class="related-heading">Related properties</h3>
    <div class="property-grid small">
      @foreach($related as $p)
        <a href="{{ route('properties.show', $p) }}" class="property-card" aria-label="View {{ $p->title }}">
          <div class="property-image" style="background-image:url('{{ $p->hero_image ? asset('storage/'.$p->hero_image) : asset('Image/category2.webp') }}')"></div>
          <div class="property-info">
            <h4>{{ $p->title }}</h4>
            <p class="price">
              @if($p->price)
                R {{ number_format($p->price, 0, ',', ' ') }}
              @elseif(!empty($p->display_price))
                {{ $p->display_price }}
              @else
                Price on request
              @endif
            </p>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>

@if(!empty($galleryImages))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('main-gallery-image');
    const thumbnails = document.querySelectorAll('.thumbnail-item');

    if (!mainImage || thumbnails.length <= 1) {
        return;
    }

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Update the main image source and alt text
            mainImage.style.opacity = 0; // Fade out
            setTimeout(() => {
                mainImage.src = this.src;
                mainImage.alt = this.alt.replace('thumbnail', '').trim();
                mainImage.style.opacity = 1; // Fade in
            }, 300);

            // Update the active thumbnail
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endif
@endsection
