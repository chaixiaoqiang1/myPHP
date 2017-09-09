<?php namespace EastBlue\Facades;

use \Illuminate\Support\Facades\Facade;

class DldGameServerApi extends Facade {

	protected static function getFacadeAccessor() {
		return 'dldgameserverapi'; 
	}
}