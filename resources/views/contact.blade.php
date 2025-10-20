@extends('layouts.app')

@section('title', 'Contact Us - Home Finders Coastal')

@push('styles')
    <style>
      /* ===============================
         Home Finders Coastal — Contact Page (Sharp Theme)
         =============================== */
      
      :root{
        --navy-900: #0b1220;   /* page background */
        --navy-800: #111a2b;   /* panels */
        --navy-700: #152239;   /* subtle contrast */
        --gold-500: #c0a87f;   /* primary accent */
        --text-100: #e9edf4;   /* body text */
        --text-300: #c9d1e0;   /* muted text */
        --shadow: 0 8px 25px rgba(0,0,0,.4);
      }
      
      /* Page background (behind header/footer) */
      body.contact-page-body {
        background: var(--navy-900);
        color: var(--text-100);
        font-family: "Montserrat", system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        line-height: 1.6;
      }

      /* Remove rounding on this page */
      .contact-page-container * {
        border-radius: 0 !important;
      }
      
      /* ===============================
         HERO
         =============================== */
      
      .hero-contact {
        position: relative; isolation: isolate;
        width: min(1200px, 94vw);
        margin: 40px auto 32px;
        border: 1px solid rgba(192, 168, 127, 0.35);
        box-shadow: var(--shadow);
      }
      
      .hero-contact__media{
        position: relative;
        height: clamp(280px, 42vw, 380px);
        background: url("/Image/Hero.webp") center/cover no-repeat;
        filter: saturate(0.95);
      }
      
      .hero-contact__media::after{
        content:"";
        position:absolute; inset:0;
        background: linear-gradient(180deg, rgba(11,18,32,.45), rgba(11,18,32,.85));
      }
      
      .hero-contact__content{
        position:absolute; inset:0;
        display:grid; place-items:center;
        padding: 24px;
        text-align:center;
      }
      
      .hero-contact__title{
        font-family: "Playfair Display", Georgia, serif;
        font-weight: 700;
        font-size: clamp(28px, 4vw, 46px);
        letter-spacing:.02em;
        margin: 0 0 10px;
        text-shadow: 0 4px 15px rgba(0,0,0,.5);
      }
      
      .hero-contact__subtitle{
        color: var(--text-300);
        max-width: 820px;
        margin: 0 auto 18px;
        font-size: clamp(13px, 1.5vw, 16px);
      }
      
      .hero-contact__cta{
        display: inline-block;
        padding: 12px 28px;
        background: var(--gold-500);
        color: #1a1a1a;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .1em;
        border: 1px solid var(--gold-500);
        transition: filter .15s;
      }
      .hero-contact__cta:hover{
        filter: brightness(1.1);
      }
      
      /* Thin gold divider line under hero (to echo screenshot) */
      .hero-contact__edge{
        height: 2px;
        background: linear-gradient(90deg, var(--gold-500), rgba(192,168,127,.2) 70%, transparent);
      }
      
      /* ===============================
         CONTACT SPLIT PANELS
         =============================== */
      
      .contact-panels{
        width: min(1200px, 94vw);
        margin: 32px auto 60px;
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 36px;
      }
      
      .panel{
        position: relative;
        background: var(--navy-800);
        padding: 28px;
        box-shadow: var(--shadow);
        border: 1px solid rgba(192, 168, 127, 0.35);
      }
      
      .panel__title{
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(18px, 2.2vw, 24px);
        margin: 0 0 20px;
        color: var(--text-100);
        font-weight: 700;
      }
      
      /* ---- Left: Form ---- */
      
      .form-grid{
        display:grid;
        grid-template-columns: 1fr;
        gap: 16px;
      }
      
      .input, .textarea{
        width: 100%;
        background: var(--navy-900);
        border: 1px solid rgba(192, 168, 127, 0.4);
        color: var(--text-100);
        padding: 12px 12px;
        outline: none;
        transition: border-color .2s ease;
        font-size: 14px;
      }
      
      .input::placeholder, .textarea::placeholder{
        color: var(--text-300);
        opacity: 0.7;
      }
      
      .input:focus, .textarea:focus{
        border-color: var(--gold-500);
      }
      
      .textarea{
        min-height: 90px;
        resize: vertical;
      }
      
      .btn-submit{
        margin-top: 10px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding: 12px 24px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        background: var(--gold-500);
        color: #1a1a1a;
        border: 1px solid var(--gold-500);
        cursor:pointer;
        transition: filter .15s;
      }
      .btn-submit:hover{ filter: brightness(1.1); }
      
      /* ---- Right: Details ---- */
      
      .detail-block{
        border-top: 1px solid rgba(192, 168, 127, 0.25);
        padding-top: 14px;
        margin-top: 12px;
      }
      
      .detail-block:first-of-type{
        border-top: none;
        padding-top: 0;
        margin-top: 0;
      }
      
      .detail-heading{
        font-family: "Playfair Display", Georgia, serif;
        color: var(--gold-500);
        font-size: 16px;
        margin: 0 0 6px;
      }
      
      .detail-row{
        display:flex;
        gap:10px;
        align-items:flex-start;
        color: var(--text-300);
        font-size: 14px;
        padding: 6px 0;
      }
      
      .detail-row strong{
        color: var(--text-100);
        font-weight: 600;
        min-width: 70px;
      }
      
      /* ===============================
         RESPONSIVE
         =============================== */
      
      @media (max-width: 980px){
        .contact-panels{
          grid-template-columns: 1fr;
          gap: 28px;
        }
      }
      
      @media (max-width: 520px){
        .hero-contact__media{ height: 260px; }
        .panel{ padding: 22px; }
        .btn-submit{ width:100%; }
      }
    </style>
@endpush

@section('content')
<div class="contact-page-container">
    <!-- HERO -->
    <section class="hero-contact">
        <div class="hero-contact__media"></div>
        <div class="hero-contact__content">
            <div>
                <h1 class="hero-contact__title">Connect With Us</h1>
                <p class="hero-contact__subtitle">
                    Your journey to exceptional coastal living begins here. Reach out to our dedicated team for any inquiries or to schedule a private consultation.
                </p>
                <a href="#contact-form" class="hero-contact__cta">Send a Message</a>
            </div>
        </div>
        <div class="hero-contact__edge"></div>
    </section>

    <!-- PANELS -->
    <div class="contact-panels">
        <!-- Left Panel: Form -->
        <section class="panel" id="contact-form">
            <h2 class="panel__title">Send an Enquiry</h2>
            <form action="#" method="POST">
                @csrf
                <div class="form-grid">
                    <input type="text" id="name" name="name" class="input" placeholder="Full Name" required>
                    <input type="email" id="email" name="email" class="input" placeholder="Email Address" required>
                    <textarea id="message" name="message" class="textarea" placeholder="Your Message" required></textarea>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send Message</span>
                </button>
            </form>
        </section>

        <!-- Right Panel: Details -->
        <section class="panel">
            <h2 class="panel__title">Our Details</h2>
            <div class="detail-block">
                <h3 class="detail-heading">Primary Office</h3>
                <div class="detail-row">
                    <i class="fas fa-map-marker-alt" style="margin-top:4px; width:14px;"></i>
                    <span>123 Coastal Drive, Cape Town, South Africa</span>
                </div>
            </div>
            <div class="detail-block">
                <h3 class="detail-heading">Contact Information</h3>
                <div class="detail-row">
                    <strong>Phone:</strong>
                    <span>+27 21 123 4567</span>
                </div>
                <div class="detail-row">
                    <strong>Email:</strong>
                    <span>info@hfcoastal.co.za</span>
                </div>
            </div>
            <div class="detail-block">
                <h3 class="detail-heading">Office Hours</h3>
                <div class="detail-row">
                    <strong>Mon – Fri:</strong>
                    <span>09:00 – 17:00</span>
                </div>
                <div class="detail-row">
                    <strong>Sat – Sun:</strong>
                    <span>By Appointment</span>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection