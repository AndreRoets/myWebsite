@extends('layouts.app')

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
    {{-- Gallery --}}
    @php
      // Ensure $images is an array even if null or stored as JSON string/Collection
      $imagesRaw = $property->images ?? [];
      if (is_string($imagesRaw)) {
          $decoded = json_decode($imagesRaw, true);
          $images = is_array($decoded) ? $decoded : [];
      } elseif ($imagesRaw instanceof \Illuminate\Support\Collection) {
          $images = $imagesRaw->all();
      } else {
          $images = is_array($imagesRaw) ? $imagesRaw : [];
      }
    @endphp

    @if(count($images))
      <div class="gallery" aria-label="Property gallery">
        @foreach($images as $img)
          <img src="{{ asset('storage/'.$img) }}" alt="{{ $property->title }}">
        @endforeach
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
@endsection
