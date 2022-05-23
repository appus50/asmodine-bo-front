jQuery(document).on('ready', function () {
    var loadInformations = function () {

        var knowyoursize = document.querySelector('#know-your-size');
        var unknowyoursize = document.querySelector('#unknow-your-size');

        if (knowyoursize) {
            var span = knowyoursize.querySelector('span');
            if (unknowyoursize) {
                unknowyoursize.setAttribute('class', 'hide');
            }
        } else {
            return;
        }

        var shops = document.querySelector('#shop'),
            grade_size = document.querySelector('#grade-size'),
            current_shop_name = shops.options[shops.selectedIndex].text;

        if (span && span.getAttribute('show') !== 'shoponly') {
            var categories = document.querySelector('#categories'),
                current_category_name = categories.options[categories.selectedIndex].text
            current_grade_sizes = categories.options[categories.selectedIndex].getAttribute('data-sizes');

            current_shop_name = label_size_category
                .replace('%shop%', current_shop_name)
                .replace('%category%', current_category_name)
            ;

            var has_grade_size = false;
            var all_grade_sizes = [];
            if (current_grade_sizes && current_grade_sizes.length > 0) {
                try {
                    current_grade_sizes = JSON.parse(current_grade_sizes);
                    var current_grade_size = null;
                    if (Object.prototype.toString.call(current_grade_sizes) === '[object Object]') {
                        for (var size_type in current_grade_sizes) {
                            if (current_grade_sizes.hasOwnProperty(size_type) === false) {
                                continue;
                            }
                            if (null === current_grade_size) {
                                current_grade_size = current_grade_sizes[size_type].size;
                                grade_size.innerHTML = current_grade_size;
                                has_grade_size = true;
                            }
                            all_grade_sizes.push(current_grade_sizes[size_type].size);
                        }
                    }
                } catch (e) {
                }
            }

            grade_size.removeAttribute('data-trigger');
            grade_size.removeAttribute('data-placement');
            grade_size.removeAttribute('data-content');

            if (false === has_grade_size) {
                knowyoursize.setAttribute('class', 'hide');
                if (unknowyoursize) {
                    unknowyoursize.setAttribute('class', '');
                }
            } else {
                knowyoursize.setAttribute('class', '');
                if (all_grade_sizes.length > 1) {
                    grade_size.setAttribute('data-trigger', 'hover');
                    grade_size.setAttribute('data-toggle', 'popover');
                    grade_size.setAttribute('data-placement', 'right');
                    grade_size.setAttribute('data-content', label_all_sizes + '<span>' + all_grade_sizes.join(', ') + '</span>');
                    jQuery('[data-toggle="popover"]').popover({html: true});
                } else {
                    jQuery('[data-toggle="popover"]').popover('destroy');
                }
            }
        }

        if (span) {
            span.textContent = current_shop_name;
        }
    };

    loadInformations();

    jQuery(document).on('change', '#shop', function () {
        loadInformations();
        showIframe();
    });

    jQuery(document).on('change', '#categories', function () {
        loadInformations();
    });

    jQuery(document).on('change', '.select-asmotaille', function (event) {
        event.preventDefault();

        var form = jQuery(this).parents('form')[0];
        var form_element = document.getElementById('menu-select-asmotaille');
        var replaceMenuSelectAndKnowYourSize = function (data) {
            form_element.outerHTML = data.html;
            loadInformations();
        };

        var current_id = jQuery(this).attr('id');
        var is_shop = (current_id && current_id == 'shop');

        jQuery.ajax({
            async: true,
            type: 'POST',
            url: form.action,
            data: jQuery(this).parents('form').serialize(),
            dataType: 'json',
            success: function (data) {
                replaceMenuSelectAndKnowYourSize(data);
                if (is_shop) {
                    showIframe();
                }
            },
            error: function (data) {
                console.error('Requete impossible');
            }
        });
    });

    function showIframe() {
        var categories = jQuery('#categories'),
            iframe = jQuery('#iframe');

        var url = categories.find('option:selected').val();
        iframe.attr('src', '');

        if (categories.find('option').length > 0) {
            iframe.attr('src', url);
        }
    }

    if (undefined === window['dont_reload_page'] || true !== dont_reload_page) {
        showIframe();
    }
});