<?php
/**
 * @var $entity \Illuminate\Database\Eloquent\Model
 */
?>
@can('review records')
    @if($entity->canBeReviewedByUser(auth()->user()->id))
        <form class="d-inline" action="{{ route('admin.entity-reviews.reviews.approve') }}" method="post">
            @csrf
            <input type="hidden" name="entity_type" value="{{ $entity::class }}">
            <input type="hidden" name="entity_id" value="{{ $entity->id }}">
            <button type="submit" class="btn btn-success ms-3 confirm-button">Approve review</button>
        </form>

        <button type="button" class="btn {{ $entity->reviewNote ? 'btn-tertiary' : 'btn-secondary' }} ms-3" data-bs-toggle="modal"
                data-bs-target="#note-modal-{{ $entity->id }}">
            {{ $entity->reviewNote ? 'Show note' : 'Add note' }}
        </button>

        <div class="modal fade js-review-note-modal" id="note-modal-{{ $entity->id }}" tabindex="-1"
             aria-labelledby="note-modal-{{ $entity->id }}"
             aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h4 class="modal-title h4">Note for {{ $entity->name }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <form action="{{ route('admin.entity-reviews.note.add') }}" class="js-entity-review-note-form" method="post">
                            @csrf
                            <input type="hidden" name="entity_id" value="{{ $entity->id }}"/>
                            <input type="hidden" name="entity_type" value="{{ $entity::class }}"/>
                            @if($entity->reviewNote)
                                <p><b>Reviewer:</b> {{ $entity->reviewNote->reviewer->name }}</p>
                            @endif
                            <div class="form-group mb-2">
                                <label>Text:</label>
                                <textarea class="form-control" rows="6"
                                          name="text" required>{!! $entity->reviewNote ? $entity->reviewNote->text : '' !!}</textarea>
                            </div>
                            <div class="form-group mb-2">
                                <label>Priority:</label>
                                <select class="form-control" name="priority">
                                    <option></option>
                                    @foreach(\App\Models\EntityReviewNote::PRIORITIES as $value => $label)
                                        <option
                                            value="{{ $value }}" {{ $entity->reviewNote && $entity->reviewNote->priority === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @elseif($entity->is_need_audit)
        <form class="d-inline" action="{{ route('admin.entity-reviews.audits.approve') }}" method="post">
            @csrf
            <input type="hidden" name="entity_type" value="{{ $entity::class }}">
            <input type="hidden" name="entity_id" value="{{ $entity->id }}">
            <button type="submit" class="btn btn-success me-3 confirm-button">Approve audit</button>
        </form>
    @endif
@endcan
