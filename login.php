<?php

    // Log out the user
    session_start();
    setcookie('api_key', NULL);
    setcookie('username', NULL);

    require(__DIR__.'/api/init.php');

    if(isset($_POST['sign-up'])) {

        // Validate username
        if(isset($_POST['new-username']) && trim($_POST['new-username']) == '' || empty($_POST['new-username']) || !isset($_POST['new-username'])) {
            $_SESSION['error'] = 'Username cannot be blank.';
            header('Location: login.php?new-acc=true');
            die();
        }

        if(strlen($_POST['new-username']) < 4 || strlen($_POST['new-username']) > 25) {
            $_SESSION['error'] = 'Username must contain at least 4 characters and not be longer than 25.';
            header('Location: login.php?new-acc=true');
            die();
        }

        if (!preg_match('/^[A-Za-z][A-Za-z0-9]{3,25}$/', $_POST['new-username'])) {
            $_SESSION['error'] = 'Usernames may only contain a-zA-Z0-9_-.';
            header('Location: login.php?new-acc=true');
            die();
        }

        // Validate passwords
        if(strlen($_POST['new-password']) < 7 && strlen($_POST['new-password']) <= 1000) {
            $_SESSION['error'] = 'Password must be between 8 and 1000 characters.';
            header('Location: login.php?new-acc=true');
            die();
        }
        
        if($_POST['new-password'] != $_POST['repeat-password']) {
            $_SESSION['error'] = 'Passwords does not match!';
            header('Location: login.php?new-acc=true');
            die();
        }

        $hash_options = array(
            'salt' => random_bytes(25),
            'cost' => 12,
        );

        $username = $_POST['new-username'];
        $password_hash = password_hash($_POST['new-password'], PASSWORD_BCRYPT, $hash_options);
        $api_key = generateRandomString(25);

        $stmt = $con->prepare("INSERT INTO users (`username`, `password`, `api_key`) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $password_hash, $api_key);

        $stmt->execute();

        if($stmt->error) {
            $_SESSION['error'] = 'Account creation failed. Username might already be taken.';    
            $stmt->close();
            header('Location: login.php?new-acc=true');
            die();
        }

        $stmt->close();

        $_SESSION['success'] = 'Account created!';

    }

    $con->close();

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login | o-todo</title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#ffcc00" />
        <meta name="mobile-web-app-capable" content="no">
        <link href="assets/css/main.css" rel="stylesheet" />
        <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
        <link rel="shortcut icon" href="assets/icons/icon-96.png" />
        <style>
            form {
                margin-top: 20px;
            }

            input {
                background-color: transparent;
                border: none;
                border-radius: 0;
                border-bottom: 1px solid #888;
                outline: none;
                margin-bottom: 10px;
                transition: 0.2s;
                
            }

            input:focus {
                border-color: #333;
            }

            input[type=submit], #create-account, #reset-password {
                width: max-content;
                border: none;
                background-color: transparent;
                border-radius: 0;
                color: #333;
                height: 50px;
                transition: 0.2s;
                padding: 0 20px;
            }

            input[type=submit]:hover, #create-account:hover, #reset-password:hover {
                background-color: rgba(0, 0, 0, 0.1);
            }

            #create-account, #reset-password {
                font-size: 12px;
            }

            #reset-password {
                display: block;
                margin: auto;
            }

            label {
                font-size: 1em;
            }

            p.error {
                display: block;
                margin: auto;
                color: red;
                text-align: center;
                margin-top: 10px;
            }

            p.success {
                display: block;
                margin: auto;
                color: green;
                text-align: center;
                margin-top: 10px;
                z-index: 100;
            }
        </style>
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
                <input type="text" id="username" placeholder="Username" required="required" />
                <input type="password" id="password" placeholder="Password" required="required" />
                <button type="button" id="create-account">Create account</button>
                <input type="submit" value="Login">
                <!--<button type="button" id="reset-password">Forgot your password?</button>-->
            </form>
            <p class="success"><?php if(isset($_SESSION['success'])) { echo $_SESSION['success']; } ?></p>

        </main>

        <div class="modal" id="account-modal">
            <div class="inner">
                <i id="close-create-account" class="fa fa-close close-modal" aria-hidden="true"></i>
                <h1>Create new account</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <label for="new-username">Username</label>
                    <input type="text" name="new-username" id="new-username" required="required" />
                    <label for="new-password">Password (min 8 chars)</label>
                    <input type="password" name="new-password" id="new-password" required="required" min="8"/>
                    <label for="repeat-password">Repeat password</label>
                    <input type="password" name="repeat-password" id="repeat-password" required="required" />
                    <input type="submit" name="sign-up" value="Sign up" style="margin:auto;display:block;margin-top:10px;" />
                    <p class="error"><?php if(isset($_SESSION['error'])) {echo $_SESSION['error']; } ?></p>
                </form>
            </div>
        </div>

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

            document.getElementById('create-account').onclick = () => {
                document.getElementById('account-modal').style.display = 'block';
            }

            document.getElementById('close-create-account').onclick = () => {
                document.getElementById('account-modal').style.display = 'none';
            }

            if(getParameterByName('new-acc') == 'true') {
                document.getElementById('account-modal').style.display = 'block';
            }
        </script>
    </body>
</html>

<?php 

    // Unset variables;
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    session_destroy();

?>