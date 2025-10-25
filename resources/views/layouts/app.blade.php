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
    
    <style>
        /* Modal Styles */
        .auth-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .auth-modal {
            background: var(--navy-900, #111827);
            color: var(--text-100, #f1f1f1);
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 450px;
            position: relative;
            border: 1px solid var(--gold-500, #c0a87f);
        }
        .auth-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: var(--text-100);
            font-size: 1.5rem;
            cursor: pointer;
        }
        .auth-modal-tabs {
            display: flex;
            border-bottom: 1px solid var(--gold-500);
            margin-bottom: 1.5rem;
        }
        .auth-modal-tabs button {
            flex: 1;
            padding: 0.75rem;
            background: none;
            border: none;
            color: var(--text-300, #ccc);
            cursor: pointer;
            font-size: 1.1rem;
            font-family: "Playfair Display", serif;
            transition: color 0.3s;
        }
        .auth-modal-tabs button.active {
            color: var(--gold-500);
            border-bottom: 2px solid var(--gold-500);
        }
        .auth-modal-content {
            display: none;
        }
        .auth-modal-content.active {
            display: block;
        }
        .auth-modal form .form-group {
            margin-bottom: 1rem;
        }
        .auth-modal form label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-200);
        }
        .auth-modal form input {
            width: 100%;
            padding: 0.75rem;
            background: #1f2937; /* dark slate */
            border: 1px solid #4b5563; /* gray border */
            border-radius: 4px;
            color: #f9fafb; /* near white text */
            font-family: "Montserrat", sans-serif;
        }

        .auth-modal form input:focus {
            outline: 0;
            border-color: var(--gold-500, #c0a87f);
            box-shadow: 0 0 6px rgba(192,168,127,0.6);
        }

        .field-error {
            color: #c0392b; font-size:0.8rem; margin-top:0.4rem;
        }
    </style>
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
                            <li><a href="{{ route('agents.index') }}">Agents</a></li>
                            <li><a href="{{ route('contact') }}">Contact</a></li>
                            @guest
                                <li><a href="#" class="login-btn" id="login-btn-hero">Login</a></li>
                            @else
                                @if (Auth::user()->isAdmin())
                                    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                                @endif
                                <li><a href="{{ route('profile.show') }}">Profile</a></li>
                            @endguest
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
                        <li><a href="{{ route('agents.index') }}">Agents</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        @guest
                            <li><a href="#" class="login-btn" id="login-btn-main">Login</a></li>
                        @else
                            @if (Auth::user()->isAdmin())
                                <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                            @endif
                            <li><a href="{{ route('profile.show') }}">Profile</a></li>
                        @endguest
                    </ul>
                </nav>
            </div>
        </header>
        <main class="main-content">@yield('content')</main>
    @endif

    {{-- Login/Register Modal --}}
    <div class="auth-modal-overlay" id="auth-modal-overlay">
        <div class="auth-modal" id="auth-modal">
            <button class="auth-modal-close" id="auth-modal-close">&times;</button>

            <div class="auth-modal-tabs">
                <button id="login-tab-btn" class="active">Login</button>
                @if (Route::has('register'))
                    <button id="register-tab-btn">Register</button>
                @endif
            </div>

            {{-- Login Form --}}
            <div id="login-content" class="auth-modal-content active">
                <form method="POST" action="{{ route('login') }}" id="login-form-modal">
                    @csrf

                    {{-- Global login error (e.g. wrong email/password) --}}
                    @if ($errors->has('email') && old('email') && !$errors->has('password'))
                        <div class="auth-error-box" style="background:#4b1e1e; color:#f8d7da; border:1px solid #c0392b; padding:0.75rem; border-radius:4px; margin-bottom:1rem; font-size:0.9rem;">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="login-email">{{ __('Email Address') }}</label>
                        <input id="login-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            @if ($errors->first('email') !== trans('auth.failed'))
                                <div class="field-error">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="login-password">{{ __('Password') }}</label>
                        <input id="login-password" type="password" name="password" required autocomplete="current-password">
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" class="hero-btn" style="width: 100%;">{{ __('Login') }}</button>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="clear-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </form>
            </div>

            @if (Route::has('register'))
                {{-- Register Form --}}
                <div id="register-content" class="auth-modal-content">
                    <form method="POST" action="{{ route('register') }}" id="register-form-modal">
                        @csrf
                        <div class="form-group">
                            <label for="register-name">{{ __('Name') }}</label>
                            <input id="register-name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="register-surname">{{ __('Surname') }}</label>
                            <input id="register-surname" type="text" name="surname" value="{{ old('surname') }}" required autocomplete="family-name">
                             @error('surname')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="register-email">{{ __('Email Address') }}</label>
                            <input id="register-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="register-password">{{ __('Password') }}</label>
                            <input id="register-password" type="password" name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="hero-btn" style="width: 100%;">{{ __('Register') }}</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

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
            <li><a href="{{ route('agents.index') }}">Agents</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
          </ul>
        </nav>
      </div>
    </footer>

    {{-- Blade -> JS Bridge --}}
    <script>
        const HAS_LOGIN_ERRORS = @json(
            ($errors->has('email') || $errors->has('password')) && old('email')
        );

        const HAS_REGISTER_ERRORS = @json(
            $errors->has('name') || $errors->has('surname') || $errors->has('password_confirmation') ||
            ($errors->has('email') && old('name')) // Email error on registration attempt
        );
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalOverlay = document.getElementById('auth-modal-overlay');
        const modal = document.getElementById('auth-modal');
        const openButtons = document.querySelectorAll('.login-btn');
        const closeButton = document.getElementById('auth-modal-close');

        const loginTabBtn = document.getElementById('login-tab-btn');
        const registerTabBtn = document.getElementById('register-tab-btn');
        const loginContent = document.getElementById('login-content');
        const registerContent = document.getElementById('register-content');

        // --- Helper Functions ---
        function openModal() {
            modalOverlay.style.display = 'flex';
        }

        function closeModal() {
            modalOverlay.style.display = 'none';
        }

        function activateLoginTab() {
            if (!loginTabBtn) return;
            loginTabBtn.classList.add('active');
            if (registerTabBtn) registerTabBtn.classList.remove('active');
            if (loginContent) loginContent.classList.add('active');
            if (registerContent) registerContent.classList.remove('active');
        }

        function activateRegisterTab() {
            if (!registerTabBtn) return;
            registerTabBtn.classList.add('active');
            if (loginTabBtn) loginTabBtn.classList.remove('active');
            if (registerContent) registerContent.classList.add('active');
            if (loginContent) loginContent.classList.remove('active');
        }

        // --- Event Listeners ---

        // Open modal from navbar click
        openButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                openModal();
                activateLoginTab(); // Default to login tab on manual open
            });
        });

        // Close modal events
        closeButton.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });

        // Tab switching click handlers
        if (loginTabBtn && registerTabBtn) {
            loginTabBtn.addEventListener('click', activateLoginTab);
            registerTabBtn.addEventListener('click', activateRegisterTab);
        }

        // --- Auto-open modal if there were validation errors on page load ---
        if (HAS_LOGIN_ERRORS) {
            openModal();
            activateLoginTab();
        } else if (HAS_REGISTER_ERRORS) {
            openModal();
            activateRegisterTab();
        }
    });
    </script>
    @stack('scripts')
</body>
</html>
