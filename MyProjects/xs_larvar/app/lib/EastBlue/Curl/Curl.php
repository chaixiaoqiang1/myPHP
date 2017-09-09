<?php namespace EastBlue\Curl;

use \Log;
use \App;

class Curl implements CurlInterface{

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';

	const ACCEPT_TYPE = 'Accept: application/json';

	protected $request = '';
	private $url = '';
	private $fields = array();

	public function __construct()
	{

	}

	private function init()
	{
		$this->request = curl_init();
		$this->defaultOptions();
	}

	public function url($url)
	{
		$url_info = parse_url($url);
		$this->url = $url;
		$this->init();
		curl_setopt($this->request, CURLOPT_URL, $url);
		if (isset($url_info['scheme']) && $url_info['scheme'] == 'https') {
			curl_setopt($this->request, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->request, CURLOPT_SSL_VERIFYHOST, 2);
		}
		return $this;
	}

	private function setMethod($method)
	{
		$method = strtoupper($method);
		switch ($method) {
			case self::METHOD_GET:
				curl_setopt($this->request, CURLOPT_HTTPGET, true);
				break;
			case self::METHOD_POST:
				curl_setopt($this->request, CURLOPT_POST, true);
				break;
			case self::METHOD_PUT:
				curl_setopt($this->request, CURLOPT_PUT, true);
				break;
			case self::METHOD_DELETE:
				curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, METHOD_DELETE);
				break;
			default:
				curl_setopt($this->request, CURLOPT_HTTPGET, true);
				break;
		}
		return $this;
	}

	private function defaultOptions()
	{
		$options = array(
			CURLOPT_TIMEOUT        => 3000000,
			CURLOPT_FAILONERROR    => false,
			CURLOPT_HEADER         => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTP200ALIASES => array(400, 401, 403, 500, 404)
		);
		curl_setopt_array($this->request, $options);
	}

	public function setOptions($options = array())
	{
		curl_setopt_array($this->request, $options);
		return $this;
	}

	public function postFields($fields = array(), $is_query = true)
	{
		$this->fields = $fields;
		if ($is_query) {
			$fields = http_build_query($fields);
		}
		curl_setopt($this->request, CURLOPT_POSTFIELDS, $fields);
		//httpcode 417 bug content length > 1KB
		curl_setopt($this->request, CURLOPT_HTTPHEADER , array('Expect:'));
		return $this;
	}

	private function getRespose()
	{
		header("Content-Type: text/html; charset=utf-8");
		$url_info = parse_url($this->url);
		if (!isset($url_info['scheme'])) {
			return (object)array(
				'http_code' => 404,
				'body' => (object)array(
					'code' => 404,
					'error' => 'Not Found'
				)
			);
		}
		$response = curl_exec($this->request);
		// var_export($response);

		$errno = curl_errno($this->request);
		$error = curl_error($this->request);
		$http_code = curl_getinfo($this->request, CURLINFO_HTTP_CODE);
		curl_close($this->request);	
		
		$debug_info = 'url:' . $this->url;

		if ($this->fields) {
			$debug_info .= ' postFields:' . json_encode($this->fields);
			
		}
		// var_dump($debug_info);
		try {

			if (strpos($response, 'XServerFormationBackup')) {
				$pos1 = strpos($response, 'XServerFormationBackup');
				$aa1 = substr($response, 0, $pos1);
				$pos2 = strpos($response, 'XServerOperatorID');
				$aa2 = substr($response, $pos2);
				$response = $aa1 . $aa2;
				$body = json_decode($response);
			}else{
				$body = json_decode($response);
			} 

		} catch(\Exception $e) {
			$body = (object)array(
				'code' => 500,
				'error' => ''	
			);
			Log::error('Curl JSON_DECODE Error' . $response . ' '. $debug_info);
		}
		$http_code = $http_code == 0 ? 500 : $http_code;
		if ($errno || $http_code == 404) {
			if($http_code != 404){
				Log::error('Curl Error' . $debug_info . 'errono=' . $errno . '&error=' . $error . '&http_code=' . $http_code);
			}
			if (!$body) {
				$body = (object)array(
					'code' => $errno,
					'error' => $error
				);
			}
			return (object)array(
				'http_code' => $http_code,
				'body' => $body
			);
		} else {
			//Log::error((array)$body);
			return (object)array(
				'http_code' => $http_code,
				'body' => $body
			);
		}
	}

	public function post()
	{
		$this->setMethod(self::METHOD_POST);
		return $this->getRespose();
	}

	public function get()
	{
		$this->setMethod(self::METHOD_GET);
		return $this->getRespose();
	}

	public function toArray()
	{
		
	}

}