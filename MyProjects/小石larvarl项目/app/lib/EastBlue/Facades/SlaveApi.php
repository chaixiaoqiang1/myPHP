<?php namespace EastBlue\Facades;

use \Illuminate\Support\Facades\Facade;

class SlaveApi extends Facade {

	protected static function getFacadeAccessor() {
		return 'slaveapi'; 
	}
}