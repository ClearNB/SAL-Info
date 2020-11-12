<?php

$requestmg = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');

$request = isset($requestmg) ? strtolower($requestmg) : '';
if ($request !== 'xmlhttprequest') {
    http_response_code(403);
    header('Location: ../../403.php');
    exit;
}

include_once ('../sqldata.php');
include_once ('../common.php');
include_once ('../dbconfig.php');
include_once ('../session_chk.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'POST') {
    $work_year = filter_input(INPUT_POST, 'sel-yr', FILTER_SANITIZE_SPECIAL_CHARS);
    $lsdata = filter_input(INPUT_POST, 'lsid', FILTER_SANITIZE_SPECIAL_CHARS);
    
    $ls_array = explode('_', $lsdata);
    $n_a = [];
    foreach($ls_array as $v) {
        $n_a[] = (int) $v;
    }
    
    session_start_once();
    $userindex = $_SESSION['mktk_userindex'];
}