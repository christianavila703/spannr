@extends('layouts.public')

@if($seo)
    @push('head')
        @if(blank($seo->seo))
            <title>{{ $seo->name }} - Spannr</title>
            <meta name="description" content="{{ $seo->meta_description }}" />
        @endif
        @if($seo->tags->contains('name', 'noindex'))
            <meta name="robots" content="noindex, follow" />
        @endif
    @endpush
@endif

@section('content')
    <div class="page-container">
        <div class="container">
            <div class="single-page-content">
                @auth
                    @can('edit records')
                        <p class="mb-3">
                            <a href="{{ route('admin.content.edit', $seo->id) }}" class="text-tertiary">Edit page</a>
                        </p>
                    @endcan
                @endauth
                @if($seo)
                    {!! $seo->content !!}
                @endif
            </div>
        </div>
    </div>
@endsection
