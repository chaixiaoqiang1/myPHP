<?php

class SlaveApiItemController extends \BaseController
{
	private function getItems(){	//获取固定格式的item文件，返回以"item[物品id] => 物品名称"为格式的数组
		$game = Game::find(Session::get('game_id'));
		$items_file = $this->OpenFile(public_path() . '/table/' . $game->game_code . '/item.txt');
		$items = array();
		foreach ($items_file as $value) {
			$items[$value->id] = $value->name;
		}
		unset($items_file);
		return $items;
	}

	public function ItemCountIndex(){	//item表的统计功能--因为不同游戏记录item表的结构有差异，所以可能需要具体游戏具体查询，目前支持到萌娘三国--潘达
		$game = Game::find(Session::get('game_id'));
		$servers = Server::CurrentGameServers()->get();
		$items = $this->getItems();
		$data = array(
				'content' => View::make('slaveapi.item.item_count', array(
						'servers' => $servers,
						'items' => $items,
						'game_code' => $game->game_code,
						'game_type' => $game->game_type,
				))
		);
		return View::make('main', $data);
	}

	public function ItemCountData(){
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$servers = Input::get('servers');
		$change_type = (int)Input::get('change_type');
		$serach_type = (int)Input::get('serach_type');
		$item_id = (int)Input::get('item_id');
		$start_time = (int)strtotime(trim(Input::get('start_time')));
		$end_time = (int)strtotime(trim(Input::get('end_time')));

		if(empty($servers)){	//没有选择服务器
			return Response::json(array('error' => 'Please Select At least One server.'), 401);
		}
		$items = $this->getItems();
		if(!$item_id){
			$item_name = Input::get('item_name');
			$item_info = explode(':', $item_name);
			$item_id = isset($item_info[0]) ? $item_info[0] : 0;
		}
		if(!$item_id || !array_key_exists($item_id, $items)){	//没有选择物品或者物品不是表内的有效物品
			return Response::json(array('error' => 'Please Select A Valid Item.'), 401);
		}

		$slave_data = array(
			'start_time' => $start_time,
			'end_time' => $end_time,
			'game_id' => $game_id,
			'change_type' => $change_type,
			'item_id' => $item_id,
			'player_info' => $serach_type,	//是否同时查询玩家信息
			);
		
		$slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		$result = array();//结果数组

		foreach ($servers as $server_id) {
			$server = Server::find($server_id);
			$result['server'][$server_id]['server_name'] = $server->server_name;
			$slave_data['server_internal_id'] = $server->server_internal_id;	//重复赋值server_internal_id来循环各个服

			$slave_result = $slaveapi->ItemLogCount($slave_data);
			if(200 == $slave_result->http_code){
				$slave_result = $slave_result->body;
				//返回值有整个服务器的统计信息以及可能有玩家的统计信息
				$server_info = $slave_result->server;
				$player_info = $slave_result->player;
				foreach ($server_info as $value) {
					$result['server'][$server_id]['player_num'] = $value->player_num;
					$result['server'][$server_id]['item_num'] = $value->item_num;
				}
				unset($value);
				foreach ($player_info as $value) {
					$result['player'][] = array(
						'server_name' => $server->server_name,
						'player_id' => $value->player_id,
						'player_name' => $value->player_name,
						'item_num' => $value->item_num,
						);
				}
			}else{
				$result['server'][$server_id]['player_num'] = 'Error';
				$result['server'][$server_id]['item_num'] = 'Error';
			}

			unset($slave_result);
			unset($slave_data['server_internal_id']);
		}

		return Response::json($result); 
	}
}