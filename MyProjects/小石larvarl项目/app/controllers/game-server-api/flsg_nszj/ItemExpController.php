<?php

class ItemExpController extends \BaseController {

	private function initTable($file_name, $area_id = array()){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if (!empty($area_id) && in_array($game_id, $area_id)) {
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.$game_id.'.txt');
        }else {
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        }
        $file_table = $table->getData();
        return $file_table;
    }

	//查看玩家Item
	public function userItemIndex()
	{
		$server = $this->getUnionServers();
		$game = Game::find(Session::get('game_id'));
		try {
			$table = $this->init_item();
		} catch (Exception $e) {
			App::abort(404);
		}
        $item = $table->getData();
        $data = array(
        	'content' => View::make('serverapi.flsg_nszj.backpack.item', array(
        			'server' => $server, 'item' => $item,
        			'game_code' => $game->game_code
        		))
        );
        return View::make('main', $data);
	}

	public function userItemData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unkonw'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$rules = array(
			'server_id' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}
		$item_id = Input::get('item_id');
		$item_name = Input::get('item_name');
		$table = $this->init_item();
        $items = $table->getData();
        foreach ($items as $v) {
        	$item[(isset($v->id) ? $v->id : $v->Id)] = isset($v->name) ? $v->name : $v->Name;
        }
       	if (!$item_id && $item_name) {
       		if(in_array($item_name, $item)){
       			$item_id = array_search($item_name, $item);
       		}
       	}
		$player_id = Input::get("player_id");
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$server_id = Input::get('server_id');
		$server = Server::find($server_id);
		if (!$server) {
			return Response::json($msg, 403);
		}
		$platform_id = Session::get('platform_id');
		$uid = Input::get('uid');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$player_name = Input::get('player_name');
		$page = (int)Input::get('page');
		$page = $page > 0 ? $page : 1;
		$type = 1;
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		if ($item_id < 0 ) {
			return Response::json($msg, 403);
		}
		$result = $api->getExpInfo($platform_id, $game_id, $server->server_internal_id , $player_id, $start_time, $end_time, $type, $item_id, $page, 1000);
		if (isset($result->body) && $result->http_code == 200) {
			$res = $result->body;
			foreach ($res->items as $v) {
				if(isset($item[$v->item_id])){
					$v->item_id = $item[$v->item_id];
				}
				if(1 == $v->type){
					$v->type = '背包';
				}elseif(2 == $v->type){
					$v->type = '仓库';
				}else{
					$v->type = '';
				}
			}
			return Response::json($res);
		}else {
			$msg['error'] = '没有数据';
			return Response::json($msg, 401);
		}
	}
	
	public function userExpIndex()
	{
		//$server = Server::currentGameServers()->get();
		$server = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.backpack.exp', array('server' => $server)),
		);
		return View::make('main', $data);
	}

	public function userExpData()
	{
		$rules = array(
			'server_id' => 'required',
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json(array('error'=>'Did you select a server?'), 403);
		}
		$server_id = Input::get('server_id');
		$server = Server::find($server_id);
		if (!$server) {
			return Response::json(array('error'=>'Can not find server.'), 403);
		}
		$player_id = (int)Input::get('player_id');
		$player_name = Input::get('player_name');
		$server_internal_id = $server->server_internal_id;
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$page = (int)Input::get('page');
		$page = $page > 0 ? $page : 1;
		if(!$player_id && !$player_name){
    		return Response::json(array('error'=>'Please input the player info (at least one).'), 403);
    	}
    	$player_name = is_null($player_name)?'0':$player_name;
    	$player_id = is_null($player_id)?0:$player_id;
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if('0' == $player_id){
	    	$info = $api->getIdByName($platform_id, $game_id, $server->server_internal_id, $player_name, $player_id);
	    	//Log::info(var_export($info->body, true));die();
	    	if (!empty($info->body)) {
				$user = reset($info->body);
				//Log::info(var_export($info->body, true));
				if($user && isset($user->player_id)){
					$player_id = $user->player_id;
					$player_name = $user->player_name;
				}else{
					return Response::json(array('error'=>$player_id.$player_name.' No data from Database.'), 403);
				}
			}else{
				return Response::json(array('error'=>$player_id.$player_name.' No data from Database.'), 403);
			}
		}

		$type = 2;
		$item_id = 0;
		$result = $api->getExpInfo($platform_id, $game_id, $server->server_internal_id , $player_id, $start_time, $end_time, $type, $item_id, $page, 1000);
		if (isset($result)) {
			$res = $result->body;
			try {
				if($res->error){
					return Response::json(array('error'=>$res->error), 403);
				}
			} catch (Exception $e) {
				
			}
		}
		$table = $this->init_message();
        $messages = (array)$table->getData();
        foreach ($messages as $value) {
        	if(isset($value->desc)){
        		$message[$value->id] = $value->desc;
        	}
        }
		if (isset($res)) {
			foreach ($res->items as $v) {
				if(isset($message[$v->action_type])){
					$v->action_type = $message[$v->action_type];
				}
			}
			return Response::json($res);
		} else{
			$msg = array('error'=>'No data.');
			return Response::json($msg, 403);
		}

	}

	public function serverRemainyuanbaoIndex(){
		//$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.backpack.remain',array('servers' => $servers)),
			);
		return View::make('main',$data);
	}

	public function serverRemainyuanbaoData(){
		$server_ids = Input::get('server_id');
		if($server_ids == '0'){
			return Response::json(array('error'=>'Did you select a server? '), 403);
		}

		$game_id = Session::get('game_id');
		$platform_id = Session::get('platform_id');

		$game = Game::find($game_id);
		$min_yuanbao = (int)Input::get('min_yuanbao');
		if($min_yuanbao < 0){
			return Response::json(array('error'=>'元宝下线不能小于0 '), 403);
		}
		$api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key);
		if('flsg' == $game->game_code){
			$remain = 0;
			$player_yuanbao = array();
			foreach ($server_ids as $server_id) 
			{
				$server = Server::find($server_id);
				$server_internal_id = $server->server_internal_id;

				$res_player_ids = $api->getServerRemainPlayer($platform_id, $game_id, $server_internal_id);
				//Log::info("res_player_ids ===============> ".json_encode($res_player_ids));
				if(empty($res_player_ids->body)){
					return Response::json(array('error'=>'No data while get remain players.'), 403);
				}
				$player_id_arr = $res_player_ids->body;
				foreach ($player_id_arr as $key => $value) {
					if(is_null($value->player_id)){
						unset($player_id_arr[$key]);
					}
				}
				$total = 0;
				$index = 1;
				$game_api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
				foreach ($player_id_arr as $player) {
					$player_info_from_id = $game_api->getPlayerInfoByPlayerID($player->player_id);
					/*if($index <= 10)
						Log::info("player info first 10 players===============>".json_encode($player_info_from_id));*/
					if(isset($player_info_from_id->YuanBao) && $player_info_from_id->YuanBao>=$min_yuanbao){
						$yuanbao = $player_info_from_id->YuanBao;
						$total = $total + $yuanbao;
						$player_yuanbao[] = array(
							'server_name' => $server->server_name,
							'player_name' => $player->player_name,
							'player_id' => $player->player_id,
							'yuanbao' => $yuanbao,
						);
					}
					$index++;
					
				}
				$date = (array)$server->created_at;
				if($date){
					if(isset($date['date'])){
						$created_at = $date['date'];
					}else{
						$created_at = '';
					}
				}else{
					$created_at = '';
				}
				
				$result[] = array(
						'server_name' => $server->server_name,
						'is_server_on' => $server->is_server_on=='1'?'ON':'OFF',
						'created_at' => $created_at,
						'server_yuanbao' => $total,
					);
				$remain = $remain + $total;
			}
			$remain_total = array(
				'server_name' => 'TOTAL',
				'is_server_on' => 'TOTAL',
				'created_at' => 'TOTAL',
				'server_yuanbao' => $remain
			);
			if($min_yuanbao != 0){
				$data = array(
					'total' => $remain_total,
					'result' => $result,
					'player' =>$player_yuanbao 
				);
			}else{
				$data = array(
					'total' => $remain_total,
					'result' => $result
				);
			}
		}elseif ('nszj' == $game->game_code) {
			$remain = 0;
			$player_yuanbao = array();
			foreach ($server_ids as $server_id) 
			{
				$server = Server::find($server_id);
				$server_internal_id = $server->server_internal_id;
				$date = (array)$server->created_at;
				if($date){
					$created_at = $date['date'];
				}else{
					$created_at = '';
				}

				$total = 0;

				$game_api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
				$response = $game_api->getremainyuanbao($min_yuanbao);
				$response = (array)$response;
				$response = $response['left_yuanbao'];
				if(is_null($response)){
					$result[] = array(
						'server_name' => $server->server_name,
						'is_server_on' => $server->is_server_on=='1'?'ON':'OFF',
						'created_at' => $created_at,
						'server_yuanbao' => 0,
					);
					continue;
				}
				foreach ($response as $value) {
					$yuanbao = isset($value->yuanbao) ? $value->yuanbao : $value->YuanBao;
					$total = $total + $yuanbao;
					$player_yuanbao[] = array(
						'server_name' => $server->server_name,
						'player_name' => isset($value->player_name) ? $value->player_name : '',
						'player_id' => isset($value->player_id) ? $value->player_id : $value->PlayerID,
						'yuanbao' => $yuanbao,
					);
					
				}
				
				$result[] = array(
						'server_name' => $server->server_name,
						'is_server_on' => $server->is_server_on=='1'?'ON':'OFF',
						'created_at' => $created_at,
						'server_yuanbao' => $total,
					);
				$remain = $remain + $total;
			}
			$remain_total = array(
				'server_name' => 'TOTAL',
				'is_server_on' => 'TOTAL',
				'created_at' => 'TOTAL',
				'server_yuanbao' => $remain
			);
			if($min_yuanbao != 0){
				$data = array(
					'total' => $remain_total,
					'result' => $result,
					'player' =>$player_yuanbao 
				);
			}else{
				$data = array(
					'total' => $remain_total,
					'result' => $result
				);
			}
		}else{
			return Response::json(array('error'=>'本游戏暂不支持此功能'), 403);
		}
		
		return Response::json($data);
	}
	
	private function init_item()
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
    private function init_message()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/game_message.txt');
        return $table;
    }

    private function init_dragon()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'flsg' . '/dragonballlevel.txt');
        return $table;
    }

    public function dragonBallIndex()
    {
    	$servers = $this->getUnionServers();
    	$table = $this->init_dragon();
        $dragon = $table->getData();
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.backpack.dargon', array('server'=> $servers, 'dragon' => $dragon))
    	);
    	return View::make('main', $data);
    }

     public function dragonBallData()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error'=> ''
    	);

    	$rules = array(
    		'server_id' => 'required',
    		'start_time' => 'required',
    		'end_time' => 'required',
    	);

    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg['error'] = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$server_id = Input::get('server_id');
    	$player_name = Input::get('player_name');
    	$player_id = Input::get('player_id');
    	$start_time = strtotime(Input::get('start_time'));
    	$end_time = strtotime(Input::get('end_time'));
    	$type = Input::get('dragon_id');
        $dragons = $this->initTable('dragonballlevel');
        foreach ($dragons as $d) {
        	$dragon[$d->id] = $d->ballname;
        }
        $messages = $this->initTable('game_message');
        foreach($messages as $m){
            $message[$m->id] = $m->desc;
        }
    	$game_id = session::get('game_id');
    	$game = Game::find($game_id);
    	$platform_id = Session::get('platform_id');
    	$server = Server::find($server_id);
    	$types = array(
    		'1' => '玩家上阵类型',
    		'2' => '红颜技背包类型',
    		'3' => '红颜技仓库类型',
    		'4' => '红颜技客栈类型',
    	);
    	if (!$server) {
    		$msg['error'] = 'error.basic_not_found';
    		return Response::json($msg, 403);
    	}
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	if(!$player_id && !$player_name){
    		return Response::json(array('error'=>'Please input the player info (at least one).'), 403);
    	}
    	$player_name = is_null($player_name)?'0':$player_name;
    	$player_id = is_null($player_id)?0:$player_id;
    	$info = $api->getIdByName($platform_id, $game_id, $server->server_internal_id, $player_name, $player_id );
    	if (!empty($info->body)) {
			$body = reset($info->body);
			$player_id = $body->player_id;
			$player_name = $body->player_name;
		}else{
			return Response::json(array('error'=>$player_id.$player_name.' No data from Database.'), 403);
		}

    	$response = $api->getDragonLog($game_id, $server->server_internal_id, $player_id, $start_time, $end_time, $type); 
    	//var_dump($response);die();
    	$data = array();
    	if (!empty($response->body)) 
    	{
    		$body = $response->body;
    		foreach ($body as $key => $value) 
    		{
    			$arr = explode(",", $value->dragon_balls);
    			for ($i=0; $i < count($arr); $i++) { 
    				if ($arr[$i] == '') {
    					unset($arr[$i]);
    				}
    			}
    			foreach ($arr as $val) 
    			{
    				$arr2 = explode(":", $val);
    				if ($type) 
    				{
    					if ($arr2[0] == $type) 
    					{
		    				$data[] = array(
			    				'player_id' => $player_id,
			    				'player_name' => $player_name,
			    				'dragon_type' => $types[$value->container_type],
			    				'time' => date("Y-m-d H:i:s", $value->time),
			    				'action_type' => isset($message[$value->action_type]) ? $message[$value->action_type] : $value->action_type,
			    				'dragon_balls' => isset($dragon[$arr2[0]]) ? $dragon[$arr2[0]] : $arr2[0],
			    				'dragon_exp' => isset($arr2[1]) ? $arr2[1] : "0",
			    				'dragon_level' => isset($arr2[2]) ? $arr2[2] : '0',
			    			);	
	    				}
    				}else 
    				{
	    				$data[] = array(
		    				'player_id' => $player_id,
		    				'player_name' => $player_name,
		    				'dragon_type' => $types[$value->container_type],
		    				'time' => date("Y-m-d H:i:s", $value->time),
		    				'action_type' => isset($message[$value->action_type]) ? $message[$value->action_type] : $value->action_type,
		    				'dragon_balls' => isset($dragon[$arr2[0]]) ? $dragon[$arr2[0]] : $arr2[0],
		    				'dragon_exp' => isset($arr2[1]) ? $arr2[1] : "0",
		    				'dragon_level' => isset($arr2[2]) ? $arr2[2] : '0',
		    			);
    				}
    			}
    		}
	    	return Response::json($data);
    	}
    	else {
    		$msg['error'] = "没有数据";
    		return Response::json($msg, 403);
    	}
    	
    }

    //查看封号禁言玩家

    public function freezePlayerIndex()
    {
    	$servers = $this->getUnionServers();
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.backpack.freeze', array('servers' => $servers))
    	);
    	return View::make('main', $data);
    }

    public function freezePlayerData()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error' => '',
    	);
    	$rules = array(
    		'server_id' => 'required'
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg['error'] = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$server_id = trim(Input::get('server_id'));
    	$player_name = trim(Input::get('player_name'));
    	$player_id = trim(Input::get('player_id'));
    	$type = trim(Input::get('type'));
    	$server = Server::find($server_id);
    	$game_id = Session::get('game_id');
    	if (!$server) {
    		$msg['error'] = Lang::get('error.server_not_found');
    		return Response::json($msg, 403);
    	}
    	switch ($type) {
    		case '1':
    			$type = "freeze";
    			break;
    		
    		case '2':
    			$type = 'banner';
    			break;

    		default:
    			$type = "freeze";
    			break;
    	}
    	if ($player_name) {
    		$info = EastBlueLog::where('game_id', $game_id)->where('log_key', $type)->where('platform_uid', $player_name)->where('old_value', $server->server_name)->get();
    		//$info = EastBlueLog::select("select * from log where log_key = {$type} and game_id = {$game_id} and platform_uid = {$player_name} and old_value = {$server->server_name}");
    	}elseif ($player_id) {
    		$info = EastBlueLog::where('game_id', $game_id)->where('log_key', $type)->where('desc', $player_id)->where('old_value', $server->server_name)->get();
    	}
    	$data = array();
    	if (isset($info)) {
    		foreach ($info as $key => $value) {
				$user = SlaveUser::where('user_id', $value->user_id)->first();
    			$date = $value->created_at;
		    	$dd = explode(',', $date);
		    	if ($value->log_key == "freeze") {
		    		$action_type = "封号";
		    	}elseif ($value->log_key == "banner") {
		    		$action_type = "禁言";
		    	}
    			$data[] = array(
		    		'server_name' => $value->old_value,
		    		'player_id' => $value->desc,
		    		'player_name' => $value->platform_uid,
		    		'days' => $value->new_value,
		    		'operater' => isset($user->username) ? $user->username : Auth::user()->username,
		    		'date' => $dd[0],
		    		'type' => $action_type,
		    	);
		    	
		    	
    		}
    	}
    	
    	if (!empty($info)) {
    		return Response::json($data);
    	}else{
    		$msg['error'] = Lang::get('error.basic_not_found');
    		return Response::json($msg, 403);
    	}

    }

    public function inviteFriendIndex()
    {
    	$servers = $this->getUnionServers();
    	$table = $this->init_partner();
        $partner = $table->getData();
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.backpack.partner', array('servers'=> $servers, 'partner' => $partner))
    	);
    	return View::make('main', $data);
    }

    
    public function inviteFriendAction()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error' => ''
    	);
    	$rules = array(
    		'server_id' => 'required',
    		//'partner_id' => 'required',
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg['error'] = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$server_ids = Input::get('server_id');
    	$partner_ids = Input::get('partner_id');
    	$type = Input::get('action');
    	$table = $this->init_partner();
        $part = $table->getData();
        $part = (array)$part;
        $result = array();
    	foreach ($server_ids as $key => $server_id) {
    		$server = Server::find($server_id);
    		if (!$server) {
    			$msg['error'] = Lang::get('error.basic_not_found');
    			return Response::json($msg, 403);
    		}
    		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
    		if ($type == 'look') { //查看
    			$response = $api->partnerOperate($partner='',$type);
    			//var_dump($response);die();
    			if ($response->active === true) { //查询成功
    				$partners = $response->partners;
    				$arr = explode(',', $partners);
    				foreach ($part as $key => $value) {
    					for ($i=0; $i < count($arr); $i++) { 
    						if ($arr[$i] == $value->id) {
    							$result[] = array(
			    					'status' => "OK",
			    					'msg' => '伙伴'.'  '.$value->name.'  ('.$arr[$i] . ')  ' . '已开启' . '==' . $response->active
			    				);
    						}
    					}
    				}
    			}elseif ($response->active === false) {
					$result[] = array(
    					'status' => "error",
    					'msg' => '没有伙伴开启'
    				);		
    			}
    		}else{
    			$partner = implode(',', $partner_ids);
    			$response = $api->partnerOperate($partner, $type);
    			if ($response->active === true ) { // 开启成功
    				$str = $response->partners;
    				$arr = explode(',', $str);
    				foreach ($part as $key => $value) {
    					for ($i=0; $i < count($arr); $i++) { 
    						if ($arr[$i] == $value->id) {
    							$result[] = array(
			    					'status' => "OK",
			    					'msg' => '伙伴'.'  '.$value->name.'  ('.$arr[$i] . ')  ' . '开启成功' . '==' . $response->active
			    				);
    						}
    					}
    				}
    			}elseif ($response->active === false) {
    				$str = $partner;
    				$arr = explode(',', $str);
    				foreach ($part as $key => $value) {
    					for ($i=0; $i < count($arr); $i++) { 
    						if ($arr[$i] == $value->id) {
    							$result[] = array(
			    					'status' => "OK",
			    					'msg' => '伙伴'.'  '.$value->name.'  ('.$arr[$i] . ')  ' . '关闭成功' . '==' . $response->active
			    				);
    						}
    					}
    				}
    			}else{
    				foreach ($part as $key => $value) {
    					for ($i=0; $i < count($arr); $i++) { 
    						if ($arr[$i] == $value->id) {
    							$result[] = array(
			    					'status' => "error",
			    					'msg' => '伙伴'.'  '.$value->name.'  ('.$arr[$i] . ')  ' . '操作失败' . '==' 
			    				);
    						}
    					}
    				}
    			}
    		}
    	}
    	if (isset($result)) {
    		return Response::json($result);
    	}else{
    		$msg['error'] = 'Something wrong';
    		return Response::json($msg, 403);
    	}
    }

    public function oneKeyIndex(){
    	$servers = $this->getUnionServers();
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.backpack.onekey', array('servers' => $servers))
    	);
    	return View::make('main', $data);
    }

    public function oneKeyOperate()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error'=>''
    	);
    	$rules = array(
    		'content' => 'required',
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg['error'] = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$action_type = Input::get('action_type');
    	$content = Input::get('content');
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id); 
    	$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$data = explode("\n", $content);
    	$platform_id = Session::get('platform_id');
    	$result = array();
    	if ($action_type==1) { //玩家昵称操作
    		foreach ($data as $key => $value) {
    			$arr = explode("\t", $value);
    			$server = DB::table('servers')->where('game_id', $game_id)->where('server_track_name', $arr[0])->first();
    			//$server = Server::whereRaw("game_id = {$game_id} and server_track_name = '{$arr[0]}'")->get();
    			// Log::info(var_export($server, true));
    			if (!$server) {
    				$result[] = array(
						'status' => 'error',
						'msg' => '服务器 ' . $arr[0] . '  未找到--' , 
					);
    			}
    			$server_internal_id = $server->server_internal_id;
    			$user = $slave_api->getCreatePlayer_xs($platform_id, $game_id, $arr[1], $player_id='', $uid = '', $server_internal_id);
    			if ($user->http_code == 200 && !empty($user->body)) {
    				$user = $user->body;
    				$player_id = $user[0]->player_id;
    				$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
					$response = $api->oneKeyOperate($player_id);
					//Log::info(var_export($response, true));
					if ($response->result == 'OK') {
						$result[] = array(
							'status' => 'OK',
							'msg' => '玩家  ' . $arr[1] . ' (' . $player_id . ' )  操作成功--' . $response->result, 
						);
					}else{
						$result[] = array(
							'status' => 'error',
							'msg' => '玩家  ' . $arr[1] . $player_id . '  操作失败--' , 
						);
					}
    			}else{
    				$msg['error'] = 'player not found';
    				return Response::json($msg, 403);
    			}
    			unset($api);
    			unset($player_id);
    			unset($response);
    			
    		}
    		return Response::json($result); 
    	}elseif ($action_type == 2) { //player_id 操作
    		foreach ($data as $key => $value) {
    			$arr = explode("\t", $value);
    			$server = DB::table('servers')->where('game_id', $game_id)->where('server_track_name', $arr[0])->first();
    			//Log::info(var_export($server, true));
    			if (!$server) {
    				$result[] = array(
						'status' => 'error',
						'msg' => '服务器 ' . $arr[0] . '  未找到--' , 
					);
    			}
    			//$server_internal_id = $server->server_internal_id;
    			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
				$response = $api->oneKeyOperate($arr[1]);
				//Log::info(var_export($response, true));
				if ($response->result == 'OK') {
					$result[] = array(
						'status' => 'OK',
						'msg' => '玩家  ' .'  '. $arr[1] . '   操作成功--' . $response->result, 
					);
				}else{
					$result[] = array(
						'status' => 'error',
						'msg' => '玩家  ' .'  '. $arr[1] . '  操作失败--' , 
					);
				}
    		}
    		return Response::json($result); 
    	}
   			
    }

    public function itemActivityIndex()
	{
		$servers = $this->getUnionServers();
		$table = $this->init_item();
        $items = $table->getData();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.backpack.item2', array('servers' => $servers, 'items' => $items))
		);
		return View::make('main', $data);
	}

	public function itemActivityOperate()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		); 
		$rules = array(
			'server_id' => 'required',
			'item_id' => 'required',
			//'item_num' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$server_ids = Input::get('server_id');
		$item_id = (int)Input::get('item_id');
		$item_num = Input::get('item_num');
		$item = $item_id . ":" . $item_num;
		$open_time = strtotime(trim(Input::get('start_time')));
		$close_time = strtotime(trim(Input::get('end_time')));
		$type = 37;
		$time_arr1 = getdate($open_time);
	    $time_arr2 = getdate($close_time);

	    if (($time_arr1['hours'] == 23 && $time_arr1['minutes'] >=51) || $time_arr1['hours'] == 0 && $time_arr1['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		if (($time_arr2['hours'] == 23 && $time_arr2['minutes'] >=51) || $time_arr2['hours'] == 0 && $time_arr2['minutes'] <= 9) {
			$msg['error'] = Lang::get('serverapi.lucky_dog_time_error');
			return Response::json($msg, 403);
		}
		
		$action = Input::get('action');
		$result = array();
		$item_name = array(
			'30300894' => Lang::get('serverapi.xiaonangua'),
			'30300895' => Lang::get('serverapi.xiohuoji'),
			'30301260' => Lang::get('serverapi.zongzi'),
			'30300175' => Lang::get('serverapi.xinnianhongbao'),
		);
  
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			if ($action == 'open') {
				if ($item_num == 0) {
					$msg['error'] = Lang::get('error.basic_input_error');
					return Response::json($msg, 403);
				}
				$response = $api->itemActivity($open_time, $close_time, $type, $item);
				if (isset($response->result) && $response->result == 'OK') {
					$result[] = array(
						'status' => 'OK',
						'msg' =>  $server->server_track_name  .'--'. $item_name[$item_id]. ' (' . $item_id . ' )  操作成功--' . $response->result, 
					);
				}else{
					$result[] = array(
						'status' => 'error',
						'msg' => $server->server_track_name .'--'.$item_name[$item_id] .'(' . $item_id . ')   操作失败' 
					);
				}
			}
			if ($action == "look") {
				$response = $api->lookupActivity($type);
				$acts = $response->activities;
				foreach ($acts as $key => $value) {
					if ($value->type == 37 ) {
						if ($value->is_open === true) {
							$result[] = array(
								'status' => 'OK',
								'msg' => $server->server_track_name . '   已开启  --开启时间 :' . date('Y-m-d H:i:s', $value->open_time) .' 关闭时间:' . date('Y-m-d H:i:s', $value->close_time)
							);
						} elseif ($value->is_open == false) {
							$result[] = array(
								'status' => 'OK',
								'msg' => $server->server_track_name . '   未开启  --开启时间 :' . date('Y-m-d H:i:s', $value->open_time) .' 关闭时间:' . date('Y-m-d H:i:s', $value->close_time) 
							);
						} else{
							$result[] = array(
								'status' => 'error',
								'msg' => $server->server_track_name .'--   操作失败'  
							);
						}
						break;
					} 
				}

			}
			if ($action == 'close') {
				$response = $api->closeActivity($type);
				if ($response->result == "OK") {
					$result[] = array(
						'status' => 'OK',
						'msg' => $server->server_track_name .'--   关闭成功' .$response->result 
					);
				}else{
					$result[] = array(
						'status' => 'OK',
						'msg' => $server->server_track_name .'--   操作失败' 
					);
				}
			}
			unset($api);
			unset($response);
		}
		return Response::json($result);

	}

	/*
	仙界运粮   created_by  xianshi 2014.11.14
	*/
	public function heavenGrainIndex()
	{
		$servers = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.tournament.grain', array('server' => $servers))
		);
		return View::make('main', $data);
	}
	public function heavenGrainOperate()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'server_id' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$game = Game::find(Session::get('game_id'));
		$action = Input::get('action');

		if('nszj' == $game->game_code){
			switch ($action) {
				case 'open':
					$type = 0xbc7f;
					break;
				
				case 'close':
					$type = 0xbc80;
					break;

				case 'look':
					$type = 0xbc81;
					break;
				
			}
		}else{
			switch ($action) {
				case 'open':
					$type = 0xbc4d;
					break;
				
				case 'close':
					$type = 0xbc4e;
					break;

				case 'look':
					$type = 0xbc4f;
					break;
				
			}
		}
		$server_ids = Input::get('server_id');
		$success = "";
		$fail = "";
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
			$server_track_name = isset($server->server_track_name) ? $server->server_track_name : ''; 
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$result = $api->heavenStudio($type);
		
			if ($action == 'look') {
				if (isset($result->is_open) && $result->is_open == true) {
					$success .= "--(" . $server_track_name . '--已开启--  '.$result->is_open.' )--';
				}else{
					$fail .= "--(" . $server_track_name . '--Fail-- )--';
				}
			}else{
				if (isset($result->result) && $result->result == 'OK') {
					$success .= "--(" . $server_track_name . '--OK--  '.$result->result.' )--';
				}else{
					$fail .= "--(" . $server_track_name . '--Fail--)--';
				}
			}
			unset($api);
			unset($result);
		}
		$result1 = array(
			'status' => 'ok',
			'msg' => $success,
		);
		$result2 = array(
			'status' => 'error',
			'msg' => $fail
		);
		$data = array(
			'result1' => $result1,
			'result2' => $result2
		);
		return Response::json($data);
	} 


    private function init_partner()
    {
        $table = Table::init(public_path() . '/table/' . 'nszj' . '/partner.txt');
        return $table;
    }

    public function serverItemIndex()
	{
		$server = $this->getUnionServers();
		$table = $this->init_item();
        $item = $table->getData();
        $data = array(
        	'content' => View::make('serverapi.flsg_nszj.backpack.server_item', array('server' => $server, 'item' => $item))
        );
        return View::make('main', $data);
	}

	public function serverItemData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unkonw'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$rules = array(
			'server_id' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}
		$item_id = Input::get('item_id');
		$table = $this->init_item();
        $item = (array)$table->getData();
       	if (!$item_id) {
       		foreach ($item as  $value) {
	       		if ($value->name == $item_name) {
	       			$item_id = $value->id;
	       		}
       		}
       	}
		$server_id = Input::get('server_id');
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
		$data = array();
		if ($item_id < 0 ) {
			return Response::json($msg, 403);
		}
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if($server_id != 0){
			$server = Server::find($server_id);
			if (!$server) {
				return Response::json($msg, 403);
			}
			$server_internal_id = $server->server_internal_id;
			$result = $api->getPlayerServerInfo($platform_id, $game_id, $server_internal_id,$item_id,$start_time,$end_time);
			if (isset($result->body) && $result->http_code == 200) {
				$body = $result->body;
				foreach ($body as $key => $value) {
					$data[] = array(
					     'player_id' =>$value->player_id,
					     'server_name' => $server->server_name
					);
				}
				return Response::json($data);
			}else {
				$msg['error'] = '没有数据';
				return Response::json($msg);
			}
		}else{
			$server = $this->getUnionServers();
			foreach ($server as $key => $value) {
				$server2 = Server::find($value->server_id);
				$server_internal_id=$value->server_internal_id;
				$result = $api->getPlayerServerInfo($platform_id, $game_id, $server_internal_id,$item_id,$start_time,$end_time);
				if (isset($result->body) && $result->http_code == 200) {
					$body = $result->body;
					foreach ($body as $key => $value) {
						$data[] = array(
						     'player_id' =>$value->player_id,
						    'server_name' => $server2->server_name
						);
					}
				}
			}
			if(isset($data)){
				return Response::json($data);
			}else {
				$msg['error'] = '没有数据';
				return Response::json($msg);
			}

		}
		
	}
	public function downloadServerItemIndex()
    {
        $now = Input::get('now');
        $file = storage_path() . "/cache/" . $now . ".csv";
        $data = array(
                'content' => View::make('download', 
                        array(
                                'file' => $file
                        ))
        );
        return View::make('main', $data);
    }

    public function downloadServerItemData()
    {
       $msg = array(
       			'code' => Config::get('errorcode.unkonw'),
       			'error' => Lang::get('error.basic_input_error'),
       		);
       		$rules = array(
       			'server_id' => 'required',
       		);
       		$validator = Validator::make(Input::all(), $rules);
       		if ($validator->fails()) {
       			return Response::json($msg, 403);
       		}
       		$item_id = Input::get('item_id');
       		$table = $this->init_item();
               $item = (array)$table->getData();
              	if (!$item_id) {
              		foreach ($item as  $value) {
       	       		if ($value->name == $item_name) {
       	       			$item_id = $value->id;
       	       		}
              		}
              	}
       		$server_id = Input::get('server_id');
       		$platform_id = Session::get('platform_id');
       		$game_id = Session::get('game_id');
       		$game = Game::find($game_id);
       		$start_time = strtotime(Input::get('start_time'));
        	$end_time = strtotime(Input::get('end_time'));
       		$data = array();
       		if ($item_id < 0 ) {
       			return Response::json($msg, 403);
       		}
       		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $title = array(
                Lang::get("slave.player_id"),
                Lang::get("slave.server_name"),
        );
        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";
        $csv = CSV::init($file, $title);
        if($server_id != 0){
        	$server = Server::find($server_id);
        	if (!$server) {
        		return Response::json($msg, 403);
        	}
        	$server_internal_id = $server->server_internal_id;
        	$result = $api->getPlayerServerInfo($platform_id, $game_id, $server_internal_id,$item_id,$start_time,$end_time);
        	if (isset($result->body) && $result->http_code == 200) {
        		$body = $result->body;
        		foreach ($body as $key => $value) {
        			$data = array(
        			     'player_id' =>$value->player_id,
        			     'server_name' => $server->server_name
        			);
        			$csv->writeData($data);
        		}
        	}
        }else{
        	$server = $this->getUnionServers();
        	foreach ($server as $key => $value) {
        		$server2 = Server::find($value->server_id);
        		$server_internal_id=$value->server_internal_id;
        		$result = $api->getPlayerServerInfo($platform_id, $game_id, $server_internal_id,$item_id,$start_time,$end_time);
        		if (isset($result->body) && $result->http_code == 200) {
        			$body = $result->body;
        			foreach ($body as $key => $value) {
        				$data= array(
        				     'player_id' =>$value->player_id,
        				    'server_name' => $server2->server_name
        				);
        				$csv->writeData($data);
        			}
        		}
        	}
        }
        $res = $csv->closeFile();
        if ($res)
        {
            $data = array(
                    'now' => $now
            );
            return Response::json($data);
        } else
        {
            return Response::json($msg, 403);
        }
    }
    private function init_mingge()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/xinxiu.txt');
        return $table;
    }
     public function mingGeIndex()
    {
    	$servers = $this->getUnionServers();
    	$table = $this->init_mingge();
        $mingge = $table->getData();
    	$data = array(
    		'content' => View::make('serverapi.flsg_nszj.backpack.mingge', array('server'=> $servers, 'mingge' => $mingge))
    	);
    	return View::make('main', $data);
    }

    public function mingGeData()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error'=> ''
    	);

    	$rules = array(
    		'server_id' => 'required',
    		'start_time' => 'required',
    		'end_time' => 'required',
    	);

    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg['error'] = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$server_id = Input::get('server_id');
    	$player_name = Input::get('player_name');
    	$player_id = Input::get('player_id');
    	$start_time = strtotime(trim(Input::get('start_time')));
    	$end_time = strtotime(trim(Input::get('end_time')));
    	$type = (int)Input::get('mingge_id');
        $mingge = $this->initTable('marklevel');
    	$game_id = session::get('game_id');
    	$game = Game::find($game_id);
    	$platform_id = Session::get('platform_id');
    	$server = Server::find($server_id);
    	$action_types = array(
    		'1796' => '背包到角色',
    		'1797' => '角色到背包',
    		'1798' => '背包到背包',
    		'1799' => '角色到角色',
    		'1813' => '从背包丢弃',
    		'1814' => '从角色丢弃',
    		'1832' => '到分解界面',
    		'1833' => '离开分解界面',
    		'1838' => '分解',
    	);
    	if (!$server) {
    		$msg['error'] = 'error.basic_not_found';
    		return Response::json($msg, 403);
    	}
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	if(is_null($player_id) && is_null($player_name)){
    		return Response::json(array('error'=>'Please input the player info (at least one).'), 403);
    	}
    	$player_name = is_null($player_name)?'0':$player_name;
    	$player_id = is_null($player_id)?0:$player_id;
    	if(!$player_id){
	    	$info = $api->getIdByName($platform_id, $game_id, $server->server_internal_id, $player_name, $player_id );
	    	if (!empty($info->body)) {
				$body = reset($info->body);
				//Log::info(var_export($body, true));
				$player_id = $body->player_id;
				$player_name = $body->player_name;
			}else{
				return Response::json(array('error'=>$player_id.$player_name.' No data from Database，如果是合服后的从服玩家请用id查询'), 403);
			}
		}

    	$response = $api->getMingGeLog($game_id, $server->server_internal_id, $player_id, $start_time, $end_time, $type); 
    	//var_dump($response);die();
    	$data = array();
    	if (!empty($response->body)) 
    	{
			$mingge_name = array();
			foreach ($mingge as $k => $v) {
				$mingge_name[$v->markid] = array(
					'markname' => $v->markname,
					'lev' => explode("|", $v->levelupexp)
				); 
			}
    		$body = $response->body;
    		foreach ($body as $key => $value) 
    		{
    			if(isset($mingge_name[$value->from_id])){
    				if(end($mingge_name[$value->from_id]['lev']) < $value->from_exp){
    					$mingge_name1 = (count($mingge_name[$value->from_id]['lev'])-1) . '级 ' . $mingge_name[$value->from_id]['markname'];
    				}else{
    					foreach ($mingge_name[$value->from_id]['lev'] as $k2 => $v2) {
    						if($value->from_exp < $v2){
    							$mingge_name1 = $k2 . '级 ' . $mingge_name[$value->from_id]['markname'];break;
    						}
    					}
    				}
    				
    			}else{
    				$mingge_name1 = $value->from_id;
    			}
    			if(isset($mingge_name[$value->to_id])){
    				if(end($mingge_name[$value->to_id]['lev']) < $value->to_exp){
    					$mingge_name2 = (count($mingge_name[$value->to_id]['lev'])-1) . '级 ' . $mingge_name[$value->to_id]['markname'];
    				}else{
    					foreach ($mingge_name[$value->to_id]['lev'] as $k3 => $v3) {
    						if($value->to_exp < $v3){
    							$mingge_name2 = $k3 . '级 ' . $mingge_name[$value->to_id]['markname'];break;
    						}
    					}
    				}	
    			}else{
    				$mingge_name2 = $value->to_id;
    			}

    			$data[] = array(
    				'server_name' => $server->server_name,
    				'player_id' => $player_id,
    				'player_name' => $player_name,
    				'time' => date("Y-m-d H:i:s", $value->action_time),
    				'mingge_name1' => $mingge_name1,
    				'mingge_name2' => $mingge_name2,
    				'from_exp' => $value->from_exp,
    				'to_exp' => $value->to_exp,
    				'action_type' =>isset($action_types[$value->action_type]) ? $action_types[$value->action_type] : $value->action_type,
    			);
    		}
	    	return Response::json($data);
    	}
    	else {
    		$msg['error'] = "没有数据";
    		return Response::json($msg, 403);
    	}
    	
    } 
    public function userLonelyExpIndex()
	{
		//$server = Server::currentGameServers()->get();
		$server = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.backpack.lonely_exp', array('server' => $server)),
		);
		return View::make('main', $data);
	}

	public function userLonelyExpData()
	{
		$rules = array(
			'server_id' => 'required',
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			return Response::json(array('error'=>'Did you select a server?'), 403);
		}
		$server_id = Input::get('server_id');
		$server = Server::find($server_id);
		if (!$server) {
			return Response::json(array('error'=>'Can not find server.'), 403);
		}
		$player_id = (int)Input::get('player_id');
		$player_name = Input::get('player_name');
		$server_internal_id = $server->server_internal_id;
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		if(is_null($player_id) && is_null($player_name)){
    		return Response::json(array('error'=>'Please input the player info (at least one).'), 403);
    	}
    	$player_name = is_null($player_name)?'0':$player_name;
    	$player_id = is_null($player_id)?0:$player_id;
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$info = $api->getIdByName($platform_id, $game_id, $server->server_internal_id, $player_name, $player_id );
    	if (!empty($info->body)) {
			$user = reset($info->body);
			//Log::info(var_export($info->body, true));
			$player_id = $user->player_id;
			$player_name = $user->player_name;
		}else{
			return Response::json(array('error'=>$player_id.$player_name.' No data from Database.'), 403);
		}
		$result = $api->getLonelyExpInfo($platform_id, $game_id, $server->server_internal_id , $player_id, $start_time, $end_time);
		if (isset($result)) {
			$res = $result->body;
			try {
				if($res->error){
					return Response::json(array('error'=>$res->error), 403);
				}
			} catch (Exception $e) {
				
			}
		}
		$table = $this->init_message();
        $message = (array)$table->getData();
        //Log::info(var_export($res, true));
		if (isset($res)) {
			for ($i=0; $i < count($res); $i++) { 
				$data[$i] = array( 
					'uid' => $user->user_id,
					'player_name' => $user->player_name,
					'player_id' => $player_id,
					'server_name' => $server->server_name,
					'time' => date("Y-m-d H:i:s", $res[$i]->action_time),
					'exp' => $res[$i]->exp,
					'action_type' => $res[$i]->action_type
				);
				foreach ($message as $value) {
					if ($value->id == $res[$i]->action_type) {
						$data[$i]['action_name'] = $value->desc;
					}
				}
				
			}
			return Response::json($data);
		} else{
			$msg = array('error'=>'No data.');
			return Response::json($msg, 403);
		}

	}


}