<?php

include_once ('../sqldata.php');
include_once ('../common.php');
include_once ('../dbconfig.php');
include_once ('checkers.php');

function generate_quiz($setid, $diff) {
    $ques_data = [];

    // 1: テーマ数を数える
    $sql01 = select(true, 'MKTK_USERS_SL a, MKTK_USERS_THEME b', 'SELECT COUNT(*) AS COUNT_LS_THEME', 'WHERE a.SETID = ' . $setid . ' AND a.OCCRID = b.OCCRID');

    if ($sql01) {
	/* 2. 問題数を計算
	 *  テーマ数 * 5
	 */
	$q_count = (int) $sql01['COUNT_LS_THEME'] * 5;

	/* 3. 問題一覧を生成
	 * SELECT LSID, QUESTION
	 * FROM   MKTK_LS_LIST a, MKTK_LS_TEST b
	 * WHERE  a.SETID = $setid
	 * AND    a.LSID = b.LSID
	 * AND    b.Difficulty = $diff
	 */
    } else {
	
    }
}

function result($index, $convertID) {
    
}