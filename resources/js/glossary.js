const nav = document.querySelector(".glossary-nav");
const navMenu = document.querySelector('.glossary-nav-menu');

function glossaryMenu(){

    if (window.scrollY >= nav.offsetTop){
        navMenu.classList.add("fixed-nav");
    } else {
        navMenu.classList.remove("fixed-nav");
    }
}

function glossaryMenuHeight() {
    let navHeight = document.querySelector(".glossary-nav-menu").offsetHeight;
    nav.style.height = navHeight + 'px';
}
window.addEventListener("scroll", glossaryMenu);
window.onresize = glossaryMenuHeight;

/* jQuery */
jQuery(document).ready(function($) {
    /* Anchor Scrolling */
    $(document).on('click', 'a[href^="#"]', function (event) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: $($.attr(this, 'href')).offset().top + -100
        }, 1000);
    });

    /* Glossary Nav for Mobile */
    $('.glossary-nav-menu .toggle-mobile').on('click', function() {

        if ($(navMenu).hasClass('open')) {
            $(navMenu).removeClass('open');
            $('.glossary-nav-menu .toggle-mobile span').text('+');
        } else {
            $(navMenu).addClass('open');
            $('.glossary-nav-menu .toggle-mobile span').html('&ndash;');
        }

    });
});
