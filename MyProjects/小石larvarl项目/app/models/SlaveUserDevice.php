<?php

class SlaveUserDevice extends Eloquent {

	protected $table = 'device_list as dl';

	protected $primaryKey = 'device_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeGetUserDevice($query, $start_time, $end_time, $interval, $check_type, $game_id, $serach_type, $channel, $source)
	{
		if($interval==0){
			$sql = "count(distinct dl.device_id) as count,";
		}elseif($interval==86400){
			$sql = 'UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(dl.time), "%Y-%m-%d 00:00:00")) as ctime,
				count(distinct dl.device_id) as count,';
		}else{
			$sql = "FLOOR(dl.time/$interval) * {$interval} as ctime,
				count(distinct dl.device_id) as count,";
		}

		if($interval>0){
			$query->groupBy("ctime");
		}

		if('1' == $serach_type){ //按照channel统计
			$sql .= 'cl.channel as channel';
			if($channel){
				$query->leftJoin(DB::raw('(select distinct channel,device_id from channel_list where game_id = '.$game_id.') as cl'), function($join) use ($channel){
					$join->on('cl.device_id', '=', 'dl.device_id');
				});
				$query->where('cl.channel', $channel);
			}else{
				$query->leftJoin(DB::raw('(select distinct channel,device_id from channel_list where game_id = '.$game_id.') as cl'), function($join){
					$join->on('cl.device_id', '=', 'dl.device_id');
				});
				$query->groupBy('cl.channel');
			}
		}elseif('0' == $serach_type){  //按照设备统计
			$sql .= "os_type";
			if($check_type == '1'){ //仅安卓设备
				$query->where("os_type", "android");
			}elseif($check_type == '2'){	//仅IOS设备
				$query->where("os_type", "iOS");
			}
			$query->groupBy("os_type");
		}elseif('2' == $serach_type){	//按照source
			$sql .= "source ";
			if($source || $source === 0){
				$query->where('dl.source', $source);
			}else{
				$query->groupBy('source');
			}
		}
		$query->selectRaw($sql);
		$query->where('dl.game_id', $game_id);
		$query->whereBetween("dl.time", array($start_time, $end_time));
		return $query;
	}

	public function scopeWeeklyDeviceStat($query, $start_time, $end_time, $game_id, $filter_u1=1){
		if($filter_u1){
			$query->selectRaw("COUNT(DISTINCT(device_id)) as count_device, os_type, source, campaign as u1")
				->whereBetween('time', array($start_time, $end_time))
				->where('game_id', $game_id)
				->groupBy('os_type', 'source', 'campaign');
			return $query;
		}else{
			$query->selectRaw("COUNT(DISTINCT(device_id)) as count_device, os_type, source")
				->whereBetween('time', array($start_time, $end_time))
				->where('game_id', $game_id)
				->groupBy('os_type', 'source');
			return $query;
		}
	}

}