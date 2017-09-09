<?php
    class AdJs extends Eloquent
    {
    	protected $table = 'ad_js';
		protected $primaryKey = 'js_id';
		
		public function getDataFormat()
		{
			return "U";
		}
    }

?>