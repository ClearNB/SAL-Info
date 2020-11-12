<?php

/* Query Function
 * Send Query to SQL Database for MySQL.
 * {return: Result for Query}
 */

function query($query) {
    $mysqli = get_db();
    $result = $mysqli->query($query);

    if (!$result) {
        echo json_encode(['res'=> 1, 'error'=> $mysqli->error]);
        return false;
    }

    return $result;
}

/* Random String
 */

function random($length = 8) {
    return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, $length);
}

/* Load File(*.JSON) Function
 * {args: File Name (file pass)}
 * {return: Decoded Json Data}
 */

function loadfile($filename) {
    $contextOptions = array(
        'http' => array(
            "ignore_errors" => true,
        )
    );
    $context = stream_context_create($contextOptions);
    $http_response_header = array();
    $ip = filter_input(INPUT_SERVER, 'SERVER_ADDR', FILTER_SANITIZE_STRING);
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }
    $json = file_get_contents("http://" . $ip . "/" . $filename, false, $context);
    if (empty($http_response_header)) {
        return false;
    }
    $matches = 0;
    preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
    $statusCode = $matches[1];
    if ($statusCode !== "200") {
        return false;
    }
    if (!$json) {
        return false;
    }
    $arr = json_decode($json, true);
    return $arr;
}
