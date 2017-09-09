<?php
class BossController extends BaseController
{
	public function bosslivesIndex()
	{
		$servers = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.boss.bosslives', array(
									'servers' => $servers
									))
			);
		return View::make('main', $data);
	}

	public function updateBosslives()
	{
		$servers_id = Input::get('servers_id'); //array or string '0'
		$boss_type = (int) Input::get('boss_type');
		$times = (int) Input::get('times');

		$game_id = Session::get('game_id');
		$platform_id = Session::get('platform_id');
		$game = Game::find($game_id);

		if($servers_id == 0){
			$msg = array('error' => 'Did you select a server?');
			return Response::json($msg, 403);
		}
		foreach ($servers_id as $server_id) {
			$server = Server::find($server_id);
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->updateBosslives($boss_type, $times);
			$info = $api->checkBosslives($boss_type);
			try {
				$return_boss_id = $response->boss_id;
				$return_times = $response->times;
				$info_boss_id = $info->boss_id;
				$info_times = $info->times;
				if($return_boss_id == '268435440'){
					$boss = '12:00--18:00 BOSS';
				}elseif ($return_boss_id == '268435441') {
					$boss = '18:00--24:00 BOSS';
				}elseif ($return_boss_id == '268435442') {
					$boss = Lang::get('serverapi.xianjie');
				}else{
					$boss = 'A Mysterious BOSS';
				}
				if($info_boss_id == '268435440'){
					$check_boss = '12:00--18:00 BOSS';
				}elseif ($info_boss_id == '268435441') {
					$check_boss = '18:00--24:00 BOSS';
				}elseif ($info_boss_id == '268435442') {
					$check_boss = Lang::get('serverapi.xianjie');
				}else{
					$check_boss = 'A Mysterious BOSS';
				}
			} catch (Exception $e) {
				return Response::json(array('error'=>$response), 403);
			}
			$result[] = array('server_name'=>$server->server_name, 'boss'=>$boss, 'times'=>$return_times,
				'check_boss'=>$check_boss, 'check_times'=>$info_times);
		}

		if(!empty($result)){
			return $result;
		}
		
	}


}