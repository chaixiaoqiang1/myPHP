<?php

class SlaveCreatePlayer extends Eloquent {

	protected $table = 'create_player as p';

	protected $primaryKey = 'create_player_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeGetUser($query, $game_id, $server_internal_id, $player_id, $player_name, $tp_code='fb')
	{
		$query->leftJoin('users as u', 'u.uid', '=', 'p.uid');
		$query->leftJoin('third_party as tp', function($join) use ($tp_code){
			$join->on('tp.uid', '=', 'u.uid')
				->where('tp.tp_code', '=', $tp_code);	
		});
		if(in_array($game_id,Config::get('game_config.mobilegames')) || 77 == $game_id){
			$query->leftJoin('device_list as dl', function($join) use ($game_id){
			$join->on('dl.device_id', '=', 'u.device_id')
				->where('dl.game_id', '=', $game_id);	
			});
		}
		if ($server_internal_id) {
			$query->where('server_id', $server_internal_id);
		}
		if ($player_id) {
			$query->where('player_id', $player_id);
		} else if ($player_name) {
			$query->whereRaw("binary player_name = '$player_name'");
		}
		if ($game_id) {
			// $query->where('p.game_id', $game_id);
		}
		if(in_array($game_id,Config::get('game_config.mobilegames')) || 77 == $game_id){
			$query->selectRaw('u.uid, u.name,u.contact_email,u.created_time,u.last_visit_ip,u.last_visit_time,u.created_time,u.created_ip, p.player_id, tp.tp_user_id, p.server_id as server_internal_id, u.nickname, u.login_email, p.player_name, u.u, u.u2, u.source, u.is_anonymous,dl.device_type');
		}else{
			$query->selectRaw('u.uid, u.name,u.contact_email,u.created_time,u.last_visit_ip,u.last_visit_time,u.created_time,u.created_ip, p.player_id, tp.tp_user_id, p.server_id as server_internal_id, u.nickname, u.login_email, p.player_name, u.u, u.u2, u.source, u.is_anonymous');
		}
		// Log::info($query);
		return $query;
	}

	public function scopeCreatePlayerStat($query, $start_time, $end_time, $server_internal_id, $interval)
	{
		$query->leftJoin('users as u', 'u.uid', '=', 'p.uid');
		$query->whereBetween('p.created_time', array(
			$start_time, 
			$end_time,
		));
		$query->where('server_id', $server_internal_id);
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

	public function scopeCreatePlayerStatAllServers($query, $start_time, $end_time, $server_internal_id, $interval, $game_id){
		$query->leftJoin('users as u', 'u.uid', '=', 'p.uid');
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

	public function scopeRetentionCreatePlayer($query, $db_qiqiwu, $retention_time, $is_anonymous)
	{
		$query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'p.user_id')
			->where('p.created_time', '>=', $retention_time)
			->where('p.created_time', '<=', $retention_time + 86399);
		if ($is_anonymous != null) {
			$query->where('u.is_anonymous', $is_anonymous);
		}

		$query->select('player_id');
		return $query;	
	}


	public function scopeChannelCreatePlayer($query, $cre_start_time, $cre_end_time, $game_id, $channel = ''){	//周报用来查询单周内玩家的创建量
		
		if(in_array($game_id, array(52, 57))){
			$query->Join("login_device as ld", function($join) use ($game_id){
				$join->on('p.uid', '=', 'ld.uid')
					 ->on('ld.create_time', '<=', 'p.created_time'); 
			})
			->Join("channel_list as cl", function($join) use ($game_id){
				$join->on('ld.device_id', '=', 'cl.device_id')
					 ->on('cl.time', '<=', 'p.created_time');
			});
		}else{
			$query->Join("login_device as ld", function($join) use ($game_id){
				$join->on('p.uid', '=', 'ld.uid')
					 ->on('ld.create_time', '<=', 'p.created_time')
					 	->where('p.game_id', '=', $game_id)
					 	->where('ld.game_id', '=', $game_id); 
			})
			->Join("channel_list as cl", function($join) use ($game_id){
				$join->on('ld.device_id', '=', 'cl.device_id')
					 ->on('cl.time', '<=', 'p.created_time')
					 	->where('cl.game_id', '=', $game_id);
					 
			});
		}
		$query->whereBetween('p.created_time', array($cre_start_time, $cre_end_time))
		->selectRaw('cl.channel, count(distinct p.player_id) as num');
		if($channel){
			$query->where('cl.channel', $channel);
		}else{
			$query->groupBy('cl.channel');
		}
		
		return $query;
	}

	public function scopeRetentionChannelCreatePlayer($query, $db_qiqiwu, $retention_time, $is_anonymous, $game_id, $server_id, $channel){
		if(in_array($game_id, Config::get('game_config.mobilegames'))){//手游不区分匿名和不匿名，is_anonymous传过来为9
			$query->Join("login_device as ld", function($join) use ($game_id){
				$join->on('p.uid', '=', 'ld.uid')
					 ->on('ld.create_time', '<=', 'p.created_time')
					 ->where('ld.game_id', '=', $game_id);
			})
			->Join("channel_list as cl", function($join) use ($game_id){
				$join->on('ld.device_id', '=', 'cl.device_id')
					 ->on('cl.time', '<=', 'p.created_time')
					 ->where('cl.game_id', '=', $game_id);
			})
			->where('p.game_id', $game_id)
			->where('p.server_id', $server_id)
			->whereBetween('p.created_time', array($retention_time, $retention_time+86400))
			->where('cl.channel', $channel)
			->selectRaw('distinct player_id');
		}else{
			return;
		}
		return $query;
	}
}