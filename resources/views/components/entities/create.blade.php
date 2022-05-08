@props([
    'title'
])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="container">
        <h1 class="text-primary">{{ $title }}</h1>

        @include('admin.includes.status-messages')
        {{ $slot }}
    </div>
</div>
