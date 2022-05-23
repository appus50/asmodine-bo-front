(function () {

    var selector = '.onglets .onglet:not(.no-capture-event)',
        active_classname = 'active',
        page_content_id = 'page-content';

    $("#profile-loader").hide().removeClass('hidden');

    var changePage = function (event) {
        event.preventDefault();

        var scope = this,
            url = $(scope).data('url'),
            parser = new DOMParser();

        if ($(scope).hasClass(active_classname)) {
            return;
        }

        $('#page-content').hide();
        $('#profile-loader').show();

        $(selector + '.' + active_classname).removeClass(active_classname);
        $(scope).addClass(active_classname);

        $.ajax({
            type: 'POST',
            url: url,
            data: {ajax: true},
            dataType: 'html',
            success: function (html) {
                var page_content = document.getElementById(page_content_id),
                    parsed_dom = parser.parseFromString(html, 'text/html');
                if (page_content) {
                    $('#profile-loader').hide();
                    page_content.innerHTML = parsed_dom.body.innerHTML;
                    $('#page-content').show();
                }
            }
        });
    };

    var $document = $document || $(document);
    $document.on('click', selector, changePage);

    $document.on('click', '#modify-desc', function (event) {
        $('#description_update_field').trigger('focus');
    });

    $document.on('focus', '#description_update_field', function (event) {
        var ref_desc = event.currentTarget.innerText;
        event.currentTarget.setAttribute('data-desc', ref_desc);
    });

    $document.on('focusout', '#description_update_field', function (event) {
        var new_desc = event.currentTarget.innerText;
        var old_desc = event.currentTarget.getAttribute('data-desc');
        if (new_desc == old_desc) {
            return;
        } else {
            var url = event.currentTarget.getAttribute('data-action');
            // update description
            $.ajax({
                type: 'POST',
                url: url,
                data: {desc: encodeURIComponent(new_desc)},
                success: function (html) {
                }
            });
        }
    });

    // Change morpho colors
    $document.on('click', '.morphotype-toggle', function (event) {
        var tab_to_display = event.currentTarget.getAttribute('data-toggle');
        $('.morphotype_step').addClass('hide');
        $('.morphotype_step.' + tab_to_display).removeClass('hide');
    });

    $document.on('change', '.morphotype_step input[type=radio]', function (event) {
        var tab_to_display = event.currentTarget;
        var class_value = tab_to_display.value;
        var type_element = $(tab_to_display).parents('.morphotype_step')[0];
        var type = type_element.getAttribute('data-type');
        var $element = $('.color-container.' + type);

        $element.removeClass();
        $element.addClass('color-container');
        $element.addClass(type);
        $element.addClass(class_value);
    });

})();