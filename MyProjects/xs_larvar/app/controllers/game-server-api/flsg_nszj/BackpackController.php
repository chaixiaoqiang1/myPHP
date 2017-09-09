<?php 

class BackpackController extends \BaseController {

	public function index()
	{
		$servers = Server::currentGameServers()->get();
		$table = $this->initTable();
		$items = $table->getData();
		$data = array (
			'content' => View::make('serverapi.flsg_nszj.backpack.index', array (
				'servers' => $servers,
				'items' => $items,
			)), 
		);
		return View::make('main', $data);
	}

    
    private function initTable()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if(in_array($game_id, $this->area_item_id)){
           $table = Table::init(public_path() . '/table/' . $game->game_code . '/item'.$game_id.'.txt'); 
        }else{
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/item.txt');
        }
        return $table;
    }

	public function addItemToBackpack()
	{
		$msg = array(
			'error' => Lang::get('error.basic_input_error'),
		);
		$server_id = (int)Input::get('server_id');
		$server = Server::find($server_id);
		
		if (!$server) {
			return Response::json($msg, 403);	
		}

		$item_id_name = Input::get('item_id');
		
		$player_name = Input::get('player_name');
		$item_num = (int)Input::get('item_num');

		$rules = array(
		    'item_id' => 'required',
		    'player_name' => 'required',
		    'item_num' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
		    return Response::json($msg, 403);
		}
		$item_id = explode(":", $item_id_name);
		$item_id = (int)$item_id[0];
		$table = $this->initTable();
		$items = $table->getData();
		foreach ($items as $v) {
			$item_ids[] = (int)$v->id;
		}
		if(!in_array($item_id, $item_ids)){
			return Response::json(array('error'=>'错误的礼包！'),403);
		}
		if($item_num<=0 || $item_num>=10000){
			return Response::json($msg, 403);	
		}

		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		$player = $api->getPlayerInfoByName($player_name);
		if (!isset($player->player_id)) {
			$msg['error'] = Lang::get('error.slave_player_not_found');
			return Response::json($msg, 403);	
		}

		$api->addItemToBackpack($item_id, $player->player_id, $item_num);

		return $api->sendResponse();
	}
}