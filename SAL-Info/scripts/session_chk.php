<?php

function session_start_once() {
    if (session_status() == PHP_SESSION_NONE) {
	session_start();
    }
}

function session_chk() {
    session_start_once();
    $res01 = isset($_SESSION['mktk_userindex']);
    if (!$res01) {
	return 1;
    } else {
	$index = $_SESSION['mktk_userindex'];
	$res02 = select(true, 'MKTK_USERS', 'USERNAME', 'WHERE USERINDEX = ' . $index);
	if ($res02) {
	    return 0;
	} else {
	    return 2;
	}
    }
}

function user_table_check() {
    $res02 = select(true, 'MKTK_USERS_SET', 'COUNT(*) AS COUNT');
    if ($res02) {
	return true;
    } else {
	return false;
    }
}

function fails_check() {
    session_start_once();
    return (isset($_SESSION['mktk_login_fails']) && $_SESSION['mktk_login_fails'] > 3);
}

function failed_check() {
    session_start_once();
    return isset($_SESSION['mktk_failed']);
}

function update_fails() {
    session_start_once();
    if (!isset($_SESSION['mktk_login_fails'])) {
	$_SESSION['mktk_login_fails'] = 1;
    } else {
	$_SESSION['mktk_login_fails'] += 1;
    }
}

function failed() {
    unset($_SESSION['mktk_login_fails']);
    session_write_close();
    ini_set('session.gc_divisor', 1);
    ini_set('session.gc_maxlifetime', 600);
    session_start();
    $_SESSION['mktk_failed'] = 'failed';
}
