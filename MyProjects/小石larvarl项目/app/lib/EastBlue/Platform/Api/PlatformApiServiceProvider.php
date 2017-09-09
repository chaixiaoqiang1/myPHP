<?php namespace EastBlue\Platform\Api;

use \Illuminate\Support\ServiceProvider;

class PlatformApiServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['platformapi'] = $this->app->share(function($app)
        {
			return new PlatformApi;
		});
	}
}