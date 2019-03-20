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
            'domain'        => $_ENV['AUTH0_DOMAIN'],
            'client_id'     => $_ENV['AUTH0_CLIENT_ID'],
            'client_secret' => $_ENV['AUTH0_CLIENT_SECRET'],
            'redirect_uri'  => $_ENV['AUTH0_REDIRECT_URI'],
            'audience'      => $_ENV['AUTH0_AUDIENCE'],
            'scope'         => $_ENV['AUTH0_SCOPE'],
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