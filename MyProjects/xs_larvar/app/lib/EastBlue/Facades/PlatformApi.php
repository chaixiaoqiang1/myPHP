<?php namespace EastBlue\Facades;

use \Illuminate\Support\Facades\Facade;

class PlatformApi extends Facade {

	protected static function getFacadeAccessor() {
		return 'platformapi'; 
	}
}