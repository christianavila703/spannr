@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.content.index') }}" backLabel="Content" class="has-long-form">
        <x-slot:title>Add Content</x-slot:title>
        <div id="long-form-container">
            @include('admin.content.form', ['route' => route('admin.content.store')])
        </div>
    </x-entities.create>

@endsection

@push('end')

@endpush
