<?php

class PlatformController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
			'content' => View::make('platforms.index')
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
			'content' => View::make('platforms.create')
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
			'code' => Config::get('errorcode.platform_add'),
			'error'=> Lang::get('error.platform_add')
		);

		$region = Region::find((int)Input::get('region_id'));
		if (!$region) {
			return Response::json($msg, 403);
		}

		$rules = array(
			'platform_name' => 'required',
			'platform_url' => 'required',
			'region_id' => 'required',
			'platform_api_url' => 'required',
			'payment_api_url' => 'required',
			'api_key' => 'required',
			'api_secret_key' => 'required',
			'default_currency_id' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$platform = new Platform;
			$platform->platform_name = trim(Input::get('platform_name'));
			$platform->platform_url = trim(Input::get('platform_url'));
			$platform->region_id = (int)Input::get('region_id');
			$platform->platform_api_url = trim(Input::get('platform_api_url'));
			$platform->payment_api_url = trim(Input::get('payment_api_url'));
			$platform->api_key = trim(Input::get('api_key'));
			$platform->api_secret_key = trim(Input::get('api_secret_key'));
			$platform->default_currency_id = trim(Input::get('default_currency_id'));
			if ($platform->save()) {
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
		$platform = Platform::find($id);

		if (!$platform) {
			App::abort(404);
			exit;
		}

		if (!Auth::user()->is_admin) {
			$games = Game::userGames()->select('platform_id')->distinct()->get();
			$isMinePlatform = false;
			foreach ($games as $k => $v) {
				if ($id == $v->platform_id) {
					$isMinePlatform = true;
					break;	
				}
			}
			if (!$isMinePlatform) {
				App::abort(403);
				exit;
			}
		}

		Session::forget('game_id');
		Session::put('platform_id', $platform->platform_id);

		$game = Game::where('platform_id', '=', $id)->first();

		if (!$game) {
			return Redirect::to('/');
		}

		return View::make('games');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$platform = Platform::find($id);

		if (!$platform) {
			App::abort(404);
			exit;
		}

		$data = array(
			'content' => View::make('platforms.edit', array(
				'platform' => $platform
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
		$msg = array(
			'code' => Config::get('errorcode.platform_edit'),
			'error'=> Lang::get('error.platform_edit')
		);

		$platform = Platform::find($id);

		if (!$platform) {
			return Response::json($msg, 404);
		}

		$region = Region::find((int)Input::get('region_id'));
		if (!$region) {
			return Response::json($msg, 404);
		}

		$rules = array(
			'platform_name' => 'required',
			'platform_url'  => 'required',
			'region_id'     => 'required',
			'platform_api_url' => 'required',
			'payment_api_url' => 'required',
			'api_key' => 'required',
			'api_secret_key' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$platform->platform_name = trim(Input::get('platform_name'));
			$platform->platform_url = trim(Input::get('platform_url'));
			$platform->region_id = (int)Input::get('region_id');
			$platform->default_game_id = (int)Input::get('default_game_id');
			$platform->platform_api_url = trim(Input::get('platform_api_url'));
			$platform->payment_api_url = trim(Input::get('payment_api_url'));
			$platform->api_key = trim(Input::get('api_key'));
			$platform->api_secret_key = trim(Input::get('api_secret_key'));
			$platform->default_currency_id = (int)(Input::get('default_currency_id'));

			if ($platform->save()) {
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