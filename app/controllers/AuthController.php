<?php

session_start();

require 'app/models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user = User::findByEmail($_POST['email']);

    if ($user && password_verify($_POST['password'], $user['password'])) {

        $_SESSION['user'] = $user;

        header("Location: ?url=/");
        exit;
    }

    echo "Credenciales incorrectas";
}

require 'app/views/auth/login.php';
