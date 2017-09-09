<?php
    class Project extends Eloquent {

	protected $table = 'project';

	protected $primaryKey = 'project_id';

	protected function getDateFormat()
	{
		return 'U';
	}
}