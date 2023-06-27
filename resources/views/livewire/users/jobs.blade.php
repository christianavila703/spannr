<div>
    <table class="table align-middle">
        <thead>
            <tr>
                <th class="sortable-column">Date
                    <x-tables.sortable-button columnName="posted_date" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th class="sortable-column">Job Title
                    <x-tables.sortable-button columnName="name" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th class="sortable-column">Type
                    <x-tables.sortable-button columnName="type" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th>Location</th>
                <th>Category</th>
                <th class="sortable-column">Salary
                    <x-tables.sortable-button columnName="salary_min" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th>Subscription</th>
            </tr>
        </thead>

        <tbody>
        @foreach($jobs as $job)
            <tr>
                <td class="text-nowrap">
                    {{ Carbon\Carbon::parse($job->posted_date)->format('M d, Y') }}
                </td>
                <td>
                    {{ $job->name }}
                </td>
                <td>{{ $job->type }}</td>
                <td>
                    @if($job->remote)
                        <span class="m-0 small text-quaternary">Remote / Virtual</span>
                    @endif
                    @if($job->locations->count() > 0)
                        @foreach($job->locations as $location)
                            <span class="small">{{ $location->name }}</span>
                        @endforeach
                    @endif
                </td>
                <td>
                    <div class="max-width-300">
                        {{ $job->category->name }}
                    </div>
                </td>
                <td>
                    @if($job->salary_min)
                        ${{ number_format($job->salary_min, 0) }}{{$job->salary_max ? ' - ' . number_format($job->salary_max, 0) : null }}
                    @endif
                    @if($job->hourly)
                        ${{ number_format($job->hourly, 0) }} / hour
                    @endif
                </td>
                <td>
                    @if($job->monthly)
                        <span class="text-success">Active</span>
                        <form class="d-inline-block" action="{{ route('users.jobs.unsubscribe', $job->id) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-link confirm-button">Cancel</button>
                        </form>
                    @else
                        <span class="text-secondary">No subscription</span>
                    @endif
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
