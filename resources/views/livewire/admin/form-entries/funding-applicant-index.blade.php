<div>
    <div class="my-2 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="ms-auto lead">
            {{ $formEntries->total() }} results
        </div>
    </div>
    <table class="table align-middle">
        <thead>
        <tr>
            <th>Date/Time</th>
            <th>Name</th>
            <th>Email</th>
            <th>MailChimp Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($formEntries as $formEntry)
            <tr>
                <td class="text-nowrap">{{ $formEntry->formatted_date }}</td>
                <td class="text-nowrap">{{ $formEntry->name }}</td>
                <td class="text-nowrap">{!! $formEntry->email_link !!}</td>
                <td>{{ $formEntry->mailchimp_status }}</td>
                <td>
                    <a class="btn btn-sm btn-link" data-bs-toggle="modal"
                       data-bs-target="#form-fields-{{ $formEntry->id }}">
                        Show all fields
                    </a>

                    <div class="modal fade" id="form-fields-{{ $formEntry->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Funding Applicant Form
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @foreach(json_decode($formEntry->fields) as $field => $value)
                                        <p><b>{{ ucfirst(str_replace('_', ' ', $field)) }}:</b> {{ $value }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <a class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#form-{{ $formEntry->id }}">
                        View MailChimp Response
                    </a>

                    <div class="modal fade" id="form-{{ $formEntry->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        MailChimp Response for {{ $formEntry->email }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <pre class="small">{!! $formEntry->formatted_mailchimp_response !!}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No entries match your query</td>
            </tr>
        @endforelse
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

        {{ $formEntries->links() }}
    </div>
</div>
