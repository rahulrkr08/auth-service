<?php

namespace App;

/**
 * @OA\Info(title="Authentication service", version="0.1")
 */

/**
 * @OA\Server(
 *      url="http://localhost/auth-service/public/",
 *      description="OpenApi parameters"
 * )
 */
class Controller {

    public function __construct($container) {
        $this->container = $container;        
    }
}