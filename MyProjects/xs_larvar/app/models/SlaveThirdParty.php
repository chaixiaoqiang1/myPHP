<?php
    class SlaveThirdParty extends Eloquent {

	protected $table = 'third_party as tp';

	protected $primaryKey = 'tp_id';

	protected function getDateFormat()
	{
		return 'U';
	}
}