@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:title>{{ $investor->name }}</x-slot:title>
        <x-slot:buttons>
            <a href="{{ route('investors.edit', $investor->id) }}" class="btn btn-info">Edit</a>
        </x-slot:buttons>
        @include('admin.investors.show-data')
    </x-entities.show>

@endsection
