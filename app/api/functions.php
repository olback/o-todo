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

        $stmt = $con->prepare("SELECT `username`, `password`, `api_key` FROM `users` WHERE `api_key`=?");
        $stmt->bind_param('s', $_COOKIE['api_key']);
        $stmt->execute();
        
        $data = $stmt->get_result();

        // if($stmt->error) {
        //     die('Fatal error occured.');
        // }

        if($data->num_rows == 1) {

            while($row_data = $data->fetch_assoc()) {

                return $row_data['username'] == $_COOKIE['username'] && $row_data['api_key'] == $_COOKIE['api_key'];
                
            }
          
        } else {

            return false;
            
        }

        $stmt->close();
        $con->close();

    }

    function badRequest() {
        // If no action/method is specified, return with 'Bad request'.
        $error = ['error' => true, 'code' => 400, 'message' => 'Bad request', 'method' => $_SERVER['REQUEST_METHOD']];
        http_response_code($error['code']);
        die(json_encode($error));
    }

    function version() {
        if(file_exists('../info.json')) {
            // Return first 7 chars of git hash
            return json_decode(file_get_contents('../info.json'), true)['version'];
        } else {
            return 'not found.';
        }
    }

?>
