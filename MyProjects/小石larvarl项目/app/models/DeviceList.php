<?php

use \Log;

class DeviceList extends Eloquent{
	protected $table = 'device_list as dl';
	protected $primaryKey = 'id';
	
	public function getDataFormat()
	{
		return "U";
	}

	public function scopeGetSetupStat($query, $start_time, $end_time, $interval, $filter, $source, $u1, $u2, $os_type, $game_id, $server_internal_id, $db_server, $has_game_id){
		if('9' == $os_type){//只获取安装数且不区分设备类型
			$query->where('dl.game_id', $game_id)
			->whereBetween('dl.time', array($start_time, $end_time));

			$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $os_type, $interval, $filter, $interval_sql, $filter_sql);
			$query->select(
					($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
					DB::raw('COUNT(distinct dl.device_id) as device_num'));
			$this->filtersource($query, $source, $u1, $u2, $filter);
			return $query;
		}
		$query->leftJoin('login_device as ld', function($join) {
			$join->on('dl.device_id', '=', 'ld.device_id')
				 ->on('dl.game_id', '=', 'ld.game_id');
		})
		->leftJoin('create_player as cp', function($join) use ($has_game_id, $game_id, $server_internal_id, $start_time, $end_time){
			$join->on('ld.uid', '=', 'cp.uid');
			if($has_game_id){
				$join->where('cp.game_id', '=', $game_id);
			}
			$join->where('cp.server_id', '=', $server_internal_id)
				 ->where('cp.created_time', '>=', $start_time);

		});
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$query->leftJoin(DB::raw('`'.$db_server.'`.log_levelup as ll'), function ($join){
				$join->on('cp.player_id', '=', 'll.player_id')
					 ->where('ll.lev', '=', 10);
			});
			$lev10_sql = 'COUNT(distinct ll.player_id) as device_create_lev10';
		}else{
			$lev10_sql = '0 as device_create_lev10';
		}
		$query->where('dl.game_id', $game_id)
		->whereBetween('dl.time', array($start_time, $end_time));

		$interval_sql = '';
		$filter_sql = '';
		$this->filterInterval($query, $os_type, $interval, $filter, $interval_sql, $filter_sql);
		$query->select(
				($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(distinct dl.device_id) as device_num'), 
				DB::raw('COUNT(distinct cp.player_id) as device_create'), 
				DB::raw($lev10_sql));
		$this->filtersource($query, $source, $u1, $u2, $filter);
		return $query;
	}

	private function filtersource($query, $source, $u1, $u2, $filter){
		if ($source || $source === '0') {
            $query->where('dl.source', $source);
        }
        if ($u1) {
        	if($u1 == '-1'){
				$u1 = -1;
			}
            $query->where('dl.campaign', $u1);
        }
        if ($u2) {
        	if($u2 == '-1'){
				$u2 = -1;
			}
            $query->where('dl.term', $u2);
        }

        if ($filter == 'source') {
            $query->orderBy('dl.source', 'ASC');
        } else if ($filter == 'u1') {
            $query->orderBy('dl.campaign', 'ASC')->orderBy('dl.source', 'ASC');
        } else if ($filter == 'u2') {
            $query->orderBy('dl.term', 'ASC')->orderBy('dl.campaign', 'ASC')->orderBy('dl.source', 'ASC');
        }
	}

	private function filterInterval($query, $os_type, $interval, $filter, &$interval_sql, &$filter_sql){
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

		if('0' == $os_type){
			$filter_sql_os_type = 'dl.os_type';
			$query->groupBy('dl.os_type');
		}elseif('1' == $os_type){
			$filter_sql_os_type = 'dl.os_type';
			$query->where('dl.os_type', 'android');
		}elseif('2' == $os_type){
			$filter_sql_os_type = 'dl.os_type';
			$query->where('dl.os_type', 'iOS');
		}else{//不区分设备类型
			$filter_sql_os_type = '';
		}
		
		if ($filter == 'source') {
			$filter_sql_ad = 'dl.source';
			$query->groupBy('dl.source');
		} else if ($filter == 'u1') {
			$filter_sql_ad = 'dl.campaign as u1, dl.source';
			$query->groupBy('dl.campaign', 'dl.source');
		} else if ($filter == 'u2') {
			$filter_sql_ad = 'dl.term as u2, dl.campaign as u1, dl.source';
			$query->groupBy('dl.term', 'dl.campaign', 'dl.source');
		}else{
			$filter_sql_ad = '';
		}

		if($filter_sql_os_type && $filter_sql_ad){
			$filter_sql .= $filter_sql_os_type.','.$filter_sql_ad;
		}elseif($filter_sql_os_type){
			$filter_sql .= $filter_sql_os_type;
		}elseif($filter_sql_ad){
			$filter_sql .= $filter_sql_ad;
		}else{

		}
	}

	private function selectTenMinute()
	{
		$interval = 600;
		return "FLOOR(dl.time/{$interval}) * {$interval} as ctime";
	}
	
	private function selectHour()
	{
		return 'UNIX_TIMESTAMP(FROM_UNIXTIME(dl.time, \'%Y-%m-%d %H:00:00\')) as ctime';
	}

	private function selectDay()
	{
		return 'UNIX_TIMESTAMP(FROM_UNIXTIME(dl.time, \'%Y-%m-%d\')) as ctime';
	}
}
?>