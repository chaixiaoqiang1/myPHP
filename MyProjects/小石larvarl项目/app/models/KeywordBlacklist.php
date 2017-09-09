<?php

class KeywordBlacklist extends Eloquent {

	protected $table = 'keyword_blacklist';

	protected $primaryKey = 'word_id';

	protected function getDateFormat()
	{
		return 'U';
	}
}