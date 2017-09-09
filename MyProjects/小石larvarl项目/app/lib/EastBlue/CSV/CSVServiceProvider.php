<?php namespace EastBlue\CSV;

use \Illuminate\Support\ServiceProvider;

class CSVServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['CSV'] = $this->app->share(function($app)
        {
			return new CSV;
		});
	}
}