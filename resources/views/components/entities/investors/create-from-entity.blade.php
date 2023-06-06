<div class="me-3">
    <button type="button" class="btn btn-quintary" data-bs-toggle="modal" data-bs-target="#createInvestorModal">
        Make Investor
    </button>
    <div class="modal fade" id="createInvestorModal" tabindex="-1" aria-labelledby="createInvestorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-4" id="createInvestorModalLabel">Create Investor</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.investors.create-from-entity') }}" method="POST">
                        @csrf
                        <input type="hidden" name="entity_type" value="{{ $entityType }}">
                        <input type="hidden" name="entity_id" value="{{ $entityId }}">
                        <div class="mb-4 d-flex">
                            <label for="type" class="form-label fw-bold me-2">Type</label>
                            <select class="form-select width-auto" id="type" name="type" required>
                                <option selected disabled>Select type</option>
                                @foreach(\App\Models\Investor::TYPES as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-quintary me-3">Make Investor</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
