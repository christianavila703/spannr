<div>
    <div class="mb-4 d-flex">
        <div class="me-4">
            <select wire:model="logName" class="form-select">
                <option value="" selected>All Logs</option>
                @foreach(\App\Models\Activity::LOG_OPTIONS as $logName)
                    <option value="{{ $logName }}">{{ ucwords($logName) }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex align-items-center me-4">
            <select wire:model="perPage" id="perPage" class="form-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label for="perPage" class="d-block fw-bold ms-2 flex-shrink-0">Per Page</label>
        </div>
        <div class="me-4">
            <input wire:model="search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Log</th>
            <th>Description</th>
            <th>Subject</th>
            <th>Causer</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($activities as $activity)
            <tr class="align-middle">
                <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('Y-m-d') }}</td>
                <td>{{ $activity->log_name }}</td>
                <td>{{ $activity->description }}</td>
                <td>
                    {!! $activity->subjectLink !!}
                </td>
                <td>
                    @if ($activity->causer)
                        {{ $activity->causer->name }}
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#activityModal-{{ $activity->id }}">
                        View Details
                    </button>
                </td>
            </tr>
            <div class="modal fade" id="activityModal-{{ $activity->id }}" tabindex="-1" aria-labelledby="activityModal-{{ $activity->id }}Label" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="activityModal-{{ $activity->id }}Label">Properties</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <pre>{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </tbody>
    </table>

    {{ $activities->links() }}
</div>
