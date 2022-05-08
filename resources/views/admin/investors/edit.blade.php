@extends('layouts.app')

@section('content')

    <x-entities.create>
        <x-slot:title>Edit {{ $investor->name }}</x-slot:title>
        @include('admin.investors.form', [
            'route' => route('investors.update', $investor->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
