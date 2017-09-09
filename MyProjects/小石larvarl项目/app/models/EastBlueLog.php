<?php

class EastBlueLog extends Eloquent {

	protected $table = 'log';

	protected $primaryKey = 'log_id';

	public function user()
	{
		return $this->belongsTo('User');
	}

	protected function getDateFormat()
	{
		return 'U';
	}

}