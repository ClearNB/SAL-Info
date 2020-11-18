<!DOCTYPE html>
<?php
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
include_once './scripts/session_chk.php';

switch(session_chk()) {
    case 0: http_response_code(301); header('Location: ./dash.php'); exit(); break;
    case 1: if(!user_table_check()) { http_response_code(301); header('Location: ./init_d.php'); exit(); } break;
    case 2: if(!user_table_check()) { http_response_code(301); header('Location: ./init_d.php'); exit(); } break;
}

$loader = new loader();

$fm = new form_generator('fm');
$fm->openCenter();
$fm->Title('Select. Answer. Learn.', 'book');
$fm->Caption("苦手な知識を、個別に管理！<br>セキュリティの涵養のため、学習環境から考えよう。<br>「選ぶ、答える、学ぶ。」<br>私たちは、この概念を重点に置いたツールを開発しています。");
$fm->ButtonLg('learn', 'PUSH TO LEARN', 'button', 'play-circle', 'orange');
$fm->closeDiv();
?>

<html>
    <head>
	<?php echo $loader->loadHeader('SAL Info', 'INDEX') ?>
	<script type="text/javascript">
	    var fm = '<?php echo $fm->Export() ?>';
	</script>
    </head>

    <body class="text-monospace">
	<?php echo $loader->userHeader() ?>

	<div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('INDEX', 'lightbulb-o') ?>
                </div>
            </div>
        </div>
	
        <div class="bg-primary py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

	<?php echo $loader->Footer() ?>

	<?php echo $loader->loadFootS() ?>
	
	<script type="text/javascript">
	    $(document).ready(function() {
		animation('data_output', 0, fm);
	    });
	    
	    $(document).on('click', '#learn', function() {
		animation_to_sites('data_output', 400, './login.php');
	    });
	</script>
    </body>
</html>