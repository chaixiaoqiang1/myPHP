<?php namespace EastBlue\Table;

use \Illuminate\Support\ServiceProvider;

class TableServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->registerApi();
	}

	protected function registerApi()
	{
        $this->app['table'] = $this->app->share(function($app)
        {
			return new Table;
		});
	}
}