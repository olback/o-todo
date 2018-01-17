<?php

    require(__DIR__.'/functions.php');

    header('Content-Type: application/json');

    if(!loggedIn()) {
        $error = ['error' => true, 'code' => 401, 'message' => 'Unauthorized'];
        http_response_code($error['code']);
        die(json_encode($error));
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

        echo json_encode($notes);

        $stmt->close();
        $con->close();
        
        die();

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
    
            if($stmt->error) {
                $stmt->close();
                $con->close();
                $error = ['error' => true, 'code' => 200, 'message' => 'Unable to add note to database', 'errInfo' => $stmt];
                die(json_encode($error));
            }
    
            $stmt->close();
            $con->close();

            $success = ['error' => false, 'code' => 200, 'message' => 'Success'];
            die(json_encode($success));

        } else {

            badRequest();

        }

        badRequest();

    }

    badRequest();

?>
