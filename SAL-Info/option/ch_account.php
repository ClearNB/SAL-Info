<?php

//Menu
$fm_ch = new form_generator('edit_account');
$fm_ch->SubTitle('[USER] さん', 'アカウントを編集します。以下から選択してください。', 'pencil-square-o');
$fm_ch->Button('bttn_ed_cr', 'ユーザID',            'button', 'address-card');
$fm_ch->Button('bttn_ed_ed', 'ユーザ名',            'button', 'user-circle');
$fm_ch->Button('bttn_ed_dl', 'パスワード',          'button', 'key');
$fm_ch->Button('bttn_ed_bk', 'アカウント一覧へ戻る', 'button', 'chevron-circle-left', 'gray');

//Change UserID
$fm_ch_i = new form_generator('fm_ch_i');
$fm_ch_i->SubTitle('対象のユーザ: [USER]', '以下よりユーザIDを変更してください。', 'pencil-square-o');
$fm_ch_i->Password('ch_i_c_ps', '現在のパスワード', 'ここには現在のパスワードを入力します。', 'key', true);
$fm_ch_i->Input('ch_i_n_ui', 'あなたの新しいユーザID', '最大20文字, 重複なし, 半角英数字', 'key', true);
$fm_ch_i->Button('bt_ch_i_sb', 'ユーザ名を変更', 'button', 'check-circle', 'orange');
$fm_ch_i->Button('bt_ch_i_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');

//Change UserName
$fm_ch_n = new form_generator('fm_ch_n');
$fm_ch_n->SubTitle('対象のユーザ: [USER]', '以下よりユーザ名を変更してください。', 'pencil-square-o');
$fm_ch_n->Password('ch_n_c_ps', '現在のパスワード', 'ここには現在のパスワードを入力します。', 'key', true);
$fm_ch_n->Input('ch_n_n_un', 'あなたの新しいユーザ名', '半角最大30文字, 全角最大15文字', 'key', true);
$fm_ch_n->Button('bt_ch_n_sb', 'ユーザ名を変更', 'button', 'check-circle', 'orange');
$fm_ch_n->Button('bt_ch_n_bk', 'キャンセル', 'button', 'chevron-circle-left', 'gray');

//Change Password
$fm_ch_p = new form_generator('fm_ch_p');
$fm_ch_p->SubTitle('対象のユーザ: [USER]', '以下よりパスワードを変更してください。', 'pencil-square-o');
$fm_ch_p->Password('ch_p_c_ps', '現在のパスワード', 'ここには現在のパスワードを入力します。', 'key', true);
$fm_ch_p->Password('ch_p_n_ps', '新しいパスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true);
$fm_ch_p->Password('ch_p_nr_ps', 'パスワードの確認', 'もう一度入力してください。', 'key', true);
$fm_ch_p->Button('bt_ch_p_sb', 'パスワードを変更', 'check-circle', 'orange');
$fm_ch_p->Button('bt_ch_p_bk', 'キャンセル', 'chevron-circle-left', 'gray');



//Completed
$fm_ch_c = new form_generator('fm_ch_c');
$fm_ch_c->SubTitle("設定完了しました！", "下記ボタンで設定一覧へ遷移します。", "thumbs-up");
$fm_ch_c->Button('back', '設定一覧へ戻る', false);
?>

<html>
    <head>
	<?php echo $loader->loadHeader('SAL Info', 'Account Option', true) ?>
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
		    animation('data_output', 0, $fm);
		});
		
		$('#back').click(function () {
		    window.location.href = "../";
		});
	    });
        </script>
    </body>
</html>