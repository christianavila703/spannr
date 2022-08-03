@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Content</h1>

        @include('admin.includes.status-messages')

        <livewire:content.index />
    </div>
@endsection
