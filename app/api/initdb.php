<?php

if(file_exists(__DIR__ . '/settings.php')) {
    $conf = require(__DIR__ . '/settings.php');
} else {
    die('Config file not found');
}

$con = mysqli_connect($conf['mysql']['host'], $conf['mysql']['username'], $conf['mysql']['password'], $conf['mysql']['database'], $conf['mysql']['port']);

if ($con->connect_error) {
    die('Connection to database failed');
}

?>