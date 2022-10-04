<?php

@ini_set('error_level', E_ALL & ~E_STRICT & ~E_NOTICE & ~E_USER_NOTICE);

use App\Exceptions\Handler;
use App\Http\Middleware;
use App\Http\Request;
use App\Loggers\FileLogger;

date_default_timezone_set('Asia/Taipei');

include __DIR__.'/../app/helpers.php';
include __DIR__.'/../vendor/autoload.php';

$config = include __DIR__.'/../config.php';
$request = new Request();
$middleware = new Middleware($request);
$logger = new FileLogger($config);
Handler::make($logger);
