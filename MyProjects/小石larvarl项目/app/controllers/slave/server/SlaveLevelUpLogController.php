<?php 

class SlaveLevelUpLogController extends \SlaveServerBaseController {
	public function getPlayerLevelUp()
	{
		$msg = array(
			'error' => ''
		);
		$player_name = Input::get('player_name');
		$player_id = (int)Input::get('player_id');
	   	if (!$player_name && !$player_id) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$game_id = (int)Input::get('game_id');
		if($player_name && !$player_id){	//只有名字没有id的时候尝试通过名字获取id
			if(in_array($game_id, Config::get('game_config.mobilegames'))){
				$player_id_data = PlayerNameLog::on($this->db_name)->where('player_name', $player_name)->first();
			}else{
				$player_id_data = CreatePlayerLog::on($this->db_name)->where('player_name', $player_name)->first();
			}
			if($player_id_data){
				$player_id = $player_id_data->player_id;
			}else{
				$player_id = 0;
			}
		}
		if(!$player_id){
			$msg['error'] = 'Can not find such player.';
			return Response::json($msg, 403);
		}

		if(in_array($game_id, Config::get('game_config.mobilegames'))){	//这里把手游和页游不同的字段对应起来
			$select_sql = "old_lev as old_level, lev as new_level, created_at as levelup_time";
		}else{
			$select_sql = "old_level, new_level, levelup_time";
		}

		$result = LevelUpLog::on($this->db_name)->where('player_id', $player_id)->selectRaw($select_sql)->get();
		if ($result) {
			return Response::json($result);
		} else {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 404);
		}
	}
}