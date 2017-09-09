<?php

class IPController extends \BaseController {

	public function userIPIndex()
	{
		$server = $this->getUnionServers();
		$data = array(
        	'content' => View::make('serverapi.flsg_nszj.backpack.IP', array('server' => $server)));
        return View::make('main', $data);
	}

	public function userIPData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unkonw'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$rules = array(
			'server_id' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);//验证
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}

		$player_id = Input::get("player_id");
		$server_id = Input::get('server_id');
		$player_name = Input::get('player_name');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		//var_dump($start_time);die();
		$server = Server::find($server_id);
		if (!$server) {
			return Response::json($msg, 403);
		}
		$server_internal_id = $server->server_internal_id;
		$platform_id = Session::get('platform_id');
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);

		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if($player_name){
			$info = $api->getIdByName2($platform_id, $game_id, $server_internal_id, $player_name, $player_id = '');	
		}
		else{
			$info = $api->getIdByName2($platform_id, $game_id, $server_internal_id, $player_name= '', $player_id);
		}
		//var_dump($info);die();
		if (isset($info->body)) {
			$body = $info->body[0];
			//var_dump($body);die();
			$player_id = $body->player_id;
			$player_name = $body->player_name;
			$uid = $body->user_id;
		}
		$result = $api->getIPInfo($platform_id, $game_id, $server_internal_id , $player_id,  $start_time, $end_time);
		//var_dump(isset($result->body));
		//var_dump($result);die();
		if (isset($result->body) && $result->http_code == 200)
		{
			$res = $result->body;
			//Log::info(var_export($res, true));
			for ($i=0; $i < count($res); $i++) {
				$data[$i] = array(
					'uid' => $uid,
					'player_name' => $player_name,
					'player_id' => $player_id,
					'server_name' => $server->server_name,
					'time' => date('Y-m-d H:i:s', $res[$i]->login_time),
					'remote_host' => $res[$i]->remote_host
				);
			}
			//var_dump($data);die();
			return Response::json($data);
		}else {
			$msg['error'] = '没有数据';
			return Response::json($msg);
		}
	}

}