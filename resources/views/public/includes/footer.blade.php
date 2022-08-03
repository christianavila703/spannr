<footer class="public-footer">
    <div class="container">
        <img src="{{ asset('images/spannr-logo-white.svg') }}" alt="Spannr" class="logo">
        <p class="h3">End-to-End Longevity.</p>

        <div class="copyright-terms">
            <p class="copyright">
                &copy; {{ date('Y') }} Spannr. All Rights Reserved.
            </p>
            <ul class="terms">
                <li>
                    <a href="{{ route('public.content.terms') }}">Terms &amp; Conditions</a>
                </li>
                <li>
                    <a href="{{ route('public.content.privacy') }}">Privacy Policy</a>
                </li>
                <li>
                    <a href="{{ route('public.content.contact') }}">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</footer>
