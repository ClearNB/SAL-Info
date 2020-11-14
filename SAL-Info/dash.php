<!DOCTYPE html>
<?php
include ('./scripts/session_chk.php');
if(!session_chk()) {
    http_response_code(403);
    header("Location: 403.php");
    exit();
}

include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
include_once './scripts/confirmls.php';

$loader = new loader();
$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE USERINDEX = $index");

$check = confirm_lessondata($index);
$check_text = '';
switch($check) {
    case 0:
        //研修状況の把握用
        break;
    case 1:
        $check_text = '研修データがありません。';
        break;
}

$fm = new form_generator('fm');
$fm->backTitle();
$fm->SubTitle('研修状況', '現在の状況をお知らせします。<hr>' . $check_text, 'book');
$fm->closeDiv();
$fm->openRow();
switch($getdata['PERMISSION']) {
    case 1:
	$fm->ButtonLgx3('wizard', '分析', 'button', 'book', 'orange');
	$fm->ButtonLgx3('option', '設定', 'button', 'wrench');
	$fm->ButtonLgx3('logout', 'ログアウト', 'button', 'sign-out');
	break;
    case 2:
	$fm->ButtonLgx2('user', 'ユーザ情報', 'button', 'book', 'orange');
	$fm->ButtonLgx2('lesson', '研修', 'button', 'book', 'orange');
	$fm->ButtonLgx2('option', '設定', 'button', 'wrench');
	$fm->ButtonLgx2('logout', 'ログアウト', 'button', 'sign-out');
	break;
}
$fm->closeDiv();
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'DASHBOARD') ?>
	<script type="text/javascript">
	    var fm = '<?php echo $fm->Export() ?>';
	</script>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader(5, 0, $getdata['USERNAME']) ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('DASHBOARD', 'window-restore') ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3">
            <div class="container " id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <?php echo $loader->loadFootS() ?>
        <script type="text/javascript">
            $(function() {
		$(document).ready(function() {
		    animation('data_output', 400, fm);
		});
		
                $(document).on('click', '#lesson', function() {
		    animation_to_sites('data_output', 400, './lesson.php');
                });
		$(document).on('click', '#wizard', function() {
                    animation_to_sites('data_output', 400, './wizard.php');
                });
                $(document).on('click', '#option', function() {
                    animation_to_sites('data_output', 400, './option');
                });
                $(document).on('click', '#logout', function() {
                    animation_to_sites('data_output', 400, './logout.php');
                });
            });
        </script>
    </body>
</html>