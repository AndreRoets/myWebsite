{{--
    resources/views/layouts/footer.blade.php
    A redesigned luxury footer with a balanced layout and subtle animations.
--}}
<footer class="site-footer">
    <div class="site-footer__container">

        {{-- Main footer content with branding and navigation --}}
        <div class="site-footer__main">
            <div class="site-footer__branding">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="site-footer__logo" title="Home Finders Coastal Home">
                    <img src="{{ asset('Images/Logo.png') }}" alt="Home Finders Coastal Logo">
                </a>
                {{-- Tagline --}}
                <p class="site-footer__tagline">
                    Discover your dream coastal home with us. Excellence in every detail.
                </p>
            </div>

            <div class="site-footer__navigation">
                {{-- Quick Links --}}
                <div class="site-footer__nav-group">
                    <h4 class="site-footer__heading">Explore</h4>
                    <nav class="site-footer__nav">
                        <a href="{{ route('properties.index') }}">Properties</a>
                        <a href="{{ route('agents.index') }}">Agents</a>
                        <a href="{{ route('contact') }}">Contact</a>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Bottom bar with copyright --}}
        <div class="site-footer__bottom">
            <p class="site-footer__copyright">
                &copy; {{ date('Y') }} Home Finders Coastal. Designed by Andre Roets. All Rights Reserved.
            </p>
        </div>
    </div>
</footer>