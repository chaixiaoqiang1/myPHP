<?php

class SpecialPlayers extends Eloquent {

	protected $table = 'special_players';

	protected $primaryKey = 'id';

	protected function getDateFormat()
	{
		return 'U';
	}

}