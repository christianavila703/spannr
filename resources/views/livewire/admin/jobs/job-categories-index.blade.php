<div>
    <div class="mb-4 d-flex align-items-center mt-3">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Clear Search</button>
        </div>
        <div class="ms-auto lead">
            {{ $jobCategories->total() }} results
        </div>
    </div>
    <div class="list-group list-group-flush">
         @forelse ($jobCategories as $jobCategory)
             <div class="list-group-item">
                 {{ $jobCategory->name }}
                 <span class="ml-2 text-muted">({{ $jobCategory->jobs_count }})</span>
             </div>
         @empty
            <div class="list-group-item">
                No job categories match your query
            </div>
         @endforelse

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

            {{ $jobCategories->links() }}
        </div>
    </div>
</div>
