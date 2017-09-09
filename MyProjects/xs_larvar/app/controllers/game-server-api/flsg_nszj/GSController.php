<?php
class GSController extends \BaseController {
	public function index()
	{
		$servers = Server::currentGameServers()->get();
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.gs.index', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function load()
	{
		$msg = array(
				'error' => Lang::get('error.basic_input_error')
		);
		$type = Input::get('type');
		if($type == 'setSuperGM')
		{ // 设置超级GM
			$server_id = ( int ) Input::get('server_id1');
			$server = Server::find($server_id);
			if(! $server)
			{
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$player_name = Input::get('player_name1');
			$player = $api->getPlayerInfoByName($player_name);
			if(! isset($player->player_id))
			{
				$msg['error'] = Lang::get('serverapi.player_not_found');
				return Response::json($msg, 404);
			}
			$is_super_gm = ( int ) Input::get('is_super_gm') == 1 ? true : false;
			$response = $api->setSuperGM($is_super_gm, $player->player_id);
			if(! isset($response->error_code))
			{
				return $api->sendResponse();
			} else
			{
				$msg['error'] = Lang::get('error.contact_luluxiu');
				return Response::json($msg, 404);
			}
		} else if($type == 'setSuperCustomer')
		{ // 设置超级VIP
			$server_id = ( int ) Input::get('server_id2');
			$server = Server::find($server_id);
			if(! $server)
			{
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$player_name = Input::get('player_name2');
			$player = $api->getPlayerInfoByName($player_name);
			
			if(! isset($player->player_id))
			{
				$msg['error'] = Lang::get('serverapi.player_not_found');
				return Response::json($msg, 404);
			}
			$is_super_customer = ( int ) Input::get('is_super_customer') == 1 ? true : false;
			$response = $api->setSuperCustomer($is_super_customer, $player->player_id);
			if(! isset($response->error_code))
			{
				return $api->sendResponse();
			} else
			{
				$msg['error'] = Lang::get('error.contact_luluxiu');
				return Response::json($msg, 404);
			}
		} else if($type == 'contact')
		{ // 修改对应关系
			$server_id = ( int ) Input::get('server_id3');
			$server = Server::find($server_id);
			if(! $server)
			{
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$player_name1 = Input::get('player_name31');
			$player_name2 = Input::get('player_name32');
			$player1 = $api->getPlayerInfoByName($player_name1);
			$player2 = $api->getPlayerInfoByName($player_name2);
			
			if(! isset($player1->player_id) || ! isset($player2->player_id))
			{
				$msg['error'] = Lang::get('serverapi.player_not_found');
				return Response::json($msg, 404);
			}
			$add_or_remove = ( int ) Input::get('gs_add_or_remove');
			if($add_or_remove)
			{
				$response = $api->addGSContact($player1->player_id, $player2->player_id);
			} else
			{
				$response = $api->removeGSContact($player1->player_id, $player2->player_id);
			}
			if(! isset($response->error_code))
			{
				return $api->sendResponse();
			} else
			{
				$msg['error'] = Lang::get('error.contact_luluxiu');
				return Response::json($msg, 404);
			}
		}
	}


	//群雄争霸 跨服

	public function crossWarLordsIndex()
	{
		$servers = $this->getUnionServers();
		$period = array();
		/*foreach ($servers as $key => $value) {
			$api = GameServerApi::connect($value->api_server_ip, $value->api_server_port, $value->api_dir_id);
			$response = $api->loadGameMatchStatus(GameServerApi::MATCH_TYPE_WARLORD);
			if (isset($response->period)) {
				$period =[$response->period][] = (object) array(
					'server_name' => $value->server_name,
					'match' => $response
				);
			}else{
				$period[9999][] = array(
					'server_name' => $value->server_name,
					'match' => null;
				);
			}
		}
		ksort($period);*/
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.tournament.singlewar', array(
				'servers' => $servers,
				'period' => $period
			))
		);
		return View::make('main', $data);
	}

	//群雄争霸连接和开启
	public function crossWarLordsOpen()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		
		$rules = array(
			'start_time' => 'required',
			'server_id' => 'required',
			'server_id2' => 'required',
			//'num' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}

		$server_ids = Input::get('server_id');
		$server_id2 = Input::get('server_id2');
		$num = Input::get('num');
		$start_time = strtotime(trim(Input::get('start_time')));
		$game_id = Session::get('game_id');
		//$game_id = 4;//三国本地测试
		$game = Game::find($game_id);

		/*if (Cache::has('single-server-time')) {
			$singleServerTime = Cache::get('single-server-time');
			if ($start_time == '' || $start_time < $singleServerTime + 604800 ) {
				$msg['error'] = Lang::get('serverapi.warslords_time_wrong');
				return Response::json ( $msg, 403);
			}
		} */
		$main_server = Server::find($server_id2);
		if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port; 
		$result = array();
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			//建立连接
			$update_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_WARLORD, $host, $port, true);
			if (isset($update_response->result) && $update_response->result == 'OK') {
				$result[] = array(
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
					'status' => 'ok'
				);
			}else{
				$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->error . "\n",
						'status' => 'error'
				);
			}
		}
		//开启跨服争霸
		$main_api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
		$open_response = $main_api->openGameMatch(GameServerApi::MATCH_TYPE_WARLORD, $start_time);
		if (isset($open_response->result) && $open_response->result == 'OK') {
			//Cache::add('cross-warslords-time', $start_time , 100000);
			$result[] = array (
				'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_open') . ': ' . $open_response->result . "\n",
				'status' => 'ok'
			);
		}else{
			$result[] = array (
				'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_open') . ': ' . $open_response->error . "\n",
				'status' => 'error'
			);
		}
		//设置争霸届数
		if (isset($num) && $num > 0) {
			$set_response = $main_api->warsLordsSet($game->game_code,GameServerApi::MATCH_TYPE_WARLORD, intval($num));
			if (isset($set_response->result) && $set_response->result == 'OK') {
				$result[] = array(
					'status' => 'ok',
					'msg' => $main_server->server_name . '--' . $set_response->result . '--' .$num
				);
			}else{
				$result[] = array(
					'status' => 'error',
					'msg' => $main_server->server_name . '--' . $set_response->result . '--' .$num
				);
			}
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
	}

	//跨服争霸更新链接
	public function crossWarLordsUpdate()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);

		$rules = array(
			'server_id' => 'required',
			'server_id2' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get ( 'error.basic_input_error' );
			return Response::json ( $msg, 404 );
		}
		$server_ids = Input::get('server_id');
		$server_id2 = Input::get('server_id2');
		$main_server = Server::find($server_id2);
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port;
		$result = array();
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );	 
		 	}
		 	$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		 	$close_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_WARLORD, $host, $port, false);
		 	$update_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_WARLORD, $host, $port, true);
		 	if (isset($update_response->result) && $update_response->result == 'OK') {
		 	 	$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
					'status' => 'ok'
				);
		 	}else{
		 		$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->error . "\n",
						'status' => 'error'
				);
		 	} 	
		}
		$msg = array (
				'result' => $result
		);
		return Response::json($msg); 
	}

	//向比赛服报名
	public function crossWarLordsSignUp()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$server_ids = Input::get('server_id');
		$result = array();
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->requestWarLords(GameServerApi::MATCH_TYPE_WARLORD, intval(1));
			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array(
					'status' => 'ok',
					'msg' => '(' . $server->server_name .')' . Lang::get('serverapi.tournament_cross_signup') . ':' . $response->result . "\n"
				);
			}else{
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_signup') . ': ' . $response->error . "\n",
					'status' => 'error' 
				);
			}
			
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
	}

	//查询报名
	public function crossWarLordsSignLookUp()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id2' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$server_id = Input::get('server_id2');
		$main_server = Server::find($server_id);
		if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$game_id = Session::get('game_id');
		$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
		$response = $api->searchGameMatchOtherServerWL(GameServerApi::MATCH_TYPE_WARLORD, $game_id);
		//var_dump($response);die();
		if (isset($response->error)) {
			$msg['error'] = 'fail to lookup';
			return Response::json($msg, 404);
		}
		if (isset($response->list)) {
            $list = '';
            foreach($response->list as $v)
            {
                $server = Server::InternalServer($game_id, $v->server_id)->get();
                $list = $list . $server . ' ';
            }
			$result[] = array(
				'status' => 'ok',
				'msg' => ($response->tournament_type == 5 ? '群雄争霸---'. $response->tournament_type : $response->tournament_type) . '---Counter---' . $response->counter . '---List---' . $list
			);
			return Response::json ($result);
		}else{
			$msg['error'] = Lang::get( 'serverapi.tournament_lookup_none_in');
			return Response::json($msg, 404);
		}
	}

	//群雄争霸关闭
	public function crossWarLordsClose()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id2' => 'required',
			'id' => 'required',
			'password' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$id = Input::get('id');
		$password = Input::get('password');
		$server_id = Input::get('server_id2');
        //Log::info('111id:'.$id.'  password:'.$password.'  server_id:'.$server_id);
        $main_server = Server::find($server_id);
		if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
        //Log::info('222id:'.$id.'  password:'.$password.'  server_id:'.$server_id.'  server export:'.var_export($main_server, true));
		if ($id == '456' && $password == '456') {
            //Log::info('333');
			$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
            //Log::info('444');
            $response = $api->closeGameMatch(GameServerApi::MATCH_TYPE_WARLORD);
            //Log::info('555 response:'.var_export($response, true));
            if (isset($response->result) && $response->result == 'OK') {
				Cache::forget ( 'cross-server-time' );
				$result[] = array(
					'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_close') . ': ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
				$result[] = array (
					'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_close') . ': ' . $response->error . "\n",
					'status' => 'error' 
				);
			}
		}else {
			$result[] = array (
				'msg' => 'Password is wrong !!!',
				'status' => 'error' 
			);
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);

	}

	public function crossWarLordsLookUp()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id' => 'required'
		);
		$game = Game::find(Session::get('game_id'));
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$server_ids = Input::get('server_id');
		$result = array();
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Resposne::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->lookUpCrossServer();
			if (isset($response->servers)) {
				$servers = $response->servers;
				foreach ($servers as $key => $val) {
					if ($val->tournament_type == 5) {
						$server_name = Server::whereRaw("api_server_ip = '{$val->host}' and match_port = {$val->port}")->pluck('server_name');
						if($game->game_code == 'flsg' && $value->check_result == true && $value->check_time > 0 && $value->status == 'Connected'){
							$connect_status = '连接并确认';
						}elseif($game->game_code == 'flsg' && $value->check_result == false && $value->check_time == 0 && $value->status == 'Connected'){
							$connect_status = '连接未确认';
						}else{
							$connect_status = $value->status;
						}
						$result[] = array(
							'status' => 'ok',
							'msg' => $server->server_name .'--'.(($val->active == true)? '已开启' : '已关闭') .'--连接到--'. $server_name.'--'.$val->active."--host：" . ($val->host) .'--端口：'. ($val->port) .'--状态：'. ($connect_status ) .'--类型：'.$val->tournament_type
						);
					}
				}
			}else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . '查看链接' . ': ' . 'error',
					'status' => 'error' 
				);
			}
		}
		return Response::json($result);
	}

	//设置劫数
	public function crossWarLordsSet()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'num' => 'required',
			'server_id2' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$num = Input::get('num');
		$server_id2 = Input::get('server_id2');
		$main_server = Server::find($server_id2);
		if (!$main_server) {
		 	$msg['error'] = Lang::get('error.basic_not_found');
		 	return Response::json($msg, 403);
		}
		$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
		$response = $api->warsLordsSet(GameServerApi::MATCH_TYPE_WARLORD, intval($num));
		$result = array();
		if (isset($response->result) && $response->result == 'OK') {
			$result[] = array(
				'status' => 'ok',
				'msg' => $main_server->server_name . '--' . $response->result . '--' .$num
			);
		}else{
			$result[] = array(
				'status' => 'error',
				'msg' => $main_server->server_name . '--' . $response->result . '--' .$num
			);
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
	}

    //天下第一 跨服
    public function crossWorldLordsIndex()
	{
		$servers = $this->getUnionServers($no_skip=1);
		$game = Game::find(Session::get('game_id'));
		$period = array();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.tournament.crossworld', array(
				'servers' => $servers,
				'period' => $period,
				'game_code' => $game->game_code
			))
		);
		return View::make('main', $data);
	}
	//天下第一连接和开启&风流三国武将PK
	public function crossWorldLordsOpen()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		
		$rules = array(
			'start_time' => 'required',
			'server_id' => 'required',
			'server_id2' => 'required',
			'match_type' => 'required',
			//'num' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}

        $match_type = (int)Input::get('match_type');
        if (0 == $match_type) {
            $msg['error'] = Lang::get('serverapi.not_select_match');
            return Response::json($msg, 403);
        }
        $server_ids = Input::get('server_id');
		$server_id2 = Input::get('server_id2');
		//$player_id = Input::get('player_id');
		$start_time = strtotime(trim(Input::get('start_time')));
		$main_server = Server::find($server_id2);

		if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port; 
		$result = array();
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			//建立连接
			$update_response = $api->updateGameMatch($match_type, $host, $port, true);
			if (isset($update_response->result) && $update_response->result == 'OK') {
				$result[] = array(
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
					'status' => 'ok'
				);
			}else{
				$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->error . "\n",
						'status' => 'error'
				);
			}
		}
		//开启天下第一&风流三国武将PK
		$main_api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
		$open_response = $main_api->openGameMatch($match_type, $start_time);
		if (isset($open_response->result) && $open_response->result == 'OK') {
			//Cache::add('cross-warslords-time', $start_time , 100000);
			$result[] = array (
				'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_open') . ': ' . $open_response->result . "\n",
				'status' => 'ok'
			);
		}else{
			$result[] = array (
				'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_open') . ': ' . $open_response->error . "\n",
				'status' => 'error'
			);
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
		
	}
	//天下第一更新链接&风流三国武将PK
	public function crossWorldLordsUpdate()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);

		$rules = array(
			'server_id' => 'required',
			'server_id2' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get ( 'error.basic_input_error' );
			return Response::json ( $msg, 404 );
		}

        $match_type = (int)Input::get('match_type');
        if (0 == $match_type) {
            $msg['error'] = Lang::get('serverapi.not_select_match');
            return Response::json($msg, 403);
        }
		$server_ids = Input::get('server_id');
		$server_id2 = Input::get('server_id2');
		$main_server = Server::find($server_id2);
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port;
		$result = array();
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );	 
		 	}
		 	$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		 	//建立连接
		 	$close_response = $api->updateGameMatch($match_type, $host, $port, false);
		 	$update_response = $api->updateGameMatch($match_type, $host, $port, true);
		 	if (isset($update_response->result) && $update_response->result == 'OK') {
		 	 	$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
					'status' => 'ok'
				);
		 	}else{
		 		$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->error . "\n",
						'status' => 'error'
				);
		 	} 	
		}
		$msg = array (
				'result' => $result
		);
		return Response::json($msg); 
	}
	//向比赛服报名
	public function crossWorldLordsSignUp()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'gift_data' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
        $match_type = (int)Input::get('match_type');
        if (0 == $match_type) {
            $msg['error'] = Lang::get('serverapi.not_select_match');
            return Response::json($msg, 403);
        }
		$gift_datas = Input::get('gift_data');
        $gift_datas = explode("\n", $gift_datas);
        foreach ($gift_datas as &$v) {
            $v = trim($v);
        }
        unset($v);
        $gift_datas = array_unique($gift_datas);
		$result = array();
		$ok = array();
        $error = array();

		foreach ($gift_datas as $gift_data) {
            $gift_data = explode("\t", $gift_data, 2);
            if (count($gift_data) != 2) {
                $error[] = $gift_data[0] . ': No Server Name. ';
                continue;
            }
            $server_name = trim($gift_data[1]);
            $server = Server::currentGameServers()->where('server_track_name', $server_name)->first();
            if (! $server) {
                $error[] = $gift_data[0] . "($server_name) Server Not Found. ";
                continue;
            }
            //$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			//$player_id = 0;
            //$player_ids = array();
            //$player_id =$gift_data[0];
            //$player_id = (int) $gift_data[0];
            $player_id =$gift_data[0];
           /* $player_ids = array(
                $player_id
            );*/
           /* Log::info(var_export($player_id,true));
 			Log::info(var_export("ip:" . $server->api_server_ip . "port:" . $server->api_server_port . "id:" . $server->api_dir_id,true));*/
 			$game_id = Session::get('game_id');
 			$game = Game::find($game_id);
        	$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->requestWorldLords($match_type, $player_id, $game_id, $game->game_code);
            if (isset($response->result) && $response->result == 'OK') {
				$result[] = array(
					'status' => 'ok',
					'msg' => '(' . $server->server_name .')' . $player_id . Lang::get('serverapi.tournament_world_signup') . ':' . $response->result . "\n"
				);
			}else{
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . $player_id . Lang::get('serverapi.tournament_world_signup') . ': ' . $response->error . "\n",
					'status' => 'error' 
				);
			}
			
		}
		$msg = array (
			'result' => $result
		);
        if (count($error) > 0) {
            $msg['error'] = $error;
            return Response::json($msg, 403);
        }
		return Response::json($msg);
	}
	//天下第一查询报名
	public function crossWorldLordsSignLookUp()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id2' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
        $match_type = (int)Input::get('match_type');
        if (0 == $match_type) {
            $msg['error'] = Lang::get('serverapi.not_select_match');
            return Response::json($msg, 403);
        }
		$server_id = Input::get('server_id2');
		$main_server = Server::find($server_id);
		if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
		$response = $api->searchGameMatchOtherServer($match_type, $game_id ,$game->game_code);
		//var_dump($response);die();
		//Log::info(var_export($response,true));
		if (isset($response->error)) {
			$msg['error'] = 'fail to lookup';
			return Response::json($msg, 404);
		}
		if (isset($response->counter)) {
			$result[] = array(
				'status' => 'ok',
				'msg' => ($response->tournament_type == $match_type ? '天下第一/王者之战---'. $response->tournament_type : $response->tournament_type) . '---Counter---' . $response->counter
			);
			return Response::json ($result);
		}else{
			$msg['error'] = Lang::get( 'serverapi.tournament_lookup_none');
			return Response::json($msg, 404);
		}
	}
	//天下第一关闭&风流三国武将PK
	public function crossWorldLordsClose()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id2' => 'required',
			'id' => 'required',
			'password' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
        $match_type = (int)Input::get('match_type');
        if (0 == $match_type) {
            $msg['error'] = Lang::get('serverapi.not_select_match');
            return Response::json($msg, 403);
        }
		$id = Input::get('id');
		$password = Input::get('password');
		$server_id = Input::get('server_id2');
		$main_server = Server::find($server_id);
		if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		if ($id == '456' && $password == '456') {
			$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
			$response = $api->closeGameMatch($match_type);
			if (isset($response->result) && $response->result == 'OK') {
				Cache::forget ( 'cross-server-time' );
				$result[] = array(
					'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_close') . ': ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
				$result[] = array (
					'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_close') . ': ' . $response->error . "\n",
					'status' => 'error' 
				);
			}
		}else {
			$result[] = array (
				'msg' => 'Password is wrong !!!',
				'status' => 'error' 
			);
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);

	}
	//天下第一查看链接&风流三国武将PK
	public function crossWorldLordsLookUp()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id' => 'required'
		);
		$game = Game::find(Session::get('game_id'));
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$match_type = (int)Input::get('match_type');
		if (0 == $match_type) {
		    $msg['error'] = Lang::get('serverapi.not_select_match');
		    return Response::json($msg, 403);
		}
		$server_ids = Input::get('server_id');
		$result = array();
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Resposne::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->lookUpCrossServer();
			if (isset($response->servers)) {
				$servers = $response->servers;
				$other_type = 1;
				foreach ($servers as $key => $val) {
					if ($val->tournament_type == $match_type) {
						$server_name = Server::whereRaw("api_server_ip = '{$val->host}' and match_port = {$val->port}")->pluck('server_name');
						if($game->game_code == 'flsg' && isset($val->check_result) && $val->check_result == true && isset($val->check_time) && $val->check_time > 0 && isset($val->status) && $val->status == 'Connected'){
							$connect_status = '连接并确认';
						}elseif($game->game_code == 'flsg' && isset($val->check_result) && $val->check_result == false && isset($val->check_time) && $val->check_time == 0 && isset($val->status) && $val->status == 'Connected'){
							$connect_status = '连接未确认';
						}else{
							$connect_status = isset($val->status) ? $val->status : 'unknow';
						}
						$result[] = array(
							'status' => 'ok',
							'msg' => $server->server_name .'--'.(($val->active == true)? '已开启' : '已关闭') .'--连接到--'. $server_name.'--'.$val->active."--host：" . ($val->host) .'--端口：'. ($val->port) .'--状态：'. ($connect_status ) .'--类型：'.$val->tournament_type
						);
						$other_type = 0;
						break;
					}
				}
				if(1 == $other_type){
					$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . 'NOT Found match type',
						'status' => 'error' 
					);
				}
			}else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . '查看链接' . ': ' . 'error',
					'status' => 'error' 
				);
			}
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json ($msg);
	}
	//武将PK（和跨服争霸类似，部分方法使用天下第一的）
	public function crossServerPK()
	{
		$servers = $this->getUnionServers($no_skip=1);
		$game = Game::find(Session::get('game_id'));
		$data = array (
			'content' => View::make ( 'serverapi.flsg_nszj.tournament.crossserver_pk', array (
				'servers' => $servers,
				'game_code' => $game->game_code
			)) 
		);
		return View::make('main', $data);
	}
	//武将PK根據當前配置重置所有比賽服連接未测试
	public function crossServerAllUpdate()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id2' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$server_id = Input::get('server_id2');

		$result = array();
		
		$server = Server::find($server_id);
		if (!$server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Resposne::json($msg, 403);
		}
		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		$all_update = $api->resetAllGameMatch();
		if (isset($all_update->result) && $all_update->result == 'OK') {
		 	 	$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . '重置' . $all_update->result . "\n",
					'status' => 'ok'
				);
	 	}else{
	 		$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . '重置' . 'error' . "\n",
					'status' => 'error'
			);
	 	} 	
		$msg = array (
				'result' => $result
		);
		return Response::json($msg); 
	}

	/**
	 * 武将pk报名
	 */
	public function crossServerPKSignup()
	{
		$server_ids = Input::get ('server_id');
		$match_type = (int)Input::get('match_type');
		if (0 == $match_type) {
		    $msg['error'] = Lang::get('serverapi.not_select_match');
		    return Response::json($msg, 403);
		}
		$result = array ();
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json ( $msg, 404 );
			}

			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

			$response = $api->requestGameMatch($match_type);

			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . '报名' . ': ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . '报名' . ': ' . $response->error . "\n",
					'status' => 'error' 
				);
			}
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
	}
	
	//武将pk报名查看
	public function crossServerPKLookup()
	{
		
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id2' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$match_type = (int)Input::get('match_type');
		if (0 == $match_type) {
		    $msg['error'] = Lang::get('serverapi.not_select_match');
		    return Response::json($msg, 403);
		}
		$server_id = Input::get('server_id2');
		$main_server = Server::find($server_id);
		if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$game_id = Session::get('game_id');
		$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
		$response = $api->searchGameMatchOtherServerWL($match_type, $game_id);

		if (isset($response->list)) {
	        $list = '';
	        foreach($response->list as $v)
	        {
	            $server = Server::InternalServer($game_id, $v->server_id)->get();
	            $list = $list . $server . '-';
	        }
			$result[] = array(
				'status' => 'ok',
				'msg' => 'Counter:' . $response->counter . '--List:' . $list
			);
			$msg = array(
				'result' => $result
			);
			return Response::json ($msg);
		}else{
			return Response::json(array('error'=>'查询出错!'), 403);
		}
	}


    //天下第一特别版 跨服
    public function crossWorldXLordsIndex()
    {
        $servers = $this->getUnionServers();
        $period = array();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.tournament.crossworldX', array(
                'servers' => $servers,
                'period' => $period
            ))
        );
        return View::make('main', $data);
    }
    //天下第一特别版连接和开启
    public function crossWorldXLordsOpen()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );

        $rules = array(
            'start_time' => 'required',
            'server_id' => 'required',
            //'num' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }

        $server_ids = Input::get('server_id');
        $server_id2 = Input::get('server_id2');
        //$player_id = Input::get('player_id');
        $start_time = strtotime(trim(Input::get('start_time')));
        $main_server = Server::find($server_id2);
        if (!$main_server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 403);
        }
        $host = $main_server->api_server_ip;
        $port = $main_server->match_port;
        $result = array();
        foreach ($server_ids as $key => $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 403);
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            //建立连接
            $update_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_TIANXIA_DIYI_X, $host, $port, true);
            if (isset($update_response->result) && $update_response->result == 'OK') {
                $result[] = array(
                    'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
                    'status' => 'ok'
                );
            }else{
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->error . "\n",
                    'status' => 'error'
                );
            }
        }
        //开启天下第一
        $main_api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
        $open_response = $main_api->openGameMatch(GameServerApi::MATCH_TYPE_TIANXIA_DIYI_X, $start_time);
        if (isset($open_response->result) && $open_response->result == 'OK') {
            //Cache::add('cross-warslords-time', $start_time , 100000);
            $result[] = array (
                'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_open') . ': ' . $open_response->result . "\n",
                'status' => 'ok'
            );
        }else{
            $result[] = array (
                'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_open') . ': ' . $open_response->error . "\n",
                'status' => 'error'
            );
        }
        $msg = array (
            'result' => $result
        );
        return Response::json($msg);

    }
    //天下第一特别版更新链接
    public function crossWorldXLordsUpdate()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );

        $rules = array(
            'server_id' => 'required',
            'server_id2' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get ( 'error.basic_input_error' );
            return Response::json ( $msg, 404 );
        }
        $server_ids = Input::get('server_id');
        $server_id2 = Input::get('server_id2');
        $main_server = Server::find($server_id2);
        $host = $main_server->api_server_ip;
        $port = $main_server->match_port;
        $result = array();
        foreach ($server_ids as $key => $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get ( 'error.basic_not_found' );
                return Response::json ( $msg, 404 );
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            //建立连接
            $close_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_TIANXIA_DIYI_X, $host, $port, false);
            $update_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_TIANXIA_DIYI_X, $host, $port, true);
            if (isset($update_response->result) && $update_response->result == 'OK') {
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
                    'status' => 'ok'
                );
            }else{
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->error . "\n",
                    'status' => 'error'
                );
            }
        }
        $msg = array (
            'result' => $result
        );
        return Response::json($msg);
    }
    //向比赛服报名
    public function crossWorldXLordsSignUp()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'gift_data' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $gift_datas = Input::get('gift_data');
        $gift_datas = explode("\n", $gift_datas);
        foreach ($gift_datas as &$v) {
            $v = trim($v);
        }
        unset($v);
        $gift_datas = array_unique($gift_datas);
        $result = array();
        $ok = array();
        $error = array();

        foreach ($gift_datas as $gift_data) {
            $gift_data = explode("\t", $gift_data, 2);
            if (count($gift_data) != 2) {
                $error[] = $gift_data[0] . ': No Server Name. ';
                continue;
            }
            $server_name = trim($gift_data[1]);
            $server = Server::currentGameServers()->where('server_track_name', $server_name)->first();
            if (! $server) {
                $error[] = $gift_data[0] . "($server_name) Server Not Found. ";
                continue;
            }
            //$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            //$player_id = 0;
            //$player_ids = array();
            //$player_id =$gift_data[0];
            //$player_id = (int) $gift_data[0];
            $player_id =$gift_data[0];
            /* $player_ids = array(
                 $player_id
             );*/
            /* Log::info(var_export($player_id,true));
              Log::info(var_export("ip:" . $server->api_server_ip . "port:" . $server->api_server_port . "id:" . $server->api_dir_id,true));*/
            $game_id = Session::get('game_id');
            //$game_id = 4;//三国测试
            $game = Game::find($game_id);
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->requestWorldLords(GameServerApi::MATCH_TYPE_TIANXIA_DIYI_X, $player_id, $game_id,$game->game_code);
            if (isset($response->result) && $response->result == 'OK') {
                $result[] = array(
                    'status' => 'ok',
                    'msg' => '(' . $server->server_name .')' . $player_id . Lang::get('serverapi.tournament_world_signup') . ':' . $response->result . "\n"
                );
            }else{
                $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . $player_id . Lang::get('serverapi.tournament_world_signup') . ': ' . $response->error . "\n",
                    'status' => 'error'
                );
            }

        }
        $msg = array (
            'result' => $result
        );
        if (count($error) > 0) {
            $msg['error'] = $error;
            return Response::json($msg, 403);
        }
        return Response::json($msg);
    }
    //天下第一特别版查询报名
    public function crossWorldXLordsSignLookUp()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'server_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $server_id = Input::get('server_id2');
        $main_server = Server::find($server_id);
        if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
        $game_id = Session::get('game_id');
        //$game_id = 4;//本地测试
        $game = Game::find($game_id);
        $result = array();
    	$server = Server::find($server_id);
    	$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
        $response = $api->searchGameMatchOtherServer(GameServerApi::MATCH_TYPE_TIANXIA_DIYI_X, $game_id,$game->game_code);
        if (isset($response->error)) {
            $result[] = array(
            	'msg' => '(' . $server->server_name . ')报名' . 'error' . "\n",
            	'statue' =>'error'
            );
        }elseif (isset($response->result) && $response->result =='OK') {
            $result[] = array(
                'msg' => '(' . $main_server->server_name . ')报名'. 'error'  . "\n",
                'status' => 'error'
            );
        }elseif (isset($response->members)) {
	        foreach ($response->members as $value) {
	            $player_str = 'player_id: ' . $value->player_id . ' server_id: ' . $value->server_id; 
	            
	            $result[] = array(
	                'msg' => '(' . $main_server->server_name . ') ' . $player_str  . ' OK' .  "\n",
	                'status' => 'ok'
	            );
        	}
        }else{
            $result[] = array(
                'msg' => '天下第一特别版报名查询异常---' . "\n",
                'status' => 'error'
            );
        }
        $msg = array (
            'result' => $result
        );
        return Response::json($msg);
       
    }
    //天下第一特别版关闭
    public function crossWorldXLordsClose()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'server_id2' => 'required',
            'id' => 'required',
            'password' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $id = Input::get('id');
        $password = Input::get('password');
        $server_id = Input::get('server_id2');
        $main_server = Server::find($server_id);
        if (!$main_server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 403);
        }
        if ($id == '456' && $password == '456') {
            $api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
            $response = $api->closeGameMatch(GameServerApi::MATCH_TYPE_TIANXIA_DIYI_X);
            if (isset($response->result) && $response->result == 'OK') {
                Cache::forget ( 'cross-server-time' );
                $result[] = array(
                    'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_close') . ': ' . $response->result . "\n",
                    'status' => 'ok'
                );
            } else {
                $result[] = array (
                    'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_close') . ': ' . $response->error . "\n",
                    'status' => 'error'
                );
            }
        }else {
            $result[] = array (
                'msg' => 'Password is wrong !!!',
                'status' => 'error'
            );
        }
        $msg = array (
            'result' => $result
        );
        return Response::json($msg);

    }
    //天下第一特别版查看链接
    public function crossWorldXLordsLookUp()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'server_id' => 'required'
        );
        $game = Game::find(Session::get('game_id'));
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $server_ids = Input::get('server_id');
        $result = array();
        foreach ($server_ids as $key => $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Resposne::json($msg, 403);
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->lookUpCrossServer();
            if (isset($response->servers)) {
                $servers = $response->servers;
                foreach ($servers as $key => $val) {
                    if ($val->tournament_type == \EastBlue\Facades\GameServerApi::MATCH_TYPE_TIANXIA_DIYI_X) {
                        $server_name = Server::whereRaw("api_server_ip = '{$val->host}' and match_port = {$val->port}")->pluck('server_name');
                        if($game->game_code == 'flsg' && $value->check_result == true && $value->check_time > 0 && $value->status == 'Connected'){
                        	$connect_status = '连接并确认';
                        }elseif($game->game_code == 'flsg' && $value->check_result == false && $value->check_time == 0 && $value->status == 'Connected'){
                        	$connect_status = '连接未确认';
                        }else{
                        	$connect_status = $value->status;
                        }
                        $result[] = array(
                            'status' => 'ok',
                            'msg' => $server->server_name .'--'.(($val->active == true)? '已开启' : '已关闭') .'--连接到--'. $server_name.'--'.$val->active."--host：" . ($val->host) .'--端口：'. ($val->port) .'--状态：'. ($connect_status) .'--类型：'.$val->tournament_type
                        );
                    }
                }
            }else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . '查看链接' . ': ' . 'error',
					'status' => 'error' 
				);
			}
        }
        $msg = array (
            'result' => $result
        );
        return Response::json($msg);
    }

    public function matchLookUpIndex(){
    	$servers = $this->getUnionServers();
    	if(empty($servers)){
    		App::abort(404);
    		exit();
    	}
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.tournament.matchLookup',
    			array(
    				'servers' => $servers
    			))
    	);
    	return View::make('main', $data);
    }

    public function matchLookup(){
    	$server_ids = Input::get('server_id');
    	$tournament_type = (int)Input::get('type');
    	if('0' == $server_ids){
    		return Response::json(array('error'=>'Did you select a server?'), 403);
    	}
    	if(0 == $tournament_type){
    		$msg['error'] = Lang::get('serverapi.not_select_match');
    		return Response::json($msg, 403);
    	}
    	foreach ($server_ids as $server_id) {
    		$server = Server::find($server_id); 
			if(! $server)
			{
			    $msg['error'] = Lang::get('error.basic_not_found');
			    return Response::json($msg, 404);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
    		$response = $api->getMatchInfo($tournament_type);
    		Log::info('test:' . var_export($response,true));
    		if(!isset($response->tables)){
    			continue;
    		}
    		$tables = $response->tables;

    		if(isset($tables[0]->members)){
    			$sky_members = $tables[0]->members;
    			$sky = array();
				$server_temp = Server::find($sky_members->server_id);
				$sky_temp = array(
					'server_id' => isset($server_temp->server_name) ? $server_temp->server_name : $sky_members->server_id,
					'player_id' => $sky_members->player_id,
					'player_name' => $sky_members->player_name,
					'level' => $sky_members->level,
					'shengwang' => $sky_members->shengwang,
				);
				$sky[] = $sky_temp;
				unset($sky_temp); 
    			Log::info(var_export($sky,true));
    		}
    		if(isset($tables[1]->members)){
    			$ground_members = $tables[1]->members;
    			$ground = array();
    			foreach ($ground_members as $v) {
    				$server_temp = Server::find($v->server_id);
					$ground_temp = array(
						'server_id' => isset($server_temp->server_name) ? $server_temp->server_name : $v->server_id,
						'player_id' => $v->player_id,
						'player_name' => $v->player_name,
						'level' => $v->level,
						'shengwang' => $v->shengwang,
					);
					$ground[] = $ground_temp;
					unset($ground_temp);
    			}
				 
    		}
    		
    	}
    }

}