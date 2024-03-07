<?php
	class vsHomebridge {
		private $token;
		private $token_expires = 0;
		private $api_host;
		private $api_port = 8581;
		public $timeout = 120;

		public function __construct($homebridgeIP){
			$this->api_host = $homebridgeIP;
			if(strpos($this->api_host, 'http') === false){
				$this->api_host = ('http://'. $this->api_host);
			}
		}

		private function callAPI($request, $payload=false, $method='POST'){
			$headers = [];
			if(is_array($payload) && array_key_exists('file', $payload)) $headers[] = 'Content-Type: multipart/form-data';
			else $headers[] = 'Content-Type: application/json';

			if(isset($this->token) && $this->token != '') $headers[] = 'Authorization: Bearer '. $this->token;

			$ch = curl_init($this->api_host .':'. $this->api_port .'/'. $request);
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

			if($payload && is_array($payload)){
				if(array_key_exists('file', $payload)) curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
				else curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($ch);
			if(curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
					$response = json_encode(['status'=>'error', 'msg'=>'timeout', 'full_msg'=>curl_error($ch)]);
			}
			curl_close($ch);

			return json_decode($response, true);
		}

		private function getAuth($user='admin', $pass='admin', $force=false){
			$url = 'api/auth/login';

			if($this->token_expires > time()) return true;
			else {
				$data = $this->callAPI($url, ['username'=>$user, 'password'=>$pass, 'opt'=>'string']);

				if(isset($data) && isset($data['access_token'])){
					$this->token_expires = (time() + $data['expires_in']);
					$this->token = $data['access_token'];
					return true;
				}
			}

			return false;
		}
		// Public Functions
		public function login($user='admin', $pass='admin', $force=false){
			return $this->getAuth($user, $pass, $force);
		}
		// Get list of all accessories
		public function getAccessories(){
			return $this->callAPI('api/accessories', NULL, 'GET');
		}
		public function getSIMAccessories(){
			$results = $this->callAPI('api/accessories', NULL, 'GET');
			
			$payload = [];
			foreach($results as $v){
				$item = [];
				$item['uid'] = $v['uniqueId'];
				$item['name'] = $v['accessoryInformation']['Name'];
				$item['type'] = $v['humanType'];//$v['serviceCharacteristics'][0]['serviceType'];
				$item['value'] = $v['serviceCharacteristics'][0]['value'];
				$item['format'] = $v['serviceCharacteristics'][0]['format'];
				$item['desc'] = $v['serviceCharacteristics'][0]['description'];
				if(isset($v['serviceCharacteristics'][0]['unit'])) $item['unit'] = $v['serviceCharacteristics'][0]['unit'];
				if(isset($v['serviceCharacteristics'][0]['minValue'])) $item['min'] = $v['serviceCharacteristics'][0]['minValue'];
				if(isset($v['serviceCharacteristics'][0]['maxValue'])) $item['max'] = $v['serviceCharacteristics'][0]['maxValue'];

				$payload[] = $item;
			}
			
			return $payload;
		}
		// Get single accessory
		public function getAccessory($uid, $raw=false){
			if($raw) return $this->callAPI('api/accessories/'. $uid, NULL, 'GET');
			else {
				$results = $this->callAPI('api/accessories/'. $uid, NULL, 'GET');

				$payload = array_merge($results['values'], $results['accessoryInformation']);
				$payload['type'] = $results['type'];
				return $payload;
			}
		}
	}
?>
