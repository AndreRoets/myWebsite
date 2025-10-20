<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Home Finders Coastal')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    
    @stack('styles')
</head>

<body class="{{ Route::currentRouteName() === 'contact' ? 'contact-page-body' : '' }}">
    @if (Route::currentRouteName() === 'home')
        <div class="hero-section">
            <header>
                <div class="container navbar">
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset('Images/Logo.png') }}" alt="Home Finders Coastal Logo" style="width: 250px; height: 100px;">
                    </a>
                    <nav>
                        <ul class="nav-links">
                            <li><a href="{{ route('properties.index') }}">Properties</a></li>
                            <li><a href="#">Services</a></li>
                            <li><a href="#">About</a></li>
                            <li><a href="{{ route('contact') }}">Contact</a></li>
                            <li><a href="#" class="login-btn">Login</a></li>
                        </ul>
                    </nav>
                </div>
            </header>
            @yield('hero-content') {{-- For home and contact page hero sections --}}
        </div>
        <main>@yield('content')</main>
    @else
        <header>
            <div class="container navbar">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset('Images/Logo.png') }}" alt="Home Finders Coastal Logo" style="width: 250px; height: 100px;">
                </a>
                <nav>
                    <ul class="nav-links">
                        <li><a href="{{ route('properties.index') }}">Properties</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="#" class="login-btn">Login</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <main class="main-content">@yield('content')</main>
    @endif


    <footer class="footer-section">
      <div class="container footer-content">
        <a href="{{ route('home') }}" class="footer-logo">
          <img src="{{ asset('Images/Logo.png') }}" alt="Home Finders Coastal Logo">
        </a>
        <p class="footer-text">Discover your dream coastal home with us. Excellence in every detail.</p>
        <div class="social-icons">
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Home Finders Coastal. All Rights Reserved.</p>
        <nav>
          <ul class="footer-nav">
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
          </ul>
        </nav>
      </div>
    </footer>

    @stack('scripts')
</body>
</html>
