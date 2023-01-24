@props([
    'title',
    'backUrl',
    'backLabel',
    'headingClass' => ''
])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="container">
        <div class="heading-container d-lg-flex align-items-flex-end justify-content-between mb-4">
            <h1 class="text-primary me-5 {{ $headingClass }}">{{ $title }}</h1>

            <div>
                <a href="{{ $backUrl }}" class="btn btn-sm text-dark text-nowrap"><i class="fa-regular fa-arrow-left me-1"></i>Back to {{ $backLabel }}</a>
            </div>
        </div>

        @include('admin.includes.status-messages')
        {{ $slot }}
    </div>
</div>
