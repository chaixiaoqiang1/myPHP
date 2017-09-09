<?php

class PayCurrencyController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$game = Game::find(Session::get('game_id'));
		$platform = Platform::find(Session::get('platform_id'));
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$response = $api->getPlatformCurrency($platform->platform_id);
		if ($response->http_code != 200) {
			App::abort(404);
		}
		$platform_currency = $response->body;
		$data = array(
			'content' => View::make('payment.payment_currency.index',
				array(
					'platform_currency' => $platform_currency,
					'platform' => $platform,
					'game' => $game,
				)
			)
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
		$platform_id = Session::get("platform_id");
        $data = array(
                'content' => View::make('payment.payment_currency.create', 
                        array(
                                'platform_id' => $platform_id
                        ))
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
			'platform_id' => 'required',
			'pay_type_id' => 'required',
			'method_id' => 'required',
			'currency_id' => 'required',
			'currency_order' => 'required',
		); 
		$msg = array(
			'code' => Config::get('errorcode.currency_add'),
            'error' => Lang::get('error.currency_add'),
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$insert_id = $this->addPlatformCurrency(Input::all());
			return Response::json(array('msg' => $insert_id.Lang::get('basic.create_success')));
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
		$game = Game::find(Session::get('game_id'));
		$platform = Platform::find(Session::get('platform_id'));
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$response = $api->getPlatformCurrency($platform->platform_id, $id);
		if ($response->http_code != 200) {
			App::abort(404);
		}
		$edit_currency = $response->body[0];
		$data = array('content' => View::make('payment.payment_currency.edit',
				array(
					'edit_currency' => $edit_currency,
					'platform_id' => $platform->platform_id
				)
		 	)
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
		$rules = array(
			'pay_type_id' => 'required',
			'method_id' => 'required',
			'currency_id' => 'required',
			'currency_order' =>'required',
			'id' => 'required',
		);
		$msg = array(
			'code' => Config::get('errorcode.currency_edit'),
			'error' => Lang::get('error.currency_edit'),
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$res = $this->updatePlatformCurrency(Input::all());
			return Response::json( array('msg' => Lang::get('basic.edit_success')));
		}
	}


	private function addPlatformCurrency($params)
	{
		$platform = Platform::find(Session::get('platform_id'));
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$params = array(
			'pay_type_id' => $params['pay_type_id'],
			'method_id' => $params['method_id'],
			'currency_id' => $params['currency_id'],
			'currency_order' => $params['currency_order'],
		);
		$response = $api->addPlatformPaymentCurrency($params);
		if ($response->http_code == 200) {
			$insert_id = $response->body->id;
		} else {
			App::abort(404);
		}
		return $insert_id;
	}

	private function updatePlatformCurrency($params)
	{
		$platform = Platform::find(Session::get('platform_id'));
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$params = array(
			'id' => $params['id'],
			'pay_type_id' => $params['pay_type_id'],
			'method_id' => $params['method_id'],
			'currency_id' => $params['currency_id'],
			'currency_order' => $params['currency_order'],
		);
		$response = $api->updatePlatformPaymentCurrency($params);
		if ($response->http_code != 200) {
			App::abort(404);
		} else {
			return $response;
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