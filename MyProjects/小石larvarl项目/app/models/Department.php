<?php

class Department extends Eloquent {
	const ID_SHICHANG = 5;

	protected $table = 'department';

	protected $primaryKey = 'department_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeOrganization($query)
	{
		return $query->where('organization_id', Auth::user()->organization_id);
	}
}