String.prototype.contains = function (string) {
    return (this.indexOf(string) != -1);
};

/**
 * Find first ancestor of element or undefined if not found
 * @param id id or class
 * @returns {*}
 */
Element.prototype.findParent = function (id) {
    var el = this;
    while (el && el.parentNode) {
        el = el.parentNode;
        if ((el.className && el.className.indexOf(id) != -1) || (el.id && el.id.toLowerCase() == id)) {
            return el;
        }
    }
    return null;
};

function openInscriptionModal(){
    $('#autoProposalModal').modal('show');
}

function openTempModal(){
    $('#tempModal').modal('show');
}

function openEndModal(){
    $('#endModal').modal('show');
}

if (document.getElementById("banner") != null ) {
    if (document.getElementsByClassName('tmpR').length== 1){
        setTimeout(openEndModal, 1000);
    }
}

function test(idselect){
    //if body.user-connect

    if (document.getElementById(idselect) != null ) {
        if (document.getElementsByClassName('user-connect ').length!= 1){
            setTimeout(openTempModal, 1000);
        }
    }
}

jQuery(document).on('ready', function () {
    test("banner");

    var $nav_header = jQuery('#header'),
        offset_val = 200;

    // Method
    // =================================================

    function navSlide() {
        $nav_header.addClass('is-sticky');
        /*
        var scroll_top = jQuery(window).scrollTop();

        if (scroll_top >= offset_val) { // the detection!
            $nav_header.addClass('is-sticky');
        } else {
            $nav_header.removeClass('is-sticky');
        }*/
    }

// Handler
// =================================================

    jQuery(window).scroll(navSlide);

    // Toggle thirdline submenu
    var current_category = null,
        current_third_line = null;

    var toggleThirdLine = function (event) {
        var id = event.target.getAttribute('data-id');
        if (id) {
            event.preventDefault();
        } else {
            return;
        }

        var $element = jQuery(event.target),
            $third_line = jQuery(document.querySelector('#header .third-line[data-parent="' + id + '"]'));

        var all_titles = document.querySelectorAll('.second-line-left-menu'),
            all_menus = document.querySelectorAll('#header .third-line');

        Array.prototype.forEach.call(all_menus, function (all_menu) {
            jQuery(all_menu).removeClass('toggled');
        });

        Array.prototype.forEach.call(all_titles, function (all_title) {
            jQuery(all_title).removeClass('active');
        });

        if ($element.hasClass('active')) {
            current_category.removeClass('active');
            $third_line.removeClass('toggled');
            if (!jQuery('#beta-row').hasClass('onscroll')) {
                jQuery('#beta-row').show();
            }
        } else {
            if (current_category !== null) {
                current_category.removeClass('active');
                $third_line.removeClass('toggled');
            }
            current_category = $element;
            current_third_line = $third_line;
            $element.addClass('active');
            $third_line.addClass('toggled');
            jQuery('#beta-row').hide();
        }

    };

    document.addEventListener('click', function (event) {
        var is_outside_third_line = (current_third_line &&
            !(event.target == current_third_line[0]
                || current_third_line[0].contains(event.target)
                || document.getElementById('second-line-left-menu').contains(event.target)));

        if (is_outside_third_line) {
            current_category.removeClass('active');
            current_third_line.removeClass('toggled');
        }
    });

    jQuery(document).on('click', '#second-line-left-menu .link, #second-line-mobile-menu .link', toggleThirdLine);
    if (jQuery.scrollUp) {
        jQuery.scrollUp({
            scrollName: 'scrollUp', // Element ID
            // topDistance: '300', // Distance from top before showing element (px)
            topSpeed: 300, // Speed back to top (ms)
            animation: 'fade', // Fade, slide, none
            animationInSpeed: 200, // Animation in speed (ms)
            animationOutSpeed: 200, // Animation out speed (ms)
            scrollText: '', // Text for element
            scrollTitle: false,
            activebasOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
            elementToAppend: '#main'
        });
    }

    // Block background scroll while sidebar is opened
    var trigger = document.querySelector('#menu-trigger'),
        mobile_menu = document.querySelector('#second-line-mobile-menu');


    trigger.addEventListener('click', function (event) {
        event.preventDefault();
        jQuery(document).body.style.overflow = (this.className.contains('open')) ? 'auto' : 'hidden';
    });

    mobile_menu.addEventListener('touchstart', function () {
        var top = this.scrollTop,
            totalScroll = this.scrollHeight,
            currentScroll = top + this.offsetHeight;

        if (top === 0) {
            this.scrollTop = 1;
        } else if (currentScroll === totalScroll) {
            this.scrollTop = top - 1;
        }
    });

    // Header icons
    var header = document.querySelector('#header'),
        icon_links = header.querySelectorAll('.icons a'),
        $current_icon_link = null;

    var moveElementTo = function (element, to) {
        var _body_rect = jQuery(document).body.getBoundingClientRect(),
            _dest_rect = to.getBoundingClientRect(),
            _new_left = _body_rect.left + _dest_rect.left,
            _new_right = _body_rect.right - _dest_rect.right,
            triangle = element.querySelector('.triangle');


        element.style.right = null;
        element.style.left = _new_left + 'px';
        if (triangle) {
            element.style.left = _new_left + 'px';
        }

        var _elem_rect = element.getBoundingClientRect(),
            _size = (_elem_rect.width != 0) ? _elem_rect.width : 320;

        if (_size + _new_left > window.innerWidth) {
            element.style.right = 0;
            element.style.left = null;
            if (triangle) {
                triangle.style.right = (_new_right) + 'px';
            }
        }
    };

    var iconClickAction = function (event) {
        event.preventDefault();

        var $this = jQuery(this);
        if ($this.hasClass('active')) {
            $this.removeClass('active');
            subdiv.style.display = 'none';
        } else {
            if ($current_icon_link !== null) {
                $current_icon_link.removeClass('active');
                subdiv.style.display = 'none';
            }
            $current_icon_link = $this;
            $this.addClass('active');

            moveElementTo(subdiv, this);

            subdiv.style.display = 'block';
        }
    };

    for (var i = 0; i < icon_links.length; i++) {
        var icon_link = icon_links[i];

        icon_link.addEventListener('click', function (event) {
            event.preventDefault();

            var subdiv = document.querySelector(this.getAttribute('data')),
                $this = jQuery(this);

            if ($this.hasClass('active')) {
                $this.removeClass('active');
                subdiv.style.display = 'none';
            } else {
                if ($current_icon_link !== null) {
                    $current_icon_link.removeClass('active');
                    document.querySelector($current_icon_link[0].getAttribute('data')).style.display = 'none';
                }
                $current_icon_link = $this;
                $this.addClass('active');
                moveElementTo(subdiv, this);
                subdiv.style.display = 'block';
            }
        }, false);

        window.addEventListener('resize', function (event) {
            if ($current_icon_link !== null) {
                var subdiv = document.querySelector(this.getAttribute('data'));
                if (subdiv) {
                    moveElementTo(subdiv, $current_icon_link[0]);
                }
            }
        });
    }

    jQuery('#submit-login').on('click', function () {
        jQuery('#loginModal form').submit();
    });

    // Display Forgot password form in login popup
    jQuery(document).on('click', '#loginModal .reset-password', function (event) {
        event.preventDefault();
        var $form = jQuery(this).parents('form').first();
        jQuery.ajax({
            type: 'GET',
            url: this.href,
            dataType: 'json',
            success: function (data) {
                var html = data.html;
                $form.replaceWith(html);
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    // Display Forgot password form return in login popup
    jQuery(document).on('submit', 'form.fos_user_resetting_request', function (event) {
        event.preventDefault();
        var $form = jQuery(this);
        var url = this.action;
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            dataType: 'html',
            success: function (html) {
                if (html) {
                    var element = jQuery(document).createElement('html');
                    element.innerHTML = html;
                    var form = element.querySelector('form');
                    if (form) {
                        $form.replaceWith(form);
                    } else {
                        $form.replaceWith(html);
                    }
                }
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    // FACEBOOK LOGIN EVENT
    jQuery(document).on('click', '#facebook-login', function (event) {
        event.preventDefault();
        fb_login(event);
    });

    // Submit subscription events
    jQuery(document).on('submit', '#subscription_user', function (event) {
        event.preventDefault();

        var form = this;
        var $this = jQuery(this);

        var date = jQuery('#user_registration_birthdate');

        var match_result = date.val().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if (null != match_result) {
            date.val(match_result[3] + '-' + match_result[2] + '-' + match_result[1]);
        }

        jQuery.ajax({
            async: false,
            type: 'POST',
            url: form.action,
            data: jQuery(this).serialize(),
            dataType: 'json',
            success: function (data) {
                document.getElementById('subscription_user').outerHTML = data.html;
            },
            error: function (data) {
                document.getElementById('subscription_user').outerHTML = data.html;
            }
        });

    });

    //triggered when modal is about to be shown
    jQuery('#wishlistModal').on('show.bs.modal', function (e) {
        //get data-id attribute of the clicked element
        var product_id = jQuery(e.relatedTarget).data('product-id');
        var form_element = jQuery(e.currentTarget).find('.modal-body .form');
        var url = jQuery(e.currentTarget).data('url');
        //populate the textbox

        if (product_id) {
            jQuery.ajax({
                type: 'GET',
                async: false,
                url: encodeURI(url),
                data: {'product': product_id},
                dataType: 'json',
                success: function (data) {
                    form_element.html(data.html);
                    jQuery(form_element).find('input[name="product_id"]').val(product_id);
                    // send add form wishlist creation
                    var submit_form = jQuery(form_element).find('#switch_to_create_wishlist');
                    submit_form.attr('data-product-to-add', product_id);
                }
            });
        }
    });

    // Reload login form on close if content
    jQuery('#loginModal').on('hidden.bs.modal', function (e) {
        var modal = e.currentTarget;
        var url = modal.getAttribute('data-original-content');
        var $form_reset = jQuery(e.currentTarget).find('form.fos_user_resetting_request');
        var $reset_content = jQuery(e.currentTarget).find('.reset-password-content');
        // load form if needed
        if ($form_reset.length > 0 || $reset_content.length > 0) {
            jQuery.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                    jQuery(e.currentTarget).find('.modal-body').html(data.html);
                },
                error: function (data) {
                    console.error(data);
                }
            });
        }
    });

    //when close inscription modal reload initial content
    jQuery('#subscriptionModal').on('hidden.bs.modal', function (e) {
        var modal = e.currentTarget;
        var url = modal.getAttribute('data-original-content');
        // load form if needed
        if (!(jQuery(e.currentTarget).find('form').length > 0)) {
            jQuery.ajax({
                type: 'GET',
                async: false,
                url: url,
                dataType: 'json',
                success: function (data) {
                    jQuery(e.currentTarget).find('.modal-body').html(data.html);
                },
                error: function (data) {
                    jQuery(e.currentTarget).find('.modal-body').html(data.html);
                }
            });
        }
    });

    //when close tempinscription modal reload initial content
    jQuery('#tempsubscriptionModal').on('hidden.bs.modal', function (e) {
        var modal = e.currentTarget;
        var url = modal.getAttribute('data-original-content');
        // load form if needed
        if (!(jQuery(e.currentTarget).find('form').length > 0)) {
            jQuery.ajax({
                type: 'GET',
                async: false,
                url: url,
                dataType: 'json',
                success: function (data) {
                    jQuery(e.currentTarget).find('.modal-body').html(data.html);
                },
                error: function (data) {
                    jQuery(e.currentTarget).find('.modal-body').html(data.html);
                }
            });
        }
    });

    //triggered when modal is about to be shown
    jQuery('#wishlistAddModal').on('show.bs.modal', function (e) {
        // Coming from Product add modal
        var x;
        if (x = e.relatedTarget.getAttribute('data-product-to-add')) {
            jQuery(e.currentTarget).find('.modal-body input#wishlist_product_to_add').val(parseInt(x, 10));
        } else {
            //get data-id attribute of the clicked element
            var product_id = jQuery(e.relatedTarget).data('product-id');

            var form_element = jQuery(e.currentTarget).find('.modal-body form input.product_id');
            if (product_id) {
                form_element.val(product_id);
            }
        }
        var reload;

        if (reload = e.relatedTarget.getAttribute('data-reload')) {
            jQuery(e.currentTarget).find('.modal-body input#wishlist_redirect').val(parseInt(reload, 10));
        }
    });

    //when close wishlist modal reload initial content
    jQuery('#wishlistAddModal').on('hidden.bs.modal', function (e) {
        var modal = e.currentTarget;
        var url = modal.getAttribute('data-original-content');
        // load form if needed
        if (!(jQuery(e.currentTarget).find('form').length > 0)) {
            jQuery.ajax({
                type: 'GET',
                async: false,
                url: url,
                dataType: 'json',
                success: function (data) {
                    jQuery(e.currentTarget).find('.modal-body').html(data.html);
                },
                error: function (data, a, b) {
                    jQuery(e.currentTarget).find('.modal-body').html(data.html);
                }
            });
        }
    });

    //triggered when modal is about to be shown
    jQuery('#confirmDeleteReviewModal').on('show.bs.modal', function (e) {
        var url_content = e.relatedTarget.getAttribute('data-content');
        if (url_content) {
            jQuery.ajax({
                type: 'GET',
                url: url_content,
                dataType: 'html',
                success: function (data) {
                    jQuery(e.currentTarget).find('.modal-content').replaceWith(data);
                },
                error: function (data, a, b) {
                    console.error(data, a, b);
                }
            });
        }
    });

    // lors de la fermeture de la modal de suppression d'avis on reload les avis du produit
    jQuery('#confirmDeleteReviewModal').on('hidden.bs.modal', function (e) {
        var modal = e.currentTarget;
        var is_success_reload = modal.querySelector('.modal-body .review-deleted-success');
        if (is_success_reload) {
            var url_content_reload_reviews = is_success_reload.getAttribute('data-reload');
        }
        if (is_success_reload && url_content_reload_reviews) {
            jQuery.ajax({
                type: 'GET',
                url: url_content_reload_reviews,
                dataType: 'html',
                success: function (data) {
                    var $review_list_block = jQuery('#review-list');
                    $review_list_block.replaceWith(data);
                    // reloadReviewsNumber
                    var nb_review = jQuery('#client-feedbacks div.lines').children('div').length;
                    jQuery('span.review-number').replaceWith(nb_review);
                },
                error: function (data, a, b) {
                    console.error(data, a, b);
                }
            });
        }
    });

    jQuery(function () {
        jQuery('[data-toggle="popoverbeta"]').popover();
    });


    if(!(jQuery('body').hasClass('user-connect') || jQuery('body').hasClass('user-creation') || jQuery('#dontpopin').length > 0)) {
        setTimeout(openTempModal, 1);
    }
});
