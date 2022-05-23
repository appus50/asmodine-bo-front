$(function () {

    /** TODO Supprimer pour multi filtres */
    $("input:checkbox").on('click', function () {
        // in the handler, 'this' refers to the box clicked on
        var $box = $(this);
        if ($box.is(":checked")) {
            // the name of the box is retrieved using the .attr() method
            // as it is assumed and expected to be immutable
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            // the checked state of the group/box on the other hand will change
            // and the current value is retrieved using .prop() method
            $(group).prop("checked", false);
            $box.prop("checked", true);
        } else {
            $box.prop("checked", false);
        }
    });

    /** TODO Fin suppression pour multi filtres */

    function replaceElement(html_data, selector) {
        var _element = document.querySelector(selector),
            _temp = document.createElement('div');

        _temp.innerHTML = html_data;

        var _new_element = _temp.querySelector(selector);
        _element.innerHTML = _new_element.innerHTML;
    }

    var elements_to_reload = [
        '#list-of-products',
        '.top-presentation .description',
        '.top-part .breadcrumb',
        '#filters',
        '.filter-by-and-pagination > .pagination',
        '.bottom-part > .pagination'
    ];

    var selects = document.querySelectorAll('.filter-by-and-pagination .filter-by select');
    Array.prototype.forEach.call(selects, function (select) {
        select.addEventListener('change', function (event) {
            var select_element = this;
            var option = select_element.options[select_element.selectedIndex];
            var url = option.getAttribute('data-url');
            if (url) {
                window.location = url;
            }
        });
    });

    function addEventListenerOnFilters() {
        var apply_buttons = document.querySelectorAll('.filter-block .apply-filter');
        Array.prototype.forEach.call(apply_buttons, function (button) {
            button.addEventListener('click', function (event) {
                var ids_node = document.querySelectorAll('.filter-block .content input:checked');
                var ids = [];
                Array.prototype.forEach.call(ids_node, function (id) {
                    var obj = {};
                    var fType = id.getAttribute('data-type');
                    var fLabel = id.getAttribute('data-label');
                    obj[fType] = fLabel;
                    ids.push(obj);
                });
                var url = new URL(window.location.href);
                var search = url.searchParams.get("search");
                var orderby = url.searchParams.get("order");
                $.ajax({
                    type: 'POST',
                    url: $('#filters_route').val(),
                    data: 'method=filter&route=' + encodeURIComponent($('#current_route').val()) + '&filters=' + encodeURIComponent(JSON.stringify(ids) + (search === null ? '' : '&search=' + search) + (orderby === null ? '' : '&order=' + orderby)),
                    success: function (response) {
                        window.location.href = response;
                    }
                });
            });
        });

        /* remove filters of attribute keep others */
        var delete_buttons = document.querySelectorAll('.delete-filter');
        Array.prototype.forEach.call(delete_buttons, function (button) {
            button.addEventListener('click', function (event) {
                var ids_node = document.querySelectorAll('.filter-block .content input:checked');
                var current_block_ids_node = button.parentElement.querySelectorAll('input:checked');

                var current_block_ids = [];
                var ids = [];
                Array.prototype.forEach.call(current_block_ids_node, function (id) {
                    current_block_ids.push(id.getAttribute('data-id'));
                });
                Array.prototype.forEach.call(ids_node, function (id) {
                    var obj = {};
                    var fType = id.getAttribute('data-type');
                    var fLabel = id.getAttribute('data-label');
                    obj[fType] = fLabel;
                    ids.push(obj);
                });
                $.ajax({
                    type: 'POST',
                    url: $('#filters_route').val(),
                    data: 'method=filter&route=' + encodeURIComponent($('#current_route').val()) + '&action=delete',
                    success: function (response) {
                        window.location.href = response;
                    }
                });
            });
        });

        var filter_titles = document.querySelectorAll('.filter-block .title');
        Array.prototype.forEach.call(
            filter_titles, function (filter_title) {
                filter_title.addEventListener(
                    'click', function (event) {
                        var element = this;
                        var content = element.parentElement.querySelector('.content');
                        if ($(content).hasClass('opened')) {
                            $(content).removeClass('opened');
                        } else {
                            $(content).addClass('opened');
                        }
                    }
                );
            }
        );
    }

    addEventListenerOnFilters();
    var ajax_change_advice;
    jQuery(document).on('change', '#personal_shopper', function (event) {
        $checkbox = $(event.currentTarget);

        if (ajax_change_advice) {
            ajax_change_advice.abort();
        }

        activate = 0;
        if ($checkbox.is(':checked')) {
            activate = 1;
        }

        ajax_change_advice = $.ajax({
            url: $checkbox.attr('data-url'),
            type: 'POST',
            data: 'activate=' + activate,
            success: function () {
                window.location.reload();
            }
        });
    });
});