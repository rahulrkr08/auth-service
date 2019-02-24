<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

error_reporting(0); 

include('../src/lib/SessionHandler.php');
include('../src/lib/Auth.php');

$session = new Session();

$config = [
    'settings' => [
        'displayErrorDetails' => true
    ],
];

$app = new \Slim\App($config);

$container = $app->getContainer();

$container['HomeController'] = function($container) {
    return new \App\Controllers\HomeController($container);
};

$container['SessionController'] = function($container) {
    return new \App\Controllers\SessionController($container);
};

require_once __DIR__ . '/../src/routes.php';

// Common route for /swagger
$app->get('/swagger', function(Request $request, Response $response, array $args) {    
    $openapi = \OpenApi\scan(__DIR__ . '/../src');
    header('Content-Type: application/x-yaml');
    return $response
        ->withAddedHeader('Content-Type', 'application/json')
        ->write($openapi->toJson());
});

$app->run();
