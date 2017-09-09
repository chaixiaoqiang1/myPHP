<?php namespace EastBlue\Curl;

interface CurlInterface {

	public function url($url);

	public function postFields($data = array());

	public function post();

	public function toArray();

}