<!DOCTYPE html>
<?php
include ('./scripts/session_chk.php');
if(session_chk()) {
    http_response_code(301);
    header("Location: dash.php");
    exit();
}

include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
$loader = new loader();
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'INDEX') ?>
    </head>

    <body class="text-monospace">
        <?php echo $loader->userHeader() ?>

        <div class="bg-primary py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
                <div class="row">
                    <div class="text-center col-md-12 mx-auto px-1 col-lg-8">
                        <h3 class="my-3">Select. Answer. Learn.</h3>
                        <p class="mb-3 text-center">苦手な知識を、個別に管理！
                            <br>セキュリティの涵養のため、学習環境から考えよう。
                            <br>「選ぶ、答える、学ぶ。」
                            <br>私たちは、この概念を重点に置いたツールを開発しています。
                        </p>
                        <a href="login.php" class="btn btn-block btn-lg shadow-lg btn-dark">
                            <i class="fa fa-fw fa-play-circle fa-lg"></i>PUSH TO LEARN
                        </a>
                        <p class="mt-2 lead"><small>Currently version 1.0.0-beta</small></p>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <script src="./js/jquery.js"></script>
        <script src="./jquery/jquery-ui.js"></script>
        <script src="./js/popper.min.js"></script>
        <script src="./js/bootstrap.min.js"></script>
    </body>
</html>