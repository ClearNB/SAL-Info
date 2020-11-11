<?php

include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';

/* [Confirm Lesson]
 * 今接続されているユーザで研修データが存在するか確認します。
 * [セットフラグ]
 * 研修内容データ, 0 ... LESSONフラグ
 * 1 ... SELECTフラグ
 * 2 ... COMPLETEDフラグ
 * 3 ... TESTフラグ
 * 4 ... ERRORフラグ
 */

function confirm_lessondata($index) {
    $query01 = select(true, "MKTK_USERS_SET", "SETID", "WHERE USERINDEX = $index AND COMPLETEDFLAG = 0");
    if($query01) {
        $setid = $query01['SETID'];
        $query02 = select(false, "MKTK_LS_LIST", "LSID, COMPLETEDFLAG", "WHERE SETID = $setid");
        if($query02) {
            $lsdata = ["LSID" => [], "COMPLETEDFLAG" => []];
            while($row = $query02->fetch_assoc()) {
                $lsdata["LSID"] = $row["LSID"];
                $lsdata["COMPLETEDFLAG"] = $row["COMPLETEDFLAG"];
            }
            
        } else {
            //該当するSETIDの情報を削除
            $delete_query = delete("MKTK_USERS_SET", "WHERE SETID = $setid");
            if($delete_query) {
                return 1;
            } else {
                return 4;
            }
        }
    } else {
        return 1;
    }
}