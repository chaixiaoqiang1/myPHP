<?php
    class AdLp extends Eloquent
    {
    	protected $table = 'ad_lp';
		protected $primaryKey = 'lp_id';
		
		public function getDataFormat()
		{
			return "U";
		}
		protected function currentGameLps()
	    {
		    return $this->where('game_id', Session::get('game_id'));
	    }
    }

?>