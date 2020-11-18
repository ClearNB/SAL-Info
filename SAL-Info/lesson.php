<!DOCTYPE html>
<?php
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/former.php';
include_once './scripts/loader.php';
include_once './scripts/confirmls.php';
include_once './scripts/session_chk.php';

switch (session_chk()) {
    case 0: break;
    case 1: http_response_code(403);
	header('Location: ./403.php');
	exit();
	break;
    case 2: http_response_code(301);
	header('Location: ./logout.php');
	exit();
	break;
}

$loader = new loader();

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE USERINDEX = $index");

$check = confirm_lessondata($index);

switch ($check) {
    case 1: case 2: http_response_code(301);
	header('Location: ./select.php');
	exit();
	break;
    case 3: http_response_code(301);
	header('Location: ./test.php');
	exit();
	break;
}

//1: Loading
$fm_ld = new form_generator('fm_ld');
$fm_ld->SubTitle('読み込み中', 'しばらくお待ちください...', 'spinner fa-spin');

//2: Failed
$fm_fl = new form_generator('fm_fd');
$fm_fl->SubTitle('読み込みに失敗しました', 'データベースへの接続に失敗しました。', 'times-circle');

//3: Start Page
$fm_st = new form_generator('fm_st');
$fm_st->SubTitle($getdata['USERNAME'] . 'さん', '研修を行います', 'book');
$fm_st->openList();
$fm_st->addList('研修はスライド形式で行います');
$fm_st->addList('研修のスライドの読んだ数だけ、研修の進捗率が上がります');
$fm_st->addList('すべてを読み終えると、次は確認テストに移ります');
$fm_st->closeList();
$fm_st->Button('bt_st_sb', '研修を開始', 'button', 'play', 'orange');
$fm_st->Button('bt_st_bk', 'ホームに戻る', 'button', 'chevron-circle-left');
if ($check) {
    $fm_st->caption('研修内容を完遂しました。確認テストを実施できます。');
    $fm_st->Button('bt_st_ts', '確認テストへ', 'button', 'file-text', 'orange');
}

//4-1: Lesson Page
$fm_ls = new form_generator('fm_ls');
$fm_ls->SubTitle('title', 'スライドを動かして研修しましょう！', 'book');
$fm_ls->Caption('data');
$fm_ls->SubTitle('<span id="page_cross">[Page] / [MaxPage]</span>', 'すべてのページを読むと研修はクリアです', 'book');
$fm_ls->openRow();
$fm_ls->Buttonx2('bt_ls_li', '研修を選択', 'button', 'chevron-circle-up');
$fm_ls->Buttonx2('bt_ls_ed', '研修を終了', 'button', 'times-circle', 'gray');
$fm_ls->closeDiv();
if ($check) {
    $fm_ls->Button('bt_ls_ts', '確認テストへ', 'button', 'file-text', 'orange');
}

//4-2: Lesson Select Page
$fm_sl = new form_generator('fm_sl');
$fm_sl->SubTitle('研修を選択', '以下から研修内容を選択してください。', 'address-book');
$fm_sl->caption('sl_data');
$fm_sl->Button('bt_sl_sb', '研修を行う', 'submit', 'book', 'orange');
$fm_sl->Button('bt_sl_bk', '研修へ戻る', 'button', 'chevron-circle-left', 'gray');

//5: Exit Confirm Page
$fm_cf = new form_generator('fm_cf');
$fm_cf->SubTitle('研修を終了します。', '本当によろしいですか？', 'sign-out');
$fm_cf->Button('bt_cf_yes', 'はい', 'button', 'home');
$fm_cf->Button('bt_cf_no', 'いいえ', 'button', 'book', 'gray');
?>

<html>
    <head>
	<?php echo $loader->loadHeader('SAL Info', 'LESSON') ?>
        <script type="text/javascript">
	    var fm_ld = '<?php echo $fm_ld->Export() ?>';
	    var fm_fl = '<?php echo $fm_fl->Export() ?>';
	    var fm_st = '<?php echo $fm_st->Export() ?>';
	    var fm_ls = '<?php echo $fm_ls->Export() ?>';
	    var fm_sl = '<?php echo $fm_sl->Export() ?>';
	    var fm_cf = '<?php echo $fm_cf->Export() ?>';
	    var fm_w, fm_sl_w, pages = [];
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
	    $(document).ready(function () {
		animation_update_slides('data_output', 400, fm_ld);
		ajax_dynamic_post('./scripts/lesson/lesson.php').then(function (data) {
		    switch (data['code']) {
			case 0:
			    fm_w = fm_ls.replace('data', data['slide']).replace('title', data['title']);
			    fm_sl_w = fm_sl.replace('sl_data', data['select']);
			    animation('data_output', 400, fm_st);
			    break;
			case 1:
			    animation('data_output', 400, fm_fl);
			    break;
		    }
		});
	    });

	    $(document).on('init', '.slider', function (event, slick) {
		pages['c_page'] = slick.currentSlide + 1;
		pages['max_page'] = slick.slideCount;
		pages['cnt_page'] = 1;
		pages['cnt_flag'] = false;
		$('#page_cross').text(pages['c_page'] + ' / ' + pages['max_page'] + '【読込済み: ' + pages['cnt_page'] + 'ページ】');
	    });

	    $(document).on('afterChange', '.slider', function (event, slick) {
		pages['c_page'] = slick.currentSlide + 1;
		if (pages['cnt_page'] < slick.slideCount && pages['cnt_page'] < pages['c_page']) {
		    pages['cnt_page'] += 1;
		}
		if (pages['c_page'] === slick.slideCount && !pages['cnt_flag']) {
		    ajax_dynamic_post('./scripts/lesson/lesson.php', 'f_num=1').then(function (data) {
			switch (data['code']) {
			    case 0:
				animation('page_cross', 400, data['text']);
				pages['cnt_flag'] = true;
				break;
			}
		    });
		} else {
		    $('#page_cross').text(pages['c_page'] + ' / ' + pages['max_page'] + '【読込済み: ' + pages['cnt_page'] + 'ページ】');
		}
	    });

	    //fm_st
	    $(document).on('click', '#bt_st_sb, #bt_st_bk, #bt_st_ts', function () {
		switch ($(this).attr('id')) {
		    case 'bt_st_sb':
			animation_update_slides('data_output', 400, fm_w);
			break;
		    case 'bt_st_bk':
			animation_to_sites('data_output', 400, './');
			break;
		    case 'bt_st_ts':
			animation_to_sites('data_output', 400, './test.php');
			break;
		}
	    });

	    //fm_ls
	    $(document).on('click', '#bt_ls_li, #bt_ls_ed', function () {
		switch ($(this).attr('id')) {
		    case 'bt_ls_li':
			animation('data_output', 400, fm_sl_w);
			break;
		    case 'bt_ls_ed':
			animation('data_output', 400, fm_cf);
			break;
		    case 'bt_ls_ts':
			animation_to_sites('data_output', 400, './test.php');
			break;
		}
	    });

	    //fm_sl
	    $(document).on('submit', '#fm_sl', function () {
		event.preventDefault();
		var d = $(this).serialize();
		animation('data_output', 400, fm_ld);
		ajax_dynamic_post('./scripts/lesson/lesson.php', d + "&f_num=2").then(function (data) {
		    switch (data['code']) {
			case 0:
			    fm_w = fm_ls.replace('data', data['slide']).replace('title', data['title']);
			    fm_sl_w = fm_sl.replace('sl_data', data['select']);
			    animation_update_slides('data_output', 400, fm_w);
			    break;
			case 1:
			    animation('data_output', 400, fm_fl);
			    break;
		    }
		});
	    });

	    $(document).on('click', '#bt_sl_bk', function () {
		animation_update_slides('data_output', 400, fm_w);
	    });
	    
	    $(document).on('click', '#bt_cf_yes, #bt_cf_no', function() {
		switch($(this).attr('id')) {
		    case 'bt_cf_yes':
			animation_to_sites('data_output', 400, './');
			break;
		    case 'bt_cf_no':
			animation_update_slides('data_output', 400, fm_w);
			break;
		}
	    });
        </script>
    </body>

</html>