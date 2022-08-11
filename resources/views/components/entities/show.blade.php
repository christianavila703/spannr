@props([
    'title',
    'buttons'
])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="container">

        <div class="d-flex flex-wrap flex-lg-nowrap align-items-center justify-content-between">
            <div class="breadcrumbs me-5">{{ $breadcrumbs ?? '' }}</div>
            <div class="d-flex flex-wrap align-items-center position-relative flex-shrink-0 my-2">
                {{ $buttons }}
            </div>
        </div>

        <h1 class="text-primary">{{ $title }}</h1>

        @include('admin.includes.status-messages')

        {{ $slot }}

    </div>
</div>
