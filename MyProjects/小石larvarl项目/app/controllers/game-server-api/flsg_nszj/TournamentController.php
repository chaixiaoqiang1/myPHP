<?php
class TournamentController extends \BaseController {
	const TYPE_HEAVEN = 23;

	public function singleServerIndex()
	{
		// $servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServers();
		$period = array();
		foreach ($servers as $v) {
			$api = GameServerApi::connect($v->api_server_ip, $v->api_server_port, $v->api_dir_id);
			$response = $api->loadGameMatchStatus(GameServerApi::MATCH_TYPE_ZHENGBA);
			if (isset($response->period)) {
				$period[$response->period][] = (object)array(
					'server_name' => $v->server_name,
					'match' => $response,
				);
			} else {
				$period[9999][] = (object)array(
					'server_name' => $v->server_name,	
					'match' => null,
				);
			}
		}
		ksort($period);
		$data = array (
			'content' => View::make ( 'serverapi.flsg_nszj.tournament.singleserver', array (
				'servers' => $servers,
			   	'period' => $period
			)) 
		);
		return View::make ( 'main', $data );
	}
	public function singleServer()
	{
		$server_ids = Input::get('server_id');
		$start_time = strtotime(trim(Input::get('start_time')));	
		if ($start_time == '' || $start_time < time ()) {
			$msg = array (
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error') 
			);
			return Response::json ($msg, 403);
		}

		$game_id = Session::get('game_id');
		$result = array ();

		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}

			$host = $server->api_server_ip;
			$port = $server->match_port;

			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

			// 建立连接
			$update_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_ZHENGBA, $host, $port, true);
			if (isset($update_response->result) && $update_response->result == 'OK') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_single_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
					'status' => 'ok' 
				);

				// 开启单服争霸
				$open_response = $api->openGameMatch(GameServerApi::MATCH_TYPE_ZHENGBA, $start_time);
				if (isset($open_response->result) && $open_response->result == 'OK') {
					Cache::forget('single-server-time'.$game_id.'i'.$server->server_internal_id);
					Cache::add('single-server-time'.$game_id.'i'.$server->server_internal_id,
								$start_time, 7*24*60); // 将开启时间加入cache
					$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_single_open') . ' : ' . $open_response->result . "\n",
						'status' => 'ok' 
					);
				} else {
					$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_single_open') . ': ' . $open_response->error . "\n",
						'status' => 'error' 
					);
				}
			} else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_single_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->error . "\n",
					'status' => 'error' 
				);
			}
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json ( $msg );
	}
	
	/**
	 * 更新比赛连接
	 */
	public function singleServerUpdate()
	{
		$server_ids = Input::get('server_id');
		$result = array ();
		if (empty($server_ids)) {
			$msg['error'] = Lang::get( 'error.basic_not_found' );
			return Response::json( $msg, 404 );
		}
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get( 'error.basic_not_found' );
				return Response::json( $msg, 404 );
			}

			$host = $server->api_server_ip;
			$port = $server->match_port;

			$api = GameServerApi::connect ( $server->api_server_ip, $server->api_server_port, $server->api_dir_id );
			
			$close_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_ZHENGBA, $host, $port, false);
			$response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_ZHENGBA, $host, $port, true);

			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_update') . '['.$host . ':' . $port . ']' . ' : ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_update') . '['.$host . ':' . $port . ']' . ' : ' . $response->error . "\n",
					'status' => 'error' 
				);
			}
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json ($msg);
	}

	/* 载入比赛连接 */
	public function singleServerLoad()
	{
		$server_ids = Input::get('server_id');
		$result = array ();
		if (empty($server_ids)) {
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json ( $msg, 404 );
		}
		foreach ($server_ids as $server_id) {
			$server = Server::find ($server_id);
			if (! $server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );
			}
			
			$api = GameServerApi::connect ( $server->api_server_ip, $server->api_server_port, $server->api_dir_id );

			$response = $api->loadGameMatchStatus (GameServerApi::MATCH_TYPE_ZHENGBA);

			if (isset($response->error_code)) {
				return Response::json ($response, 500);
			}
			
			$result[] = array (
				'msg' => $server->server_name . ' ' .  Lang::get('serverapi.tournament_period_' . $response->period) . ' ' . date ( "Y-m-d H:i:s", $response->start_time) . ' Match: ' . $response->match . ' Round: ' . $response->round,
				'status' => 'ok' 
			);
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json ($msg);
	}
	
	/* 关闭争霸 */
	public function singleServerClose()
	{
		$msg = array (
			'code' => Config::get ( 'errorcode.unknow' ),
			'error' => Lang::get ( 'error.basic_input_error' ) 
		);
		$id = (int)Input::get('id');
		$password = ( int )Input::get ('password');
		$server_ids = Input::get('server_id');

		$result = array ();

		if ($id == '123' && $password == '123')
		{
			foreach ( $server_ids as $server_id )
			{
				$server = Server::find ( $server_id );
				if (! $server) {
					$msg['error'] = Lang::get ( 'error.basic_not_found' );
					return Response::json ( $msg, 404 );
				}
				$api = GameServerApi::connect ( $server->api_server_ip, $server->api_server_port, $server->api_dir_id );

				$response = $api->closeGameMatch(GameServerApi::MATCH_TYPE_ZHENGBA);

				if (isset ( $response->result ) && $response->result == 'OK') {
					$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_single_close') . ': ' . $response->result . "\n",
						'status' => 'ok' 
					);
				} else {
					$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_single_close') . ' error'  . "\n",
						'status' => 'error' 
					);
				}
			}
		} else {
			$result[] = array (
				'msg' => 'Password is wrong !!!',
				'status' => 'error' 
			);
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json ($msg);
	}
	public function crossServerIndex()
	{
		//$servers = Server::currentGameServers ()->get ();
		$servers = $this->getUnionServers();
		$period = array();
		foreach ($servers as $v) {
			$api = GameServerApi::connect($v->api_server_ip, $v->api_server_port, $v->api_dir_id);
			$response = $api->loadGameMatchStatus(GameServerApi::MATCH_TYPE_KUAFU_ZHENGBA);
			if (isset($response->period)) {
				$period[$response->period][] = (object)array(
					'server_name' => $v->server_name,
					'match' => $response,
				);
			} else {
				$period[9999][] = (object)array(
					'server_name' => $v->server_name,	
					'match' => null,
				);
			}
		}
		ksort($period);
		$data = array (
			'content' => View::make ( 'serverapi.flsg_nszj.tournament.crossserver', array (
				'servers' => $servers,
				'period' => $period
			)) 
		);
		return View::make('main', $data);
	}

	/**
	 * 跨服争霸连接和开启
	 */
	public function crossServer()
	{
		$msg = array (
			'code' => Config::get ( 'errorcode.unknow' ),
			'error' => Lang::get ( 'error.basic_input_error' ) 
		);

		$start_time = strtotime(trim(Input::get('start_time')));
		$game_id = Session::get('game_id');
		/* 跨服争霸开启时间必须在单服争霸结束之后 */
		//Log::info(var_export(Cache::has('single-server-time'), true));

		$server_ids = Input::get('server_id');
		$server_id2 = Input::get('server_id2');
		if(empty($server_ids) || empty($server_id2)){
			return Response::json(array('error'=>'Did you select a server?'), 403);
		}
		if(count($server_id2)>1){
			return Response::json(array('error'=>'该操作主服一次只能选择一个服'), 403);
		}
		$main_server = Server::find($server_id2[0]);

		if (! $main_server) {
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json ( $msg, 404 );
		}
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port;
		$result = array();
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json (array('error'=>'Server not found.'), 403 );
			}
			if (Cache::has('single-server-time'.$game_id.'i'.$server->server_internal_id)) {
				$singleServerTime = Cache::get('single-server-time'.$game_id.'i'.$server->server_internal_id);
				/*Log::info("####singleServerTime####\n".$singleServerTime);
				Log::info(var_export('time error. single-server-time:'
						.$singleServerTime
						.'single-server-time'.$game_id.'i'.$server->server_internal_id
						.' start_time:'
						.$start_time, true));*/
				$singleTimeDate = date("Y-m-d H:i:s", $singleServerTime);
				return Response::json (array('error'=>$server->server_name.'Start Time is '.$singleTimeDate), 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			// 建立连接
			$update_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_KUAFU_ZHENGBA, $host, $port, true);
//			Log::info('update game match response:'.var_export($update_response, true));
			if (isset($update_response->result) && $update_response->result == 'OK') {
				$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
						'status' => 'ok'
				);
			} else {
				$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->error . "\n",
						'status' => 'error'
				);
			}
		}
		// 开启跨服争霸
		$main_api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
		
		$open_response = $main_api->openGameMatch(GameServerApi::MATCH_TYPE_KUAFU_ZHENGBA, $start_time);
//        Log::info('open game match response:'.var_export($update_response, true));
		if (isset($open_response->result ) && $open_response->result == 'OK') {
			Cache::add ( 'cross-server-time', $start_time, 600 );
			$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_open') . ': ' . $open_response->result . "\n",
					'status' => 'ok'
			);
		} else {
			$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_open') . ': ' . $open_response->error . "\n",
					'status' => 'error'
			);
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
	}
	/**
	 * 跨服争霸更新链接
	 */
	public function crossServerUpdate()
	{
		$msg = array (
				'code' => Config::get ( 'errorcode.unknow' ),
				'error' => Lang::get ( 'error.basic_input_error' )
		);
	
		$server_ids = Input::get('server_id');
		$server_id2 = Input::get('server_id2');
		if(empty($server_ids) || empty($server_id2)){
			return Response::json(array('error'=>'Did you select a server?'), 403);
		}
		if(count($server_id2)>1){
			return Response::json(array('error'=>'该操作主服一次只能选择一个服'), 403);
		}
		$main_server = Server::find($server_id2[0]);
		if (! $main_server) {
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json ( $msg, 404 );
		}
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port;
		$result = array();
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			// 建立连接
			$close_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_KUAFU_ZHENGBA, $host, $port, false);
			$update_response = $api->updateGameMatch(GameServerApi::MATCH_TYPE_KUAFU_ZHENGBA, $host, $port, true);
			if (isset($update_response->result) && $update_response->result == 'OK') {
				$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
						'status' => 'ok'
				);
	
			} else {
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
	/**
	 * 跨服争霸报名，参加的所有服报名
	 */
	public function crossServerSignup()
	{
		$server_ids = Input::get ('server_id');
		$result = array ();
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json ( $msg, 404 );
			}

			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

			$response = $api->requestGameMatch(GameServerApi::MATCH_TYPE_KUAFU_ZHENGBA);

			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_signup') . ': ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
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
	
	/**
	 * 跨服争霸，查询报名，主要的比赛服
	 */
	// need to add
	public function crossServerLookup()
	{
		$server_ids = Input::get('server_id2');
		if(empty($server_ids)){
			return Response::json(array('error'=>'Did you select a server?'), 403);
		}
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		foreach ($server_ids as $server_id) {
			$main_server = Server::find($server_id);
			if(!$main_server){
				continue;
			}
			$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
			$response = $api->searchCrossMatchOtherServer(GameServerApi::MATCH_TYPE_KUAFU_ZHENGBA, $game_id, $game->game_code);
			if (isset($response->list)) {
				$info = $response->list;
				$ss = Server::where("game_id", "=", $game_id)->get();
				for ($i=0; $i < count($info); $i++) { 
					$info[$i]->server_name = "";
	                for ($k=0; $k < count($ss); $k++) { 
	                    if ($ss[$k]->server_internal_id == $info[$i]->server_id) {
	                        $info[$i]->server_name = $ss[$k]->server_name."   OK";
	                    }
	                }
				}
				$result[] = array (
					'msg' => $info,
					'status' => 'ok'
				);

			} else {
				$result[] = array (
					'msg' => ' ( ' . $main_server->server_name . ' ) error' . "\n",
					'status' => 'error'
				);
			}
		}
		return Response::json($result);
	}
	
	/**
	 * 跨服争霸关闭，关闭主的比赛服
	 */
	public function crossServerClose()
	{
		$id = (int)Input::get('id');
		$password = (int)Input::get('password');
		$server_id2 = Input::get('server_id2');
		if(empty($server_id2)){
			return Response::json(array('error'=>'Did you select a server?'), 403);
		}
		if(count($server_id2)>1){
			return Response::json(array('error'=>'该操作主服一次只能选择一个服'), 403);
		}
		$main_server = Server::find($server_id2[0]);
		$result = '';
		if (! $main_server) {
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json($msg, 404);
		}
		if ($id == '456' && $password == '456') {
			$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
			$response = $api->closeGameMatch(GameServerApi::MATCH_TYPE_KUAFU_ZHENGBA);
			if (isset($response->result) && $response->result == 'OK') {
				Cache::forget ( 'cross-server-time' );
				$result[] = array (
					'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_close') . ': ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
				$result[] = array (
					'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_cross_close') . ': ' . $response->error . "\n",
					'status' => 'error' 
				);
			}
		} else {
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

	//跨服争霸查看
	public function crossServerLook()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id' => 'required',
		);
		$game = Game::find(Session::get('game_id'));
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$server_ids = Input::get('server_id');
		if('0' == count($server_ids)){
			return Response::json(array('error'=>'请选择服务器'), 403);
		}
		$result = array(

		);
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Resposne::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->lookUpCrossServer();
			//var_dump($response);die();
			//$result[] = $response;
			if (isset($response->servers)) { //获得反馈
				$info = $response->servers;
				foreach ($info as $key => $value) {
					if ($value->tournament_type != 2) {
						continue;
					}
					switch ($value->tournament_type) {
						case '1':
							$type = "单服争霸";
							break;
						case '2':
							$type = "跨服争霸";
							break;
						case '3':
							$type = "大乱斗";
							break;
						default:
							break;
					}
					$server_name = Server::whereRaw("api_server_ip = '{$value->host}' and match_port = $value->port")->pluck('server_name');
					if($game->game_code == 'flsg' && $value->check_result == true && $value->check_time > 0 && $value->status == 'Connected'){
						$connect_status = '连接并确认';
					}elseif($game->game_code == 'flsg' && $value->check_result == false && $value->check_time == 0 && $value->status == 'Connected'){
						$connect_status = '连接未确认';
					}else{
						$connect_status = $value->status;
					}
					$result[] .=$server->server_name .'--'.(($value->active == true)? '已开启' : '已关闭') .'--连接到--'. $server_name.'--'.$value->active."--host：" . ($value->host) .'--端口：'. ($value->port) .'--状态：'. ($connect_status ) .'--类型：'. ($type) .'--'.$value->tournament_type;
				}
			}else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . '查看链接' . ': ' . 'error',
					'status' => 'error' 
				);
			}
		}
		if (isset($result)) {
			return Response::json($result);
		} else{
			return Response::json($msg, 403);
		}
	}
	
	/**
	 * 三国大乱斗---入口
	 */
	public function meleeIndex()
	{
		$servers = $this->getUnionServers();
		
		if (empty ( $servers ))
		{
			App::abort ( 404 );
			exit ();
		}
		$data = array (
				'content' => View::make ( 'serverapi.flsg_nszj.tournament.melee', array (
						'servers' => $servers 
				) ) 
		);
		return View::make ( 'main', $data );
	}
	public function allserverconnectIndex()
	{
		$servers = $this->getUnionServers();
		
		if (empty ( $servers ))
		{
			App::abort ( 404 );
			exit ();
		}
		$data = array (
				'content' => View::make ( 'serverapi.flsg_nszj.tournament.allserverconnect', array (
						'servers' => $servers 
				) ) 
		);
		return View::make ( 'main', $data );
	}
	/**
	 * 三国大乱斗--开启
	 */
	public function meleeData()
	{
		$msg = array (
			'code' => Config::get ( 'errorcode.unknow' ),
			'error' => Lang::get ( 'error.basic_input_error' ) 
		);

		$server_ids = Input::get('server_id');
		$server_id2 = (int)Input::get('server_id2');

		$main_server = Server::find($server_id2);
		$is_allserverconnect = (int)Input::get('is_allserverconnect');//1全服连接，0大乱斗
		if (! $main_server) {
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json ( $msg, 404 );
		}
		//var_dump($main_server);die();
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port;
		$result = array();
		$game_id = Session::get('game_id');
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			
			// 建立连接
			$response = $api->updateMelee( $host, $port, true, $is_allserverconnect, $game_id);
			//$s = var_export($response,true);
			//Log::info($s);
			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_melee_connect') . '['.$host . ':' . $port . ']' . ' : ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_melee_connect') . '['.$host . ':' . $port . ']' . ' : error' . "\n",
					'status' => 'error' 
				);
			}
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
	}
	/**
	 * 三国大乱斗--查询
	 */
	public function meleeLookup()
	{
		$msg = array (
			'code' => Config::get ( 'errorcode.unknow' ),
			'error' => Lang::get ( 'error.basic_input_error' ) 
		);
		$server_ids = Input::get('server_id');
		$is_allserverconnect = (int)Input::get('is_allserverconnect');//1全服连接，0大乱斗
		$result = array();
		$game_id = Session::get('game_id');
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );
			}	
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api ->searchMelee($is_allserverconnect, $game_id);
			//$s = var_export($response,true);
			//Log::info($s);
			if (isset($response->error)) {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->error ."\n" ,
					'status' => 'error' 
				);
			}else if(isset($response->status) && $response->status == 'Disconnected') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_melee_disconnect') . "===".$response->active. "\n",
					'status' => 'disconnected' 
				);
			}else if(isset($response->status) && $response->status == 'Connected') {
				$main_server_name = ' ';
				$query = Server::where("api_server_ip", $response->host)->where("match_port", $response->port)->where("game_id", Session::get('game_id'))->first();
				if($query){
					$main_server_name = $query ->server_name;
				}
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_melee_connect') .' ( ' . $main_server_name . ' ) : ' .$response->host .' : '.$response->port. "===".$response->active . "\n",
					'status' => 'connected' 
				);
			}else if(isset($response->status) && $response->status == 'Connecting') {
				$main_server_name = ' ';
				$query = Server::where("api_server_ip", $response->host)->where("match_port", $response->port)->where("game_id", Session::get('game_id'))->first();
				if($query){
					$main_server_name = $query ->server_name;
				}
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_melee_connecting') .' ( ' . $main_server_name . ' ) : ' .$response->host .' : '.$response->port. "===".$response->active. "\n",
					'status' => 'connecting' 
				);
			}else{
			    $result[] = array (
			            'msg' => ' ( ' . $server->server_name . ' ) : Unknow Statu '."\n",
			            'status' => 'error'
			    );
			}
			
		}
		$msg = array (
				'result' => $result
		);
		return Response::json($msg);
	}

	public function closeMelee()
	{
		/*$server_id2 = (int)Input::get('server_id2');

		$main_server = Server::find($server_id2);

		if (! $main_server) {
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json ( $msg, 404 );
		}
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port;
		$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
		$response = $api->closeMelee($host, $port, false);
		return Response::json($response);*/

		$server_ids = Input::get('server_id');
		$is_allserverconnect = (int)Input::get('is_allserverconnect');//1全服连接，0大乱斗
		$result = array();
		$game_id = Session::get('game_id');
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Reposne::json($msg, 403);
			}
			$host = $server->api_server_ip;
			$port = $server->match_port;
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->closeMelee($host, $port, false, $is_allserverconnect, $game_id);
			//$result[] = $response;
			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . 'connect ['.$host . ':' . $port . ']' . ' : ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . 'connect ['.$host . ':' . $port . ']' . ' : error' . "\n",
					'status' => 'error' 
				);
			}
		}
		$msg = array (
				'result' => $result
		);
		return Response::json($msg);

	}

	/**
	 * 界王---入口
	 */
	public function jiewangIndex()
	{
		$servers = $this->getUnionServers();
		
		$data = array (
				'content' => View::make ( 'serverapi.flsg_nszj.tournament.melee', array (
						'servers' => $servers 
				) ) 
		);
		return View::make ( 'main', $data );
	}
	/**
	 * 界王--开启
	 */
	public function jiewangOpen()
	{
		$msg = array (
			'code' => Config::get ( 'errorcode.unknow' ),
			'error' => Lang::get ( 'error.basic_input_error' ) 
		);

		$server_ids = Input::get('server_id');
		$server_id2 = (int)Input::get('server_id2');

		$main_server = Server::find($server_id2);

		if (! $main_server) {
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json ( $msg, 404 );
		}
		//var_dump($main_server);die();
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port;
		$result = array();
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			
			// 建立连接
			$response = $api->jiewangOpen( $host, $port, true);
			//$s = var_export($response,true);
			//Log::info($s);
			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_melee_connect') . '['.$host . ':' . $port . ']' . ' : ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else if (isset($response->error_code) && isset($response->error)) {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_melee_connect') . '['.$host . ':' . $port . ']' . ' : ' .  $response->error . "\n",
					'status' => 'error' 
				);
			}
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
	}
	/**
	 * 三国大乱斗--查询
	 */
	public function jiewangLookup()
	{
		$msg = array (
			'code' => Config::get ( 'errorcode.unknow' ),
			'error' => Lang::get ( 'error.basic_input_error' ) 
		);
		$server_ids = Input::get('server_id');
		$result = array();
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if (! $server) {
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );
			}
			
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api ->jiewangLookup();
			//$s = var_export($response,true);
			//Log::info($s);
			if (isset($response->error)) {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->error ."\n" ,
					'status' => 'error' 
				);
			}else if(isset($response->status) && $response->status == 'Disconnected') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_melee_disconnect') . "===".$response->active. "\n",
					'status' => 'disconnected' 
				);
			}else if(isset($response->status) && $response->status == 'Connected') {
				$main_server_name = ' ';
				$query = Server::where("api_server_ip", $response->host)->where("match_port", $response->port)->where("game_id", Session::get('game_id'))->first();
				if($query){
					$main_server_name = $query ->server_name;
				}
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_melee_connect') .' ( ' . $main_server_name . ' ) : ' .$response->host .' : '.$response->port. "===".$response->active . "\n",
					'status' => 'connected' 
				);
			}else if(isset($response->status) && $response->status == 'Connecting') {
				$main_server_name = ' ';
				$query = Server::where("api_server_ip", $response->host)->where("match_port", $response->port)->where("game_id", Session::get('game_id'))->first();
				if($query){
					$main_server_name = $query ->server_name;
				}
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) : ' . Lang::get('serverapi.tournament_melee_connecting') .' ( ' . $main_server_name . ' ) : ' .$response->host .' : '.$response->port. "===".$response->active. "\n",
					'status' => 'connecting' 
				);
			}else{
			    $result[] = array (
			            'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->error . "\n",
			            'status' => 'error'
			    );
			}
			
		}
		$msg = array (
				'result' => $result
		);
		return Response::json($msg);
	}

	public function jiewangClose()
	{
		$server_ids = Input::get('server_id');
		$result = array();
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Reposne::json($msg, 403);
			}
			$host = $server->api_server_ip;
			$port = $server->match_port;
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->jiewangClose($host, $port, false);
			//$result[] = $response;
			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_melee_close_success') . '['.$host . ':' . $port . ']' . ' : ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else if (isset($response->error_code) && isset($response->error)) {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.tournament_melee_close_error') . '['.$host . ':' . $port . ']' . ' : ' .  $response->error . "\n",
					'status' => 'error' 
				);
			}
		}
		$msg = array (
				'result' => $result
		);
		return Response::json($msg);

	}

	public function heavenIndex()
	{
		$server = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.tournament.heaven',array('server' => $server)),
		);
		return View::make('main', $data);
	}

	public function closeHeaven()
	{
		$msg = array (
			'code' => Config::get ( 'errorcode.unknow' ),
			'error' => Lang::get ( 'error.basic_input_error' ) 
		);

		$rules = array(
			'start_time' => 'required',
			'end_time' => 'required',
			'server_id' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
		 	return Response::json($msg, 403);
		} 
		$open_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));

		$time_arr1 = getdate($open_time);
	    $time_arr2 = getdate($end_time);

	    if (($time_arr1['hours'] == 23 && $time_arr1['minutes'] >=51) || $time_arr1['hours'] == 0 && $time_arr1['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		if (($time_arr2['hours'] == 23 && $time_arr2['minutes'] >=51) || $time_arr2['hours'] == 0 && $time_arr2['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		
		$server_id = Input::get('server_id');
		$type = intval(self::TYPE_HEAVEN);

		$result = array();
		$len = count($server_id);
		for ($i=0; $i < $len; $i++) { 
			$server[$i] = Server::find($server_id[$i]);
			if (!$server[$i]) {
				return Response::json($msg, 403);
			}
			$api[$i] = GameServerApi::connect($server[$i]->api_server_ip, $server[$i]->api_server_port, $server[$i]->api_dir_id);
			$result[$i] = $api[$i] ->closeHeaven($open_time, $end_time, $type);
			//var_dump($result[$i]);die();
			if ($result[$i]->result == "OK") {
				$info[] = array(
					'msg' => ' ( ' . $server[$i]->server_name . ' ) : ' . $result[$i]->result . "\n",
			        'status' => 'OK'
				);
			} else {
				$info[] = array(
					'msg' => ' ( ' . $server[$i]->server_name . ' ) : ' . $result[$i]->error . "\n",
			        'status' => 'error'
				);
			}
		}
		return Response::json($info);
	}

	public function openHeaven()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$server_id = Input::get('server_id');
		$type = intval(self::TYPE_HEAVEN);
		$result = array();
		$len = count($server_id);
		for ($i=0; $i < $len; $i++) { 
			$server[$i] = Server::find($server_id[$i]);
			if (!$server[$i]) {
				return Response::json($msg, 403);
			}
			$api[$i] = GameServerApi::connect($server[$i]->api_server_ip, $server[$i]->api_server_port, $server[$i]->api_dir_id);
			$result[$i] = $api[$i]->openHeaven($type);
			if ($result[$i]->result == "OK") {
				$info[] = array(
					'msg' => ' ( ' . $server[$i]->server_name . ' ) : ' . $result[$i]->result . "\n",
			        'status' => 'OK'
				);
			} else {
				$info[] = array(
					'msg' => ' ( ' . $server[$i]->server_name . ' ) : ' . $result[$i]->error . "\n",
			        'status' => 'error'
				);
			}
		}
		//var_dump($result);
		return Response::json($info);
	}
	public function lookUpHeaven()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error')
		);
		$server_id = Input::get('server_id');

		$result = array();
		$len = count($server_id);
		for ($i=0; $i < $len; $i++) { 
			$server[$i] = Server::find($server_id[$i]);
			if (!$server[$i]) {
				return Response::json($msg, 403);
			}
			$api[$i] = GameServerApi::connect($server[$i]->api_server_ip, $server[$i]->api_server_port, $server[$i]->api_dir_id);
			$result[$i] = $api[$i] -> lookupHeaven();
			//var_dump($result);
			//Log::info(var_export($result[$i],true));
			if (isset($result[$i]->activities)) {
				$res[$i] = $result[$i]->activities;
				$data[$i] = array();
				foreach ($res[$i] as $val) {
					if ($val->type == self::TYPE_HEAVEN) {
						$data[$i] = $val;
						break;
					}
				}
				$info[$i] = array(
					'server_name' => $server[$i]->server_name,
					'open_time' => isset($data[$i]->open_time) ? date("Y-m-d H:i:s", $data[$i]->open_time) : '',
					'close_time' => isset($data[$i]->close_time) ? date("Y-m-d H:i:s", $data[$i]->close_time) : '',
					'counter' => isset($data[$i]->counter) ? $data[$i]->counter :0,
					'is_open' => isset($data[$i]->is_open) ? (!$data[$i]->is_open) : 'Unknow',
					'type' => Lang::get('serverapi.heaven'),
				);

			}
		}
		if (isset($info)) {
			return Response::json($info);
		} else {
			return Response::json($msg, 403);
		}
	}

	public function heavenStudioIndex()
	{
		$server = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.tournament.heaven_studio', array('server' => $server)),
		);
		return View::make('main', $data);
	}

	public function heavenStudioOpen()
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
		$action = Input::get('action');
		switch ($action) {
			case 'open':
				$type = 0xbc3e;
				break;
			
			case "look":
				$type = 0xbc3f;
				break;

			case "close":
				$type = 0xbc41;
				break;
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
			$result = $api->heavenStudio($type);
			//var_dump($result);die();
			if ($action == "open" || $action == "close") {
				if ($result->result == "OK") {
					$info[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $result->result . "\n",
				        'statu' => 'OK'
					);
				} else {
					$info[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $result->error . "\n",
				        'statu' => 'error'
					);
				}
				
			} else {
				if (isset($result->is_open)) {
					$info[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . ($result->is_open == 1 ? '已开启' : '已关闭'). "\n",
				        'statu' => 'OK'
					);
				} else {
					$info[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $result->error . "\n",
				        'statu' => 'error'
					);
				}
			}

			
		}
		return Response::json($info);
	}
	
	  //仙界帮派战
    public function heavenBattleIndex()
    {
    	$server = $this->getUnionServers();
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.tournament.heaven_battle', array('server' => $server)),
    	);
    	return View::make('main', $data);
    }
    public function heavenBattleOperate()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error' => '',
    	);
    	$rules = array(
    		'server_id' => 'required',
    	);
    	$validator = Validator::make(Input::all(), $rules);
        $game = Game::find(Session::get("game_id"));
    	$server_ids = Input::get('server_id');
    	$type = Input::get('action');
    	$result = array();
    	foreach ($server_ids as $key => $server_id) {
    		$server = Server::find($server_id);
    		if (!$server) {
    			$msg['error'] = Lang::get('error.basic_not_found');
    			return Response::json($msg, 403);
    		}
    		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
    		$response = $api->heavenBattleOperate($type, $game->game_code);
    		//var_dump($response);die();
    		if (isset($response->active) && $response->active  == true ) { //成功
				if ($type == "open") { // 开启
					$result[] = array(
	    				'status' => "OK",
	    				'msg' => $server->server_name ."----开启成功----" . ($response->active == 0 ? 'false' :'true')  
		    		);
				}elseif ($type == "look") { //查看
					$result[] = array(
	    				'status' => "OK",
	    				'msg' => $server->server_name ."----已开启----" . ($response->active == 0 ? 'false' :'true')  
	    			);
				}
    			
    		}elseif(isset($response->active) && $response->active  == false){
				if ($type == "close") {
					$result[] = array(
	    				'status' => "OK",
	    				'msg' => $server->server_name ."----关闭成功----". ($response->active == 0 ? 'false' :'true')  
	    			);
				}elseif ($type == "look") {
					$result[] = array(
	    				'status' => "OK",
	    				'msg' => $server->server_name ."----已关闭----". ($response->active == 0 ? 'false' :'true')  
	    			);
				} 
    		}else{
    			$result[] = array(
    				'status' => "error",
    				'msg' => $server->server_name ."----error----" 
    			);
    		}
    	}
    	return Response::json($result);
    }


	//
	public function getUnionGame()
	{
		$server = $this->initTable3();
        $server = $server->getData();
        $server = (array)$server;
        return  $server;
	}

	private function initTable3()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'flsg' . '/server.txt');
        return $table;
    }

    public function getServers()
    {
    	$ser = $this->getUnionGame();
		$game_id = Session::get('game_id');
		//$game_id = 9;
		$len = count($ser);
		for ($i=0; $i < $len; $i++) { 
			$game_arr[$i] =  $ser[$i]->gameid;	
		}
		$ga = array_unique($game_arr);
		$se = "";
		$servers = array();
		if (in_array($game_id, $ga)) {
			for ($i=0; $i < $len; $i++) { 
				if ($ser[$i]->gameid == $game_id) { //判断是联运
					/*$servers[$i]['server_id'] = $ser[$i]->serverid1;
					$ss = Server::where("game_id", "=", $game_id)->get();
	                for ($k=0; $k < count($ss); $k++) { 
	                    if ($ss[$k]->server_internal_id == $ser[$i]->serverid1) {
	                        $servers[$i]['server_name'] = $ss[$k]->server_name;
	                    }
	                }
	                $servers[$i] = (object)$servers[$i];*/
	                $se .= $ser[$i]->serverid2 . ' , '; 
				}
			}
			$se_arr = explode(',' , $se);
			unset($se_arr[count($se_arr)]);
			$server = Server::whereNotIn('server_internal_id', $se_arr)->get();
			for ($i=0; $i < count($server); $i++) { 
				if ($server[$i]->game_id == $game_id) {
					$servers[] = $server[$i];
				}
			}

		} else {
			$servers = Server::currentGameServers()->get();
		}
		return $servers;
    }
    private function getGameId()
    {
    	$ser = $this->getUnionGame();
		$len = count($ser);
		for ($i=0; $i < $len; $i++) { 
			$game_arr[$i] =  $ser[$i]->gameid;	
		}
		$ga = array_unique($game_arr);
		return $ga;
    }

    public function nsLonelyIndex()
	{
		$server = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.tournament.ns_lonely', array('server' => $server)),
		);
		return View::make('main', $data);
	}

	public function nsLonelyOpen()
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
		$action = Input::get('action');
		switch ($action) {
			case 'open':
				$type = 0xbc5d;
				break;
			
			case "look":
				$type = 0xbc5c;
				break;

			case "close":
				$type = 0xbc5e;
				break;
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
			$result = $api->nsLonely($type);
			//var_dump($result);die();
			if ($action == "open" || $action == "close") {
				if (isset($result->result) && $result->result == "OK") {
					$info[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $result->result . "\n",
				        'status' => 'OK'
					);
				} else {
					$info[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $result->error . "\n",
				        'status' => 'error'
					);
				}
				
			} else {
				if (isset($result->is_x_lonely_open)) {
					$info[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . ($result->is_x_lonely_open == 1 ? '已开启' . '本周活跃人数：' . $result->active_player_number . '，女神个数：' . $result->god_number : '已关闭'). "\n",
				        'status' => 'OK'
					);
				} else {
					$info[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $result->error . "\n",
				        'status' => 'error'
					);
				}
			}

			
		}
		return Response::json($info);
	}

	public function resetleagueBossIndex(){
		$server = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.tournament.resetboss',array(
				'servers' => $server
			))
		);
		return View::make('main',$data);
	}

	public function resetleagueBoss(){
        $msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$game = Game::find(Session::get('game_id'));
		$type = (int)Input::get('type');
		$league_id = (int)Input::get('league_id');
		$player_id = (int)Input::get('player_id');
		$add_times = (int)Input::get('add_times');
		if(0 == $type && 0 == $league_id){
			return Response::json(array('error'=>'Did you enter league_id?'), 403);
		}
		if(1 == $type && 0 == $player_id){
			return Response::json(array('error'=>'Did you enter player_id?'), 403);
		}
		if(1 == $type && 0 == $add_times){
			return Response::json(array('error'=>'Did you enter times?'), 403);
		}
		
		$server_id = (int)Input::get('server_id');
		if(0 == $server_id){
			return Response::json(array('error'=>'Did you select a server?'), 403);
		}
		$server = Server::find($server_id);
		if(!$server)
		{
		    $msg['error'] = Lang::get('error.basic_not_found');
		    return Response::json($msg, 404);
		}
		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		$result = array();
		if(0 == $type){
			$response = $api->sesetLeagueBoss($league_id);
			if (isset($response->result) && $response->result == 'OK') {
			    $result[] = array(
			        'msg' => ' ( ' . $server->server_name .' ) : ' . $league_id . 'OK' . "\n",
			        'status' => 'ok'
			    );
			} else {
			    $result[] = array(
			        'msg' => ' ( ' . $server->server_name .' ) : ' . $league_id . 'error' . "\n",
			        'status' => 'error'
			    );
			}
		}elseif(1 == $type){
			for($i=0; $i<$add_times;$i++){
				$response = $api->sesetAccumulateLoginTime($player_id);
				if (isset($response->result) && $response->result == 'OK') {
				    $result[] = array(
				        'msg' => ' ( ' . $server->server_name .' ) : ' . $player_id . 'OK' . "\n",
				        'status' => 'ok'
				    );
				} else {
				    $result[] = array(
				        'msg' => ' ( ' . $server->server_name .' ) : ' . $player_id . 'error' . "\n",
				        'status' => 'error'
				    );
				}
			}
		}
		
		$msg = array(
		        'result' => $result
		);
		return Response::json($msg);
	}
   
}