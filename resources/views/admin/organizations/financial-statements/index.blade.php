@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Organization's Financial Statements</h1>

        <ul class="list-group">
        @foreach($organizations as $organization)
            <li class="list-group-item">
                <a href="{{ route('admin.organizations.financial-statements.show', $organization->slug) }}">
                    {{ $organization->name }}
                </a>
            </li>
        @endforeach
        </ul>
    </div>
@endsection

