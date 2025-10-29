@extends('layouts.app')

@section('title', 'Properties')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/propertypage.css') }}">
@endpush

@section('content')
<div class="container properties-page">
  <h1 class="page-title">Our Curated Collection</h1>

  @if (session('status'))
      <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid #c3e6cb; border-radius: .25rem;">
          {{ session('status') }}
      </div>
  @endif

  {{-- Filter Section --}}
  <form method="GET" class="filters" action="{{ route('properties.index') }}">
    <select name="type" aria-label="Property Type">
      <option value="">All types</option>
      @foreach (['house','apartment','townhouse','vacant_land','commercial'] as $t)
        <option value="{{ $t }}" @selected(request('type')===$t)>
          {{ ucfirst(str_replace('_', ' ', $t)) }}
        </option>
      @endforeach
    </select>

    <select name="beds" aria-label="Minimum Beds">
      <option value="">Any beds</option>
      @for($i=1;$i<=6;$i++)
        <option value="{{ $i }}" @selected(request('beds')==$i)>{{ $i }}+</option>
      @endfor
    </select>

    <input type="number" name="min" placeholder="Min price" value="{{ request('min') }}">
    <input type="number" name="max" placeholder="Max price" value="{{ request('max') }}">

    <button class="hero-btn" type="submit">Filter</button>

    @if(request()->hasAny(['type','beds','min','max']) && (request('type')||request('beds')||request('min')||request('max')))
      <a class="clear-link" href="{{ route('properties.index') }}">Clear</a>
    @endif
  </form>

  {{-- Property Cards --}}
  <div class="properties-grid">
    @forelse($properties as $p)
      @php
        // A property is visually restricted if:
        // 1. It's not visible AND the user is a guest or not approved.
        // 2. It's exclusive AND the user is a guest.
        $isVisuallyRestricted = (!$p->is_visible && (!auth()->check() || !auth()->user()->isApproved()))
                                || ($p->is_exclusive && !auth()->check());
        $isClickable = !$isVisuallyRestricted;
      @endphp

      @if($isClickable)
        <a href="{{ route('properties.show', $p) }}" class="property-card-link">
      @endif
      <div class="property-card @if($isVisuallyRestricted) is-restricted @endif" style="position: relative;">
        <div class="property-image"
             style="background-image:url('{{ $p->hero_image ? asset('storage/'.$p->hero_image) : asset('Image/category1.webp') }}');
                    {{ $isVisuallyRestricted ? 'filter: blur(12px); transform: scale(1.1);' : '' }}">
        </div>
        <div class="property-card-content">
          <h3>{{ $p->title ?? 'Untitled Property' }}</h3>

          @if(!is_null($p->price))
              <p class="price">R {{ number_format($p->price, 0, ',', ' ') }}</p>
          @endif

          <p>
            <strong>Beds:</strong> {{ $p->bedrooms ?? '—' }}
            &nbsp;•&nbsp;
            <strong>Baths:</strong> {{ $p->bathrooms ?? '—' }}
          </p>

          <p>
            <strong>Location:</strong>
            {{ trim(($p->suburb ?? '').($p->suburb && $p->city ? ', ' : '').($p->city ?? '—')) }}
          </p>

          @if($isVisuallyRestricted)
            <div class="restricted-cta" style="margin-top: 1rem; text-align: center;">
                @php
                    $needsApproval = !$p->is_visible && (!auth()->check() || !auth()->user()->isApproved());
                    $needsLogin = $p->is_exclusive && !auth()->check();
                @endphp

                @if ($needsApproval && $needsLogin)
                    <span class="btn" style="background-color: #6c757d; cursor: not-allowed;">Login as an approved user to view.</span>
                @elseif ($needsApproval)
                    <span class="btn" style="background-color: #6c757d; cursor: not-allowed;">Only approved users can view this property.</span>
                @elseif ($needsLogin)
                    <span class="btn" style="background-color: #6c757d; cursor: not-allowed;">Login to view this property.</span>
                @else
                    <span class="btn" style="background-color: #6c757d; cursor: not-allowed;">Details Restricted</span>
                @endif
            </div>
          @else
            @if(!empty($p->floor_size))
                <p><strong>Size:</strong> {{ $p->floor_size }} m²</p>
            @endif
            <span class="btn">View Details</span>
          @endif
        </div>
      </div>
      @if($isClickable)
        </a>
      @endif
    @empty
      <p>No properties found matching your filters.</p>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="pagination" style="margin-top:2rem;">
    {{ $properties->onEachSide(1)->links() }}
  </div>
</div>
@endsection
