<?php

class AdSourceController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$source = AdSource::orderBy('created_at', 'desc')->paginate(20);
		$data = array(
		    'content' => View::make('sources.index', array('source' => $source))
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
		    'content' => View::make('sources.create')
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
			'game_id'      => 'required',
			'source_name'  => 'required',
			'source_value' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		$msg = array(
			'code' => Config::get('errorcode.source_add'),
			'error'=> Lang::get('error.source_add')
		);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$source = new AdSource;
			$source->source_name  = trim(Input::get('source_name'));
			$source->source_value = trim(Input::get('source_value'));
			$source->game_id      = (int)(Input::get('game_id'));
			if ($source->save()) {
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