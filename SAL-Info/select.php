<!DOCTYPE html>
<?php
include_once __DIR__ . '/scripts/common.php';
include_once __DIR__ . '/scripts/sqldata.php';
include_once __DIR__ . '/scripts/dbconfig.php';
include_once __DIR__ . '/scripts/former.php';
include_once __DIR__ . '/scripts/loader.php';

include ('./scripts/session_chk.php');
if (!session_chk()) {
    http_response_code(403);
    header('Location: ./403.php');
    exit();
}
$loader = new loader();

/* This is the form Definition top. */

//1
$former_begin = new form_generator('form_begin');
$former_begin->SubTitle('研修選択（SELECT）',
        'ここでは、各社員の今の状態によって、研修内容を決定するものです。工程は以下のとおりです。'
        . '<ol>'
        . '<li>職種の選択</li>'
        . '<li>勤務年の選択</li>'
        . '<li>事前テストの実施</li>'
        . '</ol>', 'bookmark');
$former_begin->Caption('【注意】<br>設定が完了するまではページから離れないようお願い致します。基本的にUIによる操作に従ってください。');
$former_begin->Button('bttn_begin', '選択を開始', 'button', 'play');
$former_begin->Button('bttn_exit', 'ホームに戻る', 'button', 'home', 'gray');

//2
$former = new form_generator('form_workType');
$former->SubTitle('職種を選択', '職種を選択してください（複数選択可能, 最低1コ）', 'building-o');
$occr = select(false, 'MKTK_OCCR', 'OCCRID, OCCRNAME');
$occr_array = [];
if ($occr) {
    $i = 1;
    $dummy = $occr->fetch_assoc();
    while ($var = $occr->fetch_assoc()) {
        $former->Check(0, 'sel-wk-' . $i, 'sel-wk', $var['OCCRID'], $var['OCCRNAME'], false);
        array_push($occr_array, $var['OCCRNAME']);
        $i += 1;
    }
} else {
    http_response_code(301);
    header('Location: ./403.php');
    exit();
}

//3
$former->Caption('この選択によって、受ける研修内容が変わります。<br>どういった研修を行うかについては、選択後に確認できます。');
$former->Button('bttn_ok_form2', '完了', 'button', 'check-circle');
$former->Button('bttn_back_form2', '戻る', 'button', 'chevron-circle-left', 'gray');

//4
$former2 = new form_generator('form_workYR');
$former2->SubTitle('勤務年を選択', 'あなたの会社への所属年（もしくは職務の経験年）を教えてください。', 'clock-o');
$former2->Check(1, 'sel-yr-1', 'sel-yr', 1, '勤務から1年未満', false);
$former2->Check(1, 'sel-yr-2', 'sel-yr', 2, '勤務から1年以上', false);
$former2->Caption('勤務年の指定により、事前テストにおいて問題の難易度が変動します。勤務年が長い場合、近年追加された要素に関する問題を出題します。<br>講義や確認テストにおいては難易度に変異はありません。');
$former2->Button('bttn_ok_form3', '完了', 'button', 'check-circle');
$former2->Button('bttn_back_form3', '戻る', 'button', 'chevron-circle-left', 'gray');

//5
$former_confirm = new form_generator('form_confirm', '');
$former_confirm->SubTitle('入力の確認', '入力事項をご確認ください。', 'pencil-square');
$former_confirm->Caption('<p id="confirm"></p>');
$former_confirm->Button('bttn_ok_confirm', '研修内容を確認する', 'button', 'check-circle');
$former_confirm->Button('bttn_back_confirm', '研修選択へ戻る', 'button', 'chevron-circle-left', 'gray');

//6
$former_pro = new form_generator('form_pro', '');
$former_pro->SubTitle('研修内容の確認', 'こちらが研修内容となります。<br>()内は研修スライドのある研修の数となっています。', 'check-square');
$former_pro->Caption('<p id="confirm"></p>');
$former_pro->Button('bttn_pro_confirm', 'これで研修する', 'button', 'check-circle');
$former_pro->Button('bttn_pro_back', '研修選択へ戻る', 'button', 'chevron-circle-left', 'gray');

//7
$former_wait = new form_generator('form_wait', '');
$former_wait->SubTitle('しばらくお待ちください...', 'データベースにアクセス中です...', 'spinner fa-spin');
$former_wait->Caption('セッション中です...');

//8
$form_failed = new form_generator('failed_form_02');
$form_failed->SubTitle("手続きに失敗しました。", "データベースの状態を確認してください。", "exclamation-triangle");
$form_failed->Caption("<h3 class=\"py-1 md-0\">【警告】</h3><ul class=\"title-view\"><li>データベースの設定を見直してください。</li><li>この件は管理者に必ず相談してください。</li></ul>");
$form_failed->Button('bttn_back_failed', '最初に戻る', 'button', 'caret-square-o-left');

//9
$form_completed = new form_generator('failed_form_02');
$form_completed->SubTitle("お疲れ様でした", "これにて選択の工程は終了です。", "exclamation-triangle");
$form_completed->Caption("<h3 class=\"py-1 md-0\">【事前テストを実施】</h3><ul class=\"title-view\">"
        . "<li>事前テストとは、研修の前に行うテストです。</li>"
        . "<li>事前テストを受けるには、下記のボタンを押すか、ホームから「研修」を押してください。</li></ul>");
$form_completed->Button('bttn_begin', '事前テストへ進む', 'button', 'textfile');
$form_completed->Button('bttn_exit', 'ホームに戻る', 'button', 'home', 'gray');

/* This is the form Definition bottom. */

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'SELECT') ?>
        <script type="text/javascript">
            var formdata = {};
            var fdata1 = '<?php echo $former_begin->Export() ?>';
            var fdata2 = '<?php echo $former->Export() ?>';
            var fdata3 = '<?php echo $former2->Export() ?>';
            var fdata4 = '<?php echo $former_confirm->Export() ?>';
            var fdata5 = '<?php echo $former_wait->Export() ?>';
            var fdata6 = '<?php echo $former_pro->Export() ?>';
            var fdata7 = '<?php echo $form_failed->Export() ?>';
            let arr_wk = <?php echo json_encode($occr_array) ?>;
            let arr_yr = ['', '勤務から1年未満', '勤務から1年以上'];
        </script>
    </head>

    <body class="text-monospace">
        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Logo_Title('SELECT', 'search-plus') ?>
                </div>
            </div>
        </div>

        <div class="bg-primary h-min py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <?php echo $loader->loadFootS() ?>
        <script type="text/javascript">
            function data_convert() {
                var text = '<ul id="data-view" class="title-view"><li>職種: WORKWKVALUE</li><li>勤務年: WORKYRVALUE</li></ul>';
                var checked = '';
                for (var i = 0; i < formdata['checkedlist_row'].length; i++) {
                    checked = checked + arr_wk[formdata['checkedlist_row'][i]['value'] - 2];
                    if (i < formdata['checkedlist_row'].length - 1) {
                        checked = checked + ', ';
                    }
                }
                text = text.replace('WORKWKVALUE', checked);
                text = text.replace('WORKYRVALUE', arr_yr[formdata['workyrvalue_row'][0]['value']]);
                fdata4 = fdata4.replace('<p id="confirm"></p>', '<p id="confirm">' + text + '</p>');
            }

            $(document).ready(function () {
                animation('data_output', 0, fdata1);
            });

            //Begin Page : 1
            $(document).on('click', '#bttn_begin', function () {
                animation('data_output', 400, fdata2);
            });
            $(document).on('click', '#bttn_exit', function () {
                window.location.href = 'index.php';
            });

            //Form Page : 2
            $(document).on('click', '#bttn_ok_form2', function () {
                if ($('#error_content').length > 0) {
                    $('#error_content').remove();
                }
                var check = $('#form_workType [name=sel-wk]:checked');
                if (check.length === 0) {
                    $('#data_output').append('<div id="error_content" class="failedMessage text-monospace">エラー: どれか1つを選択してください。</div>');
                } else {
                    var data = $('#form_workType').serializeArray();
                    var d = 'wk_tp=1_';
                    for (var i = 0; i < data.length; i++) {
                        var st = data[i]['value'];
                        d = d + st;
                        if (i < data.length - 1) {
                            d = d + '_';
                        }
                    }
                    console.log(d);
                    if (formdata['checkedlist'] !== data) {
                        formdata['checkedlist'] = d;
                        formdata['checkedlist_row'] = data;
                    }
                    fdata2 = document.getElementById('form_workType');
                    animation('data_output', 400, fdata3);
                }
            });
            $(document).on('click', '#bttn_back_form2', function () {
                if ($('#error_content').length > 0) {
                    $('#error_content').remove();
                }
                fdata2 = document.getElementById('form_workType');
                animation('data_output', 400, fdata1);
            });

            //Form Page : 3
            $(document).on('click', '#bttn_ok_form3', function () {
                if ($('#error_content').length > 0) {
                    $('#error_content').remove();
                }
                var check = $('#form_workYR [name=sel-yr]:checked');
                if (check.length === 0) {
                    $('#data_output').append('<div id="error_content" class="failedMessage text-monospace">エラー: どれか1つを選択してください。</div>');
                } else {
                    var data = $('#form_workYR').serializeArray();
                    if (formdata['workyrvalue'] !== data) {
                        formdata['workyrvalue'] = 'wk_ry=' + data;
                        formdata['workyrvalue_row'] = data;
                    }
                    data_convert();
                    fdata3 = document.getElementById('form_workYR');
                    animation('data_output', 400, fdata4);
                }
            });
            $(document).on('click', '#bttn_back_form3', function () {
                if ($('#error_content').length > 0) {
                    $('#error_content').remove();
                }
                fdata3 = document.getElementById('form_workYR');
                animation('data_output', 400, fdata2);
            });

            //Confirm Page : 4
            $(document).on('click', '#bttn_ok_confirm', function () {
                var d = formdata['checkedlist'];
                $('#data_output').hide(400, function () {
                    $("#data-view").remove();
                    fdata4 = document.getElementById('data_output').innerHTML;
                    $('#data_output').html(fdata5).show('slow');

                    $.ajax({
                        type: 'POST',
                        url: './scripts/searchls.php',
                        data: d,
                        crossDomain: false,
                        dataType: 'json'
                    }).done(function (res) {

                        animation('data_output', 400, fdata6);
                    }).fail(function () {
                        animation('data_output', 400, fdata7);
                    });
                });
            });
            $(document).on('click', '#bttn_back_confirm', function () {
                $('#data_output').hide(400, function () {
                    $("#data-view").remove();
                    fdata4 = document.getElementById('data_output').innerHTML;
                    $('#data_output').html(fdata2);
                    $('#data_output').show('slow');
                });
            });

            //Confirm Page : 6
            $(document).on('click', '#bttn_pro_confirm', function () {

            });

            $(document).on('click', '#bttn_pro_back', function () {
                data_convert();
                animation('data_output', 400, fdata4);
            });

            //Confirm Page : 7
            $(document).on('click', '#bttn_back_failed', function () {
                data_convert();
                animation('data_output', 400, fdata4);
            });
        </script>
    </body>
</html>