@extends('admin.layouts.app')

@section('title', 'Edit Home Page Content')
@section('page-title', 'Edit Home Page Content')

@section('content')
<style>
    .hc-form { max-width: 800px; }
    .hc-section {
        background: var(--navy-700);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .hc-section h3 {
        margin-top: 0;
        font-family: "Playfair Display", serif;
        color: var(--gold-500);
        border-bottom: 1px solid var(--border-color);
        padding-bottom: .5rem;
        margin-bottom: 1rem;
    }
    .hc-error { color: var(--red-500); font-size: .9em; margin-top: .25rem; }
    .hc-actions { margin-top: 1rem; }
</style>

@if(session('status'))
    <div class="alert-success">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('admin.home-content.update') }}" class="hc-form">
    @csrf
    @method('PUT')

    <div class="hc-section">
        <h3>Hero Section</h3>
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="hero_title" value="{{ old('hero_title', $content['hero_title']) }}">
            @error('hero_title') <div class="hc-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Subtitle</label>
            <textarea name="hero_subtitle" rows="3">{{ old('hero_subtitle', $content['hero_subtitle']) }}</textarea>
            @error('hero_subtitle') <div class="hc-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label>Button Text</label>
            <input type="text" name="hero_button_text" value="{{ old('hero_button_text', $content['hero_button_text']) }}">
        </div>
        <div class="form-group">
            <label>Button Link (URL or path, e.g. /properties)</label>
            <input type="text" name="hero_button_url" value="{{ old('hero_button_url', $content['hero_button_url']) }}">
        </div>
    </div>

    @foreach([1,2,3] as $i)
        <div class="hc-section">
            <h3>Category {{ $i }}</h3>
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="cat{{ $i }}_title" value="{{ old('cat'.$i.'_title', $content['cat'.$i.'_title']) }}">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="cat{{ $i }}_text" rows="3">{{ old('cat'.$i.'_text', $content['cat'.$i.'_text']) }}</textarea>
            </div>
            <div class="form-group">
                <label>Link (URL or path)</label>
                <input type="text" name="cat{{ $i }}_url" value="{{ old('cat'.$i.'_url', $content['cat'.$i.'_url']) }}">
            </div>
        </div>
    @endforeach

    <div class="hc-actions">
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>
@endsection
