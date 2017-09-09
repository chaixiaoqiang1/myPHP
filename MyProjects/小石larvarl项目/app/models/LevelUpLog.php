<?php 

class LevelUpLog extends Eloquent {

	protected $table = 'log_levelup as up';

	protected $primaryKey = 'log_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeGetFirstOrderTime($query, $order_time, $player_id)
	{
		$query->where('levelup_time', '<=', $order_time)
			->where('player_id', '=', $player_id)
			->orderBy('levelup_time', 'DESC');
		return $query;
	}

	public function scopeGetFirstOrderTimeForMG($query, $order_time, $player_id)
	{
		$query->where('created_at', '<=', $order_time)
			->where('player_id', '=', $player_id)
			->orderBy('created_at', 'DESC');
		return $query;
	}

	public function scopeGetPlayer($query, $player_name)
	{
		$query->leftJoin('log_create_player as cp', 'cp.player_id', '=', 'up.player_id');
		$query->where('cp.player_name', $player_name);
		return $query;
	}

	public function scopeGetUserStatUserStatLevelten($query, $db_qiqiwu, $game_id, $server_internal_id, $start_time, $end_time, $interval, $filter, $source, $u1, $u2){
		$mobilegameids = Config::get('game_config.mobilegames');

		if(in_array($game_id, Config::get('game_config.yysggameids'))){
			$query->join('log_create_player as cp', function($join) use ($start_time) {
					$join->on('up.player_id', '=', 'cp.player_id')
						->where('up.lev', '=', '10')
						->where('cp.created_time', '>', $start_time);
			});
		}else{
			$query->join("$db_qiqiwu.create_player as cp", function($join) use ($server_internal_id, $game_id, $mobilegameids, $start_time){
				$join->on('up.player_id', '=', 'cp.player_id')
					->where('cp.server_id', '=', $server_internal_id)
					->where('cp.created_time', '>', $start_time);
				if(in_array($game_id, $mobilegameids)){
					$join->where('up.lev', '=', '10');
				}else{
					$join->where('up.new_level', '=', '10');
				}
			});
		}

		$query->join("$db_qiqiwu.users as u", function($join) use ($source, $u1, $u2, $start_time, $end_time) {
			$join->on('u.uid', '=', 'cp.uid')
				->where('u.created_time', '>', date("Y-m-d H:i:s", $start_time))
				->where('u.created_time', '<', date("Y-m-d H:i:s", $end_time));
				if ($source || $source === '0') {
					if(strlen($source) > 16){
						$source = substr($source, 0, 16);
					}
					if('-1' == $source){
						$source = -1;
					}
		            $join->where('u.source', '=', $source);
		        }
		        if ($u1) {
		        	if('-1' == $u1){
						$u1 = -1;
					}
		            $join->where('u.u', '=', $u1);
		        }
		        if ($u2) {
		        	if('-1' == $u2){
						$u2 = -1;
					}
		            $join->where('u.u2', '=', $u2);
		        }
		});		

		$interval_sql = '';
		$filter_sql = '';
		$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

		$query->select(
			($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
			DB::raw('COUNT(1) as all_levelten'), 
			DB::raw('sum(u.is_anonymous) as anonymous_levelten'), 
			DB::raw('sum(u.still_anonymous) as still_anonymous_levelten')
		); 
		return $query;  
	}

	private function filterInterval($query, $interval, $filter, &$interval_sql, &$filter_sql){
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
		
		if ($filter == 'source') {
			$filter_sql = 'u.source';
			$query->groupBy('u.source');
		} else if ($filter == 'u1') {
			$filter_sql = 'u.u as u1, u.source';
			$query->groupBy('u.u', 'u.source');
		} else if ($filter == 'u2') {
			$filter_sql = 'u.u2, u.u as u1, u.source';
			$query->groupBy('u.u2', 'u.u', 'u.source');
		}
	}

	private function filtersource($query, $source, $u1, $u2, $game_id, $filter, $des){
		if ($source) {
			if(strlen($source) > 16){
				$source = substr($source, 0, 16);
			}
            $query->where('u.source', $source);
        }
        if ($u1) {
            $query->where('u.u', $u1);
        }
        if ($u2) {
            $query->where('u.u2', $u2);
        }
        if($game_id && $des) {
            $query->where('u.'.$des,'=', $game_id);
        }

        if ($filter == 'source') {
            $query->orderBy('u.source', 'ASC');
        } else if ($filter == 'u1') {
            $query->orderBy('u.u', 'ASC')->orderBy('u.source', 'ASC');
        } else if ($filter == 'u2') {
            $query->orderBy('u.u', 'ASC')->orderBy('u.u2', 'ASC')->orderBy('u.source', 'ASC');
        }
	}

	private function selectTenMinute()
	{
		$interval = 600;
		return "FLOOR(UNIX_TIMESTAMP(u.created_time)/{$interval}) * {$interval} as ctime";
	}
	
	private function selectHour()
	{
		return 'UNIX_TIMESTAMP(DATE_FORMAT(u.created_time, \'%Y-%m-%d %H:00:00\')) as ctime';
	}

	private function selectDay()
	{
		return 'UNIX_TIMESTAMP(DATE_FORMAT(u.created_time, \'%Y-%m-%d\')) as ctime';
	}
}