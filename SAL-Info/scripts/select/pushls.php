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
    $work_type = explode('_', filter_input(INPUT_POST, 'wk_tp', FILTER_SANITIZE_SPECIAL_CHARS));
    $work_year = filter_input(INPUT_POST, 'wk_yr', FILTER_SANITIZE_SPECIAL_CHARS);
    $lsdata = explode('_', filter_input(INPUT_POST, 'lsid', FILTER_SANITIZE_SPECIAL_CHARS));
    
    session_start_once();
    $userindex = $_SESSION['mktk_userindex'];
    $err_flag = false;
    
    //[Insert] MKTK_USERS_SET (USERINDEX, YEARFLAG)
    $ls_in_res_1 = insert('MKTK_USERS_SET', ['USERINDEX', 'YEARFLAG'], [$userindex, $work_year]);
    $setid;
    if($ls_in_res_1) {
        $setid = select(true, 'MKTK_USERS_SET', 'SETID', 'WHERE USERINDEX = ' . $userindex);
    } else {
        $err_flag = true;
    }
    
    //[Insert] MKTK_USERS_SL (SETID, OCCRID)
    foreach($work_type as $var) {
        $ls_in_res_2 = insert('MKTK_USERS_SL', ['SETID', 'OCCRID'], [$setid['SETID'], $var]);
        if(!$ls_in_res_2) {
            $err_flag = true;
            break;
        }
    }
    
    //[Insert] MKTK_LS_LIST (SETID, LSID)
    foreach($lsdata as $var) {
        $lsnum = (int) $var;
        $ls_in_res_3 = insert('MKTK_LS_LIST', ['SETID', 'LSID'], [$setid['SETID'], $lsnum]);
        if(!$ls_in_res_3) {
            $err_flag = true;
            break;
        }
    }
    
    $code = 0;
    if($err_flag) {
        $code = 1;
    }
    ob_get_clean();
    echo json_encode(['res'=>$code]);
}