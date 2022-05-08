@props([
    'title',
    'buttons'
])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="container">

        <div class="d-flex flex-wrap justify-content-between">
            <div class="me-5">
                <h1 class="text-primary">{{ $title }}</h1>
            </div>
            <div class="d-flex flex-wrap align-items-center position-relative">
                {{ $buttons }}
            </div>
        </div>

        @include('admin.includes.status-messages')

        {{ $slot }}

    </div>
</div>
