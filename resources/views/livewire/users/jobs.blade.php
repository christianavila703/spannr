<div>
    <table class="table align-middle">
        <thead>
            <tr>
                <th class="sortable-column">
                    <span>Date</span>
                    <x-tables.sortable-button columnName="posted_date" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th class="sortable-column">
                    <span>Job Title</span>
                    <x-tables.sortable-button columnName="name" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
                </th>
                <th>Details</th>
                <th></th>
                <th class="sortable-column">
                    <span>Salary</span>
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
                    <div class="max-width-450">{{ $job->name }}</div>
                </td>
                <td>
                    <div class="max-width-300">
                        {{ $job->category->name }}
                    </div>
                    <div class="text-body-secondary">
                        {{ $job->type }}
                    </div>
                </td>
                <td>
                    @if($job->locations->count() > 0)
                        @foreach($job->locations as $location)
                            <div>{{ $location->name }}</div>
                        @endforeach
                    @endif
                    @if($job->remote)
                        <div class="m-0 text-quaternary">Remote / Virtual</div>
                    @endif
                </td>
                <td>
                    @if($job->salary_min)
                        ${{ number_format($job->salary_min) }}{{$job->salary_max ? ' - ' . number_format($job->salary_max) : null }}
                    @endif
                    @if($job->hourly)
                        ${{ number_format($job->hourly) }} / hour
                    @endif
                </td>
                <td>
                    @if($job->monthly)
                        <div>
                            <div class="text-body-secondary">
                                {{ $job->jobSubscription->card_name ?? 'N/A' }}
                            </div>
                            <div class="mb-1">Next Charge: <span class="text-success">{{ $job->jobSubscription->pretty_current_period_end ?? '' }}</span></div>
                            <div>
                                <a href="{{ route('users.jobs.update-card', $job->id) }}" class="btn btn-tertiary btn-sm me-2">Update Card</a>
                                <form class="d-inline-block" action="{{ route('users.jobs.unsubscribe', $job->id) }}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm confirm-button">Cancel</button>
                                </form>
                            </div>
                        </div>
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
