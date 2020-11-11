<?php

/* [Account Information Checkers]
 * Form Validation Checking Tool.
 * Coded by Minokutonika 2020.
 */

include_once '../sqldata.php';
include_once '../common.php';
include_once '../sqldata.php';

function check_username($data) {
    
}

function check_userid($data) {
    
}

function check_password($data) {
    if(!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{10,15}+\z/', $data)) {
        return '・パスワードルールに則っていません。';
    } else {
        return '';
    }
}

function check_auth_password($data1, $data2) {
    if($data1 != $data2) {
        return '・確認用パスワードが間違っています。';
    } else {
        return '';
    }
}