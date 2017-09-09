<?php
class TestController extends \BaseController {

	public function testIndex(){	//用来测试游戏接口或者使用一次性借口
		$servers = $this->getUnionServers();
		$delete = DB::table('test_mids')->where('valid_time', '<', time())->delete();	//删除过期可测试的mid
		$data = array(
				'content' => View::make('serverapi.mid_test', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}

	public function testDeal(){
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$servers = Input::get('server_ids');
		if(is_array($servers) && count($servers)){
			if(in_array(0, $servers)){	//全服
				$servers = $this->getUnionServers();
			}else{
				$servers = Server::find($servers);
			}
		}else{
			return Response::json(array('error' => 'No server selected!'), 403);
		}

		$data2post = array();

		$mid = (int)Input::get('mid');
		if(!$mid){
			return Response::json(array('error' => 'No mid input!'), 403);
		}

		$available = DB::table('test_mids')->where('mid', $mid)->where('game_id', $game_id)->first();
		if(!$available){
			return Response::json(array('error' => 'Mid not available!'), 403);
		}

		for($i = 0; $i <=6 ; $i++){
			$key_name = 'key'.$i;
			$value_name = 'value'.$i;
			if(0 == $i){
				$$value_name = (int)Input::get('value'.$i);
			}else{
				$$value_name = Input::get('value'.$i);
			}
			$$key_name = Input::get('key'.$i);
			if(0 == $i && 0 == $$value_name && '' == $$key_name){
				continue;
			}
			if($$key_name && $$value_name){
				$data2post[$$key_name] = $$value_name;
			}else{
				break;
			}
			unset($key_name);
			unset($value_name);
		}

		$array_key = Input::get('array_key');
		$array_value = Input::get('array_value');
		if($array_value){
			$array_value = explode("\n", $array_value);
		}
		if($array_key){
			$data2post[$array_key] = $array_value ? $array_value : array();
		}

		$array_key_value_key = Input::get('array_key_value_key');
		$array_key_value_value = Input::get('array_key_value_value');
		if($array_key_value_value){
			$tmp = explode("\n", $array_key_value_value);
			$array_key_value_value = array();
			foreach ($tmp as $value) {
				$key2value = explode(' ', $value, 2);
				$array_key_value_value[$key2value[0]] = $key2value[1];
				unset($value);
			}
		}
		if($array_key_value_key){
			$data2post[$array_key_value_key] = $array_key_value_value ? $array_key_value_value : array();
		}

		$loop_key = Input::get('loop_key');
		$loop_value = Input::get('loop_value');
		if($loop_value){
			$loop_value = explode("\n", $loop_value);
		}

		$result = array();

		Log::info("TestMid-".$mid);
		foreach ($servers as $server) {
			if(1 == $game->game_type){	//页游
				$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);   
			}else{
				$api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
			}
			if($loop_key){
				foreach ($loop_value as $value) {
					$data2post[$loop_key] = $value;
					$tmp_result = $api->testmid($mid, $data2post);
					$tmp_result = $server->server_name.'--'.$loop_key.'='.$value.'--'.var_export($tmp_result, true);
					Log::info('Mid---test---'.$mid.'-----'.$tmp_result);
					$result[] = $tmp_result;
					unset($tmp_result);
					unset($data2post[$loop_key]);
				}
			}else{
				$tmp_result = $api->testmid($mid, $data2post);
				$tmp_result = $server->server_name.'--'.var_export($tmp_result, true);
				Log::info('Mid---test---'.$mid.'-----'.$tmp_result);
				$result[] = $tmp_result;
				unset($tmp_result);
			}
		}

		return Response::json(array('result' => $result));
	}

	public function availableMidsIndex(){	//管理可测试mid
		$game_id = Session::get('game_id');
		$delete = DB::table('test_mids')->where('valid_time', '<', time())->delete();	//删除过期可测试的mid
		$all_mids = DB::table('test_mids')->where('game_id', $game_id)->get();
		$data2view = array();
		foreach ($all_mids as $value) {
			$data2view[] = array(
				'id' => $value->id,
				'mid' => $value->mid,
				'created_time' => date("Y-m-d H:i:s", $value->created_time),
				'valid_time' => date("Y-m-d H:i:s", $value->valid_time),
			);
		}
		$data = array(
				'content' => View::make('serverapi.mid_manger', array(
						'data2view' => $data2view
				))
		);
		return View::make('main', $data);
	}

	public function availableMidsManger(){
		$game_id = Session::get('game_id');
		$type = Input::get('type');

		if('1.4.4' == $type){	//新增操作
			$mid = (int)Input::get('mid');
			$valid_days = (int)Input::get('valid_days');
			$valid_days = $valid_days ? $valid_days : 1;
			$valid_time = time() + 86400*$valid_days;
			$data = array(
				'game_id' => $game_id,
				'mid' => $mid,
				'created_time' => time(),
				'valid_time' => $valid_time,
				);
			DB::table('test_mids')->insert($data);
		}elseif('4.5.12.5.20.5' == $type){	//删除操作
			$id = Input::get('id');
			DB::table('test_mids')->where('id', $id)->delete();
		}else{
			return Response::json(array('error' => 'Unknown Operate!'), 403);
		}

		return Response::json(array('msg' => 'Operate Success!'), 200);
	}
}