<?php

class AppController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{	
		$type = Input::get('type');
		$keywords = trim(Input::get('search'));
		if (empty($keywords)) {
			$pg = EastblueApp::paginate(50);
			$data = array(
		    'content' => View::make('apps.index',array('pg' => $pg,'type'=>$type)),
			);
		}else{
			$type = Input::get('type');
			//主要用来检测是否是规则内的Type值
			$is_type = array(0,1,2);
			if (!in_array($type, $is_type)) {
				return $this->show_message('404', '不合法的请求!');
			}
			if ($type == 0 && !empty($keywords)) {
				return $this->show_message('404', '请选择一种检索方式!');
			}elseif ($type == 1 && !empty($keywords)) {
				$re = EastblueApp::where('app_name','like','%'.$keywords.'%')->paginate(10);
			}elseif ($type == 2 && !empty($keywords)) {
				$re = EastblueApp::where('description','like','%'.$keywords.'%')->paginate(10);
			}	
			
			$data = array(
			    'content' => View::make('apps.index',array('re' => $re,'keywords'=>$keywords,'type'=>$type)),
			);
		}
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
			'content' => View::make('apps.create')
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
			'app_name' => 'required',
			'app_key'  => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		$msg = array(
			'code' => Config::get('errorcode.app_add'),
			'error'=> Lang::get('error.app_add')
		);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$app = new EastblueApp;
			$app->app_name = trim(Input::get('app_name'));
			$app->app_key = trim(Input::get('app_key'));
			$app->department_id = (int)Input::get('department_id');
			$app->game_code_id = (int)Input::get('game_code');
			$app->description = trim(Input::get('description'));
			if ($app->save()) {
				return Response::json(array('msg' => Lang::get('basic.create_success')));
			} else {
				return Response::json($msg, 500);
			}
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	
	//系统功能模块搜索功能
	public function show($id)
	{	
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$app = EastblueApp::find($id);
		if (!$app) {
			return $this->show_message('404', 'No Such App!');
		}
		$data = array(
		    'content' => View::make('apps.edit',array('app' => $app)),
		);
		return View::make('main',$data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$app = EastblueApp::find($id);
		$msg = array(
		    'code' => Config::get('errorcode.app_edit'),
		    'error' => Lang::get('error.app_edit')
		);
		if (!$app) {
			return Response::json($msg,404);
		}
		switch(Input::get('type')) {
			case 'name':
				return $this->editName($app, $msg);
				break;
			case 'app':
				return $this->editApp($app, $msg);
				break;
		}
	}
	private function editName($app,$msg)
	{
		$rules = array(
		    'app_name' => 'required',
		    'app_key'  => 'required'
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Resonse::json($msg, 403);
		} else {
			$app->app_name = trim(Input::get('app_name'));
			$app->app_key = trim(Input::get('app_key'));
			$app->department_id = (int)Input::get('department_id');
			$app->game_code_id = (int)Input::get('game_code');
			$app->description = trim(Input::get('description'));
			if ($app->save()) {
				return Response::json(array('msg' => Lang::get('basic.edit_success')));
			} else {
				return Response::json($msg, 500);
			}
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

}