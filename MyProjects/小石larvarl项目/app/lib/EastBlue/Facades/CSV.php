<?php namespace EastBlue\Facades;

use \Illuminate\Support\Facades\Facade;

class CSV extends Facade {

	protected static function getFacadeAccessor() {
		return 'CSV'; 
	}
}