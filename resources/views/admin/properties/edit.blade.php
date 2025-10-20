<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property: {{ $property->title }}</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.25rem; }
        input, select, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .error { color: red; font-size: 0.875rem; }
        button { padding: 0.75rem 1.5rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .container { max-width: 800px; margin: auto; }
        h1 { margin-bottom: 2rem; }
        .flex-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .image-preview { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem; }
        .image-preview img { max-width: 150px; height: auto; border: 1px solid #ddd; padding: 2px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Property: {{ $property->title }}</h1>

        @if (session('ok'))
            <div style="color: green; margin-bottom: 1rem;">{{ session('ok') }}</div>
        @endif

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
                <input type="file" id="hero" name="hero" accept="image/webp,image/jpeg,image/png">
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

            <button type="submit">Update Property</button>
        </form>
    </div>
</body>
</html>