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

$fm_st = new form_generator('fm_st');
$fm_st->subTitle('分析ウィザード', 'ここでは、各受講者の進捗を確認することができます。', 'bar-chart');
$fm_st->Button('bt_st_bk', '戻る', 'button', 'chevron-circle-left', 'gray');
$fm_st->ButtonLg('bt_st_tb', 'ユーザ進捗管理表', 'button', 'table', 'orange');
$fm_st->ButtonLg('bt_st_li', '各ユーザ過去実績', 'button', 'sticky-note', 'title');

$fm_wt = new form_generator('fm_wt', '');
$fm_wt->SubTitle('しばらくお待ちください...', 'データベースにアクセス中です...', 'spinner fa-spin');
$fm_wt->Caption('セッション中です...');

$fm_fl = new form_generator('fm_fl');
$fm_fl->SubTitle("手続きに失敗しました。", "データベースの状態を確認してください。", "exclamation-triangle");
$fm_fl->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>データベースの設定を見直してください。</li><li>この件は管理者に必ず相談してください。</li></ul>");
$fm_fl->Button('bttn_back_failed01', '戻る', 'button', 'caret-square-o-left');

$fm_tb = new form_generator('fm_tb');
$fm_tb->SubTitle('ユーザ進捗管理表', 'ここで、リアルタイムで受講者の進捗を確認できます。', 'table');
$fm_tb->Button('bt_tb_bk', '戻る', 'button', 'chevron-circle-left', 'gray');
$fm_tb->Button('bt_tb_rs', '最新の状態に更新', 'button', 'refresh');
$fm_tb->Caption('<p id="data"></p>');
$fm_tb->SubTitle('困っている人を支援しましょう！', '遅れている人はいるはずです！軽く付き添ってあげましょう。', 'hand-lizard-o');

$fm_li = new form_generator('fm_li');
$fm_li->SubTitle('各ユーザ過去実績', 'ユーザを選択し、過去の研修履歴を表示しましょう。', 'table');
$fm_li->Button('bt_li_bk', '戻る', 'button', 'chevron-circle-left', 'gray');
$fm_li->Caption('<p id="data"></p>');
$fm_li->ButtonLg('bt_li_st', '開く', 'submit', 'chevron-circle-left', 'orange');

$fm_us = new form_generator('fm_us');
$fm_us->SubTitle('[USER]', 'こちらがユーザの過去履歴です。', 'sticky-note');
$fm_us->Button('bt_us_bk', '戻る', 'button', 'chevron-circle-left', 'gray');
$fm_us->Caption('<p id="data"></p>');
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'WIZARD') ?>
	<script type="text/javascript">
	    var fm_st = '<?php echo $fm_st->Export() ?>';
	    var fm_tb = '<?php echo $fm_tb->Export() ?>';
	    var fm_li = '<?php echo $fm_li->Export() ?>';
	    
	    var fm_tb_w, fm_li_w, fm_us_w;
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
            $(function() {
		$(document).ready(function() {
		    animation('data_output', 400, fm_st);
		});
		
		function refresh_table() {
		    
		}
		
		//fm_st
		$(document).on('click', '#bt_st_bk, #bt_st_tb, #bt_st_li', function() {
		    switch($(this).attr('id')) {
			case 'bt_st_bk':
			    animation_to_sites('data_output', 400, './');
			    break;
			case 'bt_st_tb':
			    animation('data_output', 400, fm_tb);
			    break;
			case 'bt_st_li':
			    animation('data_output', 400, fm_li);
			    break;
		    }
                });
		
		//fm_tb
		$(document).on('click', '#bt_tb_bk, #bt_tb_rs', function() {
		    switch($(this).attr('id')) {
			case 'bt_tb_bk':
			    animation('data_output', 400, fm_st);
			    break;
			case 'bt_tb_rs':
			    break;
		    }
                });
		
		//fm_li
		$(document).on('click, change', '#bt_li_bk, #sl_dt', function() {
		    switch($(this).attr('id')) {
			case 'bt_tb_bk':
			    animation('data_output', 400, fm_st);
			    break;
			case 'sl_dt':
			    break;
		    }
                });
		
		//fm_us
		$(document).on('click', '#bt_us_bk', function() {
		    switch($(this).attr('id')) {
			case 'bt_tb_bk':
			    animation('data_output', 400, fm_li);
			    break;
			case 'sl_dt':
			    break;
		    }
                });
            });
        </script>
    </body>
</html>