<div>
    <div class="d-md-flex flex-wrap mt-4">

        <div class="me-md-5 mb-3">
            <x-livewire-filters.search label="Search" placeholder="Search" search="{{ $search }}" tooltip="Search by name, entity, status" />
        </div>

        <div class="filter-widget me-md-5 mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-md @if($filters['status']) btn-accent @else btn-primary @endif btn-primary dropdown-toggle rounded-0" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter by Status
                </button>
                <ul class="dropdown-menu" style="min-width: 220px">
                    @foreach ($statusOptions as $optionId => $optionName)
                        <li class="px-3">
                            <div class="form-check form-check-small form-check-inline">
                                <input wire:model="filters.status" class="form-check-input" type="checkbox" value="{{ $optionName }}"
                                       id="filter-status-{{ $optionId }}" @if(in_array($optionName, $filters['status'])) checked @endif>
                                <label class="form-check-label @if(in_array($optionName, $filters['status'])) fw-bold @endif" for="filter-status-{{ $optionId }}">
                                    <span>{{ ucwords($optionName) }}</span>
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="filter-widget me-md-5 mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-md @if($filters['type']) btn-accent @else btn-primary @endif btn-primary dropdown-toggle rounded-0" data-bs-toggle="dropdown" aria-expanded="false">
                    Filter by Type
                </button>
                <ul class="dropdown-menu" style="min-width: 220px">
                    @foreach ($typeOptions as $optionId => $optionName)
                        <li class="px-3">
                            <div class="form-check form-check-small form-check-inline">
                                <input wire:model="filters.type" class="form-check-input" type="checkbox" value="{{ $optionName }}"
                                       id="filter-type-{{ $optionId }}" @if(in_array($optionName, $filters['type'])) checked @endif>
                                <label class="form-check-label @if(in_array($optionName, $filters['type'])) fw-bold @endif" for="filter-type-{{ $optionId }}">
                                    <span>{{ ucwords($optionName) }}</span>
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="filter-widget ms-auto">
            <button wire:click="clearFilters" class="btn btn-sm btn-dark rounded-0">Clear Filters</button>
        </div>
    </div>
    <div class="d-md-flex flex-wrap">
        <div class="me-md-4 mb-3">
            <x-livewire-filters.checkbox-single wireModel="filters.errors-exception" id="filter-errors-exception" label="Has Exception" />
        </div>
        <div class="me-md-4 mb-3">
            <x-livewire-filters.checkbox-single wireModel="filters.errors-tags" id="filter-errors-tags" label="Has Tag Errors" />
        </div>
        <div class="me-md-4 mb-3">
            <x-livewire-filters.checkbox-single wireModel="filters.errors-address" id="filter-errors-address" label="Has Address Errors" />
        </div>
        <div class="mb-3">
            <x-livewire-filters.checkbox-single wireModel="filters.errors-email" id="filter-errors-email" label="Has Email Errors" />
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <div class="mb-3">
                <strong>{{ number_format($records->total(), 0) }}</strong> total records
            </div>
            <div class="ps-2 d-flex align-items-center">
                <div class="form-check me-4">
                    <input wire:model="selectPage" class="form-check-input" type="checkbox" id="select-page" aria-label="Select">
                    @if($selectAll)
                        <label for="select-page" class="ps-2"><strong>{{ $records->total() }}</strong> selected</label>
                    @else
                        <label for="select-page" class="ps-2"><strong>{{ count($selected) }}</strong> selected</label>
                    @endif
                </div>
                <div class="me-4">
                    @if ($selectPage)
                        @unless ($selectAll)
                            <div>
                                <button wire:click="selectAll" class="btn btn-link p-0">Select everything?</button>
                            </div>
                        @else

                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div wire:ignore class="dropdown">
            <button class="btn btn-secondary rounded-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Bulk Actions
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a wire:click="processImports" class="dropdown-item" href="#">
                        <i class="fa-sharp fa-solid fa-code fa-fw me-1"></i>Process imports
                    </a>
                </li>
                <li>
                    <a wire:click="makePublic" class="dropdown-item" href="#">
                        <i class="fa-sharp fa-solid fa-upload fa-fw me-1"></i>Make Public
                    </a>
                </li>
                <li>
                    <a wire:click="deleteImports" class="dropdown-item" href="#">
                        <i class="fa-sharp fa-solid fa-trash fa-fw me-1"></i>Delete imports
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th style="width: 32px"></th>
                    <th><x-livewire-filters.sort-button label="ID" field="id" :sorts="$sorts" /></th>
                    <th><x-livewire-filters.sort-button label="Date" field="created_at" :sorts="$sorts" /></th>
                    <th><x-livewire-filters.sort-button label="Type" field="type" :sorts="$sorts" /></th>
                    <th><x-livewire-filters.sort-button label="Status" field="status" :sorts="$sorts" /></th>
                    <th>Entity</th>
                    <th>Errors</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr wire:key="record-{{ $record->id }}">
                        <td>
                            <div class="form-check">
                                <input wire:model="selected" class="form-check-input" type="checkbox" value="{{ $record->id }}" id="select-{{ $record->id }}" aria-label="Select">
                            </div>
                        </td>
                        <td>{{ $record->id }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.import.listings.show', $record->id) }}">{{ $record->created_at }}</a>
                        </td>
                        <td class="text-nowrap">{{ $record->type }}</td>
                        <td class="text-nowrap">
                            <span class="{{ $record->status_color }}">{{ $record->status }}</span>
                        </td>
                        <td class="text-nowrap">
                            @if($record->entity)
                                <span class="badge {{ $record->entity->status_color }} me-2">{{ $record->entity->status }}</span>
                                <a href="{{ $record->entity_url }}">{{ $record->entity->name }}</a>
                            @endif
                        </td>
                        <td>
                            @if($record->exception_errors)
                                <div class="small">{{ count($record->exception_errors) }} Exception</div>
                            @endif
                            @if($record->tag_errors)
                                <div class="small">{{ count($record->tag_errors) }} Tags</div>
                            @endif
                            @if($record->address_errors)
                                <div class="small">{{ count($record->address_errors) }} Addresses</div>
                            @endif
                            @if($record->email_errors)
                                <div class="small">{{ count($record->email_errors) }} Emails</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="99">No records match your query</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="my-5 d-flex flex-wrap justify-content-between">
        <div>

        </div>
        <div>
            {{ $records->links() }}
        </div>
    </div>
</div>
