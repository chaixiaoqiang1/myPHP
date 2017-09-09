<?php namespace EastBlue\GameServer\Poker;

use \Illuminate\Support\ServiceProvider;

class PokerGameServerApiServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['pokergameserverapi'] = $this->app->share(function($app)
        {
			return new PokerGameServerApi;
		});
	}
}