const navMenu = document.querySelector('.glossary-nav-menu');

function toggleGlossaryNav(){
    if (window.scrollY > 0) {
        navMenu.classList.add("glossary-nav-mobile");
    } else {
        navMenu.classList.remove("glossary-nav-mobile");
    }
}

window.addEventListener("scroll", toggleGlossaryNav);

/* jQuery */
jQuery(document).ready(function($) {
    /* Anchor Scrolling */
    $(document).on('click', 'a[href^="#"]', function (event) {
        event.preventDefault();

        console.log($($.attr(this, 'href')).offset().top);

        $('html, body').animate({
            scrollTop: $($.attr(this, 'href')).offset().top + -200
        }, 1000);
    });

    /* Glossary Nav Toggle for Mobile */
    $('.glossary-nav-menu .toggle-mobile').on('click', function() {
        $('.glossary-nav-menu').toggleClass('open');
    });
});
