<?php
class CrossServerController extends \BaseController {
    //跨服活动
    public function peoplePKIndex()
	{
		$servers = $this->getUnionServers($no_skip=1);
		$game = Game::find(Session::get('game_id'));
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.activity.crosspeoplepk', array(
				'servers' => $servers,
				'game_code' => $game->game_code
			))
		);
		return View::make('main', $data);
	}

    //全民PK
	public function peoplePK()
	{
		$msg = array(
		    'code' => Config::get('errorcode.unknow'),
		    'error' => Lang::get('error.basic_input_error')
		);
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
        $server_ids = Input::get('server_id');
		$server_id2 = Input::get('server_id2');
		if(!$server_ids || !$server_id2){
			return Response::json($msg, 403);
		}
		$main_server = Server::find($server_id2);
		$url_type = Input::get('url_type');
		if(3 == $url_type){//关闭
			$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
			$response = $api->closePeoplePK();
			if (isset($response->result) && $response->result == 'OK') {
				$result[] = array(
					'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_close') . ': ' . $response->result . "\n",
					'status' => 'ok' 
				);
			} else {
				$result[] = array (
					'msg' => ' ( ' . $main_server->server_name . ' ) ' . Lang::get('serverapi.tournament_world_close') . ': ' . $response->error . "\n",
					'status' => 'error' 
				);
			}
			$msg = array (
				'result' => $result 
			);
			return Response::json($msg);
		}elseif(4 == $url_type){//初始化
			$text_datas = Input::get('text_data');
			$text_datas = explode("\n", $text_datas);
			if(!$text_datas){
				return Response::json($msg, 403);
			}
			foreach ($text_datas as &$v) {
			    $v = trim($v);
			}
			unset($v);
			$text_datas = array_unique($text_datas);
			$api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
			$ok_str = '';
			$error_str = '';
			foreach ($text_datas as $text_data) {
				$response = $api->initPeoplePK($text_data);
				if (isset($response->pkMatch_dragonBall)) {
					$ok_str = $ok_str.$text_data . ',';
				} else {
					$error_str = $error_str.$text_data . ',';
				}
			}
			if('' != $ok_str){
				$result[] = array(
					'msg' => ' ( ' . $ok_str . ' ) OK' . "\n",
					'status' => 'ok' 
				);
			}
			if('' != $error_str){
				$result[] = array(
					'msg' => ' ( ' . $error_str . ' ) error' . "\n",
					'status' => 'error' 
				);
			}
			$msg = array (
				'result' => $result 
			);
			return Response::json($msg);
		}

		//开启和更新
		if (!$main_server) {
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 403);
		}
		$host = $main_server->api_server_ip;
		$port = $main_server->match_port; 
		$result = array();
		$match_type = ('flsg' == $game->game_code) ? 20 : 14;
		foreach ($server_ids as $key => $server_id) {
			$server = Server::find($server_id);
			if (!$server) {
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			//建立连接
			$update_response = $api->updateGameMatch($match_type, $host, $port, true);
			if (isset($update_response->result) && $update_response->result == 'OK') {
				$result[] = array(
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.cross_server_connect') . '['.$host . ':' . $port . ']' . ' : ' . $update_response->result . "\n",
					'status' => 'ok'
				);
			}else{
				$result[] = array (
						'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.cross_server_connect') . '['.$host . ':' . $port . ']' . ' : error' . "\n",
						'status' => 'error'
				);
			}
		}
		if(1 == $url_type){//开启活动
			$main_api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
			$open_response = $main_api->openPeoplePK();
			if (isset($open_response->result) && $open_response->result == 'OK') {
				//Cache::add('cross-warslords-time', $start_time , 100000);
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.cross_server_open') . ': ' . $open_response->result . "\n",
					'status' => 'ok'
				);
			}else{
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . Lang::get('serverapi.cross_server_open') . ': error' . "\n",
					'status' => 'error'
				);
			}
		}
		$msg = array (
			'result' => $result 
		);
		return Response::json($msg);
		
	}
	//设置查看全服等级
	public function allServerLevelIndex(){
		$servers = $this->getUnionServers();
		if(!$servers){
			App::abort(404);
            exit();
		}
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.level.set_level',array(
				'servers' => $servers,
				))
		);
		return View::make('main',$data);
	}

	public function allServerLevel(){
		$server_ids = Input::get('server_id');
		if(!$server_ids){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
		$url_type = Input::get('url_type');
		$aserver_level = Input::get('aserver_level');

		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if(!$server){
				continue;
			}
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			if(2 == $url_type){//查看
				$response = $api->aserverLevelLook();
			}elseif(1 == $url_type){//设置
				if(!$aserver_level){
					return Response::json(array('error'=>'Did you enter level?'), 403);
				}
				$response = $api->aserverLevel($aserver_level);
			}
			if (isset($response->aserver_level)) {
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) ' . $response->aserver_level . "\n",
					'status' => 'ok'
				);
			}else{
				$result[] = array (
					'msg' => ' ( ' . $server->server_name . ' ) error' . "\n",
					'status' => 'error'
				);
			}
		}
		$result = array(
			'result' => $result
		);
		return Response::json($result);
		
	}


}