<?php
class CoinHiveAPI {
	const API_URL = 'https://api.coinhive.com';
	private $secret = null;
	public function __construct($secret) {
		if (strlen($secret) !== 32) {
			throw new Exception('CoinHive - Invalid Secret');
		}
		$this->secret = $secret;
	}
  
	function get($path, $data = []) {
		$data['secret'] = $this->secret;
		$url = self::API_URL.$path.'?'.http_build_query($data);
		$response = file_get_contents($url);
		return json_decode($response,true);
	}
	
	function post($path, $data = []) {
		$data['secret'] = $this->secret;
		$context = stream_context_create([
			'http' => [
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			]
		]);
		$url = SELF::API_URL.$path;
		$response = file_get_contents($url, false, $context);
		return json_decode($response,true);
	}	
}