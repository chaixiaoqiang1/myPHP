<?php namespace EastBlue\GameServer\Flsg_nszj;

use \Illuminate\Support\ServiceProvider;

class GameServerApiServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['gameserverapi'] = $this->app->share(function($app)
        {
			return new GameServerApi;
		});
	}
}