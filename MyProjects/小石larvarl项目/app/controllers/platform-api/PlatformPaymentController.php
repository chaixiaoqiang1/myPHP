<?php

class PlatformPaymentController extends \BaseController
{
	public function chuliDelayOrder()
	{
		$result = array(
				'orderChuli' => '',
				'orderBysn'	 => '',
				'giveYuanbao'=> '',
				'sendMessage'=> '',
			);
		$order_sn = Input::get('order_sn');
		$deal_status = (int)Input::get('yesORno');
		$user_id = (int)Input::get('user_id');
		$code = Input::get('code');
		$order = array(
			'order_sn' => $order_sn,
			'deal_status' => $deal_status,
			'user_id' => $user_id,
			'code' => $code,
			);
		$platform = Platform::find(Session::get('platform_id'));
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$response = $api->caozuoDelayOrder($order);
		$result['orderChuli'] = $response;
		//var_dump("12312312");die();
		if($response->http_code == 200 && $response->body->error == 0 && $deal_status == 2){
			//通过order_sn获取订单的详细信息
			 $order_sn = trim(Input::get('order_sn'));
			 $game = Game::find(Session::get('game_id'));
        	 $game_id = Session::get('game_id');
       		 $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        	 $platform_id = Session::get('platform_id');
        	 $response = '';
        	 $response = $api->getOrderByOrderSN($platform_id, $order_sn, $game_id);//德州game_id 11
        	 $result['orderBysn'] = $response;
             $body = $response->body;
             //var_dump($body);die();
             //通过订单的详细信息去补储元宝
             
             if(!isset($body->mycard_id))
             	$mycard_id = 0;
             else
             	$mycard_id = $body->mycard_id;

             $order = array(
				'order_id' => (int)$body->order_id,
				'tradeseq'       => $body->tradeseq,
				'pay_amount' => (float)$body->pay_amount,
				'basic_yuanbao_amount'   => (int)$body->basic_yuanbao_amount,
				'extra_yuanbao_amount'   => (int)$body->extra_yuanbao_amount,
				'huodong_yuanbao_amount'  => (int)$body->huodong_yuanbao_amount,
				'yuanbao_amount'     => (int)$body->yuanbao_amount,
				'mycard_id'     => $mycard_id,
			 );
			 //var_dump($order);die();
			 $platform = Platform::find(Session::get('platform_id'));
			 $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
			 if ($mycard_id) {
				$response = $api->giveYuanbaoForMycard($order);
			 } else {
				$response = $api->giveYuanbaoForOther($order);
			 }
			 if('11' != $game_id){
			 	Log::info('Panda-test-offer-yuanbao---------------------'.var_export($response, true));			 	
			 }
			 $result['giveYuanbao'] = $response;
			 //var_dump($response);die();

			 //发送给玩家一条通过的消息
			$success = $fail = '';
    		$result1 = array();
    		$result2 = array();
    		$data = array();
    		$players = $body->player_id;
    		$message = 'Pesanan telah berhasil diverifikasi, item telah dikirimkan. Terima kasih~ Salam All In';//你的订单审核通过，发放元宝
    		$server = Server::find(13);
    		if (!$server) {
    			$msg['error'] = Lang::get('error.basic_not_found');
    			return Response::json($msg, 403);
    		}

    		$game_api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
			$player_arr = explode("\n", $players);
    		foreach ($player_arr as $key => $value) {
    			$response = $game_api->sendMessage($value, $message);
    			if ($response->is_ok) { //发送成功
    				if ($response->is_ok) {
					  	$success .= "--(" .  (isset($value) ? $value : '')  .')';
					} else{
						$fail .= "--(" .  (isset($value) ? $value : '')  .')';
					}
    			}
    			unset($response);
			}
			$result1 = array(
    			'status' => 'ok',
    			'msg' => $success
    		);
    		$result2 = array(
    			'status' => 'error',
    			'msg' => $fail
    		);
    		$data = array(
    		'result1' => $result1,
    		'result2' => $result2
    		);
    		$result['sendMessage'] = $data;	
		}
		//var_dump($result);die();
		return Response::json($result);
	}

	public function offerYuanbao()
	{
		$mycard_id = Input::get('mycard_id');
		$order = array(
			'order_id' => (int)Input::get('order_id'),
			'order_sn' => Input::get('order_sn'),
			'tradeseq'       => Input::get('tradeseq'),
			'pay_amount' => (float)Input::get('pay_amount'),
			'basic_yuanbao_amount'   => (int)Input::get('basic_yuanbao_amount'),
			'extra_yuanbao_amount'   => (int)Input::get('extra_yuanbao_amount'),
			'huodong_yuanbao_amount'  => (int)Input::get('huodong_yuanbao_amount'),
			'yuanbao_amount'     => (int)Input::get('yuanbao_amount'),
			'giftbag_id' => Input::get('giftbag_id'),
			'mycard_id'     => $mycard_id,
		);
		if('' == $order['giftbag_id']){	//如果页面传入的是空值则
			unset($order['giftbag_id']);
		}else{
			$order['giftbag_id'] = (int)$order['giftbag_id'];
		}
		$platform = Platform::find(Session::get('platform_id'));
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		if ($mycard_id) {
			$response = $api->giveYuanbaoForMycard($order);
		} else {
			$response = $api->giveYuanbaoForOther($order);
		}
		if(!isset($order['giftbag_id'])){
			$order['giftbag_id'] = '';
		}
		$player_id = Input::get('player_id');
		$player_name = Input::get('player_name');
		$server_name = Input::get('server_name');
		if ($response->http_code == 200) {
			$restore_log = new EastBlueLog();
            $restore_log->log_key = 'restore';
            $restore_log->user_id = Auth::user()->user_id;
            $restore_log->game_id = Session::get('game_id');
            $restore_log->desc = $order['order_id'] . '|' . $order['pay_amount'] . '|' . $order['yuanbao_amount'] . '|' . $order['giftbag_id'] . '|' . $server_name . '|' . $player_name . '|' . $player_id;
            $restore_log->created_at = time();
            $restore_log->updated_at = time();
            $restore_log->save();
			return Response::json(array());
		} else {
			return Response::json($response->body, $response->http_code);
		}	
	}

	public function createNewOrderIndex()
	{
		$servers = Server::currentGameServers()->get();
		$currencies = Currency::all();
		$data = array(
			'content' => View::make('platformapi.payment.neworder', array(
				'servers' => $servers,
				'currencies' => $currencies,
			))
		);
		return View::make('main', $data);
	}

	public function createNewOrder()
	{
		$msg = array(
			'error' => Lang::get('error.basic_input_error')
		);
		$server_id = (int)Input::get('server_id');
		$server = Server::find($server_id);
		if (!$server) {
			return Response::json($msg, 404);
		}
		$currency_code = Input::get('currency_code');
		if (!$currency_code) {
			return Response::json($msg, 404);
		}
		$order = array(
			'platform_server_id' => $server->platform_server_id,
			'game_id' => $server->game_id,
			'pay_user_id' => Input::get('pay_user_id'),
			'currency' => $currency_code,
			'pay_amount' => (float)Input::get('pay_amount'),
			'basic_yuanbao_amount' => (float)Input::get('basic_yuanbao_amount'),
			'extra_yuanbao_amount' => (float)Input::get('extra_yuanbao_amount'),
			'huodong_yuanbao_amount' => (float)Input::get('huodong_yuanbao_amount'),
			'yuanbao_amount' => (float)Input::get('yuanbao_amount'),
		);
		$platform = Platform::find(Session::get('platform_id'));
		$game_id = Session::get('game_id');
		if ($game_id == 11) { //德州扑克
			$platform->payment_api_url = 'http://payid.joyspade.com' ;
		}
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$api->createNewOrder($order);
		return $api->sendResponse();
	}

	public function createOrderIndex()
	{
		$servers = Server::currentGameServers()->get();
		$types = PayType::currentPlatform()->get();
		$currencies = Currency::all();
		$data = array(
			'content' => View::make('platformapi.payment.order', array(
				'servers' => $servers,
				'currencies' => $currencies,
				'types' => $types
			))
		);
		return View::make('main', $data);
	}

	public function createOrder()
	{
		$msg = array(
			'error' => Lang::get('error.basic_input_error')
		);
		$server_id = (int)Input::get('server_id');
		$server = Server::find($server_id);
		if (!$server) {
			return Response::json($msg, 404);
		}
		$currency_code = Input::get('currency_code');
		if (!$currency_code) {
			return Response::json($msg, 404);
		}
		$order = array(
			'pay_type_id' => Input::get('type'),
			'method_id' => Input::get('payment'),
			'server_id' => $server->platform_server_id,
			'game_id' => $server->game_id,
			'pay_user_id' => Input::get('pay_user_id'),
			'currency' => $currency_code,
			'pay_amount' => (float)Input::get('pay_amount'),
			'basic_yuanbao_amount' => (float)Input::get('basic_yuanbao_amount'),
			'extra_yuanbao_amount' => (float)Input::get('extra_yuanbao_amount'),
			'huodong_yuanbao_amount' => (float)Input::get('huodong_yuanbao_amount'),
			'yuanbao_amount' => (float)Input::get('yuanbao_amount'),
		);
		$platform = Platform::find(Session::get('platform_id'));
		$game_id = Session::get('game_id');
		if ($game_id == 11) { //德州扑克
			$platform->payment_api_url = 'http://payid.joyspade.com' ;
		}
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$response = $api->createOrder($order);
		if($response->http_code==200 && $response->body->res=='true'){
			$result = array(
				'res' => true,
				'msg' => "(".$server->server_name.")"."录入订单成功".$response->body->order_sn,
				);
		}else{
			$result = array(
				'res' => false,
				'msg' => "录入失败"
				);
		}
		return Response::json($result);
		// var_dump($response);die();
		// return $api->sendResponse();
	}
	public function getPayment()
	{
		$platform_id = Session::get('platform_id');
		$pay_type_id = Input::get('type');
		$type = Payment::orderBy('pay_id', 'asc')->where('platform_id', $platform_id)->where('pay_type_id', $pay_type_id)->get();
		return Response::json($type);
	}


	//手游第三方支付开关
	public function sdkRechargeIndex()
	{
		$servers = Server::currentGameServers()->get();
		$currencies = Currency::all();
		$data = array(
			'content' => View::make('payment.game_list.update_recharge', array(
				'servers' => $servers,
				'currencies' => $currencies,
			))
		);
		return View::make('main', $data);
	}

	public function sdkRecharge()
	{
		$msg = array(
			'error' => Lang::get('error.basic_input_error')
		);
		$sdk_recharge = (int)Input::get('sdk_recharge');

		$info = array(
			'sdk_recharge' => $sdk_recharge,
			'game_id' => Session::get('game_id'),
		);

		$platform = Platform::find(Session::get('platform_id'));
		$game_id = Session::get('game_id');

		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$response = $api->updatesdkRecharge($info);
		log::info(var_export($response));
		if($response->http_code==200&&$response->body->res==true){
			$result = array(
				'res' => true,
				'msg' => 'success',
				);
		}else{
			$result = array(
				'res' => false,
				'msg' => 'failed',
				);
		}
		return response::json($result);
	}

	public function joyCardIndex()
	{
		$game = Game::find(Session::get('game_id'));
		if('poker' != $game->game_code){
			return $this->show_message('403', 'Not a Poker Game!');
		}
		$point_array = array(100,200,400,600,800,1000,2000);
		$data = array(
			'content' => View::make('platformapi.payment.joycard',array('point' => $point_array)),
			);
		return View::make('main', $data);
	}

	public function joyCardCreate()
	{
		$msg = array(
			'error' => Lang::get('error.basic_input_error'),
		);
		$game = Game::find(Session::get('game_id'));
		if('poker' != $game->game_code){
			return Response::json(array('error' => 'Not a Poker Game!'), 403);
		}
		$player_id = (int)Input::get('player_id');
		$points = (int)Input::get('points');
		$money = (int)Input::get('money');
		$num = (int)Input::get('num');
		$time = time();
		if($points == 0 || $money == 0 || $num == 0 || $player_id == 0){
			return Response::json($msg, 403);
		}
		$dx = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$xx = 'abcdefghijklmnopqrstuvwxyz';
		$zm = '1234567890';
		$pool = $dx . $xx . $zm;
		$prefix_array = array(
			'100' => '10','200' => '20','400' => '40','600' => '60','800' => '80','1000' => '1X','2000' => '2X'
		);

		$result = array();
		$params = array();
		for($i = 0;$i < $num;$i ++){
			$card_number = $prefix_array[$points] . $this->randNum($pool,14);
			if($this->joyCardCheck($card_number)){
				$i --;
				continue;
			}
			$encrypt_key = 'joyspade_card_key';
			$card_password = 'J'.strtoupper(substr(md5($card_number.$encrypt_key), 0, 15));
			$r = array('card_number' => $card_number , 'card_secret' => $card_password , 'price' => $money, 'point' => $points, 'owner' => $player_id);
			$result[] = $r;
		}
		$params['card_array'] = json_encode($result);
		$params['time'] = $time;
		$params['owner'] = $player_id;
		$params['creator'] = Auth::user()->username;
		$platform = Platform::find(Session::get('platform_id'));
		$game_id = Session::get('game_id');
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$response = $api->joyCardCreate($params);
		if($response->http_code==200){
			return Response::json(array(), 200);
		}
		return $api->sendResponse();
	}

	public function joyCardCheck($card_number){
		$result = DB::table('log')->where('desc',$card_number)->first();
		if($result){
			return 1;
		}
		DB::table('log')->insert(array('desc' => $card_number));
		return 0;
	}

	public function joyCardQuery()
	{
		$msg = array(
			'error' => Lang::get('error.basic_input_error'),
		);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$start_time = date("Y-m-d H:i:s",$start_time);
		$end_time = date("Y-m-d H:i:s",$end_time);
		$player_id = (int)Input::get('player_id');
		$is_use = Input::get('type');
		$params = array();
		$params['is_use'] = $is_use;
		$params['start_time'] = $start_time;
		$params['end_time'] = $end_time;
		if($player_id){
			$params['owner'] = $player_id;
		}
		$platform = Platform::find(Session::get('platform_id'));
		$game_id = Session::get('game_id');
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$params['time'] = time();
		$response = $api->joyCardQuery($params);
		// var_export($response);
		return Response::json($response->body);
	}

	public function joyCardChangeOwner()
	{
		$msg = array(
			'error' => Lang::get('error.basic_input_error'),
		);
		$card_number = Input::get('tar_card_number');
		$player_id = Input::get('new_player_id');
		$payload = array(
			'card_number' => $card_number,
			'owner' => $player_id,
			'time' => time()
		);
		$platform = Platform::find(Session::get('platform_id'));
		$game_id = Session::get('game_id');
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$response = $api->joyCardChangeOwner($payload);
		return Response::json($response);
	}

	public function joyCardDownload(){
		$msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );

        $start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$start_time = date("Y-m-d H:i:s",$start_time);
		$end_time = date("Y-m-d H:i:s",$end_time);
		$is_use = Input::get('type');
		$params = array();
		$params['is_use'] = $is_use;
		$params['start_time'] = $start_time;
		$params['end_time'] = $end_time;
		$platform = Platform::find(Session::get('platform_id'));
		$game_id = Session::get('game_id');
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$params['time'] = time();
		$response = $api->joyCardQuery($params);
        if ('200' != $response->http_code){
            return Response::json(array('error'=>'没有数据需要下载!'), 403);
        }
        $body = $response->body;
        $titles = Input::get('titles');
        $result = array();
        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";

        $csv = CSV::init($file, $titles);
        foreach ($body as $value) {
            $result = array(
            	'creator' => isset($value->creator) ? $value->creator : '',
            	'card_number' => isset($value->card_number) ? $value->card_number : '',
            	'card_secret' => isset($value->card_secret) ? $value->card_secret : '',
            	'owner' => isset($value->owner) ? $value->owner : '',
            	'uid' => isset($value->uid) ? $value->uid : '',
            	'create_time' => isset($value->create_time) ? $value->create_time : '',
            	'use_time' => isset($value->use_time) ? $value->use_time : '',
            	'point' => isset($value->point) ? $value->point : '',
            );
            $res = $csv->writeData($result);
            unset($result);
        }
        $res = $csv->closeFile();
        if ($res){
            $data = array('now' => $now);
            return Response::json($data);
        } else{
            return Response::json($msg, 403);
        }
	}

	public function joyCardDownloadIndex(){
        $now = Input::get('now');
        $file = storage_path() . "/cache/" . $now . ".csv";
        $data = array(
                'content' => View::make('download', 
                        array(
                                'file' => $file
                        ))
        );
        return View::make('main', $data);
    }

	public function randNum($str,$n)
	{
		$res = '';
		$l = strlen($str);
		for($i = 0;$i < $n;$i ++){
			$res = $res . $str[mt_rand(0,$l - 1)];
		}
		return $res;
	}

	public function game_packageIndex(){	//查看现有游戏包--手游
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);

		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		$response = $api->get_game_package($game_id, $platform_id, '');

		if ('200' != $response->http_code){
			$response = array();
		}else{
			$response = (array)($response->body);
			foreach ($response as &$result) {
				$result = (array)$result;
				foreach ($result as &$value) {
					if(preg_match("/{/", $value)){
						$value = json_decode($value);
						$value = (array)$value;
					}
				}
			}
		}


		$view = array(
			'content' => View::make('platformapi.payment.game_package',
				array(
					'data' => $response
					)),
			);
		return View::make('main', $view);
	}

	public function game_packageModifyIndex(){
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);

		if(2 != $game->game_type){
			return $this->show_message('401', 'Not a mobile game');
		}

		$package_id = Input::get('id');

		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		$response = $api->get_game_package($game_id, $platform_id, $package_id);

		if ('200' != $response->http_code){
			return $this->show_message($response->http_code, json_encode($response->body));
		}

		$data = $response->body;
		$data = (array)$data;

		$view = array(
			'content' => View::make('platformapi.payment.game_package_modify',
				array(
					'data' => $data
					)),
			);
		return View::make('main', $view);
	}

	public function game_packageAddModify(){	//提交修改给官网，等待官网接口
		$data = array(
			'id' => Input::get('id'),
			'package_name' => Input::get('package_name'),
			'fb' => Input::get('fb'),
			'game_id' => Session::get('game_id'),
			'google_play' => Input::get('google_play'),
			'apps_flyer' => Input::get('apps_flyer'),
			'chart_boost' => Input::get('chart_boost'),
			'adwords' => Input::get('adwords'),
			'gocpa' => Input::get('gocpa'),
			'os_type' => Input::get('os_type'),
			'extra1' => Input::get('extra1'),
			'extra2' => Input::get('extra2'),
			'sdk_ad_info' => Input::get('sdk_ad_info'),
			'time' => time(),
			);
		$platform_id = Session::get('platform_id');
		$platform = Platform::find($platform_id);
		$platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
		$result = $platform_api->modify_game_package($data);
		if(null == $data['id']){
			if('200' == $result->http_code){
				$response = array('msg' => '新增成功');
				return Response::json($response);
			}else{
				return $platform_api->sendResponse();
			}
		}else{
			if('200' == $result->http_code){
				$response = array('msg' => '修改成功');
				return Response::json($response);
			}else{
				return $platform_api->sendResponse();
			}
		}
		return;
	}

	public function game_packageAddNewIndex(){	//新增一条游戏包记录
		$data = array(
			'package_name' => '',
			'fb' => '',
			'google_play' => '',
			'apps_flyer' => '',
			'chart_boost' => '',
			'adwords' => '',
			'gocpa' => '',
			'os_type' => '',
			'extra1' => '',
			'extra2' => '',
			'sdk_ad_info' => '',
			);

		$view = array(
			'content' => View::make('platformapi.payment.game_package_modify',
				array(
					'data' => $data
					)),
			);
		return View::make('main', $view);
	}
/*
 *Google validate Modify & Add
 */
    public function ggvalidateData()
    {
    	$platform = Platform::find(Session::get('platform_id'));
    	$platform_id = Session::get('platform_id');
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$response = $api->ggvalidateData($game_id , $platform_id , '');
    	if('200' != $response->http_code and null==$response->body)
    	{
    		App::abort(404);
    	}
    	$data = $response->body;
    	$view = array(
    					'content' => View::make('platformapi.payment.ggvalidateShow',array(
    																						'data' => $data,
    																						'platform' => $platform,
    																						'game' => $game
    																					  )
    										   )
    				 );
    	return View::make('main',$view);
    }

    public function ggvalidateModify()
    {
		$platform_id = Session::get('platform_id');
    	$game_id = Session::get('game_id');
    	$package_id = Input::get('id');
    	$game = Game::find($game_id);
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$response = $api->ggvalidateData($game_id , $platform_id , $package_id);
    	if('200' != $response->http_code and null==$response->body)
    	{
    		App::abort(404);
    	}
    	$data = $response->body;
    	$data = (array)$data;
    	$view = array(
    					'content' => View::make('platformapi.payment.ggvalidateModify',array(
    																						'data' => $data,
    																					  )
    										   )
    				 );
    	return View::make('main',$view);
    }

    public function ggvalidateUpdate()
    {
    	$data = array(
			'id' => Input::get('id'),
			'package_name' => Input::get('package_name'),
			'game_id' => Session::get('game_id'),
			'refresh_token' => Input::get('refresh_token'),
			'data_client_id' => Input::get('client_id'),
			'data_client_secret' => Input::get('client_secret'),
			'time' => time(),
			);

    	$platform_id = Session::get('platform_id');
		$platform = Platform::find($platform_id);
		$platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);

		$result = $platform_api->ggvalidateModify($data);

		if(('200' == $result->http_code) && (isset($result->body->stat)) && ('success' == $result->body->stat))
		{

			if(null == $data['id'])
			{
				return Response::json(array('msg' => '新增成功'));
			}
			else
			{
				return Response::json(array('msg' => '修改成功'));
			}
		}
		else
		{
			return $platform_api->sendResponse();
		}
    }

    public function ggvalidateAdd()
    {
		$data = array(
			'package_name' => '',
			'game_id' => '',
			'refresh_token' => '',
			'client_id' => '',
			'client_secret' => '',
			);
		$view = array(
			'content' => View::make('platformapi.payment.ggvalidateModify',array(
																				'data' => $data
																				)
									)
					 );
		return View::make('main', $view);
    }

//third_product modify & add
    public function thirdproductData()
    {
    	$platform_id = Session::get('platform_id');
    	$platform = Platform::find($platform_id);
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$response = $api->thirdproductData($game_id, $platform_id , '');
    	$data = $response->body;
    	if('200' != $response->http_code)
    	{
    		App::abort(404);
    	}
    	$view = array(
    					'content' => View::make('platformapi.payment.thirdproductShow',array(
    																						'data' => $data,
    																						'platform' => $platform,
    																						'game' => $game
    																					  )
    										   )
    				 );
    	return View::make('main',$view);
    }

    public function thirdproductModify()
    {
    	$id = Input::get('id');
    	if(null == $id)
    	{
    		$data = array(
    		  		        'id' => '',
    		  		        'package_name' => '',
    		  		  	 	'product_type' => '',
    		  		   		'third_product_id' => '',
    		  		  	 	'game_id' => '',
    		  		   		'payment_id' => '',
    		  		   		'currency_id' => '',
    		  		   		'pay_amount' => '',
    				    );
    	}
    	else
    	{
    		$platform_id = Session::get('platform_id');
    		$game_id = Session::get('game_id');
    		$game = Game::find($game_id);	
    		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    		$response = $api->thirdproductData($game_id, $platform_id , $id);   	
    		$data = (array)$response->body;
    		if('200' != $response->http_code)
    		{
    			App::abort(404);
    		}
    	}
    	$view = array(
    					'content' => View::make('platformapi.payment.thirdproductModify',array(
    																						'data' => $data,
    																					    )
    										   )
    				 );
    	return View::make('main',$view);
    }

    public function thirdproductAdd()
    {
    	$data = array(
    		  		   'id' => '',
    		  		   'package_name' => '',
    		  		   'product_type' => '',
    		  		   'third_product_id' => '',
    		  		   'game_id' => '',
    		  		   'payment_id' => '',
    		  		   'currency_id' => '',
    		  		   'pay_amount' => '',
    				 );
    	$view = array(
    					'content' => View::make('platformapi.payment.thirdproductModify',array(
    																						'data' => $data,
    																					    )
    										   )
    				 );
    	return View::make('main',$view);
    }

    public function thirdproductUpdate()
    {
    	$id = Input::get('id');
    	if(!$id)
    	{
    		$id = '';
    	}
    	$data = array(
    		  		   'id' => $id,
    		  		   'package_name' => Input::get('package_name'),
    		  		   'product_type' => Input::get('product_type'),
    		  		   'third_product_id' => Input::get('third_product_id'),
    		  		   'game_id' => Input::get('game_id'),
    		  		   'payment_id' => Input::get('payment_id'),
    		  		   'currency_id' => Input::get('currency_id'),
    		  		   'pay_amount' => Input::get('pay_amount'),
    		  		   'time' => time()
    				 );
    	$platform_id = Session::get('platform_id');
    	$platform = Platform::find(Session::get('platform_id'));
    	$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$result = $api->thirdproductModify($data);
		unset($data['time']);

		if(('200' == $result->http_code) && (isset($result->body->stat)) && ('success' == $result->body->stat))
		{
			// $game_id = Session::get('game_id');
			// $game = Game::find($game_id);
			// $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
   //  	    $response = $api->thirdproductUpdate($game_id, $platform_id , $data['id'] , $data);
			if('' == $data['id'])
			{
				return Response::json(array('msg' => '新增成功'));
			}
			else
			{
				return Response::json(array('msg' => '修改成功'));
			}
		}
		else
		{
			return $api->sendResponse();
		}
    }

//payment_method modify & add
    public function paymentMethodData()
    {
    	$result = DB::table('mobile_payment_method')->get();
    	$view = array( 'content' => View::make('platformapi.payment.paymentMethodShow',
    					array('data' => $result))
    				 );
    	return View::make('main',$view);
    }

    public function paymentMethodModify()
    {   
    	$result = DB::table('mobile_payment_method')->where('payment_id', Input::get('id'))->first();
    	if(null != $result)
    	{
    		$data = array(
    					'payment_id' => Input::get('id'),
    					'method_name' => $result->method_name,
    					'pay_type' => $result->pay_type,
    					'pay_lib' => $result->pay_lib
    		 		   );
    	}
    	else
    	{
    		$data = array(
    					'payment_id' => '',
    					'method_name' => '',
    					'pay_type' => '',
    					'pay_lib' => ''
    		 		   );
    	}
    	$view = array( 'content' => View::make('platformapi.payment.paymentMethodModify',
    					array('data' => $data))
    				 );
    	return View::make('main',$view);
    }

    public function paymentMethodAdd()
    {
    	$data = array(
    					'payment_id' => '',
    					'method_name' => '',
    					'pay_type' => '',
    					'pay_lib' => ''
    		 		 );
    	$view = array( 'content' => View::make('platformapi.payment.paymentMethodModify',
    					array('data' => $data))
    				 );
    	return View::make('main',$view);
    }

    public function paymentMethodUpdate()
    {
    	$data = Input::all();
    	$result = DB::table('mobile_payment_method')->where('method_name',$data['method_name'])->where('pay_type',$data['pay_type'])->where('pay_lib',$data['pay_lib'])->first();
    	if(isset($result))
    	{
    		return Response::json(array('msg' => '此数据已经存在，请勿重复输入！'));
    	}
    	else
    	{
    		if( NULL == Input::get('payment_id'))
    		{
    			$result = DB::table('mobile_payment_method')->insert($data);
    			if($result)
    			{
    				return Response::json(array('msg' => '新增成功'));
    			}
    			else
    			{
    				return Response::json(array('msg' => '新增失败'));
    			}
    		}
    		else
    		{
    			$exist_id = DB::table('mobile_payment_method')->where('payment_id',$data['payment_id'])->first();
    			if(null == $exist_id) 
    			{
    				return Response::json(array('msg' => '此ID不存在，请输入有效的ID'));
    			} 			
    			else
    			{
    				$result = DB::table('mobile_payment_method')->where('payment_id',$data['payment_id'])->update($data);
    				if(1 == $result)
    				{
    					return Response::json(array('msg' => '修改成功'));
    				}
    				else
    				{
    					return Response::json(array('msg' => '修改失败'));
    				}
    			}
    		}
        }
    }

    public function paymentMethodQueryview()
    {
    	$data = array(
    					'query_data' => ''
    		 		 );
    	$view = array( 'content' => View::make('platformapi.payment.paymentMethodQuery',
    					array('data' => $data))
    				 );
    	return View::make('main',$view);
    }

    public function paymentMethodQuery()
    {
    	$query_type = Input::get('query_type');
    	$query_data = Input::get('query_data');
    	if('0' == $query_type)
    	{
    		$type_name = 'id';
    		$result = DB::table('mobile_payment_method')->where('payment_id',$query_data)->get();
    	}
    	elseif('1' == $query_type) 
    	{
    		$type_name = 'method_name';
    		$result = DB::table('mobile_payment_method')->where('method_name',$query_data)->get();
    	}
    	elseif ('2' == $query_type) 
    	{
    		$type_name = 'pay_type';
    		$result = DB::table('mobile_payment_method')->where('pay_type',$query_data)->get();
    	}
    	else
    	{
    		$type_name = 'pay_lib';
    		$result = DB::table('mobile_payment_method')->where('pay_lib',$query_data)->get();
    	}

    	$data = (array)$result;
    	if (!empty($result)) {
    		return Response::json($data);
    	}
    	else
    	{
    		return Response::json(array('msg' => '没有此 '.$type_name.' !!!'));
    	}
    }

    public function modifyTradeseq(){
        $game = Game::find(Session::get('game_id'));
        $order_sn = Input::get('order_sn');
        $tradeseq = Input::get('tradeseq');
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
        $data = array(
            'order_sn' => $order_sn,
            'tradeseq' => $tradeseq,
            'time' => time(),
        );
        $response = $api->modifyTradeseq($data);
        if ($response->http_code == 200 && $response->body)
        {   
            if($response->body->error == 0){
                return Response::json($response->body);
            }
            else{
                return Response::json($response->body, 403);
            }
        } else
        {
            return Response::json($response->body, $response->http_code);
        }
    }

    public function confirmYuanbao(){
        $game = Game::find(Session::get('game_id'));
        $order_sn = Input::get('order_sn');
        $platform = Platform::find(Session::get('platform_id'));
        $offer_yuanbao = 1;
        $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
        $data = array(
            'order_sn' => $order_sn,
            'offer_yuanbao' =>$offer_yuanbao,
            'offer_time' => time(),
            'time' => time(),
        );
        $response = $api->confirmYuanbao($data);
        if ($response->http_code == 200 && $response->body)
        {   
            if($response->body->error == 0){
                return Response::json($response->body);
            }
            else{
                return Response::json($response->body, 403);
            }
        } else
        {
            return $api->sendResponse();
        }
    }
}
