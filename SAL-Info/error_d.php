<!DOCTYPE html>
<?php
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';

$fm_dt = new form_generator('fm_dt');
$fm_dt->SubTitle("表示エラー", "データベースからのデータの取得に失敗しました。<br>管理者に報告してください。", "times-circle");
$fm_dt->Button('fm_bk', 'ホームに戻る', 'button', 'home');

$loader = new loader();
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'ERROR DATABASE') ?>
    </head>

    <body class="text-monospace">
        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Title('DATABASE ERROR', 'lightbulb-o') ?>
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
            $(function() {
               $('#fm_bk').click(function() {
                   window.location.href = "./index.php";
               });
            });
        </script>
    </body>
</html>