<?php 

class SlaveWebLogController extends \SlaveServerBaseController {
	public function getguessData(){//获取玩家美人猜猜猜日志信息
		$msg = array(
				'code' => Config::get('errorcode.slave_player_created'),
				'error' => ''
		);

		$game_id = Input::get('game_id');
		$server_id = Input::get('server_id');
		$player_id = Input::get('player_id');
		$start_time = (int)Input::get('start_time');
		$end_time = Input::get('end_time');
		$server_internal_id = (int)Input::get('server_internal_id');

		$db = DB::connection($this->db_name);
		$result = $db->select("
			select * from log_shake_dice 
			where binary player_id = ".$player_id." and time between '".$start_time."' and '".$end_time."'
			limit 1"
			);
		
		if (!$result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}
}