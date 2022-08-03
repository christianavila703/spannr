@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.investors.index') }}" backLabel="Investors">
        <x-slot:title>Add Investor</x-slot:title>
        @include('admin.investors.form', ['route' => route('admin.investors.store')])
    </x-entities.create>

@endsection
