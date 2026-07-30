<footer class="site-footer">
    <div class="container-xl">
        <div class="site-footer-main">
            <div class="site-footer-brand">
                <a href="{{ route('home') }}" class="brand brand-light" aria-label="Resume Studio home">
                    <span class="brand-mark">
                        <img src="{{ asset('assets/common/media/logo.png') }}" width="42" height="42" alt="" aria-hidden="true">
                    </span>
                    Resume<span>Studio</span>
                </a>
                <p>Professional resumes, thoughtfully designed to help your next opportunity stand out.</p>
            </div>

            <div class="site-footer-links">
                <section aria-labelledby="footer-explore">
                    <h2 id="footer-explore">Explore</h2>
                    <a href="{{ route('about') }}">About us</a>
                    <a href="{{ route('contact') }}">Contact us</a>
                    @auth
                        <a href="{{ route('dashboard') }}">My workspace</a>
                    @else
                        <a href="{{ route('register') }}">Create an account</a>
                    @endauth
                </section>
                <section aria-labelledby="footer-legal">
                    <h2 id="footer-legal">Legal</h2>
                    <a href="{{ route('privacy') }}">Privacy policy</a>
                    <a href="{{ route('terms') }}">Terms of service</a>
                </section>
            </div>
        </div>

        <div class="site-footer-bottom">
            <span>© {{ now()->year }} Resume Studio. All rights reserved.</span>
            <span>Build your story with confidence.</span>
        </div>
    </div>
</footer>
