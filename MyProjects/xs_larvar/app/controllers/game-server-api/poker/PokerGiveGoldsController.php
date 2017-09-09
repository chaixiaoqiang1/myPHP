<?php

class PokerGiveGoldsController extends \BaseController {

	/*
	  发送金币
	*/
	const SERVER_IP = "119.81.84.118";

	public function goldIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.golds'),
		);
		return View::make('main', $data);
	}

	public function giveGold()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$rules = array(
			'golds' => 'required',
		);

		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}
		$golds = Input::get('golds');
		$player_id = Input::get('player_id'); 
		$uid = Input::get('player_uid');
		$player_name = Input::get('player_name');
		if ($player_name) {  //通过player_name发送
			$game = Game::find(Session::get('game_id'));
        	$game_id = Session::get('game_id');  //8
       		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        	$platform = Platform::find(Session::get('platform_id'));
        	if (! $platform) {
           		return Response::json($msg, 404);
        	}
        	$response = $api->getUserByPlayerName($platform->platform_id, $player_name, 1, $game_id, $tp_code = 'fb');
//         	$s = var_export($response,true);
//         	Log::info($s);
        	$v = $response->body[0];
            $player_id = $v->player_id;
		}

		
		if (!isset($player_id)  && !isset($uid)) {  //两个数据都没有
			return Response::json($msg, 403);
		}
		$server = Server::find(13);
		if (!$server) {
			return Response::json($msg, 403);
		}
		$api = PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
		$response = $api->giveGold($golds, $player_id, $uid);

		/*$s = var_export($response,true);
		Log::info($s);*/
		if (!isset($response->error_code)) {
			$response->result = "OK";
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
	}

	public function pokerChipsInfoIndex()
	{
		$servers = Server::currentGameServers()->get();
		$data = array(
			'content' => View::make('serverapi.poker.payment.chips-info'),
		);
		return View::make('main', $data);
	}

	public function pokerChipsInfoData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => '',
		);
		$rules = array(
			'chips' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);		
		}
		$chips = trim(Input::get('chips'));
		$chips = intval($chips);
		if ($chips < 10000) {
			$msg['error'] = Lang::get('error.chips_num_wrong');
			return Response::json($msg, 403);
		}
		$server = Server::find(13);
		//Log::info(var_export($server),true);
		if (!$server) {
			$msg['error'] = Lang::get('error.server_not_found');
			return Response::json($msg, 403);
		}
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$platform_id = Session::get('platform_id');
		$server_ip = "119.81.84.118";
		$api = PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
		$sl_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$response = $api->getPlayerChips($chips);
		if (isset($response->uids)) {
			$data = $response->uids;
			for ($i=0; $i < count($data); $i++) { 
				$data[$i]->nickname = '';
				$data[$i]->fb_id = '';
				$data[$i]->create_time = '';
				$data[$i]->is_recharge = '';
				$user = $sl_api->getPokerPlayerByUID($platform_id, $game_id, $data[$i]->uid);
				if (200 == $user->http_code && isset($user->body[0])) {
					$user_info = $user->body[0];
					$data[$i]->nickname = $user_info->player_name;
					$data[$i]->create_time = $user_info->created_time;
					$data[$i]->fb_id = $user_info->tp_user_id;
				}
				$pay = $sl_api->getPokerPayNums($platform_id, $game_id, $data[$i]->uid);
				if (200 == $pay->http_code && isset($pay->body[0])) {
					$pay_info = $pay->body[0];
					$data[$i]->recharge_num = $pay_info->pay_num; 
				}
			}
		}
		if (isset($data)) {
			return Response::json($data);
		}else{
			$msg['error'] = '没有数据';
			return Response::json($msg, 403);
		}

	}

	//德州录入订单

	public function addOrderIndex()
	{
		$platform_id = Session::get('platform_id');
		$payments = Payment::where('platform_id', $platform_id)->get();
		$currencies = Currency::all();
		$data = array(
			'content' => View::make('serverapi.poker.payment.add_order', 
				array(
					'payments' => $payments	,
					'currencies' => $currencies,
				)
			),
		);
		return View::make('main', $data);
	}

	public function addOrderData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'pay_type_id' => 'required',
			'pay_user_id' => 'required',
			'currency_code' => 'required',
			'pay_amount' => 'required',
			'basic_yuanbao_amount' => 'required',
			'extra_yuanbao_amount' => 'required',
			'huodong_yuanbao_amount' => 'required',
			'yuanbao_amount' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$server = Server::find(13);
		$pay = trim(Input::get('pay_type_id'));
		//$method_id = trim(Input::get('method_id'));
		$pay_array = explode('|', $pay);
		$pay_type_id = $pay_array[0];
		$method_id = $pay_array[1];
		$currency_code = Input::get('currency_code');
		$uid = trim(Input::get('pay_user_id'));
		$pay_amount = trim(Input::get('pay_amount'));
		$basic_yuanbao = trim(Input::get('basic_yuanbao_amount'));
		$extra_yuanbao = trim(Input::get('extra_yuanbao_amount'));
		$huodong_yuanbao = trim(Input::get('huodong_yuanbao_amount'));
		$total_yuanbao = trim(Input::get('yuanbao_amount'));
		$platform_id = Session::get('platform_id');
		$platform = Platform::find($platform_id);
		$game_id = Session::get('game_id');
		$platform->payment_api_url = 'http://payid.joyspade.com' ;
		$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
		$order = array(
			'server_id' => $server->platform_server_id,
			'game_id' => $game_id,
			'pay_type_id' => $pay_type_id,
			'method_id' => $method_id,
			'pay_user_id' => $uid,
			'currency' => $currency_code,
			'pay_amount' => $pay_amount,
			'basic_yuanbao_amount' => $basic_yuanbao,
			'extra_yuanbao_amount' => $extra_yuanbao,
			'huodong_yuanbao_amount' => $huodong_yuanbao,
			'total_yuanbao_amount' => $total_yuanbao,
		);
		var_dump($order);die();
		$resposne = $api->addPokerOrder($order);

	}
	//登录时长
	public function loginPlayersIndex()
	{
		//var_dump('ere');
		$data = array(
			'content' => View::make('serverapi.poker.users.time'),
		);
		return View::make('main', $data);
	}

	public function loginPlayersData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$server = Server::find(13);
		if (!$server) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));
		//select l1.login_time as time1  , l2.login_time as time2  , l1.player_id, l1.is_login, l2.is_login  from log_login l1 left join  log_login l2 on l1.player_id = l2.player_id where l1.is_login = 1 and l2.is_login = -1 
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$platform_id = Session::get('platform_id');
		$server = Server::find(13);
		if (!$server) {
			$msg['error'] = Lang::get('serverapi.server_not_found');
			return Response::json($msg, 403);
		}
		$server_internal_id = $server->server_internal_id;
		
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$days = ceil(($end_time - $start_time)/86400);
		$data_arr = array();
		$start = $start_time;
		$data = array();
		for ($i=0; $i < $days; $i++) {
			$end = $start+ 86399;
			$response = $api->LoginPlayersData($game_id, $server_internal_id, $start, $end);
			if ($response->http_code == 200 && isset($response->body)) {
				$body = $response->body;
					$data[] = array(
						'date' => date("Y-m-d", $start),
						'num1' => isset($body->num1) ? $body->num1 : 0,
						'num2' => isset($body->num2) ? $body->num2 : 0,
						'num3' => isset($body->num3) ? $body->num3 : 0,
						'num4' => isset($body->num4) ? $body->num4 : 0,
						'num5' => isset($body->num5) ? $body->num5 : 0,
						'num6' => isset($body->num6) ? $body->num6 : 0,
					);
				}
			$start += 86400;
		}
		$arr = array();
		if (isset($data)) {
			for ($i=count($data)-1; $i >= 0 ; $i--) { 
				$arr[] = $data[$i];
			}
		}

		return Response::json($arr);
	}

	/*
	created_by xianshui 2014.11.12 玩家筹码变化
	*/

	private function initTable()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/economy_mid.txt');//allserver_giftbag.txt
        return $table;
    }

	public function chipsRangeIndex()
	{
		$table = $this->initTable();
        
        $mid = $table->getData();
		$data = array(
			'content' => View::make('serverapi.poker.users.chips-range',array(
				'mid' => $mid
			))
			
		);
		return View::make('main', $data);
	}

	public function chipsRangeData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$player_name = Input::get('player_name');
		$player_id = Input::get('player_id');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$sort = Input::get('sort');
		$mid = Input::get('mid');
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$server = Server::find(13);
		$per_page = (int) Input::get('per_page');
		$page = (int)Input::get('page');
		$page = $page > 0 ? $page : 1;
		if (!$server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$server_internal_id = $server->server_internal_id;
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if (isset($player_name)) {
			$player = $api->getIdByName2($platform_id, $game_id, $server_internal_id,$player_name, '');
			if ($player->http_code == 200 && isset($player->body)) {
				$body = $player->body[0];
				$player_id = $body->player_id;
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
		}

		if (isset($player_id)) {
		 	$response = $api->chipsRangeData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $sort, $mid, $page, $per_page);
			if ($response->http_code == "200") {
				$body = $response->body;
				return Response::json($body);
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
		} 

		if($mid){
			$group_by = Input::get('group_by');
		 	$response = $api->chipsRangeData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $sort, $mid, $page, $per_page, $group_by);
			if ($response->http_code == "200") {
				$body = $response->body;
				return Response::json($body);
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}				
		}

		$msg['error'] = Lang::get('error.basic_input_error');
		return Response::json($msg, 403);

	}

	/*
	玩家牌局查看 created by 仙水 2014.11.12
	*/
	public function roundsRangeIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.users.rounds')
		);
		return View::make('main', $data);
	}

	public function roundsRangeData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''	
		);
		$player_name = Input::get('player_name');
		$player_id = Input::get('player_id');
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));
		$page = (int) Input::get('page');
		$page = $page > 0 ? $page : 1;
		$per_page = Input::get('per_page');
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$server = Server::find(13);
		if (!$server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$server_internal_id = $server->server_internal_id;
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if ($player_name != '') {
			$player = $api->getIdByName2($platform_id, $game_id, $server_internal_id,$player_name, '');
			if ($player->http_code == 200 && isset($player->body)) {
				$body = $player->body[0];
				$player_id = $body->player_id;
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
		}
		if (isset($player_id)) {
			$response = $api->roundsRangeData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $page, $per_page);
			if ($response->http_code == "200") {
				$body = $response->body;
				// Log::info("TaishouTEST");
				$body = (array)$body;
				for($i=0;isset($body['items'][$i]);$i++){
					$body['items'][$i] = (array)$body['items'][$i];
				}
				$num = count((array)$body['items']);
				for($i=0;$i<$num;$i++){
					if(!$body['items'][$i]['public_cards']) continue;
					$public_card = explode("|",$body['items'][$i]['public_cards']);
					$public_cards = "";
					$hua = array("♠","♥","♣","♦");
					//$hua = array(chr(06),chr(03),chr(05),chr(04));
					$zhi = array("J","Q","K","A");
					for($j=0;$j<count($public_card);$j++){
						$n = $hua[((int)$public_card[$j]/13)];
						$m = (int)$public_card[$j]%13<9?(int)$public_card[$j]%13 + 2:$zhi[(int)$public_card[$j]%13-9];
        				$public_card[$j] = $n.$m;
        				$public_cards = $public_cards.$public_card[$j];
        				if($j != count($public_card)-1){
        					$public_cards = $public_cards."|";
        				}
        			}
        			$body['items'][$i]['public_cards'] = $public_cards;
        		}
				//Log::info(var_export($body, true));
				return Response::json($body);
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
		}
	}


	/*
	created_by hlcai 2015.01.19 玩家登陆变化
	*/

	public function playerLoginIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.users.player_login')
		);
		return View::make('main', $data);
	}

	public function playerLoginData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$player_name = Input::get('player_name');
		$player_id = Input::get('player_id');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$server = Server::find(13);
		$per_page = (int) Input::get('per_page');
		$page = (int)Input::get('page');
		$page = $page > 0 ? $page : 1;
		if (!$server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$server_internal_id = $server->server_internal_id;
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if (isset($player_name)) {
			$player = $api->getIdByName2($platform_id, $game_id, $server_internal_id,$player_name, '');
			if ($player->http_code == 200 && isset($player->body)) {
				$body = $player->body[0];
				$player_id = $body->player_id;
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
		}

		if (isset($player_id)) {
		 	$response = $api->playerLoginData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $page, $per_page);
			if ($response->http_code == "200") {
				$body = $response->body;
				return Response::json($body);
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
		} 
	}

	public function chipsRecordIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.users.chips_record')
		);
		return View::make('main', $data);
	}

	public function chipsRecordDate()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$player_name = Input::get('player_name');
		$player_id = Input::get('player_id');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$server = Server::find(13);
		$per_page = (int) Input::get('per_page');
		$page = (int)Input::get('page');
		$page = $page > 0 ? $page : 1;
		if (!$server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$server_internal_id = $server->server_internal_id;
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if (isset($player_id)) {
		 	$response = $api->chipsRecordData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $page, $per_page);
			if ($response->http_code == "200") {
				$body = $response->body;
				return Response::json($body);
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
		} 
		elseif (isset($player_name)) {
			$response = $api->chipsRecordData2($game_id, $server_internal_id, $player_name, $start_time, $end_time, $page, $per_page);
			if ($response->http_code == "200") {
				$body = $response->body;
				return Response::json($body);
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
		}
	}


}