<?php namespace EastBlue\Slave\Api;

use \Illuminate\Support\ServiceProvider;

class SlaveApiServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['slaveapi'] = $this->app->share(function($app)
        {
			return new SlaveApi;
		});
	}
}