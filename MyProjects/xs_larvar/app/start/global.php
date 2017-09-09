<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

$log_file = storage_path().'/logs/eb-' . date('Y-m-d') . '.log';
//设置权限
if (!is_file($log_file)) {
	touch($log_file);
	chmod($log_file, 0777);
}
Log::useFiles($log_file);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	if($code != 403){	//权限问题可以正常捕获，不打印日志
		Log::error($exception);
	}else{
		Log::info('User-----'.var_export(Auth::user()->username, true).' is trying to use not allowed function----- '.var_export(Request::url(), true));
	}

	if (Config::get('app.model') == 'Slave') {
		$params = array(
			'code' => $code,
			'error' => Lang::get('error.system_error'),
		);

		if (Config::get('app.debug') == true) {
			$params['file'] = $exception->getFile();
			$params['line'] = $exception->getLine();
			$params['error'] = $exception->getMessage();
		}
		return Response::json($params, $code);
	}

	if (Request::ajax()) {
		$params = array(
			'code' => $code,
			'error' => Lang::get('error.system_error'),
		);
		if (Config::get('app.debug') == true) {
			$params['error'] = $exception->getMessage();
		}
		if($code == 403){
			$params = array(
				'code' => $code,
				'error' => '您没有此功能的权限',
			);
		}
		return Response::json($params, $code);
	} else if ($code == 403) {
		return Response::view('error403', array(), 403);
	}
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

App::missing(function($exception) {
	if (Request::ajax() || Config::get('app.model') == 'Slave') {
		$params = array(
			'code' => 404,
			'error' => $exception->getMessage(),
		);
		return Response::json($params, 404);
	} else {
		return Response::view('error404', array(), 404);
	}
});

View::composer('main', function($view) {

	$groups = Group::all();
	foreach ($groups as $k => $v) {
		$app_ids = explode(',', $v->apps); 
		if (Auth::user()->is_admin) {
			$ids = $app_ids;
		} else {
			$permissions = Auth::user()->permissions();
			$ids = array_intersect($app_ids, $permissions);
			if (empty($ids)) {
				continue;
			}
		}
		//app 分游戏
		$game = Game::find(Session::get('game_id'));
		$game_code_id = GameCode::where('game_code', '=', $game->game_code)->pluck('code_id');
		$filter_ids = array();
		foreach($ids as $id){
			$app = EastblueApp::find($id);
			if($app){
			    $is_old_game = false;
			    if($game_code_id == 1 || $game_code_id == 2){
			        $is_old_game = true;
			    }
			    if($is_old_game){//三国和女神许多接口共用
			        // app的game_code_id为0表示是系统功能，所有游戏共用。
			        // app的game_code_id为101表示三国和女神共用游戏API的功能。
			        // 103代表页游
			        // 104代表手游
			        if($app->game_code_id == $game_code_id || $app->game_code_id == 0 || $app->game_code_id == 101 || $app->game_code_id == 103){
			            $filter_ids[] = $id;
			        }
			    } else {
			    	if(1 == $game->game_type){	//页游
						if($app->game_code_id == $game_code_id || $app->game_code_id == 0 || $app->game_code_id == 103){
				            $filter_ids[] = $id;
				        }
			    	}elseif(2 == $game->game_type){	//手游
			    		if($app->game_code_id == $game_code_id || $app->game_code_id == 0 || $app->game_code_id == 104){
				            $filter_ids[] = $id;
				        }
			    	}
			    }
			    $is_our_game = true;
			    if(in_array($game_code_id, array(4, 7, 8, 10, 12, 14, 16, 17))){ //目前4-神仙道不是我们的游戏
			        $is_our_game = false;
			    }
			    if($is_our_game){
			        // app的game_code_id为102表示我们的游戏的共用的功能。
			        if($app->game_code_id == 102){
			            $filter_ids[] = $id;
			        }
			    } 
			}
		}
		if($filter_ids){
		    $groups[$k]->child = EastblueApp::whereIn('app_id', $filter_ids)->get();
		} else {
			continue;
		}
	}
	$gid = Input::get('gid') ? Input::get('gid') : Session::get('gid');

	$data = array(
		'groups' => $groups,
		'gid' => $gid
	);

	$view->with('sidebar', View::make('subview.sidebar', $data));

	Session::put('gid', $gid);	

	$excludes = array('login', 'logout');

	$app_name = '';
	if (Request::is('/')) {	
		$app_name = Lang::get('basic.site_name');
	} else if (!in_array(Request::path(), $excludes)) {
		$segments = Request::segments();
		foreach ($segments as $k => $v) {
			if (is_numeric($v)) {
				$segments[$k] = '*';
			}
		}
		$path = implode('/', $segments);	
		$eastblueApp = EastblueApp::where('app_key', '=', $path)->first();
		if ($eastblueApp) {
			$app_name = $eastblueApp->app_name;
		}
	}

	if (Request::is('apps/create') && !$app_name) {
		$app_name = Lang::get('basic.app_create');	
	}
	$view->with('app_name', $app_name);
	
	$platform = Platform::find(Session::get('platform_id'));
	$game = Game::find(Session::get('game_id'));

	$view->with('platform', $platform);
	$view->with('game', $game);
});

require app_path().'/filters.php';