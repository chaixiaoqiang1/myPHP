<?php namespace EastBlue\GameServer\Dld;

use \Illuminate\Support\ServiceProvider;

class DldGameServerApiServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['dldgameserverapi'] = $this->app->share(function($app)
        {
			return new DldGameServerApi;
		});
	}
}