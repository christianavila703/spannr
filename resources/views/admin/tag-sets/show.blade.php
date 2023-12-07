@extends('layouts.app')

@section('content')
    <div class="container">
        <livewire:admin.tag-sets.manage-tag-set :tagSet="$tagSet" />
    </div>
@endsection
