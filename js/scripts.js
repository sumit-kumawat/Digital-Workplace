// Custom JavaScript can go here

// Optional: Smooth transition for tab switching
$(document).ready(function(){
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        $(target).css('opacity', '0');
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        $(target).css('opacity', '1').hide().fadeIn(650);
    });
});
