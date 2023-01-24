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
            <th class="sortable-column" style="width: 200px">Created
                <x-tables.sortable-button columnName="created_at" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}"/>
            </th>
            <th class="sortable-column" style="width: 300px">Name
                <x-tables.sortable-button columnName="name" sortColumnName="{{ $sortColumnName }}" sortDirection="{{ $sortDirection }}" />
            </th>
            <th>Excerpt</th>
            <th class="text-end">Word Count</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td class="text-nowrap">
                    {{ $item->formatted_created_date }}
                </td>
                <td>
                    <div class="max-width-300">
                        <a href="{{ $item->url }}">{{ $item->name }}</a>
                    </div>
                </td>
                <td>
                    <div class="max-width-500 small">
                        {{ $item->excerpt }}
                    </div>
                </td>
                <td class="text-end">
                    {{ $item->content_word_count }}
                </td>
                <td class="ps-5 text-end">
                    <a href="{{ route('admin.glossary.edit', $item->id) }}" class="btn btn-sm btn-primary">edit</a>
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
