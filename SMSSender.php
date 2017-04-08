<?php

namespace katanyoo\smssender;

/**
 * This is just an example.
 */
class SMSSender
{
	// $proxy = 'localhost:7777';
	// $proxy_userpwd = 'username:password';
	public $proxy = '';
	public $proxy_userpwd = '';
	public $host;
	public $username;
	public $password;
	public $mobile_no;
	public $message;
	public $provider_endpoint;

	public function setMobileNo($number) {
		$this->mobile_no = $number;
		return $this;
	}

	public function setMessage($message) {
		$this->message = $message;
		return $this;
	}

	public function send() {
		return $this->sendMessage();
	}

	public function sendMessage($schedule = '', $category = '', $sender_name = '', $proxy = '', $proxy_userpwd = '') {
		$option = '';
		if ($category == '') {
			$category = 'General';
		}
		$option = "SEND_TYPE=$category";
		if ($sender_name != '') {
			$option .= ",SENDER=$sender_name";
		}

		$params = array(
			'ACCOUNT' => $this->username,
			'PASSWORD' => $this->password,
			'MOBILE' => $this->mobile_no,
			'MESSAGE' => iconv("utf-8", "tis-620", $this->message)
		);
		if ($schedule) {
			$params['SCHEDULE']=$schedule;
		}
		if ($option) {
			$params['OPTION']=$option;
		}
		
		$curl_options = array(
			CURLOPT_URL => $this->provider_endpoint,
			CURLOPT_PORT => 80,
			CURLOPT_POST => true,			
			CURLOPT_POSTFIELDS => http_build_query($params),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false
		);
  		if ($proxy != '') {
			$curl_options[CURLOPT_PROXY] = $proxy;
			if ($proxy_userpwd != '') {
				$curl_options[CURLOPT_PROXYUSERPWD] = $proxy_userpwd;   
			}
		}
		
		$ch = curl_init();
		curl_setopt_array($ch, $curl_options);
		$response = curl_exec($ch);
		$error_msg = '';
		if($response === false) {
			$error_msg = curl_error($ch);
		}
		curl_close($ch);
		
		if($response === false) {
			return array('result'=> false, 'error'=>$error_msg);
		} else {
			//STATUS=0 MESSAGE_ID=19293998 TASK_ID=1237348 END=OK
			$results = explode("\n", trim($response));
			$index = count($results) - 1;
			if (trim($results[$index]) == 'END=OK') {
				$results[0] = trim($results[0]);
				if ($results[0] == 'STATUS=0') {					
					$m = explode("=", $results[1]);
					$t = explode("=", $results[2]);
					return array('result'=> true, 'message_id'=>$m[1], 'task_id'=>$t[1]);
				} else {
					return array('result'=>false, 'error'=>$results[0]);
				}
			} else {
				return array('result'=>false, 'error'=>"Incorrect Response: $response");
			}
		}
	}
}
