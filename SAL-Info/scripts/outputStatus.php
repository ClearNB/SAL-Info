<?php

/* 現在の進捗状態をHTMLコードとして渡すプログラム
 * outputStatus
 * 失敗時はすべてのファンクションでfalseを返します
 */

function getStatus() {
    $index = getSetID();
    if ($index) {
	switch (confirm_lessondata($index)) {
	    case 0:
		$cnt = getLessonCompletedCount();
		$c_cnt = getLessonCount();
		$per = "研修中: " . Intval(($c_cnt / $cnt) * 100) . " % ($cnt / $c_cnt)";
		return $per;
	    case 1: case 2:
		return "SELECT未実施";
	    case 3:
		return "事前テスト未実施";
	    case 4:
		return "確認テスト未実施";
	    case 5:
		return false;
	}
    }
}

function getStatusFromUser($index) {
    switch (confirm_lessondata($index)) {
	case 0:
	    $cnt = getLessonCompletedCount();
	    $c_cnt = getLessonCount();
	    $per = "研修中: " . Intval(($c_cnt / $cnt) * 100) . " % ($cnt / $c_cnt)";
	    return $per;
	case 1: case 2:
	    return "SELECT未実施";
	case 3:
	    return "事前テスト未実施";
	case 4:
	    return "確認テスト未実施";
	case 5:
	    return "";
    }
}

function getStatusNameFromUser($index) {
    switch (confirm_lessondata($index)) {
	case 0:
	    return "LESSON";
	case 1:
	    return "SELECT (初期)";
	case 2:
	    return "SELECT (新規)";
	case 3:
	    return "TEST（事前）";
	case 4:
	    return "TEST（確認）";
	case 5:
	    return false;
    }
}

function getLastLSID() {
    $setid = getSetID();
    $get01 = select(true, "MKTK_USERS_SET", "LASTLSID", "WHERE SETID = $setid");
    if ($get01) {
	return $get01['LASTLSID'];
    } else {
	$get02 = select(true, "MKTK_LS_LIST", "MIN(LSID) as LSID", "WHERE SETID = $setid AND COMPLETEDFLAG = 0");
	if ($get02) {
	    return $get02['LSID'];
	} else {
	    return false;
	}
    }
}

function getIndex() {
    if (session_chk() == 0) {
	return $_SESSION['mktk_userindex'];
    } else {
	return false;
    }
}

function getSetID() {
    $index = getIndex();
    if ($index) {
	$get = select(true, "MKTK_USERS_SET", "MAX(SETID) AS SETID", "WHERE USERINDEX = $index");
	if ($get) {
	    return $get['SETID'];
	} else {
	    return false;
	}
    } else {
	return false;
    }
}

function getSetIDFromUser($index) {
    $get = select(true, "MKTK_USERS_SET", "MAX(SETID) AS SETID", "WHERE USERINDEX = $index");
    if ($get) {
	return $get['SETID'];
    } else {
	return false;
    }
}

function getSetCountFromUser($index) {
    $get = select(true, "MKTK_USERS_SET", "COUNT(*) AS COUNT", "WHERE USERINDEX = $index");
    if ($get) {
	return $get['COUNT'];
    } else {
	return false;
    }
}

function getAllSetID() {
    $index = getIndex();
    if ($index) {
	$get = select(true, "MKTK_USERS_SET", "MAX(SETID) AS SETID");
	if ($get) {
	    return $get['SETID'];
	} else {
	    return false;
	}
    } else {
	return false;
    }
}

function getLessonCount() {
    $setid = getSetID();
    if ($setid) {
	$get = select(true, "MKTK_LS_LIST a, MKTK_LS b", "COUNT(*) as LESSONNUM", "WHERE a.SETID = $setid AND a.LSID = b.LSID AND b.LSTYPEID = 1");
	if ($get) {
	    return $get['LESSONNUM'];
	} else {
	    return false;
	}
    } else {
	return false;
    }
}

function getCountUnCompleted_FromUser($setid) {
    $get = select(true, "MKTK_LS_LIST a, MKTK_LS b", "COUNT(*) as COMPLETEDNUM", "WHERE a.SETID = $setid AND a.COMPLETEDFLAG = 0 AND a.LSID = b.LSID AND b.LSTYPEID = 1");
    if ($get) {
	return $get['COMPLETEDNUM'];
    } else {
	return false;
    }
}

function getLessonCompletedCount() {
    $setid = getSetID();
    if ($setid) {
	$get = select(true, "MKTK_LS_LIST a, MKTK_LS b", "COUNT(*) as COMPLETEDNUM", "WHERE a.SETID = $setid AND a.COMPLETEDFLAG = 1 AND a.LSID = b.LSID AND b.LSTYPEID = 1");
	if ($get) {
	    return $get['COMPLETEDNUM'];
	} else {
	    return false;
	}
    } else {
	return false;
    }
}

function getUsersStudy() {
    return select(false, 'MKTK_USERS', 'USERINDEX, USERNAME', 'WHERE PERMISSION = 2');
}

function getUserName($index) {
    $get = select(true, 'MKTK_USERS', 'USERNAME', 'WHERE USERID = ' . $index);
    if ($get) {
	return $get['USERNAME'];
    } else {
	return false;
    }
}

function getStatusTable() {
    $headers = [
	['ユーザ名', '回数', '進捗名', '進捗状況'],
	['USERNAME', 'COUNT', 'STATUS_NAME', 'STATUS']
    ];
    $id = 'users_status';
    $title = "ユーザステータス表";
    $value_arr = [];
    $data = "";
    //MKTK_USERS_SETにデータがない場合は、以下に統一する
    //COUNT - 1, STATUS_NAME - SELECT(初期), STATUS - 未達成
    $users = getUsersStudy();
    if ($users) {
	while ($row = $users->fetch_assoc()) {
	    $index = $row['USERINDEX'];
	    $username = $row['USERNAME'];
	    $count = 1;
	    $status_name = "SELECT（初期）";
	    $status = "未達成";
	    $get = getSetIDFromUser($row['USERINDEX']);
	    if ($get) {
		$count = getSetCountFromUser($index);
		$status_name = getStatusNameFromUser($index);
		$status = getStatusFromUser($index);
	    }
	    array_push($value_arr, ["USERNAME" => $username, "COUNT" => $count, "STATUS_NAME" => $status_name, "STATUS" => $status]);
	}
	$data = table_horizonal_v($id, $title, 'group', $headers[0], $headers[1], $value_arr);
    } else {
	$data = false;
    }
    return $data;
}

function getUserStatusTable() {
    $headers = [
	['ユーザ名', '回数', '進捗名', '進捗状況'],
	['USERNAME', 'COUNT', 'STATUS_NAME', 'STATUS']
    ];
    $id = 'users_status';
    $title = "ステータス";
    $value_arr = [];
    $data = "";
    $index = getIndex();
    if ($index) {
	$user = getUserName($index);
	if ($user) {
	    $count = 1;
	    $status_name = "SELECT（初期）";
	    $status = "未達成";
	    $get = getSetIDFromUser($index);
	    if ($get) {
		$count = getSetCountFromUser($index);
		$status_name = getStatusNameFromUser($index);
		$status = getStatusFromUser($index);
	    }
	    array_push($value_arr, ["USERNAME" => $user, "COUNT" => $count, "STATUS_NAME" => $status_name, "STATUS" => $status]);
	    $data = table_horizonal_v($id, $title, 'group', $headers[0], $headers[1], $value_arr);
	} else {
	    $data = false;
	}
    } else {
	$data = false;
    }

    return $data;
}
