<?php
/*
 * Copyright IBM Corp. 2017
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Class to handle performing basic CRUD operations on a Couch DB.
 * This class uses the Sag library to talk to the Couch DB.
 */
final class Cloudant {

	private static $inst = null;
    private $sag;
		private $db_exists = false;

    public static function Instance() {
        if (self::$inst === null) {
            self::$inst = new Cloudant();
        }
        return self::$inst;
    }

    public function isConnected() {
        return $this->db_exists;
    }


    private function __construct() {        
        
		#If running locally enter your own host, port, username and password
		$host 		= $_ENV['CLOUDANT_HOST'] ?? '0b0138f7-b259-4aef-a247-34801977c9be-bluemix.cloudant.com'; 
		$port 		= '443';
		$username 	= $_ENV['CLOUDANT_USERNAME'] ?? '0b0138f7-b259-4aef-a247-34801977c9be-bluemix'; 
		$password 	= $_ENV['CLOUDANT_PASSWORD'] ?? 'ae435d591665d747213b0b347ed4099f3bca1b17e26d2b2ecb0b732ed0e6bb97';
		$dbname 	= $_ENV['CLOUDANT_PASSWORD'] ?? 'sessions';

		if($vcapStr = getenv('VCAP_SERVICES')) {
			$vcap = json_decode($vcapStr, true);
			foreach ($vcap as $serviceTypes) {
				foreach ($serviceTypes as $service) {
					if($service['label'] == 'cloudantNoSQLDB') {
						$credentials = $service['credentials'];
						$username = $credentials['username'];
						$password = $credentials['password'];
						$parsedUrl = parse_url($credentials['url']);
						$host = $parsedUrl['host'];
						$port = isset($parsedUrl['port']) ? $parsedUrl['port'] : $parsedUrl['scheme'] == 'http' ? '80' : '443';
						break;
					}
				}
			}
        }
        
		$this->sag = new \Sag($host, $port);
		$this->sag->useSSL(true);
		$dbsession = $this->sag->login($username, $password);
		try {
			$this->sag->setDatabase($dbname, true);
			$this->db_exists = true;
		} catch (Exception $e) {
			$this->db_exists = false;
		}
    }

	/**
	 * Gets all visitors from the DB.
	 */
	public function get($id) {
		$doc = $this->sag->get($id);
		$data = json_decode($doc->body);
		return $data;
	}

	/**
	 * Creates a new Visitor in the DB.
	 */
	public function post($data) {
        $resp = $this->sag->post(json_encode($data));
        $data['id'] = json_decode($resp->body)->id;
		return $data;
	}

	/**
	 * Updates a Visitor in the DB.
	 */
	public function put($id, $data) {
        $couchTodo = json_decode($this->sag->get($id)->body);

        // If data not found, then create it
        if(isset($couchTodo->error) && $couchTodo->error === 'not_found'){
            $data['_id'] = $id;
            return $this->post($data);
        }

        $couchData = array_merge((array) $couchTodo, $data);
        $this->sag->put($id, json_encode($couchData));

        // Re-formatting data
    	$couchData['id'] = $id;
    	unset($couchData['_id']);
    	unset($couchData['_rev']);
    	return $couchData;
	}

	/**
	 * Deletes a Visitor from the DB.
	 */
	public function delete($id) {
        $rev = json_decode($this->sag->get($id)->body)->_rev;
        return $this->sag->delete($id, $rev);
	}
}