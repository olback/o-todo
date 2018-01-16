<?php

if(file_exists(__DIR__ . '/config.php')) {
    $conf = require(__DIR__ . '/config.php');
} else {
    die('Config file not found');
}

$con = mysqli_connect($conf['host'], $conf['username'], $conf['password'], $conf['database']);

if ($con->connect_error) {
    die('Connection to database failed');
} 

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>