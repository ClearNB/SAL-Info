<?php
include_once '../scripts/common.php';
include_once '../scripts/sqldata.php';
include_once '../scripts/dbconfig.php';
include_once '../scripts/former.php';
include_once '../scripts/loader.php';
include_once '../scripts/session_chk.php';

switch (session_chk()) {
    case 0: break;
    case 1: http_response_code(403);
	header('Location: ../403.php');
	exit();
	break;
    case 2: http_response_code(301);
	header('Location: ../logout.php');
	exit();
	break;
}

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");

$loader = new loader();

//Menu
$fm_ed = new form_generator('fm_ed');
$fm_ed->SubTitle('[USER] さん', 'アカウントを編集します。以下から選択してください。', 'pencil-square-o');
$fm_ed->Button('bt_ed_i', 'ユーザID', 'button', 'address-card');
$fm_ed->Button('bt_ed_n', 'ユーザ名', 'button', 'user-circle');
$fm_ed->Button('bt_ed_p', 'パスワード', 'button', 'key');
$fm_ed->Button('bt_ed_bk', 'アカウント一覧へ戻る', 'button', 'chevron-circle-left', 'gray');

//Completed
$fm_cc = new form_generator('fm_cc');
$fm_cc->SubTitle("設定完了しました！", "下記ボタンでアカウント一覧へ遷移します。", "thumbs-up");
$fm_cc->Button('bt_cc_bk', 'アカウント一覧へ戻る', false, 'chevron-circle-left', 'gray');

//Authentication
$fm_at = new form_generator('fm_at');
$fm_at->SubTitle("まだ終了できません。", "設定を完了するには、あなたのパスワードが必要です。", "key");
$fm_at->Password('at_pass', '[USER]さんのパスワードを入力', '認証のため入力してください。', 'key', true);
$fm_at->openRow();
$fm_at->Button('bt_at_sb', '認証', 'submit', 'play', 'orange');
$fm_at->Button('bt_at_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');
$fm_at->closeDiv();

//Change UserID
$fm_ch_i = new form_generator('fm_ch_i');
$fm_ch_i->SubTitle('対象のユーザ: [USER]', '以下よりユーザIDを変更してください。', 'pencil-square-o');
$fm_ch_i->Input('ch_i_n_ui', '新しいユーザID', '最大20文字, 重複なし, 半角英数字', 'user', true);
$fm_ch_i->openRow();
$fm_ch_i->Buttonx2('bt_ch_i_sb', 'ユーザ名を変更', 'submit', 'check-circle', 'orange');
$fm_ch_i->Buttonx2('bt_ch_i_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');
$fm_ch_i->closeDiv();

//Change UserName
$fm_ch_n = new form_generator('fm_ch_n');
$fm_ch_n->SubTitle('対象のユーザ: [USER]', '以下よりユーザ名を変更してください。', 'pencil-square-o');
$fm_ch_n->Input('ch_n_n_un', '新しいユーザ名', '半角最大30文字, 全角最大15文字', 'address-card', true);
$fm_ch_n->openRow();
$fm_ch_n->Buttonx2('bt_ch_n_sb', 'ユーザ名を変更', 'submit', 'check-circle', 'orange');
$fm_ch_n->Buttonx2('bt_ch_n_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');
$fm_ch_n->closeDiv();

//Change Password
$fm_ch_p = new form_generator('fm_ch_p');
$fm_ch_p->SubTitle('対象のユーザ: [USER]', '以下よりパスワードを変更してください。', 'pencil-square-o');
$fm_ch_p->Password('ch_p_n_ps', '新しいパスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true);
$fm_ch_p->Password('ch_p_nr_ps', 'パスワードの確認', 'もう一度入力してください。', 'key', true);
$fm_ch_p->openRow();
$fm_ch_p->Buttonx2('bt_ch_p_sb', 'パスワードを変更', 'submit', 'check-circle', 'orange');
$fm_ch_p->Buttonx2('bt_ch_p_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');
$fm_ch_p->closeDiv();

//Confirm
$fm_cf = new form_generator('fm_cf');
$fm_cf->SubTitle('確認', '以下の入力項目を確認してください。', 'pencil-square-o');
$fm_cf->Caption("<p id=\"confirm\"></p>");
$fm_cf->Button('bt_cf_sb', '実行する', 'button', 'play');
$fm_cf->Button('bt_cf_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');

//Loading
$fm_wt = new form_generator('fm_we');
$fm_wt->SubTitle("セッション中です。", "そのままお待ちください...", "spinner fa-spin");

//Failed : confirm
$fm_fl_cf = new form_generator('fm_fl_cf');
$fm_fl_cf->SubTitle("設定に失敗しました。", "設定項目を確認してください。", "exclamation-triangle");
$fm_fl_cf->Caption("<p id=\"confirm\"></p>");
$fm_fl_cf->Button('bt_fl_cf_bk', '入力画面に戻る', 'button', 'caret-square-o-left', 'gray');

//Failed : account
$fm_fl_at = new form_generator('fm_fl_at');
$fm_fl_at->SubTitle("設定を完了できません", "認証に失敗しました。", "exclamation-triangle");
$fm_fl_at->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>パスワードを正しく入力してください。</li></ul>");
$fm_fl_at->Button('bt_fl_at_bk', 'アカウント設定一覧に戻る', 'button', 'caret-square-o-left', 'gray');

//Failed : database
$fm_fl_dt = new form_generator('fm_fl_dt');
$fm_fl_dt->SubTitle("手続きに失敗しました。", "データベースの状態を確認してください。", "exclamation-triangle");
$fm_fl_dt->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>データベースの設定を見直してください。</li><li>この件は管理者に必ず相談してください。</li></ul>");
$fm_fl_dt->Button('bt_fl_dt_bk', 'アカウント設定一覧に戻る', 'button', 'caret-square-o-left', 'gray');
?>

<html>
    <head>
	<?php echo $loader->loadHeader('SAL Info', 'Account Option', true) ?>
	<script type="text/javascript">
	    var fm_ed = '<?php echo $fm_ed->Export() ?>';
	    var fm_at = '<?php echo $fm_at->Export() ?>';
	    var fm_cc = '<?php echo $fm_cc->Export() ?>';
	    var fm_wt = '<?php echo $fm_wt->Export() ?>';
	    var fm_ch_i = '<?php echo $fm_ch_i->Export() ?>';
	    var fm_ch_n = '<?php echo $fm_ch_n->Export() ?>';
	    var fm_ch_p = '<?php echo $fm_ch_p->Export() ?>';
	    var fm_cf = '<?php echo $fm_cf->Export() ?>';
	    var fm_fl_cf = '<?php echo $fm_fl_cf->Export() ?>';
	    var fm_fl_dt = '<?php echo $fm_fl_dt->Export() ?>';
	    var fm_fl_at = '<?php echo $fm_fl_at->Export() ?>';

	    var d = '<?php echo "index-s=" . $index ?>';
	    var data_array = {};
	    var fm_w, fm_w2;
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
	    $(document).ready(function () {
		animation('data_output', 0, fm_wt);
		ajax_dynamic_post('../scripts/account/getuser.php', d).then(function (data) {
		    switch (data['code']) {
			case 0:
			    data_array['a_name'] = data['a_name'];
			    data_array['u_d'] = data['data'];
			    fm_w = fm_ed.replace('[USER]', data['data']['USERNAME']);
			    animation('data_output', 400, fm_w);
			    break;
			case 1:
			    animation('data_output', 400, fm_fl_dt);
			    break;
			case 2:
			    animation('data_output', 400, fm_fl_ck);
			    break;
		    }
		});
	    });

	    //EDIT - Button
	    $(document).on('click', '#bt_ed_i, #bt_ed_n, #bt_ed_p', function () {
		switch ($(this).attr('id')) {
		    case "bt_ed_i":
			fm_w = fm_ch_i;
			break;
		    case "bt_ed_n":
			fm_w = fm_ch_n;
			break;
		    case "bt_ed_p":
			fm_w = fm_ch_p;
			break;
		}
		fm_w = fm_w.replace('[USER]', data_array['u_d']['USERNAME']);
		animation('data_output', 400, fm_w);
	    });

	    //Change - Submit
	    $(document).on('submit', '#fm_ch_i, #fm_ch_n, #fm_ch_p', function () {
		event.preventDefault();
		switch ($(this).attr('id')) {
		    case 'fm_ch_i':
			data_array['function'] = 3;
			break;
		    case 'fm_ch_n':
			data_array['function'] = 4;
			break;
		    case 'fm_ch_p':
			data_array['function'] = 5;
			break;
		}
		data_array["c_data"] = $(this).serialize();
		animation('data_output', 400, fm_wt);
		ajax_dynamic_post('../scripts/account/check_account.php', "f_num=" + data_array['function'] + '&' + data_array['c_data']).then(function (data) {
		    switch (data['res']) {
			case 0:
			    fm_w2 = fm_cf.replace('<p id="confirm"></p>', data['data']);
			    fm_w = fm_at.replace('[USER]', data['auth_name']);
			    animation('data_output', 400, fm_w);
			    break;
			case 1:
			    fm_w = fm_fl_cf.replace('<p id="confirm"></p>', data['data']);
			    animation('data_output', 400, fm_w);
			    break;
		    }
		});
	    });

	    //Authentication
	    $(document).on('submit', '#fm_at', function () {
		event.preventDefault();
		var d = $(this).serialize();
		animation('data_output', 400, fm_wt);
		ajax_dynamic_post('../scripts/account/check_auth.php', d).then(function (data) {
		    switch (data['res']) {
			case 0:
			    animation('data_output', 400, fm_w2);
			    break;
			case 1:
			    animation('data_output', 400, fm_fl_at);
			    break;
			case 2:
			    animation('data_output', 400, fm_fl_dt);
			    break;
		    }
		});
	    });

	    //back to Setting
	    $(document).on('click', '#bt_ed_bk, #bt_cc_bk, #bt_fl_dt_bk, #bt_fl_at_bk', function () {
		data_array = {};
		animation_to_sites('data_output', 400, './');
	    });

	    //COMFIRM
	    $(document).on('click', '#bt_cf_sb', function () {
		switch (data_array['function']) {
		    case 3:
		    case 4:
		    case 5:
			animation('data_output', 400, fm_wt);
			ajax_dynamic_post('../scripts/account/ch_change.php', "ch_in=" + data_array['u_d']['USERINDEX'] + "&" + "f_num=" + data_array['function'] + "&" + data_array['c_data']).then(function (data) {
			    data_array = {};
			    switch (data['res']) {
				case 0:
				    animation('data_output', 400, fm_cc);
				    break;
				case 1:
				    animation('data_output', 400, fm_fl_dt);
				    break;
			    }
			});
			break;
		}
	    });

	    //FAILED (Input Error)
	    $(document).on('click', '#bt_fl_cf_bk', function () {
		switch (data_array['function']) {
		    case 3:
			fm_w = fm_ch_i;
			break;
		    case 4:
			fm_w = fm_ch_n;
			break;
		    case 5:
			fm_w = fm_ch_p;
			break;
		}
		fm_w = fm_w.replace('[USER]', data_array['u_d']['USERNAME']);
		animation('data_output', 400, fm_w);
	    });

	    //BACK TO ACCOUNT SETTING
	    $(document).on('click', '#bt_ch_n_bk, #bt_ch_i_bk, #bt_ch_p_bk, #bt_at_bk, #bt_cf_bk', function () {
		fm_w = fm_ed.replace('[USER]', data_array['u_d']['USERNAME']);
		animation('data_output', 400, fm_w);
	    });
        </script>
    </body>
</html>