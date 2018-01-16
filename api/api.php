<?php

    // API

    //require(__DIR__.'/init.php');

    $API_KEY = '0UagHLS6KdTJ2yQ105K9';

    // Serve note list
    if(isset($_GET['api_key']) && $_GET['api_key'] == $API_KEY) {
        if($_GET['action'] == 'list') {
            http_response_code(200);
            header('Content-Type: application/json');

            
            //$con->close();

            // Temporary, read from db.
            $file = '../assets/test/sample-list.json';
            $f = fopen($file, "r");
            $c = fread($f, filesize($file));
            fclose($f);
            echo $c;
            
            die();
        } else {
            echo 'Bad Request 400';
            http_response_code(400);
            die();
        }
    }

    echo 'Unauthorized 401';
    http_response_code(401);

?>
