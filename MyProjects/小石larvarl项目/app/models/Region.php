<?php

class Region extends Eloquent {

	protected $table = 'regions';

	protected $primaryKey = 'region_id';

	protected function getDateFormat()
	{
		return 'U';
	}
}