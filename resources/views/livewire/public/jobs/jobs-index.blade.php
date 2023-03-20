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
                                        <div wire:key="type-{{ $id }}" class="form-check">
                                            <input wire:model="filters.type" class="form-check-input" type="checkbox" value="{{ $option }}" id="filter-type-{{ $id }}">
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
                                        <div wire:key="category-{{ $id }}" class="form-check">
                                            <input wire:model="filters.category" class="form-check-input" type="checkbox" value="{{ $option }}" id="filter-category-{{ $id }}">
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
                    <div data-bs-toggle="offcanvas" data-bs-target="#job-description-{{ $job->slug }}" aria-controls="job-description-{{ $job->slug }}" class="job-posting-item text-dark text-decoration-none">
                        <div class="job-posting-item-content">
                            <h2 class="job-title">
                                <span>{{ $job->name }}</span>
                            </h2>
                            <p class="lead mb-2">{{ $job->owner->name }}</p>
                            <x-content.jobs.job-summary :job="$job" />
                            <div class="d-flex align-items-end">
                                <div class="mt-3 lead me-3">
                                    <span class="badge mt-3 bg-secondary">{{ $job->type }}</span>
                                </div>
                                <div>
                                    @if($job->remote)
                                        <div class="mt-3 lead">
                                            <span class="badge mt-3 text-dark bg-info">Remote</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="job-description-offcanvas offcanvas offcanvas-bottom" tabindex="-1" id="job-description-{{ $job->slug }}"
                         aria-labelledby="job-description-{{ $job->slug }}-label">
                        <div class="offcanvas-header border-bottom border-dark">
                            <div class="offcanvas-header-container">
                                <div class="h4 mt-1 mb-0 ps-lg-3" id="job-description-{{ $job->slug }}-label">{{ $job->name }}</div>
                                <div class="pe-2">
                                    <button type="button" class="btn" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-sharp fa-regular fa-xmark fa-lg"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="offcanvas-body">
                            <div class="pb-5 px-lg-3">
                                <div class="my-4">
                                    <a href="{{ route('public.content.jobs.apply', $job->slug) }}"
                                       class="btn btn-lg btn-neon-green" target="_blank" rel="noopener noreferrer">Apply Now</a>
                                </div>
                                <p class="lead mb-0">{{ $job->owner->name }}</p>
                                <div class="mt-4">
                                    <x-content.jobs.job-summary :job="$job" />
                                </div>
                                <div class="mt-5">
                                    <h2 class="h5 border-bottom border-dark">Job Description</h2>
                                    <div>
                                        @if ($job->description)
                                            <div>{!! $job->description !!}</div>
                                        @else
                                            <div>{!! $job->trixRender('description') !!}</div>
                                        @endif
                                    </div>
                                    @if ($job->qualifications)
                                        <h2 class="h5 border-bottom border-dark">Minimum Qualifications</h2>
                                        <div>{!! $job->qualifications !!}</div>
                                    @endif
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('public.content.jobs.apply', $job->slug) }}"
                                       class="btn btn-lg btn-neon-green" target="_blank" rel="noopener noreferrer">Apply Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
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
