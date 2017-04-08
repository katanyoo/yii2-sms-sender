<?php

namespace katanyoo\smssender;

/**
 * This is just an example.
 */
class SMSSender
{
	// $proxy = 'localhost:7777';
	// $proxy_userpwd = 'username:password';
	public $account = '';
	public $password = '';
	public $mobile_no = '';
	public $message = '';

	public function showParams()
	{
		echo $account, PHP_EOL, $password, PHP_EOL,  $mobile_no, PHP_EOL, $message, PHP_EOL;
	}

	public static function sendMessage($account, $password, $mobile_no, $message, $schedule = '', $category = '', $sender_name = '', $proxy = '', $proxy_userpwd = '') {
		$option = '';
		if ($category == '') {
			$category = 'General';
		}
		$option = "SEND_TYPE=$category";
		if ($sender_name != '') {
			$option .= ",SENDER=$sender_name";
		}

		$params = array(
			'ACCOUNT' => $account,
			'PASSWORD' => $password,
			'MOBILE' => $mobile_no,
			'MESSAGE' => $message
		);
		if ($schedule) {
			$params['SCHEDULE']=$schedule;
		}
		if ($option) {
			$params['OPTION']=$option;
		}
		
		$curl_options = array(
			CURLOPT_URL => 'http://smartcomm2.net/sc2otp/SendMessage',
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
