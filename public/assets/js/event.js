var title = $('.js-event-ajax');
var page = 0;

$(document).ready(function () {
    HomeEvent(page);
});

// Ajax function to display events with the value of the search bar
// if value == '' show all events
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
                //Render events templates in the twig
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

// function for the pagination
var pagination = function (page){
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
}

// Search bar click on the button "search"
$('.js-event-search-submit').click(function () {
    $('.home-event-js div').remove();
    HomeEvent();
});

// Load function with the search bar click if the button "Enter" is pressed by the user
title.on('keyup', function (e) {
    if (e.keyCode == 13)
        $('.js-event-search-submit').click();
});


/*Bouton Paginations*/
$('.js-event-page').click(function () {
    page = this.getAttribute("data-value");
    pagination(page);
    HomeEvent(page);
});

/*Bouton Previous page*/
$('.js-event-page-previous').click(function () {
    page = this.getAttribute("data-value");
    pagination(page);
    HomeEvent(page);
});


/*Bouton Next page*/
$('.js-event-page-next').click(function () {
    page = this.getAttribute("data-value");
    pagination(page);
    HomeEvent(page);
});