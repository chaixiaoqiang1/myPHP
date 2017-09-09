<?php 

class GiftCodeController extends \BaseController {

	public function create()
	{
		//$servers = Server::currentGameServers()->get();		
		//暂时关闭此功能
		return $this->show_message('401', 'The function is not available now.');	
		$servers = $this->getUnionServers();

		$table = $this->initTable();

		$gifts = $table->getData();

		$data = array(
			'content' => View::make('serverapi.flsg_nszj.giftcode.create', array(
				'servers' => $servers,
				'gifts' => $gifts,
			)),
		);
		return View::make('main', $data);
	}

	private function initTable()
	{
       	$game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/gift.txt');
		return $table;
	}

	public function send()
	{
		return Response::json(array());
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$rules = array(
			'type' => 'required',
			'num' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}
		$game_id = Session::get('game_id');
		$game_to_ip = array(
			1 => '203.74.199.18',
			2 => '123.30.238.15',
			3 => '183.90.168.158',
			4 => '202.158.55.199',
			30 => '119.81.84.117',
		);
		if(in_array($game_id, array(1, 2, 3, 4, 30))){
			$server = Server::where('game_id',$game_id)->where('server_ip',$game_to_ip[$game_id])->where('is_server_on',1)->orderBy('server_id', 'ASC')->first();
		}elseif ($game_id == 37) {
			$server_id = 961;
			$server = Server::find($server_id);
		}elseif(in_array($game_id, array(59, 60, 61, 62, 63))){
			$server = Server::where('game_id',$game_id)->where('server_ip','119.81.84.115')->where('is_server_on',1)->orderBy('server_id', 'ASC')->first();
		}else{
			$server = Server::currentGameServers()->first();
		}
        if (!$server) {
			return Response::json(array('error'=>'Not Found Server'), 403);
		}
		//Log::info("var export server==>".var_export($server, true));
		$num = (int)Input::get('num');
		$type = (int)Input::get('type');

		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);		
		$response = $api->createGiftCode($type, $num);
		//Log::info("create gift code response==>".var_export($response, true));
		$table = $this->initTable();
		if(!isset($response->code_type)){
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 404);
		}
		
		$gifts = $table->getData();
		foreach ($gifts as $k => $v) {
		    if ($v->id == $response->code_type) {
		        $code_name = $v->name;
		        break;
		    }
		}
		$data = array(
			'codes' => $response->codes,
			'code_name' => $code_name,
		);
		return Response::json($data);
	}

	public function index()
	{
		//$servers = Server::currentGameServers()->get();
		try {
			$table = $this->initTable();
		} catch (Exception $e) {
			App::abort(404);
		}

		$gifts = $table->getData();

		$data = array(
			'content' => View::make('serverapi.flsg_nszj.giftcode.index', array(
				'gifts' => $gifts,
			)),
		);
		return View::make('main', $data);
	}

    public function search()
    {
        $type = (int) Input::get('type');
        $gift_code = trim(Input::get('gift_code'));
        $player_id = Input::get('player_id');
        $game_id = Session::get('game_id');
		$game_to_ip = array(
			1 => '203.74.199.18',
			2 => '123.30.238.15',
			3 => '183.90.168.158',
			4 => '202.158.55.199',
			30 => '119.81.84.117',
		);
		if(in_array($game_id, array(1, 2, 3, 4, 30))){
			$code_server = Server::where('game_id',$game_id)->where('server_ip',$game_to_ip[$game_id])->where('is_server_on',1)->orderBy('server_id', 'ASC')->first();
		}elseif ($game_id == 37) {
			$server_id = 961;
			$code_server = Server::find($server_id);
		}elseif(in_array($game_id, array(59, 60, 61, 62, 63))){
			$code_server = Server::where('game_id',$game_id)->where('server_ip','119.81.84.115')->where('is_server_on',1)->orderBy('server_id', 'ASC')->first();
		}else{
			$code_server = Server::currentGameServers()->first();
		}
        if (!$code_server) {
			return Response::json(array('error'=>'Not Found Server'), 403);
		}
        $code_api = GameServerApi::connect($code_server->api_server_ip, $code_server->api_server_port, $code_server->api_dir_id);
        
		$table = $this->initTable();
        $gifts = $table->getData();
        $codes = array();
        if ($gift_code) {
            $response = $code_api->getGiftCodeStatusByCode($gift_code);
        } else {
            if ($player_id) {
                $response = $code_api->getGiftCodeStatusByPlayerID($player_id);
            } else 
                if ($type) {
                    $response = $code_api->getGiftCodeStatusByCodeType($type);
                }
        }
        foreach ($response as $code) {
			if (!isset($code->CodeType)) {
				continue;
			}
            foreach ($gifts as $k => $v) {
                if ($v->id == $code->CodeType) {
                    $code_name = $v->name;
                }
            }
            $player_id = $code->Used_PlayerID;
            $server_internal_id = $code->Used_ServerID;
			$server = Server::where('server_internal_id',$server_internal_id)->where('game_id',$game_id)->first();
            $server_name = $server->server_name;
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $player = $api->getPlayerInfoByPlayerID($player_id);
            if ($player && isset($player->Name)) {
                $player_name = $player->Name;
            } else {
                $player_name = ' ';
            }

            $used_time = $code->Used == 1 ? date("Y-m-d H:i:s", $code->UsedTime) : ' ';
            $code->code_name = $code_name;
            $code->is_used = $code->Used;
            $code->player_name = $player_name;
            $code->server_name = $server_name;
            $code->UsedTime = $used_time;
            $code->Used_PlayerID = $code->Used_PlayerID == 0 ? ' ' : $code->Used_PlayerID;
        }
        return Response::json($response);
    }

}