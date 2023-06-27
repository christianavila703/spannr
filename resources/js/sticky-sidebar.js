jQuery(document).ready(function($) {
    function stickToTheTop() {
        let browserWidth = window.innerWidth;
        let browserHeight = window.innerHeight;
        let window_top = $(window).scrollTop();
        let stickyStabilizerTop = $('#sticky-stabilizer').offset().top;
        let postContent = $('.post-content');
        let widthOfSidebar = $('#sidebar').width();
        let headerOuterHeight = $('.header-container ').outerHeight();

        if (browserWidth >= 1024 && browserHeight > 700 && postContent.height() > 1200) {
            if (window_top + headerOuterHeight > stickyStabilizerTop) {
                $('#sticky-sidebar-contents').addClass('sticky-sidebar');
                $('#sticky-sidebar-contents').css('top', headerOuterHeight + 20);
                $('#sticky-sidebar-contents').css('maxWidth', widthOfSidebar);
                $('#sticky-stabilizer').height($('#sticky-sidebar-contents' + 20).outerHeight());
            } else {
                $('#sticky-sidebar-contents').removeClass('sticky-sidebar');
                $('#sticky-stabilizer').height(20);
            }
        } else {
            $('#sticky-sidebar-contents').removeClass('sticky-sidebar');
            $('#sticky-stabilizer').height(20);
        }
    }

    // Check Footer
    function checkFooter() {
        let footer = document.querySelector('.after-single-post');
        let footerBounding = footer.getBoundingClientRect();

        let stickySidebar = document.querySelector('#sticky-sidebar-contents');
        let sidebarBounding = stickySidebar.getBoundingClientRect();

        let difference = footerBounding.top - sidebarBounding.bottom;

        if (difference < 25) {
            $('#sticky-sidebar-contents').removeClass('sticky-sidebar');
            $('#sticky-stabilizer').height(0);
        }
    }

    checkFooter();

    $(window).on('resize', function() {
        stickToTheTop();
        checkFooter();
    });

    $(window).on('scroll', function() {
        stickToTheTop();
        checkFooter();
    });
});
