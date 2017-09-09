<?php

class Game extends Eloquent {
	const TYPE_FLSG = 'flsg';
	const TYPE_NSZJ = 'nszj';
	const TYPE_POKER = 'poker';

	protected $table = 'games';

	protected $primaryKey = 'game_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function apps()
	{
		if (!$this->apps) {
			return array();
		}
		return explode(',', $this->apps);
	}

	public function platform()
	{
		return $this->belongsTo('Platform');
	}

	public function scopeCurrentPlatform($query)
	{
		return $query->where('platform_id', '=', Session::get('platform_id'));
	}
	
	public function scopeUserGames($query)
	{
		if (Auth::user()->is_admin) {
			return $query;
		}
		$games = Auth::user()->games();
		if (empty($games)) {
			return $query->where('game_id', 0);
		} else {
			return $query->whereIn('game_id', Auth::user()->games());
		}
	}

}