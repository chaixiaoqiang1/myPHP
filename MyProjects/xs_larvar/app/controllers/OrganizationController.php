<?php

class OrganizationController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
		    'content' => View::make('organizations.index')
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
		    'content' => View::make('organizations.create')
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
		$msg = array(
		    'code'  =>Config::get('errorcode.organ_add'),
		    'error' =>Lang::get('error.organ_add')
		);
		$rules = array(
		    'organ_name'  => 'required',
		    'allowed_ips' => 'required'
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$organ = new Organization;
			$organ->organization_name = trim(Input::get('organ_name'));
			$organ->allowed_ips       = trim(Input::get('allowed_ips'));
		}
		if ($organ->save()) {
		    return Response::json(array('msg' => Lang::get('basic.create_success')));
		} else {
				return Response::json($msg, 500);
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
		$organ = Organization::find($id);
		if (!$organ) {
			App:abort(404);
		}
		$data = array(
		    'content' => View::make('organizations.edit', array('organ' => $organ))
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
		$organ = Organization::find($id);
		$msg = array(
		    'code'    =>  Config::get('errorcode.organ_edit'),
		    'error'   =>  Lang::get('error.organ_edit')
		);
		if (!$organ) {
			return Response::json($msg, 404);
		} 
		return $this->editOrgan($organ, $msg);
	}
	
	private function editOrgan($organ, $msg)
	{
		$rules = array(
		    'organ_name'  => 'required',
		    'allowed_ips'  => 'required'
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json($msg,404);
		} else {
			$organ->organization_name  =  trim(Input::get('organ_name'));
			$organ->allowed_ips        =  trim(Input::get('allowed_ips'));
			if ($organ->save()) {
				return Response::json(array('msg' => Lang::get('basic.update_success')));
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