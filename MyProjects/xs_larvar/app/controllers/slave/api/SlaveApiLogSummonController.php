<?php 

class SlaveApiLogSummonController extends \BaseController {
	private $summon_types = array(
		1 => '金币',
		2 => '钻石',
		3 => '钻石免费',
		4 => '魂匣',
		5 => '远征',
		6 => '低级抽卡券',
		7 => '高级抽卡券',
		8 => '元旦战姬礼包',
		9 => '元旦装备礼包',
		10 => '低级打折券',
		11 => '高级打折券',
		12 => '远征宝箱',
	);

	public function mnsglogsummonIndex(){
		$servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.summon.mnsgsummon', 
                        array(
                                'servers' => $servers,
                                'summon_types' => $this->summon_types,
                        ))
        );
        return View::make('main', $data);
	}

	public function mnsglogsummon(){
		$single_or_count = Input::get('single_or_count');
		$player_id = (int)Input::get('player_id');
		$summon_type = (int)Input::get('summon_type');
		$start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $result2view = array(
			'key' => array(),
			'value' => array(),
			);
		$itemid2name = array();
		$table = $this->OpenFile(public_path().'/table/'.$game->game_code.'/item.txt');
		foreach ($table as $value) {
			$itemid2name[$value->id] = $value->name;
		}
		unset($table);

		if('single' == $single_or_count){
			$server_id = (int)Input::get('server_id');
			if(!$server_id){
				return Response::json(array('error' => "Please select one game server"), 401);
			}

			$server = Server::find($server_id);
			if(!$server){
				return Response::json(array('error' => "No such server"), 401);
			}
			$server_internal_id = $server->server_internal_id;
			unset($server);
			if(!$player_id){
				return Response::json(array('error' => "Please input the player_id"), 401);
			}
			$slave_result = $slaveapi->getmnsgsummondata($game_id, $server_internal_id, $single_or_count, $player_id, $summon_type, $start_time, $end_time);

			if(200 != $slave_result->http_code){
				return $slaveapi->sendResponse();
			}
			$data = $slave_result->body;

			$result2view['key'] = array(
				0 => 'PlayerID',
				1 => Lang::get('slave.summon_type'),
				2 => Lang::get('slave.summon_get'),
				3 => Lang::get('slave.created_time'),
				);
			foreach ($data as $value) {
				$item_ids = explode(',', $value->item_ids);
				$item_nums = explode(',', $value->item_nums);
				$to_stones = explode(',', $value->to_stones);
				$operation = '';
				foreach ($item_nums as $itemkey => $itemnum) {
					$operation .= '('.$itemnum.' 个 '.(isset($itemid2name[$item_ids[$itemkey]]) ? $itemid2name[$item_ids[$itemkey]] : $item_ids[$itemkey]);
					if($to_stones[$itemkey]){
						$operation .= '-'.Lang::get('slave.to_stones').");";
					}else{
						$operation .= ");";
					}
				}
				$result2view['value'][] = array(
					$value->player_id,
					isset($this->summon_types[$value->summon_type]) ? $this->summon_types[$value->summon_type] : $value->summon_type,
					$operation,
					date('Y-m-d H:i:s', $value->created_at),
				);
				unset($value);
				unset($operation);
			}
		}elseif('count' == $single_or_count){
			$has_player_id = 0;
			$has_player_num = 0;
			$server_ids = Input::get('server_ids');
			if(count($server_ids)){
				if(1 == count($server_ids) && 0 == $server_ids[0]){
					return Response::json(array('error' => "Please select at least one game server"), 401);
				}
				foreach ($server_ids as $server_id) {
					if(0 == $server_id){
						continue;
					}
					$server = Server::find($server_id);
					if(!$server){
						return Response::json(array('error' => "No such server"), 401);
					}
					$server_internal_id = $server->server_internal_id;
					unset($server);
					$slave_result = $slaveapi->getmnsgsummondata($game_id, $server_internal_id, $single_or_count, $player_id, $summon_type, $start_time, $end_time);

					if(404 == $slave_result->http_code){	//代表没有结果
						continue;
					}elseif(200 != $slave_result->http_code){
						return $slave_result->sendResponse();
					}

					$data = $slave_result->body;

					if(!count($result2view['key'])){
						$result2view['key'] = array(
							1 => Lang::get('slave.summon_type'),
							3 => Lang::get('slave.times'),
							);
						foreach ($data as $value) {
							if(isset($value->player_id)){
								$has_player_id = 1;
								$result2view['key'][0] = 'PlayerID';
							}
							if(isset($value->player_num)){
								$has_player_num = 1;
								$result2view['key'][2] = Lang::get('slave.player_num');
							}
							break;
							unset($value);
						}
					}
					if($has_player_id){
						foreach ($data as $value) {
							$single_line = array(
								1 => isset($this->summon_types[$value->summon_type]) ? $this->summon_types[$value->summon_type] : $value->summon_type,
								3 => $value->times,
								);
							$single_line[0] = $value->player_id;
							$result2view['value'][] = $single_line;
							unset($value);
							unset($single_line);
						}
					}elseif($has_player_num){
						foreach ($data as $value) {
							if(isset($result2view['value'][$value->summon_type])){
								$result2view['value'][$value->summon_type][2] += $value->player_num;
								$result2view['value'][$value->summon_type][3] += $value->times;
							}else{
								$single_line = array(
									1 => isset($this->summon_types[$value->summon_type]) ? $this->summon_types[$value->summon_type] : $value->summon_type,
									2 => $value->player_num,
									3 => $value->times,
								);
								$result2view['value'][$value->summon_type] = $single_line;
								unset($single_line);
							}
							unset($value);
						}
					}
				}
			}else{
				return Response::json(array('error' => "Please select at least one game server"), 401);
			}
		}else{
			return Response::json(array('error' => "Unknown search type"), 401);
		}
		return Response::json($result2view);
	}
}