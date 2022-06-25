@props([
    'title',
    'buttons'
])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="container">

        <div class="d-flex flex-wrap flex-lg-nowrap align-items-start justify-content-between">
            <div class="me-5">
                <h1 class="text-primary">{{ $title }}</h1>
            </div>
            <div class="d-flex flex-wrap align-items-center position-relative flex-shrink-0 my-2">
                {{ $buttons }}
            </div>
        </div>

        @include('admin.includes.status-messages')

        {{ $slot }}

    </div>
</div>
