<!DOCTYPE html>
<?php
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';

$loader = new loader();

$select = select(true, 'MKTK_USERS', 'COUNT(*) AS COUNT');
if(!$select['COUNT']) {
    header("Location: init_d.php");
}

/* Content Data */
$logger = new form_generator('logger');
$logger->SubTitle("表示エラー", "要求されたページは存在しません。", "times-circle");
$logger->Button('bttn_exit', 'ホームに戻る', 'button', 'home', 'orange');
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', '404 (Not Found)') ?>
        <script type="text/javascript">
            var logger = '<?php echo $logger->Export() ?>';
        </script>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader() ?>

        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('404 (Not Found)', 'times-circle') ?>
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