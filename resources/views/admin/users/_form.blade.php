@push('styles')
<style>
    .form-container { max-width: 800px; margin: auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-group input, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; }
    .form-group .error-message { color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; }
    .btn-submit { background-color: #28a745; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
    .btn-submit:hover { background-color: #218838; }
    .form-check { display: flex; align-items: center; gap: 0.5rem; }
    .form-check input { width: auto; }
</style>
@endpush

<div class="form-container">
    <form action="{{ $action }}" method="POST">
        @csrf
        @if($method === 'PUT')
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
            @error('name') <div class="error-message">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email') <div class="error-message">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="hidden" name="is_admin" value="0">
                <input type="checkbox" id="is_admin" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin ?? false) == 1)>
                <label for="is_admin">Make this user an Administrator</label>
            </div>
            @error('is_admin') <div class="error-message">{{ $message }}</div> @enderror
        </div>

        {{-- Add password fields if you want to allow password changes --}}
        {{-- Be sure to add validation in the controller if you do --}}

        <button type="submit" class="btn-submit">Save User</button>
    </form>
</div>