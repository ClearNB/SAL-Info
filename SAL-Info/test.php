<!DOCTYPE html>
<?php
include_once './scripts/common.php';
include_once './scripts/sqldata.php';
include_once './scripts/dbconfig.php';
include_once './scripts/confirmls.php';
include_once './scripts/table_generator.php';
include_once './scripts/session_chk.php';
include_once './scripts/loader.php';
include_once './scripts/former.php';

switch(session_chk()) {
    case 0: break;
    case 1: http_response_code(403); header('Location: ./403.php'); exit(); break;
    case 2: http_response_code(301); header('Location: ./logout.php'); exit(); break;
}

//Definition
$loader = new loader();

$index = $_SESSION['mktk_userindex'];
$getdata = select(true, "MKTK_USERS", "USERNAME, PERMISSION", "WHERE  USERINDEX = $index");

$check = confirm_lessondata($index);
switch($check) {
    case 0: http_response_code(301); header('Location: ./lesson.php'); exit(); break;
    case 1: case 2: http_response_code(301); header('Location: ./select.php'); exit(); break;
    case 5: http_response_code(301); header('Location: ./err_d.php'); exit(); break;
}

//Test pre-Process

//Page1: Loading
$fm_ld = new form_generator('fm_ld');
$fm_ld->SubTitle('しばらくお待ちください...', 'データベースにアクセス中です...', 'spinner fa-spin');

//Page2: Failed to Load
$fm_fl = new form_generator('fm_fl');
$fm_fl->SubTitle("処理に失敗", "データの処理中にエラーが発生しました。", "times-circle");
$fm_fl->Button("bt_fl_bk", "ホームに戻る", "button", "home");



/* [データ処理方法]
 * 1. SetID取得処理を行います
 * 2. 取得できなかった場合は0をフラグとして出します
 * 3. MKTK_TESTから、TestIDをSetIDから取得します
 * 4. 取得できなかった場合は1をフラグとして出します
 * 5. 事前テストのフラグ（TestTypeID=0）のみがあれば2をフラグとして出します
 * 6. TestTypeID=0, 1 の両方がある場合は3をフラグとして出します
 */
//Page3: Test Start Page
$fm_st = new form_generator('fm_st');

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
        $fm_st->Button("bk_st_st", "テストを実施する", "button", "file-text", "orange");
        $fm_st->Button("bk_st_bk", "ホームに戻る", "button", "home");
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
        $fm_st->Button("bk_st_st", "テストを実施する", "button", "file-text", "orange");
        $fm_st->Button("bk_st_bk", "ホームに戻る", "button", "home");
        break;
    default: //それ以外（テストなし）
        $fm_st->SubTitle("現在、あなたに課せられているテストはありません", "ホームに戻り、「研修」から始めてください。", "times-circle");
        $fm_st->Caption("これを出力されている場合、あなたはすべての研修内容を完遂していると推定されています。おめでとうございます。"
                . "<br>研修を再開したい場合は、ホームに戻り「研修」から始めて、「研修を再開」で再開してください。");
        $fm_st->Button("bk_st_bk", "ホームに戻る", "button", "home");
        break;
}

//Page4: Test Page
$fm_tt = '';

//Page5: Result Page
$fm_rt = '';

switch ($check) {
    case 3: //事前テスト
	$fm_tt = new form_generator('fm_tt');
	$fm_tt->SubTitle('事前テスト・第CT問', '現在進捗: CT / C_MAX', 'file-text');
	$fm_tt->CaptionLg('QUES');
	$fm_tt->Input('f_ans', '解答を入力', 'ここに入力します', 'pencil', true, false);
	$fm_tt->ButtonLg('bt_tt_sb', '解答', 'submit', 'pencil', 'orange');
        
	$fm_rt = new form_generator('fm_rt');
	$fm_rt->SubTitle($getdata['USERNAME'] . 'さんの事前テスト結果', '実施日時:TIME', 'star');
	$fm_rt->CaptionLg('HTML');
	$fm_rt->SubTitle('合計点: SCORE', 'お疲れ様でした。研修か、ホームを選択してください。', 'star');
	$fm_rt->Button('bt_rt_jm', '研修に移る', 'submit', 'book', 'orange');
	$fm_rt->Button('bt_rt_bk', 'ホームに戻る', 'submit', 'home', 'gray');
	break;
    case 4: //確認テスト
	$fm_tt = new form_generator('fm_tt');
	$fm_tt->SubTitle('確認テスト・第CT問', '現在進捗: CT / C_MAX', 'file-text');
	$fm_tt->CaptionLg('QUES');
	$fm_tt->Input('f_ans', '解答を入力', 'ここに入力します', 'pencil', true, false);
	$fm_tt->ButtonLg('bt_tt_sb', '解答', 'submit', 'pencil', 'orange');
        
	$fm_rt = new form_generator('fm_rt');
	$fm_rt->SubTitle($getdata['USERNAME'] . 'さんの確認テスト結果', '実施日時:TIME', 'star');
	$fm_rt->CaptionLg('HTML');
	$fm_rt->SubTitle('合計点', '実施日時:TIME', 'star');
	$fm_rt->Button('bt_rt_jm', '研修に移る', 'submit', 'book', 'orange');
	$fm_rt->Button('bt_rt_bk', 'ホームに戻る', 'submit', 'home', 'gray');
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
            var fm_tt = '<?php if($fm_tt != '') { echo $fm_tt->Export(); } ?>';
            var fm_rt = '<?php if($fm_rt != '') { echo $fm_rt->Export(); } ?>';
	    var fm_w;
	    var fm_d = [];
        </script>
    </head>

    <body class="text-monospace">
        <!-- Contents -->
        <div class="py-1 bg-title">
            <div class="container">
                <div class="row">
                    <?php echo $loader->Logo_Title('TEST', 'file-text') ?>
                </div>
            </div>
        </div>

        <div class="bg-primary py-3">
            <div class="container" id="data_output">
                <!-- CONTENT OUTPUTS -->
            </div>
        </div>

        <?php echo $loader->Footer() ?>
        
        <?php echo $loader->loadFootS(); ?>
        <script type="text/javascript">
            $(document).ready(function () {
                animation('data_output', 0, fm_ld);
		ajax_dynamic_post('./scripts/test/test.php').then(function(data) {
		    switch(data['code']) {
			case 0:
			    fm_w = fm_tt.replace(/CT/g, data['count']);
			    fm_w = fm_w.replace(/C_MAX/g, data['max_count']);
			    fm_w = fm_w.replace(/QUES/g, data['ques']);
			    animation('data_output', 400, fm_st);
			    break;
			case 1: animation('data_output', 400, fm_fl); break;
		    }
		});
            });

	    //1: Start Page
            $(document).on('click', '#bk_st_st, #bk_st_bk', function () {
		switch($(this).attr('id')) {
		    case 'bk_st_st':
			animation('data_output', 400, fm_w);
			break;
		    case 'bk_st_bk':
			animation_to_sites('data_output', 400, './');
			break;
		}
            });
	    
	    //2: Test Page
	    $(document).on('submit', '#fm_tt', function () {
		event.preventDefault();
		var d = $(this).serialize();
		animation('data_output', 400, fm_ld);
		ajax_dynamic_post('./scripts/test/test.php', d + '&f_num=2').then(function(data) {
		    switch(data['code']) {
			case 0:
			    fm_w = fm_tt.replace(/CT/g, data['count']);
			    fm_w = fm_w.replace(/C_MAX/g, data['max_count']);
			    fm_w = fm_w.replace(/QUES/g, data['ques']);
			    animation('data_output', 400, fm_w);
			    break;
			case 1: animation('data_output', 400, fm_fl); break;
			case 2:
			    fm_w = fm_rt.replace(/TIME/g, data['time']);
			    fm_w = fm_w.replace(/HTML/g, data['html']);
			    fm_w = fm_w.replace(/SCORE/g, data['score']);
			    animation('data_output', 400, fm_w);
			    break;
		    }
		});
	    });
	    
	    //3: Result Page / Failed Page
	    $(document).on('click', '#bt_rt_jm, #bt_rt_bk, #bt_fl_bk', function() {
		switch($(this).attr('id')) {
		    case 'bt_rt_jm':
			animation_to_sites('data_output', 400, './lesson.php');
			break;
		    case 'bt_fl_bk':
		    case 'bt_rt_bk':
			animation_to_sites('data_output', 400, './');
			break;
		}
	    });
        </script>
    </body>

</html>