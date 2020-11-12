function animation_update_slides(element, duration, data) {
    $('#' + element).hide(duration, function () {
        $('#' + element).html(data);
        $('#' + element).show(duration, function () {
            var d = document.getElementsByClassName('slider');
            $(d).slick({
                focusOnSelect: true,
                infinite: false,
                touchMove: true
            });
        });
    });
}

function animation(output_id, duration, data) {
    $('#' + output_id).hide(duration, function () {
        $('#' + output_id).html(data);
        $('#' + output_id).show('slow'); 
    });
}