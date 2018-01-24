<?php

    // Helper functions
    require(__DIR__.'/functions.php');

    // Set content type to json
    header('Content-Type: application/json');

    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];

    // Check if a user is logged in. If not, return with HTTP Status code 401 Unauthorized.
    if(!loggedIn()) {
        $error = ['error' => true, 'code' => 401, 'message' => 'Unauthorized', 'method' => $method];
        http_response_code($error['code']);
        die(json_encode($error));
    }

    if($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $_DELETE);
    } elseif($method === 'PUT') {
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

        if(!empty($_POST['new-note-title']) && // Holy...
           !empty($_POST['new-note-body']) &&
           !empty($_POST['new-note-due-date']) &&
           isset($_POST['new-note-importance']) &&
           !empty($_POST['new-note-create-date'])) {

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
                
                $return = ['error' => false, 'code' => 200, 'message' => 'Success', 'method' => $method];

            } else {

                $return = ['error' => true, 'code' => 200, 'message' => 'Something went wrong!', 'method' => $method];

            }
    
            $stmt->close();
            $con->close();

            die(json_encode($return));

        } else {

            die(json_encode(['error' => true, 'code' => 200, 'message' => 'Unable to add note to database. Make sure all fields are filled in.', 'method' => $method]));

        }

        badRequest();

    }

    /**
     *  Delete note
     */
    if(isset($_DELETE['note-id'])) {

        require(__DIR__.'/initdb.php');

        $stmt = $con->prepare("DELETE FROM `notes` WHERE id=? AND user=?");
        $stmt->bind_param('is', $_DELETE['note-id'], $_COOKIE['username']);
        $stmt->execute();

        if(!$stmt->error && $stmt->affected_rows == 1) {

            $return = ['error' => false, 'code' => 200, 'message' => 'Success. Removed note from database', 'method' => $method];

        } else {

            $return = ['error' => true, 'code' => 200, 'message' => 'Unable to remove note from database', 'method' => $method];

        }

        $stmt->close();
        $con->close();

        die(json_encode($return));

    }

    /**
     *  Update existing note
     */
    if(isset($_PUT['update-note']) && !empty($_PUT['note-id'])) {

        if(!empty($_PUT['updated-note-title']) && // Holy...
           !empty($_PUT['updated-note-body']) &&
           !empty($_PUT['updated-note-due-date']) &&
           isset($_PUT['updated-note-importance'])) {

            require(__DIR__.'/initdb.php');
            $stmt = $con->prepare("UPDATE `notes` SET `title`=?, `body`=?, `due`=?, `importance`=? WHERE `user`=? AND `id`=?");
            $stmt->bind_param('sssisi', $_PUT['updated-note-title'], $_PUT['updated-note-body'], $_PUT['updated-note-due-date'], $_PUT['updated-note-importance'], $_COOKIE['username'], $_PUT['note-id']);
            $stmt->execute();

            if(!$stmt->error && $stmt->affected_rows == 1) {

                $return = ['error' => false, 'code' => 200, 'message' => 'Success. Updated note.', 'method' => $method];

            } else {

                $return = ['error' => true, 'code' => 200, 'message' => 'Unable to update note. Did you change any of the values?', 'method' => $method];

            }

            $stmt->close();
            $con->close();

            die(json_encode($return));

        } else {

            die(json_encode(['error' => true, 'code' => 200, 'message' => 'Unable to update note. Make sure all fields are filled in.', 'method' => $method]));

        }

    }

    badRequest();

?>
