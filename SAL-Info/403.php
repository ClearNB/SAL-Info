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
    case 1: break;
    case 2: if(user_table_check()) { http_response_code(301); header('Location: ./init_d.php'); exit(); } break;
}

$loader = new loader();
$logger = new form_generator('logger');
if (fails_check()) {
    $logger->SubTitle("ログイン不可", "あなたは一定時間以内に指定ログイン試行回数以上の試行を行いました。<br>しばらくしてからもう一度試行してください。", "times-circle");
} else {
    $logger->SubTitle("アクセスエラー", "あなたはこのページを閲覧・操作する権限がありません。", "times-circle");
}
$logger->Button('bttn_exit', 'ホームに戻る', 'button', 'home', 'orange');
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', '403 (Forbidden)') ?>
        <script type="text/javascript">
            var logger = '<?php echo $logger->Export() ?>';
        </script>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader() ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('403 (Forbidden)', 'times-circle') ?>
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
            $(document).ready(function () {
                animation('data_output', 0, logger);
            });
            
            $(document).on('click', '#bttn_exit', function () {
                animation_to_sites('data_output', 400, './');
            });
        </script>
    </body>
</html>