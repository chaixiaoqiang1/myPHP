<?php

class PayAmountController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//var_dump(Input::all());die();
		$data = array(
			'content' => View::make('payment.amount.index')
		);
		return View::make('main',$data);
	}
	public function create_index()
	{
		//var_dump(Input::all());die();
		$data = array(
			'content' => View::make('payment.amount.create')
		);
		return View::make('main',$data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function check()
	{
		$pay_type_id = Input::get('type');
		$method_id = Input::get('payment');
		// $domain_name = Input::get('domain_name');
		$game = Game::find(Session::get('game_id'));
		$params = array(
			"pay_type_id" => $pay_type_id,
			"method_id" => $method_id,
			// "domain_name" => $domain_name,
			"game_id" => $game->game_id,
			"platform_id" => Session::get('platform_id')
			);
		// if(isset($method_id)){
		// 	$params['method_id'] = $method_id;
		// }
		// var_dump($params);die();
		$api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key);
		$response = $api->getAmounts($params);
		if($response->http_code==200){
			$data = $response->body;
			// var_dump($data);die();
			foreach ($data as $value) {
				$result[] = array(
					'pay_amount_id' => $value->pay_amount_id,
					'currency_id' => $value->currency_id,
					'pay_amount' => $value->pay_amount,
					'yuanbao_amount' => $value->yuanbao_amount,
					'yuanbao_extra' => $value->yuanbao_extra,
					'yuanbao_huodong' => $value->yuanbao_huodong,
					'pay_type_id' => $value->pay_type_id,
					'method_id' => $value->method_id,
					'domain_name' => $value->domain_name,
					'goods_type' => $value->goods_type
					);
			}
			// var_dump($result);die();
			return Response::json($data);
		}

		// $data = array(
		// 	'content' => View::make('payment.amount.create')
		// );
		// return View::make('main',$data);
	}
	public function batch_update(){
		$pay_type_id = Input::get('type');
		$method_id = Input::get('payment');
		$platform_id = Input::get('platform_id');
		$params = array(
			'pay_type_id' => $pay_type_id,
			'method_id' => $method_id,
			'platform_id' => $platform_id
			);
		$platform = Platform::find($platform_id);
		$api = PlatformApi::connect($platform->payment_api_url,$platform->api_key,$platform->api_secret_key);
		$response = $api->updatePlatformPaymentAmount($params);
		// var_dump($response);die();
				if ($response->http_code==200) {
					$result = array(
						'res' => true,
						'msg' => Lang::get('basic.abandon_success') . "(" . $response->body ."rows)"
						);
				}else{
					$result = array(
						'res' => false,
						'msg' => Lang::get('basic.abandon_fail') 
						);
				}
		return Response::json($result);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		$msg = array(
		    'code'  => Config::get('errorcode.currency_add'),
		    'error' => Lang::get('error.currency_add')
		);		
		$rules = array(
		    'amount' => 'required',
		);
		$validator = Validator::make(Input::all(),$rules);
		if($validator->fails()){
			return Response::json($msg,403);
		} else {
			$amounts = array();
			$amount_string = trim(Input::get('amount'));
			$amounts_arr = array();
			$amounts_arr = explode("\n", $amount_string);
			foreach ($amounts_arr as $key => $value) {
				$amounts[] = explode("\t", $value);
			}
			$platform = Platform::find(Session::get('platform_id'));
			
			$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);

			foreach ($amounts as  $value) {
				if(count($value)==8){
					$amount = array(
							'currency_id'    => intval($value[0]),
							'pay_amount'    => floatval($value[1]),
							'yuanbao_amount' => intval($value[2]),
							'yuanbao_extra'    => intval($value[3]),
							'yuanbao_huodong'    => intval($value[4]),
							'pay_type_id'    => intval($value[5]),			
							'method_id'      => intval($value[6]),
							'domain_name'    => intval($value[7]),
					);
				}else{
					$amount = array(
							'currency_id'    => intval($value[0]),
							'pay_amount'    => floatval($value[1]),
							'yuanbao_amount' => intval($value[2]),
							'yuanbao_extra'    => intval($value[3]),
							'yuanbao_huodong'    => intval($value[4]),
							'pay_type_id'    => intval($value[5]),			
							'method_id'      => intval($value[6]),
							'domain_name'    => intval($value[7]),
							'goods_type' => intval($value[8])
					);	
				}
				$response = $api->addPlatformPayAmount($amount);

				if ($response->http_code==200) {
					$result[] = array(
						'res' => true,
						'msg' => Lang::get('basic.create_success')
						);
				}else{
					$result[] = array(
						'res' => false,
						'msg' => Lang::get('basic.create_fail') 
						);
				}			
			}
			return Response::json($result);
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
		$amount = Amount::find($id);
		if (!$amount) {
			App::abort(404);
			exit;
		}
		$data = array(
			'content' => View::make('payment.amount.edit', array('amount' => $amount))
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
		$amount = Amount::find($id);
		$msg = array(
			'code' => Config::get('errorcode.currency_edit'),
			'error'=> Lang::get('error.currency_edit')
		);
		if (!$amount) {
			return Response::json($msg, 404);
		}
		$rules = array(
		    'currency_id' => 'required',
		    'pay_amount' => 'required',
		    'yuanbao_amount' => 'required',
		    'yuanbao_extra' => 'required',
		    'yuanbao_hudong' => 'required',
		    //'pay_type_id' => 'required',
		    //'method_id' => 'required',
		    'platform_id' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$amount->currency_id = trim(Input::get('currency_id'));
			$amount->pay_amount = trim(Input::get('pay_amount'));
			$amount->yuanbao_amount = trim(Input::get('yuanbao_amount'));
			$amount->yuanbao_extra = trim(Input::get('yuanbao_extra'));
			$amount->yuanbao_hudong = trim(Input::get('yuanbao_hudong'));
			$amount->platform_id = trim(Input::get('platform_id'));
			if ($amount->save()) {
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


	public function getType()
	{
		$platform_id = Input::get('platform_id');
		$type = PayType::orderBy('type_id', 'asc')->where('platform_id', $platform_id)->get();
		return Response::json($type);
	}

	public function getPayment()
	{
		$platform_id = Input::get('platform_id');
		$pay_type_id = Input::get('type');
		$type = Payment::orderBy('pay_id', 'asc')->where('platform_id', $platform_id)->where('pay_type_id', $pay_type_id)->get();
		return Response::json($type);
	}

	public function addPaymentAmount($amount){
		$platform = Platform::find(Session::get('platform_id'));
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$params = array(
			'currency_id' => $amount['currency_id'],
			'pay_amount' => $amount['pay_amount'],
			'yuanbao_amount' => $amount['yuanbao_amount'],
			'yuanbao_extra' => $amount['yuanbao_extra'],
			'yuanbao_huodong' => $amount['yuanbao_hudong'],
			'pay_type_id' => $amount['pay_type_id'],
			'method_id' => $amount['method_id'],
			'domain_name' => $amount['domain_name'],
			'goods_type' => $amount['goods_type']
			);
		return $api->addPlatformPayAmount($params);
	}
}