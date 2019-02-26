<?php

namespace App;

use Auth0\SDK\Auth0;
use Auth0\SDK\API\Authentication;

final class Auth {

    private static $instance = null;
    private $auth0  = null;
    public static function Instance() {
        if (self::$instance === null) {
            self::$instance = new Auth();
        }
        return self::$instance;
    }

    private function __construct() {
        
        $this->auth0 = new Auth0([
            'domain'        => $_ENV['AUTH0_DOMAIN'] ?? 'avaazz.auth0.com',
            'client_id'     => $_ENV['AUTH0_CLIENT_ID'] ?? 'sGRnbt7rujtTyhG4C7Sv0F3vDkinVjI3',
            'client_secret' => $_ENV['AUTH0_CLIENT_SECRET'] ?? 'V9uiiL8V3TsfqSTwnbmc_6Hp0uKgKl-6qzVsUm0atc5eW4U5_ycHeCqUcQLFA2ix',
            'redirect_uri'  => $_ENV['AUTH0_REDIRECT_URI'] ?? 'http://localhost/auth-service/public/verify',
            'audience'      => $_ENV['AUTH0_AUDIENCE'] ?? 'https://avaazz.auth0.com/userinfo',
            'scope'         => $_ENV['AUTH0_SCOPE'] ?? 'openid profile',
            'persist_id_token' => true,
            'persist_access_token' => true,
            'persist_refresh_token' => true
        ]);
    }

    public function __call(string $function_name, array $arguments) {
        return call_user_func_array(array($this->auth0, $function_name), $arguments);
    }

    /**
     * Get the state from POST or GET, depending on response_mode
     *
     * @return string|null
     */
    public function getState() {
        $state = null;
        if (isset($_GET['state'])) {
            $state = $_GET['state'];
        } else if (isset($_POST['state'])) {
            $state = $_POST['state'];
        }

        return $state;
    }
}