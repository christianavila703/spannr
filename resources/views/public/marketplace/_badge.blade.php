{{--
    DEV-10: "Add the Spannr badge to your site". Clinic and Doctor profiles only, and only for Public listings (a pending
    profile is not visible to visitors, so a link to it would 404). Sits directly under the "Is this your clinic?" box.
    The snippet is a plain link back to this listing's own profile, with no rel attribute.
--}}
@php
    $badgeIsMedical = strcasecmp((string) $listing->type, \App\Models\Listing::TYPE_CLINIC) === 0
        || strcasecmp((string) $listing->type, \App\Models\Listing::TYPE_DOCTOR) === 0;
    $badgeIsPublic = strcasecmp((string) $listing->status, \App\Models\Listing::STATUS_PUBLIC) === 0;
@endphp
@if($badgeIsMedical && $badgeIsPublic)
    @php
        $badgeUrl = url('/images/badges/find-us-on-spannr.svg');
        $badgeSnippet = '<a href="' . e($listing->show_url) . '"><img src="' . e($badgeUrl) . '" width="200" height="60" alt="Find us on Spannr"></a>';
    @endphp
    <section class="listing-badge mt-5 pt-4 border-top max-width-600" aria-labelledby="listing-badge-title">
        <h2 class="h4 mb-3" id="listing-badge-title">Add the Spannr badge to your site</h2>
        <p class="mb-3">Find us on Spannr? Show it on your website and link your visitors back to your profile.</p>
        <div class="mb-3">
            <img src="{{ $badgeUrl }}" width="200" height="60" alt="Find us on Spannr">
        </div>
        <label for="listing-badge-snippet" class="visually-hidden">HTML snippet for the badge</label>
        <textarea id="listing-badge-snippet" class="form-control font-monospace small" rows="4" readonly>{{ $badgeSnippet }}</textarea>
        <div class="mt-2 d-flex align-items-center">
            <button type="button" class="btn btn-outline-dark" id="listing-badge-copy">Copy</button>
            <span class="ms-3 small text-muted" id="listing-badge-copy-status" role="status" aria-live="polite"></span>
        </div>
        <script>
            (function () {
                var button = document.getElementById('listing-badge-copy');
                var box = document.getElementById('listing-badge-snippet');
                var status = document.getElementById('listing-badge-copy-status');
                if (!button || !box) { return; }

                function report(copied) {
                    status.textContent = copied ? 'Copied' : 'Press Ctrl+C or Cmd+C to copy';
                }

                function selectAndCopy() {
                    box.focus();
                    box.select();
                    box.setSelectionRange(0, box.value.length);
                    var copied = false;
                    try { copied = document.execCommand('copy'); } catch (e) { copied = false; }
                    report(copied);
                }

                button.addEventListener('click', function () {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(box.value).then(function () { report(true); }, selectAndCopy);
                    } else {
                        selectAndCopy();
                    }
                });
            })();
        </script>
    </section>
@endif
