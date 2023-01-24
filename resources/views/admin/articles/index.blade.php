@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Articles</h1>

        @include('admin.includes.status-messages')

        <livewire:admin.articles.articles-index />
    </div>
@endsection
