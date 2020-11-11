<!DOCTYPE html>
<?php
include_once __DIR__ . '/scripts/common.php';
include_once __DIR__ . '/scripts/sqldata.php';
include_once __DIR__ . '/scripts/dbconfig.php';
include_once __DIR__ . '/scripts/former.php';
include_once __DIR__ . '/scripts/loader.php';

include ('./scripts/session_chk.php');
if(!session_chk()) {
    http_response_code(403);
    header('Location: ' . __DIR__ . '/403.php');
    exit();
}

$loader = new loader();

//$former = new form_generator();
$index = $_SESSION['userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'FORMAT'); ?>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader() ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('FORMAT', 'window-restore') ?>
                </div>
            </div>
        </div>
        
        <div class="bg-primary py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <script src="./js/jquery.js"></script>
        <script src="./jquery/jquery-ui.js"></script>
        <script src="./js/popper.min.js"></script>
        <script src="./js/bootstrap.min.js"></script>
    </body>
</html>