<style>
    .form-group { margin-bottom: 1.5rem; }
    label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    input[type="text"], input[type="email"], input[type="tel"], textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    textarea { min-height: 120px; resize: vertical; }
    .btn-submit { padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; color: white; background-color: #28a745; border: none; cursor: pointer; font-size: 1rem; }
    .btn-submit:hover { background-color: #218838; }
    .error-message { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
    .image-preview { margin-top: 1rem; }
    .image-preview img { max-width: 150px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd; }
    .image-note { font-size: 0.875rem; color: #6c757d; margin-top: 0.5rem; }
</style>

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

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $agent->name) }}" required>
    </div>

    <div class="form-group">
        <label for="title">Title / Role</label>
        <input type="text" id="title" name="title" value="{{ old('title', $agent->title) }}" required>
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email', $agent->email) }}" required>
    </div>

    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $agent->phone) }}" required>
    </div>

    <div class="form-group">
        <label for="description">Short Description</label>
        <textarea id="description" name="description">{{ old('description', $agent->description) }}</textarea>
    </div>

    <div class="form-group">
        <label for="image">Agent Image</label>
        <input type="file" id="image" name="image" accept="image/*">
        <p class="image-note">Recommended size: 400x400 pixels. Max file size: 2MB.</p>
        @if ($agent->image)
            <div class="image-preview">
                <p>Current Image:</p>
                <img src="{{ $agent->image_url }}" alt="Current image for {{ $agent->name }}">
            </div>
        @endif
    </div>

    <button type="submit" class="btn-submit">
        {{ $agent->exists ? 'Update Agent' : 'Create Agent' }}
    </button>
</form>