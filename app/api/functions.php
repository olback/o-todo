<?php

    // Functions...?

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


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

            $_SESSION['error'] = 'Username or password incorrect.';
            
        }

        $stmt->close();
        $con->close();

    }

    function badRequest() {
        // If no action/method is specified, return with 'Bad request'.
        $error = ['error' => true, 'code' => 400, 'message' => 'Bad request'];
        http_response_code($error['code']);
        die(json_encode($error));
    }

?>
