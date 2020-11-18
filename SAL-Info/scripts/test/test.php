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
include_once ('../dbconfig.php');
include_once ('../session_chk.php');
include_once ('./test_generate.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'POST') {
    //f_num ... 1 (初期化・ロード), 2（次の問題へ）
    $functionid = filter_input(INPUT_POST, 'f_num', FILTER_SANITIZE_STRING);
    $answer = filter_input(INPUT_POST, 'f_ans', FILTER_SANITIZE_STRING);
    if(!$functionid) {
	$functionid = 1;
    }
    if(!$answer) {
	$answer = '';
    }
    $code = 0;
    
    //1: Confirm to Initialize
    session_start_once();
    $result = true;
    $d = '';
    if(isset($_SESSION['mktk_test']) && session_chk() == 0) {
	if($functionid == 1) {
	    $result = initializeQuiz();
	}
    } else if(session_chk() == 0) {
	$result = initializeQuiz();
    } else {
	$d = ['code'=>1];
    }
    if($result) {
	//2: Get the Object.
        $obj = $_SESSION['mktk_test'];
	if($functionid == 2) {
	    $obj->setAns($answer);
	    $_SESSION['mktk_test'] = $obj;
	}
	$data = $obj->getQues();
	if($data == 2) {
	    $r = $obj->getResult();
	    if(!$r) {
		$d = ['code'=>1];
	    } else {
		$d = ['code'=>$data, 'html'=>$r['html'], 'score'=>$r['score'], 'time'=>$r['time']];
	    }
	} else {
	    $d = ['code'=>$code, 'ques'=>$data['ques'], 'count'=>$obj->getCount(), 'max_count'=>$obj->getMax()];
	}
    } else {
	$d = ['code'=>1];
    }
    //ob_get_clean();
    echo json_encode($d);
}

function initializeQuiz() { 
    unset($_SESSION['mktk_test']);
    
    $index = $_SESSION['mktk_userindex'];
    
    $select = select(true, 'MKTK_USERS_SET', 'MAX(SETID) AS SETID', 'WHERE USERINDEX = ' . $index);
    if($select) {
	$quiz = new Quiz($select['SETID']);
	$_SESSION['mktk_test'] = $quiz;
	return true;
    } else {
	return false;
    }
}