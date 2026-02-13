<?php

function view($path)
{

    ob_start();

    require __DIR__ . "/views/$path.php";

    $content = ob_get_clean();

    require __DIR__ . "/views/layout.php";
}
