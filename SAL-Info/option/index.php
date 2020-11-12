<!DOCTYPE html>
<?php
/* SAL Info - Coded
 * [2020 Minokutonika Project Team All Rights Reserved.]
 * PHP Information
 * - Date: 2020/11/8 Updated.
 * - Page Name: Option -Index
 * - Desc: {host}/option/index.php
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

$loader = new loader();
$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");

?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'Option', true) ?>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader(4, 0, $getdata['USERNAME']) ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('OPTION', 'window-restore') ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
                <div class="row">
                    <?php
                    $permission_text = '受講者';
                    $per_icon = 'book';
                    if($getdata['PERMISSION'] == 1) {
                        $permission_text = '管理者';
                        $per_icon = 'check-square';
                    }
                    echo $loader->SubTitle($permission_text . 'モード', '以下から設定を選択してください。', $per_icon);
                    echo $loader->button_s('back', '戻る', false, 'external-link', 'gray');
                    echo $loader->SubTitle('あなた自身の設定', 'ユーザID・ユーザ名・パスワードを変更できます。', 'user');
                    echo $loader->button('change_userid', 'ユーザIDの変更', false, 'address-book');
                    echo $loader->button('change_username', 'ユーザ名の変更', false, 'pencil');
                    echo $loader->button('change_password', 'パスワードの変更', false, 'key');
                    echo $loader->SubTitle('アカウント管理', '作成・編集・変更ができます。', 'group');
                    echo $loader->button('account', 'アカウント一覧', false, 'user');
                    echo $loader->SubTitle('研修・問題管理', '研修・問題のデータ管理を行います。', 'book');
                    echo $loader->button('ls_r', 'データベース内容の初期化', false, 'plus');
                    ?>
                </div>
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <?php echo $loader->loadFootS(true); ?>
        <script type="text/javascript">
            $(function () {
                $('#back').click(function() {
                    window.location.href = "../";
                });
                $('#account').click(function () {
                    window.location.href = "./account.php";
                });
                $('#change_password').click(function () {
                    window.location.href = "./edit_password.php";
                });
                $('#change_userid').click(function () {
                    window.location.href = "./edit_userid.php";
                });
                $('#change_username').click(function () {
                    window.location.href = "./edit_username.php";
                });
                $('#ls_r').click(function() {
                    window.location.href = "./question.php";
                });
            });
        </script>
    </body>
</html>