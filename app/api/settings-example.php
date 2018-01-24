<?php

    /* Rename this file to 'settings.php' */    

    return [
        'mysql' => [ // MySQL connection info
            'host' => 'localhost',
            'port' => '3306',
            'username' => 'o-todo',
            'password' => '',
            'database' => 'o-todo'
        ],
        'recaptcha' => [
            'captcha-private' => '', // If you want a reCaptcha when creating new accounts.
            'captcha-public' => '' // Site key
        ],
        'allow-sign-up' => true // Allow new users?
    ];

?>
