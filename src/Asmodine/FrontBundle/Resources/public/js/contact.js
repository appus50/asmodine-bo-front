jQuery(document).on('ready', function () {
    jQuery('.header').on('click', function () {
        jQuery('.header').each(function () {
            jQuery(this).removeClass('open');
            jQuery(this).parent().children('.form').addClass('hide');
        });
        jQuery(this).addClass('open');
        jQuery(this).parent().children('.form').removeClass('hide');
    })
});