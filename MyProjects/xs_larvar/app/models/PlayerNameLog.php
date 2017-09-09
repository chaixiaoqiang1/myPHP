<?php

class PlayerNameLog extends Eloquent {

	protected $table = 'log_player_name as lpn';

	protected $primaryKey = 'id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopecheckplayername($query, $player_name){
		if(0 === strpos($player_name, '"') || strpos($player_name, '"')){	//含双
			if(0 === strpos($player_name, "'") || strpos($player_name, "'")){	//双单
				$query->where('player_name', $player_name);
			}else{	//仅双
				$query->whereRaw("binary player_name = '$player_name'");
			}
		}else{
			if(0 === strpos($player_name, "'") || strpos($player_name, "'")){	//仅单
				$query->whereRaw("binary player_name = \"$player_name\"");
			}else{	//无单无双
				$query->whereRaw("binary player_name = '$player_name'");
			}
		}
		return $query;
	}

	public function scopegetPlayerName($query, $player_id){
		$query->where('player_id', $player_id)
			  ->orderBy('id', 'desc');
		return $query;
	}

}