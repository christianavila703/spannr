<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
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
            <th class="sortable-column">Name</th>
            <th class="sortable-column max-width-200">Review date</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td class="text-nowrap">{{ $item->name }}</td>
                <td>{{ $item->reviews[0]->updated_at }}</td>
                <td class="text-end">
                    <a href="{{ route($entityRouteNames['show'], $item->{$item::$routeParameterField}) }}"
                       class="btn btn-sm btn-primary me-2 mb-1">Show</a>
                    <form class="d-inline" action="{{ route('admin.entity-reviews.audits.approve') }}" method="post">
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
