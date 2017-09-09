<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function getIndex()
	{
		$platform = Platform::find(Session::get('platform_id'));
		$game = Game::find(Session::get('game_id'));
		
		$data = array(
			'content' => View::make('home', array(
				'platform' => $platform,
				'game' => $game
			))
		);
		return View::make('main', $data);
	}

	public function getLogin()
	{
		if (Auth::guest()) {
			return View::make('login');
		}
		return Redirect::to('/');
	}

	public function postLogin()
	{
		$params = array(
			'username' => Input::get('username'),
			'password' => Input::get('password'),
			'is_closed' => 0
		);

		$msg = array(
			'code'  => Config::get('errorcode.login'),
			'error' => Lang::get('error.login')
		);

		if (Auth::attempt($params)) {
			$this->userGames();
			$this->loginLog();
			return Response::json(Auth::user());
		} else {
			return Response::json($msg, 401);
		}
	}

	private function userGames()
	{
		Session::set('login_time', time());
		Session::set('last_operation_time', time());
		Session::set('password', Auth::user()->password);
		if (Auth::user()->is_admin) {
			return;
		}
		$game_ids = Auth::user()->games();
		if (count($game_ids) == 1) {
			$game = Game::find($game_ids[0]);
			if ($game) {
				Session::put('platform_id', $game->platform->platform_id);
				Session::put('game_id', $game->game_id); 
			}
		}
	}

	private function loginLog()
	{
		$log = new EastBlueLog;
		$log->log_key = 'login';
		$log->desc = Auth::user()->username . '于' . Carbon::now() . '登录，IP为:' .$_SERVER['REMOTE_ADDR'];
		$log->new_value = '浏览器信息：'.$_SERVER['HTTP_USER_AGENT'];
		$log->user_id = Auth::user()->user_id;
		$log->save();

		$login_ip = $_SERVER['REMOTE_ADDR'];
		$login_ips = explode(',', Auth::user()->login_ips);
		if(empty($login_ips) || (isset($login_ips[0]) && !$login_ips[0])){	//如果没有记录有登陆IP，那么将本条记录到里面
			$data2update = array(
				'login_ips' => $login_ip,
				'login_times' => Auth::user()->login_times+1,
			);
		}else{
			if(in_array($login_ip, $login_ips)){	//如果是一个已经记录过的IP登陆
				$data2update = array(
					'login_times' => Auth::user()->login_times+1,
				);	
			}else{	//发送报警邮件并新增IP到玩家的历史登陆IP中
				$this->sendMail($login_ip);
				$data2update = array(
					'login_ips' => Auth::user()->login_ips.','.$login_ip,
					'login_times' => Auth::user()->login_times+1,
				);		
			}	
		}
		User::where('user_id', Auth::user()->user_id)->update($data2update);
	}

	private function sendMail($login_ip){
		Log::info('User:'.Auth::user()->username.' has been logged on in an abnormal IP:'.$login_ip);
		$mail_text = 'Hi:'.Auth::user()->username.'. Your Eastblue-account has been logged on in an abnormal IP: '.$login_ip. '. Please check and confirm. Change your password if necessary';
		$mail_data = array(
			'msg' => $mail_text,
			);
		$to_email = array(Auth::user()->email);
       	Mail::send('Abnormallogin', $mail_data, function($message) use ($to_email)
        {
            $message->subject('Eastblue-Abnormal-login');
            $message->from('cs@game168.com.tw', 'Eastblue');
            $message->to($to_email);
        });
	}

	public function getLogout()
	{
		$this->logoutLog();
		Auth::logout();
		Session::flush();
		return Redirect::to('/login');
	}

	private function logoutLog()
	{
		$log = new EastBlueLog;
		$log->log_key = 'logout';
		$log->desc = Auth::user()->username . '于' . Carbon::now() . '登出';
		$log->user_id = Auth::user()->user_id;
		$log->save();
	}

	public function showPlatforms()
	{
		if (Platform::count() == 0) {
			return Redirect::to('/');
		}

		$platforms = array();
		if (Auth::user()->is_admin) {
			$platforms = Platform::all()->toArray();
		} else {
			$games = Game::userGames()->select('platform_id')->distinct()->get();
			foreach ($games as $k => $v) {
				$platform = Platform::find($v->platform_id);
				if ($platform) {
					array_push($platforms, $platform->toArray());
				}
			}
		}
		return View::make('platforms', array(
			'platforms' => $platforms
		));
	}
	
	public function getPHPInfo()
	{
		if (Config::get('app.debug') == true || Auth::user()->is_admin) {
			phpinfo();
		}
	}

}