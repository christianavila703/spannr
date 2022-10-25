import './bootstrap';
import * as bootstrap from 'bootstrap';
import '../sass/app.scss';

/* Tooltips */
let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

/* Confirm */
let confirmationButtons = document.querySelectorAll('.confirm-button');

confirmationButtons.forEach(button => {
    button.addEventListener('click', function(event) {
        if (confirm("Are you sure?") !== true) {
            event.preventDefault();
        }
    });
});

/* Post Newsletter Optins */
if (document.getElementById('post-newsletter')) {
    let postOptIn = document.getElementById('post-newsletter');
    let paragraphs = document.querySelectorAll('.single-post-content p');
    let paragraphCount = paragraphs.length;

    if (paragraphCount > 30) {
        let paragraph = paragraphs[18];
        paragraph.after(postOptIn);
    } else {
        let paraCount = parseInt(paragraphCount / 2);
        let paragraph = paragraphs[paraCount];
        paragraph.after(postOptIn);
    }
}
