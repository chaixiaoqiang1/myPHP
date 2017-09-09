<?php

class Platform extends Eloquent {

	protected $table = 'platforms';

	protected $primaryKey = 'platform_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function region()
	{
		return $this->belongsTo('Region');
	}

	public function game()
	{
		return $this->hasMany('Game');
	}

	public function scopeCurrentPlatform($query)
	{
		return $query->where('platform_id', '=', Session::get('platform_id'));
	}
	
}