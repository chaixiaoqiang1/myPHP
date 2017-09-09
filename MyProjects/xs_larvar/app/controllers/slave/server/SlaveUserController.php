<?php

class SlaveUserController extends \SlaveServerBaseController{

	private $days = array(
		2, 3, 4, 5, 6, 7, 14
	);

	public function getUser()
	{
		$game_id = (int)Input::get('game_id');
		$uid = Input::get('uid');
		$email = Input::get('email');

		/*Log::info(var_export($uid, true));
		Log::info(var_export($email, true));
		Log::info(var_export($game_id, true));*/
		$server_internal_id = (int)Input::get('server_internal_id');
		if ($uid) {
			return $this->getUserByUID($uid, $server_internal_id, $game_id);
		} else if ($email) {
			return $this->getUserByEmail($email, $server_internal_id, $game_id);
		} 
		return Response::json(array(), 404);	
	}

	private function getUserByUID($uid, $server_internal_id, $game_id)
	{
		$user = SlaveUser::on($this->db_qiqiwu)
			->where('uid', $uid);
		$user = $user->first();
		if ($user) {
			$players = SlaveCreatePlayer::on($this->db_qiqiwu)
				->where('uid', $uid);
			if ($game_id == 38 || $game_id == 51) {
				$players->where('game_id', $game_id);
			}
			if ($server_internal_id) {
				$players->where('server_id', $server_internal_id);
			}
			$players = $players->get();
			$user->players = array();
			if ($players) {
				foreach ($players as $v) {
					if ($this->game_id == 0) {
						break;
					}
					$order = $this->getPlayerOrderStat($v->uid, $v->server_id, $game_id);
					$v->all_pay_amount = $order->pay_amount;
					$v->all_pay_times = $order->count;
					if(in_array($game_id, Config::get('game_config.agent_games'))){//神仙道或君王2
					    $v->first_lev = 0;
					} else {
						$v->first_lev = $this->getFirstPayLevel($v->player_id, $v->uid, $order->server_id, $v->server_id,$game_id);
					}
				}
				$user->players = $players->toArray();
			}
//			Log::info(var_export($user, true));
			return Response::json($user);
		} else {
			return Response::json(array(), 404);
		}
	}

	private function getUserByEmail($email, $server_internal_id, $game_id)
	{
		$user = SlaveUser::on($this->db_qiqiwu)
			->where('login_email', $email)
			->orWhere('nickname', $email);
		$user = $user->first();
		if ($user) {
			$players = SlaveCreatePlayer::on($this->db_qiqiwu)
				->where('uid', $user->uid);
			if ($game_id == 38 || $game_id == 51) {
				 $players->where('game_id', $game_id);
			}

			if ($server_internal_id) {
				$players->where('server_id', $server_internal_id);
			}
			$players = $players->get();
			$user->players = array();
			if ($players) {
				foreach ($players as $v) {
					if ($game_id == 0) {
						break;
					}
					$order = $this->getPlayerOrderStat($v->uid, $v->server_id, $game_id);
					$v->all_pay_amount = $order->pay_amount;
					$v->all_pay_times = $order->count;
					if(in_array($game_id, Config::get('game_config.agent_games'))){
					    $v->first_lev = 0;
					} else {
					$v->first_lev = $this->getFirstPayLevel($v->player_id, $v->uid, $order->server_id, $v->server_id,$game_id);
				    }
				}
				$user->players = $players->toArray();
			}
			return Response::json($user);
		} else {
			return Response::json(array(), 404);
		}
	}

	public function getUserByPlayer()
	{
		$player_name = Input::get('player_name');
		$player_id = (int)Input::get('player_id');
		$tp_code = Input::get('tp_code');
		$server_internal_id = (int)Input::get('server_internal_id');
		//一个平台多个游戏用到
		$game_id = (int)Input::get('game_id');
		if ($player_name) {
			return $this->getUserByPlayerName($player_name, $server_internal_id, $game_id, $tp_code);
		} else if ($player_id) {
			return $this->getUserByPlayerID($player_id, $server_internal_id, $game_id, $tp_code);
		}
		return Response::json(array(), 404);
	}
		
	private function getUserByPlayerID($player_id, $server_internal_id, $game_id, $tp_code) 
	{
		if (in_array($game_id, Config::get('game_config.agent_games'))) {
			$user = SlaveCreatePlayer::on($this->db_qiqiwu)
			->getUser($game_id, $server_internal_id, $player_id, '', $tp_code)
			->get();
		} else {
			$user = CreatePlayerLog::on($this->db_name)
			->getUser($this->db_qiqiwu, $game_id, $server_internal_id, $player_id, '', $tp_code)
			->get();
			if(count($user) == 0){
				Log::info('合服后创建的玩家：' . var_export($player_id,true));
				$user = SlaveCreatePlayer::on($this->db_qiqiwu)
				->getUser($game_id, $server_internal_id, $player_id, '', $tp_code)
				->get();
			}
		}
		if ($user) {
			foreach ($user as $k => $v) {
			    if(!in_array($game_id, Config::get('game_config.agent_games'))){
			        $has_db = true;
			        $this->db_name = $this->game_id . '.' . $v->server_internal_id;
			        $this->setDB();
			        try {
			            DB::connection($this->db_name);
			        } catch (\Exception $e) {
			            $has_db = false;
			        }
			        if ($has_db == false) {
			            unset($user[$k]);
			            continue;
			        }
			    }
				$order = $this->getPlayerOrderStat($v->uid, $v->server_internal_id, $game_id);
				$v->all_pay_amount = $order->pay_amount;
				$v->all_pay_times = $order->count;
				if(in_array($game_id, Config::get('game_config.agent_games'))){
				    $v->first_lev = 0;
				} else {
				    $v->first_lev = $this->getFirstPayLevel($v->player_id, $v->uid, $order->server_id, $v->server_internal_id,$game_id);
				}
			}
			// Log::info(var_export($user,true));
			return Response::json($user);
		} else {
			return Response::json(array(), 404);
		}
	}

	private function getUserByPlayerName($player_name, $server_internal_id, $game_id, $tp_code) 
	{
		if (in_array($game_id, Config::get('game_config.agent_games'))) {
			$user = SlaveCreatePlayer::on($this->db_qiqiwu)
			->getUser($game_id, $server_internal_id, '', $player_name, $tp_code)
			->get();
		} else {
			$user = CreatePlayerLog::on($this->db_name)
			->getUser($this->db_qiqiwu, $game_id, $server_internal_id, '', $player_name, $tp_code)
			->get();
			if(count($user) == 0){
				$user = CreatePlayerLog::on($this->db_name)
				->getUser($this->db_qiqiwu, $game_id, $server_internal_id, '', $player_name, $tp_code, 1)
				->get();
			}
			if(count($user) == 0){
				Log::info('合服后创建的玩家：' . var_export($player_name,true));
				$user = SlaveCreatePlayer::on($this->db_qiqiwu)
				->getUser($game_id, $server_internal_id, '', $player_name, $tp_code)
				->get();
			}
		}
		if ($user) {
			foreach ($user as $k => $v) {

			    if(!in_array($game_id, Config::get('game_config.agent_games'))){
			        $has_db = true;
			        $this->db_name = $this->game_id . '.' . $v->server_internal_id;
			        $this->setDB();
			        try {
			            DB::connection($this->db_name);
			        } catch (\Exception $e) {
			            $has_db = false;
			        }
			        if ($has_db == false) {
			            unset($user[$k]);
			            continue;
			        }
			    }
				$order = $this->getPlayerOrderStat($v->uid, $v->server_internal_id, $game_id);
				$v->all_pay_amount = $order->pay_amount;
				$v->all_pay_times = $order->count;
				if(in_array($game_id, Config::get('game_config.agent_games'))){
				    $v->first_lev = 0;
				} else {
				    $v->first_lev = $this->getFirstPayLevel($v->player_id, $v->uid, $order->server_id, $v->server_internal_id,$game_id);
                    Log::info('get first pay level('.$order->server_id. ', ' . $v->server_internal_id.')');
				}
			}
			return Response::json($user);
		} else {
			return Response::json(array(), 404);
		}
	}

	private function getPlayerOrderStat($uid, $server_internal_id, $game_id)
	{
		if ($game_id == 11) {
			$order = PayOrder::on($this->db_payment)
			->leftJoin("{$this->db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
			->where('sl.server_internal_id', $server_internal_id)
			->where('o.get_payment', 1)
			->where('o.pay_user_id', $uid)
			->selectRaw('SUM(o.pay_amount * o.exchange) as pay_amount, count(o.order_id) as count, sl.server_id')
			->first();
		}else{
			$order = PayOrder::on($this->db_payment)
			->leftJoin("{$this->db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
			->where('sl.server_internal_id', $server_internal_id)
			->where('o.get_payment', 1)
			->where('o.pay_user_id', $uid)
			->where('o.game_id', $game_id)
			->selectRaw('SUM(o.pay_amount * o.exchange) as pay_amount, count(o.order_id) as count, sl.server_id')
			->first();
		}
		return $order;
	}

	private function getFirstPayLevel($player_id, $uid, $server_id, $server_internal_id,$game_id)
	{
		$order = PayOrder::on($this->db_payment)
			->where('pay_user_id', $uid)
			->where('server_id', $server_id)
			->orderBy('pay_time', 'ASC')
			->first();
		if (!$order) {
			return 0;
		}

		$this->db_name = $this->game_id . '.' . $server_internal_id;
		$this->setDB();
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$lev = LevelUpLog::on($this->db_name)
			->where('player_id', $player_id)
			->where('created_at', '<=', $order->pay_time)
			->orderBy('created_at', 'DESC')
			->first();
		}else{
			$lev = LevelUpLog::on($this->db_name)
			->where('player_id', $player_id)
			->where('levelup_time', '<=', $order->pay_time)
			->orderBy('levelup_time', 'DESC')
			->first();
		}
		if ($lev) {
			return $lev->new_level;
		} 
		return 1;
	}

	public function getUserDevice()
	{
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$game_id = Input::get('game_id');
		$interval = (int)Input::get('interval');
		$check_type = Input::get('check_type');
        $serach_type = (int)Input::get('serach_type');
        $channel_type = Input::get('channel_type');
        $source = Input::get('source');
		//$game_id = $this->game_id;
		//$server_internal_id = (int)Input::get('server_internal_id');

		$result = SlaveUserDevice::on($this->db_qiqiwu)
			->getUserDevice($start_time, $end_time, $interval, $check_type, $game_id, $serach_type, $channel_type, $source)
			->get();
		if ($result) {
			foreach ($result as &$value) {
				
				$value->ctime = date("Y-m-d H:i:s", $value->ctime);
			}
			return Response::json($result);
		} else {
			return Response::json(array(), 404);
		}
	}

	public function getStat()
	{
		$filter_array = array(
			'source', 'u1', 'u2'
		);

		$interval_array = array(
			600, 3600, 86400
		);

		$msg = array(
			'code' => Config::get('errorcode.slave_user'),
			'error' => ''
		);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		
		$interval = (int)Input::get('interval');

		if ($interval > 0 && !in_array($interval, $interval_array)) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}

		$filter = Input::get('filter');

		if (!in_array($filter, $filter_array)) {
			$msg['error'] = Lang::get('error.slave_user_stat_filter');
			return Response::json($msg, 403);
		}

		$source = Input::get('source');
		$u1 = Input::get('u1');
		$u2 = Input::get('u2');
		$game_id = $this->game_id;
		$server_internal_id = (int)Input::get('server_internal_id');
		$classify = (int)Input::get('classify');
//        Log::info('Post data:internal_id:'.$server_internal_id.' game_id:'.$game_id.' u1:'.$u1.' u2:'.$u2.' source:'.$source.' filter:'. $filter.' interval'.$interval.' start_time:'.$start_time.' end_time'.$end_time);
		/*$result = SlaveUser::on($this->db_qiqiwu)
			->userStat($server_internal_id, $this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2)
			->get();*/
		if ($game_id == 11) {  //德州扑克
			$result = SlaveUser::on($this->db_qiqiwu)
			->userPokerStat($server_internal_id, $this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id)
			->get();
		}elseif($game_id == 8){//繁体女神
			$result = SlaveUser::on($this->db_qiqiwu)
			->userStatTW($server_internal_id, $this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id, $classify)
			->get();
		}elseif($game_id == 44 || $game_id == 53 || $game_id == 50 || $game_id == 38 || $game_id == 51 || $game_id == 64){
			//土耳其女神 土耳其火影 印尼大乱斗 印尼神仙道 印尼君王2
			$result = SlaveUser::on($this->db_qiqiwu)
			->userStatTR($server_internal_id, $this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id, $classify)
			->get();
		}elseif(in_array($game_id, array(1, 2, 3, 4, 30, 59, 60, 61, 62, 63, 66)) || in_array($game_id, Config::get('game_config.yysggameids'))){
            //三国普通服和世界服都是在同一平台，所以Users表中有game_source字段表明用户来源，需要按游戏分别统计。
            $result = SlaveUser::on($this->db_qiqiwu)
                ->userStat($server_internal_id, $this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id, $classify)
                ->get();
        }else{
            //单平台单游戏没有game_source字段，不需要按游戏分别统计
			$result = SlaveUser::on($this->db_qiqiwu)
			->userStatSingleGame($server_internal_id, $this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id, $classify)
			->get();
		}

		if ($result) {
			return Response::json($result);
		} else {
			return Response::json(array(), 404);
		}
	}

    public function getAdStat()
    {
        $start_time = (int)Input::get('start_time');
        $end_time = (int)Input::get('end_time');
        $interval = (int)Input::get('interval');
        $game_id = $this->game_id;

        if ($game_id == 11) {  //德州扑克
            $result = SlaveUser::on($this->db_qiqiwu)
                ->userPokerStat($this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id)
                ->get();
        }elseif($game_id == 8){//繁体女神
            $result = SlaveUser::on($this->db_qiqiwu)
                ->userStatTW($this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id)
                ->get();
        }elseif($game_id == 44 || $game_id == 53 || $game_id == 50 || $game_id == 38 || $game_id == 51){
            //土耳其女神 土耳其火影 印尼大乱斗 印尼神仙道 印尼君王2
            $result = SlaveUser::on($this->db_qiqiwu)
                ->userStatTR($this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id)
                ->get();
        }elseif(in_array($game_id, array(1, 2, 3, 4, 30, 59, 60, 61, 62, 63))){
            //三国普通服和世界服都是在同一平台，所以Users表中有game_source字段表明用户来源，需要按游戏分别统计。
            $result = SlaveUser::on($this->db_qiqiwu)
                ->userStat($this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id)
                ->get();
        }else{
            //单平台单游戏没有game_source字段，不需要按游戏分别统计
            $result = SlaveUser::on($this->db_qiqiwu)
                ->userStatSingleGame($this->db_name, $start_time, $end_time, $interval, $filter,$source, $u1, $u2, $game_id)
                ->get();
        }

        if ($result) {
            return Response::json($result);
        } else {
            return Response::json(array(), 404);
        }
    }

	public function SXDGetStat()
	{
	    $filter_array = array(
	            'source', 'u1', 'u2'
	    );
	
	    $interval_array = array(
	            600, 3600, 86400
	    );
	
	    $msg = array(
	            'code' => Config::get('errorcode.slave_user'),
	            'error' => ''
	    );
	
	    $start_time = (int)Input::get('start_time');
	    $end_time = (int)Input::get('end_time');
	
	    if ($start_time >= $end_time) {
	        $msg['error'] = Lang::get('error.time_interval');
	        return Response::json($msg, 403);
	    }
	
	    $interval = (int)Input::get('interval');
	
	    if ($interval > 0 && !in_array($interval, $interval_array)) {
	        $msg['error'] = Lang::get('error.time_interval');
	        return Response::json($msg, 403);
	    }
	
	    $filter = Input::get('filter');
	
	    if (!in_array($filter, $filter_array)) {
	        $msg['error'] = Lang::get('error.slave_user_stat_filter');
	        return Response::json($msg, 403);
	    }
	
	    $source = Input::get('source');
	    $u1 = Input::get('u1');
	    $u2 = Input::get('u2');
	
	    $platform_server_id = (int)Input::get('platform_server_id');
	    $result = SlaveUser::on($this->db_qiqiwu)
	    ->sXDUserStat($platform_server_id, $start_time, $end_time, $interval, $filter, $source, $u1, $u2)
	    ->get();

	    if ($result) {
	        return Response::json($result);
	    } else {
	        return Response::json(array(), 404);
	    }
	}
	public function getCreatePlayerStat()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_create_player_stat'),
			'error' => '',
		);
		$interval_array = array(
			600,
			3600,
			86400,
		);
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$game_id = (int)Input::get('game_id');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}

		$interval = (int)Input::get('interval');
		if ($interval > 0 && !in_array($interval, $interval_array)) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}	
		$server_internal_id = (int)Input::get('server_internal_id');
		$test = SlaveCreatePlayer::on($this->db_qiqiwu)->first();

		if('0' == $server_internal_id){
			if(isset($test->game_id)){
				$players = SlaveCreatePlayer::on($this->db_qiqiwu)
					->createPlayerStatAllServers($start_time, $end_time, $server_internal_id, $interval, $game_id)
					->where('p.game_id', $game_id)
					->get();
			}else{
				$players = SlaveCreatePlayer::on($this->db_qiqiwu)
					->createPlayerStatAllServers($start_time, $end_time, $server_internal_id, $interval, $game_id)
					->get();
			}
		}else{
			if (!$server_internal_id) {
				$msg['error'] = Lang::get('error.slave_server_internal_id');
				return Response::json($msg, 403);
			}
			if(in_array($game_id, Config::get('game_config.yysggameids'))){
				$players = CreatePlayerLog::on($this->db_name)
					->createPlayerStatYYSG($start_time, $end_time, $server_internal_id, $interval, $this->db_qiqiwu)
					->get();
			}else{
				if(isset($test->game_id)){
					$players = SlaveCreatePlayer::on($this->db_qiqiwu)
						->createPlayerStat($start_time, $end_time, $server_internal_id, $interval)
						->where('p.game_id', $game_id)
						->get();
				}else{
					$players = SlaveCreatePlayer::on($this->db_qiqiwu)
						->createPlayerStat($start_time, $end_time, $server_internal_id, $interval)
						->get();
				}
			}
		}
		if ($players) {
			return Response::json($players);
		} else {
			return Response::json(array(), 404);
		}	
	}

	/*
	 * 根据注册用户的渠道进行统计留存率
	 */
	public function getChannelRetentionStat()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_channel_stat'),
			'error' => '',
		);
		$game_id = Input::get('game_id');
		$server_internal_id = Input::get('server_internal_id');
		$platform_id = Input::get('platform_id');
		$filter = Input::get('filter');
		$filter_array = array(
			'', 'source', 'u1', 'u2'
		);
			
		if (!in_array($filter, $filter_array)) {
			$msg['error'] = Lang::get('error.slave_user_stat_filter');
			return Response::json($msg, 403);

		}
		$source = Input::get('source');
		$u1 = Input::get('u1');
		$u2 = Input::get('u2');
		$os_type = Input::get('os_type');

		$reg_start_time = (int)Input::get('reg_start_time');
		$reg_end_time = (int)Input::get('reg_end_time');

		if ($reg_start_time >= $reg_end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		$is_anonymous = Input::get('is_anonymous');	
		$result = UserRetention::on($this->db_qiqiwu_retention)
			->channelRetention($server_internal_id, $os_type, $source, $u1, $u2, 
				$filter, $reg_start_time, $reg_end_time, $is_anonymous, $game_id, $platform_id)
				->get();

		if (count($result)) {
			return Response::json($result);	
		} else {
			return Response::json($msg, 404);
		}
	}

	private function retentionByTime($data, $is_anonymous, $game_id, $platform_id)
	{
		$retention = array(
			'days_2' => 0,
			'days_3' => 0,
			'days_4' => 0,
			'days_5' => 0,
			'days_6' => 0,
			'days_7' => 0,
			'days_14' => 0	
		);
				
		foreach ($data as $k => &$v) {
			$start_time = $v->ctime;
			$end_time = $v->ctime + 86399;
			$source = '';
			$u1 = '';
			$u2 = '';
			if (isset($v->source)) {
				$source = $v->source;	
			}
			if (isset($v->u1)) {
				$u1 = $v->u1;
			}
			if (isset($v->u2)) {
				$u2 = $v->u2;
			}
			$create_player = CreatePlayerLog::on($this->db_name)
				->retentionPlayers($this->db_qiqiwu, $start_time, $end_time, $is_anonymous, $source, $u1, $u2, $game_id, $platform_id)
				->get();
            //Log::info('====================================get retention players');


            $create_player_ids = array();
			foreach( $create_player as $vv) {
				if ($vv->player_id) {
					$create_player_ids[] = $vv->player_id;
				}
			}

			$r = $this->retentionByDay($create_player_ids, $v->ctime);
			$retention['days_2'] += $r['days_2'];
			$retention['days_3'] += $r['days_3'];
			$retention['days_4'] += $r['days_4'];
			$retention['days_5'] += $r['days_5'];
			$retention['days_6'] += $r['days_6'];
			$retention['days_7'] += $r['days_7'];
			$retention['days_14'] += $r['days_14'];
		}
		unset($v);
		return $retention;
	}

	private function retentionByDay($create_player_ids, $retention_time)
	{
		$result = array();
		foreach ($this->days as $v) {
			$result['days_' . $v] = $this->dayDetail($v, $retention_time, $create_player_ids);	
		}
		return $result;
	}

	private function dayDetail($day, $retention_time, $create_player_ids)
	{
		if (empty($create_player_ids)) {
			return 0;
		}

		if(($retention_time + 86400*$day -1) > time()){
			return 0;
		}
		
		$test = LoginLog::on($this->db_name)->first();
		if(isset($test->is_login)){
			$online = LoginLog::on($this->db_name)
			->loginOnline($retention_time, $day, $create_player_ids, 'is_login', 'login_time')->get();
		}elseif(isset($test->action)){
			$online = LoginLog::on($this->db_name)
			->loginOnline($retention_time, $day, $create_player_ids, 'action', 'action_time')->get();
		}

        Log::info('====================================get login online');



        $player_ids = array();

		foreach ($online as $v) {
			$player_ids[] = $v->player_id;
		}
		$online_number = count($player_ids);

		$create_player_ids = array_diff($create_player_ids, $player_ids);
		if (empty($create_player_ids)) {
			return $online_number;
		}
		$login_count = LoginLog::on($this->db_name)
			->loginCount($retention_time, $day, $create_player_ids, $this->game_id)
			->first();

        Log::info('====================================get login count');

        $total = 0;
		if ($login_count->count > 0) {
			$total = $login_count->count + $online_number;
		}
		return $total;
	}

	/*
	 * 根据注册用户的渠道获得充值数据
	 */
	public function getChannelOrderStat()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_channel_stat'),
			'error' => '',
		);
		$source = Input::get('source');
		$u1 = Input::get('u1');
		$u2 = Input::get('u2');
		$game_id = Input::get('game_id');
		$platform_id = Input::get('platform_id');
		$reg_start_time = (int)Input::get('reg_start_time');
		$reg_end_time = (int)Input::get('reg_end_time');
		$game_type = Input::get('game_type');
		if ($reg_start_time >= $reg_end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		$order_start_time = (int)Input::get('order_start_time');
		$order_end_time = (int)Input::get('order_end_time');
		if ($order_start_time >= $order_end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		$platform_server_id = (int)Input::get('platform_server_id');
		$currency_id = (int)Input::get('currency_id');

		$filter = Input::get('filter');
		$filter_array = array(
			'', 'source', 'u1', 'u2'
		);
			
		if (!in_array($filter, $filter_array)) {
			$msg['error'] = Lang::get('error.slave_user_stat_filter');
			return Response::json($msg, 403);
		}
		$is_anonymous = Input::get('is_anonymous');

		$stat = SlaveUser::on($this->db_qiqiwu)
			->channelOrder($this->db_payment, $source, $u1, $u2, 
				$reg_start_time, $reg_end_time,
				$order_start_time, $order_end_time,
				$filter, $currency_id, $is_anonymous, $game_id, $platform_id, $game_type
			)->get();

		$total = SlaveUser::on($this->db_qiqiwu)
			->channelOrderTotal($this->db_payment, $source, $u1, $u2, 
				$reg_start_time, $reg_end_time,
				$order_start_time, $order_end_time,
				$filter, $currency_id, $is_anonymous, $game_id, $platform_id
			)->first();

		if ($stat) {
			$arr = $stat->toArray();
            Log::info('stat to array:' . date('H:i:s'));
            if(2 == $game_type){
            	$total->os_type = 'Total';
            	$total->source = '-';
            }else{
            	$total->os_type = '-';
            	$total->source = 'Total';
            }
			$total->u1 = '-';
			$total->u2 = '-';
			array_unshift($arr, $total->toArray());
     //       Log::info('array unshift:' . date('H:i:s'));
            return Response::json($arr);
		} else {
			$msg['error'] = Lang::get('error.slave_channel_order_stat_not_found');
			return Response::json($msg, 404);
		}
	}

	/*
	 * 根据开服日期和注册日期统计注册创建信息
	 */
	public function getStatOverServers() {
		$game_id = Input::get('game_id');
		$reg_start_time = Input::get('reg_start_time');
		$reg_end_time = Input::get('reg_end_time');
		$server_start_time = Input::get('server_start_time');
		$server_end_time = Input::get('server_end_time');
		$game_type = Input::get('game_type');
		$filter_u1 = Input::get('filter_u1');
        Log::info('==========================================================start');

	    $db = DB::connection($this->db_qiqiwu);

	    if(in_array($game_id, array(2,36))){	//单独处理下越南--Panda
	    	if('2' == $game_id){
	    		$sql = "select ".($filter_u1 ? "u.u as u1," : "")." u.source, COUNT(DISTINCT(f.uid)) as count_formal, COUNT(cp.create_player_id) as count_player 
	    			from (select u,source,uid from `users` where game_source = 2 and created_time between '".date("Y-m-d H:i:s", $reg_start_time)."' and '".date("Y-m-d H:i:s", $reg_end_time)."') as `u`
	    			left join `users` as `f` on `f`.`uid` = `u`.`uid` and `f`.`is_anonymous` = 0 
	    			left join `create_player` as `cp` on `cp`.`uid` = `u`.`uid` and cp.game_id = 2
	    			and `cp`.`created_time` between $reg_start_time and $reg_end_time group by u.source".($filter_u1 ? ",u.u" : "");
	    	}
	    	if('36' == $game_id){
	    		$sql = "select ".($filter_u1 ? "u.u as u1," : "")." u.source, COUNT(DISTINCT(f.uid)) as count_formal, COUNT(cp.create_player_id) as count_player 
	    			from (select u,source,uid from `users` where created_time between '".date("Y-m-d H:i:s", $reg_start_time)."' and '".date("Y-m-d H:i:s", $reg_end_time)."') as `u`
	    			left join `users` as `f` on `f`.`uid` = `u`.`uid` and `f`.`is_anonymous` = 0 
	    			left join `create_player` as `cp` on `cp`.`uid` = `u`.`uid` 
	    			and `cp`.`created_time` between $reg_start_time and $reg_end_time group by u.source".($filter_u1 ? ",u.u" : "");
	    	}
	    	$result = $db->select($sql);
	    }else{
	    	$user = $db->select("select * from users where (1) limit 1");
		    $user = $user[0];
		    $users_type = 0;  //这个字段用来表明数据库表users中是否存在game_source,game_id
		    if(isset($user->game_source)){
		    	$users_type = 1;
		    }elseif(isset($user->game_id)){
		    	$users_type = 2;
		    }

		    $player = $db->select("select * from create_player where (1) limit 1");
		    $player = $player[0];
		    $player_type = 0;  //这个字段用来表明数据库表create_player中是否存在game_id
		    if(isset($player->game_id)){
		    	$player_type = 1;
		    }
			$result = SlaveUser::on($this->db_qiqiwu)
				->weeklyStat($server_start_time, $server_end_time, $reg_start_time, $reg_end_time, $game_id, $users_type, $player_type, $game_type, $filter_u1)
				->get();
		}
        //
      //  Log::info('==========================================================end:'.var_export($result, true));
        return Response::json($result);
	}
	/*
	 * 更新内玩表
	*/
	public function neiwan() {
		$uid = Input::get('neiwan_uid');
		$user = Input::get('user');
		$game_id = Input::get('game_id');
		$is_delete = Input::get('is_delete');
		$table = Table::init(public_path() . '/table/neiwan.txt');
		$message_arr = array('game_id' => $game_id, 'neiwan_uid' => $uid);
		if($is_delete){
			$res = $table->deleteNeiWan($message_arr);
		}else{
			$res = $table->addData($message_arr);
		}
		return Response::json($res);
	}
	
	public function getPokerUserActivate()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$type = Input::get('type');
		$game_id = Input::get('game_id');
		$server_interval_id = Input::get('server_interval_id');
		$result = SlaveCreatePlayer::on($this->db_name)->select('*')->count();
		if (!$result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}

	public function getPokerRetention()
	{
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$db = DB::connection($this->db_qiqiwu);
		$activate_users = $db->select("select count(uid) as nums FROM_UNIXTIME(created_time, '%Y-%m-%d') as date from create_player where `created_time` > {$start_time} and `created_time` < {$end_time} and `activate_time` >0  ");  
		if ($activate_users) {
			return Response::json($activate_users);
		}else {
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getIdByName()
	{
		$player_name = Input::get('player_name');
		$player_id = (int) Input::get('player_id');
		$server_internal_id = Input::get('server_internal_id');
		$game_id = Input::get('game_id');
		$db = DB::connection($this->db_name);
		if ($player_name != '0') {
			$user = $db->select("select player_id, user_id, player_name from log_create_player where binary player_name = '{$player_name}' ");
			/*Log::info(var_export("player_name test",true));
			Log::info(var_export($user,true));*/
		} elseif ($player_id) {
			$user = $db->select("select player_id, user_id, player_name from log_create_player where  player_id = {$player_id} ");
			/*Log::info(var_export("player_id test",true));
			Log::info(var_export($user,true));*/
		}
		
		if ($user) {
			return Response::json($user);
		}else {	//合服后可能通过昵称或者id查询会导致查询不到结果，会尝试到官网的create_player中查询，此时官网恰好漏掉数据的概率很小
			$db_qiqiwu = DB::connection($this->db_qiqiwu);
			if ($player_name != '0') {
				$user = $db_qiqiwu->select("select player_id, uid as user_id, player_name from create_player where  player_name = '{$player_name}' ");
			} elseif ($player_id) {
				$user = $db_qiqiwu->select("select player_id, uid as user_id, player_name from create_player where  player_id = {$player_id} ");
			}
			if ($user) {
				return Response::json($user);
			}else{
				return 0;
			}
		}

	}
	public function getUserInfo()
	{
		$player_id = Input::get('player_id');
		$server_internal_id = Input::get('server_internal_id');
		$platform_id = Input::get('platform_id');
		$game_id = Input::get('game_id');
		$db = DB::connection($this->db_qiqiwu);
		$user = $db->select("select * from create_player where  player_id = {$player_id} ");
		if ($user) {
			return Response::json($user);
		} else {
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}

	public function getPlayerIdByUid()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$uid = Input::get('uid');
		$platform_id = Input::get('platform_id');
		$game_id = (int)Input::get('game_id');
		if(in_array($game_id, Config::get('game_config.yysggameids'))){
			$sql = "select player_id from log_create_player where uid= '{$uid}'";
			$info = DB::connection($this->db_name)->select($sql);
		}else{
			$sql = "select cp.player_id from create_player cp left join users u on cp.uid = u.uid where cp.uid = '{$uid}'";
			$info = DB::connection($this->db_qiqiwu)->select($sql);
		}
		
		if (isset($info)) {
			return Response::json($info);
		} else{
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}
	public function getPlayerGames()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$player_id = Input::get('player_id');
		$platform_id = Input::get('platform_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time'); 
		$sql = "select count(log_id) as game_num , FROM_UNIXTIME(time, '%Y-%m-%d') as date, rule_id as game_name from log_game  WHERE players LIKE '{$player_id}'
                OR players LIKE '%{$player_id}' OR players LIKE '%{$player_id}%' and time >= '{$start_time}'  and time <= '{$end_time}'  group by game_name having rule_id in (7001,7007,7015,7010,7012,7029,7014,7021,7018,7027,7028,8001,8002) order by date";
		$info = DB::connection($this->db_name)->select($sql);
		if (isset($info)) {
			return Response::json($info);
		} else{
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}

	public function getPokerGames()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$str = Input::get('str');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		if (isset($str)) {
			$str = " and rule_id in ({$str})";
		} else {
			$str = '';
		}
		$sql = "select players, FROM_UNIXTIME(time, '%Y-%m-%d') as date from log_game where time >= {$start_time} and time <= {$end_time} ".$str;
		$info = DB::connection($this->db_name)->select($sql);
		if (isset($info)) {
			return Response::json($info);
		} else{
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}
	public function getPokerUsers()
    {
        $msg = array(
                'code' => Config::get('errorcode.slave_player_created'),
                'error' => ''
        );
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $sql = "select count(distinct player_id ) as log_num ,FROM_UNIXTIME(login_time, '%Y-%m-%d') as date from log_login  where login_time  > {$start_time} and login_time < {$end_time}  ";
        $info = DB::connection($this->db_name)->select($sql);
        if (isset($info)) {
                return Response::json($info);
        } else{
                $msg['error'] = Lang::get('slave.slave_result_none');
                return Response::json($msg, 404);
        }
    }

    public function getAllUsers()
    {
        $msg = array(
                'code' => Config::get('errorcode.slave_player_created'),
                'error' => ''
        );
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $sql = "select count(distinct player_id ) as user_num ,FROM_UNIXTIME(created_time, '%Y-%m-%d') as date from create_player  where created_time < {$start_time} and activate_time > 0 ";
        $info = DB::connection($this->db_qiqiwu)->select($sql);
        if (isset($info)) {
                return Response::json($info);
        } else{
                $msg['error'] = Lang::get('slave.slave_result_none');
                return Response::json($msg, 404);
        }
    }

	public function getPokerInfo()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$uid = Input::get('uid');
		$sql = "select cp.player_name, tp.tp_user_id, u.created_time from create_player cp left join third_party tp on tp.uid = cp.uid left join users u on u.uid = cp.uid where cp.uid = '{$uid}' and tp.tp_code = 'fb' ";
		$info = DB::connection($this->db_qiqiwu)->select($sql);
		if (count($info)) {
			return Response::json($info);
		} else{
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}

	public function getPokerPayNum()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$uid = Input::get('uid');
		$sql = "select count(order_id) as pay_num from pay_order where pay_user_id = '{$uid}' and get_payment = 1";
		$info = DB::connection($this->db_payment)->select($sql);
		if (count($info)) {
			return Response::json($info);
		} else{
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}

	//黑暗之光· -- 第三方游戏
	public function getUserTH()
	{
		$game_id = (int)Input::get('game_id');
		$uid = Input::get('uid');
		$email = Input::get('email');
		
		$server_internal_id = (int)Input::get('server_internal_id');
		if ($uid) {
			return $this->getUserByUIDTH($uid, $server_internal_id, $game_id);
		} else if ($email) {
			return $this->getUserByEmailTH($email, $server_internal_id, $game_id);
		} 
		return Response::json(array(), 404);	
	}

	private function getUserByUIDTH($uid, $server_internal_id, $game_id)
	{
		$user = SlaveUser::on($this->db_qiqiwu)
			->where('uid', $uid)
			->where('game_id', $game_id);
		$user = $user->first();
		if ($user) {
			
			if ($game_id == 46) {
				$create_player = 'create_player_hg';
			}
			if (isset($server_internal_id)) {
				$players = DB::connection($this->db_qiqiwu)->select("select * from {$create_player} where uid = {$uid} and server_id = {$server_internal_id}");
			} else {
				$players = DB::connection($this->db_qiqiwu)->select("select * from {$create_player} where uid = {$uid}");
			}

			$user->players = array();
			if ($players) {
				foreach ($players as $v) {
					if ($this->game_id == 0) {
						break;
					}
					$order = $this->getPlayerOrderStat($v->uid, $v->server_id, $game_id);
					$v->all_pay_amount = isset($order->pay_amount) ? $order->pay_amount : 0;
					$v->all_pay_times = isset($order->count) ? $order->count : 0;
					$v->first_lev = 0;
					
				}
				$user->players = (array)$players;
			}
			return Response::json($user);
		} else {
			return Response::json(array(), 404);
		}
	}

	private function getUserByEmailTH($email, $server_internal_id, $game_id)
	{
		$user = SlaveUser::on($this->db_qiqiwu)
			->where('login_email', $email)
			->orWhere('nickname', $email)
			->where('game_id', $game_id);
		$user = $user->first();
		if ($user) {
			if ($game_id == 46) {
				$create_player = 'create_player_hg';
			}
			if (isset($server_internal_id)) {
				$players = DB::connection($this->db_qiqiwu)->select("select * from '{$create_player}' where uid = {$uid} and server_id = {$server_internal_id}");
			} else {
				$players = DB::connection($this->db_qiqiwu)->select("select * from '{$create_player}' where uid = {$uid}");
			}

			$user->players = array();
			if ($players) {
				foreach ($players as $v) {
					if ($game_id == 0) {
						break;
					}
					$order = $this->getPlayerOrderStat($v->uid, $v->server_id, $game_id);
					$v->all_pay_amount = isset($order->pay_amount) ? $order->pay_amount : 0;
					$v->all_pay_times = isset($order->count) ? $order->count : 0;
					
					$v->first_lev = 0;
					
				}
				$user->players = (array)$players;
			}
			return Response::json($user);
		} else {
			return Response::json(array(), 404);
		}
	}

	public function getUserByPlayerTH()
	{
		$player_name = Input::get('player_name');
		$player_id = (int)Input::get('player_id');
		$tp_code = Input::get('tp_code');
		$server_internal_id = (int)Input::get('server_internal_id');
		//一个平台多个游戏用到
		$game_id = (int)Input::get('game_id');

		if ($player_name) {
			return $this->getUserByPlayerNameTH($player_name, $server_internal_id, $game_id);
		} else if ($player_id) {
			return $this->getUserByPlayerIDTH($player_id, $server_internal_id, $game_id);
		}
		return Response::json(array(), 404);
	}
		
	private function getUserByPlayerIDTH($player_id, $server_internal_id, $game_id) 
	{
		/*if ($game_id == 38) {
			$user = SlaveCreatePlayer::on($this->db_qiqiwu)
			->getUser($game_id, $server_internal_id, $player_id, '', $tp_code)
			->get();
		}*/
		if ($game_id == 46) {
			$create_player = "create_player_hg";
		}

		$db = DB::connection($this->db_qiqiwu);
		$str = "";
		if ($$player_id) {
			$str .= " and  p.player_id = {$player_id}";
		}
		
		if ($game_id) {
			$str .= " and  u.game_id = {$game_id}";
		}
		$sql = "select u.uid, u.name,u.contact_email,u.created_time,u.last_visit_ip,u.last_visit_time,u.created_time,u.created_ip, p.player_id,  p.server_id as server_internal_id, u.nickname, u.login_email, p.player_name, u.u, u.u2, u.source, u.is_anonymous from users u left join '{$create_player}' p on u.uid = p.uid where p.server_id = {$server_internal_id}" . $str;
		
		$user = $db->select($sql);
		if ($user) {
			foreach ($user as $k => $v) {
				$order = $this->getPlayerOrderStat($v->uid, $v->server_internal_id, $game_id);
				$v->all_pay_amount = isset($order->pay_amount) ? $order->pay_amount : 0 ;
				$v->all_pay_times = isset($order->count) ? $order->count : 0 ;
				
				$v->first_lev = 0; //首充等级
				
			}
			return Response::json($user);
		} else {
			return Response::json(array(), 404);
		}
	}

	private function getUserByPlayerNameTH($player_name, $server_internal_id, $game_id) 
	{
		if ($game_id == 46) {
			$create_player = "create_player_hg";
		}

		$db = DB::connection($this->db_qiqiwu);
		$str = "";
		if ($$player_id) {
			$str .= " and p.player_id = {$player_id}";
		}
		if ($game_id) {
			$str .= " and  u.game_id = {$game_id}";
		}
		$sql = "select u.uid, u.name,u.contact_email,u.created_time,u.last_visit_ip,u.last_visit_time,u.created_time,u.created_ip, p.player_id,  p.server_id as server_internal_id, u.nickname, u.login_email, p.player_name, u.u, u.u2, u.source, u.is_anonymous from users u left join '{$create_player}' p on u.uid = p.uid where p.server_id = {$server_internal_id}" . $str;
		
		$user = $db->select($sql);
		
	
		if ($user) {
			foreach ($user as $k => $v) {

				$order = $this->getPlayerOrderStat($v->uid, $v->server_internal_id, $game_id);
				$v->all_pay_amount = isset($order->pay_amount) ? $order->pay_amount : 0 ;
				$v->all_pay_times = isset($order->count) ? $order->count : 0 ;
				$v->first_lev = 0;
				
			}
			return Response::json($user);
		} else {
			return Response::json(array(), 404);
		}
	}

	public function THGetStat()
	{
	    $filter_array = array(
            'source', 'u1', 'u2'
        );

        $interval_array = array(
            600, 3600, 86400
        );

        $msg = array(
            'code' => Config::get('errorcode.slave_user'),
            'error' => ''
        );

        $start_time = (int)Input::get('start_time');
        $end_time = (int)Input::get('end_time');

        if ($start_time >= $end_time) {
            $msg['error'] = Lang::get('error.time_interval');
            return Response::json($msg, 403);
        }

        $interval = (int)Input::get('interval');

        if ($interval > 0 && !in_array($interval, $interval_array)) {
            $msg['error'] = Lang::get('error.time_interval');
            return Response::json($msg, 403);
        }

        $filter = Input::get('filter');

        if (!in_array($filter, $filter_array)) {
            $msg['error'] = Lang::get('error.slave_user_stat_filter');
            return Response::json($msg, 403);
        }

        $source = Input::get('source');
        $u1 = Input::get('u1');
        $u2 = Input::get('u2');

        $platform_server_id = (int)Input::get('platform_server_id');
        $game_id = Input::get('game_id');
        $result = SlaveUser::on($this->db_qiqiwu)
        ->tHUserStat($platform_server_id, $start_time, $end_time, $interval, $filter, $source, $u1, $u2, $game_id)
        ->get();
        //Log::info(var_export($game_id, true));
       // Log::info(var_export($result, true));
        if ($result) {
            return Response::json($result);
        } else {
            return Response::json(array(), 404);
        }

	}
	
	 public function getServerUnion()
    {
        $game_id = Input::get('game_id');
	    $ser = $this->getUnionGame();
	    $len = count($ser);
	    for ($i=0; $i < $len; $i++) {
	        $game_arr[$i] =  $ser[$i]->gameid;
	    }
	    $ga = array_unique($game_arr);
	    $se = "";
	    if (in_array($game_id, $ga)) {
	        for ($i=0; $i < $len; $i++) {
	            if ($ser[$i]->gameid == $game_id) { //判断是联运
	                $se .= $ser[$i]->serverid2 . ' , ';
	            }
	    }
        $se_arr = explode(',' , $se);
        unset($se_arr[count($se_arr)-1]);
        $server = Server::whereNotIn('server_internal_id', $se_arr)->get();
        $servers = array();
        for ($i=0; $i < count($server); $i++) {
            if ($server[$i]->game_id != $game_id) {
               unset($server[$i]);
            }
        }
    } else {
        $server = Server::currentGameServers()->get();
    }
        return Response::json($server);

    }

    public function getUnionGame()
    {
        $server = $this->initTableServer();
        $server = $server->getData();
        $server = (array)$server;
        return  $server;
    }


    private function initTableServer()
    {
        $table = Table::init(public_path() . '/table/' . 'flsg'. '/server.txt');
        return $table;
    }

    public function getCreatePlayer()
    {
    	$game_id = Input::get('game_id');
    	$server_internal_id = Input::get('server_internal_id');
    	$player_name = Input::get('player_name');
    	$player_id = Input::get('player_id');
    	$uid = Input::get('uid');
    	if ($uid) {
    		$user = SlaveCreatePlayer::on($this->db_qiqiwu)->where('uid', $uid)->where('server_id', $server_internal_id)->get();
    	}
    	if ($player_id) {
    		$user = SlaveCreatePlayer::on($this->db_qiqiwu)->where('player_id', $player_id)->where('server_id', $server_internal_id)->get();
    	}
    	if ($player_name) {
    		$user = SlaveCreatePlayer::on($this->db_qiqiwu)->whereRaw("binary player_name = '$player_name'")->where('server_id', $server_internal_id)->get();
    	}
    	if (isset($user)) {
    		return $user;
    	}
    }

    public function getCreatePlayerById(){
    	$game_id = Input::get('game_id');
    	$player_id = Input::get('player_id');

    	$db = DB::connection($this->db_qiqiwu);
    	if($db->select('desc create_player game_id')){
    		$result = $db->table('create_player')->where('game_id', $game_id)->where('player_id', $player_id)->first();
    	}else{
    		$result = $db->table('create_player')->where('player_id', $player_id)->first();
    	}

    	if($result){
    		return Response::json($result);
    	}else{
    		if(in_array($game_id, Config::get('game_config.yysggameids'))){
    			$db = DB::connection($this->db_name);
    			$result = $db->table('log_player_name')->where('player_id', $player_id)->orderBy('id', 'desc')->first();
    			if($result){
    				return Response::json($result);
    			}else{
    				return Response::json(array(), 404);
    			}
    		}
    		return Response::json(array(), 404);
    	}
    }

    public function getDragonLog()
    {
        $msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );

        $player_id = Input::get('player_id');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $type = Input::get('type');
        $db = DB::connection($this->db_name);
        $info = $db->table('log_dragon')
        	->whereBetween('time',array($start_time,$end_time))
        	->where('player_id',$player_id);
        	if($type > 0 ){
        		$info->where('dragon_balls','like','%'.$type.'%');
        	}
        $info = $info->get();
        if (isset($info)) {
                return Response::json($info);
        } else{
                return Response::json($msg, 403);
        }
    }

    public function getPokerUserInfo()
    {
        $msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );
        $game_id = Input::get('game_id');
        $server_internal_id = Input::get('server_internal_id');
        $player_name = Input::get('player_name');
        $player_id = Input::get('player_id');
        $uid = Input::get('uid');
        if ($uid) {
                $user = SlaveCreatePlayer::on($this->db_qiqiwu)->where('uid', $uid)->get();
        }
        if ($player_id) {
                $user = SlaveCreatePlayer::on($this->db_qiqiwu)->where('player_id', $player_id)->get();
        }
        if ($player_name) {
                $user = SlaveCreatePlayer::on($this->db_qiqiwu)->whereRaw("binary player_name = '$player_name'")->get();
        }
        if (isset($user)) {
                return Response::json($user);
        } else{
                return Response::json($msg, 403);
        }
    }


    public function getPlayerUid()
    {
        $sql = Input::get('sql');
        $db = DB::connection($this->db_qiqiwu);
        $sqll = "select uid from create_player where player_id in ($sql)";
        $user = $db->select($sqll);
        return Response::json($user);
    }

   public function getPokerLogin()
    {
        $msg = array(
            'code' => Config::get('error.unknow'),
            'error' => Lang::get('error.basic_not_found')
        );
        $game_id = Input::get('game_id');
        $server_internal_id = Input::get('server_internal_id');
        $ss = Input::get('ss');
        $db = DB::connection($this->db_name);
        $sql = "select distinct player_id, login_time from log_login where is_login = -1  and  login_time < {$ss} ";
        $user = $db->select($sql);
       // Log::info(var_export($user,true));
        if (isset($user)) {
                return Response::json($user);
        }
    }

    public function getCreatePlayer_xs()
    {
    	$game_id = Input::get('game_id');
    	$server_internal_id = Input::get('server_internal_id');
    	$player_name = Input::get('player_name');
    	$player_id = Input::get('player_id');
    	$uid = Input::get('uid');
    	if ($uid) {
    		$user = SlaveCreatePlayer::on($this->db_qiqiwu)->where('uid', $uid)->where('server_id', $server_internal_id)->get();
    	}
    	if ($player_id) {
    		$user = SlaveCreatePlayer::on($this->db_qiqiwu)->where('player_id', $player_id)->where('server_id', $server_internal_id)->get();
    	}
    	if ($player_name) {
    		$user = SlaveCreatePlayer::on($this->db_qiqiwu)->whereRaw("binary player_name = '$player_name'")->where('server_id', $server_internal_id)->get();
    	}
    	if (isset($user)) {
    		return $user;
    	}
    }

    public function getIdByName2()
    {
    	$player_name = Input::get('player_name');
		$player_id = Input::get('player_id');
		$server_id = Input::get('server_internal_id');
		$game_id = Input::get('game_id');
		$db = DB::connection($this->db_name);
		if ($player_name) {
			$user = $db->select("select player_id, user_id, player_name from log_create_player where  player_name = '{$player_name}' and server_id = {$server_id}");
		} elseif ($player_id) {
			$user = $db->select("select player_id, user_id, player_name from log_create_player where  player_id = {$player_id} and server_id = {$server_id}");
		}
		if ($user) {
			return Response::json($user);
		}else {
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
    }

    //特定时间登陆且在一定时间内注册人数，从数据表连接获取
     public function getPokerBackStat()//回流
    {
        $platform_id = Input::get('platform_id');
        $game_id = Input::get('game_id');
        $server_internal_id = Input::get('server_internal_id');
        $start_time = Input::get('start_time');//注册时间
        $end_time   = Input::get('end_time');//注册时间
        $p_time     = Input::get('p_time');//登录时间开始
        $pd_time    = Input::get('pd_time');//距离登陆时间往前间隔 player_death_time
        $db_qiqiwu = DB::connection($this->db_qiqiwu);
        $db_dot =    DB::connection($this->db_name);

        	$p_time_tmp = $p_time + 1*24*3600;
        	$death_begin_time = $p_time - $pd_time;
        	$login_time_Arr = '';
       		$login_time_Arr = $db_dot->select("
       			select max(l1.login_time) from 
       				log_login l1 
       				left join 
       				log_login l2
       				on l1.player_id = l2.player_id
       				where l1.login_time < '{$p_time}' and l2.login_time between '{$p_time}' and '{$p_time_tmp}'
       				group by l1.player_id
         			");
       		$timeD1_arr = array();
			$timeD4_arr = array();
			$timeD6_arr = array();
			$timeD8_arr = array();
			$timeD15_arr = array();
			$timeM1_arr = array();
			$timeM2_arr = array();
			$timeM3_arr = array();
			$timeM6_arr = array();
			$dada_arr = array();//隔一天算回流!--
       		foreach ($login_time_Arr as $key => $value) {
       			$v = reset($value);
       			if($v < $p_time && $v > strtotime('-3 day' , $p_time))
       				$timeD1_arr[] = $v;
       			if($v < strtotime('-3 day' , $p_time) && $v > strtotime('-5 day' , $p_time))
       				$timeD4_arr[] = $v;
       			if($v < strtotime('-5 day' , $p_time) && $v > strtotime('-7 day' , $p_time))
       				$timeD6_arr[] = $v;
       			if($v < strtotime('-7 day' , $p_time) && $v > strtotime('-14 day' , $p_time))
       				$timeD8_arr[] = $v;
       			if($v < strtotime('-14 day' , $p_time) && $v > strtotime('-30 day' , $p_time))
       				$timeD15_arr[] = $v;
       			if($v < strtotime('-1 month, +1 day' , $p_time) && $v > strtotime('-2 month', $p_time))
       				$timeM1_arr[] = $v;
       			if($v < strtotime('-2 month, +1 day' , $p_time) && $v > strtotime('-3 month', $p_time))
       				$timeM2_arr[] = $v;
       			if($v < strtotime('-3 month, +1 day' , $p_time) && $v > strtotime('-6 month', $p_time))
       				$timeM3_arr[] = $v;
       			if($v < strtotime('-6 month, +1 day' , $p_time))
       				$timeM6_arr[] = $v;
       			if($v < strtotime('-1 day', $p_time))
       				$dada_arr[] = $v;
       		}
       		$result_num['timeD1'] = count( $timeD1_arr );
       		$result_num['timeD4'] = count( $timeD4_arr );
       		$result_num['timeD6'] = count( $timeD6_arr );
       		$result_num['timeD8'] = count( $timeD8_arr );
       		$result_num['timeD15'] = count($timeD15_arr);
       		$result_num['timeM1'] = count( $timeM1_arr );
       		$result_num['timeM2'] = count( $timeM2_arr );
       		$result_num['timeM3'] = count( $timeM3_arr );
       		$result_num['timeM6'] = count( $timeM6_arr );
       		$result_num['dada'] = count($dada_arr);

       	$result = $result_num['dada'];
        return Response::json($result);
    }

    public function getPokerBackStatOld()//老用户
    {
        $platform_id = Input::get('platform_id');
        $game_id = Input::get('game_id');
        $server_internal_id = Input::get('server_internal_id');
        $start_time = Input::get('start_time');//注册时间
        $end_time   = Input::get('end_time');//注册时间
        $p_time     = Input::get('p_time');//登录时间开始
        $pd_time    = Input::get('pd_time');//距离登陆时间往前间隔 player_death_time
        $db_qiqiwu = DB::connection($this->db_qiqiwu);
        $db_dot =    DB::connection($this->db_name);

        	$p_time_tmp = $p_time + 1*24*3600;
        	$death_begin_time = $p_time - $pd_time;
       		$result_num = $db_dot->select("
		       			select count( C.player_id ) 
						from (
							select distinct player_id
							from `{$this->db_name}`.log_login
							where login_time >= '{$p_time}' and login_time < '{$p_time_tmp}'
						) as C
						left join `{$this->db_name}`.log_create_player B 
						on C.player_id = B.player_id
						left join  `{$this->db_qiqiwu}`.users A 
						on B.user_id = A.uid
						where A.created_time between from_unixtime({$start_time}) and from_unixtime({$end_time})
						and B.player_id IS NOT NULL
         			");

        //Log::info(var_export('number of DISTINCT player_id:', true));
        //Log::info(var_export($result_num, true));
        $result = (int)reset($result_num[0]);
        return Response::json($result);
    }

    //获得一定时间内首次充值的玩家
    public function getFirstPayPlayer()
    {
    	$platform_id = Input::get('platform_id');
        $game_id = Input::get('game_id');
        $server_internal_id = Input::get('server_internal_id');
        $start_time = Input::get('start_time');
        $end_time   = Input::get('end_time');
        $db =    DB::connection($this->db_payment);
        $result_num = $db->select("select count(*) from (
        						select pay_user_id,offer_time from pay_order
        						where offer_yuanbao=1 group by pay_user_id
        						having min(offer_time) between '{$start_time}' and '{$end_time}'
        						) as tmp");//pay_order
        $result = (int)reset($result_num[0]);
        if($result){
        	return Response::json($result);
        }else{
        	return Response::json('No data from '.$this->db_payment.' pay_order.');
        }
    }

    //匿名登录的玩家
    public function getAnonyPlayer()
    {
    	$platform_id = Input::get('platform_id');
        $game_id = Input::get('game_id');
        $server_internal_id = Input::get('server_internal_id');
        $start_time = Input::get('start_time');
        $end_time   = Input::get('end_time');
        $db_qiqiwu = DB::connection($this->db_qiqiwu);
        $db_name = DB::connection($this->db_name);

        $result_num = $db_name->select("
        				select count( C.player_id ) 
        				from(
        					select distinct player_id
							from `{$this->db_name}`.log_login
							where login_time >= '{$start_time}' and login_time < '{$end_time}'
						) as C
        				left join `{$this->db_name}`.log_create_player B 
						on C.player_id = B.player_id
						left join `{$this->db_qiqiwu}`.users A
						on B.user_id = A.uid 
						where A.still_anonymous = 1
        	");
        
        $result = (int)reset($result_num[0]);
        if($result){
        	return Response::json($result);
        }else{
        	return Response::json('No data from '.$this->db_payment.' log_create_player, _login and users');
        }
    }

     //德扑每日登陆
     public function getGamesData()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error' => ''
    	);
    	$start_time = Input::get('start_time');
    	$end_time = Input::get('end_time');
    	$db_name = DB::connection($this->db_name);

    	$sql1="select website_log as num1,Anonymouse_log as num2,
    			share_log as num3 
    			from daily_visit 
    			where date_log between {$start_time} and {$end_time}";
    	$sql2 = "select count(distinct player_id) as num,  count(1) as times
    			from log_login 
    			where login_time between {$start_time} and {$end_time}";
    	$sql3 = "select count(distinct player_id) as num,  count(1) as times
    			from log_economy where action_type = 'endOneRound' 
    			and action_time between {$start_time} and {$end_time}";

    	$log1 = $db_name->select($sql1);
    	$log2 = $db_name->select($sql2);
    	$log3 = $db_name->select($sql3);

    	$result = array(
    		'log1' => $log1,
    		'log2' => $log2,
    		'log3' => $log3,
    	);
    	if ($result) {
    		return Response::json($result);
    	}else{
    		$msg['error'] = Lang::get('slave.slave_result_none');
    		return Response::json($msg, 403);
    	}
    }


    /*
	获取登录时长  xianshui 2014.11.13
	*/
	public function getLogLogin()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$start = Input::get('start');
		$end = Input::get('end');
		$db = DB::connection($this->db_name);
		$sql = "SELECT l1.login_time as time1, l2.login_time as time2 , l1.player_id , l1.is_login  as login1, l2.is_login as login2 from log_login l1 left join log_login l2 on l1.player_id = l2.player_id where l1.is_login = 1 and l2.is_login =-1 and l1.login_time >= $start and l2.login_time <= $end ";	
		$users = $db->select($sql);
		if ($users) {
			return Response::json($users);
		}else{
			$msg['error'] = Lang::get('salve.slave_result_none');
			return Response::json($msg, 403);
		}
	}

	//筹码流向查询
	public function queryChips()
	{
		//Log::info("OOOOOOOOOO");
		$platform_id = Input::get('platform_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$player = Input::get('player_id');

		$db = DB::connection($this->db_name);
		$query = "select a.log_time as time,a.room_id,a.player_id as player,a.diff_tongqian as delta from log_settle As a
				  join log_settle As b
				  on
				  b.player_id={$player} and
				  a.log_time = b.log_time  and a.room_id = b.room_id and a.rule_id = b.rule_id
				  where a.log_time>{$start_time} and a.log_time<{$end_time} and b.log_time>{$start_time} and b.log_time<{$end_time} and b.diff_tongqian != 0
				  order by a.log_time,a.room_id";
		// Log::info($query);
		$data = $db->select($query);
		
		if(!$data){
			return array('0' => array(
								'delta' => '-1',
								'player' => '-1',
							));
		}
		$i = 0;
        $dataToArray = array();
        while($row = array_shift($data)){
            $dataToArray[$i++] = $row;
        }
      	
       	//Log::info($R);
       	// $dataToArray[$i] = $dataToArray[0];
       	// $dataToArray[$i]->time = 0;
       	// $dataToArray[$i]->room_id = 0;
		$dataToArray[$i] = array("endflag"=>1);

        $j = 0;
        $zheng = 0;
        $fu = 0;
        $flag = 0;
        $Sum = 0;
        $num = count($dataToArray);
        $result = array();
        for($i = 0;$i < $num;$i ++){
            if($i == 0){
                $Same_T = $dataToArray[0]->time;
                $Same_R = $dataToArray[0]->room_id;
            }
  			// Log::info("isset 0".isset($dataToArray[0]->player)."____");
            if($i != count($dataToArray)-1 && $dataToArray[$i]->time == $Same_T && $dataToArray[$i]->room_id == $Same_R){
                if($dataToArray[$i]->player == $player){
                    $Sum += $dataToArray[$i]->delta;
                    // Log::info("player=____".$player."nowPlayer=___".$dataToArray[$i]->player."i=____".$i."__delta=____".$dataToArray[$i]->delta."_____");
                    if($dataToArray[$i]->delta > 0){
                        $flag = 1;
                    }else{
                        $flag = 0;
                    }
                }
                if($dataToArray[$i]->delta > 0){
                    $zheng += $dataToArray[$i]->delta;//当局所有玩家的赢钱的量(无用)
                     // Log::info("zheng===========".$zheng."________________".$i."______");
                }else{
                    $fu += $dataToArray[$i]->delta;//当局所有玩家的输钱的量
                    // Log::info("fu===============".$fu."___________________".$i."______");
                }
            }
            if($i == $num - 1 || $dataToArray[$i]->time != $Same_T || $dataToArray[$i]->room_id != $Same_R){
                for($k = $j;$k < $i;$k ++){
                    if($flag == 1){
                        if($dataToArray[$k]->delta < 0 && $fu != 0 && $dataToArray[$k]->player != $player && $Sum){
                            if(isset($result[$dataToArray[$k]->player]))
                                $result[$dataToArray[$k]->player]['delta'] += floor($Sum * ($dataToArray[$k]->delta / $fu));
                            else{
                                $result[$dataToArray[$k]->player]['delta'] = floor($Sum * ($dataToArray[$k]->delta / $fu));
 								$result[$dataToArray[$k]->player]['id'] = $dataToArray[$k]->player;
                            }
                        }
                    }else   if($dataToArray[$k]->delta > 0 && $zheng != 0 && $dataToArray[$k]->player != $player && $Sum){
                        if($dataToArray[$k]->delta > 0){
                            if(isset($result[$dataToArray[$k]->player]))
                                // $result[$R[$k]->player]['delta'] += floor($L * ($R[$k]->delta / $zheng));
                                $result[$dataToArray[$k]->player]['delta'] += floor($dataToArray[$k]->delta * $Sum / $fu * -1);
                            else{
                                // $result[$R[$k]->player]['delta'] = floor($L * ($R[$k]->delta / $zheng));
                                $result[$dataToArray[$k]->player]['delta'] = floor($dataToArray[$k]->delta * $Sum / $fu * -1);
                                $result[$dataToArray[$k]->player]['id'] = $dataToArray[$k]->player;
                            }
                        }
                    }
                    // Log::info("___".$dataToArray[$k]->delta."____".$Sum."_________".$fu."\n");
                    // Log::info($result);
                }
                $j = $i;
                $zheng = $fu = 0;
                $Sum = 0;
                if($i != $num-1){
                	$Same_T = $dataToArray[$i]->time;
               		$Same_R = $dataToArray[$i]->room_id;
               		$i --;
                }
            }
        }
        function comp($a,$b)
        {
        	if($a['delta'] > $b['delta']) return -1;
        	else if($a['delta'] == $b['delta']) return 0;
        	else return 1;
        }
        usort($result,'comp');
        //Log::info(json_encode($result));
        return Response::json($result);
	}
	//牌局统计 by mumu
	public function queryPoker()
	{
		$platform_id = Input::get('platform_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$player_id = Input::get('player_id');
		// $db_name = 	"11.1";
		//Log::info($platform_id);
		$db = DB::connection($this->db_name);
		$sql='select GAME_TYPE, BLIND,count(distinct concat(ROOM_ID,TIME)) as COUNT_GAME_ID,count(distinct PLAYER_ID) as COUNT_PLAYER_ID,sum(TABLE_FEE)/PLAYER_NUM as sum_table_fee_player_num from 
				log_gameplay where TIME>'.$start_time.' and TIME<'.$end_time.' group by GAME_TYPE,BLIND';
		$result  = $db->select($sql);
		$reflect = array('',
			'新手','初级','中级','高级','快速','淘汰赛','锦标赛','循环赛','spin&go'
			);
		for ($i=0; $i < count($result); $i++) { 
			# code...
			$result[$i]->GAME_TYPE = $reflect[$result[$i]->GAME_TYPE];
		}
		//Log::info(json_encode($result));
		return Response::json($result);
	}
	//不同盲注场玩牌统计
	public function queryPlayCount()
	{
		$start_time = Input::get('start_time');
		$end_time 	 = Input::get('end_time');
		$game_id 	 = Input::get('game_id');
		$db = DB::connection($this->db_name);
		$result = array();
		$d1 = date("Y-m-d",$start_time);
		$d2 = date("Y-m-d",$end_time);
		if($d1 != $d2){
			$temp_time_1 = $start_time;
			$temp_time_2 = strtotime("+1 day",$temp_time_1);

			while($temp_time_2 < ($end_time + 3700)){
				$sql = "select blind,count(distinct player_id) as player_num,count(1) as game_num from log_playcount 
				where time between {$temp_time_1} and {$temp_time_2} group by blind";
				// $result = $db->table('log_playcount')->whereBetween('time',array($temp_time_1, $temp_time_2))
				// 			->groupBy('blind')->select('blind,count(distinct player_id),count(distinct time)')->get();
				$res = $db->select($sql);
				$res = (array)$res;
				$result[date("Y-m-d",$temp_time_1)] = $res;

				$temp_time_1 = $temp_time_2;
				$temp_time_2 = strtotime("+1 day",$temp_time_2);
			}
		}else{
			$sql = "select blind,count(distinct player_id) as player_num,count(1) as game_num from log_playcount
					where time between {$start_time} and {$end_time} group by blind";
			$res = $db->select($sql);
			$result[date("Y-m-d",$start_time)] = $res;
		}
		return $result;
	}

	//经济日志查询-奇修 by mumu
	public function queryLogEconomy()
	{
		$platform_id = Input::get('platform_id');
		$start_time  = Input::get('start_time');
		$end_time 	 = Input::get('end_time');
		$game_id 	 = Input::get('game_id');
		$this->db_name = $game_id.'.1';
		// $db_name = 	"11.1";
		if (!$this->db_name) {
			Log::info($this->db_name);
		}
		$db = DB::connection($this->db_name);
		$sql="select (diff_tongqian>0) as is_fafang, from_unixtime(action_time, '%Y%m%d') as d, action_type, SUM( diff_tongqian ) s 
				from  `log_economy` 
				where action_time >= ".$start_time." and action_time <= ".$end_time."
				group by is_fafang, d, action_type
				order by is_fafang, d, s";
		$sql2 = "select from_unixtime(action_time, '%Y%m%d') as d,tongqian,sum(tongqian) as sum 
				from  `log_economy` USE INDEX ( action_time )
				where action_type='createPlayer' and 
				action_time >= ".$start_time." and action_time <= ".$end_time." group by d, tongqian";
		$re1  = $db->select($sql);
		$re2  = $db->select($sql2);
		$result = array(
			're1' => $re1,
			're2' => $re2
			);
		//Log::info(json_encode($result));
		return Response::json($result);
	}

	public function querySteadWinPlayer()
	{
	  $start_time = Input::get('start_time');
      $end_time = Input::get('end_time');
 	  $server = Server::find(13);

 	  if (!$server) {
				return Response::json($msg, 403);
		}

      $api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
      $response = $api->steadWinPlayer($start_time,$end_time);
    
      return Response::json($response);
	}

	//德扑退款查询
	public function getPokerRefund()
	{
		$platform_id = Input::get('platform_id');
		$game_id = Input::get('game_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$page = Input::get('page');
        $per_page = Input::get('per_page');
        $page = $page > 0 ? $page :1;
        $per_page = $per_page > 0 ? $per_page : 30;
		$offset = $page == 1 ? 0 : ($page-1)*$per_page;
		$db = DB::connection($this->db_payment);
		//$user = $db->select("select order_sn,refundable_amount,create_time,refundable_amount,status,refund_time,currency,user_name,user_fb_id,refund_amount from dispute_order where refund_time > {$start_time} and refund_time <{$end_time} limit $offset,$per_page");
		$sql1 = "select * from dispute_order where refund_time > {$start_time} and refund_time <{$end_time} ";
		$sql2 = "select order_sn,refundable_amount,create_time,refundable_amount,status,refund_time,currency,user_name,user_fb_id,refund_amount from dispute_order where refund_time > {$start_time} and refund_time <{$end_time} limit $offset,$per_page"; 
		$arr1 = $db->select($sql1);
		$count = count($arr1);
		$user = $db->select($sql2);
		$result = array(
            'count' => $count,
            'total' => ceil($count / $per_page),
            'per_page' => $per_page,
            'current_page' => $page,
            'items' =>$user
        );

		if ($result) {
			return Response::json($result);
		} else {
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}

	//从服务器上取得错误储值的信息，并独立发送报警邮件
	public function getRechargeFailInfoFromSlave()
	{
		$game_id = Input::get('game_id');
		$platform_id = Input::get('platform_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$from_email = Input::get('from_email');
		$email_to = Input::get('email_to');
		$email_subject = Input::get('email_subject');
		$db = DB::connection($this->db_payment);
		if($db){
			$info = $db->select("select * from pay_order where get_payment=1 and offer_yuanbao=0
				and pay_time between {$start_time} and {$end_time} and game_id = {$game_id}");
		}
		else{
			Log::info(var_export($db_payment.'DB connect fail.', true));
			$msg['error'] = 'DB connect fail';
			return Response::json($msg, 403);
		}
		$mail_data = array();
		if($info){
			foreach ($info as $key => $line) {
				if(isset($line->giftbag_id)){
					$giftbag_id = $line->giftbag_id;
				}else{
					$giftbag_id = '-';
				}
				if(isset($line->combined_order) && $line->combined_order){
					$combined_order = $line->combined_order;
					$combined_orders = json_decode($combined_order);
					if(count($combined_orders)){
						$sum_pay_amount = PayOrder::on($this->db_payment)->whereIn('order_sn', $combined_orders)->where('get_payment', 1)->selectRaw("sum(pay_amount) as combined_pay_amount")->get();
						$sum_pay_amount = $sum_pay_amount[0]->combined_pay_amount;
					}else{
						$sum_pay_amount = 0;
					}
					if('-' != $giftbag_id){
						$giftbag_price = $db->table('giftbag_list as gl')->join('gift_price_list as gpl', function($join) use ($giftbag_id){
										$join->on('gl.price', '=', 'gpl.price_amount')
											->on('gl.currency', '=', 'gpl.price_currency_id')
											->where('gl.giftbag_id', '=', $giftbag_id);
										})
										->where('gpl.pay_type_id', $line->pay_type_id)
										->where('gpl.method_id', $line->method_id)
										->where('gpl.currency', $line->currency)
										->select('gpl.amount')
										->get();
						if(count($giftbag_price)){
							$giftbag_price = $giftbag_price[0]->amount;
						}else{
							$giftbag_price = 0;
						}
					}else{
						$giftbag_price = 0;
					}
					if($sum_pay_amount && $giftbag_price){
						if($sum_pay_amount < $giftbag_price){
							continue;
						}
					}
				}else{
					$combined_order = '-';
				}

				$server_info = $db->table('pay_order as o')->join($this->db_qiqiwu.".server_list as sl", function($join) use ($line){
					$join->on('o.server_id', '=', 'sl.server_id')
						->where('o.order_id', '=', $line->order_id);
				})->first();
				if($server_info){
					$server_internal_id = $server_info->server_internal_id;
				}else{
					$server_internal_id = 0;
				}

				$mail_data[] = array(
						'order_id' => $line->order_id,
						'order_sn' => $line->order_sn,
						'pay_type_id' => $line->pay_type_id,
						'method_id' => $line->method_id,
						'tradeseq' => $line->tradeseq,
						'server_internal_id' => $server_internal_id,
						'combined_order' => $combined_order,
						'pay_user_id' => $line->pay_user_id,
						'pay_amount' => $line->pay_amount,
						'currency_id' => $line->currency,
						'create_time' => date('Y-m-d H:i:s', $line->create_time),
						'pay_time' => date('Y-m-d H:i:s', $line->pay_time),
						'get_payment' => $line->get_payment,
						'yuanbao_amount' => $line->yuanbao_amount,
						'giftbag_id' => $giftbag_id,
				);
			}
		}
		if(!$mail_data)
		{
			Log::info(var_export('Mail did not send because there is no data. game_id:'.$game_id, true));
			return Response::json(array());
		}
		$data = array(
				'mail_data' => $mail_data,
				'game_id' => $game_id,
				'start_time' => date('Y-m-d H:i:s', $start_time),
				'end_time' => date('Y-m-d H:i:s', $end_time),
			);
		$dayreport_str = 'rechargeFail';

		Mail::send($dayreport_str, $data, function($message) use ($from_email, $email_to, $email_subject)
		{
			$message->subject($email_subject);
			$message->from($from_email, 'cs');
			$message->to($email_to);
		});
		
		Log::info(var_export('Mail send execute.', true));
		return Response::json($mail_data);
	}

	public function getSoldStatics()
	{
		$game_id = Input::get('game_id');
		$platform_id = Input::get('platform_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$db = DB::connection($this->db_name);

		$persons_number = array();
		$tmp = $db->select("
				SELECT COUNT( DISTINCT player_id ) as pers_num , diff_yuanbao, count(diff_yuanbao) as yuanb_num
				FROM  log_economy
				WHERE action_type=8220
							and action_time between {$start_time} and {$end_time}
				GROUP BY diff_yuanbao
				");
		if($tmp){
			foreach ($tmp as $value) {
				$v = (array) $value;
				$persons_number[] = array(
					'pers_num'=> $v['pers_num'],
					'yuanbao'=> -$v['diff_yuanbao'], 
					'yuanb_num'=> $v['yuanb_num']
					);
			}
			return Response::json($persons_number);
		}else{
			return Response::json(0);
		}
	}
	public function sameIpData()
    {
    	$game_id = Input::get('game_id');
    	$platform_id = Input::get('platform_id');
		$server_id = Input::get('server_internal_id');
		$ip = Input::get('ip');
		$db = DB::connection($this->db_qiqiwu);
		//$sql1 = "select last_visit_ip as ip,uid from users where last_visit_ip='{$ip}'";
		$sql1 = "select if(created_ip='{$ip}',created_ip,last_visit_ip) as ip,if(created_ip='{$ip}','创建IP','最后访问IP') as type,if(created_ip='{$ip}',created_time,last_visit_time) as time,uid from users where created_ip='{$ip}' or last_visit_ip='{$ip}'";
		$user1 = $db->select($sql1);
		$count = count($user1);
		$db = DB::connection($this->db_name);
		$user2=array();
		for($i=0;$i<$count;$i++){
			//$sql2 = "select c.player_id,c.player_name,FROM_UNIXTIME(c.created_time, '%Y-%m-%d %H:%i:%s') as created_time,e.tongqian from log_create_player c left outer join log_economy e on c.player_id=e.player_id where c.user_id='{$user1[$i]->uid}'";
			$sql2 = "select c.player_id,c.player_name,e.tongqian from log_create_player c left outer join log_economy e on c.player_id=e.player_id where c.user_id='{$user1[$i]->uid}'";
			$user2[$i] = $db->select($sql2);
		}
		$result = array(
    		'user1' => $user1,
    		'user2' => $user2,
    	);
		if ($result) {
			return Response::json($result);
		}else {
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
    }
    //德扑比赛查询
    public function matchRankData()
    {
    	$game_id = Input::get('game_id');
    	$platform_id = Input::get('platform_id');
		$server_id = Input::get('server_internal_id');
		$player_id = Input::get('player_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$db = DB::connection($this->db_name);
		$sql = "select distinct if(rule_id=8001 || rule_id=8002,'round robin','Kejuaraan') as match_type,match_id,player_ranking,acquire_tongqian,acquire_token,acquire_fragment,acquire_goods,acquire_integral from log_match_rank where player_id={$player_id} and log_time between {$start_time} and {$end_time} and (rule_id like '7%' or rule_id=8001 or rule_id=8002) order by log_time desc";
		$user= $db->select($sql);
		$result = $user;
		if ($result) {
			return Response::json($result);
		}else {
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
    }
    //gm 未回复列表
    public function getGMQuestions()
    {
    	//server
    	$server_id = Input::get('server_id');
    	$server_name = Input::get('server_name');
    	$api_server_ip = Input::get('api_server_ip');
    	$api_server_port = Input::get('api_server_port');
    	$api_dir_id =  Input::get('api_dir_id');

		//Log::info(var_export($server_id , true));
		//Log::info(var_export($server_name , true));

    	$api = GameServerApi::connect($api_server_ip, $api_server_port, $api_dir_id);
        $response = $api->getGMQuestions();



		//Log::info(var_export($api , true));
		//Log::info(var_export($response , true));

        if (!empty($response->GM_Logs)) 
        {
            $log = $response->GM_Logs;
            $types = array(
                1 => Lang::get('serverapi.gm_type_bug'),
                2 => Lang::get('serverapi.gm_type_complaint'),
                3 => Lang::get('serverapi.gm_type_advice'),
                4 => Lang::get('serverapi.gm_type_other')
            );
            $result = array();
            foreach ($log as $key => &$v) {
                $v->type_name = $types[(int) $v->GMType];
                $v->ser_id = $server_id;
                $v->server_name = $server_name;
                if (! isset($v->Name)) {
                    $player = $api->getPlayerInfoByPlayerID((int) $v->PlayerID);
                    if ($player && isset($player->Name)) {
                        $v->Name = $player->Name;
                    }
                }
                $v->SendTime = date('Y-m-d H:i:s', $v->SendTime);
                $result[] = $v;
            }
            return Response::json($result);
        }
    	return Response::json(array());
    }
    //德扑经济查询
	public function queryEconomy(){	
		$game_id = Input::get('game_id');
		$platform_id=Input::get('platform_id');
		$start=Input::get('start');
		$end = strtotime($start)+86400;
		$start = strtotime($start);
		$db = DB::connection($this->db_name);
		$sql="select sum(diff_tongqian) as chips from log_economy use index (action_time)
			where diff_tongqian > 0 and action_time between {$start} and {$end} 
			union 
			select sum(diff_tongqian) as chips from log_economy use index (action_time)
			where diff_tongqian < 0 and action_time between {$start} and {$end}";
		$result = $db->select($sql);
		if ($result) {
			return Response::json($result);
		}else {
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}
	//德扑比赛场用户玩牌统计
    public function matchAreaData()
    {
    	$game_id = Input::get('game_id');
    	$platform_id = Input::get('platform_id');
		$server_id = Input::get('server_internal_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$db = DB::connection($this->db_name);
		//$sql = "select count(distinct(lm.player_id)),sum(lm.acquire_tongqian),sum(le.diff_tongqian) from log_match_rank as lm left join log_economy as le on lm.player_id=le.player_id where lm.rule_id like '60%' and le.action_type like '60%' and diff_tongqian<0;select count()";
		$sql="select sum(case when rule_id like '60%' then 1 else 0 end) as sit,
		sum(case when rule_id like '7%' then 1 else 0 end)as kej,
		sum(case when (rule_id='8001' or rule_id='8002') then 1 else 0 end) as round,
		sum(case when rule_id like '90%' then 1 else 0 end) as spin,
		sum(case when rule_id='7032' then 1 else 0 end) as iphone6,
		sum(case when rule_id='7033'then 1 else 0 end) as dewa 
		from 
		( select distinct player_id,rule_id 
			from log_match_rank 
			where log_time>={$start_time} and log_time<={$end_time}
		)as table1 
		 UNION ALL 
		 select sum(case when rule_id like '60%' then acquire_tongqian else 0 end) as sit,
		 sum(case when rule_id like '7%' then acquire_tongqian else 0 end) as kej,
		 sum(case when (rule_id='8001' or rule_id='8002') then acquire_tongqian else 0 end) as round,
		 sum(case when rule_id like '90%' then acquire_tongqian else 0 end) as spin,
		 sum(case when rule_id='7032' then acquire_tongqian else 0 end) as iphone6,
		 sum(case when rule_id='7033'then acquire_tongqian else 0 end) as dewa
		 from 
		 ( select distinct * from log_match_rank 
		 	where log_time>={$start_time} and log_time<={$end_time}
		 )as table1";
		$result=$db->select($sql);
		if ($result) {
			return Response::json($result);
		}else {
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
    }
    
      public function searchServerPlayer()
     {
     	$game_id = Input::get('game_id');
     	$platform_id = Input::get('platform_id');
 		$server_id = Input::get('server_internal_id');
 		$item_id = Input::get('item_id');
 		$start_time = Input::get('start_time');
 		$end_time = Input::get('end_time');
 		$db = DB::connection($this->db_name);
 		$sql="select distinct player_id from log_item where item_id={$item_id} and time>={$start_time} and time<={$end_time}";
 		$result=$db->select($sql);
 		if ($result) {
 			return Response::json($result);
 		}else {
 			$msg['error'] = Lang::get('slave.slave_result_none');
 			return Response::json($msg, 404);
 		}
     }
     public function getPlayerLoginTime()
	{
		$player_name = Input::get('player_name');
		$player_id = (int)Input::get('player_id');
		$server_internal_id = (int)Input::get('server_internal_id');
		$game_id = (int)Input::get('game_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$db = DB::connection($this->db_name);

		if($player_name && !$player_id){
			if(in_array($game_id, Config::get('game_config.mobilegames'))){
				$name_sql = $db->table('log_player_name')->whereRaw("binary player_name = '$player_name'")->orderBy('id', 'desc')->first();
				$player_id = isset($name_sql->player_id) ? $name_sql->player_id : '';
			}else{
				$name_sql = $db->table('log_create_player')->whereRaw("binary player_name = '$player_name'")->first();
				$player_id = isset($name_sql->player_id) ? $name_sql->player_id : '';
			}
		}
		if(!$player_id){
			$msg['error'] = Lang::get('slave.slave_result_none1');
 			return Response::json($msg, 404);
		}
		if(in_array($game_id, Config::get('game_config.mobilegames'))){ //萌娘三国日志库字段命名有区别，因此分开执行
			$sql="select FROM_UNIXTIME(action_time,'%Y-%m-%d %T') as time,if(action=-1,'登出','登录') as statu,lev as level,last_ip as last_ip from log_login where player_id={$player_id} and action_time between {$start_time} and {$end_time} order by id desc";
		}else{
			$sql="select FROM_UNIXTIME(login_time,'%Y-%m-%d %T') as time,if(is_login=-1,'登出','登录') as statu,level as level,remote_host as last_ip from log_login where player_id={$player_id} and login_time between {$start_time} and {$end_time} order by log_id desc";
		}
		$result=$db->select($sql);
 		if ($result) {
 			return Response::json($result);
 		}else {
 			return Response::json(array('error' => 'Not Found data'), 404);
 		}
	}
	public function getMingGeLog()
    {
        $msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );

        $player_id = Input::get('player_id');
        $start_time = (int)Input::get('start_time');
        $end_time = (int)Input::get('end_time');
        $type = (int)Input::get('type');
        $db = DB::connection($this->db_name);
        $str = "";
        $sql = "";
        if ($type > 0 ) { //选择类别
                $str = " and (from_id={$type} or to_id={$type})";
        }
        $sql = "select * from log_mingge where player_id = {$player_id} and action_time between {$start_time} and {$end_time}" . $str;
        Log::info('marktest:'. $type . ':' . $sql);
        $info = $db->select($sql);
        if (isset($info)) {
                return Response::json($info);
        } else{
                return Response::json($msg, 403);
        }
    }
    public function getUserPhone()
	{
		$game_id = (int)Input::get('game_id');
		$platform_id = Input::get('platform_id');
		$created_ip = Input::get('created_ip');
		$db = DB::connection($this->db_qiqiwu);
		$sql="select device_type from device_list where ip='{$created_ip}' limit 1";
		$phone=$db->select($sql);
		if($phone){
			return Response::json($phone);
		}
	}
	//夜夜三国查log
	 public function getPlayerLogDate()
	{
		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );

		$game_id = (int)Input::get('game_id');
		$player_id = (int)Input::get('player_id');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$db = DB::connection($this->db_name);
		$result = $db->table('log_summon')
		             ->where('player_id', $player_id)
		             ->whereBetween('created_at', array($start_time, $end_time))
		             ->selectRaw("player_id,scroll_id,table_id,created_at")
		             ->get();
		if($result){
			return Response::json($result);
		}else{
			return Response::json($msg,403);
		}
	}

	public function getYysgLifetime(){ //夜夜三国查询玩家生命周期
		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );

		$game_id = (int)Input::get('game_id');
		$platform_id = (int)Input::get('platform_id');
		$server_id = (int)Input::get('server_id');
		$check_type = (int)Input::get('check_type');
		$time_stamp = (int)Input::get('time_stamp');
		$key2time = array(
			0 => array(0, 3*86400),
			1 => array(3*86400, 7*86400),
			2 => array(7*86400, 30*86400),
			3 => array(30*86400, 99999*86400),
			);

		if('1' == $check_type){ //所有玩家
			$db = DB::connection($this->db_name);
			$result = $db->table(DB::raw('(select player_id,max(action_time) as last_time from log_login group by player_id) as ld'))
						 ->Join('log_create_player as lcp', function($join) use ($key2time, $time_stamp) {
						 	$join->on('lcp.player_id', '=', 'ld.player_id')
						 		 ->on(DB::raw("lcp.created_time + {$key2time[$time_stamp][0]}"), '<', "ld.last_time")
						 		 ->on(DB::raw("lcp.created_time + {$key2time[$time_stamp][1]}"), '>', "ld.last_time");
						 })
						 ->selectRaw("avg(ld.last_time - lcp.created_time) as avgtime,count(distinct lcp.player_id) as count")
						 ->get();
		}elseif('2' == $check_type){
			$db = DB::connection($this->db_payment);
			$pay_user_ids = "(select distinct pay_user_id from pay_order where game_id = $game_id and get_payment = 1) as o";
			$result = $db->table(DB::raw($pay_user_ids))
			             ->join(DB::raw("`{$this->db_name}`.log_create_player as lcp"), 'o.pay_user_id', '=', 'lcp.uid')
			             ->join(DB::raw("(select player_id,max(action_time) as last_time from `{$this->db_name}`.log_login group by player_id) as ld"), function($join) use($key2time, $time_stamp){
			             	$join->on('ld.player_id', '=', 'lcp.player_id')
			             	     ->on(DB::raw("lcp.created_time + {$key2time[$time_stamp][0]}"), '<', "ld.last_time")
						 		 ->on(DB::raw("lcp.created_time + {$key2time[$time_stamp][1]}"), '>', "ld.last_time");
			             })
			             ->selectRaw("avg(ld.last_time - lcp.created_time) as avgtime,count(distinct lcp.player_id) as count")
						 ->get();
		}

		//$result = $db->select($sql);
		if($result){
			return Response::json($result);
		}else{
			return Response::json($msg,403);
		}

	}
   
   public function getLogindeviceInfo(){ //查询官网logindevice表
		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );

		$game_id = (int)Input::get('game_id');
		$platform_id = (int)Input::get('platform_id');
		$uid = Input::get('uid');
		$device_id = Input::get('device_id');
		$baned = Input::get('baned');

		$db = DB::connection($this->db_qiqiwu);
		$result = array();
		if($baned){
			$result = $db->table(DB::raw('login_device as ld'))
			             ->leftJoin(DB::raw('device_list as dl'), function($join){
			             	$join->on('ld.device_id', '=', 'dl.device_id')
			             	     ->on('ld.game_id', '=', 'dl.game_id');
			             })
			             ->where('dl.limit_type', 1)
			             ->where('ld.game_id', $game_id)
			             ->selectRaw("ld.device_id,ld.uid,ld.create_time,ld.login_time,dl.device_type,dl.os_type,dl.limit_type")
			             ->get();
		}else{
			$result = $db->table(DB::raw('login_device as ld'))
			             ->leftJoin(DB::raw('device_list as dl'), function($join){
			             	$join->on('ld.device_id', '=', 'dl.device_id')
			             	     ->on('ld.game_id', '=', 'dl.game_id');
			             })
			             ->where('ld.game_id', $game_id)
			             ->selectRaw("ld.device_id,ld.uid,ld.create_time,ld.login_time,dl.device_type,dl.os_type,dl.limit_type");
			if($uid){
				$result = $result->where('ld.uid', $uid);
			}elseif ($device_id) {
				$result = $result->where('ld.device_id', $device_id);
			}
			$result = $result->get();
		}
		if($result){
			return Response::json($result);
		}else{
			return Response::json($msg,403);
		}
   }

   public function MGwriteOnlineNum(){
   	//在日志库写入玩家在线数量
		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );

		$game_id = (int)Input::get('game_id');
		$server_internal_id = (int)Input::get('server_internal_id');
		$num = (int)Input::get('num');
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$num_array = array(
								'online_time' => (int)(time()/600)*600,
								'online_value' => $num,
								'server_internal_id' => $server_internal_id,
							);
			$test = OnlineLog::on($this->db_name)->where('online_time', $num_array['online_time'])->first();
			if(!$test){	//没有这个时间的记录再插入
				$result = OnlineLog::on($this->db_name)->insert($num_array);
			}else{
				$result = array();
			}
			return Response::json($result);
		}
   }

   public function playerWjData()
   {
   		$player_id = Input::get('player_id');
   		$wj_id = Input::get('wj_id');
   		$start_time = Input::get('start_time');
   		$end_time = Input::get('end_time');
   		$db = DB::connection($this->db_name);
   		$sql_str ='';
   		if($wj_id){
   			$sql_str=" and `material_table_ids` like '%{$wj_id}%'";
   		}
   		$result = $db->select("select * FROM `log_partner_powerup_and_evolve` WHERE 
   			player_id = {$player_id} and created_at between {$start_time} and {$end_time}" . "$sql_str");
   		return Response::json($result);
   }
   
   public function MGavgonlinetime(){

 		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found'),
        );

   		$game_id = Input::get('game_id');
   		$server_internal_id = Input::get('server_internal_id');
   		$start_time = Input::get('start_time');
   		$end_time = (Input::get('end_time') > time() ? time() : Input::get('end_time'));	//去传过来的截止时间和当前时间更小的值
   		$lev_low = (int)Input::get('lev_low');
        $lev_up = (int)Input::get('lev_up');

        $limit_pay_user = (int)Input::get('limit_pay_user');	//这个值域为-1,0,1，其中-1代表不付费玩家，0代表所有玩家，1代表付费玩家
        $pay_player_ids = array();
        if($limit_pay_user){	//为-1或者1时，查询本服内的所有付费玩家的id
        	CreatePlayerLog::on($this->db_name)
					->join(DB::raw("`$this->db_payment`.pay_order as o"), function($join) use ($game_id) {
						$join->on('p.uid', '=', 'o.pay_user_id')
							 ->where('o.get_payment', '=', 1)
							 ->where('o.game_id', '=', $game_id);
					})
					->selectRaw("distinct p.player_id")
					->orderBy('p.player_id', 'asc')
					->chunk(2000, function($pay_player_id_data) use (&$pay_player_ids){
						foreach ($pay_player_id_data as $value) {
			        		$pay_player_ids[] = $value->player_id;
			        	}
					});
        	unset($pay_player_id_data);
        }

        $db = DB::connection($this->db_name);
        $limit = LoginLog::on($this->db_name)->whereBetween('action_time', array($start_time, $end_time));
		if($lev_up){
			$limit = $limit->whereBetween('lev', array($lev_low, $lev_up));
		}elseif($lev_low){
			$limit = $limit->where('lev', '>', $lev_low);
		}

		$login_times = 0;
        $onlinetime = 0;
        $player_num = 0;
        $last_line = array();

		$limit->orderBy('player_id', 'ASC')->orderBy('action_time', 'asc')->selectRaw("player_id,action_time,action")	//根据玩家id排序非常重要
		      ->chunk(2000, function($tmp_result) use (&$login_times, &$onlinetime, &$player_num, &$last_line, $start_time, $end_time, $limit_pay_user, $pay_player_ids){	//数据量可能过多，每次取2000条处理
		      		foreach ($tmp_result as $single_login) {
		      			if(1 == $limit_pay_user){	//付费玩家
		      				if(!in_array($single_login->player_id, $pay_player_ids)){
		      					continue;	//若本条数据的玩家id不在付费玩家的id中，跳过本条
		      				}
		      			}elseif(-1 == $limit_pay_user){	//免费玩家
		      				if(in_array($single_login->player_id, $pay_player_ids)){
		      					continue;	//若本条数据的玩家id在付费玩家的id中，跳过本条
		      				}
		      			}
		      			if(1 == $single_login->action){	//是登陆的话，那么登陆次数+1
		      				$login_times++;
		      			}
		      			if(count($last_line)){	//非首次执行
		      				if($single_login->player_id == $last_line['player_id']){	//还是同一个玩家的记录
		      					if(-1 == $single_login->action && 1 == $last_line['action']){	//上次登入，本次登出，这种情况下更新在线总时间
		      						$onlinetime += $single_login->action_time - $last_line['action_time'];
		      					}
		      					//更新上一条数据
		      					$last_line = array(
			      					'player_id' => $single_login->player_id,
			      					'action_time' => $single_login->action_time,
			      					'action' => $single_login->action,
		      					);
		      				}else{	//切换玩家
		      					$player_num++;	//增加一个玩家数量
		      					if(1 == $last_line['action']){	//如果记录中的玩家上次是登入操作，那么要增加这个玩家从这个登入到时间结束期间的时间作为在线
		      						$onlinetime += ($end_time - $last_line['action_time']);
		      					}
		      					if(-1 == $single_login->action){	//如果这条记录是登出，那么认为玩家从开始时间到这个时间都是在线的
			      					$onlinetime += ($single_login->action_time - $start_time);
			      				}
			      				$last_line = array(
			      					'player_id' => $single_login->player_id,
			      					'action_time' => $single_login->action_time,
			      					'action' => $single_login->action,
		      					);
		      				}
		      			}else{	//首次执行
		      				$player_num++;	//增加一个玩家数量
		      				if(-1 == $single_login->action){	//如果这条记录是登出，那么认为玩家从开始时间到这个时间都是在线的
		      					$onlinetime += ($single_login->action_time - $start_time);
		      				}
		      				$last_line = array(
		      					'player_id' => $single_login->player_id,
		      					'action_time' => $single_login->action_time,
		      					'action' => $single_login->action,
		      				);
		      			}
		      		}
		      });
		if(count($last_line) && 1 == $last_line['action']){	//最后如果最后一条记录是登入，那么把这个时间算上
			$onlinetime += ($end_time - $last_line['action_time']);
		}

   		if($player_num){
   			 $response = array(
                'playernum' => $player_num,
                'all_online_time' => $onlinetime,
                'all_login_times' => $login_times,
                );
   			return Response::json($response);
   		}else{
   			return Response::json($msg,403);
   		}
   }

   public function signupnum(){	//增加sql执行的次数而减少单次sql中的大量表操作,简约版注册用户统计--Panda
 		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found'),
        );

   		$platform_id = Input::get('platform_id');
   		$game_id = Input::get('game_id');
   		$start_time = Input::get('start_time');
   		$end_time = Input::get('end_time');
   		$server_internal_ids = Input::get('server_internal_ids');

   		$start_time = date('Y-m-d H:i:s', $start_time);
   		$end_time = date('Y-m-d H:i:s', $end_time);

   		$test = SlaveUser::on($this->db_qiqiwu)->first();
   		$judge_game = '';
   		if(isset($test->game_source)){
   			$judge_game = 'u.game_source';
   		}elseif(isset($test->game_id)){
   			$judge_game = 'u.game_id';
   		}
   		unset($test);
   		$test = SlaveCreatePlayer::on($this->db_qiqiwu)->first();
   		$judge_create_player_game = '';
   		if(isset($test->game_id)){
   			$judge_create_player_game = 'cp.game_id';
   		}

		$result = array(
				'sign_not' => 0,
				'create_not' => 0,
				'sign_is' => 0,
				'create_is' => 0,
			);

   		if(0 != count($server_internal_ids)){
   			$first_server_internal_id = array_shift($server_internal_ids);	//因为注册数每次查到都是一样的，因此只在第一次的时候查询，后面不需要查询注册数
   			$temp = SlaveUser::on($this->db_qiqiwu)->getSignupNum($start_time, $end_time, $judge_game, $judge_create_player_game, $game_id, 0, $first_server_internal_id)->get();
   			$result['sign_not'] += $temp[0]->unum;
   			$result['create_not'] += $temp[0]->cpnum;
   			unset($temp);
   			$temp = SlaveUser::on($this->db_qiqiwu)->getSignupNum($start_time, $end_time, $judge_game, $judge_create_player_game, $game_id, 1, $first_server_internal_id)->get();
			$result['sign_is'] += $temp[0]->unum;
			$result['create_is'] += $temp[0]->cpnum;
			unset($temp);
			if(0 != count($server_internal_ids)){
	   			foreach ($server_internal_ids as $server_internal_id) {	//用另一个方法统计，这个方法只统计创建数
	   				$temp = SlaveUser::on($this->db_qiqiwu)->getcreateNum($start_time, $end_time, $judge_game, $judge_create_player_game, $game_id, 0, $server_internal_id)->get();
	   				$result['create_not'] += $temp[0]->cpnum;
	   				unset($temp);
	   				$temp = SlaveUser::on($this->db_qiqiwu)->getcreateNum($start_time, $end_time, $judge_game, $judge_create_player_game, $game_id, 1, $server_internal_id)->get();
					$result['create_is'] += $temp[0]->cpnum;
					unset($temp);
	   			}
   			}
   		}else{
			$temp = SlaveUser::on($this->db_qiqiwu)->getSignupNum($start_time, $end_time, $judge_game, $judge_create_player_game, $game_id, 0)->get();
			$result['sign_not'] = $temp[0]->unum;
			$result['create_not'] = $temp[0]->cpnum;
			unset($temp);
			$temp = SlaveUser::on($this->db_qiqiwu)->getSignupNum($start_time, $end_time, $judge_game, $judge_create_player_game, $game_id, 1)->get();
			$result['sign_is'] = $temp[0]->unum;
			$result['create_is'] = $temp[0]->cpnum;
			unset($temp);
		}

   		if($result){
   			return Response::json($result);
   		}else{
   			return Response::json($msg,403);
   		}
   }

   public function getSqlresult(){
   	 	$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found'),
        );
   		$sql = Input::get('sql');
   		$database = Input::get('database');
   		$ifdownload = Input::get('ifdownload');
   		try {
   			if('qiqiwu' == $database){
   				$db = DB::connection($this->db_qiqiwu);
   			}elseif('payment' == $database){
   				$db = DB::connection($this->db_payment);
   			}else{
   				$db = DB::connection($this->db_qiqiwu);
   			}
   			$result = $db->select($sql);
   		} catch (Exception $e) {
   			$result = array();
   			if(is_numeric(stripos($e,'in /var/www/eastblue/'))){
   				$msg['error'] = substr($e,0,stripos($e,'in /var/www/eastblue/'));
   			}else{
   				$msg['error'] = $e;
   			}
   		}
   		if($result){
   			if($ifdownload){	//如果是要下载的，直接返回结果，不论条数
   				return Response::json($result);
   			}elseif(count($result) > 5000){	//否则查看数量，决定是否返回，因为数量过大会使浏览器崩溃
   				$msg['error'] =  '查询数据超过5000条，不返回';
   				return Response::json($msg,403);
   			}else{
   				return Response::json($result);
   			}
   		}else{
   			return Response::json($msg,403);
   		}
   }

   public function checkyysgplayer(){	//检测一个玩家是否存在于当前游戏的数据库中
   	   	$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found'),
        );
   		$player_ids = Input::get('player_ids');
   		$player_names = Input::get('player_names');
   		$result = array();
   		
   		if(count($player_ids)){
   			foreach ($player_ids as $player_id) {
   				$try = CreatePlayerLog::on($this->db_name)->where('player_id', $player_id)->first();
   				if(!$try){
   					$result[] = $player_id;
   				}
   				unset($try);
   			}
   		}elseif(count($player_names)){
   			foreach ($player_names as $player_name) {
   				$player_name = trim($player_name);
   				$try = PlayerNameLog::on($this->db_name)->where('player_name', $player_name)->first();
   				if(!$try){
   					$result[] = $player_name;
   				}
   				unset($try);
   			}  
   		}

   		if(count($result)){
   			return Response::json($result, 404);
   		}else{
   			return Response::json(array(), 200);
   		}
   }

   public function getplayerinfobyincomplete(){		//通过不完整的玩家信息获取匹配的玩家信息
   		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found'),
        );

   		$type = Input::get('type');
   		$id_or_name = Input::get('id_or_name');
   		$game_id = Input::get('game_id');
   		$result = array();
   		$type2key = array(
   				'0' => 'player_name',
   				'1'	=> 'player_id',
   				'2' => 'uid',
   			);

   		if (in_array($game_id, Config::get('game_config.yysggameids'))) {
   			if('0' == $type){
   				$result = DB::connection($this->db_name)->table(DB::raw('log_player_name as lpn'))->Join(DB::raw('log_create_player as lcp'), function($join){
					$join->on('lpn.player_id', '=', 'lcp.player_id');
				})->where('lpn.'.$type2key[$type], 'like', "%$id_or_name%")
				->selectRaw('lpn.player_name as player_name, 1 as server_internal_id, lpn.player_id as player_id, lcp.uid as uid, from_unixtime(lcp.created_time) as created_time')
				->get();
   			}else{
   				$result = DB::connection($this->db_name)->table(DB::raw('log_create_player as lcp'))->where($type2key[$type], 'like', "%$id_or_name%")
   				->selectRaw('lcp.player_name as player_name, 1 as server_internal_id, lcp.player_id as player_id, lcp.uid as uid, from_unixtime(lcp.created_time) as created_time')
   				->get();
   			}
   		}else{
   			try {
     			$result = DB::connection($this->db_qiqiwu)->table(DB::raw('create_player as cp'))
	     			->Join(DB::raw('server_list as sl'), function($join) use ($game_id){
						$join->on('cp.server_id', '=', 'sl.server_internal_id')
							->where('sl.game_id', '=', $game_id);
					})
					->where($type2key[$type], 'like', "%$id_or_name%")
					->where('cp.game_id', $game_id)
	     			->selectRaw('cp.player_name as player_name, cp.player_id as player_id, cp.uid as uid,cp.server_id as server_internal_id, sl.server_track_name as server_track_name, from_unixtime(cp.created_time) as created_time')
	     			->get(); 				
   			} catch (Exception $e) {
   				$result = DB::connection($this->db_qiqiwu)->table(DB::raw('create_player as cp'))
	   				->Join(DB::raw('server_list as sl'), function($join) use ($game_id){
						$join->on('cp.server_id', '=', 'sl.server_internal_id')
							->where('sl.game_id', '=', $game_id);
					})
					->where($type2key[$type], 'like', "%$id_or_name%")
	     			->selectRaw('cp.player_name as player_name, cp.player_id as player_id, cp.uid as uid,cp.server_id as server_internal_id, sl.server_track_name as server_track_name, from_unixtime(cp.created_time) as created_time')
	     			->get(); 	
   			}
   		}

   		if(count($result) > 0){
   			return Response::json($result);
   		}else{
   			return Response::json($msg,404);
   		}
   }

   public function getplayerinfobyincompleteserver(){
   		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found'),
        );

   		$type = Input::get('type');
   		$id_or_name = Input::get('id_or_name');
   		$game_id = Input::get('game_id');
   		$server_internal_id = Input::get('server_internal_id');

   		$result = array();
   		$type2key = array(
   				'0' => 'player_name',
   				'1'	=> 'player_id',
   				'2' => 'uid',
   			);

   		if(in_array($game_id, Config::get('game_config.mobilegames'))){
   			if('0' == $type){
   				$result = DB::connection($this->db_name)->table(DB::raw('log_player_name as lpn'))->Join(DB::raw('log_create_player as lcp'), function($join){
					$join->on('lpn.player_id', '=', 'lcp.player_id');
				})->where('lpn.'.$type2key[$type], 'like', "%$id_or_name%")
				->selectRaw('lpn.player_name as player_name, lpn.player_id as player_id, lcp.uid as uid, from_unixtime(lcp.created_time) as created_time')
				->get();
   			}else{
   				$result = DB::connection($this->db_name)->table(DB::raw('log_create_player as lcp'))->where($type2key[$type], 'like', "%$id_or_name%")
   				->selectRaw('lcp.player_name as player_name, lcp.player_id as player_id, lcp.uid as uid, from_unixtime(lcp.created_time) as created_time')
   				->get();
   			}
   		}else{
   			$type2key['2'] = 'user_id';
   			$result = DB::connection($this->db_name)->table(DB::raw('log_create_player as lcp'))->where($type2key[$type], 'like', "%$id_or_name%")
   				->selectRaw('lcp.player_name as player_name, lcp.player_id as player_id, lcp.user_id as uid, from_unixtime(lcp.created_time) as created_time')
   				->get();
   		}

   		if(count($result) > 0){
   			return Response::json($result);
   		}else{
   			return Response::json($msg,404);
   		}
   }

   public function getPlayerImportance(){
   		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found'),
        );   
        $game_id = Input::get('game_id');	
        $all_playerids = Input::get('all_playerids');
        if(is_array($all_playerids)){
			$result = CreatePlayerLog::on($this->db_name)
        			->whereIn('player_id', $all_playerids)->selectRaw('player_id')->get();
        }else{
        	$result = array();
        }

        if(count($result)){
        	return Response::json($result);
        }else{
        	return Response::json($msg,404);
        }
   }

   public function playerEquipmentData()
   {
   		$player_id = Input::get('player_id');
   		$start_time = Input::get('start_time');
   		$end_time = Input::get('end_time');
   		$db = DB::connection($this->db_name);
   		//查询装备获得信息
   		$result['get'] = $db->table('log_create_rune')->where('player_id', $player_id)->whereBetween('created_at', array($start_time, $end_time))->get();
   		//查询装备强化信息
   		$result['powerup'] = $db->table('log_rune_powerup')->where('player_id', $player_id)->whereBetween('created_at', array($start_time, $end_time))->get();
   		//查询装备穿戴信息，需要联合武将获取和装备获取表来获取具体的装备和武将是什么
   		$result['equip'] = $db->select("select lre.player_id, lre.slot, lcp.table_id as partner_table_id, 
   					lre.on_id as on_rune_id, lcr1.table_id as on_table_id, lcr1.star as on_star, lcr1.rarity as on_rarity, lcr1.attr as on_attr, 
   					lre.off_id as off_rune_id, lcr2.table_id as off_table_id, lcr2.star as off_star, lcr2.rarity as off_rarity, lcr2.attr as off_attr, lre.created_at 
   					FROM `log_rune_equip` lre join `log_create_partner` lcp on lre.player_id = lcp.player_id and lre.partner_id = lcp.partner_id
   					left join `log_create_rune` lcr1 on lre.player_id = lcr1.player_id and lre.on_id = lcr1.rune_id 
   					left join `log_create_rune` lcr2 on lre.player_id = lcr2.player_id and lre.off_id = lcr2.rune_id
   		 			WHERE lre.player_id = {$player_id} and lre.created_at between {$start_time} and {$end_time}");
   		//装备出售信息，需要联合装备获取表来获取具体出售的装备是什么
   		$tmp_result = $db->table('log_rune_sell')->where('player_id', $player_id)->whereBetween('created_at', array($start_time, $end_time))->get();
   		$result['sell'] = array();
   		foreach ($tmp_result as $value) {
   			$rune_table = $db->select("select rune_id,table_id,star,rarity,attr from `log_create_rune` where player_id = {$value->player_id} and rune_id in ({$value->rune_ids})");
   			foreach ($rune_table as $rune_talbe_id) {
   				$result['sell'][] = array(
	   				'player_id' => $value->player_id,
	   				'rune_id'	=>	$rune_talbe_id->rune_id,
	   				'rune_table_id' => $rune_talbe_id->table_id,
	   				'star' => $rune_talbe_id->star,
	   				'rarity' => $rune_talbe_id->rarity,
	   				'attr' => $rune_talbe_id->attr,
	   				'created_at' => $value->created_at,
   				);
   			}
   		}
   		return Response::json($result);
   }

   public function playerGetWjData()
   {
   		$player_id = Input::get('player_id');
   		$start_time = Input::get('start_time');
   		$end_time = Input::get('end_time');
   		$table_id = Input::get('table_id');
   		$db = DB::connection($this->db_name);
   		$tmp_result = $db->table('log_create_partner')->where('player_id', $player_id)->whereBetween('created_at', array($start_time, $end_time));
   		if($table_id){
   			$tmp_result = $tmp_result->where('table_id', $table_id);
   		}
   		$result = $tmp_result->get();
   		return Response::json($result);
   }

   //查询注册人数
   public function CountUserNum(){
   		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found'),
        ); 
   		$game_id = (int)Input::get('game_id');
   		$start_time_date = Input::get('start_time');
   		$end_time_date = Input::get('end_time');
   		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
   		$interval = Input::get('interval');
   		$u1 = Input::get('u1');
   		$u2 = Input::get('u2');
   		$source = Input::get('source');
   		$is_anonymous = Input::get('is_anonymous');
   		$test = SlaveUser::on($this->db_qiqiwu)->first();

   		$seconds = $interval*86400;

   		$result = SlaveUser::on($this->db_qiqiwu)->selectRaw("(floor((unix_timestamp(created_time) - $start_time)/$seconds)*$seconds+$start_time) as date ,count(1) as usernum");

   		if($u1){
   			$result->where('u', $u1);
   		}
   		if($u2){
   			$result->where('u2', $u2);
   		}
   		if($source){
   			$result->where('source', $source);
   		}
   		if($is_anonymous){
   			$result->where('is_anonymous', $is_anonymous);
   		}
   		if($game_id){
   			if(isset($test->game_source)){
   				$result->where('game_source', $game_id);
   			}
   			if(isset($test->game_id)){
   				$result->where('game_id', $game_id);
   			}
   		}
   		$result->whereBetween('created_time',array($start_time_date,$end_time_date));
   		$result->groupBy('date');
   		$response = $result->get();
   		if(count($response)>0){
			return Response::json($response);
   		}
   		else
   			return Response::json($msg,404);
    }

   public function getUserDeviceInfo()
	{
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$game_id = Input::get('game_id');
		$interval = (int)Input::get('interval');
		$check_type = Input::get('check_type');
        $serach_type = (int)Input::get('serach_type');
        $channel_type = (int)Input::get('channel_type');
        $server_internal_id = Input::get('server_internal_id');
        $db_qiqiwu = DB::connection($this->db_qiqiwu); //夜夜三国在官网的create_player表里没有数据，只有日志库中有数据

		//安装数
		$sql0 = "count(dl.device_id) as usernum, dl.os_type as os_type 
				from device_list dl
				where dl.game_id = $game_id and dl.time between $start_time and $end_time group by date,os_type";
		//注册数
		$sql1 = "count(u.uid) as signupnum, dl.os_type as os_type 
				from users u
                join login_device ld on u.uid = ld.uid and ld.game_id = $game_id
				join device_list dl on dl.device_id = ld.device_id and dl.game_id = $game_id
				where u.game_source = $game_id and u.created_time between from_unixtime($start_time,'%Y-%m-%d %H:%i:%s') and from_unixtime($end_time ,'%Y-%m-%d %H:%i:%s') group by date,os_type";
		if(in_array($game_id, Config::get('game_config.mobilegames'))){		
		//创建角色数
			$sql2 = "count(cp.player_id) as playernum, dl.os_type as os_type 
					from `{$this->db_name}`.log_create_player cp
            	    join (select uid,device_id from login_device group by uid) ld on cp.uid = ld.uid
					join device_list dl on dl.device_id = ld.device_id and dl.game_id = $game_id
					where cp.created_time between $start_time and $end_time group by date,os_type";
		}else{
			//创建角色数
			$sql2 = "count(cp.player_id) as playernum, dl.os_type as os_type 
					from create_player cp
                	join (select uid,device_id from login_device group by uid) ld on cp.uid = ld.uid
					join device_list dl on dl.device_id = ld.device_id and dl.game_id = $game_id
					where cp.game_id = $game_id and cp.created_time between $start_time and $end_time group by date,os_type";
		}

		//付费数
		$sql3 = "sum(pay_amount*exchange) as payment, dl.os_type as os_type 
				from `{$this->db_payment}`.pay_order o
                join (select uid,device_id from login_device group by uid) ld on o.pay_user_id = ld.uid
				join device_list dl on dl.device_id = ld.device_id and dl.game_id = $game_id
				where o.get_payment = 1 and o.game_id = $game_id and o.pay_time between $start_time and $end_time group by date,os_type";

        if($interval == 3600){
			//安装数
			$sql0 = "select floor(dl.time/$interval) * {$interval} as date,".$sql0;
			//注册数
			$sql1 = "select floor(unix_timestamp(u.created_time)/$interval) *{$interval} as date,".$sql1;
			//创建角色数
			$sql2 = "select floor(cp.created_time/$interval) * {$interval} as date, ".$sql2;
			//付费数
			$sql3 = "select floor(o.pay_time/$interval) * {$interval} as date,".$sql3;
		}
		elseif($interval == 86400){
			//安装数
			$sql0 = "select unix_timestamp(from_unixtime(dl.time, '%Y-%m-%d')) as date,".$sql0;
			//注册数
			$sql1 = "select unix_timestamp(from_unixtime(unix_timestamp(u.created_time)/86400 *86400,'%Y-%m-%d')) as date,".$sql1;
			//创建角色数
			$sql2 = "select unix_timestamp(from_unixtime(cp.created_time, '%Y-%m-%d')) as date,".$sql2;
			//付费数
			$sql3 = "select unix_timestamp(from_unixtime(o.pay_time, '%Y-%m-%d')) as date,".$sql3;
		}
		elseif ($interval == 0) {
			$selcet_sql = "select $start_time as date, ";
			//安装数
			$sql0 = $selcet_sql.$sql0;
			//注册数
			$sql1 = $selcet_sql.$sql1;	
			//创建角色数
			$sql2 = $selcet_sql.$sql2;
			//付费数
			$sql3 = $selcet_sql.$sql3;
		}
		
		$tmp_result0 = $db_qiqiwu->select($sql0);
		$tmp_result1 = $db_qiqiwu->select($sql1);
		$tmp_result2 = $db_qiqiwu->select($sql2);
		$tmp_result3 = $db_qiqiwu->select($sql3);

		$tmp_result0 = (array)$tmp_result0; 
		$tmp_result1 = (array)$tmp_result1; 
		$tmp_result2 = (array)$tmp_result2; 
		$tmp_result3 = (array)$tmp_result3; 

		$result = array();
		$time_result = array();
		if($interval != 0){
			for ($i = $start_time; $i<$end_time ; $i+=$interval) { 
				$time_result[] = array(
					'date' => "$i",
					'os_type' => "android",
					);
				$time_result[] = array(
					'date' => "$i",
					'os_type' => "iOS",
					);
			}
		}
		else{
			$time_result[] = array(
				'date' => $start_time,
				'os_type' => 'android',
				);
			$time_result[] = array(
				'date' => $start_time,
				'os_type' => 'iOS',
				);
		}
		$tmp = array();
		foreach ($time_result as $key => $value) {
			$tmp[$key] = array(
					'date' => $value['date'],
					'usernum' => 0,
					'signupnum' => 0,
					'playernum' => 0,
					'payment' => 0,
					'os_type' => $value['os_type'],
					);
			foreach ($tmp_result0 as $key0 => $value0) {
				if ($value0->os_type == $value['os_type'] && $value0->date == $value['date']) {
					$tmp[$key]['usernum'] = $value0->usernum;
					unset($tmp_result0[$key0]);
					break;
				}
			}
			foreach ($tmp_result1 as $key1 => $value1) {
				if($value1->os_type == $value['os_type'] && $value1->date == $value['date']){
					$tmp[$key]['signupnum'] = $value1->signupnum;
					unset($tmp_result1[$key1]);
					break;
				}
			}
			foreach ($tmp_result2 as $key2 => $value2) {
				if($value2->os_type == $value['os_type'] && $value2->date == $value['date']){
					$tmp[$key]['playernum'] = $value2->playernum;
					unset($tmp_result2[$key2]);
					break;
				}
			}
			foreach ($tmp_result3 as $key3 => $value3) {
				if($value3->os_type == $value['os_type'] && $value3->date == $value['date']){
					$tmp[$key]['payment'] = $value3->payment;
					unset($tmp_result3[$key3]);
					break;
				}
			}
		}
		unset($time_result);
		foreach ($tmp as $key => $value) {
			if($value['usernum'] == 0 && $value['signupnum'] == 0 && $value['playernum'] == 0 && $value['payment'] == 0)
				unset($tmp[$key]);
		}
		// Log::info(var_export($tmp,true));

		//判断设备
        if ($check_type == 0) {
            //判断间隔
            if($interval == 0){
            	$tmp_end_date = date('Y-m-d H:i:s',$end_time);
            }else{
            	$tmp_end_date = date('Y-m-d H:i:s',$value['date']+$interval);
            }
            foreach ($tmp as $key => $value) {
            	$result[]=array(
                    'date' => date('Y-m-d H:i:s',$value['date'])."——".$tmp_end_date,
                    'usernum' => $value['usernum'],
                    'signupnum' => $value['signupnum'],
                    'playernum' => $value['playernum'],
                    'payment' => $value['payment'],
                    'os_type' => $value['os_type'],
                    );
            }
        }else{
            foreach ($tmp as $key => $value) {
                if($check_type == 1){
                	$check_os_type = "android";
                } elseif ($check_type == 2) {
                	$check_os_type = "iOS";
                }
                if(isset($check_os_type)){
                	if($value['os_type'] == $check_os_type){
                        if($interval == 0){
                        	$tmp_date = date('Y-m-d H:i:s',$end_time);
                        }else{
                        	$tmp_date = date('Y-m-d H:i:s',$value['date']+$interval);
                        }
                        $result[]=array(
                            'date' => date('Y-m-d H:i:s',$value['date'])."——".$tmp_date,
                            'usernum' => $value['usernum'],
                    		'signupnum' => $value['signupnum'],
                    		'playernum' => $value['playernum'],
                    		'payment' => $value['payment'],
                    		'os_type' => $value['os_type'],
                            );
                    }
                    unset($check_os_type);
                }
            }
        }   

		// Log::info(var_export($result,true));
		if (count($result)>0) {
			return Response::json($result);
		} else {
			return Response::json(array(), 404);
		}
	}

	public function getDevicePlayerInfo(){
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$game_id = Input::get('game_id');
		$interval = (int)Input::get('interval');
		$check_type = Input::get('check_type');
        $serach_type = (int)Input::get('serach_type');
        $channel_type = (int)Input::get('channel_type');
        $server_internal_id = Input::get('server_internal_id');
        // Log::info($this->server_internal_id);
        


        $db_qiqiwu = DB::connection($this->db_qiqiwu);

        //安装数
		$sql3 = "count(dl.device_id) as usernum, dl.os_type as os_type 
				from device_list dl
				where dl.game_id = $game_id and dl.time between $start_time and $end_time group by date,os_type";
		//注册数
		$sql4 = "count(u.uid) as signupnum, dl.os_type as os_type 
				from users u
                join login_device ld on u.uid = ld.uid and ld.game_id = $game_id
				join device_list dl on dl.device_id = ld.device_id and dl.game_id = $game_id
				where u.game_source = $game_id and u.created_time between from_unixtime($start_time,'%Y-%m-%d %H:%i:%s') and from_unixtime($end_time ,'%Y-%m-%d %H:%i:%s') group by date,os_type";

        if(in_array($game_id, Config::get('game_config.mobilegames'))){		
		//创建角色数
			$sql1 = "sl.server_name as server_name, count(cp.player_id) as playernum, dl.os_type as os_type 
					from `{$this->db_name}`.log_create_player cp
            	    join (select uid,device_id from login_device group by uid) ld on cp.uid = ld.uid
					join device_list dl on dl.device_id = ld.device_id and dl.game_id = $game_id 
					join server_list sl on sl.server_internal_id = {$this->server_internal_id} and sl.game_id = $game_id
					where cp.created_time between $start_time and $end_time group by date,server_name,os_type";
		}else{
			//创建角色数
			$sql1 = "sl.server_name as server_name, count(cp.player_id) as playernum, dl.os_type as os_type 
					from create_player cp
                	join (select uid,device_id from login_device group by uid) ld on cp.uid = ld.uid
					join device_list dl on dl.device_id = ld.device_id and dl.game_id = $game_id 
					join server_list sl on sl.server_internal_id = cp.server_id and sl.game_id = $game_id
					where cp.server_id = $server_internal_id and cp.game_id = $game_id and cp.created_time between $start_time and $end_time group by date,os_type";
		}

		//付费数
		$sql2 = "sl.server_name as server_name, sum(pay_amount*exchange) as payment, dl.os_type as os_type 
				from `{$this->db_payment}`.pay_order o
                join (select uid,device_id from login_device group by uid) ld on o.pay_user_id = ld.uid
				join device_list dl on dl.device_id = ld.device_id and dl.game_id = $game_id 
				join server_list sl on sl.server_id = o.server_id and sl.game_id = $game_id and sl.server_internal_id = $server_internal_id
				where o.get_payment=1 and o.game_id = $game_id and o.pay_time between $start_time and $end_time group by date,server_name,os_type";

		if($interval == 3600){
			//安装数
			$sql3 = "select floor(dl.time/$interval) * {$interval} as date,".$sql3;
			//注册数
			$sql4 = "select floor(unix_timestamp(u.created_time)/$interval) *{$interval} as date,".$sql4;
			//创建角色数
			$sql1 = "select floor(cp.created_time/$interval) * {$interval} as date, ".$sql1;
			//付费数
			$sql2 = "select floor(o.pay_time/$interval) * {$interval} as date,".$sql2;
		}
		elseif($interval == 86400){
			$sql3 = "select unix_timestamp(from_unixtime(dl.time, '%Y-%m-%d')) as date,".$sql3;
			//注册数
			$sql4 = "select unix_timestamp(from_unixtime(unix_timestamp(u.created_time)/86400 *86400,'%Y-%m-%d')) as date,".$sql4;
			//创建角色数
			$sql1 = "select unix_timestamp(from_unixtime(cp.created_time, '%Y-%m-%d')) as date,".$sql1;
			//付费数
			$sql2 = "select unix_timestamp(from_unixtime(o.pay_time, '%Y-%m-%d')) as date,".$sql2;
		}
		elseif ($interval == 0) {
			$selcet_sql = "select $start_time as date, ";
			$sql3 = $selcet_sql.$sql3;
			$sql4 = $selcet_sql.$sql4;
			$sql1 = $selcet_sql.$sql1;
			$sql2 = $selcet_sql.$sql2;
		}
		
		$tmp_result3 = $db_qiqiwu->select($sql3);
		$tmp_result4 = $db_qiqiwu->select($sql4);
		$tmp_result1 = $db_qiqiwu->select($sql1);
		$tmp_result2 = $db_qiqiwu->select($sql2);
		$result = array();
		$time_result = array();
		if($interval != 0){
			for ($i = $start_time; $i<$end_time ; $i+=$interval) { 
				$time_result[] = array(
					'date' => "$i",
					'os_type' => "android",
					);
				$time_result[] = array(
					'date' => "$i",
					'os_type' => "iOS",
					);
			}
		}
		else{
			$time_result[] = array(
				'date' => $start_time,
				'os_type' => 'android',
				);
			$time_result[] = array(
				'date' => $start_time,
				'os_type' => 'iOS',
				);
		}
		$tmp = array();
		foreach ($time_result as $key => $value) {
			$tmp[$key] = array(
					'server_name' => 0,
					'date' => $value['date'],
					'usernum' => 0,
					'signupnum' => 0,
					'playernum' => 0,
					'payment' => 0,
					'os_type' => $value['os_type'],
					);
			
			foreach ($tmp_result1 as $key1 => $value1) {
				if($value1->os_type == $value['os_type'] && $value1->date == $value['date']){
					$tmp[$key]['playernum'] = $value1->playernum;
					$tmp[$key]['server_name'] = $value1->server_name;
					unset($tmp_result1[$key1]);
					break;
				}
			}
			foreach ($tmp_result2 as $key2 => $value2) {
				if($value2->os_type == $value['os_type'] && $value2->date == $value['date']){
					if($tmp[$key]['server_name'] == 0){
						$tmp[$key]['server_name'] = $value2->server_name;
					}
					$tmp[$key]['payment'] = $value2->payment;
						unset($tmp_result2[$key2]);
					break;
				}
			}
			foreach ($tmp_result3 as $key3 => $value3) {
				if($value3->os_type == $value['os_type'] && $value3->date == $value['date']){
					$tmp[$key]['usernum'] = $value3->usernum;
						unset($tmp_result3[$key3]);
					break;
				}
			}
			foreach ($tmp_result4 as $key4 => $value4) {
				if($value4->os_type == $value['os_type'] && $value4->date == $value['date']){
					$tmp[$key]['signupnum'] = $value4->signupnum;
						unset($tmp_result4[$key4]);
					break;
				}
			}
		}
		unset($time_result);
		foreach ($tmp as $key => $value) {
			if($value['playernum'] == 0 && $value['payment'] == 0 && $value['usernum'] == 0 && $value['signupnum'] == 0)
				unset($tmp[$key]);
		}


		if ($check_type == 0) {
            //判断间隔
            if($interval == 0){
                foreach ($tmp as $key => $value) {
            	$result[] = array(
            		'server_name' => $value['server_name'],
                    'date' => date('Y-m-d H:i:s',$value['date'])."——".date('Y-m-d H:i:s',$end_time),
                    'usernum' => $value['usernum'],
                    'signupnum' => $value['signupnum'],
                    'playernum' => $value['playernum'],
                    'payment' => $value['payment'],
                    'os_type' => $value['os_type'],
            		);
            	}
            }
            else{
                foreach ($tmp as $key => $value) {
            	$result[] = array(
            		'server_name' => $value['server_name'],
                    'date' => $value['date'] = date('Y-m-d H:i:s',$value['date'])."——".date('Y-m-d H:i:s',$value['date']+$interval),
                    'usernum' => $value['usernum'],
                    'signupnum' => $value['signupnum'],
                    'playernum' => $value['playernum'],
                    'payment' => $value['payment'],
                    'os_type' => $value['os_type'],
            		);
            	}
            }
        }
        else{
            foreach ($tmp as $key => $value) {
                if($check_type == 1){
                    if($value['os_type'] == "android"){
                        if($interval == 0){
                            $result[]=array(
                            	'server_name' => $value['server_name'],
                                'date' => date('Y-m-d H:i:s',$value['date'])."——".date('Y-m-d H:i:s',$end_time),
                                'usernum' => $value['usernum'],
                    			'signupnum' => $value['signupnum'],
                                'playernum' => $value['playernum'],
			                    'payment' => $value['payment'],
			                    'os_type' => $value['os_type'],
                                );
                        }
                        else{
                            $result[]=array(
                            	'server_name' => $value['server_name'],
                                'date' => date('Y-m-d H:i:s',$value['date'])."——".date('Y-m-d H:i:s',$value['date']+$interval),
                                'usernum' => $value['usernum'],
                    			'signupnum' => $value['signupnum'],
                                'playernum' => $value['playernum'],
                    			'payment' => $value['payment'],
                    			'os_type' => $value['os_type'],
                                );
                        }
                    }
                }
                elseif ($check_type == 2) {
                    if($value['os_type'] == "iOS"){
                        if($interval == 0){
                            $result[]=array(
                            	'server_name' => $value['server_name'],
                                'date' => date('Y-m-d H:i:s',$value['date'])."——".date('Y-m-d H:i:s',$end_time),
                                'usernum' => $value['usernum'],
                    			'signupnum' => $value['signupnum'],
                                'playernum' => $value['playernum'],
                   			 	'payment' => $value['payment'],
                    			'os_type' => $value['os_type'],
                                );
                        }
                        else{
                            $result[]=array(
                            	'server_name' => $value['server_name'],
                                'date' => date('Y-m-d H:i:s',$value['date'])."——".date('Y-m-d H:i:s',$value['date']+$interval),
                                'usernum' => $value['usernum'],
                    			'signupnum' => $value['signupnum'],
                                'playernum' => $value['playernum'],
                    			'payment' => $value['payment'],
                    			'os_type' => $value['os_type'],
                                );
                        }
                    }
                }
            }
        }   
		// Log::info(var_export($result,true));
		if(count($result)>0){
			return Response::json($result);
		}else {
			return Response::json(array(), 404);
		}

	}

	public function CountUserStatSignup(){	//获取注册用户统计的注册部分数据
		$msg = array(
			'code' => Config::get('errorcode.slave_user'),
			'error' => ''
		);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');	
		$interval = (int)Input::get('interval');
		$filter = Input::get('filter');
		$source = Input::get('source');
		$u1 = Input::get('u1');
		$u2 = Input::get('u2');
		$game_id = (int)Input::get('game_id');

		$result = SlaveUser::on($this->db_qiqiwu)
				->getUserStatSignup($start_time, $end_time, $interval, $filter, $source, $u1, $u2);
		
		$test = SlaveUser::on($this->db_qiqiwu)->first();
		if(isset($test->game_source)){
			$result->where('u.game_source', $game_id);
		}elseif(isset($test->game_id)){
			$result->where('u.game_id', $game_id);
		}
		unset($test);

		$result = $result->get();

		if($result){
			return Response::json($result);
		}else {
			return Response::json(array(), 404);
		}
	}

	public function CountUserStatCreateplayer(){	//获取注册用户统计的创建数据
		$msg = array(
			'code' => Config::get('errorcode.slave_user'),
			'error' => ''
		);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');	
		$interval = (int)Input::get('interval');
		$filter = Input::get('filter');
		$source = Input::get('source');
		$u1 = Input::get('u1');
		$u2 = Input::get('u2');
		$game_id = (int)Input::get('game_id');	
		$server_internal_id = (int)Input::get('server_internal_id');

		$result = SlaveUser::on($this->db_qiqiwu)
				->getUserStatCreateplayer($game_id, $server_internal_id, $start_time, $end_time, $interval, $filter, $source, $u1, $u2);

		$test = SlaveUser::on($this->db_qiqiwu)->first();
		if(isset($test->game_source)){
			$result->where('u.game_source', $game_id);
		}elseif(isset($test->game_id)){
			$result->where('u.game_id', $game_id);
		}
		unset($test);

		if(!in_array($game_id, Config::get('game_config.yysggameids'))){
			$test = SlaveCreatePlayer::on($this->db_qiqiwu)->first();
			if(isset($test->game_id)){
				$result->where('cp.game_id', $game_id);
			}
			unset($test);
		}

		$result = $result->get();

		if($result){
			return Response::json($result);
		}else {
			return Response::json(array(), 404);
		}
	}

	public function CountUserStatLevelten(){	//获取注册用户统计的十级数据
		$msg = array(
			'code' => Config::get('errorcode.slave_user'),
			'error' => ''
		);

		$server_internal_id = (int)Input::get('server_internal_id');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');	
		$interval = (int)Input::get('interval');
		$filter = Input::get('filter');
		$source = Input::get('source');
		$u1 = Input::get('u1');
		$u2 = Input::get('u2');
		$game_id = (int)Input::get('game_id');

		$result = LevelUpLog::on($this->db_name)
				->getUserStatUserStatLevelten($this->db_qiqiwu, $game_id, $server_internal_id, $start_time, $end_time, $interval, $filter, $source, $u1, $u2);

		$test = SlaveUser::on($this->db_qiqiwu)->first();
		if(isset($test->game_source)){
			$result->where('u.game_source', $game_id);
		}elseif(isset($test->game_id)){
			$result->where('u.game_id', $game_id);
		}

		$result = $result->get();

		if($result){
			return Response::json($result);
		}else {
			return Response::json(array(), 404);
		}
	}

    public function getBasicCount(){
	    $start_time = (int)Input::get('start_time');
	    $end_time = (int)Input::get('end_time');
	    $game_id = (int)Input::get('game_id');
	    $interval = (int)Input::get('interval');
	    $currency_id = (int)Input::get('currency_id');
	    $users_desc = SlaveUser::on($this->db_qiqiwu)->first();
	    if(isset($users_desc->game_source)){
	    	$is_game_source = 1;
	    }else{
	    	$is_game_source = 0;
	    }
	    $result = array();
	    if(0 == $interval){//安天查询
	        $inter = ($end_time - $start_time)/86400;
	        $days = intval(ceil($inter));
	        for ($i=0; $i < $days; $i++) {
	            $start_time1 = date("Y-m-d 00:00:00", $start_time);
	            $start_time2 = strtotime($start_time1);
	            $start_time2 = $start_time2 + 86400*$i;
	            $this_start_time = strtotime(date("Y-m-d", $start_time2));
	            $this_end_time = $start_time2+86399;
	            if(0 == $i){
	            	$this_start_time = $start_time;
	            }
	            if($days-1 == $i){
	            	$this_end_time = $end_time;
	            }
		        $res = $this->basicCount($this_start_time, $this_end_time, $game_id, $interval, $is_game_source);
	        	if(!empty($res)){
	        		$result[] = $res;
	        	}
	        }
	    }elseif(1 == $interval){//周
	    	$w_day=date("w",$start_time);
	    	if($w_day=='1'){
	    	$cflag = '+0';
	    	//$lflag = '-1';
	    	}else{
	    	$cflag = '-1';
	    	//$lflag = '-2';
	    	}
	       $weekstart = strtotime(date('Y-m-d',strtotime("$cflag week Monday", $start_time)));  //取得开始时间所在自然周的开始时间 
	       $inter = ($end_time - $weekstart)/(86400*7);
	       $weeks = intval(ceil($inter));
	       for($i=0; $i<$weeks; $i++){
	           $this_start_time = $weekstart + (86400*7)*$i;
	           $this_end_time = $this_start_time+(86400*7-1); 
	           if(0 == $i){
	            	$this_start_time = $start_time;
	           }
	           if($weeks-1 == $i){
	            	$this_end_time = $end_time;
	           }
	           $res = $this->basicCount($this_start_time, $this_end_time, $game_id, $interval, $is_game_source);
	      	   if(!empty($res)){
	      	   		$result[] = $res;
	      	   }
	       }
	    }elseif(2 == $interval){//月
	    	$firstdate = date("Y/m/01",$start_time);
	    	$firstday = strtotime(date("Y/m/01",$start_time));//start_time所在月第一天
	    	$this_start_time = $firstday;
	    	$this_end_time = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdate +1 month -1 day")));//start_time所在月最后一天
	    	for($this_start_time; $this_start_time<$end_time; $this_start_time++){
	           $firstdate = date("Y/m/01",$this_start_time);
	           $this_end_time = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdate +1 month -1 day")));
	           if($start_time > $this_start_time){//第一个月开始查询时间
	            	$this_start_time = $start_time;
	           }
	           if($end_time < $this_end_time){//最后一个月的结束时间
	            	$this_end_time = $end_time;
	           }

	           $res = $this->basicCount($this_start_time, $this_end_time, $game_id, $interval, $is_game_source);
	           if(!empty($res)){
	           		$result[] = $res;
	           }

	           $this_start_time = $this_end_time+1;//下个月1号0点
	        }
	    }elseif(3 == $interval){//间隔整个事件段
	        $res = $this->basicCount($start_time, $end_time, $game_id, $interval, $is_game_source);
	    	if(!empty($res)){
	    		$result[] = $res;
	    	}
	    }
   //Log::info(var_export($result,true));die();
		if($result){
			return Response::json($result);
		}else{
			
		}
    }
    private function basicCount($this_start_time, $this_end_time, $game_id, $interval, $is_game_source){
    	$temp_result = array();
    	unset($temp_result);
    	$create_player_single = SlaveCreatePlayer::on($this->db_qiqiwu)->first();	//用来测试这个平台的create_player表中是否含有game_id字段
    	if(isset($create_player_single->game_id)){
    		$create_player_single = 1;
    	}else{
    		$create_player_single = 0;
    	}
        $temp_result1 = SlaveUser::on($this->db_qiqiwu)
            ->getBasicCount($this_start_time, $this_end_time, $game_id, $this->db_name, $is_game_source, $create_player_single)->get();

        if(in_array($game_id, Config::get('game_config.yysggameids'))){
        	$temp_result2 = DB::connection($this->db_name)->table(DB::raw('log_create_player as lcp'))
        	->whereBetween('created_time',array($this_start_time,$this_end_time))
        	->selectRaw('count(distinct player_id) as create_player')
        	->get();
        }else{
        	$temp_result2 = DB::connection($this->db_qiqiwu)->table(DB::raw('create_player as lcp'))
        	->whereBetween('created_time', array($this_start_time, $this_end_time))
        	->selectRaw('count(distinct player_id) as create_player');
        	if($create_player_single){
        		$temp_result2 = $temp_result2->where('game_id', $game_id)->get();
        	}else{
        		$temp_result2 = $temp_result2->get();
        	}
        }

        $temp_result3 = SlaveUser::on($this->db_qiqiwu)
        	->leftJoin("{$this->db_payment}.pay_order as o",function($join) use($game_id,$this_start_time,$this_end_time){
        		$join->on('o.pay_user_id','=','u.uid')
        		->where('o.game_id','=',$game_id)
        		->where('o.pay_time','>=',$this_start_time)
        		->where('o.pay_time','<=',$this_end_time)
        		->where('o.get_payment','=',1);
        	});
        	if(1 == $is_game_source){
        		$temp_result3->where('u.game_source','=',$game_id);
        	}
        	$temp_result3->whereBetween('u.created_time',array(date("Y-m-d H:i:s",$this_start_time),date("Y-m-d H:i:s",$this_end_time)))
        	->selectRaw('count(distinct o.pay_user_id) as reg_pay_user');
        $temp_result3 = $temp_result3->get();

        $temp_result = array();
        $temp_result1 = json_decode($temp_result1,true);
        $temp_result = $temp_result1[0];
        $temp_result['create_player'] = $temp_result2[0]->create_player;
        $temp_result['reg_pay_user'] = $temp_result3[0]->reg_pay_user;

        $temp_sum = $temp_result;
        unset($temp_sum['time']);
        if(0 == array_sum($temp_sum)){
        	return array();
        }
        
        if(0 == $interval){
            $temp_result['title'] = $temp_result1[0]['time'];
        }
        elseif(1 == $interval){
            $temp_result['title'] = $temp_result1[0]['time'].Lang::get('slave.where_week');
        }elseif(2 == $interval){
            $temp_result['title'] = date('Y-m',strtotime($temp_result1[0]['time']));
        }elseif(3 == $interval){
            $temp_result['title'] = 'All';
        }
        unset($temp_result1);
        unset($temp_result2);
        unset($temp_result3);
        unset($temp_sum);
        return $temp_result;
    }

    public function getUidbyPlayerInfo(){
    	$game_id = (int)Input::get('game_id');
    	$server_internal_id = (int)Input::get('server_internal_id');
    	$player_id = (int)Input::get('player_id');
    	$player_name = Input::get('player_name');

    	$uid = CreatePlayerLog::on($this->db_name);
    	if($player_id){
    		$uid = $uid->where('player_id', $player_id);
    	}elseif($player_name){
    		$uid = $uid->whereRaw("binary player_name = '$player_name'");
    	}

    	if(in_array($game_id, Config::get('game_config.mobilegames'))){
    		$uid = $uid->selectRaw("uid");
    	}else{
    		$uid = $uid->selectRaw("user_id as uid");
    	}

    	$uid = $uid->first();
    	if($uid){
    		return Response::json($uid);
    	}else{
    		unset($uid);
    		$uid = SlaveCreatePlayer::on($this->db_qiqiwu)
    			->where('game_id', $game_id)
    			->where('server_id', $server_internal_id);

    		if($player_id){
	    		$uid = $uid->where('player_id', $player_id);
	    	}elseif($player_name){
	    		$uid = $uid->whereRaw("binary player_name = '$player_name'");
	    	}

	    	$uid = $uid->selectRaw("uid");
	    	$uid = $uid->first();
	    	if($uid){
    			return Response::json($uid);
    		}else{
    			return Response::json(array(), 404);
    		}
    	}
    }

    public function getSetupStat(){
    	$msg = array(
			'code' => Config::get('errorcode.slave_user'),
			'error' => ''
		);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');	
		$interval = (int)Input::get('interval');
		$filter = Input::get('filter');
		$source = Input::get('source');
		$u1 = Input::get('u1');
		$u2 = Input::get('u2');
		$game_id = (int)Input::get('game_id');
		$server_internal_id = (int)Input::get('server_internal_id');
		$os_type = Input::get('os_type');

		$test = DB::connection($this->db_qiqiwu)->table('create_player')->first();
		if(isset($test->game_id)){
			$has_game_id = 1;
		}else{
			$has_game_id = 0;
		}

		$result = DeviceList::on($this->db_qiqiwu)
					->getSetupStat($start_time, $end_time, $interval, $filter, $source, $u1, $u2, $os_type, $game_id, $server_internal_id, $this->db_name, $has_game_id)
					->get();

		if($result){
			return Response::json($result); 
		}else{
			return Response::json(array(), 404);
		}
    }

    public function getWeeklyChannelStat(){	//统计一周的channel信息，包括创建量和充值量
    	$game_id = Input::get('game_id');
    	$cre_start_time = (int)Input::get('cre_start_time');
    	$cre_end_time = (int)Input::get('cre_end_time');
    	$channle_order_start_time = (int)Input::get('channle_order_start_time');
    	$channle_order_end_time = (int)Input::get('channle_order_end_time');
    	$result = array();

    	$create_result = SlaveCreatePlayer::on($this->db_qiqiwu)
    						->ChannelCreatePlayer($cre_start_time, $cre_end_time, $game_id)
    						->get();

    	foreach ($create_result as $value) {
    		$result[$value->channel] = array(
    			'channel' => $value->channel,
    			'create_player_num' => $value->num,
    			'pay_num' => 0,
    			'pay_dollar' => 0,
    			);
    	}

    	unset($create_result);
    	unset($value);

    	$pay_result = PayOrder::on($this->db_payment)
				->ChannelPayOrder($this->db_qiqiwu,$cre_start_time,
                    $cre_end_time, $channle_order_start_time, $channle_order_end_time, $game_id)
				->get();

		foreach ($pay_result as $value) {
			if($value->channel){
				if(isset($result[$value->channel])){
					$result[$value->channel]['pay_num'] = $value->pay_num;
					$result[$value->channel]['pay_dollar'] = $value->pay_dollar;
				}else{
					$result[$value->channel] = array(
		    			'channel' => $value->channel,
		    			'create_player_num' => 0,
		    			'pay_num' => $value->pay_num,
		    			'pay_dollar' => $value->pay_dollar,
    				);
				}			
			}
		}

		if(count($result)){
			return Response::json($result); 
		}else{
			return Response::json(array(), 404);
		}
    }

   public function WEBwriteOnlineNum(){	//在日志库写入玩家在线数量
		$msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );

		$time = floor(time()/600)*600;
		$game_id = (int)Input::get('game_id');
		$server_internal_id = (int)Input::get('server_internal_id');
		$all_server_num = Input::get('all_server_num');
		
		$db_name = DB::connection($this->db_name);
		Log::info($game_id . ' insert online value ');
		foreach ($all_server_num as $value) {
			$server = $game_id.'.'.$value['server_internal_id'];
			$online_value = $value['num'];
			if(11 == $game_id){
				$time = $value['largest_time'];
			}
			$online_time= $db_name->select("select `online_time` from `{$server}`.log_online where `online_time`={$time} limit 1");
			if(count($online_time)>0){
				continue;
			}
			//Log::info($server . ' online value ' . $online_value);
			try{
				$db_name->insert("insert into `{$server}`.log_online (`online_time`, `online_value`) values ({$time}, {$online_value})");
			}catch(\Exception $e){
				Log::error($e);
			}
		}
		DB::disconnect($this->db_name);

		return Response::json(1); 
   }

    public function getDataByDeviceids(){
        $game_id = Input::get('game_id');
        $platform_id = Input::get('platform_id');
        $data_type = Input::get('data_type');
        $device_ids = Input::get('device_ids');
        foreach ($device_ids as $key => $value) {
        	$device_ids[$key] = "'".$value."'";
        }
        $result = array();

        if('create' == $data_type){
        	$device_ids_str = implode(',', $device_ids);
            $db = DB::connection($this->db_qiqiwu);
            $result = $db->table(DB::raw("(select distinct uid from login_device where device_id in ($device_ids_str)) as tmp"))
                ->leftJoin('create_player as cp', function($join) use ($game_id){
                	$join->on('cp.uid', '=', 'tmp.uid');
                	if(!in_array($game_id, array(8, 36, 41, 43, 44, 45, 70))){
		                $join->where('cp.game_id', '=', $game_id);
		            }
                })
                ->selectRaw('count(distinct tmp.uid) as user_num, count(distinct cp.player_id) as player_num')
                ->get();	
        }

        if('level' == $data_type){
        	if(in_array($game_id, Config::get('game_config.mobilegames'))){
        		$levelkey = 'lev';
        	}else{
        		$levelkey = 'new_level';
        	}
        	$device_ids_str = implode(',', $device_ids);
            $db = DB::connection($this->db_qiqiwu);
            $result = $db->table(DB::raw("(select distinct uid from login_device where device_id in ($device_ids_str)) as tmp"))
                ->Join('create_player as cp', function($join) use ($game_id){
                	$join->on('cp.uid', '=', 'tmp.uid');
                	if(!in_array($game_id, array(8, 36, 41, 43, 44, 45, 70))){
		                $join->where('cp.game_id', '=', $game_id);
		            }
                })
                ->leftJoin(DB::raw("`{$this->db_name}`.log_levelup as ll"), 'cp.player_id', '=', 'll.player_id')
                ->groupBy('cp.player_id')
                ->selectRaw('cp.player_id, ifnull(max(ll.'.$levelkey.'), 1) as max_level')
                ->get();
            if(count($result)){
            	$tmp = array();
            	foreach ($result as $value) {
            		if(isset($value->max_level) && $value->max_level){
            			if(isset($tmp[$value->max_level])){
            				$tmp[$value->max_level]++;
            			}else{
            				$tmp[$value->max_level] = 1;
            			}
            		}
            	}
            	unset($result);
            	$result = array();

            	foreach ($tmp as $key => $value) {
            		$result[$key] = array(
            			'level' => $key,
            			'player_num' => $value,
            			);
            	}
            }
        }

        if('order' == $data_type){
        	$device_ids_str = implode(',', $device_ids);
            $db = DB::connection($this->db_payment);
            $result = $db->table('pay_order as o')
                        ->join(DB::raw("(select distinct uid from `$this->db_qiqiwu`.login_device where device_id in ($device_ids_str)) as tmp"), 'tmp.uid', '=', 'o.pay_user_id')
                        ->where('o.game_id', $game_id)
                        ->where('o.get_payment', 1)
                        ->selectRaw('count(distinct pay_user_id) as pay_user_num, count(1) as pay_order_num, sum(pay_amount*exchange) as pay_dollar')
                        ->get();
        }

        if(count($result)){
			return Response::json($result); 
		}else{
			return Response::json(array(), 404);
		}
   }

   public function getWeeklySetup(){
   		$game_id = Input::get('game_id');
   		$platform_id = Input::get('platform_id');
   		$start_time = (int)Input::get('start_time');
   		$end_time = (int)Input::get('end_time');
   		$filter_u1 = Input::get('filter_u1');
		$result = SlaveUserDevice::on($this->db_qiqiwu)
			->weeklyDeviceStat($start_time, $end_time, $game_id, $filter_u1)
			->get();
		return Response::json($result); 
   }

   public function getSignupCreateInfo(){	//获取注册，创建以及一定的充值信息，因为语句简单，因此直接写在这里
   		$game_id = Input::get('game_id');
   		$platform_id = Input::get('platform_id');
   		$start_time = (int)Input::get('start_time');
   		$end_time = (int)Input::get('end_time');

   		$result = array(
   			'payment' => array(),
   			);

   		$single_user = SlaveUser::on($this->db_qiqiwu)->first();

		$result['all_sign'] = SlaveUser::on($this->db_qiqiwu)
		                        ->where('created_time', '<', date("Y-m-d H:i:s", $end_time));
		$result['single_day_sign'] = SlaveUser::on($this->db_qiqiwu)
		                        ->whereBetween('created_time', array(date("Y-m-d H:i:s", $start_time), date("Y-m-d H:i:s", $end_time)));    
		$result['payment']['single_day_sum_dollar'] = SlaveUser::on($this->db_qiqiwu)
								->join("{$this->db_payment}.pay_order as o", 'u.uid', '=', 'o.pay_user_id')
		                        ->whereBetween('u.created_time', array(date("Y-m-d H:i:s", $start_time), date("Y-m-d H:i:s", ($end_time))))
		                        ->where('o.game_id', $game_id)
		                        ->where('o.get_payment', 1)
		                        ->selectRaw("count(distinct o.pay_user_id) as user_num, sum(o.pay_amount*exchange) as all_dollar");
		$result['payment']['single_day_sign_pay_sum_dollar'] = SlaveUser::on($this->db_qiqiwu)
								->join("{$this->db_payment}.pay_order as o", 'u.uid', '=', 'o.pay_user_id')
		                        ->whereBetween('u.created_time', array(date("Y-m-d H:i:s", $start_time), date("Y-m-d H:i:s", ($end_time))))
		                        ->whereBetween('o.pay_time', array($start_time, $end_time))
		                        ->where('o.game_id', $game_id)
		                        ->where('o.get_payment', 1)
		                        ->selectRaw("count(distinct o.pay_user_id) as user_num, sum(o.pay_amount*exchange) as all_dollar");
		$result['payment']['before_7_sum_dollar'] = SlaveUser::on($this->db_qiqiwu)
								->join("{$this->db_payment}.pay_order as o", 'u.uid', '=', 'o.pay_user_id')
		                        ->whereBetween('u.created_time', array(date("Y-m-d H:i:s", ($start_time-86400*7)), date("Y-m-d H:i:s", ($end_time-86400*7))))
		                        ->where('o.game_id', $game_id)
		                        ->where('o.get_payment', 1)
		                        ->selectRaw("count(distinct o.pay_user_id) as user_num, sum(o.pay_amount*exchange) as all_dollar");
		$result['payment']['before_30_sum_dollar'] = SlaveUser::on($this->db_qiqiwu)
								->join("{$this->db_payment}.pay_order as o", 'u.uid', '=', 'o.pay_user_id')
		                        ->whereBetween('u.created_time', array(date("Y-m-d H:i:s", ($start_time-86400*30)), date("Y-m-d H:i:s", ($end_time-86400*30))))
		                        ->where('o.game_id', $game_id)
		                        ->where('o.get_payment', 1)
		                        ->selectRaw("count(distinct o.pay_user_id) as user_num, sum(o.pay_amount*exchange) as all_dollar");
   		if(isset($single_user->game_source)){
   			$result['all_sign'] = $result['all_sign']->where('u.game_source', $game_id)->count();
   			$result['single_day_sign'] = $result['single_day_sign']->where('u.game_source', $game_id)->count();
   			foreach ($result['payment'] as $key => $value) {
   				$tmp = $result['payment'][$key]->where('u.game_source', $game_id)->first();
   				$result['payment'][$key] = array(
   					'user_num' => $tmp->user_num,
   					'dollar' => $tmp->all_dollar,
   					);
   				unset($tmp);
   			}
   		}elseif(isset($single_user->game_id)){	
   			$result['all_sign'] = $result['all_sign']->where('u.game_id', $game_id)->count();
   			$result['single_day_sign'] = $result['single_day_sign']->where('u.game_id', $game_id)->count();
   			foreach ($result['payment'] as $key => $value) {
   				$tmp = $result['payment'][$key]->where('u.game_id', $game_id)->first();
   				$result['payment'][$key] = array(
   					'user_num' => $tmp->user_num,
   					'dollar' => $tmp->all_dollar,
   					);
   				unset($tmp);
   			}
   		}else{
   			$result['all_sign'] = $result['all_sign']->count();
   			$result['single_day_sign'] = $result['single_day_sign']->count();
   			foreach ($result['payment'] as $key => $value) {
   				$tmp = $result['payment'][$key]->first();
   				$result['payment'][$key] = array(
   					'user_num' => $tmp->user_num,
   					'dollar' => $tmp->all_dollar,
   					);
   				unset($tmp);
   			}
   		}

   		unset($single_user);

   		$single_player = SlaveCreatePlayer::on($this->db_qiqiwu)->first();

   		if(isset($single_player->game_id)){
   			$result['all_create'] = SlaveCreatePlayer::on($this->db_qiqiwu)
   			                        ->where('created_time', '<', $end_time)
   			                        ->where('game_id', $game_id)
   			                        ->count();
   			$result['single_day_create'] = SlaveCreatePlayer::on($this->db_qiqiwu)
   			                        ->whereBetween('created_time', array($start_time, $end_time))
   			                        ->where('game_id', $game_id)
   			                        ->count();
   		}else{
   			$result['all_create'] = SlaveCreatePlayer::on($this->db_qiqiwu)
   			                        ->where('created_time', '<', $end_time)
   			                        ->count();
   			$result['single_day_create'] = SlaveCreatePlayer::on($this->db_qiqiwu)
   			                        ->whereBetween('created_time', array($start_time, $end_time))
   			                        ->count();
   		}

   		unset($single_player);

   		try{
   			$single_device = DeviceList::on($this->db_qiqiwu)->first();
   			$has_device_list = 1;
   		}catch(Exception $e){
   			$has_device_list = 0;
   		}

   		if($has_device_list){
    		$result['all_device'] = DeviceList::on($this->db_qiqiwu)
    								->where('game_id', $game_id)
   			                        ->where('time', '<', $end_time)
   			                        ->count();
   			$result['single_day_device'] = DeviceList::on($this->db_qiqiwu)
   									->where('game_id', $game_id)
   			                        ->whereBetween('time', array($start_time, $end_time))
   			                        ->count();  			
   		}else{
   			$result['all_device'] = 0;
   			$result['single_day_device'] = 0;  			
   		}

   		return Response::json($result); 
   	}

   	public function getCommandTest()
   	{
		$db = DB::connection($this->db_name);
		/*$result = $db->table('log_levelup as v')
		->join('log_login as l',function($join){
			$join->on('l.player_id','=','v.player_id');
		})
		->where('v.lev','=',32)
		->groupBy('v.player_id')
		->having('last_time','<','UNIX_TIMESTAMP(now())-3*86400')
		->selectRaw('v.player_id,MAX(l.action_time) as last_time')
		->get();*/
		$result = $db->select("select v.player_id,from_unixtime(max(l.action_time)) as last_time from 
			log_levelup v
			join log_login l on l.player_id=v.player_id
			where v.lev=32
			group by v.player_id
			having max(l.action_time)<unix_timestamp(now())-3*86400");
		if(count($result)){
			return Response::json($result); 
		}else{
			return Response::json(array(),404);
		}
   	}

}