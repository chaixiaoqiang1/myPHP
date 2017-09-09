<?php

class DepartmentController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
			'content' => View::make('department.index')
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
		//
		$data = array(
			'content' => View::make('department.create')
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
			'department_name' => 'required',
		);

		$validator = Validator::make(Input::all(), $rules);

		$msg = array(
			'code' => Config::get('errorcode.department_add'),
			'error'=> Lang::get('error.department_add')
		);
		if ($validator->fails()) {
			return Response::json($msg);
		} else {
			$now = time();
			$department = new Department;
			$department->department_name = trim(Input::get('department_name'));
			$department->organization_id = Auth::user()->organization_id;
			
			if ($department->save()) {
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
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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