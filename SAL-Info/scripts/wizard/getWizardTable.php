<?php

$requestmg = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');

$request = isset($requestmg) ? strtolower($requestmg) : '';
if($request !== 'xmlhttprequest') {
    http_response_code(403);
    header('Location: ../../403.php');
    exit;
}

include_once ('../sqldata.php');
include_once ('../common.php');
include_once ('../confirmls.php');
include_once ('../dbconfig.php');
include_once ('../session_chk.php');
include_once ('../outputStatus.php');
include_once ('../table_generator.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'GET') {
    $d = getStatusTable();
    $data = $d;
    if(!$d) {
	$data = "受講者はいません。ユーザを作りましょう。";
    }
    ob_get_clean();
    echo json_encode(["time"=>date("Y-m-d H:i:s"), "data"=>$data]);
}

