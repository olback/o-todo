<?php

    // Some helper functions

    function generateRandomString($length = 25) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // Check if a user is logged in and verify that it's actually a real user.
    function loggedIn() {

        if(!isset($_COOKIE['api_key']) || !isset($_COOKIE['username'])) {
            return false;
        }

        require(__DIR__.'/initdb.php');

        $stmt = $con->prepare("SELECT `username`, `api_key`, `isAdmin` FROM `users` WHERE `username`=? AND `api_key`=?");
        $stmt->bind_param('ss', $_COOKIE['username'], $_COOKIE['api_key']);
        $stmt->execute();
        
        $data = $stmt->get_result();

        $stmt->close();
        $con->close();

        // if($stmt->error) {
        //     die('Fatal error occured.');
        // }

        if($data->num_rows == 1) {

            while($row_data = $data->fetch_assoc()) {

                session_start();
                $_SESSION['isAdmin'] = $row_data['isAdmin'];
                return $row_data['username'] == $_COOKIE['username'] && $row_data['api_key'] == $_COOKIE['api_key'];
                
            }
          
        } else {

            return false;
            
        }

    }

    function badRequest() {
        // If no action/method is specified, return with 'Bad request'.
        $error = ['error' => true, 'code' => 400, 'message' => 'Bad request', 'method' => $_SERVER['REQUEST_METHOD']];
        http_response_code($error['code']);
        die(json_encode($error));
    }

    function info($key) {
        if(file_exists('../info.json')) {
            return json_decode(file_get_contents('../info.json'), true)[$key];
        } else {
            return 'not found.';
        }
    }

    function escapeHTML($str, $allowed = ['b','i','a','ul','ol','li','br','strong','em', 'table', 'tr', 'th', 'td']) {
        $str = htmlspecialchars($str);
        foreach($allowed as $a){
            $str = str_replace("&lt;".$a."&gt;", "<".$a.">", $str);
            $str = str_replace("&lt;/".$a."&gt;", "</".$a.">", $str);
        }
        return $str;
    }

?>
