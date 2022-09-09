@props(['showNameFields' => ''])

<div class="sidebar-optin">
    <p class="h4 title mb-2">Sign Up For Our  Newsletter</p>
    <p class="mb-3">Weekly insights into the future of longevity</p>
    <livewire:forms.newsletter-opt-in showNameFields="{{ $showNameFields }}" />
</div>
