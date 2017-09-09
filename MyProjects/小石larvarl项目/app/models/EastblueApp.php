<?php

class EastblueApp extends Eloquent {

	protected $table = 'apps';

	protected $primaryKey = 'app_id';

	protected function getDateFormat()
	{
		return 'U';
	}
	public function department()
	{
		return $this->belongsTo('Department');
	}

	public function appsToArr()
	{
		if (!$this->apps) {
			return array();
		}
		return explode(',', $this->apps);
	}
}