@extends('layouts.public')

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
