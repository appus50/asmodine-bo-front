(function() {
    var $bestbrands = $('#best-brands');

    if ($bestbrands.length > 0) {
        $bestbrands.find('.elements-container').slick({
            dots: false,
            arrows: true,
            centerMode: true,
            prevArrow: $bestbrands.find('.arrows.prev'),
            nextArrow: $bestbrands.find('.arrows.next'),
            infinite: true,
            speed: 300,
            slidesToShow: 7,
            slidesToScroll: 7,
            responsive: [
                {
                    breakpoint: 1170,
                    settings: {
                        slidesToShow: 6,
                        slidesToScroll: 6
                    }
                }, {
                    breakpoint: 970,
                    settings: { slidesToShow: 5, slidesToScroll: 5 }
                },
                {
                    breakpoint: 860,
                    settings: { slidesToShow: 4, slidesToScroll: 4 }
                }, {
                    breakpoint: 780,
                    settings: { slidesToShow: 3, slidesToScroll: 3 }
                }, {
                    breakpoint: 680,
                    settings: { slidesToShow: 2, slidesToScroll: 2 }
                }, {
                    breakpoint: 500,
                    settings: { slidesToShow: 1, slidesToScroll: 1 }
                }
            ]
        });
    }
})();