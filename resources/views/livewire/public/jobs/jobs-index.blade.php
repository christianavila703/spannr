<div>
    <div class="jobs-index-container py-4">
        <div class="jobs-filters pt-2">
            <button class="btn btn-primary d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#jobFiltersMenu" aria-controls="jobFiltersMenu">
                Toggle Filters
            </button>
            <div wire:ignore>
                <div class="offcanvas-xl offcanvas-start" tabindex="-1" id="jobFiltersMenu" aria-labelledby="jobFiltersMenuLabel">
                    <div class="offcanvas-header">
                        <h3 class="h4 offcanvas-title mt-1" id="jobFiltersMenuLabel">Filters</h3>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" data-bs-target="#jobFiltersMenu" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <div>
                            <div class="filter-item d-flex align-items-center">
                                <input wire:model.lazy="search" type="text" class="form-control" placeholder="Search...">
                                <div class="ms-2" style="width: 3rem">
                                    @if($search)
                                        <button wire:click="resetSearch" class="btn btn link text-danger">
                                            <i class="fa-sharp fa-regular fa-xmark fa-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-item mt-4">
                                <div class="h5 form-check">
                                    <input wire:model="filters.remote" class="form-check-input" type="checkbox" id="filter-remote">
                                    <label class="form-check-label fw-bold" for="filter-remote">
                                        Remote
                                    </label>
                                </div>
                            </div>
                            <div class="filter-item mt-4">
                                <div class="h6">Job Type</div>
                                <div>
                                    @foreach($typeOptions as $id => $option)
                                        <div class="form-check">
                                            <input wire:model="filters.type.{{ $option }}" class="form-check-input" type="checkbox" value="{{ $option }}" id="filter-type-{{ $id }}">
                                            <label class="form-check-label" for="filter-type-{{ $id }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="filter-item mt-4">
                                <div class="h6">Job Category</div>
                                <div class="long-list-expandable">
                                    @foreach($categoryOptions as $id => $option)
                                        <div class="form-check">
                                            <input wire:model="filters.category.{{ $option }}" class="form-check-input" type="checkbox" value="{{ $option }}" id="filter-category-{{ $id }}">
                                            <label class="form-check-label" for="filter-category-{{ $id }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="py-3">
                                <button wire:click="resetFilters" class="btn btn-sm  btn-dark">Clear Filters</button>
                            </div>
                            <div class="mt-5 bg-light border border-quaternary border-3 p-4">
                                <p class="h4 mb-4">Are you hiring in the longevity industry?</p>
                                <a href="{{ route('public.content.jobs.submit') }}" class="btn btn-lg btn-tertiary">Post Job</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="jobs-container flex-grow-1">
            @include('components.admin.jobs.job-current-filters')
            @forelse($records as $job)
                <div wire:key="job-{{ $job->slug }}" class="@if(!$loop->last) mb-5 @endif">
                    @include('public.content.jobs._job-card')
                </div>
            @empty
                <div>
                    No jobs match your query
                </div>
            @endforelse
        </div>
    </div>

    <div class="text-center mt-5">
        {{ $records->links() }}
    </div>
</div>
