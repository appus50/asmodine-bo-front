jQuery(document).on('ready', function () {
    var left_menu = jQuery('#left-menu');
    var main_section = left_menu.parent();

    function collapeMenu() {
        left_menu
            .removeClass('expanded')
            .addClass('collapsed')
        ;
        main_section
            .removeClass('expanded')
            .addClass('collapsed')
        ;
        if (window.localStorage) {
            window.localStorage.setItem('menu-state', 0);
        }
    }

    function expandeMenu() {
        left_menu
            .removeClass('collapsed')
            .addClass('expanded')
        ;
        main_section
            .removeClass('collapsed')
            .addClass('expanded')
        ;
        if (window.localStorage) {
            window.localStorage.setItem('menu-state', 1);
        }
    }

    if (window.localStorage) {
        var menu_state = window.localStorage.getItem('menu-state');
        if (menu_state === null || menu_state == 1) {
            expandeMenu();
        } else {
            collapeMenu();
        }
    }

    jQuery(document).on('click', '#left-menu .collapse-expande .collapse', collapeMenu);
    jQuery(document).on('click', '#left-menu .collapse-expande .expande', expandeMenu);
    jQuery('.nicescroll').niceScroll();
});