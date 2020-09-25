<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read https://symfony.com/doc/current/setup.html#checking-symfony-application-configuration-and-setup
// for more information
umask(0002);
require __DIR__.'/../vendor/autoload.php';

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__.'/../.env', __DIR__.'/../.env.common');

$env = $_ENV['SYMFONY_ENV'] ?? 'prod';
$debug = (bool)getenv('SYMFONY_DEBUG');

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
$scheme = 'http';
if ($env !== 'dev') {
    $scheme = 'https';
}

if (getenv('TEST_URL') === "$scheme://{$_SERVER['HTTP_HOST']}") {
    $env = 'test';
    $debug = true;
}

if ($debug) {
    Debug::enable();
}

$kernel = new AppKernel($env, $debug);
if (PHP_VERSION_ID < 70000) {
    die('PHP 7+ only');
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
