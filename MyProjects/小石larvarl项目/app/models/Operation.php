<?php

class Operation extends Eloquent {

	protected $table = 'operations';

	protected $primaryKey = 'operation_id';

	public $timestamps = false;

	protected function getDateFormat()
	{
		return 'U';
	}
}