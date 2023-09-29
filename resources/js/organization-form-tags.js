const longevityInput = document.getElementById('longevity');
const tagsBlock = document.getElementById('organization_tags_block');
const parentTagInput = document.getElementById('parent_tag');
const childTagsBlock = document.getElementById('organization_child_tags_block')
const childTagsInput = document.getElementById('child_tags');

let childTagsChoice = new Choices(childTagsInput, {
    removeItemButton: true,
});

longevityInput.addEventListener(
    'change',
    function(event) {
        if (event.target.value === '1') {
            tagsBlock.style.display = 'inherit';
            parentTagInput.disabled = false;
        } else {
            tagsBlock.style.display = 'none';
            parentTagInput.disabled = true;
        }
    }
);

parentTagInput.addEventListener(
    'change',
    updateChildTagsInput
);

function updateChildTagsInput()
{
    let parentInputValue = parentTagInput.value;

    if (parentInputValue) {
        let tagsUrl = childTagsInput.getAttribute('data-url') + '?parent_id=' + parentInputValue;

        childTagsChoice.removeActiveItems();
        childTagsChoice.setChoices(async () => {
            try {
                const items = await fetch(tagsUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        }, 'value', 'label', true);

        childTagsBlock.style.display = 'inherit';
    }
}
