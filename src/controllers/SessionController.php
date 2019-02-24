<?php

namespace App\Controllers;

use App\Controller;
use App\Auth;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class SessionController extends Controller {
    /**
     * @OA\Info(title="My First API", version="0.1")
     */

    /**
     * @OA\Get(
     *     path="/api/resource.json",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function get_session(Request $request, Response $response, array $args) {

        $defaultSessionData = array('isAuthenticated' => false);
        $sessionData = array();
    
        try {
            $userInfo = Auth::Instance()->getUser();
    
            if ($userInfo) {
                $sessionData['user'] = $userInfo;
                $sessionData['isAuthenticated'] = false;
            }
    
            return $response->withJson(array_merge($defaultSessionData, $sessionData));
    
        } catch(Execption $e) {
            var_dump($e);
        }
    }
}