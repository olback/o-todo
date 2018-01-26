<?php

    // Load settings
    $settings = require(__DIR__.'/api/settings.php');
    require(__DIR__.'/api/functions.php');

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

                    header('Location: login.php?message=Username or password incorrect.&color=red');

                }
                
            }
          
        } else {

            header('Location: login.php?message=Username or password incorrect.&color=red');

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

        if(!empty($settings['recaptcha']['private']) && !empty($settings['recaptcha']['public'])) {

            $reCaptchaValidationUrl = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$settings['recaptcha']['private']."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link href="assets/css/main.min.css" rel="stylesheet" />
        <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
        <link rel="shortcut icon" href="assets/icons/icon-96.png" />
        <?php if(!empty($settings['recaptcha']['private']) && !empty($settings['recaptcha']['public'])) { echo '<script src="https://www.google.com/recaptcha/api.js"></script>'; } ?>
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
                padding-top: 10px;
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
            <?php echo info('author'); ?> &copy; <?php echo date('Y'); ?> &nbsp;&nbsp;&nbsp; v<?php echo info('version'); ?><br>
            Please contribute on <i class="fa fa-github" aria-hidden="true" style="color:#333;"></i> <a href="<?php echo info('project_url'); ?>" target="_blank" class="contribute">Github</a>!
        </footer>

        <!-- Github corner -->
        <a href="https://github.com/olback/o-todo" style="z-index: 100;" class="github-corner" aria-label="View source on Github"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#fc0; color:#222; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a>

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
