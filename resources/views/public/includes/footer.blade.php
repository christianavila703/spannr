<footer class="public-footer">
    <div class="container">
        <div class="logo">
            <a href="/"><img src="{{ Vite::asset('resources/images/spannr-logo-white.svg') }}" alt="Spannr" class="logo"></a>
        </div>
        <nav class="footer-nav-container">
            <div class="nav-container">
                <ul class="navbar-nav footer-nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('public.content.about') }}" class="nav-link">About</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('public.content.terms') }}" class="nav-link">Terms of Use</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('public.content.privacy') }}" class="nav-link">Privacy Policy</a>
                    </li>
                    <x-nav.contact-link />
                </ul>
            </div>
        </nav>
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> Spannr. All Rights Reserved.
        </div>
        <div class="social-icons">
            <div>
                <a href="https://www.facebook.com/spannr" target="_blank" rel="noopener noreferrer" title="Follow us on Facebook">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
            </div>
            <div>
                <a href="http://www.twitter.com/spannr_" target="_blank" rel="noopener noreferrer" title="Follow us on Twitter">
                    <i class="fa-brands fa-twitter"></i>
                </a>
            </div>
            <div>
                <a href="https://www.linkedin.com/company/spannr" target="_blank" rel="noopener noreferrer" title="Follow us on LinkedIn">
                    <i class="fa-brands fa-linkedin"></i>
                </a>
            </div>
            <div>
                <a href="https://www.instagram.com/spannr_/" target="_blank" rel="noopener noreferrer" title="Follow us on Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </div>
        </div>
    </div>
</footer>
