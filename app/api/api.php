<?php

    // Helper functions
    require(__DIR__.'/functions.php');

    // Set content type to json
    header('Content-Type: application/json');

    // Check if a user is logged in. If not, return with HTTP Status code 401 Unauthorized.
    if(!loggedIn()) {
        $error = ['error' => true, 'code' => 401, 'message' => 'Unauthorized', 'method' => $method];
        http_response_code($error['code']);
        die(json_encode($error));
    }

    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $_DELETE);
    } elseif ($method === 'PUT') {
        parse_str(file_get_contents('php://input'), $_PUT);
    }

    /**
     * Return notes
     */
    if(isset($_GET['action']) && $_GET['action'] == 'list') {

        http_response_code(200);

        require(__DIR__.'/initdb.php');

        $stmt = $con->prepare("SELECT `id`, `title`, `body`, `due`, `created`, `importance` FROM `notes` WHERE `user`=?");
        $stmt->bind_param('s', $_COOKIE['username']);
        $stmt->execute();

        $data = $stmt->get_result();

        $notes['notes'] = [];

        while($row_data = $data->fetch_assoc()) {

            array_push($notes['notes'], $row_data);
            
        }

        $stmt->close();
        $con->close();
        
        die(json_encode($notes));

    }

    /**
     * Handle new notes
     */
    if(isset($_POST['new-note'])) {

        if(isset($_POST['new-note-title']) && // Holy...
           isset($_POST['new-note-body']) &&
           isset($_POST['new-note-due-date']) &&
           isset($_POST['new-note-importance']) &&
           isset($_POST['new-note-create-date'])) {

            http_response_code(200);

            require(__DIR__.'/initdb.php');

            $note['title'] = htmlspecialchars($_POST['new-note-title']);
            $note['body'] = htmlspecialchars($_POST['new-note-body']);
            $note['due'] = htmlspecialchars($_POST['new-note-due-date']);
            $note['importance'] = htmlspecialchars($_POST['new-note-importance']);
            $note['created'] = htmlspecialchars($_POST['new-note-create-date']);

            $stmt = $con->prepare("INSERT INTO notes (`user`, `title`, `body`, `due`, `created`, `importance`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssi', $_COOKIE['username'], $note['title'], $note['body'], $note['due'], $note['created'], $note['importance']);
            $stmt->execute();
    
            if(!$stmt->error && $stmt->affected_rows == 1) {
                
                $return = ['error' => false, 'code' => 200, 'message' => 'Success'];

            } else {

                $return = ['error' => true, 'code' => 200, 'message' => 'Unable to add note to database'];

            }
    
            $stmt->close();
            $con->close();

            die(json_encode($return));

        } else {

            badRequest();

        }

        badRequest();

    }

    if(isset($_DELETE['note-id'])) {

        require(__DIR__.'/initdb.php');

        $stmt = $con->prepare("DELETE FROM `notes` WHERE id=? AND user=?");
        $stmt->bind_param('is', $_DELETE['note-id'], $_COOKIE['username']);
        $stmt->execute();

        if(!$stmt->error && $stmt->affected_rows == 1) {

            $return = ['error' => false, 'code' => 200, 'message' => 'Success. Removed note from database'];

        } else {

            $return = ['error' => true, 'code' => 200, 'message' => 'Unable to remove note from database'];

        }

        $stmt->close();
        $con->close();

        die(json_encode($return));

    }

    badRequest();

?>
