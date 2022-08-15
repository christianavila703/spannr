<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="me-4 d-flex align-items-center">
            <strong class="d-block me-3">Type</strong>
            @foreach ($typeOptions as $type)
                <div class="form-check me-3">
                    <input wire:model="filters.selected-types" class="form-check-input" type="checkbox" value="{{ $type }}" id="filter-type-{{ $type }}" @if(in_array($type, $filters['selected-types'])) checked @endif>
                    <label class="form-check-label" for="filter-type-{{ $type }}">
                        {{ $type }}
                    </label>
                </div>
            @endforeach
        </div>
        <div class="me-4 d-flex align-items-center">
            <strong class="d-block me-3">Status</strong>
            @foreach ($statusOptions as $status)
                <div class="form-check me-3">
                    <input wire:model="filters.selected-status" class="form-check-input" type="checkbox" value="{{ $status }}" id="filter-status-{{ $status }}" @if(in_array($status, $filters['selected-status'])) checked @endif>
                    <label class="form-check-label" for="filter-status-{{ $status }}">
                        {{ $status }}
                    </label>
                </div>
            @endforeach
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="ms-auto lead">
            {{ $jobs->total() }} results
        </div>
    </div>
    <table class="table align-middle">
        <thead>
        <tr>
            <th>Date</th>
            <th>Job Title</th>
            <th>Type</th>
            <th>Location</th>
            <th>Category</th>
            <th>Owner</th>
            <th>Salary</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($jobs as $job)
            <tr>
                <td class="text-nowrap">
                    {{ Carbon\Carbon::parse($job->posted_date)->format('M d, Y') }}
                </td>
                <td class="text-nowrap">
                    <a href="{{ route('admin.jobs.show', $job->slug) }}">{{ $job->name }}</a>
                    @if ($job->status === \App\Models\Job::STATUS_ARCHIVED)
                        <span class="badge bg-gray-medium text-dark ms-2">
                            Archived
                            @if ($job->exp_date)
                                {{ $job->pretty_exp_date }}
                            @endif
                        </span>
                    @endif
                </td>
                <td>{{ $job->type }}</td>
                <td>
                    @if($job->remote)
                        <p class="m-0 small text-quaternary">Remote / Virtual</p>
                    @endif
                    @if($job->locations->count() > 0)
                        <x-entities.show.relationship-list :records="$job->locations" class="small" />
                    @endif
                </td>
                <td>
                    <div class="max-width-300">
                        {{ $job->category->name }}
                    </div>
                </td>
                <td>
                    <a href="{{ $job->owner_show_link }}">
                        {{ $job->owner->name }}
                    </a>
                </td>
                <td>
                    @if($job->salary)
                        ${{ number_format($job->salary, 0) }}
                    @endif
                    @if($job->hourly)
                        ${{ number_format($job->hourly, 0) }} / hour
                    @endif
                </td>
                <td class="text-right">
                    <a href="{{ route('admin.jobs.edit', $job->id) }}">edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

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

        {{ $jobs->links() }}
    </div>
</div>
