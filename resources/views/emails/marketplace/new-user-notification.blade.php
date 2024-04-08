<x-mail::message>
Hi {{ $user->name }}!

@if($newUser)
Welcome to Spannr, your gateway to the world of longevity. We have resources, data, insights, and the first marketplace dedicated to finding longevity services and treatments.

You were invited to manage the Spannr Marketplace listing for:<br>
<strong>{{ $listing->name }}</strong>

An account has been created for you. Set your password <a href="{{ route('password.request') }}">here</a> or log in securely with an <a href="{{ route('login.link') }}">email link</a>.
@else
You can now manage the Spannr Marketplace listing for <strong>{{ $listing->name }}</strong>. When you're logged in, you will see options to edit your listing.
@endif

<x-mail::button :url="$url">
Manage Listing
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
