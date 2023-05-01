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
                <ul>
                    @foreach ($tags as $firstLevelTag)
                        <li class="py-1">
                            <a href="{{ route('admin.tags.show', $firstLevelTag->slug) }}" class="d-block">{{ $firstLevelTag->name }}</a>
                            @if($firstLevelTag->children->count() > 0)
                                <ul>
                                    @foreach ($firstLevelTag->children as $secondLevelTag)
                                        <li class="py-1">
                                            <a href="{{ route('admin.tags.show', $secondLevelTag->slug) }}" class="d-block">{{ $secondLevelTag->name }}</a>
                                        </li>
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
