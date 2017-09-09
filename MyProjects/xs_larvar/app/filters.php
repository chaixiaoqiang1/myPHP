<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	if (Config::get('app.model') == 'Slave' && !$request->is('slave/api/*')) {
		App::abort(403, 'Wrong Request URL');
		Log::info(var_export($request, true));	
	}
	if (Config::get('app.model') == 'Master') {
		$platform = Platform::find(Session::get('platform_id'));
		if ($platform) {
			$region = Region::find($platform->region_id);
			if ($region) {
				date_default_timezone_set($region->timezone);
			}
		}
		DB::disconnect('mysql');
	} else {
		$timezone = Config::get('app.timezone');
		if ($timezone) {
			date_default_timezone_set($timezone);
		}
	}
	// Here check the login time and last operation time 
	// if time now is 1 day after the login time, make the user logout
	// if time now is 2 hours after the last operation time, make the user logout
	if(Session::get(Auth::getName())){	//if we get the auth and session info
		$password = Session::get('password');
		$login_time = Session::get('login_time');
		$last_operation_time = Session::get('last_operation_time');
		if($password){
			if($password != Auth::user()->password){
				Auth::logout();
				Session::flush();
				return Redirect::to('/login');
			}
		}else{
			Session::set('password', Auth::user()->password);
		}

		if($login_time){
			if(time() - $login_time > 86400){
				Auth::logout();
				Session::flush();
				return Redirect::to('/login');
			}
		}else{
			Session::set('login_time', time());
		}

		if($last_operation_time){
			if(time() - $last_operation_time > 14400){
				Auth::logout();
				Session::flush();
				return Redirect::to('/login');
			}else{
				Session::set('last_operation_time', time());
			}
		}else{
			Session::set('last_operation_time', time());
		}

		try {
			App::setlocale(Auth::user()->language ? Auth::user()->language : 'cn');
		} catch (Exception $e) {
			
		}
	}

	$mysql = Config::get('database.connections.mysql');
	$diff_gmt = date('P');
	//Set Database Timezone
	$mysql['options'] = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '{$diff_gmt}'",	
	);
	Config::set('database.connections.mysql', $mysql);
});


App::after(function($request, $response)
{
	if (Config::get('app.model') == 'Slave') {
		return;
	}
	if (!$request->ajax()) {
		$response->headers->set('Content-type', 'text/html; charset=UTF-8');	
	}
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest() && Request::ajax()) {
		if(!Request::is('login')) {
			$msg = array(
				'code' => Config::get('errorcode.auth_guest'),
				'error' => Lang::get('error.auth_guest')
			);	
			return Response::json($msg);
		}
	} else if (Auth::guest() && !Request::is('login')) {
		return Redirect::guest('login');
	} else if (Auth::check() && auth::user()->is_closed) {
		return Redirect::to('logout');
	}

});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))//漏洞修复
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('permission', function() {

	//排除不参与权限管理的页面 
	$excludes = array('login', 'logout', '/', 'home/platforms','servers/test_store');

	if (in_array(Request::path(), $excludes)) {
		return;
	}
	
	if (Request::is('platforms/*') && !Request::is('platforms/*/edit') && !Request::is('platforms/create')) {
		return;
	}

	if (Request::is('games/*') && !Request::is('games/*/edit') && !Request::is('games/create')) {
		return;
	}

	//判断是否合法的功能App
	$segments = Request::segments();
	$len = count($segments);
	foreach ($segments as $k => $v) {
		if (is_numeric($v) && $len == 3) {
			$segments[$k] = '*';
		} else if (is_numeric($v) && $len == 2 && Request::isMethod('put')) {
			$segments[$k] = '*';
			$segments[] = 'edit';
		}
		if(is_numeric($v)){
		    $segments[$k] = '*';
		}
	}

	$path = implode('/', $segments);	

	$eastblueApp = EastblueApp::where('app_key', '=', $path)->first();

	if (!$eastblueApp && !Auth::user()->is_admin) {
		App::abort(404);
		exit;
	}

	if (Request::is('users/*')) {
		$segments = Request::segments();
		foreach($segments as $k => $v) {
			if (is_numeric($v)) {
				$user = User::find($v);
				if ($user && $user->user_id == Auth::user()->user_id) {
					return;
				}
			}
		}
	}

	$userPermissions = Auth::user()->permissions();
	if (!Auth::user()->is_admin) {
		
		if (empty($userPermissions)) {
			App::abort(403);
			break;
		};
		$eastblueApps = EastblueApp::whereIn('app_id', $userPermissions)->get();
		$isAllowed = false;
		foreach ($eastblueApps as $k => $v) {
			if ($path == $v->app_key) {
				$isAllowed = true;
				break;
			}
		}
		if (!$isAllowed) {
			App::abort(403);
			break;
		}
	}
});

Route::filter('platform', function() {

	if (Request::ajax()) {
		return;
	}

	$excludes  = array('login', 'logout', 'home/platforms', 'platforms/*');
	
	foreach ($excludes as $v) {
		if (Request::is($v)) {
			return;
		}
	}	
	
	$app_count = EastblueApp::count();
	$platform_count = Game::userGames()->select('platform_id')->distinct()->count();
	if (!Auth::user()->is_admin && ($app_count == 0 || $platform_count == 0)) {
		App::abort(403);
		exit;
	}
	if ($app_count == 0 && !Request::is('apps/create')) {
		return Redirect::to('apps/create');
	}

	if (!Session::get('platform_id') && $platform_count > 0) {
		return Redirect::to('home/platforms');
	}

	$game_count = Game::currentPlatform()->userGames()->count();

	if (!Auth::user()->is_admin && $game_count == 0) {
		App::abort(403);
		exit;
	}

	if (!Session::get('game_id') && $game_count > 0 && !Request::is('games/*')) {
		return Redirect::to('platforms/'.Session::get('platform_id'));
	}

});

Route::filter('games', function(){
	if (Request::is('logout')) {
		return;
	}
	if (Auth::check() && !Auth::user()->is_admin) {
		if (Session::get('game_id') && !in_array(Session::get('game_id'), Auth::user()->games())) {
			App::abort(403);
			exit;
		}
	}
});

Route::filter('slave_api_key', function() {
	$api_key = Input::get('api_key');
	$error_msg = array();
	if ($api_key != Config::get('app.api_key')) {
		$error_msg['code'] = Config::get('errorcode.slave_api_key');
		$error_msg['error'] = Lang::get('error.slave_api_key');
		return Response::json($error_msg, 401);
	}	
});

Route::filter('slave_api_sign', function() {
	$params = Input::all();
	$sign = '';
	if (isset($params['sign'])) {
		$sign = $params['sign'];
		unset($params['sign']);
	}
	$error_msg = array();
	$params['api_secret_key'] = Config::get('app.api_secret_key');
	uksort($params, 'strcmp');
	$newSign = md5(http_build_query($params));
	if ($sign != $newSign) {
		$error_msg['code'] = Config::get('errorcode.slave_api_sign');
		$error_msg['error'] = Lang::get('error.slave_api_sign');
		return Response::json($error_msg, 401);
	}
});
