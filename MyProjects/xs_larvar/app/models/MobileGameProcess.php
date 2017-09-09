<?php

class MobileGameProcess extends Eloquent {

	protected $table = 'schedule';

	protected $primaryKey = 'id';

	protected function getDateFormat()
	{
		return 'U';
	}

}