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
            error: function (data) {
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


/*Bouton Paginations*/
$('.js-event-page').click(function () {
    page = this.getAttribute("data-value");
    var pageTotale = $('.home-event-js').attr("data-value");
    $('.home-event-js div').remove();
    var remove = $('.active');
    remove.removeClass('active');
    var li = $('#li-' + page);
    li.addClass('active');
    var prev = $('#previous');
    var next = $('#next');

    if (page == 0) {
        prev.addClass('disabled');
        $('.js-event-page-previous').attr("data-value", 0);
    }
    else {
        prev.removeClass('disabled');
        $('.js-event-page-previous').attr("data-value", (page)-1);
    }

    if (page == (pageTotale)-1) {
        next.addClass('disabled');
        $('.js-event-page-next').attr("data-value", pageTotale);
    }
    else {
        next.removeClass('disabled');
        $('.js-event-page-next').attr("data-value", parseInt(page)+1);
    }
    HomeEvent(page);
});

/*Bouton Previous page*/
$('.js-event-page-previous').click(function () {
    page = this.getAttribute("data-value");
    var pageTotale = $('.home-event-js').attr("data-value");
    $('.home-event-js div').remove();
    var remove = $('.active');
    remove.removeClass('active');
    var li = $('#li-' + page);
    li.addClass('active');
    var prev = $('#previous');
    var next = $('#next');

    if (page == 0) {
        prev.addClass('disabled');
        $('.js-event-page-previous').attr("data-value", 0);
    }
    else {
        prev.removeClass('disabled');
        $('.js-event-page-previous').attr("data-value", (page)-1);
    }

    if (page == (pageTotale)-1) {
        next.addClass('disabled');
        $('.js-event-page-next').attr("data-value", pageTotale);
    }
    else {
        next.removeClass('disabled');
        $('.js-event-page-next').attr("data-value", parseInt(page)+1);
    }
    HomeEvent(page);
});


/*Bouton Next page*/
$('.js-event-page-next').click(function () {
    page = this.getAttribute("data-value");
    var pageTotale = $('.home-event-js').attr("data-value");
    $('.home-event-js div').remove();
    var remove = $('.active');
    remove.removeClass('active');
    var li = $('#li-' + page);
    li.addClass('active');
    var prev = $('#previous');
    var next = $('#next');

    if (page == 0) {
        prev.addClass('disabled');
        $('.js-event-page-previous').attr("data-value", 0);
    }
    else {
        prev.removeClass('disabled');
        $('.js-event-page-previous').attr("data-value", (page)-1);
    }

    if (page == (pageTotale)-1) {
        next.addClass('disabled');
        $('.js-event-page-next').attr("data-value", pageTotale);
    }
    else {
        next.removeClass('disabled');
        $('.js-event-page-next').attr("data-value", parseInt(page)+1);
    }
    HomeEvent(page);
});