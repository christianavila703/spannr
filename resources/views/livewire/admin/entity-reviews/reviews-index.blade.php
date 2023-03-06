<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="me-4 d-flex align-items-center">
            <strong class="d-block me-3">With priority:</strong>
            @foreach (\App\Models\EntityReviewNote::PRIORITIES as $priority => $label)
                <div class="form-check me-3">
                    <input wire:model="filters.priorities" class="form-check-input" type="checkbox"
                           value="{{ $priority }}" id="filter-priorities-{{ $priority }}"
                           @if(in_array($priority, $filters['priorities'])) checked @endif>
                    <label class="form-check-label" for="filter-priorities-{{ $priority }}">
                        {{ $label }}
                    </label>
                </div>
            @endforeach
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="ms-auto lead">
            {{ $items->total() }} results
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th class="sortable-column">Name
                <x-tables.sortable-button columnName="name" sortColumnName="{{ $sortColumnName }}"
                                          sortDirection="{{ $sortDirection }}"/>
            </th>
            <th class="sortable-column max-width-200">Updated at
                <x-tables.sortable-button columnName="updated_at" sortColumnName="{{ $sortColumnName }}"
                                          sortDirection="{{ $sortDirection }}"/>
            </th>
            <th class="max-width-200">Reviews</th>
            <th class="max-width-200">Priority</th>
            <th class="max-width-200">Notes</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td class="text-nowrap">{{ $item->name }}</td>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->reviews_count }} of {{ $requiredReviewers }}</td>
                <td>
                    @if($item->reviewNote && $item->reviewNote->priority)
                        <span
                            class="{{ $item->reviewNote->priority_color_class }}">{{ \App\Models\EntityReviewNote::PRIORITIES[$item->reviewNote->priority] }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-sm {{ $item->reviewNote ? 'btn-tertiary' : 'btn-secondary' }}"
                            data-bs-toggle="modal"
                            data-bs-target="#note-modal-{{ $item->id }}">
                        {{ $item->reviewNote ? 'Show' : 'Add' }}
                    </button>

                    <div class="modal fade" id="note-modal-{{ $item->id }}" tabindex="-1"
                         aria-labelledby="note-modal-{{ $item->id }}"
                         aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <h4 class="modal-title h4">Note for {{ $item->name }}</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <form action="{{ route('admin.entity-reviews.note.add') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="entity_id" value="{{ $item->id }}"/>
                                        <input type="hidden" name="entity_type" value="{{ $item::class }}"/>
                                        @if($item->reviewNote)
                                            <p><b>Reviewer:</b> {{ $item->reviewNote->reviewer->name }}</p>
                                        @endif
                                        <div class="form-group mb-2">
                                            <label>Text:</label>
                                            <textarea class="form-control" rows="6"
                                                      name="text"
                                                      required>{!! $item->reviewNote ? $item->reviewNote->text : '' !!}</textarea>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Priority:</label>
                                            <select class="form-control" name="priority">
                                                <option></option>
                                                @foreach(\App\Models\EntityReviewNote::PRIORITIES as $value => $label)
                                                    <option
                                                        value="{{ $value }}" {{ $item->reviewNote && $item->reviewNote->priority === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                    @if($item->reviewNote)
                                        <form class="form-inline"
                                            action="{{ route('admin.entity-reviews.note.delete', $item->reviewNote->id) }}"
                                            method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="mt-2 btn btn-danger confirm-button">Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-end">
                    <a href="{{ route($entityRouteNames['show'], $item->{$item::$routeParameterField}) }}"
                       class="btn btn-sm btn-primary me-2 mb-1">Show</a>
                    <form class="d-inline" action="{{ route('admin.entity-reviews.reviews.approve') }}" method="post">
                        @csrf
                        <input type="hidden" name="entity_type" value="{{ $item::class }}">
                        <input type="hidden" name="entity_id" value="{{ $item->id }}">
                        <button type="submit" class="btn btn-sm btn-success me-2 mb-1 confirm-button">Approve</button>
                    </form>
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

        {{ $items->links() }}
    </div>
</div>

</div>
