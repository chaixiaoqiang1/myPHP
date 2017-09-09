<?php

class AppGroupController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
			'content' => View::make('groups.index')
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
			'content' => View::make('groups.create')
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
			'group_name' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		$msg = array(
			'code' => Config::get('errorcode.group_add'),
			'error'=> Lang::get('error.group_add')
		);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$group = new Group;
			$group->group_name = trim(Input::get('group_name'));
			if ($group->save()) {
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
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$group = Group::find($id);

		if (!$group) {
			App::abort(404);
			exit;
		}

		$data = array(
			'content' => View::make('groups.edit', array('group' => $group))
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
		$group = Group::find($id);	
		$msg = array(
			'code' => Config::get('errorcode.group_edit'),
			'error' => Lang::get('error.group_edit')
		);
		if (!$group) {
			return Response::json($msg, 404);
		}
		switch(Input::get('type')) {
			case 'name':
				return $this->editName($group, $msg);
				break;
			case 'app':
				return $this->editApp($group, $msg);
				break;
		}
	}

	private function editName($group, $msg)
	{
		$rules = array(
			'group_name' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$group->group_name = trim(Input::get('group_name'));
			if ($group->save()) {
				return Response::json(array('msg' => Lang::get('basic.edit_success')));
			} else {
				return Response::json($msg, 500);
			}
		}
	}

	private function editApp($group, $msg)
	{
		$apps = Input::get('apps');
		$arr = array();
		foreach($apps as $k => $v) {
			if ($v == 0) {
				continue;
			}
			$eastblueApp = EastblueApp::find((int)$v);
			if (!$eastblueApp) {
				return Response::json($msg, 500);
			}
			$arr[] = $v;
		}
		$group->apps = implode(',', $arr);
		if ($group->save()) {
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

}