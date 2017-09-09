<?php
    class AdLink extends Eloquent
    {
    	protected $table = 'ad_links';

	    protected $primaryKey = 'link_id';

	    protected function getDateFormat()
	    {
		    return 'U';
	    }
    }

?>