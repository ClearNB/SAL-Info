<!DOCTYPE html>
<?php
include ('./scripts/session_chk.php');
if (!session_chk()) {
    http_response_code(403);
    header('Location: ./403.php');
    exit();
}

include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';


$loader = new loader();

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE USERINDEX = $index");

include_once './scripts/confirmls.php';
switch(confirm_lessondata($index)) {
    case 1: case 2: http_response_code(301); header('Location: ./select.php'); exit(); break;
    case 3: case 4: http_response_code(301); header('Location: ./test.php'); exit(); break;
}

$select = select(false, 'MKTK_LS_DATA', 'CONTENT', 'WHERE LSID = 1');
$text = "<ul class=\"slider\">";
$i = 1;
while ($row = $select->fetch_assoc()) {
    $content = $row["CONTENT"];
    $text .= "<li><a><img src=\"./data/slide/$content\" alt=\"image-$i\"></a></li>";
    $i += 1;
}
$text .= "</ul>";

//1: Loading
$fm_ld = new form_generator('form_load');
$fm_ld->SubTitle('読み込み中', 'しばらくお待ちください...', 'spinner');

//2: Failed
$fm_fd = new form_generator('form_failed');
$fm_fd->SubTitle('読み込みに失敗しました', 'データベースへの接続に失敗しました。', 'times-circle');

//3: Start Page
$fm_st = new form_generator('form_start');
$fm_st->SubTitle('研修 - Lesson -', '【研修を行います】', 'book');


//4-1: Lesson Page
$fm_ls = new form_generator('form_lesson');
$fm_ls->SubTitle('[研修タイトル]', 'スライドを動かして研修しましょう！', 'book');
$fm_ls->Caption($text);
$fm_ls->SubTitle('<span id="page_cross">[Page] / [MaxPage]</span>', 'すべてのページを読むと研修はクリアです', 'book');
$fm_ls->openRow();
$fm_ls->Buttonx3('bttn_ls_prv', '', 'button', 'chevron-circle-up');
$fm_ls->Buttonx3('bttn_ls_nxt', '', 'button', 'chevron-circle-down');
$fm_ls->Buttonx3('bttn_ls_end', '', 'button', 'times-circle', 'gray');
$fm_ls->closeDiv();

//4-2: Lesson Select Page
$text_02 = 'select';
$fm_ls_sl = new form_generator('form_lesson_select');
$fm_ls_sl->SubTitle('研修を選択', '以下から研修内容を選択してください。', 'address-book');
$fm_ls_sl->caption($text_02); //データを入れる
$fm_ls_sl->Button('bttn_ls_sl_back', '研修へ戻る', 'button', 'book', 'orange');

//5: Exit Confirm Page
$fm_cf = new form_generator('form_confirm');
$fm_cf->SubTitle('研修を終了します。', '本当によろしいですか？', 'sign-out');
$fm_cf->Button('bttn_cf_yes', 'はい', 'button', 'home');
$fm_cf->Button('bttn_cf_yes', 'いいえ', 'button', 'book', 'gray');
?>

<html>
    <head>
        <?php echo $loader->loadHeader('SAL Info', 'LESSON') ?>
        <script type="text/javascript">
            var fload = '<?php echo $fm_ld->Export() ?>';
            var ffail = '<?php echo $fm_fd->Export() ?>';
            var fstart = '<?php echo $fm_st->Export() ?>';
            var fless = '<?php echo $fm_ls->Export() ?>';
            var fconf = '<?php echo $fm_ls->Export() ?>';
            var pages = [];
        </script>
    </head>

    <body class="text-monospace">
        <!-- Contents -->
        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Logo_Title('LESSON', 'book') ?>
                </div>
            </div>
        </div>

        <!-- Lesson Contents -->

        <div class="bg-primary py-3">
            <div class="container " id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>

        <?php echo $loader->loadFootS(); ?>
        <script type="text/javascript">
            $(function () {
                $('#stop_ls').click(function () {
                    window.location.href = "./dash.php";
                });
            });

            $(document).ready(function () {
                animation_update_slides('data_output', 400, fless);
            });
            
            $(document).on('init', '.slider', function(event, slick) {
                pages['c_page'] = slick.currentSlide + 1;
                pages['max_page'] = slick.slideCount;
                pages['cnt_page'] = 1;
                $('#page_cross').text(pages['c_page'] + ' / ' + pages['max_page'] + '【読込済み: ' + pages['cnt_page'] + 'ページ】');
            });
            
            $(document).on('afterChange', '.slider', function(event, slick) {
                pages['c_page'] = slick.currentSlide + 1;
                if(pages['cnt_page'] < slick.slideCount && pages['cnt_page'] < pages['c_page']) {
                    pages['cnt_page'] += 1;
                }
                $('#page_cross').text(pages['c_page'] + ' / ' + pages['max_page'] + '【読込済み: ' + pages['cnt_page'] + 'ページ】');
            });
        </script>
    </body>

</html>