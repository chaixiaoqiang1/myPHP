$(function() {
    $(window).scroll(function() {
        if ($(window).scrollTop() > 100)
            $('.jl').css('background','#F7AB00');
        else
            $('.jl').css('background','none');
    });
});
