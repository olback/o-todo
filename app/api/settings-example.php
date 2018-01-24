<?php

    /* Rename this file to 'settings.php' */    

    return [
        'mysql' => [ // MySQL connection info
            'host' => '',
            'port' => '',
            'username' => '',
            'password' => '',
            'database' => ''
        ],
        'recaptcha' => [
            'captcha-private' => '', // If you want a reCaptcha when creating new accounts.
            'captcha-public' => '' // Site key
        ],
        'allow-sign-up' => true // Allow new users?
    ];

?>
