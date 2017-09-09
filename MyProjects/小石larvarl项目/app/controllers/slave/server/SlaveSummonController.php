<?php

class SlaveSummonController extends \SlaveServerBaseController{
	public function mnsglogsummonData(){
		$single_or_count = Input::get('single_or_count');
		$player_id = Input::get('player_id');
		$summon_type = Input::get('summon_type');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');

		$result = array();

		$db = DB::connection($this->db_name);
		if('single' == $single_or_count){
			$result = $db->table('log_summon')
						->whereBetween('created_at', array($start_time, $end_time))
						->where('player_id', $player_id)
						->get();
		}

		if('count' == $single_or_count){
			$result = $db->table('log_summon')
						->whereBetween('created_at', array($start_time, $end_time));
			if($summon_type){
				$result = $result->where('summon_type', $summon_type)->groupBy('player_id')
					->selectRaw("player_id, summon_type, count(1) as times")->get();
			}else{
				$result = $result->groupBy('summon_type')->selectRaw("summon_type, count(distinct player_id) as player_num, count(1) as times")->get();
			}
		}

		if(count($result)){
			return Response::json($result);
		}else{
			return Response::json(array(), 404);
		}
	}
}