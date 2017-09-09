<?php

class Group extends Eloquent {

	protected $table = 'groups';

	protected $primaryKey = 'group_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function apps()
	{
		if (!$this->apps) {
			return array();
		}
		return explode(',', $this->apps);
	}
}