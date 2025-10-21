@extends('layouts.app')

@section('title', 'Our Agents - Home Finders Coastal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/agents.css') }}">
@endpush

@section('content')
<div class="agents-page-container">
    <header class="page-header">
        <h1 class="page-header__title">Our Team of Experts</h1>
        <p class="page-header__subtitle">Meet the dedicated professionals who bring a wealth of experience and a passion for coastal properties to every client relationship.</p>
    </header>

    <div class="agents-grid">
        @foreach($agents as $agent)
            <div class="agent-card">
                <div class="agent-card__image-wrapper">
                    <img src="{{ $agent->image ? asset('storage/' . $agent->image) : asset('Image/agent-placeholder.webp') }}" alt="{{ $agent->name ?? 'Agent' }}">
                </div>
                <div class="agent-card__details">
                    <h2 class="agent-card__name">{{ $agent->name }}</h2>
                    <p class="agent-card__title">{{ $agent->title }}</p>
                </div>
                <div class="agent-card__contact">
                    <a href="mailto:{{ $agent->email }}">{{ $agent->email }}</a>
                    <a href="tel:{{ str_replace(' ', '', $agent->phone) }}">{{ $agent->phone }}</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection