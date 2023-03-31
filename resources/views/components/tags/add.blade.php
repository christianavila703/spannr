<?php
    $tagsUrl = route('admin.json.tags');
    $selectedTags = $entity->tags()->pluck('id')->toArray();

    if (!empty($selectedTags)) {
        $tagsUrl .= '?default=' . implode(',', $selectedTags);
    }
?>
<div class="modal fade" id="addTagForm" tabindex="-1" aria-labelledby="addTagFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.tags.sync') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addTagFormLabel">Add Tags to {{ $entity->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="min-height: 400px">
                    <label for="tags" class="visually-hidden">Select Tags</label>
                    <select multiple name="tags[]" id="tags" class="js-choice-tag" data-tags-url="{{ $tagsUrl }}">
                        <option value="">Select Tags</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="taggable_id" value="{{ $entity->id }}">
                    <input type="hidden" name="taggable_type" value="{{ $entity::class }}">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const tagChoice = new Choices('.js-choice-tag', {
        removeItemButton: true,
    });

    let tagsUrl = document.querySelector(".js-choice-tag").dataset.tagsUrl;

    tagChoice.setChoices(async () => {
      try {
        const items = await fetch(tagsUrl);
        return items.json();
      } catch (err) {
        console.error(err);
      }
    });
</script>
