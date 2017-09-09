<?php 

class LoginLog extends Eloquent {

	protected $table = 'log_login as ll';

	protected $primaryKey = 'log_id';



	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeGetLoginTotal($query, $start_time, $end_time, $interval, $game_id, $level)
	{
		$sql = $this->selectTimeOfInterval($interval,$game_id);
		
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$time1 = "UNIX_TIMESTAMP(FROM_UNIXTIME(ll.action_time, '%Y-%m-%d'))";
			$query->selectRaw("{$sql}, count(DISTINCT(ll.player_id)) as login_count, 
			count(DISTINCT(last_ip)) as ip_count, count(DISTINCT(lcp.player_id)) as login_count_before");
			$query->leftJoin('log_create_player as lcp', function($join) use($time1) {
				$join->on('lcp.player_id', '=', 'll.player_id')
					 ->on('lcp.created_time', '<', DB::raw($time1));
			});
			$query->whereBetween('action_time', array(
				$start_time,
				$end_time,
			));
			if($level){
				$query->where('lev', '>=', $level);
			}
		}else{
			$time2 = "UNIX_TIMESTAMP(FROM_UNIXTIME(ll.login_time, '%Y-%m-%d'))";
			$query->selectRaw("{$sql}, count(DISTINCT(ll.player_id)) as login_count, 
			count(DISTINCT(ll.remote_host)) as ip_count, count(DISTINCT(lcp.player_id)) as login_count_before");
			$query->leftJoin('log_create_player as lcp', function($join) use($time2){
				$join->on('lcp.player_id', '=', 'll.player_id')
					 ->on('lcp.created_time', '<', DB::raw($time2));
			});
			$query->whereBetween('login_time', array(
				$start_time,
				$end_time,
			));
			if($level){
				$query->where('level', '>=', $level);
			}
		}

		$query->groupBy('ltime');

		return $query;
	}
	
	private function selectTimeOfInterval($interval,$game_id)
	{
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			switch ($interval) {
				case 600:
					return "floor( action_time/{$interval} ) * {$interval} as ltime";
				case 3600:
					return "UNIX_TIMESTAMP ( FROM_UNIXTIME(action_time, '%Y-%m-%d %H') ) as ltime";
				case 86400:
					return "UNIX_TIMESTAMP ( FROM_UNIXTIME(action_time, '%Y-%m-%d') ) as ltime";
				default:
					return "floor( action_time/{$interval} ) * {$interval} as ltime";
			}
		}else{
			switch ($interval) {
				case 600:
					return "floor( login_time/{$interval} ) * {$interval} as ltime";
				case 3600:
					return "UNIX_TIMESTAMP ( FROM_UNIXTIME(login_time, '%Y-%m-%d %H') ) as ltime";
				case 86400:
					return "UNIX_TIMESTAMP ( FROM_UNIXTIME(login_time, '%Y-%m-%d') ) as ltime";
				default:
					return "floor( login_time/{$interval} ) * {$interval} as ltime";
			}
		}
		
	}
	
	public function scopeLoginOnline($query, $retention_time, $day, $create_player_ids, $action_key, $time_key)	//因为is_login值为1或者-1，所以他们的和值可以用来判定是否在线
	{
		$query->select(DB::raw('SUM('.$action_key.') as online, ll.player_id'))
			->where($time_key, '<=', $retention_time + ($day - 1) * 86400)
			->whereIn('ll.player_id', $create_player_ids)
			->groupBy('ll.player_id')
			->havingRaw('online = 1');
		return $query;
	}

	public function scopeLoginCount($query, $retention_time, $day, $create_player_ids, $game_id)
	{
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$query->whereBetween('action_time', array(($retention_time + ($day - 1) * 86400), ($retention_time + $day * 86400 - 1)))
			->whereIn('ll.player_id', $create_player_ids)
			->selectRaw('COUNT(DISTINCT(ll.`player_id`)) as count');
		}else{
			$query->whereBetween('login_time', array(($retention_time + ($day - 1) * 86400), ($retention_time + $day * 86400 - 1)))
			->whereIn('ll.player_id', $create_player_ids)
			->selectRaw('COUNT(DISTINCT(ll.`player_id`)) as count');
		}
		
		return $query;
	}

	public function scopeLoginByDay($query, $start_time, $end_time, $game_id)
	{
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$query->selectRaw("COUNT(DISTINCT player_id) as login_num,
				FROM_UNIXTIME(action_time,'%Y-%m-%d') as date")
				->whereBetween('action_time', array($start_time, $end_time))
				->groupBy('date');
		}else{
			$query->selectRaw("COUNT(DISTINCT player_id) as login_num,
				FROM_UNIXTIME(login_time,'%Y-%m-%d') as date")
				->whereBetween('login_time', array($start_time, $end_time))
				->groupBy('date');
		}
		return $query;
		
	}

	public function scopeLoginByDayNoToday($query, $start_time, $end_time, $game_id)	//与上面的功能类似，但是额外查询去新dau
	{
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$time = "UNIX_TIMESTAMP(FROM_UNIXTIME(ll.action_time, '%Y-%m-%d'))";
			$query->selectRaw("COUNT(DISTINCT ll.player_id) as login_num, COUNT(DISTINCT lcp.player_id) as login_num_not_today,
				FROM_UNIXTIME(action_time,'%Y-%m-%d') as date")
				->leftJoin("log_create_player as lcp", function($join) use ($time){
					$join->on('lcp.player_id', '=', 'll.player_id')
						 ->on('lcp.created_time', '<', DB::raw($time));
				})
				->whereBetween('action_time', array($start_time, $end_time))
				->groupBy('date');
		}else{
			$time = "UNIX_TIMESTAMP(FROM_UNIXTIME(ll.login_time, '%Y-%m-%d'))";
			$query->selectRaw("COUNT(DISTINCT ll.player_id) as login_num, COUNT(DISTINCT lcp.player_id) as login_num_not_today,
				FROM_UNIXTIME(login_time,'%Y-%m-%d') as date")
				->leftJoin("log_create_player as lcp", function($join) use ($time){
					$join->on('lcp.player_id', '=', 'll.player_id')
						 ->on('lcp.created_time', '<', DB::raw($time));
				})
				->whereBetween('login_time', array($start_time, $end_time))
				->groupBy('date');
		}
		return $query;
		
	}

	public function scopeCalculateRetention($query, $params, $db_qiqiwu, $db_payment){
		if(in_array($params['game_id'], Config::get('game_config.mobilegames'))){	//手游和页游的某些字段名字不同
			$login_time_key = 'll.action_time';
		}else{
			$login_time_key = 'll.login_time';
		}
		if($params['interval']){
			$interval_second = $params['interval'] * 86400;	//天转时间
			$query->selectRaw("({$params['login_start_time']} + floor(({$login_time_key}-{$params['login_start_time']})/{$interval_second})*{$interval_second}) as count_start_time, count(distinct ll.player_id) as result_num");
			$query->groupBy('count_start_time');
		}else{
			$query->selectRaw("{$params['login_start_time']} as count_start_time, count(distinct ll.player_id) as result_num");
		}

		$query->whereBetween($login_time_key, array($params['login_start_time'], $params['login_end_time']));
		return $query;
	}
}