<?php

static $mysqli;

function get_db() {
    try {
        $data = loadfile("data/database.json");

        $HOST = $data['host'];
        $USERNAME = $data['user'];
        $PASSWORD = $data['password'];
        $DBNAME = $data['database'];
        $PORT = $data['port'];

        $mysqli = new mysqli($HOST, $USERNAME, $PASSWORD, $DBNAME, $PORT);
        $mysqli->set_charset("utf8");
        return $mysqli;
    } catch (Exception $ex) {
        print "データベースへのアクセスに失敗:\n$ex";
        return false;
    }
}

function escape($str) {
    $mysqli = get_db();
    return $mysqli->real_escape_string($str);
}
