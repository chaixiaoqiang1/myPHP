<?php
    class SlaveExchangeRate extends Eloquent {

	protected $table = 'exchange';

	protected $primaryKey = 'id';

	protected function getDateFormat()
	{
		return 'U';
	}
}