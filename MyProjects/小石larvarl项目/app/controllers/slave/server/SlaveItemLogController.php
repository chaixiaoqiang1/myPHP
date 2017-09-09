<?php

class SlaveItemLogController extends SlaveServerBaseController {
	public function getItemCount(){	//统计道具变动信息
		$keys = array('start_time', 'end_time', 'change_type', 'item_id', 'player_info');
		$params = array();
		foreach ($keys as $key) {
			$params[$key] = (int)Input::get($key);
		}
		$symbol = $params['change_type'] > 0 ? '>' : '<';
		$result = array();

		$server_result = DB::connection($this->db_name)->table('log_item')
					->whereBetween('created_at', array($params['start_time'], $params['end_time']))
					->where('table_id', $params['item_id'])
					->where('num', $symbol, 0)
					->selectRaw('count(distinct player_id) as player_num, sum(num) as item_num')
					->get();
		$result['server'] = $server_result;

		if($params['player_info']){
			$player_result = DB::connection($this->db_name)->table('log_item')
					->whereBetween('created_at', array($params['start_time'], $params['end_time']))
					->where('table_id', $params['item_id'])
					->where('num', $symbol, 0)
					->groupby('player_id')
					->selectRaw('player_id, sum(num) as item_num')
					->get();
			foreach ($player_result as &$value) {
				$player_name = PlayerNameLog::on($this->db_name)->getPlayerName($value->player_id)->first();
				$value->player_name = isset($player_name->player_name) ? $player_name->player_name : '';
			}
			$result['player'] = $player_result;
		}else{
			$result['player'] = array();
		}
		
		return Response::json($result);
	}

	public function getPlayerLogItemData(){	//手游查询道具获取日志 log_item
        $msg = array(
                'code' => Config::get('error.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );

		$game_id = (int)Input::get('game_id');
		$player_id = (int)Input::get('player_id');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$table_id = Input::get('table_id');
		$result = DB::connection($this->db_name)->table('log_item')->where('player_id', $player_id)->whereBetween('created_at', array($start_time, $end_time));
		if($table_id){
			$result = $result->where('table_id', $table_id);
		}
		$index_try = DB::connection($this->db_name)->select("show index from log_item where Key_name = 'created_at'");
		if(count($index_try)){
			$result = $result->index('created_at')->get();
		}else{
			$result = $result->get();
		}
		if (in_array($game_id, Config::get('game_config.yysggameids'))) {	//夜夜三国额外查询了邮箱表
			$tmp_result = DB::connection($this->db_name)->table('log_giftbox')->where('to_player_id', $player_id)->whereBetween('created_at', array($start_time, $end_time));
			if($table_id){
				$tmp_result = $tmp_result->where('table_id', $table_id);
			}
			$tmp_result = $tmp_result->get();
			$result = array(
				'item'	=>	$result,
				'giftbox'	=>	$tmp_result,
				);
		}
		if($result){
			return Response::json($result);
		}else{
			return Response::json($msg,403);
		}
	}
}