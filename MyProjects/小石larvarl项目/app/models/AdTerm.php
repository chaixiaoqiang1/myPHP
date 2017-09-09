<?php
    class AdTerm extends Eloquent
    {
    	protected $table = 'ad_terms';
		protected $primaryKey = 'term_id';
		
		public function getDataFormat()
		{
			return "U";
		}
    }
?>