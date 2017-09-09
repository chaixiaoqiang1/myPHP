<?php namespace EastBlue\GameServer\YYSG;

use \Illuminate\Support\ServiceProvider;

class YYSGGameServerApiServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['yysggameserverapi'] = $this->app->share(function($app)
        {
			return new YYSGGameServerApi;
		});
	}
}