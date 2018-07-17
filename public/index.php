<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();
$kernel = new Kernel();
$kernel->boot();

try {
    $response = $kernel->handleRequest($request);
} catch (\Throwable $e) {
    $response = $kernel->handleException($request, $e);
}

$response->send();
$kernel->shutdown($request, $response);
