@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.glossary.index') }}" backLabel="Glossary">
        <x-slot:title>Create Glossary Item</x-slot:title>
        @include('admin.glossary.form', ['route' => route('admin.glossary.store')])
    </x-entities.create>

@endsection
