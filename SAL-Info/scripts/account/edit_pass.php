<?php

/* Edit Password Program
 * パスワードを変更の確認を行います。
 * ['res'] =>
 * 0. 正常終了
 * 1. データが異常（['message'] => 異常データ一覧）
 */
$requestmg = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');

$request = isset($requestmg)
        ? strtolower($requestmg) : '';
if($request !== 'xmlhttprequest') {
    http_response_code(403);
    header('Location: ' . __DIR__ . '/403.php');
    exit;
}

include_once '../sqldata.php';
include_once '../common.php';
include_once '../sqldata.php';
include_once './checkers.php';

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'GET') {
    
    //1. データの取得
    $pass = filter_input(INPUT_GET, 'pass', FILTER_SANITIZE_STRING);
    $r_pass = filter_input(INPUT_GET, 'r-pass', FILTER_SANITIZE_STRING);
    $c_pass = filter_input(INPUT_GET, 'r-pass', FILTER_SANITIZE_STRING);
    $err_arr = array();
    
    //2. データの確認
    array_push($err_arr, check_password($pass));
    array_push($err_arr, check_auth_password($r_pass, $pass));
    
    $err_text = implode($err_arr, '<br>');
    $code = 1;
    if($err_text == "") {
        $code = 0;
    }
    $r = [
        'res' => $code,
        'data' => $err_text
    ];
    echo json_encode($r);
}