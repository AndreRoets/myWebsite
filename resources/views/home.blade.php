@extends('layouts.app')

@section('title', 'Home Finders Coastal - Discover the Exceptional')

@section('content')
<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Discover the Exceptional</h1>
            <p>Than odeieur so variations tree free wood ass inc com discprenad. fou edersunt.</p>
            <a href="{{ route('properties.index') }}" class="hero-btn">VIEW PORTFOLIO</a>
        </div>
    </section>

   <section class="categories">
<div class="container category-grid">
<div class="category-item">
  <div class="category-item-inner">
    <h3>Waterfront Estates</h3>
    <p>Exclusive seaside luxury with unmatched serenity.</p>
  </div>
</div>
<div class="category-item">
  <div class="category-item-inner">
    <h3>Urban Penthouses</h3>
    <p>Experience the skyline from the top — modern & elite.</p>
  </div>
</div>
<div class="category-item">
  <div class="category-item-inner">
    <h3>Exclusive Villas</h3>
    <p>Private, elegant homes for the most discerning buyers.</p>
  </div>
</div>
</div>
</section>

    <section class="testimonial">
        <div class="container">
            <blockquote>"Curated for the Discerning"</blockquote>
            <cite>— Satisfied Client</cite>
        </div>
    </section>
</main>
@endsection