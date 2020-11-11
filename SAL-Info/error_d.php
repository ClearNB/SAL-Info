<!DOCTYPE html>
<?php
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
//$former = new form_generator();
$loader = new loader();
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="application-name" content="SAL Info">
        <link rel="icon" href="./images/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DASHBOARD - SAL Info</title>
        <meta name="description" content="SAL Info - Select, Answer, Learn!">
        <!-- CSS -->
        <link rel="stylesheet" href="./style/Roboto.css" type="text/css">
        <link rel="stylesheet" href="./style/awesome.min.css" type="text/css">
        <link rel="stylesheet" href="./style/aquamarine.css" type="text/css">
        <!-- Javascript -->
        <script src="./js/animate-in.js"></script>
        <script src="./js/loader.js"></script>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader() ?>

        <div class="py-1" style="background-color: #0078a3;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="bg-dark rounded shadow text-center py-2 my-0"><i class="fa fa-exclamation-circle fa-fw"></i>403</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-primary py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
                <?php echo $loader->SubTitle("表示エラー", "データベースからのデータの取得に失敗しました。<br>管理者に報告してください。", "times-circle") ?>
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <script src="./js/jquery.js"></script>
        <script src="./jquery/jquery-ui.js"></script>
        <script src="./js/popper.min.js"></script>
        <script src="./js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(function() {
               $('#back').click(function() {
                   window.location.href = "./index.php";
               });
            });
        </script>
    </body>
</html>