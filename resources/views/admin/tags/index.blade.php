@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Tags</h1>

        @include('admin.includes.status-messages')

        <div class="row">
            <div class="col-12">
                <livewire:admin.tags.index />
            </div>
            <div class="col-12 col-md-6">
                <ul class="list-group list-group-flush">
                    @foreach ($tags as $firstLevelTag)
                        <li class="list-group-item pb-4 mb-4 px-0 border-0">
                            <div class="mb-1">
                                <a href="{{ route('admin.tags.show', $firstLevelTag->slug) }}" class="h5 d-block">{{ $firstLevelTag->name }}</a>
                            </div>
                            @if($firstLevelTag->children->count() > 0)
                                <ul class="list-group">
                                    @foreach ($firstLevelTag->children as $secondLevelTag)
                                        <a href="{{ route('admin.tags.show', $secondLevelTag->slug) }}" class="list-group-item list-group-item-action">{{ $secondLevelTag->name }}</a>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            <div id="add-tag" class="col-12 col-md-6 col-lg-5 offset-lg-1">
                <h2 class="h4">Add a Tag</h2>
                @include('admin.tags.form', ['route' => route('admin.tags.store')])
            </div>
        </div>
    </div>
@endsection
