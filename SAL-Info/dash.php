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

//$former = new form_generator();
$loader = new loader();
$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE USERINDEX = $index");
$check = confirm_lessondata($index);
$check_text = '';
switch($check) {
    case 0:
        //研修状況の把握用
        break;
    case 1:
        $check_text = '研修データがありません。';
        break;
}
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'DASHBOARD') ?>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader(5, 0, $getdata['USERNAME']) ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('DASHBOARD', 'window-restore') ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
                <div class="py-1 bg-title">
                    <?php echo $loader->SubTitle('研修状況', '現在の状況をお知らせします。<hr>' . $check_text, 'book') ?>
                </div>
                <div class="row py-2">
                    <div class="col-md-4">
                        <?php echo $loader->button('lesson', '研修', false, 'book', 'orange') ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $loader->button('setting', '設定', false, 'wrench') ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $loader->button('logout', 'ログアウト', false, 'sign-out') ?>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <?php echo $loader->loadFootS() ?>
        <script type="text/javascript">
            $(function() {
                $('#lesson').click(function() {
                    window.location.href = "./lesson.php";
                });
                $('#setting').click(function() {
                    window.location.href = "./option";
                });
                $('#logout').click(function() {
                    window.location.href = "./logout.php";
                });
            });
        </script>
    </body>
</html>