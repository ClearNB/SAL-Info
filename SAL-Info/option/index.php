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
if (!session_chk()) {
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

$permission_text = '受講者';
$per_icon = 'book';
if ($getdata['PERMISSION'] == 1) {
    $permission_text = '管理者';
    $per_icon = 'check-square';
}

$fm = new form_generator('fm');
$fm->SubTitle($permission_text . 'モード', '以下から設定を選択してください。', $per_icon);
$fm->button('back', '戻る', 'button', 'chevron-circle-left', 'gray');
$fm->SubTitle('あなた自身の設定', 'ユーザID・ユーザ名・パスワードを変更できます。', 'user');
$fm->button('change', 'ユーザ情報の変更', 'button', 'address-book');
if ($getdata['PERMISSION'] == 1) {
    $fm->SubTitle('アカウント管理', '作成・編集・変更ができます。', 'group');
    $fm->button('account', 'アカウント一覧', 'button', 'user');
    $fm->SubTitle('研修・問題管理', '研修・問題のデータ管理を行います。', 'book');
    $fm->button('initialize', 'データベース内容の初期化', 'button', 'plus');
}
?>

<html>
    <head>
	<?php echo $loader->loadHeader('SAL Info', 'Option', true) ?>
	<script type="text/javascript">
	    var fm = '<?php echo $fm->Export() ?>';
	</script>
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
            <div class="container " id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

	<?php echo $loader->Footer() ?>

	<?php echo $loader->loadFootS(true); ?>
        <script type="text/javascript">
	    $(function () {
		$(document).ready(function() {
		    animation('data_output', 0, fm);
		});
		$(document).on('click', '#back', function () {
		    animation_to_sites('data_output', 400, '../');
		});
		$(document).on('click', '#account', function () {
		    animation_to_sites('data_output', 400, './account.php');
		});
		$(document).on('click', '#change', function () {
		    animation_to_sites('data_output', 400, './ch_account.php');
		});
		$(document).on('click', '#initialize', function () {
		    animation_to_sites('data_output', 400, './initialize.php');
		});
	    });
        </script>
    </body>
</html>