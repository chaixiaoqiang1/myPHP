<?php

class RetentionLog extends Eloquent {

	protected $table = 'log_retention';

	protected $primaryKey = 'log_id';

	public $timestamps = false;

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeRetentionByTime($query, $start_time, $end_time, $is_anonymous)
	{
		return $query->whereBetween('retention_time', array($start_time, $end_time))
			->where('is_anonymous', $is_anonymous)
			->orderBy('retention_time', 'ASC');
	}
	public function scopeRetentionByDay($query, $start_time, $end_time)
	{
		return $query->selectRaw("SUM(created_player_number) as created_player,
			SUM(days_2) as days_2,
			FROM_UNIXTIME(retention_time, '%Y-%m-%d') as date")
			->whereBetween('retention_time', array($start_time, $end_time))
			->groupBy('date')
			->orderBy('retention_time', 'DESC');
	}

}