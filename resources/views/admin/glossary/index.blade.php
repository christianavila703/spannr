@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Glossary</h1>

        @include('admin.includes.status-messages')

        <livewire:admin.glossary.index />
    </div>
@endsection
