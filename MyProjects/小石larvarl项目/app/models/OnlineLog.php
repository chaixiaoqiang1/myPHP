<?php

class OnlineLog extends Eloquent {

	protected $table = 'log_online';

	protected $primaryKey = 'log_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeOnlineByDay($query, $start_time, $end_time)
	{
		
		return $query->selectRaw("AVG(online_value) as avg_online,
			MAX(online_value) as max_online,
			FROM_UNIXTIME(online_time,'%Y-%m-%d') as date")
			->whereBetween('online_time', array($start_time, $end_time))
			->groupBy('date');
	}
}