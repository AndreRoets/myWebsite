@extends('layouts.app')

@section('title', 'Our Agents - Home Finders Coastal')

@push('styles')
<style>
    /* ===============================
       Agents Page (Sharp Theme)
       =============================== */
    :root {
        --navy-900: #0b1220;
        --navy-800: #111a2b;
        --gold-500: #c0a87f;
        --text-100: #e9edf4;
        --text-300: #c9d1e0;
        --shadow: 0 8px 25px rgba(0,0,0,.4);
    }

    body {
        background: var(--navy-900);
        color: var(--text-100);
    }

    .agents-page-container {
        width: min(1200px, 94vw);
        margin: 40px auto 80px;
    }

    .agents-page-container * {
        border-radius: 0 !important;
    }

    .page-header {
        text-align: center;
        margin-bottom: 48px;
    }

    .page-header__title {
        font-family: "Playfair Display", Georgia, serif;
        font-weight: 700;
        font-size: clamp(28px, 4vw, 42px);
        letter-spacing: .02em;
        margin: 0 0 8px;
        color: var(--text-100);
    }

    .page-header__subtitle {
        color: var(--text-300);
        max-width: 65ch;
        margin: 0 auto;
        font-size: clamp(14px, 1.5vw, 16px);
    }

    .agents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 40px;
        justify-content: center;
    }

    /* ===============================
       Agent Card
       =============================== */
    .agent-card {
        background: var(--navy-800);
        border: 1px solid rgba(192, 168, 127, 0.35);
        text-align: center;
        padding-bottom: 24px;
        box-shadow: var(--shadow);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .agent-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(0,0,0,.5);
    }

    .agent-card__image-wrapper {
        position: relative;
        background: var(--navy-900);
        margin-bottom: 20px;
    }

    .agent-card__image-wrapper img {
        width: 100%;
        height: 380px;
        object-fit: cover;
        object-position: center top;
        display: block;
        filter: grayscale(0.5) contrast(1.05);
        mix-blend-mode: screen;
        opacity: 0.7;
    }

    .agent-card__image-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, var(--navy-800) 0%, transparent 40%);
    }

    .agent-card__name {
        font-family: "Playfair Display", serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--text-100);
        margin: 0 0 4px;
    }

    .agent-card__title {
        font-size: 13px;
        color: var(--gold-500);
        text-transform: uppercase;
        letter-spacing: .1em;
        margin-bottom: 16px;
    }

    .agent-card__contact a {
        display: block;
        color: var(--text-300);
        text-decoration: none;
        font-size: 14px;
        margin: 4px auto;
        transition: color .2s;
    }

    .agent-card__contact a:hover {
        color: var(--gold-500);
    }
</style>
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
                    <img src="{{ $agent->image_url ?? asset('Image/agent-placeholder.webp') }}" alt="{{ $agent->name }}">
                </div>
                <h2 class="agent-card__name">{{ $agent->name }}</h2>
                <p class="agent-card__title">{{ $agent->title }}</p>
                <div class="agent-card__contact">
                    <a href="mailto:{{ $agent->email }}">{{ $agent->email }}</a>
                    <a href="tel:{{ str_replace(' ', '', $agent->phone) }}">{{ $agent->phone }}</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection