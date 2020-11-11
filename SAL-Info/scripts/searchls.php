<?php

$requestmg = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');

$request = isset($requestmg) ? strtolower($requestmg) : '';
if ($request !== 'xmlhttprequest') {
    http_response_code(403);
    header('Location: ../403.php');
    exit;
}

include_once ('../scripts/sqldata.php');
include_once ('../scripts/common.php');
include_once ('../scripts/dbconfig.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'POST') {
    //1. データの取得
    $work_type = filter_input(INPUT_POST, 'wk_tp', FILTER_SANITIZE_SPECIAL_CHARS);
    $text = str_replace('_', ', ', $work_type);
    $select_sql = select(false, 'MKTK_OCCR a, MKTK_LS_THEME b',
            'DISTINCT LSTHEMEGROUPID, LSTHEMENAME',
            "WHERE a.OCCRID = b.OCCRID AND a.OCCRID IN ($text)");
    if ($select_sql) {
        $theme_list = '<ul class="title-view">';
        $group_ids = [];
        $group_names = [];
        while ($var = $select_sql->fetch_assoc()) {
            array_push($group_ids, $var['LSTHEMEGROUPID']);
            array_push($group_names, $var['LSTHEMENAME']);
        }
        $group_ids_row = implode($group_ids, ', ');
        $select02_sql = select(false, 'MKTK_LS_THEME a, MKTK_LS_FIELD b, MKTK_LS c',
                'c.LSID',
                'WHERE a.LSTHEMEGROUPID IN (' . $group_ids_row . ') AND '
                . 'a.LSTHEMEGROUPID = b.LSTHEMEGROUPID AND b.LSFIELDID = c.LSFIELDID AND '
                . 'c.LSTYPEID = 1'
        );
        if($select02_sql) {
            while ($var = $select02_sql->fetch_assoc()) {
                    echo json_encode(["res" => $theme_data]);
            }
        }

    } else {
        $theme_data = "該当データなし";
        echo json_encode(["res" => $theme_data]);
    }
}