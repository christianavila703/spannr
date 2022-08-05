@extends('layouts.app')

@section('content')
    <div class="container">
        @include('admin.includes.status-messages')

        <h1 class="text-primary h4">Website Contact Forms</h1>
        <livewire:admin.form-entries.website-contact-index />

        <h1 class="text-primary h4 mt-5">Investor Opt-in Forms</h1>
        <livewire:admin.form-entries.investor-opt-in-index />
    </div>
@endsection
