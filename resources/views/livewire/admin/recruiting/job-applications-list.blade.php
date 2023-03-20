<div>
     <div class="py-4">
        <div class="d-flex flex-wrap">
            <div class="mb-4 me-5 min-width-250">
                <input wire:model.lazy="search" type="text" class="form-control" placeholder="Search...">
            </div>
            <div class="mb-4 d-flex flex-wrap align-items-center me-5">
                <div class="h6 me-3 mb-0">Status</div>
                @foreach ($statusOptions as $status)
                    <div class="form-check form-check-inline @if(!$loop->last) me-3 @endif">
                        <input wire:model.lazy="filters.status" class="form-check-input" type="checkbox" value="{{ $status }}" id="filter-status-{{ $status }}">
                        <label class="form-check-label" for="filter-status-{{ $status }}">
                            {{ $status }}
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="mb-4 me-5">
                <div class="lead form-check">
                    <input wire:model="filters.recruiter" class="form-check-input" type="checkbox" id="filter-recruiter">
                    <label class="form-check-label fw-bold text-quaternary" for="filter-recruiter">
                        Open to Opportunities
                    </label>
                </div>
            </div>
            <div class="mb-4 ms-auto">
                <button wire:click="resetFilters" class="btn btn-sm btn-dark">Reset Filters</button>
            </div>
        </div>

         <div class="jobs-container flex-grow-1">
             <div class="table-responsive">
                 <table class="table">
                     <thead>
                        <tr>
                            <th>Date</th>
                            <th>Applicant</th>
                            <th>Status</th>
                            <th>Job</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Resume</th>
                            <th>Location</th>
                            <th>Message</th>
                            <th></th>
                        </tr>
                     </thead>
                     <tbody>
                         @forelse($records as $record)
                             <tr wire:key="record-{{ $record->id }}">
                                 <td>{{ $record->pretty_created_at }}</td>
                                 <td>
                                     <span>
                                         @if ($record->recruiter)
                                             <i class="fa-sharp fa-solid fa-square-star me-2 text-quaternary"></i>
                                         @else
                                             <i class="fa-sharp fa-solid fa-phone-xmark me-2 text-muted"></i>
                                         @endif
                                     </span>
                                     <a href="{{ route('admin.recruiting.applications', 'applicant=' . $record->person->id) }}">{{ $record->person->name }}</a>
                                 </td>
                                 <td>
                                     <div class="d-flex justify-content-between me-2">
                                         <span wire:poll.visible>
                                             <span class="badge {{ $record->statusBadge }}">{{ $record->status }}</span>
                                         </span>
                                         <button wire:click="editJobApplication('{{ $record->id }}')" data-bs-toggle="modal" data-bs-target="#editJobApplicationModal" class="ms-auto btn btn-sm text-primary">
                                             <i class="fa-sharp fa-solid fa-pen"></i>
                                         </button>
                                     </div>
                                 </td>
                                 <td>
                                     <div class="max-width-420">{{ $record->job->name }}</div>
                                     <div class="text-muted">{{ $record->job->owner->name }}</div>
                                 </td>
                                 <td>
                                    <a href="mailto:{{ $record->person->email }}">{{ $record->person->email }}</a>
                                 </td>
                                 <td>
                                     <a href="tel:{{ $record->person->phone }}">{{ $record->person->phone }}</a>
                                 </td>
                                 <td>
                                    <a href="{{ $record->getFirstMediaUrl('resume') }}" target="_blank" rel="noopener noreferrer">View Resume</a>
                                 </td>
                                 <td>{{ $record->location->name }}</td>
                                 <td>
                                    <div class="truncate-250 font-size-sm">
                                        {{ $record->message }}
                                    </div>
                                 </td>
                                 <td>
                                     <button wire:click="setJobApplication('{{ $record->id }}')" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#jobApplicationModal">
                                         View Details
                                     </button>
                                 </td>
                             </tr>
                         @empty
                            <tr wire:key="empty-records">
                                <td colspan="10">
                                    <div class="lead">No records match your query</div>
                                </td>
                            </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
         </div>
         <div class="d-flex justify-content-between mt-3">
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
        <div class="mt-4 d-flex flex-wrap bg-light p-3">
             <div class="me-5 text-quaternary">
                <i class="fa-sharp fa-solid fa-square-star me-2"></i><strong>Opted in to Recruiting Opportunities</strong>
            </div>
             <div class="text-muted">
                 <i class="fa-sharp fa-solid fa-phone-xmark me-2"></i><strong>Do Not Contact</strong>
             </div>
         </div>
    </div>

    <!-- Detail Modal -->
    <div wire:ignore.self class="modal fade" id="jobApplicationModal" tabindex="-1" aria-labelledby="jobApplicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            @if ($selectedPerson AND $selectedJobApplication)
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="jobApplicationModalLabel">{{ $selectedPerson->name }} - {{ $selectedJobApplication->job->name }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-xl-6 mb-5 mb-xl-0">
                                <div class="row">
                                    <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                                        <p class="border-bottom border-dark d-flex justify-content-between align-items-end">
                                            <span class="h4 mb-0" style="margin-top: .15rem">Applicant Details</span>
                                            @can('edit records')
                                                <a href="{{ route('admin.people.show', $selectedPerson->slug) }}" class="ms-4 btn btn-sm btn-link">View Person</a>
                                            @endcan
                                        </p>
                                        <x-entities.data.person-data :person="$selectedPerson" />
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <p class="border-bottom border-dark d-flex justify-content-between align-items-end">
                                            <span class="h4 mb-0" style="margin-top: .15rem">Application Details</span>
                                        </p>
                                        <div class="my-3"><strong>Location:</strong> {{ $selectedJobApplication->location->name }}</div>
                                        <div>{{ $selectedJobApplication->message }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <p class="border-bottom border-dark d-flex justify-content-between align-items-end">
                                    <span class="h4 mb-0" style="margin-top: .15rem">Job Details</span>
                                    @can('edit records')
                                        <a href="{{ route('admin.jobs.show', $selectedJobApplication->job->slug) }}" class="ms-4 btn btn-sm btn-link">View Job</a>
                                    @endcan
                                </p>
                                <p class="lead mb-0 text-muted">{{ $selectedJobApplication->job->name }} at {{ $selectedJobApplication->job->owner->name }}</p>
                                <x-entities.data.job-data :job="$selectedJobApplication->job" />
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Job Application Modal -->
    <div wire:ignore.self class="modal fade" id="editJobApplicationModal" tabindex="-1" aria-labelledby="editJobApplicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                @if ($selectedJobApplication)
                    <div class="modal-body">
                        <livewire:admin.recruiting.edit-job-application :jobApplication="$selectedJobApplication"/>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
