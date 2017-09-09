<?php

class PlatformServer extends Eloquent {

	protected $table = 'server_list';

	protected $primaryKey = 'server_id';

	protected function getDateFormat()
	{
		return 'U';
	}

}