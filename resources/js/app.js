import './bootstrap';
import * as bootstrap from 'bootstrap';
import '../sass/app.scss';

/* Images */
import.meta.glob([
    '../images/**'
]);

/* Tooltips */
let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

/* Primary Nav */
let hamburger = document.querySelector(".hamburger");
hamburger.addEventListener("click", function () {
    hamburger.classList.toggle("is-active");
});

const myOffcanvas = document.getElementById('offcanvas-primary-menu')
myOffcanvas.addEventListener('hidden.bs.offcanvas', event => {
    hamburger.classList.remove("is-active");
})

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

/* Event Listeners for URLs */
window.addEventListener('go-to-url', event => {
    window.location = event.detail.url;
});

/* Toast Notifications */
window.addEventListener('toast-notification', event => {
    let time = 4000;

    if(window.innerWidth < 768) {
        time = 2000;
    }

    let backgroundColor = 'bg-quaternary';

    if (event.detail.background) {
        backgroundColor =  event.detail.background;
    }

    let textColor = 'text-white';

    if (event.detail.color) {
        textColor =  event.detail.color;
    }

    let toastContainer = document.querySelector('#toast-container');

    let toastElement = document.createElement('div');
    toastElement.classList.add('toast', 'show', backgroundColor, textColor, 'border-0');

    let toastBody = document.createElement('div');
    toastBody.classList.add('toast-body', 'd-flex');

    let toastText = document.createElement('span');
    toastText.innerText = event.detail.text;

    let closeButton = document.createElement('button');
    closeButton.classList.add('btn-close', 'btn-close-white', 'me-2', 'm-auto');
    closeButton.ariaLabel = 'Close';
    closeButton.setAttribute('data-bs-dismiss', 'toast');

    toastContainer.appendChild(toastElement);
    toastElement.appendChild(toastBody);
    toastBody.appendChild(toastText);
    toastBody.appendChild(closeButton);

    setTimeout(() => {
        toastElement.remove();
    }, time);
});

/* Collapse Border Animated */
let colorChangingButtons = document.querySelectorAll('.color-changing-border button[data-bs-toggle="collapse"]');

colorChangingButtons.forEach((button) => {
    let parentElement = button.parentElement;

    let collapsible = document.getElementById(button.getAttribute('aria-controls'))
    collapsible.addEventListener('hidden.bs.collapse', event => {
        parentElement.classList.add('muted-border');
    });

    collapsible.addEventListener('shown.bs.collapse', event => {
        parentElement.classList.remove('muted-border');
    });
});
