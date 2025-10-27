@extends('admin.layouts.app')

@section('title', 'Edit Property')
@section('page-title', 'Edit Property: ' . $property->title)

@section('content')
    @push('styles')
    {{-- Page-specific styles can go here if needed --}}
    @endpush

    <a href="{{ route('admin.properties.list') }}" class="back-link">&larr; Back to All Properties</a>

        @if ($errors->any())
            <div style="color: red; margin-bottom: 1rem;">
                <strong>Whoops! Something went wrong.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $property->title) }}" required>
            </div>

            <div class="form-group">
                <label for="agent_id">Agent</label>
                <select name="agent_id" id="agent_id" class="form-control @error('agent_id') is-invalid @enderror" required>
                    <option value="">Select an Agent</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @selected(old('agent_id', $property->agent_id) == $agent->id)>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
                @error('agent_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex-grid">
                <div class="form-group">
                    <label for="price">Price (ZAR)</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $property->price) }}">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="for_sale" @selected(old('status', $property->status) == 'for_sale')>For Sale</option>
                        <option value="for_rent" @selected(old('status', $property->status) == 'for_rent')>For Rent</option>
                        <option value="sold" @selected(old('status', $property->status) == 'sold')>Sold</option>
                        <option value="rented" @selected(old('status', $property->status) == 'rented')>Rented</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        <option value="house" @selected(old('type', $property->type) == 'house')>House</option>
                        <option value="apartment" @selected(old('type', $property->type) == 'apartment')>Apartment</option>
                        <option value="townhouse" @selected(old('type', $property->type) == 'townhouse')>Townhouse</option>
                        <option value="vacant_land" @selected(old('type', $property->type) == 'vacant_land')>Vacant Land</option>
                        <option value="commercial" @selected(old('type', $property->type) == 'commercial')>Commercial</option>
                    </select>
                </div>
            </div>

            <div class="flex-grid">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $property->city) }}">
                </div>
                <div class="form-group">
                    <label for="suburb">Suburb</label>
                    <input type="text" id="suburb" name="suburb" value="{{ old('suburb', $property->suburb) }}">
                </div>
            </div>

            <div class="flex-grid">
                <div class="form-group">
                    <label for="bedrooms">Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}">
                </div>
                <div class="form-group">
                    <label for="bathrooms">Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}">
                </div>
                <div class="form-group">
                    <label for="garages">Garages</label>
                    <input type="number" id="garages" name="garages" value="{{ old('garages', $property->garages) }}">
                </div>
                <div class="form-group">
                    <label for="floor_size">Floor Size (m²)</label>
                    <input type="number" id="floor_size" name="floor_size" value="{{ old('floor_size', $property->floor_size) }}">
                </div>
                <div class="form-group">
                    <label for="erf_size">Erf Size (m²)</label>
                    <input type="number" id="erf_size" name="erf_size" value="{{ old('erf_size', $property->erf_size) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt (Short Summary)</label>
                <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $property->excerpt) }}</textarea>
            </div>

            <div class="form-group">
                <label for="description">Full Description</label>
                <textarea id="description" name="description" rows="8">{{ old('description', $property->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="hero">Hero Image (Main Picture)</label>
                @if($property->hero_image)
                    <div class="image-preview">
                        <img src="{{ asset('storage/' . $property->hero_image) }}" alt="Hero Image" style="max-width: 200px;">
                    </div>
                @endif
                <input type="file" id="hero_image" name="hero_image" accept="image/webp,image/jpeg,image/png">
            </div>

            <div class="form-group">
                <label for="images">Image Gallery (Select multiple to add more)</label>
                @if($property->images)
                    <div class="image-preview">
                        @foreach($property->images as $image)
                            <img src="{{ asset('storage/' . $image) }}" alt="Gallery Image">
                        @endforeach
                    </div>
                @endif
                <input type="file" id="images" name="images[]" multiple accept="image/webp,image/jpeg,image/png">
            </div>

            @include('admin.properties._form-visibility')

            <button type="submit">Update Property</button>
        </form>
@endsection