<x-mail::message>
# {{ $formEntry->entity->name }}

**Name:** {{ $formEntry->first_name }} {{ $formEntry->last_name }}

**Email:** {{ $formEntry->email }}

@if(array_key_exists('phone', $formFields))
**Phone:** {{ $formFields['phone'] }}
@endif

**Message:**

{{ $formFields['message'] }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
