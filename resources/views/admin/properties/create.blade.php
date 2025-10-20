<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Property</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.25rem; }
        input, select, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .error { color: red; font-size: 0.875rem; }
        button { padding: 0.75rem 1.5rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .container { max-width: 800px; margin: auto; }
        input.is-invalid, select.is-invalid, textarea.is-invalid {
            border-color: red;
        }
        h1 { margin-bottom: 2rem; }
        .flex-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Property</h1>

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

        <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required class="@error('title') is-invalid @enderror">
                @error('title')
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
                    <label for="status">Status</label>
                    <select id="status" name="status" class="@error('status') is-invalid @enderror">
                        <option value="for_sale" @selected(old('status') == 'for_sale')>For Sale</option>
                        <option value="for_rent" @selected(old('status') == 'for_rent')>For Rent</option>
                        <option value="sold" @selected(old('status') == 'sold')>Sold</option>
                        <option value="rented" @selected(old('status') == 'rented')>Rented</option>
                    </select>
                    @error('status')
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
                    <input type="number" id="floor_size" name="floor_size" value="{{ old('floor_size') }}">
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
                <input type="file" id="hero" name="hero" accept="image/webp,image/jpeg,image/png" class="@error('hero') is-invalid @enderror">
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
    </div>
</body>
</html>