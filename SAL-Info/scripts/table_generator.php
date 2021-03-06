<?php

function table_vertical($table_id, $table_title, $table_title_icon, $table_headers, $table_values) {
    $result = '<div id="' . $table_id . '"><h3 class="text-left text-body"><i class="fa fa-fw fa-' . $table_title_icon . '"></i>' . $table_title . '</h3><table class="table table-hover table-responsive"><tbody>';
    for($i = 0; $i < sizeof($table_headers); $i++) {
	$result .= '<tr>';
        $result .= '<td>' . $table_headers[0][$i] . '</td>';
	$result .= '<td>' . $table_values[$table_headers[1][$i]] . '</td>';
	$result .= '</tr>';
	$i += 1;
    }
}

function table_horizonal($table_id, $table_title, $table_title_icon, $table_headers, $table_index, $table_values, $ischeckTable = false, $check_name = '', $check_id = '') {
    $result = '<div id="' . $table_id . '"><h3 class="text-left text-body"><i class="fa fa-fw fa-' . $table_title_icon . '"></i>' . $table_title . '</h3><table class="table table-hover table-responsive"><tbody>';
    $result .= '<tr>';
    if ($ischeckTable) { $result .= '<th><input id="' . $check_id . '-0" type="checkbox" name="' . $check_name . '" value="all" /><label for="' . $check_id . '-0" class="checkbox02">#</label></th>'; }
    foreach ($table_headers as $var) { $result .= '<th>' . $var . '</th>'; }
    $result .= '</tr>';
    $i = 1;
    while ($row = $table_values->fetch_assoc()) {
	$result .= '<tr>';
        if ($ischeckTable) {
            $result .= '<td><input id="' . $check_id . '-' . $i . '" type="checkbox" name="' . $check_name . '" value="' . $row['USERID'] . '" /><label for="' . $check_id . '-' . $i . '" class="checkbox02">' . $i . '</label></td>';
        }
        $c_d = [];
        foreach ($table_index as $var) { array_push($c_d, $row[$var]); }
        foreach ($c_d as $var) { $result .= '<td>' . $var . '</td>'; }
        $i += 1;
	$result .= '</tr>';
    }

    //Exit : 4
    $result .= '</tr></tbody></table></div>';

    return $result;
}

function table_horizonal_v($table_id, $table_title, $table_title_icon, $table_headers, $table_index, $table_values, $ischeckTable = false, $check_name = '', $check_id = '') {
    $result = '<div id="' . $table_id . '"><h3 class="text-left text-body"><i class="fa fa-fw fa-' . $table_title_icon . '"></i>' . $table_title . '</h3><table class="table table-hover table-responsive"><tbody>';
    $result .= '<tr>';
    if ($ischeckTable) { $result .= '<th><input id="' . $check_id . '-0" type="checkbox" name="' . $check_name . '" value="all" /><label for="' . $check_id . '-0" class="checkbox02">#</label></th>'; }
    foreach ($table_headers as $var) { $result .= '<th>' . $var . '</th>'; }
    $result .= '</tr>';
    $i = 1;
    foreach($table_values as $row) {
	$result .= '<tr>';
        if ($ischeckTable) {
            $result .= '<td><input id="' . $check_id . '-' . $i . '" type="checkbox" name="' . $check_name . '" value="' . $row['USERID'] . '" /><label for="' . $check_id . '-' . $i . '" class="checkbox02">' . $i . '</label></td>';
        }
        $c_d = [];
        foreach ($table_index as $var) { array_push($c_d, $row[$var]); }
        foreach ($c_d as $var) { $result .= '<td>' . $var . '</td>'; }
        $i += 1;
	$result .= '</tr>';
    }

    //Exit : 4
    $result .= '</tr></tbody></table></div>';

    return $result;
}

function account_table() {
    $table_id = 'account_table';
    $table_title = 'アカウント一覧';
    $table_title_icon = 'group';
    $table_headers = [
        ["権限", "ユーザ名", "ユーザID", "最終ログイン日時"],
        ["PER", "USERNAME", "USERID", "LOGINUPTIME"]
    ];
    $result = select(false, "MKTK_USERS", "CASE PERMISSION WHEN 1 THEN '管理者' WHEN 2 THEN '受講者' END AS PER, USERID, USERNAME, LOGINUPTIME");
    if ($result) {
        return table_horizonal($table_id, $table_title, $table_title_icon, $table_headers[0], $table_headers[1], $result, true, 'index-s', 'index');
    } else {
        return false;
    }
}