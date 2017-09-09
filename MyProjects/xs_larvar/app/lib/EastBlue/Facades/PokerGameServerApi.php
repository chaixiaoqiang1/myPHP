<?php namespace EastBlue\Facades;

use \Illuminate\Support\Facades\Facade;

class PokerGameServerApi extends Facade {

	protected static function getFacadeAccessor() {
		return 'pokergameserverapi'; 
	}
}