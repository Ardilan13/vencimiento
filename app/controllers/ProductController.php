<?php

require 'app/models/Product.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    Product::create(
        $_POST['name'],
        $_POST['description']
    );

    header("Location: ?url=products");
}

$products = Product::all();

require 'app/views/products/index.php';
