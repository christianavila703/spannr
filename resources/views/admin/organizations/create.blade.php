@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.organizations.index') }}" backLabel="Organizations">
        <x-slot:title>Add Organization</x-slot:title>
        @include('admin.organizations.form', ['route' => route('admin.organizations.store')])
    </x-entities.create>

@endsection
