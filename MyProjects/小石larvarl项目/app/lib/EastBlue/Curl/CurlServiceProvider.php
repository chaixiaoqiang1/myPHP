<?php namespace EastBlue\Curl;

use \Illuminate\Support\ServiceProvider;

class CurlServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['curl'] = $this->app->share(function($app)
        {
			return new Curl;
		});
	}
}