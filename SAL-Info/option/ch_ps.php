<?php

//Change Password
$former_change_p = new form_generator('change_password');
$former_change_p->Password('ch_pass', '現在のパスワード', 'ここには現在のパスワードを入力します。', 'key', true);
$former_change_p->Password('cu_pass', '新しいパスワード', '大文字・小文字含める半角英数字10-15文字', 'key', true);
$former_change_p->Password('r_pass', 'パスワードの確認', 'もう一度入力してください。', 'key', true);
$former_change_p->Button('bttn_cp_submit', 'パスワードを変更');
$former_change_p->Button('bttn_cp_back', 'キャンセル');

//Completed
$former2 = new form_generator('completed_window');
$former2->SubTitle("設定完了しました！", "下記ボタンで設定一覧へ遷移します。", "thumbs-up");
$former2->Button('back', '設定一覧へ戻る', false);