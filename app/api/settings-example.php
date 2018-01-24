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
            'private' => '', // If you want a reCaptcha when creating new accounts.
            'public' => '' // Site key
        ],
        'allow-sign-up' => true // Allow new users?
    ];

?>
