@props([
    'title',
    'backUrl',
    'backLabel'
])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="container">
        <div class="d-lg-flex align-items-flex-end justify-content-between mb-4">
            <h1 class="text-primary me-5">{{ $title }}</h1>

            <div>
                <a href="{{ $backUrl }}" class="btn btn-sm text-dark"><i class="fa-regular fa-arrow-left me-1"></i>Back to {{ $backLabel }}</a>
            </div>
        </div>

        @include('admin.includes.status-messages')
        {{ $slot }}
    </div>
</div>
