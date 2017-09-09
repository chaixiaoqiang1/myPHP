<?php
    class AdSource extends Eloquent
    {
	    protected $table = 'ad_source';
		protected $primaryKey = 'source_id';
		
		public function getDataFormat()
		{
			return "U";
		}
    }
?>