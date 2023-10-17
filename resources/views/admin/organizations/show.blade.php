@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$organization->generateBreadcrumbs()"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:buttons>
            @if($organization->isLocked && $organization->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$organization" />
            @else
                <a href="{{ route('admin.organizations.edit', $organization->id) }}" class="btn btn-info me-3">Edit</a>
            @endif
            @if ($organization->ownedInvestor)
                <a href="{{ route('admin.investors.show', $organization->ownedInvestor->slug) }}" class="btn btn-quintary me-3">
                    <i class="{{ $organization->investor_icon }} me-1"></i>Investor
                </a>
            @else
                <x-entities.investors.create-from-entity entityType="{{ \App\Models\Organization::class }}" entityId="{{ $organization->id }}" />
            @endif
            @if($organization->financialStatements->count())
                <a href="{{ route('admin.organizations.financial-statements.show', $organization->slug) }}" class="btn btn-quintary me-3">Financial Statements</a>
            @endif

            @if($organization->stock)
                <a href="{{ route('public.content.organizations.show', $organization->slug) }}" class="btn btn-quaternary me-3">View Public</a>
            @endif
            <div class="dropdown mb-0">
                <button class="btn btn-tertiary dropdown-toggle" type="button" id="add-relationship" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-plus me-1"></i>Add
                </button>
                <ul class="dropdown-menu" aria-labelledby="add-relationship">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addInvestorForm">Investors</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.organizations.people', $organization->slug) }}" >People</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addFundraisingRoundForm">Fundraising Round</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.locations.attach', ['organizations', $organization->id]) }}">Location</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addTagForm">Tags</a></li>
                    @if($organization->type == \App\Models\Organization::TYPE_PUBLIC)
                        @if($organization->stock)
                            <li><a class="dropdown-item" href="{{ route('admin.organizations.stock.edit', $organization->stock->id) }}">Edit Stock</a></li>
                        @else
                            <li><a class="dropdown-item" href="{{ route('admin.organizations.stock.add', $organization->slug) }}">Stock</a></li>
                        @endif
                    @endif
                </ul>
            </div>

            @include('admin.entity-reviews.includes.entity-form', ['entity' => $organization])
        </x-slot:buttons>
        <x-slot:title>{{ $organization->name }}</x-slot:title>
        <x-entities.data.organization :organization="$organization"  showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
    </x-entities.show>

    @include('admin.organizations.investors.add')
    @include('admin.organizations.people.add')
    @include('admin.organizations.fundraising-rounds.add')

    {{-- organization tags form modal --}}
    <div class="modal fade" id="addTagForm" tabindex="-1" aria-labelledby="addTagFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.organizations.tags.update', $organization->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTagFormLabel">Add Tags to {{ $organization->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="min-height: 400px">
                        <div class="form-group mb-3">
                            <label for="longevity">Longevity related</label>
                            <select class="form-select" id="longevity" name="longevity" aria-label="Select longevity" required>
                                <option selected disabled>Select Longevity</option>
                                <option value="1" {{ $organization->longevity === 1 ? 'selected' : null }}>Yes</option>
                                <option value="0" {{ $organization->longevity === 0 ? 'selected' : null }}>No</option>
                            </select>
                        </div>

                        <div id="organization_tags_block" @if($organization->longevity !== 1) style="display: none;" @endif>
                            <div class="form-group mb-3">
                                <label for="longevity">Parent Tag</label>
                                <select class="form-select" id="parent_tag" name="parent_tag" aria-label="Parent Tag">
                                    <option selected disabled>Select Parent Tag</option>
                                    @foreach($parentTags as $id => $name)
                                        <option value="{{ $id }}" @if($currentTags && in_array($id, $currentTags)) selected @endif >
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="organization_child_tags_block" class="form-group mb-3" @if(empty($childTags)) style="display: none;" @endif>
                                <label for="child_tags" class="visually-hidden">Child Tags</label>
                                <select class="form-select" id="child_tags" name="child_tags[]" aria-label="Child Tags" multiple data-url="{{ route('admin.json.childTagsByParentId') }}">
                                    <option value="" disabled>Child Tags</option>
                                    @if(isset($childTags))
                                        @foreach($childTags as $id => $name)
                                            <option value="{{ $id }}" @if($currentTags && in_array($id, $currentTags)) selected @endif >
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('end')
    @vite('resources/js/organization-form-tags.js')
@endpush
