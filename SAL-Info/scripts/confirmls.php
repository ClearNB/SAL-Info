<?php

/* [Confirm Lesson]
 * 今接続されているユーザで研修データが存在するか確認します。
 * [セットフラグ]
 * 0 ... LESSONフラグ
 * 1 ... SELECTフラグ (初期設定)
 * 2 ... SELECTフラグ (新規設定)
 * 3 ... TESTフラグ (事前テスト)
 * 4 ... TESTフラグ (確認テスト)
 * 5 ... ERRORフラグ
 */

function confirm_lessondata($index) {
    $query01 = select(true, "MKTK_USERS_SET", "COMPLETEDFLAG", "WHERE SETID = (SELECT MAX(SETID) FROM MKTK_USERS_SET WHERE USERINDEX = $index)");
    $code = 0;
    //1: 研修内容があるか
    if($query01) {
	$code = $query01['COMPLETEDFLAG'];
    } else {
	$code = 1;
    }
    return $code;
}
