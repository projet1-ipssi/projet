var title = $('.js-event-ajax');
var page = 0;

$(document).ready(function () {
    HomeEvent(page);
});

var HomeEvent = function (page) {

    if (title != "undefined") {
        var val = title.val();

        $.ajax({
            method: 'post',
            url: $('.home-event-js').attr('data-endpoint'),
            data: {
                title: title.val(),
                page: page
            },
            success: function (data) {
                console.log(data);
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

$('.js-event-page').click(function () {
    page = this.getAttribute("data-value");
    $('.home-event-js div').remove();
    var remove = $('.active');
    remove.removeClass('active');
    var li = $('#li-'+page);
    li.addClass('active');
    HomeEvent(page);
});