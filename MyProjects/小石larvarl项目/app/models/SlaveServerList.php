<?php
    class SlaveServerList extends Eloquent {

	protected $table = 'server_list as sl';

	protected $primaryKey = 'server_id';

	protected function getDateFormat()
	{
		return 'U';
	}
}