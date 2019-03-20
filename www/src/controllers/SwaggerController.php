<?php

namespace App\Controllers;

use App\Controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class SwaggerController extends Controller {

    public function get_swagger_json(Request $request, Response $response, array $args) {    
        $openapi = \OpenApi\scan(__DIR__ . '/../../src');
        header('Content-Type: application/x-yaml');
        return $response
            ->withAddedHeader('Content-Type', 'application/json')
            ->write($openapi->toJson());
    }

    public function get_swagger_ui(Request $request, Response $response, array $args) {    
        $file = __DIR__. '/swagger/index.html';
        if (file_exists($file)) {
            return $response->write(file_get_contents($file));
        } else {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
    }
}