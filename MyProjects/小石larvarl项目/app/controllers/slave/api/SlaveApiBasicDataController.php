<?php
class SlaveApiBasicDataController extends \BaseController {	//此控制器主要用来处理游戏基本信息功能，各部分数据查询都单独查询，一方面便于增加修改，一方面增加查询成功的效率

	public function BasicInfoIndex(){	//游戏基本信息
		$game = Game::find(Session::get('game_id'));
		$servers = Server::CurrentGameServers()->get();
		$data = array(
				'content' => View::make('slaveapi.basicinfo.game_info', array(
						'servers' => $servers,
						'game_code' => $game->game_code,
						'game_type' => $game->game_type,
				))
		);
		return View::make('main', $data);
	}

	public function BasicInfoQuery(){	//游戏基本信息，此功能对效率要求较高，将单独编写每一次sql查询而不使用不完全符合的接口---Panda
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$platform_id = Session::get('platform_id');

		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$server_ids = Input::get('server_ids');
		$all_server = 0;

		if($server_ids){
			if(is_array($server_ids)){
				if(in_array(0, $server_ids)){
					$servers = Server::CurrentGameServers()->get();
					$all_server = 1;
				}else{
					$servers = Server::find($server_ids);
				}
			}else{
				return Response::json(array('error' => 'Unknown Input!'), 403);
			}
		}else{
			return Response::json(array('error' => 'No server selected!'), 403);
		}
		$all_server_internal_ids = array();

		foreach ($servers as $server) {
			$all_server_internal_ids[] = $server->server_internal_id;
		}

		$result2view = array();

		$slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$params = array(
			'game_id' => $game_id,
			'platform_id' => $platform_id,
			'start_time' => $start_time,
			'end_time' => $end_time,
			'all_server' => $all_server,
			'all_server_internal_ids' => $all_server_internal_ids,
			);
		
		//每天的新增注册数
		$params['type'] = 'new_sign';	//因为master和slave每次请求的有效时间是独立的，因此这种可能出现长时间查询的都单独请求，多次请求
		$result_new_sign = $slaveapi->getBasicGameInfoQuery($params);
		if(200 == $result_new_sign->http_code){
			$result_new_sign = $result_new_sign->body;
			foreach ($result_new_sign as $value) {
				$result2view[$value->date]['new_sign'] = $value->sign_num;
			}
		}
		unset($value);
		unset($result_new_sign);
		unset($params['type']);

		//每天新增的创建数
		$params['type'] = 'new_create';	//因为master和slave每次请求的有效时间是独立的，因此这种可能出现长时间查询的都单独请求，多次请求
		$result_new_create = $slaveapi->getBasicGameInfoQuery($params);
		if(200 == $result_new_create->http_code){
			$result_new_create = $result_new_create->body;
			foreach ($result_new_create as $value) {
				$result2view[$value->date]['new_create'] = $value->create_num;
			}
		}
		unset($value);
		unset($result_new_create);
		unset($params['type']);

		//每天的充值人数和金额
		$params['type'] = 'day_payment';	//因为master和slave每次请求的有效时间是独立的，因此这种可能出现长时间查询的都单独请求，多次请求
		$result_payment = $slaveapi->getBasicGameInfoQuery($params);
		if(200 == $result_payment->http_code){
			$result_payment = $result_payment->body;
			foreach ($result_payment as $value) {
				$result2view[$value->date]['pay_user_num'] = $value->pay_user_num;
				$result2view[$value->date]['pay_times'] = $value->pay_times;
				$result2view[$value->date]['pay_dollar'] = $value->pay_dollar;
			}
		}
		unset($value);
		unset($result_payment);
		unset($params['type']);

		if(2 == $game->game_type){	//手游查询不同设备的充值
			//每天的不同设备类型充值
			$params['type'] = 'day_payment_os';	//因为master和slave每次请求的有效时间是独立的，因此这种可能出现长时间查询的都单独请求，多次请求
			$result_payment_os = $slaveapi->getBasicGameInfoQuery($params);
			if(200 == $result_payment_os->http_code){
				$result_payment_os = $result_payment_os->body;
				foreach ($result_payment_os as $value) {
					$result2view[$value->date][$value->pay_os_type.'_dollar'] = $value->pay_dollar;
				}
			}
			unset($value);
			unset($result_payment_os);
			unset($params['type']);
		}

		//每天的新增充值人数
		$params['type'] = 'new_payment';	//因为master和slave每次请求的有效时间是独立的，因此这种可能出现长时间查询的都单独请求，多次请求
		$result_new_payment = $slaveapi->getBasicGameInfoQuery($params);
		if(200 == $result_new_payment->http_code){
			$result_new_payment = $result_new_payment->body;
			foreach ($result_new_payment as $value) {
				$result2view[$value->date]['new_pay_user'] = $value->new_pay_user;
			}
		}
		unset($result_new_payment);
		unset($params['type']);
		unset($value);

		//每天的每个服务器的活跃等数据
		$keys2plus = array('login_num', 'avg_online', 'max_online', 'created_player_number', 'login_num_not_today');
		for ($i=2; $i <= 30; $i++) { 
			$keys2plus[] = 'days_'.$i;
		}
		$params['type'] = 'single_server';	//因为master和slave每次请求的有效时间是独立的，因此这种可能出现长时间查询的都单独请求，多次请求
		foreach ($all_server_internal_ids as $server_internal_id) {
			$params['server_internal_id'] = $server_internal_id;
			$result_single_server = $slaveapi->getBasicGameInfoQuery($params);
			if(200 == $result_single_server->http_code){
				$result_single_server = $result_single_server->body;
				foreach ($result_single_server as $value) {
					foreach ($keys2plus as $key2plus) {
						if(isset($value->$key2plus)){
							if(isset($result2view[$value->date][$key2plus])){
								$result2view[$value->date][$key2plus] += $value->$key2plus;
							}else{
								$result2view[$value->date][$key2plus] = $value->$key2plus;
							}
						}
					}
				}
			}
			unset($params['server_internal_id']);
			unset($result_single_server);
		}
		foreach ($result2view as $key => $value) {
			$result2view[$key]['date'] = $key;
		}
		ksort($result2view);
		$reverse_result = array_reverse($result2view);	//这里需要手动逆序一下，因为日期做key导致在页面用js循环的时候依然是顺序
		unset($result2view);
		$result2view = array();
		foreach ($reverse_result as $value) {
			$result2view[] = $value;
		}
		unset($value);
		unset($reverse_result);
		
		return Response::json($result2view);
	}
}