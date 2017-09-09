<?php
    class Currency extends Eloquent {

	protected $table = 'currencies';

	protected $primaryKey = 'currency_id';

	protected function getDateFormat()
	{
		return 'U';
	}
}