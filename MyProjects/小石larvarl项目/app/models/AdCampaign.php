<?php
    class AdCampaign extends  Eloquent
    {
    	protected $table = 'ad_campaign';

		protected $primaryKey = 'campaign_id';
	
		protected function getDateFormat()
		{
			return 'U';
		}
    }
?>