$(document).on('change', 'input[name="index-s"]', function () {
    var con = $(this).val();
    var s = 0;
    if ($('#index-0').prop('checked') === true) {
        s = 1;
    }
    if (con === 'all') {
        var changeFlag = $(this).prop("checked");
        $("#account_table input:checkbox").prop("checked", changeFlag);
    } else {
        var c_none_checked = $("#account-data input:checkbox").length
		- $("#account-data input:checkbox:checked").length - s - 1;
	console.log(c_none_checked);
        if (c_none_checked === 0) {
            $('#index-0').prop('checked', true);
            s = 1;
        } else {
            $('#index-0').prop('checked', false);
            s = 0;
        }
    }
    var c_checked = $("#account_table input:checkbox:checked").length - s;
    console.log(c_checked);
    if (c_checked > 0) {
        if (c_checked === 1) {
            $('#bt_ac_ed').prop("disabled", false);
        } else {
            $('#bt_ac_ed').prop("disabled", true);
        }
        $('#bt_ac_dl').prop("disabled", false);
    } else {
        $('#bt_ac_ed').prop("disabled", true);
        $('#bt_ac_dl').prop("disabled", true);
    }
});