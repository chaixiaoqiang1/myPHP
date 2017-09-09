<?php
class DailyUpdateController extends \BaseController {

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
				'content' => View::make('serverapi.flsg_nszj.dailyUpdate', array(
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

        $rules = array(
            'server_ids' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }

        $server_ids = Input::get('server_ids');
        Log::info('server_ids:'.var_export($server_ids, true));
        $game_id = Session::get('game_id');
		foreach ( $server_ids as $server_id )
		{
			$server = Server::find($server_id);
			if(!$server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

            $response = $api->setDailyUpdate($game_id);
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
	public function check()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);

        $rules = array(
            'server_ids' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }

		$server_ids = Input::get('server_ids');
        $promotion_info = array();
        $game_id = Session::get('game_id');
        foreach ( $server_ids as $server_id )
        {
	    	$server = Server::find($server_id);
    		if(! $server)
	    	{
	    		$msg['error'] = Lang::get('error.basic_not_found');
	      		return Response::json($msg, 404);
	    	}
	    	$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		    $response = $api->checkUpdateTime($game_id);            //查询最近一次game_sever进行daily_update的时间
	       	$update_time = array();
            if(isset($response->update_time))
	    	{
                $update_time = array(
                    'server_name' => $server->server_name,
                    'daily_update_time' => date('Y-m-d H:i:s', $response->update_time),
                );
            }

            $version_response = $api->checkVersionUpdateTime($game_id);     //当前版本后端的更服时间
            if(isset($version_response->update_time))
            {
                $update_time['version_update_time'] = date('Y-m-d H:i:s', strtotime($version_response->update_time));
            }
            $promotion_info[] = $update_time;
		}
        $data = ( object ) $promotion_info;
        return Response::json($data);
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
		if(empty($servers))
		{
			App::abort(404);
			exit();
		}
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.promotion.turnplate', array(
						'servers' => $servers
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
		//$game_id = 4;//三国本地测试
		$game = Game::find($game_id);
		if ($game->game_code == 'flsg') {
			$label_arr = array(
				'1' => 'zhuanpan',
				'2' => 'laohuji',
				'3' => 'fanpai',
				'4' => 'wabao',
				'5' => 'shejian',
				'7' => 'xmas',
			);
		}
		if ($game->game_code == 'nszj') {
			$label_arr = array(
				'1' => 'zhuanpan',
				'2' => 'laohuji',
				'3' => 'zhongqiu',
				'6' => 'icesnow',
				'7' => 'xmas',
			);
		}
		if ($game->game_code == 'dld'){
			$label_arr = array(
				'4' => 'wabao',
				'7' => 'xmas'
			);
		}
		$turnplate_type = ( int ) Input::get('turnplate_type');
		if(isset($label_arr[$turnplate_type])){
			$label = $label_arr[$turnplate_type];
		}else
		{
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
		$server_ids = Input::get('server_id');
		$result = array();
		foreach ( $server_ids as $server_id )
		{
			//$server = $this->getServersInternal($server_id);
			$server = Server::find($server_id);
			if(! $server)
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			//Log::info(var_export($server, true));
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$game_id = Session::get('game_id');
			$response = $api->openTurnplate($is_timing = 0, $game->game_code, $start_time, $end_time, $label);

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
		$response = $api->lookupTurnplate($game->game_code);

		if(isset($response->error) && $response->error)
		{
			return Response::json($msg, 404);
		} else
		{
			$turnplate_info = array(
					"server_name" => $server->server_name,
					"name" => $response->label ? Lang::get('serverapi.' . $response->label) : "NULL",
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
	    $servers = Server::currentGameServers()->get();

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
		$servers = $this->getUnionServers();
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
		$servers = $this->getUnionServers();
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
	    $result = array();
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
	        $response = $api->openOpenServerActivity($type);
	        //Log::info(var_export($response, true));
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
	            } else {
	                $left_time = 0;
	            }
	            $result[] = array(
	                    "server_name" => $server->server_name,
	                    "kaifu_name" => $kaifus_arr[$activity->activity_id]['titleDec'],
	                    "is_open" => $activity->is_open ? "正在启用" : "还未到开启时间",
	                    "last_days" => $last_days.'---' .$activity->left_time .'---'.$activity->last_days,
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
		$servers = $this->getUnionServers();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.promotion.ns_index', array(
	                    'servers' => $servers
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
	    $types = array();
	    foreach ( $this->type_ids as $i )
	    {
	        if(Input::get('promotion_type' . $i) == 'true')
	        {
	            $types[] = $i;
	        }
	    }
	    $type18_19 = (int)Input::get('promotion_type18_19');
	    if($type18_19 != 0){
	    	$types[] = $type18_19;
	    }
	    $type43_46 = (int)Input::get('promotion_type43_46');
	    if($type43_46 != 0){
	    	$types[] = $type43_46;
	    }
	    $type47_48 = (int)Input::get('promotion_type47_48');
	    if($type47_48 != 0){
	    	$types[] = $type47_48;
	    }
	    if(empty($types)){
	    	return Response::json(array('error'=>'Did you select an activity?'), 403);
	    }
	    $ratio = Input::get('ratio');
	    $ratio2 = Input::get('ratio2');
	    $game = Game::find(Session::get('game_id'));
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
	        foreach ( $types as $type )
	        {
	            if ($type != 15 && $type != 25) {
	            	$response = $api->addNSUnifiedPromotion($is_timing = 0, $game->game_code, $type, $start_time, $end_time);;
	            }elseif($type == 15){
	            	$response = $api->addNSUnifiedPromotion2($is_timing = 0, $game->game_code, $type, $start_time, $end_time, $ratio);
	            	//var_dump($response);die();
	            }
                elseif($type == 25){
                    $response = $api->addNSUnifiedPromotion2($is_timing = 0, $game->game_code, $type, $start_time, $end_time, $ratio2);
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
	            	Log::info(var_export($response, true));
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
	public function NSLookup()
	{
	    $server_ids = Input::get('server_id');
	    $promotion_array = array(
	    		'7' => Lang::get('serverapi.qiandaohuodong'),
	            '8' => Lang::get('serverapi.promotion_half_tili'),
	            '9' => Lang::get('serverapi.promotion_lianjing'),
	            '10' => Lang::get('serverapi.promotion_taluopai'),
	            '11' => Lang::get('serverapi.promotion_summer'),
	            '15' => Lang::get('serverapi.promotion_shop'),
	            '16' => Lang::get('serverapi.promotion_wakuang'),
	            '18' => Lang::get('serverapi.chunjieqiandao'),
	            '19' => Lang::get('serverapi.nvshenguodanian'),
	            '24' => Lang::get('serverapi.juanzhouhuodong'),
	            '25' => Lang::get('serverapi.chongwushilian'),
	            '26' => Lang::get('serverapi.shennongbanjia'),
	            '43' => Lang::get('serverapi.xiaofeisongliA'),
	            '44' => Lang::get('serverapi.xiaofeisongliB'),
	            '45' => Lang::get('serverapi.xiaofeisongliC'),
	            '46' => Lang::get('serverapi.xiaofeisongliD'),
	            '47' => Lang::get('serverapi.chunjieqitianleNew'),
	            '48' => Lang::get('serverapi.chunjieqitianleOld'),
	    );

	    $game = Game::find(Session::get('game_id'));
	    $server_id = $server_ids[0]; // 随便选一个服务器
	    $server = Server::find($server_id);
	    if(! $server)
	    {
	        $msg['error'] = Lang::get('error.basic_not_found');
	        return Response::json($msg, 404);
	    }
	    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	    $response = $api->getNSUnifiedPromotion($game->game_code);
// 	    var_dump($response);die();
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
	        	if(in_array($activity->type, $this->type_ids))
	            {
	                $result[] = array(
	                        "server_name" => $server->server_name,
	                        "promotion_name" => $promotion_array[$activity->type],
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
	    $types = array();
	    foreach ( $this->type_ids as $i )
	    {
	        if(Input::get('promotion_type' . $i) == 'true')
	        {
	            $types[] = $i;
	        }
	    }
	    $type43_46 = (int)Input::get('promotion_type43_46');
	    if($type43_46 != 0){
	    	$types[] = $type43_46;
	    }
	    $type47_48 = (int)Input::get('promotion_type47_48');
	    if($type47_48 != 0){
	    	$types[] = $type47_48;
	    }
	    $server_ids = Input::get('server_id');
	    $game = Game::find(Session::get('game_id'));
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
	        foreach ( $types as $type )
	        {
	            $response = $api->closeNSUnifiedPromotion($game->game_code, $type);
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
		                "promotion_name" => $type_arr[$value->type],
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
		$server = Server::currentGameServers()->get();
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
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->error . "\n",
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
	                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->error . "\n",
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
	    } else {
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
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.promotion.escort', array('servers' => $servers))
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
    			$response = $api->playerEscort(intval($player_id), intval($vv[2]));
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
	    		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
	    		$response = $api->playerEscort(intval($vv[1]), intval($vv[2]));
	    		if ($response->result == "OK") {
	    			$result[] = array(
	    				'msg' => $server->server_name . "  (" . $vv[1] . ")" . "  OK",
	    				'status' => 'ok'
	    			);
	    		}else{
	    			$result[] = array(
	    				'msg' => $server->server_name . "  (" . $vv[1] . ")" . "  FAIL",
	    				'status' => 'error'
	    			);
	    		}
	    	}
    	}


    	if (isset($result)) {
    		return Response::json($result);
    	}
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
}