<div class="jobs-index-container py-4">
    <div class="jobs-filters jobs-admin-filters pt-2">
        <div class="mb-4">
            <input wire:model.lazy="search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="mb-4">
            <div class="h5">Status</div>
            @foreach ($statusOptions as $status)
                <div class="form-check me-3">
                    <input wire:model.lazy="filters.status" class="form-check-input" type="checkbox" value="{{ $status }}" id="filter-status-{{ $status }}">
                    <label class="form-check-label" for="filter-status-{{ $status }}">
                        {{ $status }}
                    </label>
                </div>
            @endforeach
        </div>
        <div class="mb-4">
            <div class="h5">Type</div>
            @foreach ($typeOptions as $type)
                <div class="form-check me-3">
                    <input wire:model="filters.type" class="form-check-input" type="checkbox" value="{{ $type }}" id="filter-type-{{ $type }}">
                    <label class="form-check-label" for="filter-type-{{ $type }}">
                        {{ $type }}
                    </label>
                </div>
            @endforeach
        </div>
        <div class="mb-4">
            <div class="h5">Job Category</div>
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
        <div class="mb-4">
            <div class="h5 form-check">
                <input wire:model="filters.remote" class="form-check-input" type="checkbox" id="filter-remote">
                <label class="form-check-label fw-bold" for="filter-remote">
                    Remote
                </label>
            </div>
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm btn-dark">Reset Filters</button>
        </div>
    </div>

    <div class="jobs-container flex-grow-1">
        @include('components.admin.jobs.job-current-filters')
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Job Title</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Category</th>
                    <th>Hiring Company</th>
                    <th>Salary</th>
                    <th></th>
                </tr>
            </thead>

                <tbody>
                    @forelse($records as $job)
                        <x-admin.jobs.table-row-data :job="$job" />
                    @empty
                        <tr>
                            <td>No records match your query</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between my-4">
            <div class="d-flex align-items-center me-4">
                <select wire:model="perPage" id="perPage" class="form-select">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <label for="perPage" class="d-block fw-bold ms-2 flex-shrink-0">Per Page</label>
            </div>

            {{ $records->links() }}
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="jobDetailsModal" tabindex="-1" aria-labelledby="jobDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                @if ($selectedJob)
                    <div class="modal-header">
                        <h4 class="modal-title" id="jobApplicationModalLabel">{{ $selectedJob->name }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="lead mb-0 text-muted">{{ $selectedJob->owner->name }}</p>
                        <x-entities.data.job-data :job="$selectedJob" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
