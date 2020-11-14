<?php
/* SAL Info - Coded
 * [2020 Minokutonika Project Team All Rights Reserved.]
 * PHP Information
 * - Date: 2020/11/8 Updated.
 * - Page Name: Option -Account
 * - Desc: {host}/option/account.php
 * - Launguage: Japanese
 */

include ('../scripts/session_chk.php');
session_start();
if(!session_chk()) {
    http_response_code(301);
    header('location: ../403.php');
    exit();
}

include_once '../scripts/common.php';
include_once '../scripts/sqldata.php';
include_once '../scripts/dbconfig.php';
include_once '../scripts/former.php';
include_once '../scripts/loader.php';

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");

if($getdata['PERMISSION'] != 1) {
    http_response_code(403);
    header("Location: ../403.php");
    exit();    
}

//Create User
$fm_cf = new form_generator('form_confirm');
$fm_cf->SubTitle('データの初期化を行います',
        '問題・研修内容について初期化します',
        'refresh');
$fm_cf->SubTitle('注意', '以下をご確認ください', 'exclamation-triangle');
$fm_cf->openList();
$fm_cf->addList('この操作を行うことにより、問題データならび研修データを入れ替えます');
$fm_cf->addList('あらかじめ用意されているデータパック（JSON）を所定の位置に配置してください');
$fm_cf->addList('この操作により、受講者の受講した内容の履歴がすべて初期化されます（受講した履歴・テスト履歴は残ります）');
$fm_cf->addList('この操作には管理者権限が必要です');
$fm_cf->closeList();
$fm_cf->openRow();
$fm_cf->Buttonx2('bttn_ok_cf', '実行する', 'button', 'play-circle', 'orange');
$fm_cf->Buttonx2('bttn_back_cf', '設定一覧に戻る', 'button', 'chevron-circle-left', 'gray');
$fm_cf->closeDiv();

//Loading
$fm_ld = new form_generator('form_loading');
$fm_ld->SubTitle('処理中です...', 'ブラウザを閉じないでしばらくお待ちください...', 'refresh fa-spin');

//Failed
$fm_fl = new form_generator('form_failed');
$fm_fl->SubTitle("手続きに失敗しました。", "データベースの状態を確認してください。", "exclamation-triangle");
$fm_fl->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>データベースの設定を見直してください。</li><li>この件は管理者に必ず相談してください。</li></ul>");
$fm_fl->openRow();
$fm_fl->Button('bttn_restart_fl', '再試行', 'button', 'play-circle', 'orange');
$fm_fl->Button('bttn_back_fl', '設定一覧に戻る', 'button', 'chevron-circle-left', 'gray');
$fm_fl->closeDiv();

//Completed
$fm_cp = new form_generator('form_completed');
$fm_cp->SubTitle("設定完了しました！", "下記ボタンで設定一覧へ遷移します。", "thumbs-up");
$fm_cp->Button('bttn_back_cp', '設定一覧に戻る', 'button', 'chevron-circle-left', 'gray');

$loader = new loader();
?>

<html>
    <head>
        <?php echo $loader->loadHeader("SAL Info", "OPTION - ACCOUNT", true) ?>
        <script type="text/javascript">
            var fm_cf = '<?php echo $fm_cf->Export() ?>';
            var fm_ld = '<?php echo $fm_ld->Export() ?>';
            var fm_fl = '<?php echo $fm_fl->Export() ?>';
            var fm_cp = '<?php echo $fm_cp->Export() ?>';
        </script>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader(4, 0, $getdata['USERNAME']) ?>
        
        <div class="py-1 bg-title"">
            <div class="container">
                <div class="row">
                    <?php
                        echo $loader->Title('INITIALIZE', 'refresh');
                    ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3">
            <div class="container " id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <?php echo $loader->loadFootS(true) ?>
        
        <script type="text/javascript">
            $(document).ready(function() {
                animation('data_output', 400, fm_cf);
            });
            
            $(document).on('click', '#bttn_back_cf, #bttn_back_fl, #bttn_back_cp', function() {
                animation_to_sites('data_output', 400, './');
            });
            
            $(document).on('click', '#bttn_ok_cf, #bttn_restart_fl', function() {
                animation('data_output', 400, fm_ld);
                ajax_dynamic_get('../scripts/init_get.php').then(function(data) {
                    switch(data['res']) {
                        case 0: animation('data_output', 400, fm_cp); break;
                        case 1: animation('data_output', 400, fm_fl); break;
                    }
                });
            });
        </script>
    </body>
</html>