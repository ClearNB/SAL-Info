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
include_once ('../outputStatus.php');

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'POST') {
    //f_num ... 1 (研修完了), 2（研修スライド切り替え）
    $functionid = filter_input(INPUT_POST, 'f_num', FILTER_SANITIZE_STRING);
    $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
    if (!$functionid) {
	$functionid = 0;
    }

    $result;
    switch ($functionid) {
	case 0:
	    $result = startSlide();
	    break;
	case 1:
	    $result = completeSlide();
	    break;
	case 2:
	    $result = changeSlide($data);
	    break;
    }
    echo json_encode($result);
}

function startSlide() {
    session_start_once();
    if (session_chk() == 0) {
	$index = $_SESSION['mktk_userindex'];

	$get01 = select(true, "MKTK_USERS_SET", "MAX(SETID) AS SETID", "WHERE USERINDEX = $index");
	if ($get01) {
	    $setid = $get01['SETID'];
	    $lsid = 0;
	    $get02 = select(true, "MKTK_USERS_SET", "LASTLSID", "WHERE SETID = $setid");
	    if ($get02 && $get02['LASTLSID'] != '') {
		$lsid = $get02['LASTLSID'];
	    } else {
		$get02_2 = select(true, "MKTK_LS_LIST a, MKTK_LS b", "MIN(b.LSID) as LSID", "WHERE a.SETID = " . $setid . " AND a.COMPLETEDFLAG = 0 AND a.LSID = b.LSID AND b.LSTYPEID = 1");
		if ($get02_2) {
		    $lsid = $get02_2['LSID'];
		} else {
		    $get02_3 = select(true, "MKTK_LS_LIST a, MKTK_LS b", "MIN(b.LSID) as LSID", "WHERE a.SETID = " . $setid . " AND a.LSID = b.LSID AND b.LSTYPEID = 1");
		    if($get02_3) {
			$lsid = $get02_3['LSID'];
		    } else {
			return ['code' => 1];
		    }
		}
	    }
	    $title = getTitle($lsid);
	    $slide = makeSlides($lsid);
	    $select = makeLessonSelect($setid);
	    if ($title && $slide && $select) {
		$_SESSION['mktk_ls'] = ['setid' => $setid, 'lsid' => $lsid];
		return ['code' => 0, 'title' => $title, 'slide' => $slide, 'select' => $select];
	    } else {
		return ['code' => 1];
	    }
	} else {
	    return ['code' => 1];
	}
    } else {
	return ['code' => 1];
    }
}

function completeSlide() {
    session_start_once();
    if (isset($_SESSION['mktk_ls'])) {
	$data = $_SESSION['mktk_ls'];
	$select = select(true, 'MKTK_LS_LIST', 'COMPLETEDFLAG', "WHERE SETID = " . $data['setid'] . ' AND LSID = ' . $data['lsid']);
	if ($select) {
	    if ($select['COMPLETEDFLAG'] == 1) {
		return ['code' => 0, 'text' => '完遂済みです！'];
	    } else {
		$update = update('MKTK_LS_LIST', 'COMPLETEDFLAG', '1', "WHERE SETID = " . $data['setid'] . " AND LSID = " . $data['lsid']);
		if (!$update) {
		    return ['code' => 1, 'err' => "更新エラー"];
		} else {
		    if(getCountUnCompleted_FromUser($data['setid']) == 0) {
			$update = update('MKTK_USERS_SET', 'COMPLETEDFLAG', '3', "WHERE SETID = " . $data['setid']);
			return ['code' => 2];
		    } else {
			return ['code' => 0, 'text' => 'この研修は完遂しました！'];
		    }
		}
	    }
	} else {
	    return ['code' => 1, 'err' => "データベースエラー"];
	}
    } else {
	return ['code' => 1, 'err' => "データベースエラー"];
    }
}

function changeSlide($data) {
    if ($data != 0) {
	session_start_once();
	$setid = $_SESSION['mktk_ls']['setid'];
	$_SESSION['mktk_ls'] = ['setid'=>$setid, 'lsid' => $data];
	$update = update('MKTK_USERS_SET', 'LASTLSID', $data, "WHERE SETID = " . $setid);
	if($update) {
	    return startSlide();
	} else {
	    return ['code' => 1];
	}
    } else {
	return ['code' => 1];
    }
}

function makeLessonSelect($setid) {
    $select = select(false, "MKTK_LS_LIST a, MKTK_LS b, MKTK_LS_FIELD c, MKTK_LS_THEME d", "d.LSTHEMENAME, c.LSFIELDNAME, b.LSID, b.LSNAME, a.COMPLETEDFLAG", "WHERE a.SETID = $setid AND a.LSID = b.LSID AND b.LSFIELDID = c.LSFIELDID AND c.LSTHEMEGROUPID = d.LSTHEMEGROUPID AND b.LSTYPEID = 1 GROUP BY d.LSTHEMENAME, c.LSFIELDNAME, b.LSID, b.LSNAME, a.COMPLETEDFLAG");
    $text = '<div class="select-wrap select-circle select-circle-arrow"><select class="select-pl" name="data" required>';
    if ($select) {
	$th_e = '';
	$fd_e = '';
	$text .= add_option("研修を選択:", 0, "disabled");
	while ($row = $select->fetch_assoc()) {
	    if ($th_e != $row['LSTHEMENAME']) {
		$th_e = $row['LSTHEMENAME'];
		$text .= add_option('テーマ - ' . $th_e, 0, "disabled");
	    }
	    if ($fd_e != $row['LSFIELDNAME']) {
		$fd_e = $row['LSFIELDNAME'];
		$text .= add_option('【' . $fd_e . '】', 0, "disabled");
	    }
	    if ($row['COMPLETEDFLAG'] == 0) {
		$text .= add_option($row['LSNAME'], $row['LSID']);
	    } else {
		$text .= add_option('【OK】' . $row['LSNAME'], $row['LSID']);
	    }
	}
	$text .= '</select></div>';
	return $text;
    } else {
	return false;
    }
}

function add_option($outname, $value = 0, $disabled = "") {
    return '<option value="' . $value . '" ' . $disabled . '>' . $outname . '</option>';
}

function getTitle($lsid) {
    $get = select(true, 'MKTK_LS', 'LSNAME', 'WHERE LSID = ' . $lsid);
    if ($get) {
	return $get['LSNAME'];
    } else {
	return false;
    }
}

function makeSlides($lsid) {
    $select = select(false, 'MKTK_LS_DATA', 'CONTENT', 'WHERE LSID = ' . $lsid);
    $text = "<ul class=\"slider\">";
    $i = 1;
    while ($row = $select->fetch_assoc()) {
	$content = $row["CONTENT"];
	$text .= "<li><a><img src=\"$content\" alt=\"image-$i\"></a></li>";
	$i += 1;
    }
    $text .= "</ul>";
    return $text;
}
