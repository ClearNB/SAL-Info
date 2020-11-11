<?php

/* Session Program
 * アカウントを登録します。
 * ['res'] =>
 * 0. 正常終了
 * 1. データが異常（['message'] => 異常データ一覧）
 * -1. 異常終了（データベース接続不可能）
 */
$requestmg = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');

$request = isset($requestmg) ? strtolower($requestmg) : '';
if($request !== 'xmlhttprequest') {
    http_response_code(403);
    header('Location: ../../403.php');
    exit;
}

include_once ('../sqldata.php');
include_once ('../common.php');
include_once ('../dbconfig.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'POST') {
    
    //1. データの取得
    $userid = filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
    $r_pass = filter_input(INPUT_POST, 'r-pass', FILTER_SANITIZE_STRING);
    $permission = filter_input(INPUT_POST, 'permission', FILTER_SANITIZE_STRING);
    $err_arr = array();
    
    //2. データの確認
    $result = select(true, "MKTK_USERS", "COUNT(*) AS USERCOUNT", "WHERE USERID = '$userid'");
    if($result['USERCOUNT'] > 0) {
        array_push($err_arr, '・ユーザIDが重複しています');
    }
    
    if(!ctype_lower($userid) && strlen(mb_convert_encoding($userid, 'SJIS', 'UTF-8')) > 20) {
        array_push($err_arr, '・ユーザID入力ルールに違反しています。');
    }
    
    if(strlen(mb_convert_encoding($username, 'SJIS', 'UTF-8')) > 30) {
        array_push($err_arr, '・ユーザ名が最大半角文字数30文字をを超えています。');
    }
    
    if(!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{10,15}+\z/', $pass)) {
        array_push($err_arr, '・パスワードルールに則っていません。');
    }
    
    if($r_pass !== $pass) {
        array_push($err_arr, '・確認用パスワードが間違っています。');
    }
    $err_text = implode($err_arr, '<br>');
    $code = 1;
    if($err_text === "") {
        $code = 0;
    }
    $r = [
        'res' => $code,
        'data' => $err_text
    ];
    echo json_encode($r);
}