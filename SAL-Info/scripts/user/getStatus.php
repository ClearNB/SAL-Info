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

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
if ($method === 'GET') {
    /* ステータス表示
     * [ステータス表]
     * ・研修番号（SetID）
     * ・研修状況（SELECT・TEST（事前テスト）・LESSON・TEST（確認テスト））
     * ・進捗状況（パーセンテージ）※LESSONのみ *
     * [SELECT]
     * ・研修番号（SetID）
     * ・選択研修テーマ
     * ・研修数（テストデータを除く） *
     * [LESSON]
     * ・研修番号（SetID）
     * ・総研修数 *
     * ・研修完了数 *
     * [TEST]
     * （テスト履歴がある場合）
     * ・研修番号（SetID）
     * ・テストタイプ（事前テスト or 確認テスト）
     * ・問題数
     * ・合計点
     * ・実施日時
     */
    
    //1: get setid
    $sql_s01 = select(false, 'MKTK_USERS_SET', 'SETID', "WHERE USERINDEX = $index");
    
    //2: get
    
    //ob_get_clean();
    echo json_encode($d);
}