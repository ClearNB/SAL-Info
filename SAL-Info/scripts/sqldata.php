<?php

/* SQLData
 * desc: Generate SQL and execute it.
 * Dependent by common.php
 * [Function List]
 * + getServerStatus()
 * + getAllUserData()
 */

//テーブルの状態を把握し、存在しない場合は作成を試みます。
function setTableStatus($tables) {
    $r = true;
    foreach ($tables as $var) {
	$query = "SELECT * FROM $var[0]";
	$data = query($query);
	//テーブルがある場合・ない場合
	if (!$data) {
	    $data = createTable($var[0], $var[1]);
	    if ($var[0] == 'MKTK_USERS') {
		$salt = random(20);
		$hash = hash('sha256', 'UserPass01' . $salt);
		$sql01 = insert('MKTK_USERS', ['USERID', 'USERNAME', 'PASSWORDHASH', 'PERMISSION', 'SALT'], ['user01', 'User01', $hash, 1, $salt]);
		if (!$sql01) {
		    $data = false;
		}
	    }
	}
	$r = $r && $data;
    }
    return $r;
}

function createTable($table, $column) {
    $query = "CREATE TABLE $table ($column)";
    $result = query($query);
    return $result;
}

function insert($table, $column, $value) {
    $column_text = implode($column, ', ');
    for ($i = 0; $i < sizeof($value); $i++) {
	if (gettype($value[$i]) === 'string') {
	    $value[$i] = "'" . $value[$i] . "'";
	}
    }
    $value_text = implode($value, ", ");
    $query = "INSERT INTO $table ($column_text) VALUES ($value_text)";
    $result = query($query);
    return $result;
}

function insert_select($table, $column, $select) {
    $column_text = implode($column, ', ');
    $query = "INSERT INTO $table ($column_text) $select";
    $result = query($query);
    return $result;
}

function update($table, $column, $value, $where = '') {
    $text = $value;
    if (gettype($text) === 'string') {
	$text = "'" . $text . "'";
    }
    $query = "UPDATE $table SET $column = $text $where";
    $result = query($query);
    return $result;
}

function delete($table, $where = '') {
    $query = "DELETE FROM $table $where";
    $result = query($query);
    return $result;
}

function select($one_column, $table, $column, $other = '') {
    $query = "SELECT $column FROM $table $other";
    $result = query($query);
    if ($one_column && $result) {
	$result = $result->fetch_assoc();
    }
    return $result;
}

function reset_auto_increment($table, $index = 1) {
    $query = "ALTER TABLE $table AUTO_INCREMENT = $index";
    $result = query($query);
    return $result;
}