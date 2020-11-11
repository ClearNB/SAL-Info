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
$loader = new loader();

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");

//Test pre-Process
//Page1: Loading
$form_loader = new form_generator('form_loader');
$form_loader->SubTitle("処理中...", "データの処理の完了までしばらくお待ちください...", "spinner");

//Page2: Failed to Load
$form_failed = new form_generator('form_failed');
$form_failed->SubTitle("処理に失敗", "データの処理中にエラーが発生しました。", "times-circle");
$form_failed->Button("home_back", "ホームに戻る", "button", "home");

/* [データ処理方法]
 * 1. SetID取得処理を行います
 * 2. 取得できなかった場合は0をフラグとして出します
 * 3. MKTK_TESTから、TestIDをSetIDから取得します
 * 4. 取得できなかった場合は1をフラグとして出します
 * 5. 事前テストのフラグ（TestTypeID=0）のみがあれば2をフラグとして出します
 * 6. TestTypeID=0, 1 の両方がある場合は3をフラグとして出します
 */
//Page3: Test Start Page
$form_start = new form_generator('form_start');

$test_flag = 3; //テストフラグ処理

switch ($test_flag) {
    case 0: //テストデータがない場合
        $form_start->SubTitle("研修データがありません", "ホームに戻り、「研修」から始めてください。", "times-circle");
        $form_start->Button("home_back", "ホームに戻る", "button", "home");
        break;
    case 1: //事前テスト
        $form_start->SubTitle("事前テスト", "このページは、事前テストを受けるためのページです", "file-text");
        $form_start->Caption("<h5>注意事項</h5><ul class=\"title-view\">"
                . "<li>必ずあなた自身で解いてください。</li>"
                . "<li>制限時間はありませんが、ブラウザバックやブラウザを閉じるなどの行為をすると、最初から再開となります。</li>"
                . "<li>問題の出題範囲は、SELECTで選んだ研修内容から出します。</li>"
                . "<li>各研修テーマごとに5問ずつ提出されます。</li>"
                . "</ul>");
        $form_start->SubTitle('受講者名: ' . $getdata['USERNAME'], '上記のユーザの事前テストを実施します。', 'user');
        $form_start->Button("test_start", "テストを実施する", "button", "file-text", "orange");
        $form_start->Button("home_back", "ホームに戻る", "button", "home");
        break;
    case 2: //確認テスト
        $form_start->SubTitle("確認テスト", "このページは、確認テストを受けるためのページです", "file-text");
        $form_start->Caption("<h5>注意事項</h5><ul class=\"title-view\">"
                . "<li>必ずあなた自身で解いてください。</li>"
                . "<li>制限時間はありませんが、ブラウザバックやブラウザを閉じるなどの行為をすると、最初から再開となります。</li>"
                . "<li>問題の出題範囲は、SELECTで選んだ研修内容から出します。</li>"
                . "<li>各研修テーマごとに5問ずつ提出されます。</li>"
                . "</ul>");
        $form_start->SubTitle('受講者名: ' . $getdata['USERNAME'], '上記のユーザの確認テストを実施します。', 'user');
        $form_start->Button("test_start", "テストを実施する", "button", "file-text", "orange");
        $form_start->Button("home_back", "ホームに戻る", "button", "home");
        break;
    default: //それ以外（テストなし）
        $form_start->SubTitle("現在、あなたに課せられているテストはありません", "ホームに戻り、「研修」から始めてください。", "times-circle");
        $form_start->Caption("これを出力されている場合、あなたはすべての研修内容を完遂していると推定されています。おめでとうございます。"
                . "<br>研修を再開したい場合は、ホームに戻り「研修」から始めて、「研修を再開」で再開してください。");
        $form_start->Button("home_back", "ホームに戻る", "button", "home");
        break;
}

//Page4: Test Page
$form_test = '';

//Page5: Result Page
$form_result = '';

switch ($test_flag) {
    case 1: //事前テスト
    case 2: //確認テスト
        $form_test = new form_generator('form_test');
        $form_result = new form_generator('form_result');
        break;
    default: //それ以外（以降の処理はなし）
        break;
}
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'TEST') ?>
        <script type="text/javascript">
            var floader = '<?php echo $form_loader->Export() ?>';
            var ffailed = '<?php echo $form_failed->Export() ?>';
            var fdata1 = '<?php echo $form_start->Export() ?>';
            var fdata2 = '<?php if($form_test !== '') { echo $form_test->Export(); } ?>';
            var fdata3 = '<?php if($form_result !== '') { echo $form_result->Export(); } ?>';
        </script>
    </head>

    <body class="text-monospace">
        <!-- Contents -->
        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Logo_Title($title_row, 'search-plus') ?>
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