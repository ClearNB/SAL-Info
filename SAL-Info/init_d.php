<!DOCTYPE html>
<?php

include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
include_once './scripts/session_chk.php';

if(user_table_check()) {
    http_response_code(301); header('Location: ./'); exit();
}
ob_get_clean();

$loader = new loader();

//Loading
$fm_ld = new form_generator('form_loading');
$fm_ld->SubTitle('処理中です...', 'ブラウザを閉じないでしばらくお待ちください...', 'refresh fa-spin');

//Failed
$fm_fl = new form_generator('form_failed');
$fm_fl->SubTitle("手続きに失敗しました。", "データベースの状態を確認してください。", "exclamation-triangle");
$fm_fl->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>データベースの設定を見直してください。</li><li>この件は管理者に必ず相談してください。</li></ul>");
$fm_fl->openRow();
$fm_fl->Button('bt_rs_fl', '再試行', 'button', 'play-circle', 'orange');
$fm_fl->closeDiv();

$userid = "user01";
$pass = "UserPass01";

$fm_sl = new form_generator('fm_sl');
$fm_sl->openCenter();
$fm_sl->SubTitle('ようこそ、SAL Infoへ', 'まずはセットアップをしましょう', 'book');
$fm_sl->Caption('まずは、以下からログインしてください。<br>この情報は忘れないようご注意ください。');
$fm_sl->openList();
$fm_sl->addList('ユーザID: ' . $userid);
$fm_sl->addList('パスワード: ' . $pass);
$fm_sl->closeList();
$fm_sl->closeDiv();
$fm_sl->ButtonLg('bt_sl_st', '今すぐ始める！', 'button', 'play', 'orange');
?>

<html>
    <head>
	<?php echo $loader->loadHeader('SAL Info', 'INDEX') ?>
	<script type="text/javascript">
	    var fm_ld = '<?php echo $fm_ld->Export() ?>';
	    var fm_fl = '<?php echo $fm_fl->Export() ?>';
	    var fm_sl = '<?php echo $fm_sl->Export() ?>';
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
		animation('data_output', 400, fm_ld);
		ajax_dynamic_get('./scripts/init_get.php').then(function(data) {
                    switch(data['res']) {
                        case 0: animation('data_output', 1200, fm_sl); break;
                        case 1: animation('data_output', 400, fm_fl); break;
                    }
                });
	    });
	    
	    $(document).on('click', '#bt_rs_fl', function() {
                animation('data_output', 400, fm_ld);
                ajax_dynamic_get('./scripts/init_get.php').then(function(data) {
                    switch(data['res']) {
                        case 0: animation('data_output', 1200, fm_sl); break;
                        case 1: animation('data_output', 400, fm_fl); break;
                    }
                });
            });
	    
	    $(document).on('click', '#bt_sl_st', function() {
		animation_to_sites('data_output', 400, './');
	    });
	</script>
    </body>
</html>