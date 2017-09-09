<?php 

class CreatePlayerLog extends Eloquent {

	protected $table = 'log_create_player AS p';

	protected $primaryKey = 'log_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeLevelRank($query)
	{
		return $query->leftJoin('log_levelup as le', 'le.player_id', '=', 'p.player_id')
			->selectRaw('
			IFNULL(MAX(le.new_level), 1) as level, 
			IFNULL(MAX(le.levelup_time), created_time) as levelup_time, 
			player_name, 
			p.player_id')
			->groupBy('p.player_id')
			->orderBy('level', 'DESC')
			->orderBy('le.levelup_time');
	}


	public function scopeCreatedNumByTenMinute($query, $start_time, $end_time)
	{
		$interval = 600;
		return $query->selectRaw("
			FLOOR(created_time/{$interval}) * {$interval} as time,
			COUNT(player_id) as count")
			->where('created_time', '>=', $start_time)
			->where('created_time', '<=', $end_time)
			->groupBy(DB::raw("FLOOR(created_time/{$interval})"));
	}

	public function scopeCreatedNumByHour($query, $start_time, $end_time)
	{
		return $query->selectRaw('
			UNIX_TIMESTAMP(FROM_UNIXTIME(created_time, \'%Y-%m-%d %H:00:00\')) as time, 
			COUNT(player_id) as count')
			->where('created_time', '>=', $start_time)
			->where('created_time', '<=', $end_time)
			->groupBy('time');
	}

	public function scopeCreatedNumByDay($query, $start_time, $end_time) 
	{
		return $query->selectRaw('UNIX_TIMESTAMP(FROM_UNIXTIME(created_time, \'%Y-%m-%d\')) as time, 
			COUNT(player_id) as count')
			->where('created_time', '>=', $start_time)
			->where('created_time', '<=', $end_time)
			->groupBy('time');
	}

	public function scopeRetentionCreatePlayer($query, $db_qiqiwu, $retention_time, $is_anonymous, $game_id)
	{
		if(in_array($game_id, Config::get('game_config.mobilegames'))){//手游不区分匿名和不匿名，is_anonymous传过来为9
			$query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'p.uid')
			->where('p.created_time', '>=', $retention_time)
			->where('p.created_time', '<=', $retention_time + 86399)
			->select('player_id');
		}else{
			$query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'p.user_id')
			->where('p.created_time', '>=', $retention_time)
			->where('p.created_time', '<=', $retention_time + 86399)
			->where('u.is_anonymous', $is_anonymous)
			->select('player_id');
		}
			return $query;
	}

	public function scopeGetCreatePlayerInfo($query, $uid, $player_id, $player_name, $game_id)
	{
		if($uid){
			if(in_array($game_id, Config::get('game_config.mobilegames'))){
				return $query->where('uid', $uid);
			}else{
				return $query->where('user_id', $uid);
			}	
		}
		if($player_id){
			return $query->where('player_id', $player_id);
		}
		if($player_name){
			return $query->whereRaw("binary player_name = '$player_name'");
		}
		
	}

	public function scopeGetPokerUserActivate($query, $start_time, $end_time)
	{
		$query->leftJoin("{$db_qiqiwu}.users u ", "uid", "=", "p.uid")
		->where('u.last_visit_time', '>=', $start_time)
		->where('u.last_visit_time', '<=', $end_time)
		->where('p.activate_time', '>' ,0)
		->count();
		return $query;
	}

	public function scopeGetUser($query, $db_qiqiwu, $game_id, $server_internal_id, $player_id, $player_name, $tp_code='fb', $mergeserver = 0)
	{
		$app_sql = '';
        try {
            $app_sql = DB::connection($db_qiqiwu)->table('tp_applications')->where('game_id', $game_id)->where('tp_code', $tp_code)->get();
        } catch (\Exception $e) {
    		try {
	            $app_sql = DB::connection($db_qiqiwu)->table('tp_applications')->where('tp_code', $tp_code)->get();
	        } catch (\Exception $e) {
	        	$app_sql = '';
	        }
        }
		if($app_sql && count($app_sql)){	//获取游戏和tp_code对应的app_id，这样一个账号玩同平台下多个游戏不会查询出多个结果
			$app_id = array();
			foreach ($app_sql as $value) {
				$app_id[] = $value->app_id;	
			}
			unset($value);
		}else{
			$app_id = array();
		}
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'p.uid');
			$query->leftJoin("{$db_qiqiwu}.third_party as tp", function($join) use ($app_id) {
				$join->on('tp.uid', '=', 'u.uid');
				if(count($app_id)){
					$join->where('tp.app_id', '=', $app_id[0]);
					unset($app_id[0]);
				}
				foreach ($app_id as $value) {
					$join->orOn('tp.uid', '=', 'u.uid')
						 ->where('tp.app_id', '=', $value);
					unset($value);
				}
			});
			$query->leftJoin("{$db_qiqiwu}.device_list as dl", function($join) use ($game_id){
			$join->on('dl.device_id', '=', 'u.device_id')
				->where('dl.game_id', '=', $game_id);	
			});
			$query->leftJoin(DB::raw("(select player_id,player_name from (select player_id,player_name from log_player_name order by id desc) as pn group by player_id) as lpn"), 'p.player_id', '=', 'lpn.player_id');
		}else{
			$query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'p.user_id');
			$query->leftJoin("{$db_qiqiwu}.third_party as tp", function($join) use ($app_id, $game_id){
				$join->on('tp.uid', '=', 'u.uid');
				if(!in_array($game_id, array(5, 8, 36, 41, 43, 44, 45, 70))){
					if(count($app_id)){
						$join->where('tp.app_id', '=', $app_id[0]);
						unset($app_id[0]);
					}
					foreach ($app_id as $value) {
						$join->orOn('tp.uid', '=', 'u.uid')
							 ->where('tp.app_id', '=', $value);
						unset($value);
					}
				}
			});
			$query->where('p.server_id', $server_internal_id);
		}
		if ($player_id) {
			$query->where('p.player_id', $player_id);
		} else if ($player_name) {
			if(in_array($game_id, Config::get('game_config.mobilegames'))){
				$query->whereRaw("binary lpn.player_name = '$player_name'");
			}else{
				if($mergeserver){
					if(strpos($player_name, '.')){
						$player_name_array = explode('.', $player_name);
						$query->whereRaw("binary p.player_name = '$player_name_array[0]'");
					}else{
						$query->whereRaw("binary p.player_name = '$player_name'");
					}
				}else{
					$query->whereRaw("binary p.player_name = '$player_name'");
				}
			}
		}
		if(!(in_array($game_id, Config::get('game_config.mobilegames')))){
			$query->selectRaw('u.uid, u.name,u.contact_email,u.last_visit_ip,u.last_visit_time,u.created_time,u.created_ip, p.player_id, tp.tp_user_id, FROM_UNIXTIME(p.created_time) as player_time, p.server_id as server_internal_id, u.nickname, u.login_email, p.player_name, u.u, u.u2, u.source, u.is_anonymous');
		}elseif(in_array($game_id, Config::get('game_config.mobilegames'))){
			$query->selectRaw('u.uid, u.name,u.contact_email,u.last_visit_ip,u.last_visit_time,u.created_time,u.created_ip, p.player_id, tp.tp_user_id, FROM_UNIXTIME(p.created_time) as player_time, '.($server_internal_id ? $server_internal_id : 1).' as server_internal_id, u.nickname, u.login_email, lpn.player_name, u.u, u.u2, u.source, u.is_anonymous, dl.device_type');
		}
		
		return $query;
	}

	public function scopeCreatePlayerStatYYSG($query, $start_time, $end_time, $server_internal_id, $interval, $db_qiqiwu){
		$query->leftJoin(DB::raw("`{$db_qiqiwu}`.users u"), 'u.uid', '=', 'p.uid');
		$query->whereBetween('p.created_time', array(
			$start_time, 
			$end_time,
		));
		$query->where('u.user_id', '>', 0);
		$interval_sql = '';
		if ($interval == 600) {
			$interval_sql = $this->selectTenMinute();
		} else if ($interval == 3600) {
			$interval_sql = $this->selectHour();
		} else if ($interval == 86400) {
			$interval_sql = $this->selectDay();
		}
		if ($interval > 0) {
			$query->groupBy('ctime')->orderBy('ctime', 'DESC');
		}
		$query->selectRaw(	
			'count(player_id) as count, ' . ($interval_sql ? DB::raw($interval_sql) : 'MIN(p.created_time) as ctime, MAX(p.created_time) as max_time'));
		return $query;
	}

	private function selectTenMinute()
	{
		$interval = 600;
		return "FLOOR(p.created_time/{$interval}) * {$interval} as ctime";
	}
	
	private function selectHour()
	{
		return 'UNIX_TIMESTAMP(FROM_UNIXTIME(p.created_time, \'%Y-%m-%d %H:00:00\')) as ctime';
	}

	private function selectDay()
	{
		return 'UNIX_TIMESTAMP(FROM_UNIXTIME(p.created_time, \'%Y-%m-%d\')) as ctime';
	}

	public function scopeChannelRetention($query, $db_qiqiwu, $source, $u1, $u2, $filter, $reg_start_time, $reg_end_time, $is_anonymous, $game_id, $platform_id)
	{
		$this->retentionBasic($query, $db_qiqiwu, $source, $u1, $u2, $is_anonymous, $reg_start_time, $reg_end_time, $game_id);
		
		if ($filter == 'source') {
			$filter_sql = 'u.source, CONCAT_WS("_", u.source, "") as filter_key';
			$query->groupBy('u.source');
		} else if ($filter == 'u1') {
			$filter_sql = 'u.u as u1, u.source, CONCAT_WS("_", u.u, u.source) as filter_key';
			$query->groupBy('u.u', 'u.source');
		} else if ($filter == 'u2') {
			$filter_sql = 'u.u2, u.u as u1, u.source, CONCAT_WS("_", u.u2, u.u, u.source) as filter_key';
			$query->groupBy('u.u2', 'u.u', 'u.source');
		}

		$query->selectRaw(
			"{$filter_sql},
			COUNT(p.player_id) as create_count,
			UNIX_TIMESTAMP(FROM_UNIXTIME(p.created_time, '%Y-%m-%d')) as ctime"
		);

		$query->groupBy('ctime');
		return $query;
	}

	public function scopeRetentionPlayers($query, $db_qiqiwu, $start_time, $end_time, $is_anonymous, $source, $u1, $u2 , $game_id, $platform_id)
	{
		$this->retentionBasic($query, $db_qiqiwu, $source, $u1, $u2, $is_anonymous, $start_time, $end_time, $game_id);
		$query->selectRaw("
			p.player_id
		");
		return $query;	
	}

	private function retentionBasic($query, $db_qiqiwu, $source, $u1, $u2, $is_anonymous, $reg_start_time, $reg_end_time, $game_id) 
	{
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$query->Join(DB::raw("`{$db_qiqiwu}`.users as u"), 'p.uid', '=', 'u.uid');
		}else{
			$query->Join(DB::raw("`{$db_qiqiwu}`.users as u"), 'p.user_id', '=', 'u.uid');
		}
		if ($source || $source === '0') {
			$query->where('u.source', $source);
		}	
		if ($u1 || $source === '0') {
			$query->where('u.u', $u1);
		}
		if ($u2 || $source === '0') {
			$query->where('u.u2', $u2);
		}
		
		$query->whereBetween('p.created_time', array($reg_start_time, $reg_end_time));
		
		if ($is_anonymous != null) {
			$query->where('u.is_anonymous', $is_anonymous);
		}
	}

	public function scopeCalculateRetention($query, $params, $db_qiqiwu, $db_payment){
		if(in_array($params['game_id'], Config::get('game_config.mobilegames'))){	//手游和页游的某些字段名字不同
			$uid_key = 'p.uid';
			$login_time_key = 'll.action_time';
		}else{
			$uid_key = 'p.user_id';
			$login_time_key = 'll.login_time';
		}
		if(2 == $params['by_create_time']){	//限制有充值的玩家
			$query->join("{$db_payment}.pay_order as o", function($join) use($params, $uid_key){
				$join->on($uid_key, '=', 'o.pay_user_id')
					->where('o.get_payment', '=', 1)
					->where('o.game_id', '=', $params['game_id']);
			});
		}

		if('login' == $params['by_what_time']){	//限制登录时间
			$query->Join('log_login as ll', function($join) use ($params, $login_time_key){	//注意这里使用的是log_login表
				$join->on('p.player_id', '=', 'll.player_id')
					->where($login_time_key, '>', $params['login_start_time'])
					->where($login_time_key, '<', $params['login_end_time']);
			});
		}elseif('play' == $params['by_what_time']){	//德扑才能进入的判断，限制玩家玩牌
			$login_time_key = 'll.action_time';
			$endOneRound = "endOneRound";
			$query->Join('log_economy as ll', function($join) use ($params, $endOneRound){	//注意这里使用的是log_economy表
				$join->on('p.player_id', '=', 'll.player_id')
					->where('ll.action_time', '>', $params['login_start_time'])
					->where('ll.action_time', '<', $params['login_end_time'])
					->where('ll.action_type', '=', $endOneRound);
			});
		}
		if($params['interval']){
			$interval_second = $params['interval'] * 86400;	//天转时间
			$query->selectRaw("({$params['login_start_time']} + floor(({$login_time_key}-{$params['login_start_time']})/{$interval_second})*{$interval_second}) as count_start_time, count(distinct ll.player_id) as result_num");
			$query->groupBy('count_start_time');
		}else{
			$query->selectRaw("{$params['login_start_time']} as count_start_time, count(distinct ll.player_id) as result_num");
		}

		$query->whereBetween('p.created_time', array($params['create_start_time'], $params['create_end_time']));

		return $query;
	}

	public function scopeTotalCreate($query, $params, $db_qiqiwu, $db_payment){
		if(in_array($params['game_id'], Config::get('game_config.mobilegames'))){	//手游和页游的某些字段名字不同
			$uid_key = 'p.uid';
		}else{
			$uid_key = 'p.user_id';
		}
		if(2 == $params['by_create_time']){	//限制有充值的玩家
			$query->join("{$db_payment}.pay_order as o", function($join) use($params, $uid_key){
				$join->on($uid_key, '=', 'o.pay_user_id')
					->where('o.get_payment', '=', 1)
					->where('o.game_id', '=', $params['game_id']);
			});
		}
		if($params['by_create_time']){	//不限制创建时间的时候不限制
			$query->whereBetween('p.created_time', array($params['create_start_time'], $params['create_end_time']));
		}
		$query->selectRaw('count(distinct p.player_id) as total');
		return $query;
	}
}