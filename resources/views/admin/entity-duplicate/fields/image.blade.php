<br>
@if($entity->{$name})
    @if($entity->imageFullUrl)
        <img src="{{ $entity->imageFullUrl }}" style="max-width: 300px;" />
    @else
        <div class="alert alert-danger">Error with image url configuration</div>
    @endif
@else
    <p><span class="badge bg-secondary">No image</span></p>
@endif
