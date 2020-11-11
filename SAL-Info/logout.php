<!DOCTYPE html>

<?php
session_start();
unset($_SESSION['mktk_userindex']);
http_response_code(301);
header("Location: index.php");
