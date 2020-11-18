<?php

/* Session Program
 * ログインセッション処理を行います。
 * ['res']
 * -1. 異常終了（ユーザまたはパスワードが間違っている）
 * 0. 正常終了
 * 1. 異常終了（データベース接続不可能）
 */

$requestmg = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');

$request = isset($requestmg) ? strtolower($requestmg) : '';
if ($request !== 'xmlhttprequest') {
    http_response_code(403);
    header("Location: ../403.php");
    exit;
}

$session_time = 1500;
$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

include_once ('./sqldata.php');
include_once ('./common.php');
include_once ('./dbconfig.php');
include_once ('./session_chk.php');

if ($method == 'POST') {
    //値の取得
    $userid = filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    //ソルトの取得
    $result = select(true, "MKTK_USERS", "SALT", "WHERE BINARY USERID = '$userid'");
    if (!$result) {
	$r_text = -1;
	if (fails_check()) {
	    failed();
	} else {
	    update_fails();
	}
	echo json_encode(['res' => $r_text]);
    } else {
	$salt = $result['SALT'];
	if ($salt === "") {
	    if (fails_check()) {
		failed();
	    } else {
		update_fails();
	    }
	    $r_text = -1;
	} else {
	    $hash = hash('sha256', $pass . $salt);

	    $result = select(true, "MKTK_USERS", "(PASSWORDHASH = '$hash') AS PASSWORD_MATCHES", "WHERE BINARY USERID = '$userid'");
	    $password_matches = $result['PASSWORD_MATCHES'];

	    if ($password_matches) {
		$result = select(true, "MKTK_USERS", "USERINDEX", "WHERE BINARY USERID = '$userid'");
		$userindex = $result['USERINDEX'];

		session_write_close();
		ini_set('session.gc_divisor', 1);
		ini_set('session.gc_maxlifetime', $session_time);
		session_start();
		unset($_SESSION['mktk_login_fails']);
		$_SESSION['mktk_userindex'] = $userindex;
		
		$r = update("MKTK_USERS", "LOGINUPTIME", date("Y-m-d H:i:s"), "WHERE USERINDEX=$userindex");

		if ($r) {
		    $r_text = 0;
		} else {
		    $r_text = 1;
		}
	    } else {
		if (fails_check()) {
		    failed();
		} else {
		    update_fails();
		}
		$r_text = -1;
	    }
	}
	ob_get_clean();
	echo json_encode(['res' => $r_text]);
    }
}