@extends('layouts.app')

@section('title', 'Contact Us')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    <style>
        /* The provided CSS uses a linear gradient on the body, which might conflict with other pages.
           This will apply it only to the contact page body. */
        body {
            background: linear-gradient(180deg, #0b1a29, #0d1b2a);
        }
    </style>
@endpush

@section('content')

<main>
    <section class="hero">
        <div class="container hero-frame">
            <div class="bg"></div>
            <div class="content">
                <h1>Connect with Our Private Client Team</h1>
                <p>Your aspirations demand a partner who understands the nuances of luxury real estate. Reach out to begin your bespoke property journey.</p>
                <a class="cta" href="{{ route('properties.index') }}">VIEW PORTFOLIO</a>
            </div>
        </div>
    </section>

    <section class="container contact">
        {{-- Left Panel: Contact Form --}}
        <div class="card panel">
            <h3>Send Us a Message</h3>
            <p class="text-dim" style="font-size: 14px; margin-top: -8px; margin-bottom: 16px;">We'll get back to you within one business day.</p>
            <form action="#" method="POST" class="form-grid">
                @csrf
                <div class="stack-16" style="grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label for="first_name" class="label">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="input" placeholder="John" required>
                    </div>
                    <div>
                        <label for="last_name" class="label">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="input" placeholder="Doe" required>
                    </div>
                </div>
                <div>
                    <label for="email" class="label">Email Address</label>
                    <input type="email" id="email" name="email" class="input" placeholder="you@example.com" required>
                </div>
                <div>
                    <label for="phone" class="label">Phone Number (Optional)</label>
                    <input type="tel" id="phone" name="phone" class="input" placeholder="+1 (555) 000-0000">
                </div>
                <div>
                    <label for="message" class="label">Your Message</label>
                    <textarea id="message" name="message" class="textarea" placeholder="How can we assist you today?" required></textarea>
                </div>
                <button type="submit" class="btn-primary">Send Message</button>
            </form>
        </div>

        {{-- Right Panel: Contact Info --}}
        <aside class="card panel">
            <div class="stack-24">
                <div>
                    <h3>Contact Information</h3>
                    <div class="info-block">
                        <p>Our team is available to assist with your inquiries. We pride ourselves on prompt and discreet communication.</p>
                        <div class="kv">
                            <span class="k">Email</span>
                            <a href="mailto:info@hfcoastal.co.za" class="v gold">info@hfcoastal.co.za</a>
                        </div>
                        <div class="kv">
                            <span class="k">Phone</span>
                            <span class="v">+27 21 123 4567</span>
                        </div>
                    </div>
                </div>

                <hr class="gold-line">

                <div>
                    <h3 style="margin-bottom: 8px;">Office Hours</h3>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Mon – Fri</td>
                                <td>9:00 AM – 5:00 PM</td>
                            </tr>
                            <tr>
                                <td>Saturday</td>
                                <td>10:00 AM – 2:00 PM</td>
                            </tr>
                            <tr>
                                <td>Sunday</td>
                                <td>By Appointment</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="gold-line">

                <div>
                    <h3 style="margin-bottom: 14px;">Follow Us</h3>
                    <div class="social">
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
            </div>
        </aside>
    </section>

    <footer class="container footer">
        <h3 class="display" style="text-align:center">Discretion Across Continents</h3>
        <div class="world"></div>
        <div class="meta">
            <span>&copy; {{ date('Y') }} Home Finders Coastal. All Rights Reserved.</span>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </footer>
</main>

@endsection