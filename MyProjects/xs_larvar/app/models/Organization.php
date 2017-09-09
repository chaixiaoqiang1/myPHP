<?php

class Organization extends Eloquent {

	protected $table = 'organization';

	protected $primaryKey = 'organization_id';

	protected function getDateFormat()
	{
		return 'U';
	}
}