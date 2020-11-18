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
$form = new form_generator('login_form');

$form->Input('userid', 'ユーザID', 'ユーザIDは、管理者によって指定されています。', 'user-circle-o', true);
$form->Password('password', 'パスワード', '指定のパスワードを入力します。', 'key', true);
$form->Button('form_submit', 'ログイン', 'submit', 'sign-in');

$form_wait = new form_generator('form_wait');
$form_wait->SubTitle("セッション中です。", "そのままお待ちください...", "spinner fa-spin");

$form_failed_01 = new form_generator('form_failed01');
$form_failed_01->SubTitle("ログインに失敗しました。", "ユーザIDまたはパスワードが違います", "exclamation-triangle");
$form_failed_01->Caption("<h3 class=\"py-2\">【警告】</h3><hr><ul class=\"title-view\"><li>各項目の入力事項をご確認ください。</li><li>ユーザID・パスワードを忘れたら、管理者に相談してください。</li></ul>");
$form_failed_01->Button('form_back_form_01', '入力に戻る', 'button', 'caret-square-o-left');

$form_failed_02 = new form_generator('form_failed02');
$form_failed_02->SubTitle("ログインに失敗しました。", "データベースの状態を確認してください。", "exclamation-triangle");
$form_failed_02->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>データベースの設定を見直してください。</li><li>この件は管理者に必ず相談してください。</li></ul>");
$form_failed_02->Button('form_back_form_01', '入力に戻る', 'button', 'caret-square-o-left');
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'LOGIN') ?>
        <script type="text/javascript">
            var fdata1 = '<?php echo $form->Export() ?>';
            var fdata2 = '<?php echo $form_failed_01->Export() ?>';
            var fdata3 = '<?php echo $form_failed_02->Export() ?>';
            var fdataw = '<?php echo $form_wait->Export() ?>';
        </script>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader() ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('LOGIN', 'sign-in') ?>
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
                animation('data_output', 0, fdata1);
            });

            //Page1: Login Form
            $(document).on('submit', '#login_form', function (event) {
                event.preventDefault();
                var form = $('#login_form');
                var d = form.serializeArray();
                fdata1 = document.getElementById('data_output').innerHTML;
                animation('data_output', 400, fdataw);
                ajax_dynamic_post('./scripts/session.php', d).then(function(data) {
                    switch(data['res']) {
                        case 0: animation_to_sites('data_output', 400, './dash.php'); break;
                        case -1: animation('data_output', 400, fdata3);
                        case 1: animation('data_output', 400, fdata2);
                    }
                });
            });
            
            //Page2: Failed Form 01
            $(document).on('click', '#form_back_form_01', function () {
                animation('data_output', 400, fdata1);
            });
            
            //Page3: Failed Form 02
            $(document).on('click', '#form_back_form_02', function () {
                animation('data_output', 400, fdata1);
            });
        </script>
    </body>
</html>