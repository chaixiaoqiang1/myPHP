<?php

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
			'content' => View::make('users.index')
		);
		return View::make('main', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$data = array(
			'content' => View::make('users.create')
		);
		return View::make('main', $data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
			'username'              => 'required',
			'email'                 => 'required|email',
			'password'              => 'required|confirmed',
			'password_confirmation' => 'required',
			'department_id' 		=> 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		$department_id = Input::get('department_id');
		
		if('0' == $department_id){
			return Response::json(array('error'=> Lang::get('basic.no_department_id')), 403);
		}

		$msg = array(
			'code' => Config::get('errorcode.user_add'),
			'error'=> Lang::get('error.user_add')
		);
		if ($validator->fails()) {
			return Response::json($msg);
		} else {
			$user = new User;
			$user->username = trim(Input::get('username'));
			$user->nickname = trim(Input::get('nickname'));
			$user->email = trim(Input::get('email'));
			$user->password = Hash::make(trim(Input::get('password')));
			$user->is_admin = (int)Input::get('is_admin');
			$user->created_ip = Request::getClientIp();
			$user->last_login_ip = Request::getClientIp();
			$user->department_id = (int)Input::get('department_id');
			$user->organization_id = Auth::user()->organization_id;
			if ($user->save()) {
				return Response::json(array('msg' => Lang::get('basic.create_success')));
			} else {
				return Response::json($msg);
			}
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$data = array(
			'content' => View::make('users.create')
		);
		return View::make('main', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$user = User::find($id);
		$users = User::where('is_admin', 0)->get();	//不可复制管理员的权限
		if (!$user) {
			App::abort(404);
			exit;
		}
		$departments = Department::all()->toArray();
		foreach ($departments as $k => &$v) {
			$apps = EastblueApp::where('department_id', $v['department_id'])->orderBy('app_key')->get()->toArray();
			if (empty($apps)) {
				unset($departments[$k]);
				continue;
			}
			$app_ids = array();
			foreach ($apps as $vv) {
				$app_ids[] = $vv['app_id'];
			}
			$v['apps'] = $apps;
			$v['app_ids'] = implode(',', $app_ids);

		}
		$data = array(
			'app_name' => Lang::get('user.user_edit'),
			'app_desc' => '',
			'content' => View::make('users.edit', array(
				'user' => $user,
				'users' => $users,
				'apps' => $departments
			))
		);
		return View::make('main', $data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$user = User::find($id);
		
		$msg = array(
			'code' => Config::get('errorcode.user_edit'),
			'error'=> Lang::get('error.user_edit')
		);

		if (!$user) {
			return Response::json($msg, 403);
			exit;
		}

		if (!(Auth::user()->is_admin || Auth::user()->user_id == $user->user_id)) {
			$msg['code'] = Config::get('errorcode.permission_forbidden');
			$msg['error'] = Lang::get('error.permission_forbidden');
			return Response::json($msg, 403);
			exit;
		}

		switch(Input::get('type')) {
			case 'pwd':
				return $this->editPassword($user, $msg);
				break;
			case 'profile':
				return $this->editProfile($user, $msg);
				break;
			case 'permission':
				return $this->editPermission($user, $msg);
				break;
			case 'games':
				return $this->editGames($user, $msg);
				break;
		}
	}

	private function editPassword($user, $msg)
	{
		$rules = array(
			'password'    => 'required|confirmed',
			'password_confirmation' => 'required',
		);
		if (Input::get('old_password')) {
			$rules['old_password'] = 'required';
		}
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			if (Input::get('old_password') && 
				$user->user_id == Auth::user()->user_id && 
				!Hash::check(Input::get('old_password'), $user->password)) {
				return Response::json($msg, 401);
			}
			$password = Input::get('password');
			if(!(preg_match('/[a-z]+/', $password) && preg_match('/[0-9]+/', $password) && preg_match('/[A-Z]+/', $password))){
				return Response::json(array('error' => Lang::get('basic.stand_pwd')), 401);
			}
			$user->password = Hash::make(Input::get('password'));
			if ($user->save()) {
				$log = new EastBlueLog;
				$log->log_key = 'edit_user_password';
				$log->desc = Auth::user()->username . '于' . Carbon::now() . '修改'.$user->username.'密码';
				$log->user_id = Auth::user()->user_id;
				$log->save();
				return Response::json(array('msg' => Lang::get('basic.edit_success')));
			} else {
				return Response::json($msg, 500);
			}
		}
	}

	private function editProfile($user, $msg)
	{
		$rules = array(
			'email'    => 'required|email'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$user->email = trim(Input::get('email'));
			$user->department_id = (int)Input::get('department_id');
			if (Auth::user()->is_admin) {
				$user->is_admin = (int)Input::get('is_admin');
			}
			if ($user->save()) {
				return Response::json(array('msg' => Lang::get('basic.edit_success')));
			} else {
				return Response::json($msg, 500);
			}
		}
	}

	private function editPermission($user, $msg)
	{
		$permissions = Input::get('permissions');
		$arr = array();
		foreach($permissions as $k => $v) {
			if ($v == 0) {
				continue;
			}
			$eastblueApp = EastblueApp::find((int)$v);
			if (!$eastblueApp) {
				return Response::json($msg, 500);
			}
			$arr[] = $v;
		}
		$user->permissions = implode(',', $arr);
		if ($user->save()) {
			return Response::json(array('msg' => Lang::get('basic.edit_success')));
		} else {
			return Response::json($msg, 500);
		}
	}

	private function editGames($user, $msg) 
	{
		$games = Input::get('games');
		$arr = array();
		foreach($games as $k => $v) {
			if ($v == 0) {
				continue;
			}
			$game = Game::find((int)$v);
			if (!$game) {
				return Response::json($msg, 500);
			}
			$arr[] = $v;
		}
		$user->games = implode(',', $arr);
		if ($user->save()) {
			return Response::json(array('msg' => Lang::get('basic.edit_success')));
		} else {
			return Response::json($msg, 500);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function close()
	{
	    $msg = array(
	        'code' => Config::get('errorcode.user_edit'),
	        'error' => Lang::get('error.user_edit')
	    );
	     
	    if (! (Auth::user()->is_admin || Auth::user()->user_id == $user->user_id)) {
	        $msg['code'] = Config::get('errorcode.permission_forbidden');
	        $msg['error'] = Lang::get('error.permission_forbidden');
	        return Response::json($msg, 403);
	        exit();
	    }
	    $user_id = Input::get('id');
	    $user = User::find($user_id);
	    
	    if (! $user) {
	        return Response::json($msg, 403);
	        exit();
	    }
	     
	    $user->is_closed = 1;
	     
	    $user->save();
	     
	    return Redirect::to("/users");
	}

	public function ChangeLanguage(){
        $data = array(
            'content' => View::make('users.changelanguage')
        );
        return View::make('main', $data);
	}

	public function ChangeLanguageChange(){
		$language = Input::get('language');
		if($language){
			$result = User::where('user_id', Auth::user()->user_id)->update(array('language' => $language));
			if($result){
				return Response::json(array('msg'=> Lang::get('basic.change_success')));
			}else{
				return Response::json(array('error'=> Lang::get('basic.change_unsuccess')), 404);
			}
		}else{
			return Response::json(array('error'=> Lang::get('basic.change_unsuccess')), 404);
		}
	}

	public function copypermission(){
		$to_user_id = (int)Input::get('to_user_id');
		$from_user_id = (int)Input::get('from_user_id');
		if(!($to_user_id*$from_user_id)){
			return Response::json(array('error'=> Lang::get('basic.input_error')), 404);
		}

		$from_user = User::find($from_user_id);
		$to_user = User::find($to_user_id);

		$data2update = array(
			'permissions' => $from_user->permissions,
			'games' => $from_user->games,
			);
		User::where('user_id', $to_user_id)->update($data2update);

		$log = new EastBlueLog;
		$log->log_key = 'copy_user_permissions';
		$log->desc = Auth::user()->username . '于' . date('Y-m-d H:i:s', time()) . '拷贝'.$from_user->username.'的权限给'.$to_user->username;
		$log->new_value = json_encode($data2update);
		$log->user_id = Auth::user()->user_id;
		$log->save();

		return Response::json(array('msg'=> Lang::get('basic.success')));
	}
}