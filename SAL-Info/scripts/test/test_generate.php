<?php

include_once ('../sqldata.php');
include_once ('../common.php');
include_once ('../dbconfig.php');
include_once ('../table_generator.php');
include_once ('../confirmls.php');
include_once ('../outputStatus.php');

/**
 * Quiz - Quizクラスです
 * =======================
 * ※SELECTをしていなければ正しく動作しません
 * - $setid	受講対象者のセットID
 * - $count	問題解答済み問題数
 * - $c_count	現在問題番号
 * - $max_count	問題数の最大値
 * - $data	問題のCONTENTIDの配列
 * - $c_data	重複出題防止のためのフラグ配列（選んでいれば1, そうでなければ0）【初期値: 0】
 * - $a_data	ユーザの解答情報配列
 */
class Quiz {

    private $setid;
    private $count;
    private $max_count;
    private $data;
    private $c_data;
    private $a_data;

    /**
     * コンストラクタ。受講者のセットIDを指定します。
     * 【変数初期化】
     * ・$setid  -> $setid
     * ・$count  -> 0
     * ・$data   -> [] 初期化
     * ・$c_data -> [] 初期化
     * ・$a_data -> [] 初期化
     * ・初期化メソッド: $generate_quiz($setid)
     * @param type $setid ... 受講者のセットID
     */
    function __construct($setid) {
	$this->setid = $setid;
	$this->count = 1;
	$this->max_count = 0;
	$this->data = [];
	$this->c_data = [];
	$this->a_data = ["id" => [], "ques" => [], "ans" => []];
	$this->generate_quiz($setid);
    }

    /**
     * [generate_quiz]
     * 問題を data に、フラグを c_data に設定します
     * @param type $setid ... 受講者のセットID
     */
    function generate_quiz($setid) {
	$flag = true;

	// 1: テーマ数を数える
	$sql01 = select(true, 'MKTK_USERS_SL a, MKTK_LS_THEME b', 'COUNT(*) AS COUNT_LS_THEME', 'WHERE a.SETID = ' . $setid . ' AND a.OCCRID = b.OCCRID');

	if ($sql01) {
	    $this->max_count = (int) $sql01['COUNT_LS_THEME'] * 5;

	    $sql02 = select(true, 'MKTK_USERS_SET', 'YEARFLAG', 'WHERE SETID = ' . $setid);
	    if ($sql02) {
		$diff = $sql02['YEARFLAG'];
		if ($diff == 2) {
		    $sql03 = select(false, 'MKTK_LS_LIST a, MKTK_LS_TEST b', 'CONTENTID', 'WHERE a.SETID = ' . $setid . ' AND a.LSID = b.LSID AND b.DIFFICULTY IN (1, ' . $diff . ')');
		} else {
		    $sql03 = select(false, 'MKTK_LS_LIST a, MKTK_LS_TEST b', 'CONTENTID', 'WHERE a.SETID = ' . $setid . ' AND a.LSID = b.LSID AND b.DIFFICULTY = ' . $diff);
		}
		if ($sql03) {
		    while ($row = $sql03->fetch_assoc()) {
			array_push($this->data, $row['CONTENTID']);
			array_push($this->c_data, 0);
		    }
		} else {
		    $flag = false;
		}
	    } else {
		$flag = false;
	    }
	} else {
	    $flag = false;
	}
	if (!$flag) {
	    $this->data = [0];
	}
    }

    /**
     * 問題を提出します
     * 返り値: 正しく取得できる場合: データ
     * エラー返り値: 1...データベース接続エラー, 2...解答完了エラー
     */
    function getQues() {
	if ($this->count - 1 == $this->max_count) {
	    return 2;
	} else {
	    do {
		$i = random_int(0, sizeof($this->data) - 1);
	    } while ($this->c_data[$i] != 0);
	    $select = select(true, 'MKTK_LS_TEST', 'QUESTION', 'WHERE CONTENTID = ' . $this->data[$i]);
	    if ($select) {
		$this->c_data[$i] = 1;
		$this->a_data['id'][$this->count - 1] = $this->data[$i];
		$this->a_data['ques'][$this->count - 1] = $select['QUESTION'];
		$this->a_data['ans'][$this->count - 1] = '';
		return ["ques" => $select['QUESTION'], "ans" => ""];
	    } else {
		return false;
	    }
	}
    }

    function setAns($ans) {
	$this->a_data['ans'][$this->count - 1] = $ans;
	$this->count = $this->count + 1;
    }

    function getCount() {
	return $this->count;
    }

    function getMax() {
	return $this->max_count;
    }

    function getResult() {
	$index = getIndex();
	$c_text = implode($this->a_data['id'], ', ');
	$select = select(false, 'MKTK_LS_TEST', 'CONTENTID, ANSWER', 'WHERE CONTENTID IN (' . $c_text . ')');
	if ($select) {
	    $ans_arr = [];
	    while ($row = $select->fetch_assoc()) {
		$c_id = $row['CONTENTID'];
		$ans_arr[$c_id] = $row['ANSWER'];
	    }
	    $r_col = [['問題番号', '問題文', '解答', '正答', '正誤'], ['ID', 'QUES', 'ANS', 'C_ANS', 'FLAG']];
	    $r_data = [];
	    $r_id = $this->a_data['id'];
	    $r_ques = $this->a_data['ques'];
	    $r_ans = $this->a_data['ans'];
	    
	    $c_sum = 0;
	    for ($i = 0; $i < sizeof($r_id); $i++) {
		$f = false;
		$r_l = mb_strtoupper($r_ans[$i]);
		$ans_sr_arr = explode(",", $ans_arr[intval($r_id[$i])]);
		foreach ((array) $ans_sr_arr as $an) {
		    $an_l = mb_strtoupper($an);
		    if ($r_l == $an_l) {
			$f = true;
		    }
		}
		$f_text = '不正解';
		$f_num = 0;
		if ($f) {
		    $f_text = '正解';
		    $f_num = 1;
		    $c_sum += 1;
		}
		array_push($r_data, ['ID' => $i + 1, 'QUES' => $r_ques[$i], 'ANS' => $r_ans[$i], 'C_ANS' => implode((array) $ans_arr[$r_id[$i]], ', '), 'FLAG' => $f_text]);
	    }
	    $max = $this->max_count;
	    $c_per = intval(($c_sum / $max) * 100);
	    $html = table_horizonal_v('table-result', 'テスト結果', 'file-text', $r_col[0], $r_col[1], $r_data);
	    $status_code = confirm_lessondata($index);
	    $typeid = 1;
	    if ($status_code == 4) {
		$typeid = 2;
	    }
	    $time = date("Y-m-d H:i:s");
	    $insert1 = insert('MKTK_TEST', ['SETID', 'TESTTYPEID', 'SCORE', 'TESTTIME'], [$this->setid, $typeid, $c_per, $time]);
	    if (!$insert1) {
		return false;
	    } else {
		$select1 = select(true, 'MKTK_TEST', 'TESTID', "WHERE SETID = $this->setid AND TESTTYPEID = $typeid");
		if ($select1) {
		    $flag = true;
		    $testid = $select1['TESTID'];
		    for ($i = 0; $i < sizeof($r_id); $i++) {
			$result = insert('MKTK_TEST_QS', ['TESTID', 'CONTENTID', 'ANSWER', 'FLAG'], [$testid, $r_id[$i], $r_ans[$i], $f_num]);
			if (!$result) {
			    $flag = false;
			    break;
			}
		    }
		    switch($status_code) {
			case 3:
			    $result = update('MKTK_USERS_SET', 'COMPLETEDFLAG', '0', 'WHERE SETID = ' . $this->setid);
			    break;
			case 4:
			    $result = update('MKTK_USERS_SET', 'COMPLETEDFLAG', '2', 'WHERE SETID = ' . $this->setid);
			    break;
		    }
		    if ($flag) {
			return ['html' => $html, 'score' => $c_per, 'time' => $time];
		    } else {
			return false;
		    }
		} else {
		    return false;
		}
	    }
	} else {
	    return false;
	}
    }

}
