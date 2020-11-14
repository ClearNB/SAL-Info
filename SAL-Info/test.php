<!DOCTYPE html>
<?php
include ('./scripts/session_chk.php');
if (!session_chk()) {
    http_response_code(403);
    header("Location: 403.php");
    exit();
}

//Definition
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
include_once './scripts/table_generator.php';
$loader = new loader();

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");

include_once './scripts/confirmls.php';
$check = confirm_lessondata($index);
switch($check) {
    case 0: http_response_code(301); header('Location: ./lesson.php'); exit(); break;
    case 1: case 2: http_response_code(301); header('Location: ./select.php'); exit(); break;
    case 5: http_response_code(301); header('Location: ./err_d.php'); exit(); break;
}

//Test pre-Process
//Page1: Loading
$fm_ld = new form_generator('form_loader');
$fm_ld->SubTitle("処理中...", "データの処理の完了までしばらくお待ちください...", "spinner");

//Page2: Failed to Load
$fm_fl = new form_generator('form_failed');
$fm_fl->SubTitle("処理に失敗", "データの処理中にエラーが発生しました。", "times-circle");
$fm_fl->Button("home_back", "ホームに戻る", "button", "home");

/* [データ処理方法]
 * 1. SetID取得処理を行います
 * 2. 取得できなかった場合は0をフラグとして出します
 * 3. MKTK_TESTから、TestIDをSetIDから取得します
 * 4. 取得できなかった場合は1をフラグとして出します
 * 5. 事前テストのフラグ（TestTypeID=0）のみがあれば2をフラグとして出します
 * 6. TestTypeID=0, 1 の両方がある場合は3をフラグとして出します
 */
//Page3: Test Start Page
$fm_st = new form_generator('form_start');

switch ($check) {
    case 3: //事前テスト
        $fm_st->SubTitle("事前テスト", "このページは、事前テストを受けるためのページです", "file-text");
        $fm_st->Caption("<h5>注意事項</h5><ul class=\"title-view\">"
                . "<li>必ずあなた自身で解いてください。</li>"
                . "<li>制限時間はありませんが、ブラウザバックやブラウザを閉じるなどの行為をすると、最初から再開となります。</li>"
                . "<li>問題の出題範囲は、SELECTで選んだ研修内容から出します。</li>"
                . "<li>各研修テーマごとに5問ずつ出題されます。</li>"
                . "</ul>");
        $fm_st->SubTitle('受講者名: ' . $getdata['USERNAME'], '上記のユーザの事前テストを実施します。', 'user');
        $fm_st->Button("test_start", "テストを実施する", "button", "file-text", "orange");
        $fm_st->Button("home_back", "ホームに戻る", "button", "home");
        break;
    case 4: //確認テスト
        $fm_st->SubTitle("確認テスト", "このページは、確認テストを受けるためのページです", "file-text");
        $fm_st->Caption("<h5>注意事項</h5><ul class=\"title-view\">"
                . "<li>必ずあなた自身で解いてください。</li>"
                . "<li>制限時間はありませんが、ブラウザバックやブラウザを閉じるなどの行為をすると、最初から再開となります。</li>"
                . "<li>問題の出題範囲は、SELECTで選んだ研修内容から出します。</li>"
                . "<li>各研修テーマごとに5問ずつ出題されます。</li>"
                . "</ul>");
        $fm_st->SubTitle('受講者名: ' . $getdata['USERNAME'], '上記のユーザの確認テストを実施します。', 'user');
        $fm_st->Button("test_start", "テストを実施する", "button", "file-text", "orange");
        $fm_st->Button("home_back", "ホームに戻る", "button", "home");
        break;
    default: //それ以外（テストなし）
        $fm_st->SubTitle("現在、あなたに課せられているテストはありません", "ホームに戻り、「研修」から始めてください。", "times-circle");
        $fm_st->Caption("これを出力されている場合、あなたはすべての研修内容を完遂していると推定されています。おめでとうございます。"
                . "<br>研修を再開したい場合は、ホームに戻り「研修」から始めて、「研修を再開」で再開してください。");
        $fm_st->Button("home_back", "ホームに戻る", "button", "home");
        break;
}

//Page4: Test Page
$fm_tt = '';

//Page5: Result Page
$fm_rt = '';

switch ($check) {
    case 3: //事前テスト
    case 4: //確認テスト
        $fm_tt = new form_generator('form_test');
        $fm_rt = new form_generator('form_result');
        break;
    default: //それ以外（以降の処理はなし）
        break;
}
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'TEST') ?>
        <script type="text/javascript">
            var fm_ld = '<?php echo $fm_ld->Export() ?>';
            var fm_fl = '<?php echo $fm_fl->Export() ?>';
            var fm_st = '<?php echo $fm_st->Export() ?>';
	    var fm_ts = '<?php echo $fm_ts->Export() ?>';
            var fm_tt = '<?php if($fm_tt !== '') { echo $fm_tt->Export(); } ?>';
            var fm_rt = '<?php if($fm_rt !== '') { echo $fm_rt->Export(); } ?>';
        </script>
    </head>

    <body class="text-monospace">
        <!-- Contents -->
        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Logo_Title('TEST', 'search-plus') ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3">
            <div class="container h-min" id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>
        
        <?php echo $loader->loadFootS(); ?>
        <script type="text/javascript">
            $(document).ready(function () {
                animation('data_output', 400, fdata1);
            });

            $(document).on('click', '#home_back', function () {
                document.location.href = 'index.php';
            });
        </script>
    </body>

</html>