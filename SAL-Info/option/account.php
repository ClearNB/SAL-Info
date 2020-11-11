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
include_once '../scripts/table_generator.php';

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");

if($getdata['PERMISSION'] != 1) {
    http_response_code(403);
    header("Location: ../403.php");
    exit();    
}

//1: アカウント表
$fm_ac = new form_generator('form_accounts');
$data = account_table();
if (!$data) {
    $data = "TABLE_DATA";
}
$fm_ac->Caption($data . '【操作方法】<ul class="title-view">'
        . '<li><i class="fa fa-user-plus"></i>作成 ... 以下の【作成】ボタンを押してください。</li>'
        . '<li><i class="fa fa-pencil-square-o"></i>編集 ... 一覧表隣のチェックボックスに1件だけ選択し、【編集】ボタンを押してください。</li>'
        . '<li><i class="fa fa-trash"></i>削除 ... 一覧表隣のチェックボックスに該当部分を選択し、【削除】ボタンを押してください。'
        . '<li><i class="fa fa-chevron-circle-left"></i>戻る ... 設定一覧へ戻ります。</li>'
        . '</ul>');
$fm_ac->Button('bttn_ac_cr', '作成',           'button', 'user-plus');
$fm_ac->Button('bttn_ac_ed', '編集',           'button', 'pencil-square-o');
$fm_ac->Button('bttn_ac_dl', '削除',           'button', 'trash');
$fm_ac->Button('bttn_ac_bk', '設定一覧へ戻る', 'button', 'chevron-circle-left', 'gray');

//Create User
$fm_cr = new form_generator('create_account');
$fm_cr->SubTitle ('ACCOUNT CREATE', 'アカウントを作成します。', 'user-plus', 'gray');
$fm_cr->Input    ('cr_userid', 'ユーザID', '最大20文字, 重複なし, 半角英数字', 'id-card-o', true, false);
$fm_cr->Input    ('cr_username', 'ユーザ名', '半角最大30文字, 全角最大15文字', 'address-book', true, false);
$fm_cr->SubTitle ('パスワード', 'このアカウントのパスワードを入力してください。', 'key');
$fm_cr->Password ('cr_pass', 'パスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true, false);
$fm_cr->Password ('cr_r_pass', 'パスワードの確認', 'もう一度入力してください。', 'key', true, false);
$fm_cr->SubTitle ('権限の設定', '権限を設定してください。', 'group');
$fm_cr->Check    (1, 'cr_ow-sel', 'permission', '1', '管理者', false);
$fm_cr->Check    (1, 'cr_stu-sel', 'permission', '2', '受講者', true);
$fm_cr->Button   ('bttn_cr_sb', 'アカウントを登録', 'button', 'check-square', 'orange');
$fm_cr->Button   ('bttn_cr_bk', '戻る', 'button', 'chevron-circle-left', 'gray');

//Edit User
$fm_ed = new form_generator('edit_account');
$fm_ed->SubTitle('[USER] さん', 'アカウントを編集します。以下から選択してください。', 'pencil-square-o');
$fm_ed->Button('bttn_ed_cr', 'ユーザID',            'button', 'address-card');
$fm_ed->Button('bttn_ed_ed', 'ユーザ名',            'button', 'user-circle');
$fm_ed->Button('bttn_ed_dl', 'パスワード',          'button', 'trash');
$fm_ed->Button('bttn_ed_bk', 'アカウント一覧へ戻る', 'button', 'chevron-circle-left', 'gray');

//Change Password
$fm_ch_p = new form_generator('change_password');
$fm_ch_p->SubTitle('[USER] さんのパスワードの変更', '以下より変更してください。', 'pencil-square-o');
$fm_ch_p->Password('ch_p_pass', '新しいパスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true);
$fm_ch_p->Password('ch_p_r_pass', 'パスワードの確認', 'もう一度入力してください。', 'key', true);
$fm_ch_p->Button('bttn_ch_p_submit', 'パスワードを変更', 'button', 'check-square');
$fm_ch_p->Button('bttn_ch_p_back', 'キャンセル', 'button', 'chevron-circle-left', 'gray');

//Change UserName
$fm_ch_un = new form_generator('change_username');
$fm_ch_un->SubTitle('現在のユーザ名: [USER]', '以下よりユーザ名を変更してください。', 'pencil-square-o');
$fm_ch_un->Input('ch_un_username', 'ユーザ名', '半角最大30文字, 全角最大15文字', 'address-book', true);
$fm_ch_un->Button('bttn_ch_un_submit', 'ユーザ名を変更', 'button', 'check-square');
$fm_ch_un->Button('bttn_ch_un_back', 'キャンセル', 'button', 'chevron-circle-left', 'gray');

//Authentication
$fm_at = new form_generator('authentication');
$fm_at->SubTitle("まだ終了できません。", "設定を完了するには、あなたのパスワードが必要です。", "key");
$fm_ch_p->Password('at_pass', '新しいパスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true);
$fm_at->Button('bttn_cc_back', 'アカウント一覧へ戻る', false, 'chevron-circle-left', 'gray');

//Completed
$fm_cc = new form_generator('completed_window');
$fm_cc->SubTitle("設定完了しました！", "下記ボタンでアカウント一覧へ遷移します。", "thumbs-up");
$fm_cc->Button('bttn_cc_back', 'アカウント一覧へ戻る', false, 'chevron-circle-left', 'gray');

$loader = new loader();
?>

<html>
    <head>
        <?php echo $loader->loadHeader("SAL Info", "OPTION - ACCOUNT", true) ?>
        <script type="text/javascript">
            var fm_ac = '<?php echo $fm_ac->Export() ?>';
            var fm_cr = '<?php echo $fm_cr->Export() ?>';
            var fm_ed = '<?php echo $fm_ed->Export() ?>';
        </script>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader(4, 0, $getdata['USERNAME']) ?>
        
        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php
                        echo $loader->Title('ACCOUNT', 'user');
                    ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <?php echo $loader->loadFootS(true) ?>
        
        <script type="text/javascript">
            //ドキュメント読込完了時
            $(document).ready(function() {
                animation('data_output', 400, fm_ac);
            });
            
            //作成ボタン押下
            $(document).on('click', '#bttn_ac_cr', function() {
                animation('data_output', 400, fm_cr);
            });
            
            //編集ボタン押下
            $(document).on('click', '#bttn_ac_ed', function() {
                animation('data_output', 400, fm_ed);
            });
            
            //削除ボタン押下
            $(document).on('click', '#bttn_ac_ed', function() {
                animation('data_output', 400, fm_ed);
            });
            
            //戻るボタン押下
            $(document).on('click', '#bttn_cr_bk, #bttn_ed_bk, #bttn_dl_bk', function() {
                animation('data_output', 400, fm_ac);
            });
        </script>
    </body>
</html>