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
if ($request !== 'xmlhttprequest') {
    http_response_code(403);
    header('Location: ../../403.php');
    exit;
}

include_once ('../sqldata.php');
include_once ('../common.php');
include_once ('../dbconfig.php');
include_once ('./checkers.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'POST') {

    //1. データの取得
    $function_id = filter_input(INPUT_POST, 'f_num', FILTER_SANITIZE_STRING);
    $userid = filter_input(INPUT_POST, 'cr_userid', FILTER_SANITIZE_STRING)
	    || filter_input(INPUT_POST, 'ch_n_n_ui', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'cr_username', FILTER_SANITIZE_STRING)
	    || filter_input(INPUT_POST, 'ch_n_n_un', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'cr_pass', FILTER_SANITIZE_STRING)
	    || filter_input(INPUT_POST, 'ch_p_n_ps', FILTER_SANITIZE_STRING);
    $r_pass = filter_input(INPUT_POST, 'cr_r_pass', FILTER_SANITIZE_STRING)
	    || filter_input(INPUT_POST, 'ch_p_nr_ps', FILTER_SANITIZE_STRING);
    $permission = filter_input(INPUT_POST, 'permission', FILTER_SANITIZE_STRING);
    
    //入力項目確認
    $function = 0;
    if($userid && $username && $pass && $r_pass && $permission) {
	$function = 1;
    } else if($userid) {
	$function = 2;
    } else if($username) {
	$function = 3;
    } else if($pass && $r_pass) {
	$fucntion = 4;
    }
    //相違確認
    if($function != $function_id) {
	$function = 0;
    }
    
    $err_arr = array();

    //2. データの確認
    switch ($function) {
	case 0: //FAILED
	    array_push($err_arr, '・正しく値が取得できませんでした');
	    array_push($err_arr, '・あなたの行っている行為と入力した項目が一致しません');
	    array_push($err_arr, '・不正行為は行わないようにお願い致します');
	case 1: //CREATE
	    array_push($err_arr, check_userid($userid));
	    array_push($err_arr, check_username($username));
	    array_push($err_arr, check_password($pass));
	    array_push($err_arr, check_conf_password($pass, $r_pass));
	    array_push($err_arr, check_permission($permission));
	    break;
	case 2: //CHANGE USERID
	    array_push($err_arr, check_userid($userid));
	    break;
	case 3: //CHANGE USERNAME
	    array_push($err_arr, check_username($username));
	    break;
	case 4: //CHANGE PASSWORD
	    array_push($err_arr, check_conf_password($pass, $r_pass));
	    break;
    }

    $err_text = implode('<br>', array_filter($err_arr));
    $code = 0;

    $r = [
	'res' => $code,
	'data' => $code,
    ];
    ob_get_clean();
    echo json_encode($r);
}