@extends('admin.layouts.app')

@section('title', 'Create New Property')
@section('page-title', 'Create New Property')

@section('content')
    @push('styles')
    {{-- Page-specific styles can go here if needed --}}
    @endpush
    
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

        <a href="{{ route('admin.properties.list') }}" class="back-link">&larr; Back to All Properties</a>

        <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required class="@error('title') is-invalid @enderror">
                @error('title')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="agent_id">Agent</label>
                <select name="agent_id" id="agent_id" class="form-control @error('agent_id') is-invalid @enderror" required>
                    <option value="">Select an Agent</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @selected(old('agent_id') == $agent->id)>
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
                    <input type="number" id="price" name="price" value="{{ old('price') }}" class="@error('price') is-invalid @enderror">
                    @error('price')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" class="@error('type') is-invalid @enderror">
                        <option value="house" @selected(old('type') == 'house')>House</option>
                        <option value="apartment" @selected(old('type') == 'apartment')>Apartment</option>
                        <option value="townhouse" @selected(old('type') == 'townhouse')>Townhouse</option>
                        <option value="vacant_land" @selected(old('type') == 'vacant_land')>Vacant Land</option>
                        <option value="commercial" @selected(old('type') == 'commercial')>Commercial</option>
                    </select>
                    @error('type')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex-grid">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" class="@error('city') is-invalid @enderror">
                    @error('city')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="suburb">Suburb</label>
                    <input type="text" id="suburb" name="suburb" value="{{ old('suburb') }}" class="@error('suburb') is-invalid @enderror">
                    @error('suburb')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex-grid">
                <div class="form-group">
                    <label for="bedrooms">Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" value="{{ old('bedrooms') }}" class="@error('bedrooms') is-invalid @enderror">
                    @error('bedrooms')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="bathrooms">Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms" value="{{ old('bathrooms') }}" class="@error('bathrooms') is-invalid @enderror">
                    @error('bathrooms')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="garages">Garages</label>
                    <input type="number" id="garages" name="garages" value="{{ old('garages') }}" class="@error('garages') is-invalid @enderror">
                    @error('garages')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="floor_size">Floor Size (m²)</label>
                    <input type="number" id="floor_size" name="floor_size" value="{{ old('floor_size') }}" class="@error('floor_size') is-invalid @enderror">
                    @error('floor_size')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="erf_size">Erf Size (m²)</label>
                    <input type="number" id="erf_size" name="erf_size" value="{{ old('erf_size') }}" class="@error('erf_size') is-invalid @enderror">
                    @error('erf_size')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt (Short Summary)</label>
                <textarea id="excerpt" name="excerpt" rows="3" class="@error('excerpt') is-invalid @enderror">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Full Description</label>
                <textarea id="description" name="description" rows="8" class="@error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="hero">Hero Image (Main Picture)</label>
                <input type="file" id="hero" name="hero_image" accept="image/webp,image/jpeg,image/png" class="@error('hero_image') is-invalid @enderror">
                @error('hero')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="images">Image Gallery (Select multiple)</label>
                <input type="file" id="images" name="images[]" multiple accept="image/webp,image/jpeg,image/png" class="@error('images') is-invalid @enderror">
                @error('images')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit">Create Property</button>
        </form>
@endsection