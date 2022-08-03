@extends('layouts.public')

@section('content')
    <div class="page-container">
        <div class="container">
            <div class="single-page-content">
                @if($seo)
                    {!! $seo->content !!}
                @endif
            </div>
        </div>
    </div>
@endsection
