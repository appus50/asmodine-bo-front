(function() {
    var $document = $document || $(document);

    var $leftslider = $('#left-slider');
    if ($leftslider.lengt > 0 ) {

    $leftslider.find('.elements').slick({
        vertical: true,
        verticalSwiping: true,
        dots: false,
        arrows: true,
        prevArrow: $leftslider.find('.arrow.up'),
        nextArrow: $leftslider.find('.arrow.down'),
        infinite: true,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1170,
                settings: { slidesToShow: 4 }
            }, {
                breakpoint: 970,
                settings: { slidesToShow: 3 }
            },
            {
                breakpoint: 860,
                settings: { slidesToShow: 2 }
            }, {
                breakpoint: 780,
                settings: { slidesToShow: 1 }
            }
        ]
    });

    }
    if ($('.review_note').length > 0) {
        $('.review_note').starRating({
            starSize: 25,
            useFullStars: true,
            emptyColor: '#fff',
            disableAfterRate: false,
            callback: function(currentRating, $el) {
                $('#review_note').val(currentRating);
            }
        });
    }


    $document.on('submit', 'form[name=review]', function(event) {
        event.preventDefault();
        var $form = $(this);

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'html',
            success: function( html ) {
                var parser = new DOMParser();
                var parsed_dom = parser.parseFromString(html, 'text/html');
                var $show_review_success = $('#show-review-success');
                $show_review_success.html($(parsed_dom).find('body').html());
                $show_review_success.modal('show');
            }
        });
        return false;
    });
})();