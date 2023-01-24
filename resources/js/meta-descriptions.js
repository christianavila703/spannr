const textAreaElement = document.querySelector("#meta_description");
const characterCounterElement = document.querySelector("#meta_description_character_count");

textAreaElement.addEventListener("keydown", (event) => {
    metaDescriptionCharacterCount();
});

textAreaElement.addEventListener("change", (event) => {
    metaDescriptionCharacterCount();
});

function metaDescriptionCharacterCount() {
    const typedCharacters = textAreaElement.value.length

    characterCounterElement.textContent = typedCharacters;

    if (typedCharacters >= 50 && typedCharacters <= 160) {
        characterCounterElement.classList = "badge bg-success";
    } else if (typedCharacters > 160) {
        characterCounterElement.classList = "badge bg-danger";
    } else {
        characterCounterElement.classList = "badge bg-dark";
    }
}

metaDescriptionCharacterCount();
