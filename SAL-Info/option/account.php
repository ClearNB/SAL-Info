<?php
/* SAL Info - Coded
 * [2020 Minokutonika Project Team All Rights Reserved.]
 * PHP Information
 * - Date: 2020/11/8 Updated.
 * - Page Name: Option -Account
 * - Desc: {host}/option/account.php
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
include_once '../scripts/table_generator.php';

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");

if ($getdata['PERMISSION'] != 1) {
    http_response_code(403);
    header("Location: ../403.php");
    exit();
}

//1: アカウント表
$fm_ac = new form_generator('fm_ac');
$data = account_table();
if (!$data) {
    $data = "TABLE_DATA";
}
$fm_ac->Caption($data . '【操作方法】<ul class="title-view">'
	. '<li><i class="fa fa-user-plus"></i>作成 ... 以下の【作成】ボタンを押してください。</li>'
	. '<li><i class="fa fa-pencil-square-o"></i>編集 ... 一覧表隣のチェックボックスに1件だけ選択し、【編集】ボタンを押してください。</li>'
	. '<li><i class="fa fa-trash"></i>削除 ... 一覧表隣のチェックボックスに自分以外の該当部分を選択し、【削除】ボタンを押してください。'
	. '<li><i class="fa fa-chevron-circle-left"></i>戻る ... 設定一覧へ戻ります。</li>'
	. '</ul>');
$fm_ac->Button('bt_ac_cr', '作成', 'button', 'user-plus');
$fm_ac->Button('bt_ac_ed', '編集', 'button', 'pencil-square-o', 'title', 'disabled');
$fm_ac->Button('bt_ac_dl', '削除', 'button', 'trash', 'title', 'disabled');
$fm_ac->Button('bt_ac_bk', '設定一覧へ戻る', 'button', 'chevron-circle-left', 'gray');

//Create User
$fm_cr = new form_generator('fm_cr');
$fm_cr->SubTitle('ACCOUNT CREATE', 'アカウントを作成します。', 'user-plus', 'gray');
$fm_cr->Input('cr_userid', 'ユーザID', '最大20文字, 重複なし, 半角英数字', 'id-card-o', true, false);
$fm_cr->Input('cr_username', 'ユーザ名', '半角最大30文字, 全角最大15文字', 'address-book', true, false);
$fm_cr->SubTitle('パスワード', 'このアカウントのパスワードを入力してください。', 'key');
$fm_cr->Password('cr_pass', 'パスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true, false);
$fm_cr->Password('cr_r_pass', 'パスワードの確認', 'もう一度入力してください。', 'key', true, false);
$fm_cr->SubTitle('権限の設定', '権限を設定してください。', 'group');
$fm_cr->Check(1, 'cr_ow_sel', 'permission', '1', '管理者', false);
$fm_cr->Check(1, 'cr_stu_sel', 'permission', '2', '受講者', true);
$fm_cr->Button('bt_cr_sb', 'アカウントを登録', 'submit', 'check-square', 'orange');
$fm_cr->Button('bt_cr_bk', '戻る', 'button', 'chevron-circle-left', 'gray');

//Edit User
$fm_ed = new form_generator('fm_ed');
$fm_ed->SubTitle('[USER] さん', 'アカウントを編集します。以下から選択してください。', 'pencil-square-o');
$fm_ed->Button('bt_ed_i', 'ユーザID', 'button', 'address-card');
$fm_ed->Button('bt_ed_n', 'ユーザ名', 'button', 'user-circle');
$fm_ed->Button('bt_ed_p', 'パスワード', 'button', 'key');
$fm_ed->Button('bt_ed_bk', 'アカウント一覧へ戻る', 'button', 'chevron-circle-left', 'gray');

//Delete user
$fm_dl = new form_generator('fm_dl');
$fm_dl->SubTitle('削除', '[USER_LIST]', 'pencil-square-o');
$fm_dl->openRow();
$fm_dl->Button('bt_dl_sb', '削除する', 'button', 'address-card');
$fm_dl->Button('bt_dl_bk', 'アカウント一覧へ戻る', 'button', 'chevron-circle-left', 'gray');
$fm_dl->closeDiv();

//Completed
$fm_cc = new form_generator('fm_cc');
$fm_cc->SubTitle("設定完了しました！", "下記ボタンでアカウント一覧へ遷移します。", "thumbs-up");
$fm_cc->Button('bt_cc_bk', 'アカウント一覧へ戻る', false, 'chevron-circle-left', 'gray');

//Authentication
$fm_at = new form_generator('fm_at');
$fm_at->SubTitle("まだ終了できません。", "設定を完了するには、あなたのパスワードが必要です。", "key");
$fm_at->Password('at_pass', '[USER]さんのパスワードを入力', '認証のため入力してください。', 'key', true);
$fm_at->openRow();
$fm_at->Button('bt_cc_sb', '認証', 'submit', 'play', 'orange');
$fm_at->Button('bt_cc_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');
$fm_at->closeDiv();

//Change UserID
$fm_ch_i = new form_generator('fm_ch_i');
$fm_ch_i->SubTitle('対象のユーザ: [USER]', '以下よりユーザIDを変更してください。', 'pencil-square-o');
$fm_ch_i->Input('ch_i_n_ui', 'あなたの新しいユーザID', '最大20文字, 重複なし, 半角英数字', 'user', true);
$fm_ch_i->openRow();
$fm_ch_i->Buttonx2('bt_ch_i_sb', 'ユーザ名を変更', 'submit', 'check-circle', 'orange');
$fm_ch_i->Buttonx2('bt_ch_i_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');
$fm_ch_i->closeDiv();

//Change UserName
$fm_ch_n = new form_generator('fm_ch_n');
$fm_ch_n->SubTitle('対象のユーザ: [USER]', '以下よりユーザ名を変更してください。', 'pencil-square-o');
$fm_ch_n->Input('ch_n_n_un', 'あなたの新しいユーザ名', '半角最大30文字, 全角最大15文字', 'address-card', true);
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
$fm_wt->SubTitle("セッション中です。", "そのままお待ちください...", "spinner");

//Failed : confirm
$fm_fl_cf = new form_generator('fm_fl_cf');
$fm_fl_cf->SubTitle("設定に失敗しました。", "設定項目を確認してください。", "exclamation-triangle");
$fm_fl_cf->Caption("<p id=\"confirm\"></p>");
$fm_fl_cf->Button('bt_fl_cf_bk', '入力画面に戻る', 'button', 'caret-square-o-left', 'gray');

//Failed : account
$fm_fl_at = new form_generator('fm_fl_at');
$fm_fl_at->SubTitle("設定を完了できません", "認証に失敗しました。", "exclamation-triangle");
$fm_fl_at->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>パスワードを正しく入力してください。</li></ul>");
$fm_fl_at->Button('bt_fl_at_bk', 'アカウント一覧に戻る', 'button', 'caret-square-o-left', 'gray');

//Failed : database
$fm_fl_dt = new form_generator('fm_fl_dt');
$fm_fl_dt->SubTitle("手続きに失敗しました。", "データベースの状態を確認してください。", "exclamation-triangle");
$fm_fl_dt->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>データベースの設定を見直してください。</li><li>この件は管理者に必ず相談してください。</li></ul>");
$fm_fl_dt->Button('bt_fl_dt_bk', 'アカウント一覧に戻る', 'button', 'caret-square-o-left', 'gray');

//Failed : checked
$fm_fl_ck = new form_generator('fm_fl_dt');
$fm_fl_ck->SubTitle("手続きに失敗しました。", "選択を正しく行ってください", "exclamation-triangle");
$fm_fl_ck->Caption("<h3 class=\"py-1 md-0\">【警告】</h3>"
	. "<ul class=\"title-view\">"
	. "<li>【編集】では、選択できる人数は1人のみです</li>"
	. "<li>【削除】では、自分自身の選択はできません</li>"
	. "</ul>");
$fm_fl_ck->Button('bt_fl_dt_bk', 'アカウント一覧に戻る', 'button', 'caret-square-o-left', 'gray');

$loader = new loader();
?>

<html>
    <head>
	<?php echo $loader->loadHeader("SAL Info", "OPTION - ACCOUNT", true) ?>
        <script type="text/javascript">
	    var fm_ac = '<?php echo $fm_ac->Export() ?>';
	    var fm_cr = '<?php echo $fm_cr->Export() ?>';
	    var fm_ed = '<?php echo $fm_ed->Export() ?>';
	    var fm_dl = '<?php echo $fm_dl->Export() ?>';
	    var fm_at = '<?php echo $fm_at->Export() ?>';
	    var fm_cc = '<?php echo $fm_cc->Export() ?>';
	    var fm_wt = '<?php echo $fm_wt->Export() ?>';
	    var fm_ch_i = '<?php echo $fm_ch_i->Export() ?>';
	    var fm_ch_n = '<?php echo $fm_ch_n->Export() ?>';
	    var fm_ch_p = '<?php echo $fm_ch_p->Export() ?>';
	    var fm_cf = '<?php echo $fm_cf->Export() ?>';
	    var fm_fl_cf = '<?php echo $fm_fl_cf->Export() ?>';
	    var fm_fl_dt = '<?php echo $fm_fl_dt->Export() ?>';
	    var data_array = {};
	    var fm_cf_w, fm_fl_cf_w, fm_fl_dt_w, fm_ch_w;
        </script>
    </head>

    <body class="text-monospace">
	<?php echo $loader->userHeader(4, 0, $getdata['USERNAME']) ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
		    <?php echo $loader->Title('ACCOUNT', 'user'); ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3 h-min">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

	<?php echo $loader->Footer() ?>

	<?php echo $loader->loadFootS(true) ?>

        <script type="text/javascript">
	    //ドキュメント読込完了時
	    $(document).ready(function () {
		animation('data_output', 400, fm_ac);
	    });

	    //ACCOUNTS
	    $(document).on('click', '#bt_ac_cr, #bt_ac_ed, #bt_ac_dl', function () {
		switch($(this).attr('id')) {
		    case "bt_ac_cr": animation('data_output', 400, fm_cr); break;
		    case "bt_ac_ed":
			animation('data_output', 400, fm_ed);
			break;
		    case "bt_ac_dl":
			animation('data_output', 400, fm_dl);
			break;
		}
	    });

	    //CREATE
	    $(document).on('submit', '#fm_cr', function (event) {
		event.preventDefault();
		data_array["c_data"] = $('#fm_cr').serialize();
		data_array['function'] = 1;
		ajax_dynamic_post('../scripts/account/check_account.php', "f_num=" + data_array['function'] + data_array['c_data']).then(function (data) {
		    switch (data['res']) {
			case 0:
			    fm_cf_w = fm_cf.replace('<p id="confirm"></p>', data['data']);
			    animation('data_output', 400, fm_at);
			    break;
			case 1:
			    fm_fl_cf_w = fm_fl_cf.replace('<p id="confirm"></p>', data['data']);
			    animation('data_output', 400, fm_fl_cf_w);
			    break;
		    }
		});
	    });
	    
	    //EDIT
	    
	    
	    //DELETE
	    
	    //Authentication
	    

	    //back to Account
	    $(document).on('click', '#bt_cr_bk, #bt_ed_bk, #bt_dl_bk, #bt_cc_bk, #bt_fl_dt_bk, #bt_fl_at_bk', function () {
		data_array = {};
		animation_to_sites('data_output', 400, './account.php');
	    });

	    //COMFIRM
	    $(document).on('click', '#bt_cf_sb', function () {
		switch (data_array['function']) {
		    case 1:
			animation('data_output', 400, fm_wt);
			ajax_dynamic_post('../scripts/account/ch_create.php', data_array['c_data']).then(function (data) {
			    data_array = {};
			    switch (data['res']) {
				case 0: animation('data_output', 400, fm_cc); break;
				case 1: animation('data_output', 400, fm_fl_dt); break;
			    }
			});
			break;
		    case 2:
			break;
		    case 3:
			break;
		    case 4:
			break;
		}
	    });

	    $(document).on('click', '#bt_cf_bk', function () {
		switch (data_array['function']) {
		    case 1: animation('data_output', 400, fm_cr); break;
		    case 2: animation('data_output', 400, fm_ch_i); break;
		    case 3: animation('data_output', 400, fm_ch_n); break;
		    case 4: animation('data_output', 400, fm_ch_p); break;
		}
	    });

	    //FAILED (Input Error)
	    $(document).on('click', '#bt_fl_cf_bk', function () {
		switch (data_array['function']) {
		    case 1: animation('data_output', 400, fm_cr); break;
		    case 2: animation('data_output', 400, fm_ch_i); break;
		    case 3: animation('data_output', 400, fm_ch_n); break;
		    case 4: animation('data_output', 400, fm_ch_p); break;
		}
	    });

	    //back to Setting
	    $(document).on('click', '#bt_ac_bk', function () {
		animation_to_sites('data_output', 400, './');
	    });
        </script>
    </body>
</html>