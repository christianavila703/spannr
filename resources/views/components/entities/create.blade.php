@props([
    'title'
])

<div {{ $attributes->merge(['class' => 'alert ']) }}>
    <div class="container">
        <h1 class="text-primary">{{ $title }}</h1>

        @include('admin.includes.status-messages')
        {{ $slot }}
    </div>
</div>

