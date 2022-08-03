@extends('layouts.public')

@section('content')
    <main class="page-organizations-list py-5">
        <div class="container">
            @if($seo)
                <div class="row col-12 col-lg-9 mx-auto">
                    {!! $seo->content !!}
                </div>
            @endif
            <div id="longevity-companies" class="row">
                @forelse ($organizations as $organization)
                    <div class="col-12 col-md-6 col-lg-4 company-card">
                        <a href="{{ route('public.content.organizations.show', $organization->slug) }}">
                            <div class="logo" style="background-image: url({{ $organization->image_url }});"></div>
                            <h2 class="h4 title">
                                {{ $organization->name }}
                            </h2>
                        </a>
                    </div>
                @empty
                    <div class="col-12 col-md-6 col-lg-4 empty-card">
                        <div class="p-5">No companies match your query</div>
                    </div>
                @endforelse
            </div>
        </div>
    </main>
@endsection
