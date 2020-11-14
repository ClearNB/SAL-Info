<?php

/* [TESTのデータの送出を行います]
 * 
 * ●順序について
 * 0. セッションを読み取れるか確認します
 * 1. セッション情報を読み取ります（初期値参考）
 * 2-1. セッションがあれば以下の処理を行います
 *  2-1-1. ファンクション番号を読み取ります
 *  [ファンクション番号: 1 ▶ 【初期化】]
 * 2-2. セッションがなければ【初期化】を行います
 * 3. 【読み取り】 ※この時点でエラーがある場合、行いません
 * 
 * 【読み取り】
 *  F1-1. S -> "mktt_test_count" as $a を呼び出します
 *  F1-2. S ->  
 * 
 * 【初期化】
 *  F2-1. セッション情報の登録のために、問題作成を行います (./test_generate.php->generate_quiz)
 *  F2-2. エラーがなければその情報をセッションに登録します
 *  
 * ●セッションについて
 * ・セッション情報は、問題データの保存を行います
 * ・途中でセッション情報内にデータがない場合、テストを中断します
 * ・セッション情報は、テストを始めた際、またはテスト終了後に初期化（もしくは破棄）されます
 * [セッション初期値]
 * S -> "mktk_test_count" : 1
 * S -> "mktk_test_max_count" : n
 * S -> "mktk_test_ques_data" : ['t_data'][0..n], ['t_cid'][0..n]
 * ※ques_data は多層化連想配列になっています
 * 
 * ●フラグについて
 * 1: 問題を送出します
 * R -> "code" : 1
 * R -> "data" : ques = '...'
 * S -> "count" : +1
 * 2: 問題がの送出が終了しました（result結果が送出）
 *   -> 
 * 3: データベースエラー
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
include_once ('../session_chk.php');
include_once ('checkers.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'POST') {
    $functionid = filter_input(INPUT_POST, 'f_num', FILTER_SANITIZE_STRING);
    $code = 0;
    
    session_start_once();
    if(isset($_SESSION['mktk_test_count']) && isset($_SESSION['mktk_test_max_count']) && isset($_SESSION['mktk_test_ques_data'])) {
	if($functionid == 1) {
	    
	}
    } else if(isset($_SESSION['mktk_userindex'])) {
	initializeQuiz();
    } else {
	$code = 1;
    }
}

function initializeQuiz() {
    $userid = filter_input(INPUT_POST, 'cr_userid', FILTER_SANITIZE_STRING);
}