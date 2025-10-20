@extends('layouts.app')

@section('title', 'Properties')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/propertypage.css') }}">
@endpush

@section('content')
<div class="collection-section">
  <h1 class="page-title">Available Properties</h1>

  {{-- Filter Section --}}
  <form method="GET" class="filters">
    <select name="type">
      <option value="">All types</option>
      @foreach (['house','apartment','townhouse','vacant_land','commercial'] as $t)
        <option value="{{ $t }}" @selected(request('type')===$t)>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
      @endforeach
    </select>
    <select name="beds">
      <option value="">Any beds</option>
      @for($i=1;$i<=6;$i++)
        <option value="{{ $i }}" @selected(request('beds')==$i)>{{ $i }}+</option>
      @endfor
    </select>
    <input type="number" name="min" placeholder="Min price" value="{{ request('min') }}">
    <input type="number" name="max" placeholder="Max price" value="{{ request('max') }}">
    <button class="hero-btn">Filter</button>
  </form>

  {{-- Property Cards --}}
  <div class="property-grid">
    @forelse($properties as $p)
      <div class="property-card">
        <div class="property-image-container">
            <img src="{{ $p->hero_image ? asset('storage/'.$p->hero_image) : asset('Image/category1.webp') }}" alt="{{ $p->title }}">
        </div>
        <div class="property-details">
            <h3 class="property-title">{{ $p->title }}</h3>
            <p class="property-price">R {{ number_format($p->price, 0, ',', ' ') }}</p>
            <div class="property-specs">
                <span><strong>Beds:</strong> {{ $p->bedrooms ?? '—' }}</span> • 
                <span><strong>Baths:</strong> {{ $p->bathrooms ?? '—' }}</span> • 
                <span><strong>Size:</strong> {{ $p->floor_size ?? 'N/A' }} m²</span>
            </div>
        </div>
        <a href="{{ route('properties.show', $p) }}" class="view-details-button">View Details</a>
      </div>
    @empty
      <p class="empty-state">No properties found matching your criteria.</p>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="pagination">
    {{ $properties->links() }}
  </div>
</div>
@endsection