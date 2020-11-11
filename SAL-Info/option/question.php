<?php
/* SAL Info - Coded
 * [2020 Minokutonika Project Team All Rights Reserved.]
 * PHP Information
 * - Date: 2020/11/8 Updated.
 * - Page Name: Option -Account
 * - Desc: {host}/option/account.php
 * - Launguage: Japanese
 */

include ('./scripts/session_chk.php');
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
$former_create = new form_generator('form-account');
$former_create->Input('userid', 'ユーザID', '最大20文字, 重複なし, 半角英数字', 'id-card-o', true);
$former_create->Input('username', 'ユーザ名', '半角最大30文字, 全角最大15文字', 'address-book', true);
$former_create->SubTitle('パスワード', 'このアカウントのパスワードを入力してください。', 'key');
$former_create->Password('pass', 'パスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true);
$former_create->Password('r-pass', 'パスワードの確認', 'もう一度入力してください。', 'key', true);
$former_create->SubTitle('権限の設定', '権限を設定してください。', 'group');
$former_create->Check(1, 'ow-sel', 'permission', '1', '管理者', false);
$former_create->Check(1, 'stu-sel', 'permission', '2', '受講者', true);
$former_create->Button('form_button', 'アカウントを登録');

//Change Password
$former = new form_generator('form-account');
$former->Password('pass', '現在のパスワード', 'ここには現在のパスワードを入力します。', 'key', true);
$former->Password('pass', '現在のパスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true);
$former->Password('r-pass', 'パスワードの確認', 'もう一度入力してください。', 'key', true);
$former->Button('form_button', 'パスワードを変更');

//Completed
$former2 = new form_generator('completed_window');
$former2->SubTitle("設定完了しました！", "下記ボタンで設定一覧へ遷移します。", "thumbs-up");
$former2->Button('back', '設定一覧へ戻る', false);

$loader = new loader();
?>

<html>
    <head>
        <?php echo $loader->loadHeader("SAL Info", "OPTION - ACCOUNT", true) ?>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader(4, 0, $getdata['USERNAME']) ?>
        
        <div class="py-1 bg-title"">
            <div class="container">
                <div class="row">
                    <?php
                        echo $loader->Title('CHANGE YOUR PASSWORD', 'key');
                        echo $loader->Button('back', '設定一覧へ戻る', false);
                    ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3">
            <div class="container text-center" id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <?php echo $loader->loadFootS(true) ?>
        
        <script type="text/javascript">
            
        </script>
    </body>
</html>