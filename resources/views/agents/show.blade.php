@extends('layouts.app')

@section('title', $agent->name . ' - Agent Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/agent-show.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@section('content')
<div class="agent-profile-container">
    <a href="{{ route('agents.index') }}" class="back-link">&larr; Back to All Agents</a>

    <div class="agent-profile-card">
        <div class="agent-profile__image-wrapper">
            <img src="{{ $agent->image ? asset('storage/' . $agent->image) : asset('Image/agent-placeholder.webp') }}"
                 alt="{{ $agent->name ?? 'Agent' }}"
                 class="agent-profile__image">
        </div>
        <div class="agent-profile__details">
            <h1 class="agent-profile__name">{{ $agent->name }}</h1>
            <p class="agent-profile__title">{{ $agent->title }}</p>

            @if($agent->description)
                <div class="agent-profile__description">
                    <p>{{ $agent->description }}</p>
                </div>
            @endif

            <div class="agent-profile__contact-info">
                <h3 class="contact-info__heading">Contact Me</h3>
                <div class="contact-info__item">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:{{ $agent->email }}">{{ $agent->email }}</a>
                </div>
                <div class="contact-info__item">
                    <i class="fas fa-phone"></i>
                    <a href="tel:{{ str_replace(' ', '', $agent->phone) }}">{{ $agent->phone }}</a>
                </div>
                {{-- You can add social media links here if your Agent model includes them --}}
                {{--
                <div class="contact-info__social">
                    <a href="#" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
                --}}
            </div>
        </div>
    </div>
</div>
@endsection