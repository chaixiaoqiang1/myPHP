<?php

class PaymentController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$platform_id = Session::get("platform_id");
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);

		$platform = Platform::find($platform_id);
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$response = $api->getPlatformPayMethod($platform_id, $game_id);

        //var_dump($response->http_code);die();
		if ($response->http_code != 200) {
			App::abort(404);
		}
		$body = $response->body;
		$platform_pay_types = array();
		foreach ($body as $value) {
// 			$eastblue_pay_method = Payment::where('pay_type_id', '=', $value->pay_type_id)->where('method_id' , '=', $value->method_id)->where('platform_id', '=', $platform_id)
// 			->update(array(
// 				'platform_method_id' => $value->id,
// 			));
			$platform_pay_method = array(
				'id' => $value->id,
				'pay_type_id' => $value->pay_type_id,
				'method_id' => $value->method_id,
				'method_name' => $value->method_name,
				'domain_name' => $value->domain_name,
				'class_name' => $value->class_name,
				'is_selected' => $value->is_selected,
				'is_recommend' => $value->is_recommend,
				'method_order' => $value->method_order,
				'method_description' => $value->method_description,
				'post_url' => $value->post_url,
				'html_name' => $value->html_name,
				'is_use' => $value->is_use,
				'zone' => $value->zone,
				'currency' => $value->currency,
				'use_for_month_card' => $value->use_for_month_card,
                'start_time' => isset($value->start_time)?$value->start_time:0,
                'end_time' => isset($value->end_time)?$value->end_time:0,
                'huodong_rate' => isset($value->huodong_rate)?$value->huodong_rate:0
			);
			$platform_pay_methods[] = (object)$platform_pay_method;
		}

		$data = array(
			'content' => View::make('payment.method.index', 
				array(
					'platform' => $platform,
					'game' => $game,
					'platform_pay_methods' => $platform_pay_methods
				)
			),
		);
		return View::make('main', $data);
	}


    public function huodong_index()
    {
        $platform_id = Session::get("platform_id");
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        $platform = Platform::find($platform_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->getPlatformPayMethod($platform_id, $game_id);

        if ($response->http_code != 200) {
            App::abort(404);
        }
        $body = $response->body;
        $platform_pay_types = array();
        foreach ($body as $value) {
            $platform_pay_method = array(
                'id' => $value->id,
                'pay_type_id' => $value->pay_type_id,
                'method_id' => $value->method_id,
                'method_name' => $value->method_name,
                'domain_name' => $value->domain_name,
                'class_name' => $value->class_name,
                'is_selected' => $value->is_selected,
                'is_recommend' => $value->is_recommend,
                'method_order' => $value->method_order,
                'method_description' => $value->method_description,
                'post_url' => $value->post_url,
                'html_name' => $value->html_name,
                'is_use' => $value->is_use,
                'zone' => $value->zone,
                'currency' => $value->currency,
                'use_for_month_card' => $value->use_for_month_card,
                'start_time' => isset($value->start_time)?date("Y-m-d H:i:s", $value->start_time):0,
                'end_time' => isset($value->end_time)?date("Y-m-d H:i:s", $value->end_time):0,
                'huodong_rate' => isset($value->huodong_rate)?$value->huodong_rate:0
            );
            $platform_pay_methods[] = (object)$platform_pay_method;
        }

        $data = array(
            'content' => View::make('payment.method.huodong_index', 
                array(
                    'platform' => $platform,
                    'game' => $game,
                    'platform_pay_methods' => $platform_pay_methods
                )
            ),
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
			'content' => View::make('payment.method.create', array('platform_id' => $platform_id))
		);
		return View::make('main',$data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
			'method_name' => 'required',
			'method_description' => 'required',
			'is_recommend' => 'required',
			'method_order' => 'required',
			'is_use' => 'required',
			'currency_id' => 'required',
			'platform_id' => 'required',
			'pay_type_id' => 'required',
			'method_id' => 'required',
			'use_for_month_card' => 'required',
			'is_selected' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		$msg = array(
			'code' => Config::get('errorcode.region_add'),
			'error'=> Lang::get('error.region_add')
		);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$method = new Payment;
			$method->method_name = Input::get('method_name');
			$method->method_description = trim(Input::get('method_description'));
			$method->is_recommend = trim(Input::get('is_recommend'));
			$method->method_order = trim(Input::get('method_order'));
			$method->post_url = trim(Input::get('post_url'));
			$method->html_name = trim(Input::get('html_name'));
			$method->is_use = trim(Input::get('is_use'));
			$method->currency_id = trim(Input::get('currency_id'));
			$method->zone = trim(Input::get('zone'));
			$method->platform_id = trim(Input::get('platform_id'));
			$method->pay_type_id = trim(Input::get('pay_type_id'));
			$method->method_id = trim(Input::get('method_id'));
			$method->platform_method_id = trim(Input::get('platform_method_id'));
			$method->use_for_month_card = trim(Input::get('use_for_month_card'));
			$method->class_name = trim(Input::get('class_name'));
			$method->is_selected = trim(Input::get('is_selected'));
			$method->domain_name = trim(Input::get('domain_name'));
            $method->start_time = trim(Input::get('start_time'));
            $method->end_time = trim(Input::get('end_time'));
            $method->huodong_rate = trim(Input::get('huodong_rate'));
			$is_update_platform = (int)Input::get('is_update_platform');
			if ($method->save()&&$is_update_platform) {
				$this->addPlatformPayMethod($method); 
				return Response::json(
                        array(
                                'msg' => Lang::get('type.create_success1')
                        ));
			} else {
				return Response::json(
                        array(
                                'msg' => Lang::get('type.create_success2')
                        ));
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
		$method = Payment::find($id);
		if (!$method) {
			App::abort(404);
			exit;
		}
		$data = array(
			'content' => View::make('payment.method.edit', array('method' => $method))
		);
		return View::make('main', $data);
	}

    public function huodong_edit()
    {
        $id = Input::get('pay_id');
        $method = Payment::find($id);
        if (!$method) {
            App::abort(404);
            exit;
        }
        $data = array(
            'content' => View::make('payment.method.huodong_edit', array('method' => $method))
        );
        return View::make('main', $data);
    }

    private function getNewProjects(){  //新项目的所有game_id，一般判断50之后的都是新项目，所以50之后的可能没加
        return array(1, 2, 3, 4, 5, 6, 7, 30, 36, 38, 41, 43, 44, 45, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63);
    }

    /**
    * update-batch start_time,end_time,huodong_rate
    * @param int[] $ids
    * @return Response
    */
    public function batch_update_index(){
        // $special_method_names = array(  //某些特殊的支付方式在网页上不显示但是在手机支付中存在，所以查询的时候应该找出这些虽然is_use是0的方法
        //     'Google Play',
        //     'Apple Store',
        //     'Amazon Store',
        //     'One Store',
        //     );
        $platform_id = Session::get('platform_id');
        if ($platform_id == 1) {
        	$methods = Payment::query()->where('platform_id',Session::get('platform_id'))->whereIn('domain_name', array(1,2))->where('is_use', '>', 0)
                            ->get();
        }else{
        	$methods = Payment::query()->where('platform_id',Session::get('platform_id'))->where('is_use', '>', 0)
                            ->get();
        }

        $game = Game::find(Session::get('game_id'));
        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $data = array(
        	'game_id' => $game->game_id,
        	'platform_id' => $platform_id,
        	);
        $result = $slaveapi->getPaymentMethodActivity($data);
        $now_on = array();
        if(200 == $result->http_code){
        	$payment_activities = $result->body;
        }else{
        	$payment_activities = array();
        }
        foreach ($payment_activities as $value) {
            if(date("Y-m-d H:i:s", time()) > $value->start_time && date("Y-m-d H:i:s", time()) < $value->end_time){
                $now_on[] = $value->payment_method_id;
            }
        }
        $now_on = array_unique($now_on);
        $new_projects = $this->getNewProjects();
        $data = array(
            'content' => View::make('payment.method.updatebatch',array(
            	'methods' => $methods,
            	'payment_activities' => $payment_activities,
                'now_on' => $now_on,
                'new_projects' => $new_projects,
            	))
            );
        return View::make('main',$data);
    }
    public function batch_update(){
        $msg = array(
            'code' => Config::get('errorcode.region_add'),
            'error'=> Lang::get('error.region_add')
        );
        $rules = array(
            'huodong_rate' => 'required'
            );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $huodong_rate = floatval(trim(Input::get('huodong_rate')));
        $method_ids = Input::get('method_id');
        if('0' == count($method_ids) || 0 == $method_ids){
        	return Response::json(array('error'=>'请选择方法'), 403);
        }
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);
        $params = array(
            'start_time' => $start_time,
            'end_time' => $end_time,
            'huodong_rate' => $huodong_rate,
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            );
        $api = PlatformApi::connect($platform->payment_api_url,$platform->api_key,$platform->api_secret_key);
        foreach ($method_ids as $method_id) {
            $params['id'] = $method_id;
            $new_projects = $this->getNewProjects();
            if(in_array($game_id, $new_projects) || $game_id > 50) {//土耳其,idgameland,game168
                $response = $api->updatePlatformPaymentActivity($params);
            }
            else {
                $response = $api->updatePlatformPayMethod($params);
            }
            Log::info('batch_update------'.var_export($response, true));
            if($response->http_code==200){
                $result[] = array(
                    'status' => 'ok',
                    'msg' => Lang::get('basic.edit_success')
                    );
            }else{
                $result[] = array(
                    'status' => 'error',
                    'msg' => Lang::get('basic.edit_fail')
                    );
            }
        }
        return Response::json($result);
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$method = Payment::find($id);

        // $key = 'method_name';
        // var_dump($method->$key);die();
        // var_dump($method);die();
		$msg = array(
			'code' => Config::get('errorcode.currency_edit'),
			'error'=> Lang::get('error.currency_edit')
		);
		if (!$method) {
			return Response::json($msg, 404);
		}
		$rules = array(
			'platform_method_id' => 'required',
			'method_name' => 'required',
			'method_description' => 'required',
			'is_recommend' => 'required',
			'method_order' => 'required',
			'is_use' => 'required',
			'currency_id' => 'required',
			'platform_id' => 'required',
			'pay_type_id' => 'required',
			'method_id' => 'required',
			'use_for_month_card' => 'required',
			'is_selected' => 'required'
		);

		$validator = Validator::make(Input::all(), $rules);
        
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {

			$method->platform_method_id = intval(trim(Input::get('platform_method_id')));
			$method->method_name = Input::get('method_name');
			$method->method_description = trim(Input::get('method_description'));
			$method->is_recommend = intval(trim(Input::get('is_recommend')));
			$method->method_order = intval(trim(Input::get('method_order')));
			$method->post_url = trim(Input::get('post_url'));
			$method->html_name = trim(Input::get('html_name'));
			$method->is_use = intval(trim(Input::get('is_use')));
			$method->currency_id = intval(trim(Input::get('currency_id')));
			$method->zone = intval(trim(Input::get('zone')));
			$method->platform_id = intval(trim(Input::get('platform_id')));
			$method->pay_type_id = intval(trim(Input::get('pay_type_id')));
			$method->method_id = intval(trim(Input::get('method_id')));
			$method->class_name = trim(Input::get('class_name'));
			$method->use_for_month_card = intval(trim(Input::get('use_for_month_card')));
			$method->is_selected = intval(trim(Input::get('is_selected')));
			$method->domain_name = intval(trim(Input::get('domain_name')));

			if ($method->save()) {
                //Log::info("update()-->update success!");
				$res = $this->updatePlatformPayMethod($method);
                //var_dump($res);die();
				if ($res->http_code == 200) {
					return Response::json(array('msg' => Lang::get('basic.edit_success')));
				} else {
					return Response::json(array('msg' => Lang::get('basic.edit_fail')));
				}
			} else {
				return Response::json($msg, 500);
			}
		}
	}
    public function huodong_update()
    {
        $id = Input::get('pay_id');
        $method = Payment::find($id);
        $msg = array(
            'code' => Config::get('errorcode.currency_edit'),
            'error'=> Lang::get('error.currency_edit')
        );
        if (!$method) {
            return Response::json($msg, 404);
        }
        $rules = array(
            'huodong_rate' => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
            return Response::json($msg, 403);
        } else {
            $method->start_time = strtotime(trim(Input::get('start_time')));
            $method->end_time = strtotime(trim(Input::get('end_time')));
            $method->huodong_rate = floatval(trim(Input::get('huodong_rate')));
            $game_id = Session::get('game_id');
            $platform_id = Session::get('platform_id');
            if ($method->save()) {
                $platform = Platform::find($platform_id);
                $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
                //Log::info(var_export($api,true));
                $params = array(
                    'id' => $method->platform_method_id,
                    'start_time' => $method->start_time,
                    'end_time' => $method->end_time,
                    'huodong_rate' => $method->huodong_rate,
                    'platform_id' => $platform_id,
                    'game_id' => $game_id,
                    );
                if($platform_id == 50 || $platform_id == 1 || $platform_id == 38) {//土耳其,idgameland,game168
                    $res = $api->updatePlatformPaymentActivity($params);
                }
                else {
                    $res = $api->updatePlatformPayMethod($params);
                }
                //Log::info(var_export($res,true));
                //var_dump($res);die();
                if ($res->http_code==200) {
                    return Response::json(array('msg' => Lang::get('basic.edit_success')));
                } else {
                    return Response::json(array('error' => Lang::get('basic.edit_fail')), 401);
                }
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

	private function addPlatformPayMethod($method)
    {
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
        $params = array(
                'method_name' => $method->method_name,
                'method_description' => $method->method_description,
                'is_recommend' => $method->is_recommend,
                'method_order' => $method->method_order,
                'post_url' => $method->post_url,
                'html_name' => $method->html_name,
                'is_use' => $method->is_use,
                'currency' => $method->currency_id,
                'domain_name' => $method->domain_name,
                'zone' => $method->zone,
                'pay_type_id' => $method->pay_type_id,
                'method_id' => $method->method_id,
                'class_name' => $method->class_name,
                'use_for_month_card' => $method->use_for_month_card,
                'is_selected' => $method->is_selected,
                'start_time' => $method->start_time,
                'end_time' => $method->end_time,
                'huodong_rate' => $method->huodong_rate
        );
        $response = $api->addPlatformPayMethod($params);
        if ($response->http_code == 200 && isset($response->body->id))
        {
            $method->platform_method_id = $response->body->id;
            $method->save();
        } else
        {
            App::abort(404);
        }
        return $response;
    }

	private function updatePlatformPayMethod($method)
    {
    	$game_id = Session::get('game_id');
    	$platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $params = array(
        		'id' => $method->platform_method_id,
                'method_name' => $method->method_name,
                'method_description' => $method->method_description,
                'is_recommend' => $method->is_recommend,
                'method_order' => $method->method_order,
                'post_url' => $method->post_url,
                'html_name' => $method->html_name,
                'is_use' => $method->is_use,
                'currency' => $method->currency_id,
                'domain_name' => $method->domain_name,
                'zone' => $method->zone,
                'pay_type_id' => $method->pay_type_id, 
                'method_id' => $method->method_id,
                'class_name' => $method->class_name,
                'use_for_month_card' => $method->use_for_month_card,
                'is_selected' => $method->is_selected,
                'huodong_rate' => $method->huodong_rate,
                'start_time' => $method->start_time,
                'end_time' => $method->end_time,
                'game_id' => $game_id,
                'platform_id' => $platform_id
                );
        
        $response = $api->updatePlatformPayMethod($params);
        if('200' != $response->http_code){
        	Log::info('updatePlatformPayMethod error--'.var_export($response, true));
        }
        
        return $response;
    }

}