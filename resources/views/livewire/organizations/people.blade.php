<div>
    <div wire:poll class="list-group">
        @foreach ($people as $person)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    {{ $person->name }} - <strong>{{ $person->pivot->role }}</strong>
                </div>
                <div>
                    @can('edit records')
                        <button wire:click="setDetachablePerson('{{ $person->id }}')" data-bs-toggle="modal" data-bs-target="#removePersonModal" class="btn btn-sm text-danger">
                            <i class="fa-duotone fa-trash-can me-1"></i>Remove
                        </button>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>

    <div wire:ignore.self class="modal fade" id="removePersonModal" tabindex="-1" aria-labelledby="removePersonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="py-2 lead">
                        Are you sure you want to remove this person?
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click.prevent="detachPerson()" type="button" class="btn btn-success" data-bs-dismiss="modal">Yes</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
</div>
