<?php

$url = $_GET['url'] ?? '/';

switch ($url) {

    case '/':
        require 'app/controllers/DashboardController.php';
        break;

    case 'products':
        require 'app/controllers/ProductController.php';
        break;

    case 'boxes':
        require 'app/controllers/BoxController.php';
        break;

    case 'alerts':
        require 'app/controllers/AlertController.php';
        break;

    default:
        echo "404";
}
