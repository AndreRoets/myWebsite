@extends('layouts.app')

@section('title', 'Contact Us - Home Finders Coastal')

@push('styles')
    <style>
        .contact-page {
            padding: 80px 0;
            background: #f4f6f9;
        }
        .contact-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }
        @media (min-width: 900px) {
            .contact-container {
                grid-template-columns: 1fr 1.2fr;
            }
        }
        .contact-info, .contact-form-section {
            background: #fff;
            padding: 30px 40px;
            border: 1px solid #e9ecef;
        }
        .contact-info h2, .contact-form-section h2 {
            font-family: "Playfair Display", serif;
            font-size: 28px;
            margin-bottom: 20px;
            color: #212529;
        }
        .contact-info p {
            color: #6c757d;
            line-height: 1.7;
            margin-bottom: 25px;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            color: #495057;
        }
        .info-item i {
            font-size: 18px;
            width: 20px;
            text-align: center;
            color: #007bff;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: .5rem;
            font-weight: 600;
            color: #495057;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            font-family: "Montserrat", sans-serif;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
    </style>
@endpush

@section('content')
<div class="contact-page">
    <div class="contact-container">
        <section class="contact-info">
            <h2>Get in Touch</h2>
            <p>We'd love to hear from you. Whether you have a question about a property, our services, or anything else, our team is ready to answer all your questions.</p>
            <div class="info-item"><i class="fas fa-map-marker-alt"></i><span>123 Coastal Drive, Cape Town, SA</span></div>
            <div class="info-item"><i class="fas fa-phone"></i><span>+27 21 123 4567</span></div>
            <div class="info-item"><i class="fas fa-envelope"></i><span>info@hfcoastal.co.za</span></div>
        </section>
        <section class="contact-form-section">
            <h2>Send Us a Message</h2>
            <form action="#" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <button type="submit" class="hero-btn">Send Message</button>
            </form>
        </section>
    </div>
</div>
@endsection