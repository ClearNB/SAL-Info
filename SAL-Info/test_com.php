<?php

/* 【データベース確認テスト】
 * (設置場所 ... 初期設定BASICERスクリプト)
 * 1. SELECT * を行い、値が取得できるか判断します。
 * 2. 取得ができない場合は CREATE 文で作成できるか確認します。
 * 3. できた場合は問題なし、できない場合は問題ありとし、true or false および結果HTMLを出力します。
 */

include_once ('./scripts/sqldata.php');
include_once ('./scripts/common.php');
include_once ('./scripts/dbconfig.php');
include_once ('./scripts/init.php');

//1: Loading
$form_load = new form_generator('form_load');
$form_load->SubTitle('データベース初期化中', 'しばらくお待ちください...', 'spinner fa-spin');

//1: Completed
$form_completed = new form_generator('form_completed');
$form_completed->SubTitle('成功！', 'データベースへの書き込みに成功しました！', 'check');

//2: Failed
$form_failed = new form_generator('form_failed');
$form_failed->SubTitle('読み込みに失敗しました', 'データベースへの接続に失敗しました。', 'times-circle');

$data = new initDatabase();
$result = $data->initDatabase();
if($result['RES']) {
    echo "成功";
} else {
    echo "失敗";
}
echo $result['HTML'];