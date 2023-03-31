import $ from 'jquery';

$('.js-detach-tag-btn').on('click', function (event) {
    event.preventDefault();

    if (confirm("Are you sure?") !== true) {
        return false;
    }

    let btn = $(this),
        actionRoute = btn.data('action-route'),
        tagId = btn.data('tag-id'),
        taggableId = btn.data('taggable-id'),
        taggableType = btn.data('taggable-type'),
        container = btn.parent(),
        token = document.querySelector('meta[name="csrf-token"]').content;

    $.post(actionRoute, {
        '_token': token,
        'tag_id': tagId,
        'taggable_id': taggableId,
        'taggable_type': taggableType
    }, function (response) {
        if (response.status === 'success') {
            container.slideUp();
        } else {
            console.error(response.message);
        }
    });
});


