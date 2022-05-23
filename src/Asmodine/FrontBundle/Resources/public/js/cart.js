(function () {
    function openCart() {
        if (!jQuery('#small_cart').is(':visible')) {
            jQuery('#header #floating-cart i').click();
        }
    }

    function closeCart(force) {
        if (jQuery('#small_cart').is(':visible') || force === true) {
            jQuery('#header #floating-cart i').click();
        }
    }

    /**
     * Reload products
     * @param e
     * @param callback
     */
    function reloadProducts(e, callback) {
        var url = e.currentTarget.href;
        $.ajax({
            type: 'GET',
            url: encodeURI(url),
            dataType: 'json',
            success: function (data) {
                var cart = jQuery('#small_cart');
                cart.outerHTML = data.html;
                openCart();
                if (callback) {
                    callback();
                }
            }
        });
    }

    function countAndDisplayNbProduct() {
        var cart_block = document.getElementById('small_cart');
        var nb_product = cart_block.querySelectorAll('.list .element').length;
        if (nb_product != 0) {
            jQuery('#floating-cart .floating-cart-nb-product').text(nb_product);
            jQuery('#floating-cart .floating-cart-nb-product').show();
        } else {
            jQuery('#floating-cart .floating-cart-nb-product').hide();
        }
    }

    jQuery(document).on('click', '#small_cart .element .close-button a, .add-to-cart', function (event) {
        event.preventDefault();
        reloadProducts(event, countAndDisplayNbProduct);
    });

    countAndDisplayNbProduct();

    /**
     * Close small cart on click anywhere
     */
    jQuery(document).on('click', 'body', function (event) {
        var $element = jQuery(event.target);
        var $small_cart = jQuery('#small_cart');
        var has_to_close =
            !(
                $element.is('#small_cart')
                || $element.parents('#small_cart').length > 0
                || $element.is('#header #floating-cart')
                || $element.parents('#header #floating-cart').length > 0
            )
            && $small_cart.is(':visible');

        if (has_to_close) {
            event.preventDefault();

            closeCart();
        }
        return;
    });

    jQuery(document).on('click', '#small_cart .close-cross', function (event) {
        closeCart(true);
        return;
    });

    jQuery(document).on('click', '.close-cart', function (event) {
        closeCart();
        return;
    });

})();