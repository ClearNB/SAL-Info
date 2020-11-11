
function ajax_dynamic_post(url, data) {
    return $.ajax({
        type: 'POST',
        url: url,
        data: data,
        crossDomain: false,
        dataType: 'json'
    });
}
