<?php

namespace App\Controllers;

use App\Controller;
use App\Auth;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;

class SessionController extends Controller {
    
    public function get_session(Request $request, Response $response, array $args) {

        $defaultSessionData = array(
            'isAuthenticated' => false,
            'metalinks' => array(
                'login_url' => $_ENV['LOGIN_URL'] ?? $request->getUri()->getBaseUrl(). '/login'
            )
        );
        
        $sessionData = array();
    
        try {
            $userInfo = Auth::Instance()->getUser();    
            if ($userInfo) {
                $sessionData['user'] = $userInfo;
                $sessionData['isAuthenticated'] = true;
            }
    
            return $response->withJson(array_merge($defaultSessionData, $sessionData));
    
        } catch(Execption $e) {
            var_dump($e);
        }
    }

    public function login(Request $request, Response $response, array $args) {        
        $state = JWT::encode($request->getParams(), 'test');
        return Auth::Instance()->login($state);
    }

    public function verify(Request $request, Response $response, array $args) {

        if(Auth::Instance()->exchange()) {
            $state = Auth::Instance()->getState();
            $stateData = JWT::decode($state, 'test', array('HS256'));
            
            if(isset($stateData->redirect_url)) {
                header('Location: '. $stateData->redirect_url);
                exit;
            }
            $current_path = $request->getUri()->getBaseUrl(). '/'. $request->getUri()->getPath();
            header('Location: '. $current_path);
            exit;
        }

        return $this->get_session($request, $response, $args);
    }
}