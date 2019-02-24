<?php

require_once(__DIR__. './Cloudant.php');

class Session {
    
    private $db;
    private $session_cookie_expire;

	public function __construct(){

        $this->session_cookie_expire = $_ENV['SESSION_EXPIRE'] ?? '120'; 
        session_set_cookie_params((int) $this->session_cookie_expire);
		
        // Set handler to overide SESSION
		session_set_save_handler(
            array($this, "_open"),
            array($this, "_close"),
            array($this, "_read"),
            array($this, "_write"),
            array($this, "_destroy"),
            array($this, "_gc")
        );
        
        

		// Start the session
		session_start();
    }
    
	public function _open(){
		// If successful
		return Cloudant::Instance()->isConnected() ?? false;
    }
    
	public function _close(){
        // TODO: Close DB connection
		return true;
    }
    
	public function _read($id){
        $doc = Cloudant::Instance()->get($id);
        if(isset($doc->error)) {
            return '';
        }

        if(time() > $doc->expired_in) {
            $d = Cloudant::Instance()->delete($doc->_id);
            return '';
        }

        return $doc->data;
    }
    
	public function _write($id, $data){
        
        $access = time();
        $doc = Cloudant::Instance()->put($id, array('data' => $data, 'access' => $access, 'expired_in' => $access + $this->session_cookie_expire));
        
        if(isset($doc['error'])) {
            return false;
        }
        return true;
    }
    
	public function _destroy($id){
        Cloudant::Instance()->delete($id);
        return true;
    }
    
	public function _gc($max){
        $fp = fopen('lidn.txt', 'w');
        fwrite($fp, 'Cats chase mice');
        fclose($fp);
        return true;
	}
}
