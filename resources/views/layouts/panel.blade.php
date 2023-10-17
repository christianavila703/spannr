@extends('layouts.public')

@push('head')
    @vite('resources/sass/choices.scss')
    <script src="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js"></script>
@endpush

@section('content')
    <div class="py-4">
        @yield('panel')
    </div>
@endsection

