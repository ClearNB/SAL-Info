<?php

include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';

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
    $query01 = select(true, "MKTK_USERS_SET", "SETID, COMPLETEDFLAG", "WHERE USERINDEX = $index AND SETID = (SELECT MAX(SETID) FROM MKTK_USERS_SET WHERE USERINDEX = $index)");
    $code = 0;
    //1: 研修内容があるか
    if ($query01) {
        $setid = $query01['SETID'];
        $completedflag = $query01['COMPLETEDFLAG'];
	//2: すべての研修が完了しているか
        if ($completedflag !== 0) {
	    //3: 研修内容が正しくあるか
            $query02 = select(false, "MKTK_LS_LIST", "LSID", "WHERE SETID = $setid");
            if ($query02) {
                //4: 事前テストを実施したか
                $query03 = select(false, "MKTK_TEST", "TESTTYPEID", "WHERE SETID = $setid");
                if ($query03) {
                    $code = 4;
                } else {
                    $code = 3;
                }
            } else {
                $delete_query = delete("MKTK_USERS_SET", "WHERE SETID = $setid");
                if ($delete_query) {
                    $code = 1;
                } else {
                    $code = 5;
                }
            }
        }
    } else {
        $code = 1;
    }
    return $code;
}
