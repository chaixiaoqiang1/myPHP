<?php

class RegionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
			'content' => View::make('regions.index')
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
			'content' => View::make('regions.create')
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
			'region_name' => 'required',
			'region_code' => 'required',
			'timezone' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		$msg = array(
			'code' => Config::get('errorcode.region_add'),
			'error'=> Lang::get('error.region_add')
		);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$region = new Region;
			$region->region_name = trim(Input::get('region_name'));
			$region->region_code = strtoupper(trim(Input::get('region_code')));
			$region->timezone = trim(Input::get('timezone'));
			if ($region->save()) {
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
		$region = Region::find($id);

		if (!$region) {
			App::abort(404);
			exit;
		}

		$data = array(
			'content' => View::make('regions.edit', array('region' => $region))
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
		$region = Region::find($id);
		$msg = array(
			'code' => Config::get('errorcode.region_add'),
			'error'=> Lang::get('error.region_edit')
		);
		if (!$region) {
			return Response::json($msg, 404);
		}
		$rules = array(
			'region_name' => 'required',
			'region_code' => 'required',
			'timezone' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$region->region_name = trim(Input::get('region_name'));
			$region->region_code = strtoupper(trim(Input::get('region_code')));
			$region->timezone = trim(Input::get('timezone'));

			if ($region->save()) {
				return Response::json(array('msg' => Lang::get('basic.create_success')));
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