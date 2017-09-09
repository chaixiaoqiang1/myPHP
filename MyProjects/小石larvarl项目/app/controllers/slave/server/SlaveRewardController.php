<?php

class SlaveRewardController extends \SlaveServerBaseController{

	public function RzzwRewardData(){
		$game_id = (int)Input::get('game_id');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$player_id = (int)Input::get('player_id');
		$reward_id = (int)Input::get('reward_id');
		$record_type = (int)Input::get('record_type');

		$db = DB::connection($this->db_qiqiwu);

		$reward_record = $db->table('reward_lucky as rl')
		                    ->where('rl.game_id', $game_id)
		                    ->leftjoin('server_list as sl', 'rl.server_id', '=', 'sl.server_id');

		if($start_time && $end_time){
			$reward_record->whereBetween('time', array($start_time, $end_time));
		}

		if($player_id){
			$reward_record->where('player_id', $player_id);
		}

		if($reward_id){
			$reward_record->where('reward_id', $reward_id);
		}

		if($record_type){
			if(1 == $record_type){
				$reward_record->where('is_done', 1);
			}elseif(2 == $record_type){
				$reward_record->where('is_done', 0);
			}
		}

		$reward_record = $reward_record->get();

		if(count($reward_record)){
			return Response::json($reward_record);
		}else{
			return Response::json(array(), 404);
		}
	}

	public function getYYSGAward(){
		$gift_id = Input::get('gift_id');
		$game_id = Input::get('game_id');
		$result = DB::connection($this->db_qiqiwu)->table('award_item')
			->where('game_id',$game_id);
		if($gift_id){
			$result->where('gift_id',$gift_id);
		}
		$result = $result->get();
		return Response::json($result);
	}

	public function getYYSGAwardUser(){
		$uid = Input::get('uid');
        $player_id = Input::get('player_id');
        $total_chance = Input::get('total_chance');
        $game_id = Input::get('game_id');
        $end_time = strtotime(trim(Input::get('end_time')));

		$result = DB::connection($this->db_qiqiwu)->table('award_record')
			->where('game_id',$game_id);
		if($uid){
			$result->where('uid',$uid);
		}
		if($player_id){
			$result->where('player_id',$player_id);
		}
		if($total_chance){
			$result->where('total_chance',$total_chance);
		}
		$result = $result->get();
		return Response::json($result);
	}
}