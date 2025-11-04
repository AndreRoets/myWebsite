@extends('layouts.app')

@section('title', 'Advanced Property Search')

@section('content')
<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <h1>Advanced Property Search</h1>

    <div class="advanced-search-form">
        <form id="propertySearchForm" action="{{ route('properties.results') }}" method="GET">
            <div class="form-group">
                <label for="property_type">Property Type:</label>
                <select id="property_type" name="property_type" class="form-control" value="{{ request('property_type') }}">
                    <option value="">Any</option>
                    <option value="house" @selected(request('property_type') === 'house')>House</option>
                    <option value="apartment" @selected(request('property_type') === 'apartment')>Apartment</option>
                    <option value="condo" @selected(request('property_type') === 'condo')>Condo</option>
                    <option value="land" @selected(request('property_type') === 'land')>Land</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price_min">Min Price:</label>
                <input type="number" id="price_min" name="price_min" class="form-control" placeholder="Min Price" value="{{ request('price_min') }}">
            </div>

            <div class="form-group">
                <label for="price_max">Max Price:</label>
                <input type="number" id="price_max" name="price_max" class="form-control" placeholder="Max Price" value="{{ request('price_max') }}">
            </div>

            <div class="form-group">
                <label for="bedrooms">Bedrooms:</label>
                <input type="number" id="bedrooms" name="bedrooms" class="form-control" min="0" placeholder="Any" value="{{ request('bedrooms') }}">
            </div>

            <div class="form-group">
                <label for="bathrooms">Bathrooms:</label>
                <input type="number" id="bathrooms" name="bathrooms" class="form-control" min="0" placeholder="Any" value="{{ request('bathrooms') }}">
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" class="form-control" placeholder="e.g., New York, London" value="{{ request('location') }}">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
</div>

<style>
.advanced-search-form .form-group {
    margin-bottom: 1rem;
}
.advanced-search-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.advanced-search-form .form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
.advanced-search-form .btn-primary {
    width: auto;
    padding: 0.75rem 1.5rem;
}
</style>
@endsection
