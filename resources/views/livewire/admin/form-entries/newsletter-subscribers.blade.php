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
            <th>Subscriber</th>
            <th>URL</th>
            <th>MailChimp Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            @forelse($formEntries as $formEntry)
                <tr>
                    <td class="text-nowrap">{{ $formEntry->formatted_date }}</td>
                    <td class="text-nowrap">
                        <span class="d-block">{{ $formEntry->name }}</span>
                        {!! $formEntry->email_link !!}
                    </td>
                    <td>
                        <input type="text" class="form-control" value="{{ $formEntry->url }}">
                    </td>
                    <td>{{ $formEntry->mailchimp_status }}</td>
                    <td>
                        <a class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#form-{{ $formEntry->id }}">
                            View MailChimp Response
                        </a>
                    </td>
                </tr>
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
            @empty
                <tr>
                    <td colspan="4">No entries match your query</td>
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
