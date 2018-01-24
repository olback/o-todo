<?php

    // Load settings
    $settings = require(__DIR__.'/api/settings.php');

    // Sign out the user when navigating to this page.
    setcookie('api_key', NULL);
    setcookie('username', NULL);

    if(isset($_POST['sign-in'])) {

        require(__DIR__.'/api/initdb.php');

        $stmt = $con->prepare("SELECT `username`, `password`, `api_key` FROM `users` WHERE `username`=?");
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        
        $data = $stmt->get_result();

        if($stmt->error) {
            $_SESSION['error'] = 'Could not log in. Try again.';
        }

        if($data->num_rows == 1) {

            while($row_data = $data->fetch_assoc()) {

                if(password_verify($_POST['password'], $row_data['password'])) {
                    
                    setcookie('api_key', $row_data['api_key'], time() + (30 * 24 * 60 * 60), '/');
                    setcookie('username', $row_data['username'], time() + (30 * 24 * 60 * 60), '/');
                    header('Location: index.php');

                } else {

                    header('Location: login.php?modal=yes&message=Username or password incorrect.&color=red');

                }
                
            }
          
        } else {

            header('Location: login.php?modal=yes&message=Username or password incorrect.&color=red');

        }

        $stmt->close();
        $con->close();

    }

    function signUp() {

        // Validate username
        if(isset($_POST['new-username']) && trim($_POST['new-username']) == '' || empty($_POST['new-username']) || !isset($_POST['new-username'])) {
            header('Location: login.php?modal=yes&message=Username cannot be blank.&color=red');
            die();
        }

        if(strlen($_POST['new-username']) < 4 || strlen($_POST['new-username']) > 25) {
            header('Location: login.php?modal=yes&message=Username must contain at least 4 characters and not be longer than 25.&color=red');
            die();
        }

        if (!preg_match('/^[A-Za-z][A-Za-z0-9]{3,25}$/', $_POST['new-username'])) {
            header('Location: login.php?modal=yes&message=Usernames may only contain a-zA-Z0-9_-.&color=red');
            die();
        }

        // Validate passwords
        if(strlen($_POST['new-password']) < 8 && strlen($_POST['new-password']) <= 1000) {
            header('Location: login.php?modal=yes&message=Password must be between 8 and 1000 characters.&color=red');
            die();
        }
        
        if($_POST['new-password'] != $_POST['repeat-password']) {
            header('Location: login.php?modal=yes&message=Passwords do not match!&color=red');
            die();
        }

        $hash_options = array(
            'salt' => random_bytes(25),
            'cost' => 12,
        );

        require(__DIR__.'/api/initdb.php');
        require(__DIR__.'/api/functions.php');

        $username = $_POST['new-username'];
        $password_hash = password_hash($_POST['new-password'], PASSWORD_BCRYPT, $hash_options);
        $api_key = generateRandomString(25);

        $stmt = $con->prepare("INSERT INTO users (`username`, `password`, `api_key`) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $password_hash, $api_key);

        $stmt->execute();

        if($stmt->error) {
            $stmt->close();
            $con->close();
            header('Location: login.php?modal=yes&message=Account creation failed. Username might already be taken.&color=red');
            die();
        }

        $stmt->close();
        $con->close();
        header('Location: login.php?message=Account created!&color=green');
        die();

    }

    if(isset($_POST['sign-up']) && $settings['allow-sign-up']) {

        if(!empty($settings['recaptcha']['captcha-private']) && !empty($settings['recaptcha']['captcha-public'])) {

            $reCaptchaValidationUrl = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$settings['captcha-private']."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
            $result = json_decode($reCaptchaValidationUrl, TRUE);

            if($result['success'] == 1) {
                signUp();
            } else {
                header('Location: login.php?modal=yes&message=reCaptcha failed.&color=red');
                die();
            }

        } else {
            
            signUp();

        }

    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login | o-todo</title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#ffcc00" />
        <meta name="mobile-web-app-capable" content="no">
        <link href="assets/css/main.min.css" rel="stylesheet" />
        <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
        <link rel="shortcut icon" href="assets/icons/icon-96.png" />
        <?php if(!empty($settings['captcha-private']) && !empty($settings['captcha-public'])) { echo '<script src="https://www.google.com/recaptcha/api.js"></script>'; } ?>
        <style>
            form {
                margin-top: 20px;
                padding-bottom: 10px;
            }
            input:last-of-type {
                padding-top: 10px;
            }
            .recap {
                display: block;
                width: min-content;
                margin: 0 auto !important;
            }
            footer {
                width: 100%;
                text-align: center;
                padding: 10px 0;
            }
        </style>
        <script>
            function getParameterByName(name, url) {
                if (!url) url = window.location.href;
                name = name.replace(/[\[\]]/g, "\\$&");
                const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
                if(!results) return null;
                if(!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, " "));
            }
        </script>
    </head>
    <body>

        <!-- Top navigation -->
        <nav>
            <h1 class="title" style="margin-left: 20px;">o-ToDo</h1>
        </nav>

        <!-- Main section -->
        <main id="main">

            <h1>o-todo Login</h1>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="text" name="username" id="username" placeholder="Username" required="required" min="4" />
                <input type="password" name="password" id="password" placeholder="Password" required="required" min="8" />
                <?php if($settings['allow-sign-up']) { echo '<button type="button" id="create-account">Create account</button>'; } ?>
                <button type="submit" name="sign-in" style="font-size: 1.4em">Login</button>
            </form>
            <p class="message"></p>

        </main>

        <footer>
            olback &copy; <?php echo date('Y'); ?>
        </footer>

        <?php
            if($settings['allow-sign-up']) {
                require(__DIR__.'/include/sign-up.php');
            }
        ?>

        <script>

            const message = document.getElementsByClassName('message');
            for(let i = 0; i < message.length; i++) {
                message[i].innerHTML = getParameterByName('message');
                let color = getParameterByName('color');
                if(color) {
                    message[i].style.color = color;
                } else {
                    message[i].style.color = 'black';
                }
            }

        </script>
    </body>
</html>
