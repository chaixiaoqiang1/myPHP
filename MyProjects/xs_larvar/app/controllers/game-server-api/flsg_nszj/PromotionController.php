<?php
class PromotionController extends \BaseController {
	private $type_ids = array(
			3,6,7,8,9,10,11,15,16,17,18,19,20,21,24,25,26,27,28,29,33,34,38,39,40,41,42,43,44,45,46,47,48,49,50,
			51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66
	);
	//假日活动需要在game-server-api/promotion/award/set设置奖励的活动type
	private function award_set_type($is_timing = 0){
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		if('nszj' == $game->game_code){
			$types = array(50,51,52,70,72,73,107);
		}elseif ('flsg' == $game->game_code) {
			if(1 == $is_timing){
				$types = array(50,51,52,63,64);
			}else{
				$types = array(50,51,52);
			}	
		}else{
			$types = array();
		}
		return $types;
	}

	private function initTable($file_name)
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        return $table;
    }

	public function index()
	{
		//$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServers();
		if(empty($servers))
		{
			App::abort(404);
			exit();
		}
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.index', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function index_ns()
	{
		$servers = Server::currentGameServers()->get();
	
		if(empty($servers))
		{
			App::abort(404);
			exit();
		}
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.index_ns', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function set()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		if($start_time < time() || $start_time >= $end_time)
		{ // to add
			$msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('error.basic_time_error')
			);
			return Response::json($msg, 404);
		}
		$time_arr1 = getdate($start_time);
		if (($time_arr1['hours'] == 23 && $time_arr1['minutes'] >= 51) || ($time_arr1['hours'] == 0 && $time_arr1['minutes'] <= 9)) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		$time_arr2 = getdate($end_time);
		if (($time_arr2['hours'] == 23 && $time_arr2['minutes'] >= 51) || ($time_arr2['hours'] == 0 && $time_arr2['minutes'] <= 9)) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		$open_time = date("Y-m-d\TH:i:s", $start_time);
		$close_time = date("Y-m-d\TH:i:s", $end_time);
		$types = array();
		foreach ( $this->type_ids as $i )
		{
			if(Input::get('promotion_type' . $i) == 'true')
			{
				$types[] = $i;
			}
		}
		if(empty($types)){
			return Response::json(array('error'=>'Please select a activity.'), 403);
		}
		$server_ids = Input::get('server_id');
		if($server_ids == '0'){
			return Response::json(array('error'=>'Please select a server.'), 403);
		}
		$result = array();
		foreach ( $server_ids as $server_id )
		{
			//$server = $this->getServersInternal($server_id);
			$server = Server::find($server_id);
			if(!$server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			foreach ( $types as $type )
			{
				$response = $api->addPromotion($type, $open_time, $close_time);
				if(isset($response->result) && $response->result == 'OK')
				{
					// Cache::add('promotion-close-time', $end_time, 100000);
					$result[] = array(
							'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
							'status' => 'ok'
					);
				} else
				{
					$result[] = array(
							'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
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
	public function lookup()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$server_ids = Input::get('server_id');
		$promotion_array = array(
				'1' => Lang::get('serverapi.promotion_longchuang'),
				'2' => Lang::get('serverapi.promotion_tili'),
				'3' => Lang::get('serverapi.promotion_qianzhuang'),
				'4' => Lang::get('serverapi.promotion_jingjiwang'),
				'5' => Lang::get('serverapi.promotion_level_chong'),
				'6' => Lang::get('serverapi.promotion_borrow_arrows')
		);
		if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
		$server_id = $server_ids[0]; // 随便选一个服务器
		$server = Server::find($server_id);
		//$server = $this->getServersInternal($server_id);
		if(! $server)
		{
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 404);
		}
		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		$response = $api->getPromotion();
		// var_dump($response);die();
		if(isset($response->error) && $response->error)
		{
			return Response::json($msg, 404);
		} else if(isset($response->activities))
		{
			$promotion_info = array();
			$activities = explode(',', $response->activities);
			foreach ( $activities as $v )
			{
				$act = explode(':', $v);
				if($act[0] >= 1 && $act[0] <= 6)
				{
					$promotion_info[] = array(
							'server_name' => $server->server_name,
							'name' => $promotion_array[$act[0]],
							'open_time' => date('Y-m-d H:i:s', $act[1]),
							'close_time' => date('Y-m-d H:i:s', $act[2])
					);
				}
			}
			$data = ( object ) $promotion_info;
			return Response::json($data);
		} else
		{
			return Response::json($msg, 404);
		}
	}
	public function lookup_ns()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$server_ids = Input::get('server_id');
		$promotion_array = array(
				'1' => Lang::get('serverapi.promotion_longchuang'),
				'2' => Lang::get('serverapi.promotion_tili'),
				'5' => Lang::get('serverapi.promotion_month_card'),
		);
		if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
		$server_id = $server_ids[0]; // 随便选一个服务器
		$server = Server::find($server_id);
		if(! $server)
		{
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 404);
		}
		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		$response = $api->getPromotion();
		// var_dump($response);die();
		if(isset($response->error) && $response->error)
		{
			return Response::json($msg, 404);
		} else if(isset($response->activities))
		{
			$promotion_info = array();
			$activities = explode(',', $response->activities);
			foreach ( $activities as $v )
			{
				$act = explode(':', $v);
// 				if($act[0] >= 1 && $act[0] <= 6)
				if($act[0] == 5 || $act[0] == 1)
				{
					$promotion_info[] = array(
							'server_name' => $server->server_name,
							'name' => $promotion_array[$act[0]],
							'open_time' => date('Y-m-d H:i:s', $act[1]),
							'close_time' => date('Y-m-d H:i:s', $act[2])
					);
				}
			}
			if(!$promotion_info){
				return Response::json($msg, 404);
			}
			$data = ( object ) $promotion_info;
			return Response::json($data);
		} else
		{
			return Response::json($msg, 404);
		}
	}
	public function close()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$types = array();
		foreach ( $this->type_ids as $i )
		{
			if(Input::get('promotion_type' . $i) == 'true')
			{
				$types[] = $i;
			}
		}
		$server_ids = Input::get('server_id');
		$result = array();
		if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
		foreach ( $server_ids as $server_id )
		{
			$server = Server::find($server_id);
			//$server = $this->getServersInternal($server_id);
			if(! $server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			foreach ( $types as $type )
			{
				$response = $api->closePromotion($type);
				if(isset($response->result) && $response->result == 'OK')
				{
					$result[] = array(
							'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
							'status' => 'ok'
					);
				} else
				{
					$result[] = array(
							'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
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
	public function turnplateIndex()
	{
		//$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServers();
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$table = $this->initTable('turnplate_activities');
		$table = $table->getData();
		$activities = array();
		foreach ($table as $value) {
			if('1' == $value->canopen){
				$activities[] = array(
					'name' => $value->name,
					'label' => $value->label
				);
			}
		}
		if(empty($servers))
		{
			App::abort(404);
			exit();
		}
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.turnplate', array(
						'servers' => $servers,
						'activities' => $activities
				))
		);
		return View::make('main', $data);
	}
	public function turnplateOpen()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$time_arr = getdate($start_time);
		$game_id = Session::get('game_id');
		//$game_id = 4;//三国本地测试
		$game = Game::find($game_id);

		if(4 != $game_id && ($start_time >= $end_time 
			|| ($time_arr['hours'] == 23 && $time_arr['minutes'] >= 51) 
			|| ($time_arr['hours'] == 0 && $time_arr['minutes'] <= 9)))
			{ // to add
			$msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('serverapi.lucky_dog_time_error')
			);
			return Response::json($msg, 404);
		}

		$time_arr2 = getdate($end_time);
		if (4 != $game_id && ($time_arr2['hours'] == 23 && $time_arr2['minutes'] >= 51)) {
			$msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('serverapi.lucky_dog_time_error')
			);
			return Response::json($msg, 404);
		}
		
		$turnplate_type = Input::get('turnplate_type');
		if(empty($turnplate_type)){
			$result[] = array(
						'msg' => $game->game_code.' can not open this activity.',
						'status' => 'error'
					);
			$msg = array(
				'result' => $result
			);
			Log::info(var_export($game->game_code, true));
			Log::info('is trying open wrong activity.');
			return Response::json($msg);
		}
		if(count($turnplate_type)>1){
			return Response::json(array('error'=>'一次只能开启一个活动!'),403);
		}
		$label = $turnplate_type[0];
		$server_ids = Input::get('server_id');
		$result = array();
		if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $is_timing = (int)Input::get('is_timing');
	    $activity = array();
		foreach ( $server_ids as $server_id )
		{
			//$server = $this->getServersInternal($server_id);
			$server = Server::find($server_id);
			if(! $server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			if(Session::get('game_id') != $server->game_id){
			    return Response::json(array('error'=>'please check the current platform and servers!'), 403);
			}
			//Log::info(var_export($server, true));
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$game_id = Session::get('game_id');
			$response = $api->openTurnplate($is_timing, $game->game_code, $start_time, $end_time, $label);
			if(1 == $is_timing){
				if($start_time<time()+1200){
					return Response::json($result = array('msg' => '保证活动开启时间大于当前时间20分钟以上', 'status' => 'error'));
				}
				if(empty($response)){
					return Response::json($result = array('msg'=>'设置error','status' => 'error'));
				}
				/*$activities_list = DB::select("select count(1) as num from timing_activities where `status` = 0 and `type`=1 and `game_id`={$game_id} and 
					((`start_time` between {$start_time} and {$end_time}) or (`end_time` between {$start_time} and {$end_time}) 
						or (`start_time`<{$start_time} and `end_time`>{$end_time}) or (`start_time`>{$start_time} and `end_time`<{$end_time}))");
				if($activities_list[0]->num > 0){
					return Response::json($result = array('msg'=>'与以前所设并未执行的或动时间有冲突，请重新设置活动删除以前冲突的活动','status' => 'error'));
				}*/
				$activity['game_id'] = $game_id;
				$activity['type'] = 1;//转盘类活动活动类型为1
				$activity['start_time'] = $start_time;
				$activity['end_time'] = $end_time;
				$activity['created_time'] = time();
				$activity['user_id'] = Auth::user()->user_id;
				$activity['main_server'] = implode(",", $server_ids);
				$activity['params'] = json_encode($response);
				try{
					$res = DB::table('timing_activities')->insertGetId($activity);
				}catch(\Exception $e){
					Log::error($e);
					return Response::json($result = array('msg'=>'设置error','status' => 'error'));
				}
				unset($activity);
				return Response::json($result = array('msg'=>'本活动预计在' . date("Y-m-d H:i:s", $start_time-600) .  '—' . date("Y-m-d H:i:s", $start_time+120) . '内开启','status' => 'ok'));
			}			
			if(isset($response->result) && $response->result == 'OK')
			{
				$result[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
						'status' => 'ok'
				);
			} else
			{
				Log::info(var_export($game->game_code, true));
				Log::info(var_export($label, true));
				Log::info(var_export($response, true));
				$result[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
						'status' => 'error'
				);
			}

		}
		$msg = array(
				'result' => $result
		);
		return Response::json($msg);
	}
	public function turnplateClose()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$server_ids = Input::get('server_id');
		$result = array();
		if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
		foreach ( $server_ids as $server_id )
		{
			//$server = $this->getServersInternal($server_id);
			$server = Server::find($server_id);
			if(! $server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$game_id = Session::get('game_id');
			//$game_id =4;//三国
			$game = Game::find($game_id);
			$response = $api->closeTurnplate($game->game_code, $game_id);
			if(isset($response->result) && $response->result == 'OK')
			{
				$result[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
						'status' => 'ok'
				);
			} else
			{
				$result[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' .'error' . "\n",
						'status' => 'error'
				);
			}
		}
		$msg = array(
				'result' => $result
		);
		return Response::json($msg);
	}
	public function turnplateLookup()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$server_ids = Input::get('server_id');
		if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
		$server_id = $server_ids[0]; // 随便选一个服务器
		$server = Server::find($server_id);
		//$server = $this->getServersInternal($server_id);
		if(! $server)
		{
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 404);
		}
		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		$game_id = Session::get('game_id');
		//$game_id = 1;
		$game = Game::find($game_id);

		$table = $this->initTable('turnplate_activities');
		$table = $table->getData();
		$activity = array();
		foreach ($table as $value) {
			$activity[$value->label] = $value->name;
		}
		$response = $api->lookupTurnplate($game->game_code);
		if(isset($response->error) && $response->error)
		{
			return Response::json($msg, 404);
		} else
		{
			$turnplate_info = array(
					"server_name" => $server->server_name,
					"name" => isset($activity[$response->label]) ? $activity[$response->label] : "未知的活动",
					"open_time" => date("Y-m-d H:i:s", $response->open_time),
					"close_time" => date("Y-m-d H:i:s", $response->close_time),
					"is_open" => $response->open ? "正在启用" : "还未到开启时间",
// 					"open_left_time" => $response->open_timer_left_time/60
			);
			$data = array(
					"data" => ( object ) $turnplate_info
			);
			return Response::json($data);
		}
	}
	public function beautyGiftIndex()
	{
	    $servers = $this->getUnionServers();
	
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.promotion.beauty-gift', array(
	                    'servers' => $servers
	            ))
	    );
	    return View::make('main', $data);
	}
	public function beautyGiftOpen()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $start_time = strtotime(trim(Input::get('start_time')));
	    $end_time = strtotime(trim(Input::get('end_time')));
	    $time_arr = getdate($start_time);
// 	    if($start_time >= $end_time || ($time_arr['hours'] == 23 && $time_arr['minutes'] >= 57) || ($time_arr['hours'] == 0 && $time_arr['minutes'] == 0 && $time_arr['seconds'] == 0))
	    if($start_time >= $end_time)
	    { // to add
	        $msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('error.basic_time_error')
			);
			return Response::json($msg, 404);
	    }
		$time_arr2 = getdate($end_time);
		if (($time_arr['hours'] == 23 && $time_arr['minutes'] >=51) || $time_arr['hours'] == 0 && $time_arr['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		if (($time_arr2['hours'] == 23 && $time_arr2['minutes'] >=51) || $time_arr2['hours'] == 0 && $time_arr2['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}

	    $open_time = date("Y-m-d\TH:i:s", $start_time);
	    $close_time = date("Y-m-d\TH:i:s", $end_time);
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $result = array();
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->addExchangePromotion($open_time, $close_time);
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function beautyGiftClose()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    $result = array();
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->closeExchangePromotion();
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function beautyGiftLookup()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $server_id = $server_ids[0]; // 随便选一个服务器
	    $server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	
	    $response = $api->getExchangePromotion();
	
	    if(isset($response->error) && $response->error)
	    {
	        return Response::json($msg, 404);
	    } else
	    {
	        $info = array(
	                "server_name" => $server->server_name,
	                "promotion_name" => Lang::get("serverapi.beauty_gift"),
	                "open_time" => date("Y-m-d H:i:s", $response->open_time),
	                "close_time" => date("Y-m-d H:i:s", $response->close_time),
	                "server_time" => date("Y-m-d H:i:s", $response->server_time),
	        );
	        $data = array(
	                "data" => ( object ) $info
	        );
	        return Response::json($data);
	    }
	}
	public function activityIndex()
	{
	    //$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServersDesc();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
	    $table = $this->init_table();
        $activity = $table->getData();
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.promotion.day-sign', array(
	                    'servers' => $servers,
	                    'activity' => $activity
	            ))
	    );
	    return View::make('main', $data);
	}
	public function activityOpen()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    //$time_arr = getdate($start_time);

	    $type = (int)Input::get('activity');
	    $start_time = strtotime(trim(Input::get('start_time')));
	    $end_time = strtotime(trim(Input::get('end_time')));

	    $time_arr1 = getdate($start_time);
	    $time_arr2 = getdate($end_time);

	    if (($time_arr1['hours'] == 23 && $time_arr1['minutes'] >=51) || $time_arr1['hours'] == 0 && $time_arr1['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		if (($time_arr2['hours'] == 23 && $time_arr2['minutes'] >=51) || $time_arr2['hours'] == 0 && $time_arr2['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}

	    if ($type < 7) {
	    	$open_time = date("Y-m-d\TH:i:s", $start_time);
	    	$close_time = date("Y-m-d\TH:i:s", $end_time);
	    } else {
	    	$open_time =  $start_time;
	    	$close_time = $end_time;
	    }
	    if($open_time >= $close_time)
	    { 
	        $msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('error.basic_time_error')
			);
			return Response::json($msg, 404);
	    }
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $game_arr = $this->getGameId();
	    $game_id = Session::get('game_id');
	    $result = array();
	    foreach ( $server_ids as $server_id )
	    {
	    	/*if (in_array($game_id, $game_arr)) {
				$ser = Server::where("game_id", "=", $game_id)->get();
				for ($i=0; $i < count($ser); $i++) { 
					if ($ser[$i]->server_internal_id == $server_id) {
						$server = $ser[$i];
						break;
					}
				}
			} else {
				$server = Server::find($server_id);
			}*/
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->openActivity($open_time, $close_time, $type);
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'OK' . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    //Log::info(var_export($result, true));
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function activityClose()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    $result = array();
	    $type = (int)Input::get('activity');
	    $game_id = Session::get('game_id');
	    $game_arr = $this->getGameId();
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    } 
	    foreach ( $server_ids as $server_id )
	    {
	    	/*if (in_array($game_id, $game_arr)) {
				$ser = Server::where("game_id", "=", $game_id)->get();
				for ($i=0; $i < count($ser); $i++) { 
					if ($ser[$i]->server_internal_id == $server_id) {
						$server = $ser[$i];
						break;
					}
				}
			} else {
				$server = Server::find($server_id);
			}*/
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->closeActivity($type);
	    
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function activityLookup()
	{
		$type_arr = array(
			'1'  => '夷州尋仙半價',
			'2'  => '購買體力半價',
			'3'  => '美人錢莊',
			'5'  => '月卡返利樂翻天',
			'6'  => '草船借箭獎勵翻番',
			'7'  => '簽到活動',
			'11' => '佳人有约',
			'12' => '儲值返元寶 越南儲值',
			'13' => '儲值返元寶	泰國儲值',
			'14' => '儲值返元寶	印尼儲值',
			'15' => '儲值返元寶	英語儲值',
			'16' => '消費返元寶	越南消費',
			'17' => '消費返元寶	泰國消費',
			'18' => '消費返元寶	印尼消費',
			'19' => '消費返元寶	英語消費',
			'20' => '購買體力半價、召喚龍船半價',
			'21' => '聚寶盆、將魂洗練返元寶',
			'22' => '搖簽、拜武聖獎勵翻番',
			'23' => '仙界活动',
			'24' => '儲值返元寶 台灣儲值',
			'25' => '消費返元寶	台灣消費',
			'26' => '秘宝商店',
			'27' => '信息公告',
			'28' => '冒险王',
			'29' => '红颜技示爱送礼',
			'30' => '红颜技冒险返利',
			'37' => '节日登录有礼',
			'38' => '合服累计登陆',
			'39' => '演武王中王',
			'40' => '天下第一帮',
			'41' => '称霸三国',
			'42' => 'Boos杀手'
		);
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $type = (int)Input::get('activity');
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $server_id = $server_ids[0]; // 随便选一个服务器
	    $game_id = Session::get('game_id');
	    $game_arr = $this->getGameId();

		$server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

	    $response = $api->lookupActivity($type);
	    $promotion_array = array(
				'1' => Lang::get('serverapi.promotion_longchuang'),
				'2' => Lang::get('serverapi.promotion_tili'),
				'3' => Lang::get('serverapi.promotion_qianzhuang'),
				'4' => Lang::get('serverapi.promotion_jingjiwang'),
				'5' => Lang::get('serverapi.promotion_level_chong'),
				'6' => Lang::get('serverapi.promotion_borrow_arrows')
		);
	    if(isset($response->error) && $response->error)
	    {
	        return Response::json($msg, 404);
	    } else
	    {
	        
			if ($type >= 7 ) {
				foreach ($response->activities as  $value) {
					if(!isset($type_arr[$value->type])){
						$type_arr[$value->type] = 'Sorry, I dont know.';
					}
					$info[] = array(
						"server_name" => $server->server_name,
		                "promotion_name" => $type_arr[$value->type] ."==". $value->type,
		                'is_open' => ($value->is_open == true) ? '是' : '否', 
		                "open_time" => date("Y-m-d H:i:s", $value->open_time),
		                "close_time" => date("Y-m-d H:i:s", $value->close_time),
		                "server_time" => date("Y-m-d H:i:s", $response->server_time),
						"type" => $value->type,
					);
				}

				foreach ($info as $key => $val) {
					if ($val['type'] == $type) {
						$result = $val;
					}
				}
			}
	        if ($type < 7) {
	        	$info = array();
				$activities = explode(',', $response->activities);
				foreach ( $activities as $v )
				{
					$act = explode(':', $v);
					if($act[0] >= 1 && $act[0] <= 6)
					{
						$info[] = array(
								'server_name' => $server->server_name,
								'promotion_name' => $promotion_array[$act[0]],
								'open_time' => date('Y-m-d H:i:s', $act[1]),
								'close_time' => date('Y-m-d H:i:s', $act[2]),
								'action_type' => $act[0]
						);
					}
				}
				//$info = ( object ) $info;
				foreach ($info as $key => $value) {
					if ($value['action_type'] == $type) {
						$result = $value;
					}
				}
	        }
			if (isset($result)) {
	        	return Response::json($result);
	        }else{
	        	$msg['error'] = Lang::get('serverapi.no_activity_info');
	        	return Response::json($msg, 403);
	        }
	    }
	}
	
	private function initTableKaifu()
	{
	    $game = Game::find(Session::get('game_id'));
	    $table = Table::init(public_path() . '/table/' . $game->game_code . '/kaifu.txt');
	    return $table;
	}
	public function openServerIndex()
	{
	    //$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServersDesc();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
	    $table = $this->initTableKaifu();
	    $kaifus = $table->getData();
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.promotion.open-server', array(
	                    'servers' => $servers,
	                    'kaifus' => $kaifus,
	            ))
	    );
	    return View::make('main', $data);
	}
	public function openServerOpen()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $type = Input::get('kaifu_id');
	    $server_ids = Input::get('server_id');
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $result = array();
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->openOpenServerActivity($type);
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function openServerClose()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $result = array();
	    $type = Input::get('kaifu_id');
	    foreach ( $server_ids as $server_id )
	    {
	    	//$server = $this->getServersInternal($server_id);
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->closeOpenServerActivity($type);
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function openServerLookup()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $table = $this->initTableKaifu();
	    $kaifus = $table->getData();
	    $kaifus_arr = array();
	    foreach($kaifus as $kaifu){
	        $kaifus_arr[$kaifu->id] = array(
	                'desc' => $kaifu->desc,
	                'titleDec' => $kaifu->titleDec,
	        );
	    }
	    $server_ids = Input::get('server_id');
	    $type = Input::get('kaifu_id');
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $server_id = $server_ids[0]; // 随便选一个服务器
	    //$server = $this->getServersInternal($server_id);
	    $server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	
	    $response = $api->LookupOpenServerActivity($type);
	
	    if(isset($response->error) && $response->error)
	    {
	        return Response::json($msg, 404);
	    } else
	    {
	        $result = array();
	        $activities = $response->activities;
	        foreach($activities as $activity){
	            if(isset($activity->last_days)){
	            	$last_days = $activity->last_days;
	            } else {
	                $last_days = 0; 
	            }
	            if(isset($activity->left_time)){
	            	$left_time = round($activity->left_time/3600).'小时';
	            	$activity_left_time = $activity->left_time;
	            } else {
	                $left_time = 0;
	                $activity_left_time = 0;
	            }
	            $result[] = array(
	                    "server_name" => $server->server_name,
	                    "kaifu_name" => $kaifus_arr[$activity->activity_id]['titleDec'],
	                    "is_open" => $activity->is_open ? "正在启用" : "还未到开启时间",
	                    "last_days" => $last_days.'---' .$activity_left_time.'---'.$activity->last_days,
	                    "left_time" => $left_time,
	            );
	        }
// 	        $data = array(
// 	                "data" => ( object ) $info
// 	        );
	        return Response::json((object)$result);
	    }
	}
	public function NSIndex()
	{
	    //$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServers($no_skip=1);

		$game_id = Session::get('game_id');
		//$game_id = 4;//三国本地测试
		$table = $this->initTable('activities');
		$table = $table->getData();
		$activities = array();
		foreach ($table as $value) {
			if('1' == $value->canopen){
				$activities[$value->value] = $value->name;
			}
		}

		$game = Game::find($game_id);
		$game_code = $game->game_code;
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.promotion.ns_index', array(
	                    'servers' => $servers,
	                    'game_code'=>$game_code,
	                    'activities' => $activities,
	            ))
	    );
	    return View::make('main', $data);
	}
	public function NSOpen()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $start_time = strtotime(Input::get('start_time'));
	    $end_time = strtotime(Input::get('end_time'));
	    //活动时间限制
	    $time_arr1 = getdate($start_time);
	    $time_arr2 = getdate($end_time);

	    if (($time_arr1['hours'] == 23 && $time_arr1['minutes'] >=51) || $time_arr1['hours'] == 0 && $time_arr1['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		if (($time_arr2['hours'] == 23 && $time_arr2['minutes'] >=51) || $time_arr2['hours'] == 0 && $time_arr2['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}

	    if($start_time >= $end_time)
	    { // to add
	        $msg = array(
	                'code' => Config::get('errorcode.unknow'),
	                'error' => Lang::get('error.basic_time_error')
	        );
	        return Response::json($msg, 404);
	    }
	    $types = Input::get('promotion_type');

	    if(empty($types)){
	    	return Response::json(array('error'=>'Did you select an activity?'), 403);
	    }
	    $ratio = Input::get('ratio');
	    $ratio2 = Input::get('ratio2');
	    $game_id = Session::get('game_id');

		//$game_id = 4;//三国本地测试

		$game = Game::find($game_id);
		if(count($types) > 1){	//有两个需要额外参数的功能，需要单独开启
			if('nszj' == $game->game_code){
				if(in_array(15, $types) || in_array(25, $types) || in_array(113, $types)){
					return Response::json(array('error'=>'特殊活动请单独开启'), 403);
				}
			}
		}
	    $result = array();
	    $extra_activity = 0;

	    //定时开启
	    $is_timing = (int)Input::get('is_timing');
	    $activity = array();
	    if(1 == $is_timing && count($types)>1){
	    	return Response::json(array('error'=>'定时开启时，请一次操作不要选择多个活动'), 403);
	    }
	    if(1 == $is_timing && !in_array($types[0], $this->award_set_type($is_timing))){
	    	return Response::json(array('error'=>'该活动不支持定时开启'), 403);
	    }
	    $url_type = Input::get('url_type');
	    //女神设置名人堂入选条件
	    if(1 == $url_type){
	    	$fighting = Input::get('fighting');
	    	$vip_lev = Input::get('vip_lev');
	    	if(!$fighting || !$vip_lev){
	    		return Response::json($msg, 403);
	    	}
	    	$misc = 'hall_of_fame_attack|'.$fighting.'|hall_of_fame_vip_level|'.$vip_lev;
	    }

	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        if(Session::get('game_id') != $server->game_id){
	            return Response::json(array('error'=>'please check the current platform and servers!'), 403);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        foreach ( $types as $type )
	        {
	            if(15 == $type){
	            	$response = $api->addNSUnifiedPromotion2($is_timing, $game->game_code, $type, $start_time, $end_time, $ratio);
	            	//var_dump($response);die();
	            }elseif(25 == $type){
                    $response = $api->addNSUnifiedPromotion2($is_timing, $game->game_code, $type, $start_time, $end_time, $ratio2);
                }elseif(113 == $type && 1 == $url_type){
                	$response = $api->setGuildAwardTitle(0, $game->game_code, $type, $misc);
                }else {
	            	$response = $api->addNSUnifiedPromotion($is_timing, $game->game_code, $type, $start_time, $end_time , $ratio);;
	            }

                if(1 == $is_timing){
                	if($start_time<time()+1200){
                		return Response::json($result = array('msg' => '保证活动开启时间大于当前时间20分钟以上', 'status' => 'error'));
                	}
                	if(empty($response)){
                		return Response::json($result = array('msg'=>'设置error','status' => 'error'));
                	}
                	/*$activities_list = DB::select("select count(1) as num from timing_activities where `status` = 0 and `type`=2 and from_server='{$type}' and `game_id`={$game_id} and 
                		((`start_time` between {$start_time} and {$end_time}) or (`end_time` between {$start_time} and {$end_time}) 
                			or (`start_time`<{$start_time} and `end_time`>{$end_time}) or (`start_time`>{$start_time} and `end_time`<{$end_time}))");
                	if($activities_list[0]->num > 0){
                		return Response::json($result = array('msg'=>'与以前所设并未执行的或动时间有冲突，请重新设置活动删除以前冲突的活动','status' => 'error'));
                	}*/
                	$activity['game_id'] = $game_id;
                	$activity['type'] = 2;//需要设置奖励的活动
                	$activity['start_time'] = $start_time;
                	$activity['end_time'] = $end_time;
                	$activity['created_time'] = time();
                	$activity['user_id'] = Auth::user()->user_id;
                	$activity['main_server'] = implode(",", $server_ids);
                	$activity['from_server'] = $type;
                	$activity['params'] = json_encode($response);
                	try{
                		$res = DB::table('timing_activities')->insertGetId($activity);
                	}catch(\Exception $e){
                		Log::error($e);
                		return Response::json($result = array('msg'=>'设置error','status' => 'error'));
                	}
                	unset($activity);
                	return Response::json($result = array('msg'=>'本活动预计在' . date("Y-m-d H:i:s", $start_time-600) .  '—' . date("Y-m-d H:i:s", $start_time+120) . '内开启','status' => 'ok'));
                }			

	            if(isset($response->result) && $response->result == 'OK')
	            {
	                if(in_array($type, $this->award_set_type()) || ('flsg' == $game->game_code && in_array($type, array(53, 54, 56))) 
	                	|| ('nszj' == $game->game_code && in_array($type, array(54, 55, 56, 108, 109)))){
	                	$extra_activity = 1;
	                }
	                // Cache::add('promotion-close-time', $end_time, 100000);
	                $result[] = array(
	                        'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                        'status' => 'ok'
	                );
	            } else
	            {
	                $result[] = array(
	                        'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                        'status' => 'error'
	                );
	            }
	        }
	    }
	    $msg = array(
	            'result' => $result,
	            'extra_activity' => $extra_activity
	    );
	    return Response::json($msg);
	}
	public function NSLookup()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_not_found')
	    );
	    $server_ids = Input::get('server_id');
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//本地三国测试
	    $game = Game::find($game_id);
	    $game_code = $game->game_code;

	    $table = $this->initTable('activities');
		$table = $table->getData();
		$promotion_array = array();
		foreach ($table as $value) {
			$promotion_array[$value->value] = $value->name;
		}
		if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $server_id = $server_ids[0]; // 随便选一个服务器
	    $server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	    $response = $api->getNSUnifiedPromotion($game->game_code);
 	    //var_dump($response);die();
	    $ratio = array(
	    	'500' => '5%',
	    	'1000' => '10%',
	    	'2000' => '20%',
	    	'3000' => '30%'
	    );
	    if(isset($response->error) && $response->error)
	    {
	    	$msg['error'] = 'response error';
	        return Response::json($msg, 404);
	    } else if(isset($response->activities))
	    {
	        $result = array();
	        $activities = $response->activities;
	        foreach($activities as $activity){
	        	if(isset($promotion_array[$activity->type]))
	            {
                	$result[] = array(
                	        "server_name" => $server->server_name,
                	        "promotion_name" => isset($promotion_flsg[$activity->type]) ? $promotion_flsg[$activity->type] : $promotion_array[$activity->type],
                	        //"ratio" => isset($ratio[$activity->ratio]) ? $ratio[$activity->ratio] : 0 ,
                	        "is_open" => $activity->is_open ? "正在启用" : "还未到开启时间",
                	        "open_time" => date("Y-m-d H:i:s",$activity->open_time),
                	        "close_time" => date("Y-m-d H:i:s",$activity->close_time),
                	);
	            }else{
	            	$result[] = array(
                	        "server_name" => $server->server_name,
                	        "promotion_name" => $activity->type.' 不明活动，请截图联系技术添加活动名称',
                	        //"ratio" => isset($ratio[$activity->ratio]) ? $ratio[$activity->ratio] : 0 ,
                	        "is_open" => $activity->is_open ? "正在启用" : "还未到开启时间",
                	        "open_time" => date("Y-m-d H:i:s",$activity->open_time),
                	        "close_time" => date("Y-m-d H:i:s",$activity->close_time),
                	);
	            }
	        }
	        return Response::json((object)$result);
	    } else
	    {
	        return Response::json($msg, 404);
	    }
	}
	public function NSClose()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );

	    $types = Input::get('promotion_type');

	    if(empty($types)){
	    	return Response::json(array('error'=>'Did you select an activity?'), 403);
	    }

	    $server_ids = Input::get('server_id');
	    $game = Game::find(Session::get('game_id'));
	    $result = array();
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        foreach ( $types as $type )
	        {
	            $response = $api->closeNSUnifiedPromotion($game->game_code, (int)$type);	//关闭活动要求必须是int类型
	            if(isset($response->result) && $response->result == 'OK')
	            {
	                $result[] = array(
	                        'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                        'status' => 'ok'
	                );
	            } else
	            {
	                $result[] = array(
	                        'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
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

	public function NSUrgentOpen()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }

	    $types = Input::get('promotion_type');
	   	if(empty($types)){
	    	return Response::json(array('error'=>'Did you select an activity?'), 403);
	    }

	    $type = $types[0];
	    $proportion = (int)Input::get('proportion');
	    if(!in_array($type, array(53,54,76))){//76位三国储值返利
	    	return Response::json(array('error'=>'请检查当前活动是否有改功能'), 403);
	    }

	    $game_id = Session::get('game_id');
	    //$game_id = 4;//三国本地测试
	    $game = Game::find($game_id);
	    $result = array();
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        if($type == 53){//限时抢购
	        	$response = $api->addNSUrgentPromotion($game->game_code,$label = 'second', $game_id);
	        }elseif($type == 54){//团购
	        	$response = $api->addNSUrgentPromotion2($game->game_code,$label = 'second', $game_id);
	        }elseif($type == 76 && 'flsg' == $game->game_code){
	        	$response = $api->setProportion($proportion);
	        }
           
            if(isset($response->result) && $response->result == 'OK')
            {
                // Cache::add('promotion-close-time', $end_time, 100000);
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
                        'status' => 'ok'
                );
            } else
            {
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
                        'status' => 'error'
                );
            }
	    }
	    $msg = array(
	         'result' => $result
	    );
	    return Response::json($msg);
	}
	public function NSUrgentClose()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );

	    $types = Input::get('promotion_type');

	    if(empty($types)){
	    	return Response::json(array('error'=>'Did you select an activity?'), 403);
	    }

	    $type = $types[0];
	    if(!in_array($type, array(53,54))){
	    	return Response::json(array('error'=>'紧急开启只有限时抢购和团购可以使用'), 403);
	    }

	    $server_ids = Input::get('server_id');
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//本地测试
	    $game = Game::find($game_id);
	    $result = array();
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    foreach ( $server_ids as $server_id )
	    {
	       
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        
	        if($type == 53){
	        	$response = $api->closeNSUrgentPromotion($game->game_code,$label = 'second', $game_id);
	        }elseif($type == 54) {
	        	$response = $api->closeNSUrgentPromotion2($game->game_code,$label = 'second', $game_id);
	        } 
            if(isset($response->result) && $response->result == 'OK')
            {
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
                        'status' => 'ok'
                );
            } else
            {
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
                        'status' => 'error'
                );
            }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}

	public function init_table()
	{
		$game = Game::find(Session::get('game_id'));
        $table = Table::init(
                public_path() . '/table/' . 'flsg' . '/activity.txt');
        return $table;
	}


	/*
		QQ活动
	*/
	
	public function qqIndex()
	{
	    //$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServers();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
	    $table = $this->init_table();
        $activity = $table->getData();
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.qq.qqactivity', array(
	                    'servers' => $servers,
	                    'activity' => $activity
	            ))
	    );
	    return View::make('main', $data);
	}
	public function qqActivityOpen()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    //$time_arr = getdate($start_time);

	    $type = (int)Input::get('activity');
	    $start_time = strtotime(trim(Input::get('start_time')));
	    $end_time = strtotime(trim(Input::get('end_time')));

	    $time_arr1 = getdate($start_time);
	    $time_arr2 = getdate($end_time);

	    if (($time_arr1['hours'] == 23 && $time_arr1['minutes'] >=51) || $time_arr1['hours'] == 0 && $time_arr1['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		if (($time_arr2['hours'] == 23 && $time_arr2['minutes'] >=51) || $time_arr2['hours'] == 0 && $time_arr2['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}

	    if ($type < 7) {
	    	$open_time = date("Y-m-d\TH:i:s", $start_time);
	    	$close_time = date("Y-m-d\TH:i:s", $end_time);
	    } else {
	    	$open_time =  $start_time;
	    	$close_time = $end_time;
	    }

	    
	    if($open_time >= $close_time)
	    { 
	        $msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('error.basic_time_error')
			);
			return Response::json($msg, 404);
	    }
	    $server_ids = Input::get('server_id');
	    $result = array();
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	       
	        $response = $api->openActivity($open_time, $close_time, $type);
	        
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function qqActivityClose()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    $result = array();
	    $type = (int)Input::get('activity');
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->closeActivity($type);
	    
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function qqActivityLookup()
	{
		$type_arr = array(
			'1'  => '夷州尋仙半價',
			'2'  => '購買體力半價',
			'3'  => '美人錢莊',
			'5'  => '月卡返利樂翻天',
			'6'  => '草船借箭獎勵翻番',
			'7'  => '簽到活動',
			'11' => '佳人有约',
			'12' => '儲值返元寶 越南儲值',
			'13' => '儲值返元寶	泰國儲值',
			'14' => '儲值返元寶	印尼儲值',
			'15' => '儲值返元寶	英語儲值',
			'16' => '消費返元寶	越南消費',
			'17' => '消費返元寶	泰國消費',
			'18' => '消費返元寶	印尼消費',
			'19' => '消費返元寶	英語消費',
			'20' => '購買體力半價、召喚龍船半價',
			'21' => '聚寶盆、將魂洗練返元寶',
			'22' => '搖簽、拜武聖獎勵翻番',
			'23' => '仙界活动',
			'24' => '儲值返元寶 台灣儲值',
			'25' => '消費返元寶	台灣消費',
			'26' => '秘宝商店',
			'27' => '信息公告',
			'28' => '冒险王',
			'29' => '红颜技示爱送礼',
			'30' => '红颜技冒险返利',
			'37' => '节日登录有礼',
			'38' => '合服累计登陆',
			'39' => '演武王中王',
			'40' => '天下第一帮',
			'41' => '称霸三国',
			'42' => 'Boos杀手'
		);
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $type = (int)Input::get('activity');
	    $server_ids = Input::get('server_id');
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $server_id = $server_ids[0]; // 随便选一个服务器
	    $server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	    $action = (int)Input::get('action');
	    $promotion_array = array(
				'1' => Lang::get('serverapi.promotion_longchuang'),
				'2' => Lang::get('serverapi.promotion_tili'),
				'3' => Lang::get('serverapi.promotion_qianzhuang'),
				'4' => Lang::get('serverapi.promotion_jingjiwang'),
				'5' => Lang::get('serverapi.promotion_level_chong'),
				'6' => Lang::get('serverapi.promotion_borrow_arrows')
		);

	    $response = $api->lookupActivity($type);
	    //var_dump(Input::all());die();
	    if(isset($response->error) && $response->error)
	    {
	        return Response::json($msg, 404);
	    } else
	    {
	        if ($type >= 7) {
	        	foreach ($response->activities as  $value) {
					$info[] = array(
						"server_name" => $server->server_name,
		                "promotion_name" => isset($type_arr[$value->type]) ? $type_arr[$value->type] : '',
		                'is_open' => ($value->is_open == true) ? '是' : '否', 
		                "open_time" => date("Y-m-d H:i:s", $value->open_time),
		                "close_time" => date("Y-m-d H:i:s", $value->close_time),
		                "server_time" => date("Y-m-d H:i:s", $response->server_time),
		                'type' => $value->type
					);
				}
				foreach ($info as $key => $value) {
					if ($value['type'] == $type) {
						$result = $value;
					}
				}
	        }elseif ($type < 7) {
	        	$info = array();
				$activities = explode(',', $response->activities);
				foreach ( $activities as $v )
				{
					$act = explode(':', $v);
					if($act[0] >= 1 && $act[0] <= 6)
					{
						$info[] = array(
								'server_name' => $server->server_name,
								'promotion_name' => $promotion_array[$act[0]],
								'open_time' => date('Y-m-d H:i:s', $act[1]),
								'close_time' => date('Y-m-d H:i:s', $act[2]),
								'action_type' => $act[0]
						);
					}
				}
				//$info = ( object ) $info;
				foreach ($info as $key => $value) {
					if ($value['action_type'] == $type) {
						$result = $value;
					}
				}
	        }
	        if (isset($result)) {
	        	return Response::json($result);
	        }else{
	        	$msg['error'] = Lang::get('serverapi.no_activity_info');
	        	return Response::json($msg, 403);
	        }
	    }
	}
	
	/*
	 * 腾讯开服活动
	*/

	public function qqOpenServerIndex()
	{
	    $servers = Server::currentGameServers()->get();
	
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
	    $table = $this->initTableKaifu();
	    $kaifus = $table->getData();
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.promotion.qqopen-server', array(
	                    'servers' => $servers,
	                    'kaifus' => $kaifus,
	            ))
	    );
	    return View::make('main', $data);
	}
	public function qqOpenServerOpen()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $type = Input::get('kaifu_id');
	    $server_ids = Input::get('server_id');
	    $result = array();
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->openOpenServerActivity($type);
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error'. "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function qqOpenServerClose()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    $result = array();
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $type = Input::get('kaifu_id');
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->closeOpenServerActivity($type);
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	public function qqOpenServerLookup()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $table = $this->initTableKaifu();
	    $kaifus = $table->getData();
	    $kaifus_arr = array();
	    foreach($kaifus as $kaifu){
	        $kaifus_arr[$kaifu->id] = array(
	                'desc' => $kaifu->desc,
	                'titleDec' => $kaifu->titleDec,
	        );
	    }
	    $server_ids = Input::get('server_id');
	    $type = Input::get('kaifu_id');
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $server_id = $server_ids[0]; // 随便选一个服务器
	    $server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	
	    $response = $api->LookupOpenServerActivity($type);
	    if(isset($response->error) && $response->error)
	    {
	        return Response::json($msg, 404);
	    } else
	    {
	        $result = array();
	        $activities = $response->activities;
	        foreach($activities as $activity){
	            if(isset($activity->last_days)){
	            	$last_days = $activity->last_days;
	            } else {
	                $last_days = 0; 
	            }
	            if(isset($activity->left_time)){
	            	$left_time = round($activity->left_time/3600).'小时';
	            } else {
	                $left_time = 0; 
	            }
	            $result[] = array(
	                    "server_name" => $server->server_name,
	                    "kaifu_name" => $kaifus_arr[$activity->activity_id]['titleDec'],
	                    "is_open" => $activity->is_open ? "正在启用" : "还未到开启时间",
	                    "last_days" => $last_days,
	                    "left_time" => $left_time,
	            );
	        }

	        return Response::json((object)$result);
	    }
	}

	//印尼新活动
	public function treaSureShopIndex()
	{
		$server = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.promotion.shop', array(
					'server' => $server
			    ))
		);
		return View::make('main', $data);
	}

	public function treaSureShopOpen()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$rules =array(
			'start_time' => 'required',
			'end_time' => 'required',
			'server_id' => 'required'
		); 
		$type = intval(26);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		//活动时间限制
		$time_arr1 = getdate($start_time);
	    $time_arr2 = getdate($end_time);

	    if (($time_arr1['hours'] == 23 && $time_arr1['minutes'] >=51) || $time_arr1['hours'] == 0 && $time_arr1['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		if (($time_arr2['hours'] == 23 && $time_arr2['minutes'] >=51) || $time_arr2['hours'] == 0 && $time_arr2['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}

		if ($start_time > $end_time) {
			return Response::json($msg, 403);
		}
		$server_ids = Input::get('server_id');
		if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
		foreach ( $server_ids as $server_id ) {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	       
	        $response = $api->openActivity($start_time, $end_time, $type);
	        
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);

	}

	public function treaSureShopClose()
	{
		$msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    $result = array();
	    $type = intval(26);
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    foreach ( $server_ids as $server_id ) {
	        $server = Server::find($server_id);
	        if(! $server) {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->closeActivity($type);
	    
	        if(isset($response->result) && $response->result == 'OK'){
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
	}
	
    public function treasureShopLookUp()
    {
    	$msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $server_id = $server_ids[0]; // 随便选一个服务器
	    $server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	    $type = intval(26);
	    $response = $api->lookupActivity($type);
	    //var_dump($response);die();
	    if(isset($response->error) && $response->error) {
	        return Response::json($msg, 404);
	    } elseif(isset($response->activities)) {
			foreach ($response->activities as  $value) {
				$info[] = array(
					"server_name" => $server->server_name,
	                "promotion_name" => Lang::get('serverapi.treasure_shop'),
	                'is_open' => ($value->is_open == true) ? '是' : '否', 
	                "open_time" => date("Y-m-d H:i:s", $value->open_time),
	                "close_time" => date("Y-m-d H:i:s", $value->close_time),
	                "server_time" => date("Y-m-d H:i:s", $response->server_time),
	                'type' => $value->type
				);
			}
			foreach ($info as $key => $value) {
				if ($value['type'] == 26) {
					$result = $value; 
				}
			}
	        return Response::json($result);
	    }else{
	    	Log::info('lookupActivity:'.var_export($response,true));
	    	return Response::json(array('error'=>'游戏端返回数据异常!'), 403);
	    }
    }

    public function qqInviteFriendIndex()
    {
    	$servers = Server::currentGameServers()->get();
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.promotion.qqfriends',array(
    			'servers' => $servers
    		))
    	);
    	return View::make('main', $data);
    }

    public function qqInviteFriendData()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unkonw'),
    		'error' => Lang::get('error.basic_input_error'),
    	);
    	$rules = array(
    		'server_id' => 'required'
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		return Response::json($msg, 403);
    	}
    	$server_id = Input::get('server_id');
    	$len = count($server_id);
    	$result = array();
    	for ($i=0; $i < $len; $i++) { 
    		$server[$i] = Server::find($server_id[$i]);
    		if (!$server[$i]) {
    			return Response::json($msg, 403);
    		}
    		$data[$i] = array(
    			'1' => $server[$i]->api_server_ip,
    			'2' => $server[$i]->api_server_port,
    			'3' => $server[$i]->api_dir_id,
    		);
    		//Log::info(var_export($data[$i], true)); 
    		$api[$i] = GameServerApi::connect($server[$i]->api_server_ip, $server[$i]->api_server_port, $server[$i]->api_dir_id);
    		//Log::info(var_export($api[$i], true));
    		$result[$i] = $api[$i]->getQqFriendData();
    		$result[$i]->server_name = "";
    		$result[$i]->server_name = $server[$i]->server_name;
    	}
    	if (isset($result)) {
    		return Response::json($result);
    	} else {
    		return Response::json($msg, 403);
    	}
    }

    //批量护送
    public function playerEscortIndex()
    {
    	$servers = $this->getUnionServers();
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.promotion.escort', array(
    			'servers' => $servers,
    			'game_code' => $game->game_code,
    			))
    	); 	
    	return View::make('main', $data);
    }

    public function playerEscortOpen()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unkonw'),
    		'error' => ''
    	);
    	$rules = array(
    		'action_type' => 'required',
    		'content' => 'required'
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg['error'] = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$action_type = trim(Input::get('action_type'));
    	$content = trim(Input::get('content'));
    	$con_arr1 = explode("\n", $content);
    	$game_id = Session::get('game_id');
    	$activity_type = Input::get('activity_type');
    	foreach ($con_arr1 as $key => $value) {
    		$data[] = $value;
    	}
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	$platform_id = Session::get('platform_id');
    	$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	if ($action_type == 1) {
    		foreach ($data as $key => $value) {
    			$vv = explode("\t", $value);
    			$server = Server::whereRaw("game_id = {$game_id} and server_track_name = '{$vv[0]}'")->first();
    			if(!$server){
    				$msg['error'] = Lang::get('error.server_not_suit_game');
    				return Response::json($msg, 403);
    			}
    			$user = $slave_api->getIdByName($platform_id, $game_id, $server->server_internal_id, $vv[1], $player_id = "");
    			//var_dump($user);die();
    			if ($user->body[0]) {
    				//$player_id = $user->player_id;
    				$body = $user->body[0];
    				$player_id = $body->player_id;

    			}else{
    				$msg['error'] = Lang::get('error.player_not_found');
    				return Response::json($msg, 403);
    			}
    			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
    			$response = $api->playerEscort(intval($player_id), intval($vv[2]), $activity_type, $game->game_code);
    			if ($response->result == "OK") {
    				$result[] = array( 
    					'msg' => $server->server_name . "  (" . $vv[1] . ")" . "  OK",
	    				'status' => 'ok'
    				);
    			}else{
    				$result[] = array(
	    				'msg' => $server->server_name . "  (" . $vv[1]. ")" . "  FAIL",
	    				'status' => 'error'
	    			);
    			}
    		}
    	} elseif ($action_type == 2) {
    		foreach ($data as $key => $value) {
	    		$vv = explode("\t", $value);
	    		//$server = Server::where('server_track_name', $vv[0])->where('game_id', $game_id)->get();
	    		$server = Server::whereRaw("game_id = {$game_id} and server_track_name = '{$vv[0]}'")->first();
	    		if(!$server){
    				$msg['error'] = Lang::get('error.server_not_suit_game');
    				return Response::json($msg, 403);
    			}
	    		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	    		$response = $api->playerEscort(intval($vv[1]), intval($vv[2]), $activity_type, $game->game_code);
	    		if (isset($response->result) && $response->result == "OK") {
	    			$result[] = array(
	    				'msg' => $server->server_name . "  (" . $vv[1] . ")" . "  OK",
	    				'status' => 'ok'
	    			);
	    		}else{
	    			//return Response::json(array('error'=>'please check the current platform and servers!'), 403);
	    			$result[] = array(
	    				'msg' => $server->server_name . "  (" . $vv[1] . ")" . "  FAIL",
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
	
	public function getServersInternal($server_id)
    {
    	$game_arr = $this->getGameId();
    	$game_id = Session::get('game_id');
    	if (in_array($game_id, $game_arr)) {
			$ser = Server::where("game_id", "=", $game_id)->get();
			for ($i=0; $i < count($ser); $i++) { 
				if ($ser[$i]->server_internal_id == $server_id) {
					$server = $ser[$i];
					break;
				}
			}
		} else {
			$server = Server::find($server_id);
		}
    	 return $server; 
    }

    //女神感恩节 美人送礼
    public function beautyGiftNSZJIndex()
    {
    	$servers = Server::currentGameServers()->get();
	
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.promotion.beauty-gift-nszj', 
	            							array('servers' => $servers)
	            						)
	    );
	    return View::make('main', $data);
    }

    public function beautyGiftNSZJDeal()
    {
    	$msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $rules = array(
	    		'init_times' => 'required',
	    		'dayly_times' => 'required'
	    );
	    $validator = Validator::make(Input::all(), $rules);
	    if($validator->fails()){
	    	$msg['error'] = Lang::get('error.basic_input_error');
	    	return Response::json($msg, 403);
	    }	    
	    $start_time = strtotime(trim(Input::get('start_time')));
	    $end_time = strtotime(trim(Input::get('end_time')));
	    $init_times = Input::get('init_times');
	    $dayly_times = Input::get('dayly_times');
	    if($init_times == ''|| $dayly_times =='')
	    {
	    	$msg['error'] = Lang::get('error.basic_input_error');
	    	return Response::json($msg, 403);
	    }
	 //    $type='';
	 //    switch ($action) {
		// 	case 'open':
		// 		$type = 'open';
		// 		break;			
		// 	case 'close':
		// 		$type = 'close';
		// 		break;
		// 	case 'look':
		// 		$type = 'look';
		// 		break;			
		// }
	    if($start_time >= $end_time)
	    {
	        $msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('error.basic_time_error')
			);
			return Response::json($msg, 404);
	    }
	    $start_time_arr = getdate($start_time);
		$end_time_arr = getdate($end_time);
		if (($start_time_arr['hours'] == 23 && $start_time_arr['minutes'] >=51) 
			|| $start_time_arr['hours'] == 0 && $start_time_arr['minutes'] <= 9) 
		{
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		if (($end_time_arr['hours'] == 23 && $end_time_arr['minutes'] >=51) 
			|| $end_time_arr['hours'] == 0 && $end_time_arr['minutes'] <= 9) 
		{
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}

	    $open_time = date("Y-m-d\TH:i:s", $start_time);
	    $close_time = date("Y-m-d\TH:i:s", $end_time);
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids))
	    {
	    	Log::info(var_export('server_ids is empty.',true));
	    	return Response::json($msg, 404);
	    }
	    $result = array();
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            Log::info(var_export('Can not find server of'.$server_id,true));
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->beautyGiftNSZJ($open_time, $close_time, $init_times, $dayly_times);
	        if(!isset($response))
	        {
	        	Log::info(var_export($init_times.'-'.$dayly_times,true));
	        	Log::info(var_export($response, true));
	        }
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
    }

    public function beautyGiftNSZJClose()
    {
    	$msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    $result = array();
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    foreach ( $server_ids as $server_id )
	    {
	        $server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        $response = $api->beautyGiftNSZJClose();
	        if(isset($response->result) && $response->result == 'OK')
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
	            'result' => $result
	    );
	    return Response::json($msg);
    }

    public function beautyGiftNSZJLook()
    {
	    $server_ids = Input::get('server_id');
	    if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
	    $server_id = $server_ids[0]; // 随便选一个伺服器
	    $server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	    $response = $api->beautyGiftNSZJLook();
	    if(isset($response->error) && $response->error) 
	    {
	        return Response::json($response);
	    } else 
	    {
				$is_open = ($response->open_time<$response->server_time && $response->close_time>$response->server_time)?true:false;
				$info = array(
					"server_name" => $server->server_name,
	                'is_open' => ($is_open == true) ? '开启中' : '已结束', 
	                "open_time" => date("Y-m-d H:i:s", $response->open_time),
	                "close_time" => date("Y-m-d H:i:s", $response->close_time),
	                "init_times" => $response->init_times,
	                "dayly_times" => $response->dayly_times,
				);

			$data = array(
				'data' =>(object) $info
				);
	        return Response::json($data);
	    }
    }
    /*女神转转活动*/
    public function aroundIndex()
	{
		$servers = $this->getUnionServers();
		$game = Game::find(Session::get('game_id'));
		if(empty($servers))
		{
			App::abort(404);
			exit();
		}
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.around', array(
						'servers' => $servers,
						'game_code' => $game->game_code,
				))
		);
		return View::make('main', $data);
	}
	public function aroundOpen()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$time_arr = getdate($start_time);
		if($start_time >= $end_time 
			|| ($time_arr['hours'] == 23 && $time_arr['minutes'] >= 51) 
			|| ($time_arr['hours'] == 0 && $time_arr['minutes'] <= 9))
		{ // to add
			$msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('serverapi.lucky_dog_time_error')
			);
			return Response::json($msg, 404);
		}

		$time_arr2 = getdate($end_time);
		if ($time_arr2['hours'] == 23 && $time_arr2['minutes'] >= 51) {
			$msg = array(
					'code' => Config::get('errorcode.unknow'),
					'error' => Lang::get('serverapi.lucky_dog_time_error')
			);
			return Response::json($msg, 404);
		}
		
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		if ($game->game_code == 'nszj') {
			$label_arr = array(
				'1' => 'tw_gold',
			);
		}elseif ($game->game_code == 'flsg') {
			$label_arr = array(
				'1' => 'tw_gold',
			);
		}else{
			return Response::json(array('error'=>'error game'), 403);
		}
		$turnplate_type = ( int ) Input::get('turnplate_type');
		if(isset($label_arr[$turnplate_type])){
			$label = $label_arr[$turnplate_type];
		}else
		{
			return Response::json(array('error'=>'error label'), 403);
		}
		$server_ids = Input::get('server_id');
		$result = array();
		if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
		foreach ( $server_ids as $server_id )
		{
			$server = Server::find($server_id);
			if(! $server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$game_id = Session::get('game_id');
			$response = $api->openAround($game->game_code, $start_time, $end_time, $label);
			
			if(isset($response->result) && $response->result == 'OK')
			{
				$result[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
						'status' => 'ok'
				);
			} else
			{
				Log::info(var_export($game->game_code, true));
				Log::info(var_export($label, true));
				Log::info(var_export($response, true));
				$result[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
						'status' => 'error'
				);
			}
		}
		$msg = array(
				'result' => $result
		);
		return Response::json($msg);
	}
	public function aroundClose()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$server_ids = Input::get('server_id');
		$game = Game::find(Session::get('game_id'));
		$result = array();
		if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }

	    if ($game->game_code == 'nszj') {
	    	$label_arr = array(
	    		'1' => 'tw_gold',
	    	);
	    }elseif ($game->game_code == 'flsg') {
	    	$label_arr = array(
	    		'1' => 'tw_gold',
	    	);
	    }else{
	    	return Response::json(array('error'=>'error game'), 403);
	    }
	    $turnplate_type = ( int ) Input::get('turnplate_type');
	    if(isset($label_arr[$turnplate_type])){
	    	$label = $label_arr[$turnplate_type];
	    }else
	    {
	    	return Response::json(array('error'=>'error label'), 403);
	    }

		foreach ( $server_ids as $server_id )
		{
			$server = Server::find($server_id);
			if(! $server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$game_id = Session::get('game_id');
			$game = Game::find($game_id);
			$response = $api->closeAround($game->game_code, $label);
			if(isset($response->result) && $response->result == 'OK')
			{
				$result[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
						'status' => 'ok'
				);
			} else
			{
				$result[] = array(
						'msg' => ' ( ' . $server->server_name . ' ) : ' .'error' . "\n",
						'status' => 'error'
				);
			}
		}
		$msg = array(
				'result' => $result
		);
		return Response::json($msg);
	}
	public function aroundLookup()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$server_ids = Input::get('server_id');
		if(!$server_ids){
	    	return Response::json(array('error'=>'请选择服务器'), 403);
	    }
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if(! $server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$game_id = Session::get('game_id');
			$game = Game::find($game_id);
			$response = $api->lookupAround($game->game_code);
			if(isset($response->label)){
				$around_info[] = array(
						'server_name' => $server->server_name,
						'name' => $response->label ? Lang::get('serverapi.' . $response->label) : "NULL",
						'open_time' => date("Y-m-d H:i:s", $response->open_time),
						'close_time' => date("Y-m-d H:i:s", $response->close_time),
						'is_open' => $response->open ? "正在启用" : "未开启",
	// 					"open_left_time" => $response->open_timer_left_time/60
				);
			}
		}
		if(!isset($around_info)){
			return Response::json(array('error'=>'error'), 403);
		}

		$result = array(
				'data' => $around_info
		);
		return Response::json($result);
		
	}
	private function initAwardTable()
	{
	    $game = Game::find(Session::get('game_id'));
	    $table = Table::init(
	        public_path() . '/table/' . $game->game_code . '/award.txt');
	    return $table;
	}
	private function initItemTable()
	{
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if(in_array($game_id, $this->area_item_id)){
        	$table = Table::init(
            public_path() . '/table/' . $game->game_code . '/item'.$game_id.'.txt');
        }else{
        	$table = Table::init(
            public_path() . '/table/' . $game->game_code . '/item.txt');
        }
        return $table;
	}
	private function initArrayItemTable()
	{
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if(in_array($game_id, $this->area_item_id)){
        	$table = Table::initarray(
            public_path() . '/table/' . $game->game_code . '/item'.$game_id.'.txt');
        }else{
        	$table = Table::initarray(
            public_path() . '/table/' . $game->game_code . '/item.txt');
        }
        return $table;
	}
	public function awardSetIndex()
	{
		$servers = $this->getUnionServers();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
		$award_table = $this->initAwardTable();
		$award = $award_table->getData();
		$item_table = $this->initArrayItemTable();
		$items = $item_table->getData();
		foreach ($items as $k => $v) {
			$item[] = 
				$v['name'] . ':' . $v['id'];
		}
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$award_types = array(
			'1' => Lang::get('serverapi.total_consumption'),
            '2' => Lang::get('serverapi.total_refill'),
            '3' => Lang::get('serverapi.single_refill'),
			);
		if('nszj' == $game->game_code){
			$award_types['21'] = Lang::get('serverapi.cumulative_sign');
			$award_types['23'] = Lang::get('serverapi.total_consumption').'2';
			$award_types['24'] = Lang::get('serverapi.total_refill').'2';
			$award_types['34'] = Lang::get('serverapi.refill_big_rate');
			$award_types['58'] = Lang::get('serverapi.single_refill').'2';
		}
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.award_set',
					array(
						'servers' => $servers,
						'award' => $award,
                    	'item' => $item,
                    	'game_id' => $game_id,
                    	'game_code' => $game->game_code,
                    	'award_types' => $award_types,
					))
		);
		return View::make('main', $data);
	}
	
	public function awardSet()
	{
		
	    $msg = array(
	           'code' => Config::get('errorcode.unknow'),
	           'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $type = Input::get('award_type');
	    if('0' == $type){
	    	return Response::json(array('error'=>'未选择要设置的活动'), 403);
	    }

    	$spring_recharge_goal = (int)Input::get('spring_recharge_goal');
    	$spring_recharge_goal = $spring_recharge_goal > 0 ? $spring_recharge_goal : 0;
   		$spring_recharge_rebate = (int)Input::get('spring_recharge_rebate');
   		$spring_recharge_rebate = $spring_recharge_rebate * 100;
   		$spring_recharge_rebate = $spring_recharge_rebate > 0 ? $spring_recharge_rebate : 0;
   		if(34 == $type && !($spring_recharge_goal*$spring_recharge_rebate)){	//如果是类型34，那么以上两个参数为必须
   			return Response::json(array('error'=> Lang::get('slave.necessary_input')), 403);
   		}

	    $result = array();
	    //$award1="x";
	    $award2="y";
	    $game_id = Session::get('game_id');
	    $game = Game::find(Session::get('game_id'));
	    $area_id = (int)Input::get('area_id');//针对台湾和英文世界服分国旗
	    if(($game_id == 59 || $game_id == 63) && $area_id != 0){//针对台湾和英文分国旗
            $game_id = $area_id;
        }
	    for($i = 1;$i <= 12; $i++){
	    	$award1="x";
	    	$file_id = (int)Input::get('file_id' . $i);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
	    	if($file_id == 0){
	    		break;
	    	}
	    	if($file_id <= 0){
	    		return Response::json(array('error'=>'档数不能小于等于0'), 403);
	    	}
	    	for($j=65;$j<70;$j++){
	    		$temp=strtolower(chr($j));
	    		$award_id = (int)Input::get('award_id_' . $temp . $i);
	    		$award_value= Input::get('award_value_' . $temp . $i);
	    		if($award_id == 0){break;}
	    		if($award_value <= 0){
	    			$k = $j-64;
	    			return Response::json(array('error'=>"第$i 个档位第$k 个物品的奖励数必须大于0"), 403);
	    		}
	    		if($award_id != 9){//如果不是物品
	    			//$award1=$award1 . $award_id . ',' . $award_value . '&';
	    			$award1=$award1 . $award_id . ',' . 0 . ',' . $award_value . '&';
	    		}else{
	    			$item_id_name = Input::get('item_id_' . $temp . $i);
	    			$gift_id_name = explode(":", $item_id_name);
	    			try{
	    			    $item_id = (int)$gift_id_name[1];
	    			}catch(\Exception $e){
	    			    return Response::json($msg, 403);
	    			}
	    			$award1=$award1 . $award_id . ',' . $item_id. ',' . $award_value . '&';
	    		}
	    	}
	    	//截掉每个award1最后的&和前面的x
	    	$award1=substr($award1, 1,strlen($award1)-2) . ';';
	    	$award2=$award2 . $file_id . ':' . $award1;	
	    }
	   	$award=substr($award2, 1,strlen($award2)-2);
	   	$is_timing = (int)Input::get('is_timing');
	   	$start_time = strtotime(trim(Input::get('start_time')));
	   	foreach ( $server_ids as $server_id ){
	   		$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        if(Session::get('game_id') != $server->game_id){
	            return Response::json(array('error'=>'please check the current platform and servers!'), 403);
	        }
	   		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

            $response = $api->setActivityAward($is_timing, $game->game_code, $type, $award, $game_id, $spring_recharge_goal, $spring_recharge_rebate);
            if(1 == $is_timing){
            	if($start_time<time()+1200){
            		return Response::json($result = array('msg' => '保证活动开启时间大于当前时间20分钟以上', 'status' => 'error'));
            	}
            	if(empty($response)){
            		return Response::json($result = array('msg'=>'设置error','status' => 'error'));
            	}
            	/*$activities_list = DB::select("select count(1) as num from timing_activities where `status` = 0 and `type`=3 and `from_server`='{$type}' and `game_id`={$game_id} and 
            		((`start_time` between {$start_time} and {$end_time}) or (`end_time` between {$start_time} and {$end_time}) 
            			or (`start_time`<{$start_time} and `end_time`>{$end_time}) or (`start_time`>{$start_time} and `end_time`<{$end_time}))");
            	if($activities_list[0]->num > 0){
            		return Response::json($result = array('msg'=>'与以前所设并未执行的或动时间有冲突，请重新设置活动删除以前冲突的活动','status' => 'error'));
            	}*/
            	$activity['game_id'] = $game_id;
            	$activity['type'] = 3;//设置活动奖励
            	$activity['start_time'] = $start_time; 
            	$activity['created_time'] = time();
            	$activity['user_id'] = Auth::user()->user_id;
            	$activity['main_server'] = implode(",", $server_ids);
            	$activity['from_server'] = $type;
            	$activity['params'] = json_encode($response);
            	try{
            		$res = DB::table('timing_activities')->insertGetId($activity);
            	}catch(\Exception $e){
            		Log::error($e);
            		return Response::json($result = array('msg'=>'设置error','status' => 'error'));
            	}
            	unset($activity);
            	return Response::json($result = array('msg'=>'本次设置预计在' . date("Y-m-d H:i:s", $start_time-600) .  '—' . date("Y-m-d H:i:s", $start_time+120) . '内开启,请确定好对应的假日活动是否有设置','status' => 'ok'));
            }			
	   		//Log::info(var_export($response, true));
	   		//var_dump($response);die();
		   	if(isset($response->type) && isset($response->yunyin_award))
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->type . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	   	}
	   	$msg = array(
				'result' => $result
		);
		//Log::info(var_export($msg,true));
		return Response::json($msg);
	   	
	}
	public function awardSetLook()
	{
	    $promotion_array = array(
	    		'1' => Lang::get('serverapi.total_consumption'),
	            '2' => Lang::get('serverapi.total_refill'),
	            '3' => Lang::get('serverapi.single_refill'),
	            '21' => Lang::get('serverapi.cumulative_sign'),
				'24' => Lang::get('serverapi.total_refill').'2',
				'34' => Lang::get('serverapi.refill_big_rate'),
	    );
	    $game_id = Session::get('game_id');
	    $game = Game::find(Session::get('game_id'));
	    $server_ids = Input::get('server_id'); // 随便选一个服务器
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $type = Input::get('award_type');
	    $area_id = (int)Input::get('area_id');//针对台湾和英文世界服分国旗
	    if(($game_id == 59 || $game_id == 63) && $area_id != 0){//针对台湾和英文分国旗
	        $game_id = $area_id;
	    }
	    foreach ( $server_ids as $server_id) {
	    	$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	            
	        $response = $api->getActivityAward($game->game_code, $type, $game_id);

		    if(isset($response->type) && isset($response->yunyin_award))
		    {
		        //$result = array();
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->type . ':' . $response->yunyin_award . "\n",
	                    'status' => 'ok'
	            );
				//Log::info(var_export($msg,true));	
		    }else
		    {
		         $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
		    }  
	    }
	    $msg = array(
			'result' => $result
		);
		return Response::json($msg);  
	    
	}

	public function limitBuyIndex()
	{
		$servers = $this->getUnionServers();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
		$item_table = $this->initItemTable();
		$item = $item_table->getData();
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.limit_buy',
					array(
						'servers' => $servers,
                    	'item' => $item
					))
		);
		return View::make('main', $data);
	}
	public function limitBuySet()
	{
		$server_ids = Input::get('server_id');
	    $msg = array(
	           'code' => Config::get('errorcode.unknow'),
	           'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $result = array();
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//本地测试
	    $game = Game::find($game_id);
	    $is_clean = Input::get('is_clean');
	    $false_goods = array();
	    $true_goods = array();
	    for($i = 1;$i <= 4; $i++){
	    	$is_remove = Input::get('is_remove' . $i);
	    	//Log::info(var_export($is_remove,true));die();
	    	$item_id = (int)Input::get('item_id' . $i);
	    	$limit_num = (int)Input::get('limit_num' . $i);
	    	$need_recharge = (int)Input::get('need_recharge' . $i);
	    	$total_num = (int)Input::get('total_num' . $i);
	    	$original_price = (int)Input::get('original_price' . $i);
	    	$price = (int)Input::get('price' . $i);
			if($is_remove == 'true' && $item_id != 0){
	    		$true_goods[$i-1] = array(
	    			'item_id' => $item_id
	    		);
	    		
	    	}elseif($item_id != 0){
	    		$false_goods[$i-1] = array(
	    			'item_id' => $item_id,
	    			'original_price' => $original_price,
	    			'price' => $price,
	    			'num' => $total_num,
	    			'limit_num' => $limit_num,
	    			'need_recharge' => $need_recharge	
	    		);
	    	}
	    }

	   	foreach ( $server_ids as $server_id ){
	   		$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	   		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	   		if(!empty($false_goods)){
	   			$false_goods = array_values($false_goods);
	   			//Log::info(var_export($false_goods,true));die();
	   			$response = $api->limitBuySetPromotion($game->game_code, $false_goods, $game_id, $is_clean);
			   	if(isset($response->goods) && $response->open =='true')
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->open  . "\n",
		                    'status' => 'ok'
		            );
		        } else
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		        }
	   		}
	   		if(!empty($true_goods)){
	   			$true_goods = array_values($true_goods);
	   			//Log::info(var_export($true_goods,true));die();
	   			$response = $api->limitBuyRemovePromotion($game->game_code, $true_goods, $game_id);
			   	if(isset($response->goods) && $response->open =='true')
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->open . "移除" . "\n",
		                    'status' => 'ok'
		            );
		        } else
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		        }
	   		}
            
	   	}
	   	$msg = array(
				'result' => $result
		);
		//Log::info(var_export($msg,true));
		return Response::json($msg);
	   	
	}
	public function limitBuyLook()
	{
	    $result = array();
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//三国本地测试
	    $game = Game::find($game_id);
	    $server_ids = Input::get('server_id'); // 随便选一个服务器
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    foreach ( $server_ids as $server_id) {
	    	$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        
	        $response = $api->getlimitBuyPromotion($game->game_code, $game_id);
	        
		    if(isset($response->goods) && $response->open =='true')
		        {
		           $body = $response->goods;
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $body[0]->item_id . ";label:" . $response->label . "\n",
		                    'status' => 'ok'
		            );
		        } else
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		        }
	    }
	    $msg = array(
			'result' => $result
		);
		return Response::json($msg);  
	    
	}
	public function groupBuyIndex()
	{
		$servers = $this->getUnionServers();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
		$item_table = $this->initItemTable();
		$item = $item_table->getData();
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.group_buy',
					array(
						'servers' => $servers,
                    	'item' => $item
					))
		);
		return View::make('main', $data);
	}
	public function groupBuySet()
	{
	    $msg = array(
	           'code' => Config::get('errorcode.unknow'),
	           'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $result = array();
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//本地测试
	    $game = Game::find($game_id);
	    $is_clean = Input::get('is_clean');
	    $false_goods = array();
	    $true_goods = array();
	    for($i = 1;$i <= 6; $i++){
	    	$is_remove = Input::get('is_remove' . $i);
	    	//Log::info(var_export($is_remove,true));die();
	    	$item_id = (int)Input::get('item_id' . $i);
	    	//$current_price = (int)Input::get('current_price' . $i);
	    	$price = (int)Input::get('price' . $i);
	    	//$current_step = (int)Input::get('current_step' . $i);
	    	//$final_step = (int)Input::get('final_step' . $i);
	    	$steps = (String)Input::get('steps' . $i);
	    	//$real_num = (int)Input::get('real_num' . $i);
	    	//$virtual_num = (int)Input::get('virtual_num' . $i);
			if($is_remove == 'true' && $item_id != 0){
	    		$true_goods[$i-1] = array(
	    			'item_id' => $item_id
	    		);
	    		
	    	}elseif($item_id != 0){
	    		$steps = (String)str_replace("+",",",$steps);
	    		$false_goods[$i-1] = array(
	    			'item_id' => $item_id,
	    			//'current_price' => $current_price,
	    			'price' => $price,
	    			//'current_step' => $current_step,
	    			//'final_step' => $final_step,
	    			'steps' => $steps
	    			//'real_num' => $real_num,
	    			//'virtual_num' =>$virtual_num	
	    		);
	    	}
	    }
	   	foreach ( $server_ids as $server_id ){
	   		$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	   		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	   		if(!empty($false_goods)){//设置
	   			$false_goods = array_values($false_goods);
	   			//Log::info(var_export($false_goods,true));
	   			$response = $api->groupBuySetPromotion($game->game_code, $false_goods, $game_id, $is_clean);
			   	if(isset($response->goods) && $response->open =='true')
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->open  . "\n",
		                    'status' => 'ok'
		            );
		        } else
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		        }
	   		}
	   		if(!empty($true_goods)){//移除
	   			$true_goods = array_values($true_goods);
	   			//Log::info(var_export($true_goods,true));
	   			$response = $api->groupBuyRemovePromotion($game->game_code, $true_goods,$game_id);
			   	if(isset($response->goods) && $response->open =='true')
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->open . "移除" . "\n",
		                    'status' => 'ok'
		            );
		        } else
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		        }
	   		}
            
	   	}
	   	$msg = array(
				'result' => $result
		);
		//Log::info(var_export($msg,true));
		return Response::json($msg);
	   	
	}
	public function groupBuyLook()
	{
	    $result = array();
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//三国本地测试
	    $game = Game::find($game_id);
	    $server_ids = Input::get('server_id'); // 随便选一个服务器
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    foreach ( $server_ids as $server_id) {
	    	$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        
	        $response = $api->getgroupBuyPromotion($game->game_code,$game_id);
	        
		    if(isset($response->goods) && $response->open =='true')
	        {
	           $body = $response->goods;
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $body[0]->item_id . "\n",
	                    'status' => 'ok'
	            );
	        } else
	        {
	            $result[] = array(
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
	                    'status' => 'error'
	            );
	        }
	    }
	    $msg = array(
			'result' => $result
		);
		return Response::json($msg);  
	    
	}
	public function groupBuyChange()
	{
	    $result = array();
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//三国本地测试
	    $game = Game::find($game_id);
	    $item_id = (int)Input::get('item_id_change');
	    $delta =(int)Input::get('delta');
	    $server_ids = Input::get('server_id'); // 随便选一个服务器
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    foreach ( $server_ids as $server_id) {
	    	$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        
	        $response = $api->groupBuyChangePromotion($game->game_code ,$item_id, $delta,$game_id);
	        
		    if(isset($response->result) && $response->result == 'OK')
            {
                // Cache::add('promotion-close-time', $end_time, 100000);
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
                        'status' => 'ok'
                );
            } else
            {
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
                        'status' => 'error'
                );
            }
	    }
	    $msg = array(
			'result' => $result
		);
		return Response::json($msg);  
	    
	}
	public function onlineAwardIndex()
	{
		$servers = $this->getUnionServers();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
		$item_table = $this->initItemTable();
		$item = $item_table->getData();
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.online_award',
					array(
						'servers' => $servers,
                    	'item' => $item
					))
		);
		return View::make('main', $data);
	}
	public function onlineAwardSet()
	{
	    $msg = array(
	           'code' => Config::get('errorcode.unknow'),
	           'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $result = array();
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//本地测试
	    $game = Game::find($game_id);
	    $is_clean = Input::get('is_clean');
	    $false_goods = array();
	    $true_goods = array();
	    $sep_goods = array();

	    for($i = 1;$i <= 6; $i++){
	    	$is_remove = Input::get('is_remove' . $i);
	    	//Log::info(var_export($is_remove,true));die();
	    	$item_id = (int)Input::get('item_id' . $i);
	    	$begin_time = strtotime(trim(Input::get('begin_time' . $i)));
	    	$end_time = strtotime(trim(Input::get('end_time' . $i)));
	    	$price = (int)Input::get('price' . $i);
	    	$vip = (int)Input::get('vip' . $i);
	
			if($is_remove == 'true' && $item_id != 0){
	    		$true_goods[$i-1] = array(
	    			'item_id' => $item_id,
	    			'price' => $price,
	    			'vip' => $vip
	    		);
	    	}elseif($item_id != 0){
	    		$false_goods[$i-1] = array(
	    			'begin_time' => $begin_time,
	    			'end_time' => $end_time,
	    			'item_id' => $item_id,
	    			'price' => $price,
	    			'vip' => $vip	
	    		);
	    	}
	    }
	    //Log::info(var_export($false_goods,true));die();
	   	foreach ( $server_ids as $server_id ){
	   		$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	   		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	   		if(!empty($false_goods)){
	   			$false_goods = array_values($false_goods);
	   			//Log::info(var_export($false_goods,true));
	   			$response = $api->onlineAwardSetPromotion($game->game_code, $false_goods, $game_id, $is_clean);
			   	if(isset($response->awards) && isset($response->server_time))
		        {
		            $body = $response->awards;
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $body[0]->item_id  . "\n",
		                    'status' => 'ok'
		            );
		        } else
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		        }
	   		}
	   		if(!empty($true_goods)){
	   			$true_goods = array_values($true_goods);
	   			//Log::info(var_export($true_goods,true));
	   			$response = $api->onlineAwardRemovePromotion($game->game_code, $true_goods, $game_id);
			   	if(isset($response->awards) && isset($response->server_time))
		        {
		            $body = $response->awards;
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $body[0]->item_id . "移除" . "\n",
		                    'status' => 'ok'
		            );
		        } else
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		        }
	   		}
            
	   	}
	   	$msg = array(
				'result' => $result
		);
		//Log::info(var_export($msg,true));
		return Response::json($msg);
	   	
	}
	public function onlineAwardLook()
	{
	    $result = array();
	    $game_id = Session::get('game_id');
	    //$game_id = 4;//三国本地测试
	    $game = Game::find($game_id);
	    $server_ids = Input::get('server_id'); // 随便选一个服务器
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    foreach ( $server_ids as $server_id) {
	    	$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	        
	        $response = $api->getOnlineAwardPromotion($game->game_code,$game_id);
	        
		    if(isset($response->awards) && isset($response->server_time))
		        {
		           $body = $response->awards;
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $body[0]->item_id . "\n",
		                    'status' => 'ok'
		            );
		        } else
		        {
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		        }
	    }
	    $msg = array(
			'result' => $result
		);
		return Response::json($msg);  
	    
	}
	public function allServerFightIndex(){
		$game_id = Session::get('game_id');
		$servers = $this->getUnionServers($no_skip=1);
		if (empty($servers)) {
		    App::abort(404);
		    exit();
		}
		$data= array(
			'content' => View::make('serverapi.flsg_nszj.activity.god_remove',
				array(
                    'servers' => $servers
                ))
		);
		return View::make('main',$data);
	}

	public function allServerFightSet(){
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error')
		);
		$rules = array(
            'gift_data' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $server_id = Input::get('server_id');
        $name_or_id = Input::get('name_or_id');
        $is_alertIntegral = (int)Input::get('is_alertIntegral');
        if(0 == $server_id){
        	return Response::json(array('error'=>'请选择服务器'), 403);
        }
        $server = Server::find($server_id);
        if (!$server) {
           return Response::json(array('error'=>'Not Found Server'), 403);
        }
        $gift_datas = Input::get('gift_data');
        $gift_datas = explode("\n", $gift_datas);
        foreach ($gift_datas as &$v) {
            $v = trim($v);
        }
        unset($v);
        $gift_datas = array_unique($gift_datas);
        $str = '';
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        if(0 == $is_alertIntegral){
        	foreach ($gift_datas as $gift_data) {
        	    $gift_data = explode("\t", $gift_data, 4);
        	    if (count($gift_data) != 4) {
        	        return Response::json(array('error'=>'输入格式错误'), 403);
        	    }

        	    $player_id = 0;
        	    if('2' == $name_or_id){
        	        $player = $api->getPlayerInfoByName($gift_data[0]);
        	        if(isset($player->player_id)){
        	            $gift_data[0] = (int)$player->player_id;
        	        }else{
        	            $error = $gift_data[0] . ' ' . 'Not Found player_id';
        	            return Response::json(array('error'=>"$error"), 403);
        	        }       
        	    }
        	    $str = $str . '|' . implode('|', $gift_data);    
        	}
        	$list = str_replace(' ','',substr($str, 1)); 
        	$response = $api->allServerFightSet($list);
        	if (isset($response->result) && $response->result == 'OK') {
        	    $result[] = array(
        	        'msg' => ' ( ' . $server->server_name . ' ) : ' .
        	            $response->result . "\n",
        	        'status' => 'ok'
        	    );
        	} else {
        	    $result[] = array(
        	        'msg' => ' ( ' . $server->server_name . ' ) : ' . "\n",
        	        'status' => 'error'
        	    );
        	}
        }elseif(1 == $is_alertIntegral){
        	foreach ($gift_datas as $gift_data) {
        	    $gift_data = explode("\t", $gift_data, 4);
        	    if (count($gift_data) != 4) {
        	        return Response::json(array('error'=>'输入格式错误'), 403);
        	    }

        	    $player_id = 0;
        	    if('2' == $name_or_id){
        	        $player = $api->getPlayerInfoByName($gift_data[0]);
        	        if(isset($player->player_id)){
        	            $gift_data[0] = (int)$player->player_id;
        	        }else{
        	            $error = $gift_data[0] . ' ' . 'Not Found player_id';
        	            return Response::json(array('error'=>"$error"), 403);
        	        }       
        	    }
        	    $response = $api->allServerAlertIntegral((int)$gift_data[0], (int)$gift_data[1], (int)$gift_data[2], (int)$gift_data[3]);
        	    if (isset($response->result) && $response->result == 'OK') {
        	        $result[] = array(
        	            'msg' => ' ( ' . $server->server_name . ' ) : ' .
        	                $response->result . "\n",
        	            'status' => 'ok'
        	        );
        	    } else {
        	        $result[] = array(
        	            'msg' => ' ( ' . $server->server_name . ' ) : ' . "\n",
        	            'status' => 'error'
        	        );
        	    }
        	}
        }
        
        $res = array(
            'result' => $result,
        );
        return Response::json($res);
	}

	public function allServerFightLook(){
		$server_id = Input::get('server_id');
        if(0 == $server_id){
        	return Response::json(array('error'=>'请选择服务器'), 403);
        }
        $server = Server::find($server_id);
        if (!$server) {
           return Response::json(array('error'=>'Not Found Server'), 403);
        }
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        $response = $api->allServerFightLook();
        if(isset($response->list)){
            $list = explode('|', $response->list);
            $results = array();
            for($i=0; $i+3<count($list); $i+=4){
            	//$result['player_name'] = $list[$i];
            	$result['player_id'] = $list[$i];
            	$result['server_id'] = $list[$i+1];
        		$result['opertor_id'] = $list[$i+2];
            	$result['team_id'] = $list[$i+3];
            	$results []= (object)$result;
            }
            if($results){
            	return Response::json($results);
            }else{
            	Log::info('0xbc86:' . var_export($response->list,true));
            	return Response::json(array('error'=>'未查询到分组！'), 403);
            }
            
        }else{
            return Response::json(array('error'=>'未查询到分组！'), 403);
        }  

	}
}