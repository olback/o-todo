<?php

if(file_exists(__DIR__ . '/db_conf.php')) {
    $conf = require(__DIR__ . '/db_conf.php');
} else {
    die('Config file not found');
}

$con = mysqli_connect($conf['host'], $conf['username'], $conf['password'], $conf['database']);

if ($con->connect_error) {
    die('Connection to database failed');
}

?>