var title = $('.js-event-ajax');

$(document).ready(function () {
    HomeEvent();
});

var HomeEvent = function () {

    if (title != "undefined") {
        var val = title.val();

        $.ajax({
            method: 'post',
            url: $('.home-event-js').attr('data-endpoint'),
            data: {
                title: title.val()
            },
            success: function (data) {

                data.results.map(function (result) {
                    $('.home-event-js').append(result.html)
                });
            },
            error:function (data) {
                console.log('error');
            }

        })
    }
};

$('.js-event-search-submit').click(function () {
    $('.home-event-js div').remove();
    HomeEvent();
});

title.on('keyup', function (e) {
    if (e.keyCode == 13)
    $('.js-event-search-submit').click();
});