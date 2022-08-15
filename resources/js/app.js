require('./bootstrap');

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
