@php($content = \App\Http\Controllers\Admin\HomeContentController::load())
@extends('layouts.app')

@section('title', 'Home Finders Coastal - ' . $content['hero_title'])

@section('content')
<main>
    <section class="hero">
        <div class="hero-content">
            <h1>{{ $content['hero_title'] }}</h1>
            <p>{{ $content['hero_subtitle'] }}</p>
            <a href="{{ $content['hero_button_url'] }}" class="hero-btn">{{ $content['hero_button_text'] }}</a>
        </div>
    </section>

    <section class="categories">
        <div class="container category-grid">
            @foreach([1,2,3] as $i)
                <a href="{{ $content['cat'.$i.'_url'] }}" class="category-item">
                    <div class="category-item-inner">
                        <h3>{{ $content['cat'.$i.'_title'] }}</h3>
                        <p>{{ $content['cat'.$i.'_text'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</main>
@endsection
