(function() {
    var $document = $document || $(document);

    $('#modify-desc').on('click', function(event){
        $('#description_update_field').trigger('focus');
    });

    $('#description_update_field').on('focus', function(event){
        var ref_desc = event.currentTarget.innerText;
        event.currentTarget.setAttribute('data-desc', ref_desc);
    });

    $('#description_update_field').on('focusout', function(event){
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
                data: { desc: encodeURIComponent(new_desc)},
                success: function (html) {
                }
            });
        }
    });
    var regex = /[?&]([^=#]+)=([^&#]*)/g,
        url = window.location.href,
        params = {},
        match;
    while(match = regex.exec(url)) {
        params[match[1]] = match[2];
    }
    var $anchor = $('#suggest_pagination');
    if (params.page && $anchor && $anchor.offset()) {
        $("html, body").delay(1000).animate({
            scrollTop: $anchor.offset().top - 120
        }, 1000);
    }
})();