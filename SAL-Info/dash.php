<!DOCTYPE html>
<?php
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
include_once './scripts/confirmls.php';
include_once './scripts/session_chk.php';
include_once './scripts/outputStatus.php';

switch (session_chk()) {
    case 0: break;
    case 1: http_response_code(403);
	header('Location: ./403.php');
	exit();
	break;
    case 2: http_response_code(301);
	header('Location: ./logout.php');
	exit();
	break;
}

$loader = new loader();
$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE USERINDEX = $index");

$check_text = getStatus();

$fm = new form_generator('fm');

switch ($getdata['PERMISSION']) {
    case 1:
	$fm->backTitle();
	$fm->SubTitle('あなたは管理者です。', '色々な情報を解析しましょう。', 'server');
	$fm->closeDiv();
	$fm->openRow();
	$fm->ButtonLgx3('wizard', '分析', 'button', 'book', 'orange');
	$fm->ButtonLgx3('option', '設定', 'button', 'wrench');
	$fm->ButtonLgx3('logout', 'ログアウト', 'button', 'sign-out');
	break;
    case 2:
	$fm->backTitle();
	$fm->SubTitle('あなたは受講者です。', '研修しましょう！', 'book');
	$fm->closeDiv();
	$fm->openRow();
	$fm->ButtonLgx3('lesson', '研修', 'button', 'book', 'orange');
	$fm->ButtonLgx3('option', '設定', 'button', 'wrench');
	$fm->ButtonLgx3('logout', 'ログアウト', 'button', 'sign-out');
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
	    $(function () {
		$(document).ready(function () {
		    animation('data_output', 0, fm);
		});

		$(document).on('click', '#lesson', function () {
		    animation_to_sites('data_output', 400, './lesson.php');
		});
		$(document).on('click', '#wizard', function () {
		    animation_to_sites('data_output', 400, './wizard.php');
		});
		$(document).on('click', '#option', function () {
		    animation_to_sites('data_output', 400, './option');
		});
		$(document).on('click', '#logout', function () {
		    animation_to_sites('data_output', 400, './logout.php');
		});
		$(document).on('click', '#user', function () {
		    animation_to_sites('data_output', 400, './user.php');
		});
	    });
        </script>
    </body>
</html>