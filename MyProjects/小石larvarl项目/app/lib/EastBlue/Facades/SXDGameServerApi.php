<?php namespace EastBlue\Facades;

use \Illuminate\Support\Facades\Facade;

class SXDGameServerApi extends Facade {

	protected static function getFacadeAccessor() {
		return 'sxdgameserverapi'; 
	}
}