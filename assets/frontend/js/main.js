$(document).ready(function() {
    // Smooth scroll for anchor links (optional)
    $('a.nav-link[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = this.hash;
        if ($(target).length) {
            $('html, body').animate({
                scrollTop: $(target).offset().top
            }, 600);
        }
    });

    // Bootstrap dropdown hover on desktop
    if ($(window).width() > 768) {
        $('.navbar .dropdown').hover(
            function() {
                $(this).addClass('show');
                $(this).find('.dropdown-menu').addClass('show');
            },
            function() {
                $(this).removeClass('show');
                $(this).find('.dropdown-menu').removeClass('show');
            }
        );
    }
});
