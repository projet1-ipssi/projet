
function setRatingStars(item_part, score)
{
    for (var i = 1; i <= 5; ++i)
    {
        var item = $(item_part + i);
        item.removeClass();
        if (i <= score) {
            item.addClass("icon-star");
        }
        else {
            item.addClass("icon-star-empty");
        }
    }
}

// Environment work rating

$(".environment-rating-stars").mouseover(function() {
    var score = this.getAttribute('data-value');
    setRatingStars("#environment-rating-stars-", score);
});

$("#environment-work-rating").mouseleave(function() {
    var rate = $(".environment-rating-stars").getAttribute('data-value');

    var new_score = setRatingStars("#environment-rating-stars-", rate);

    $("#environment_rating").val(new_score);
    var score = $("#environment_rating").val();
    console.log(score);

    setRatingStars("#environment-rating-stars-", score);
})


$(".environment-rating-stars").click(function() {
    var new_score = this.getAttribute("data-value");
    $("#environment_rating").val(new_score);

});
