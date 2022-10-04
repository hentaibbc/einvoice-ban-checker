<?php

$_include = __DIR__.$_SERVER['REQUEST_URI'].'.php';
if (file_exists($_include)) {
    include $_include;
    exit;
}

header('HTTP 404 Not Found');
exit;