<!DOCTYPE html>
<?php
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
include_once './scripts/confirmls.php';
include_once './scripts/session_chk.php';

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

$fm_ld = new form_generator('fm_ld', '');
$fm_ld->SubTitle('しばらくお待ちください...', 'データベースにアクセス中です...', 'spinner fa-spin');
$fm_ld->Caption('セッション中です...');

$fm_fl = new form_generator('fm_fl');
$fm_fl->SubTitle("手続きに失敗しました。", "データベースの状態を確認してください。", "exclamation-triangle");
$fm_fl->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>データベースの設定を見直してください。</li><li>この件は管理者に必ず相談してください。</li></ul>");
$fm_fl->Button('bt_fl_bk', '戻る', 'button', 'caret-square-o-left');

$fm_tb = new form_generator('fm_tb');
$fm_tb->SubTitle('ユーザ進捗管理表', '取得日時: t_ti', 'table');
$fm_tb->Button('bt_tb_bk', '戻る', 'button', 'chevron-circle-left', 'gray');
$fm_tb->Button('bt_tb_rs', '最新の状態に更新', 'button', 'refresh');
$fm_tb->Caption('ls_td');
$fm_tb->SubTitle('困っている人を支援しましょう！', '遅れている人はいるはずです！軽く付き添ってあげましょう。', 'hand-lizard-o');
?>

<html>
    <head>
	<?php echo $loader->loadHeader('SAL Info', 'WIZARD') ?>
	<script type="text/javascript">
	    var fm_tb = '<?php echo $fm_tb->Export() ?>';
	    var fm_ld = '<?php echo $fm_ld->Export() ?>';
	    var fm_fl = '<?php echo $fm_fl->Export() ?>';
	    var fm_w;
	</script>
    </head>

    <body class="text-monospace">
	<?php echo $loader->userHeader(5, 0, $getdata['USERNAME']) ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
		    <?php echo $loader->Title('WIZARD', 'window-restore') ?>
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
	    $(document).ready(function () {
		animation('data_output', 0, fm_ld);
		ajax_dynamic_get('./scripts/wizard/getWizardTable.php').then(function (data) {
		    fm_w = fm_tb.replace('t_ti', data['time']);
		    fm_w = fm_w.replace('ls_td', data['data']);
		    animation('data_output', 400, fm_w);
		});
	    });

	    //fm_st
	    $(document).on('click', '#bt_st_bk, #bt_st_tb', function () {
		switch ($(this).attr('id')) {
		    case 'bt_st_bk':
			animation_to_sites('data_output', 400, './');
			break;
		    case 'bt_st_tb':
			animation('data_output', 400, fm_tb);
			break;
		}
	    });

	    //fm_tb
	    $(document).on('click', '#bt_tb_bk, #bt_tb_rs', function () {
		switch ($(this).attr('id')) {
		    case 'bt_tb_bk':
			animation_to_sites('data_output', 400, './');
			break;
		    case 'bt_tb_rs':
			animation('data_output', 400, fm_ld);
			ajax_dynamic_get('./scripts/wizard/getWizardTable.php').then(function (data) {
			    fm_w = fm_tb.replace('t_ti', data['time']);
			    fm_w = fm_w.replace('ls_td', data['data']);
			    animation('data_output', 400, fm_w);
			});
			break;
		}
	    });
        </script>
    </body>
</html>