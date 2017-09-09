<?php namespace EastBlue\GameServer\SXD;

use \Illuminate\Support\ServiceProvider;

class SXDGameServerApiServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['sxdgameserverapi'] = $this->app->share(function($app)
        {
			return new SXDGameServerApi;
		});
	}
}