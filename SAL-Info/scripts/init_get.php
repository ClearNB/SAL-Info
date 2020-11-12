<?php

$requestmg = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');

$request = isset($requestmg) ? strtolower($requestmg) : '';
if ($request !== 'xmlhttprequest') {
    http_response_code(403);
    header("Location: ../403.php");
    exit;
}

//変数の定義
$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

include_once ('./sqldata.php');
include_once ('./common.php');
include_once ('./dbconfig.php');
include_once ('./session_chk.php');
include_once ('./init.php');

//フォーム実行時に動的実行される
if ($method == 'GET') {
    $init_c = new initDatabase();
    $result = $init_c->init();
    ob_get_clean();
    echo json_encode(['res'=>$result]);
}