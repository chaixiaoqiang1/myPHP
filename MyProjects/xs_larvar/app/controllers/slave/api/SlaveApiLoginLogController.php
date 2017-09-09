<?php

class SlaveApiLoginLogController extends \BaseController {
	private $servers = array();

	public function __construct()
	{
		$this->servers = $this->getUnionServers();
	}

	public function index()
	{
		$game = Game::find(Session::get('game_id'));
		$slaveApi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$slaveApi->getCreatePlayerLog(1, 3, 1370074726, 1370084726, 1, 30);
		return $slaveApi->sendResponse();
	}

	public function getOnlineTrend()
	{
		$data = array(
			'content' => View::make('slaveapi.trend.index', array(
				'servers' => $this->servers 
			))
		);
		return View::make('main', $data);
	}

	public function getOnlineTrendData()
	{
		$msg = array(
			'code' => Lang::get('errorcode.unknown'),
			'msg' => Lang::get('errorcode.server_not_found')
		);

		$server_ids = Input::get('server_id');
		//var_dump($server_ids);die();
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));

		$start_time = $this->current_time_nodst($start_time);
		$end_time = $this->current_time_nodst($end_time);

		$interval = (int)Input::get('interval');
		if('0' == $interval){
			return Response::json(array('error'=>'请选择一个时间间隔，多服只支持间隔一天。'), 403);
		}
		$game = Game::find(Session::get('game_id'));
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		if ($server_ids[0] == "0" && (count($server_ids) == 1)) { //全服统计
			$k=0;
			$data = array();
			foreach ($this->servers as $key => $server) {
				$server_internal_id = $server->server_internal_id;
				if (!$server_internal_id) {
					return Response::json($msg, 403);
				}
				$response = $api->getPlayerOnlineTrend($game->game_id, $server_internal_id, $start_time, $end_time, $interval);
				$body = $response->body;
				$data1 = (array)$body;

				$day=ceil(($end_time-$start_time)/$interval);
				for($i=0;$i<$day;$i++){
					$data[$k][$i] = new stdClass();
					$data[$k][$i]->time = 0;
					$data[$k][$i]->avg_value = 0;
					$data[$k][$i]->max_value = 0;
					$data[$k][$i]->start_time = 0;
					$data[$k][$i]->end_time = 0;
					foreach ($data1 as $singledata) {
						if(isset($singledata->time)){
							if(86400 == $interval){
								if (date('Y-m-d', ($singledata->time + 3700)) == date('Y-m-d', ($start_time + $i*$interval + 3700))) {
									$data[$k][$i] = $singledata;
									break;
								}
							}else{
								if ($singledata->time == $start_time + $i*$interval) {
									$data[$k][$i] = $singledata;
									break;
								}
							}
						}
					}
				}
				$k++;
				unset($data1);
			}
			$data = array_reverse($data);
			$len = count($data);
			$total = array();
			$day=ceil(($end_time-$start_time)/$interval);
			for ($i=0; $i < $day; $i++) { 
				$total[$i] = new stdClass();
				$total[$i]->time = 0;
				$total[$i]->avg_value= 0;
				$total[$i]->max_value = 0;
				$total[$i]->start_time = 0;
				$total[$i]->end_time = 0; 
			}
			for ($i=0; $i < $day; $i++) { 
				 for ($j=0; $j < $len; $j++) { 
			 		$time=$start_time + $interval * $i;
			 		$total[$i]->avg_value += $data[$j][$i]->avg_value;
			 		$total[$i]->max_value += $data[$j][$i]->max_value;
			 		$total[$i]->start_time = date('Y-m-d H:i:s', $time);
					$total[$i]->end_time = date('Y-m-d H:i:s', $time + $interval - 1);
				 }
			}
			//var_dump($total);die();
		}elseif(count($server_ids) > 1 ){ //选某几个服务器
			$k=0;
			$data = array();
			foreach ($server_ids as $key => $server_id) {
				$server = Server::find($server_id);
				if (!$server) {
					return Response::json($msg, 403);
				}
				$server_internal_id = $server->server_internal_id;
				$response = $api->getPlayerOnlineTrend($game->game_id, $server_internal_id, $start_time, $end_time, $interval);
				$body = $response->body;
				$data1 = (array)$body;

				$day=ceil(($end_time-$start_time)/$interval);
				for($i=0;$i<$day;$i++){
					$data[$k][$i] = new stdClass();
					$data[$k][$i]->time = 0;
					$data[$k][$i]->avg_value = 0;
					$data[$k][$i]->max_value = 0;
					$data[$k][$i]->start_time = 0;
					$data[$k][$i]->end_time = 0;
					foreach ($data1 as $singledata) {
						if(isset($singledata->time)){
							if(86400 == $interval){
								if (date('Y-m-d', ($singledata->time + 3700)) == date('Y-m-d', ($start_time + $i*$interval + 3700))) {
									$data[$k][$i] = $singledata;
									break;
								}
							}else{
								if ($singledata->time == $start_time + $i*$interval) {
									$data[$k][$i] = $singledata;
									break;
								}
							}
						}
					}
				}
				$k++;
				unset($data1);
			}
//	Log::info(var_export($data,true));
			$len = count($data);
			$total = array();
			$data = array_reverse($data);
			$day=ceil(($end_time-$start_time)/$interval);
			for ($i=0; $i < $day; $i++) { 
				$total[$i] = new stdClass();
				$total[$i]->time = 0;
				$total[$i]->avg_value= 0;
				$total[$i]->max_value = 0;
				$total[$i]->start_time = 0;
				$total[$i]->end_time = 0; 
			}
			for ($i=0; $i < $day; $i++) { 
				 for ($j=0; $j < $len; $j++) { 
			 		$time=$start_time + $interval * $i;
			 		$total[$i]->avg_value += $data[$j][$i]->avg_value;
			 		$total[$i]->max_value += $data[$j][$i]->max_value;
			 		$total[$i]->start_time = date('Y-m-d H:i:s', $time);
					$total[$i]->end_time = date('Y-m-d H:i:s', $time + $interval - 1);
				 }
			}
//Log::info(var_export($total,true));
		}elseif((count($server_ids) == 1) && ($server_ids[0] != 0)){ //只选择一个服务器
			$server = Server::find($server_ids[0]);
			if (!$server) {
				return Response::json($msg, 403);
			}
			$response = $api->getPlayerOnlineTrend($game->game_id, $server->server_internal_id , $start_time, $end_time, $interval);
			$total = $response->body;
            //Log::info('趋势统计返回数据：'.var_export($total, true));
			if ($response->http_code == 200) {
				foreach ($total as $k => $v) {
					$total[$k]->start_time = date('Y-m-d H:i:s', $v->time);
					$total[$k]->end_time = date('Y-m-d H:i:s', $v->time + $interval - 1);
				}
			} 
		}

		if (isset($total) && count($total) > 0) {
			return Response::json($total);
		} else {
			return Response::json($msg, 403);
		}
	}

	public function getLoginTotal()
	{
		$data = array(
			'content' => View::make('slaveapi.login.index', array(
				'servers' => $this->servers
			))
		);
		return View::make('main', $data);
	}

	public function getLoginTotalData()
	{
		$server_ids = Input::get('server_id');
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));
		$interval = ( int )Input::get('interval');
		$level = Input::get('level');

		$start_time = $this->current_time_nodst($start_time);
		$end_time = $this->current_time_nodst($end_time);

		if($server_ids === '0'){
			return Response::json(array('error'=>'Did you select a server?'), 403);
		}
		if($interval == 0){
			$days = Input::get('days');
			if($days <= 0){
				return Response::json(array('error'=>'Did you select a time interval?'), 403);
			}else{
				$interval = 86400 * $days;
			}
		}
		if($start_time > $end_time){
			return Response::json(array('error'=>'You are kidding me? The start time!!'));
		}
		if($interval == 86400 && ($end_time - $start_time) > 31*86400){
			return Response::json(array('error'=>'一天间隔时开始到结束时间不超过31天'), 403);
		}
		if($interval == 3600 && ($end_time - $start_time) > 86400){
			return Response::json(array('error'=>'一小时间隔时开始到结束时间不超过一天'), 403);
		}
		if($interval == 600 && ($end_time - $start_time) > 6*3600){
			return Response::json(array('error'=>'十分钟间隔时开始到结束时间不超过六小时'), 403);
		}
		$game = Game::find(Session::get('game_id'));
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		//所有服务器换算成选中列表中所有服务器
		if($server_ids[0] === '0')
		{
			$server_ids = array();
			foreach ($this->servers as $server) {
				$server_ids[] = $server->server_id;
			}
		}
		//选中某个或某几个服务器
		$data = array();
		$msg = array('error'=>'Not find server in servers table.');
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id);
			if(!$server){
				return Response::json($msg, 403);
			}
			$server_internal_id = $server->server_internal_id;
			$response = $api->getLoginTotalByTime($game->game_id, $server_internal_id, $start_time, $end_time, $interval, $level);
			$body = $response->body;
			if(is_null($body)){
				continue;
			}
			$data[] = (array)$body;
		}
		if(!empty($data)){
			$result = array();

			foreach ($data as $ltime_arr) {	//这里的处理是为了处理多服的数值和
				foreach ($ltime_arr as $v) {
					if(isset($v->ltime)){
						if('86400' == $interval){
							$vltime = strtotime(date('Y-m-d', ($v->ltime + 3700)));
							$flagR = 'f'.$vltime;
						}else{
							$vltime = $v->ltime;
							$flagR = 'f'.$v->ltime;
						}
						if(isset($result[$flagR])){
							$result[$flagR]['login_count'] += $v->login_count;
							$result[$flagR]['ip_count'] += $v->ip_count;
							$result[$flagR]['login_count_before'] += $v->login_count_before;

						}else{
							if(86400 == $interval){	//如果间隔一天，处理一下每条数据的起止时间，保证天的完整性
								$result_end_time = strtotime(date('Y-m-d', $vltime + $interval + 3700));
							}else{
								$result_end_time = $vltime + $interval;
							}
							$result[$flagR] = array(
								'start_time'=> date('Y-m-d H:i:s', $vltime),
								'end_time'=> date('Y-m-d H:i:s', $result_end_time),
								'login_count'=>0, 
								'ip_count'=>0,
								'login_count_before'=>0
								);
							$result[$flagR]['login_count'] += $v->login_count;
							$result[$flagR]['ip_count'] += $v->ip_count;
							$result[$flagR]['login_count_before'] += $v->login_count_before;

						}
					}
				}
			}
			foreach ($result as $key => &$value) {
				if('0' == $value['login_count'] && '0' == $value['ip_count']){
					unset($result[$key]);
				}
			}
			// Log::info(var_export($result,true));
			return Response::json($result);
		}
		else{
			return Response::json(array('error'=>'No data from Server.'), 403);
		}
	}

//player outflow
    public function playerOutflowData()
    {   
    	$data = array(
			'content' => View::make('slaveapi.player.playeroutflowData', array(
				'servers' => $this->servers,
			))
		);
		return View::make('main', $data);
    }
    public function playerOutflowQuery()
    {
    	$game = Game::find(Session::get('game_id'));
    	$server_id = Input::get('server_id');
    	$is_pay = Input::get('is_pay');
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));
		$login_start_time = strtotime(Input::get('login_start_time'));
		$login_end_time = strtotime(Input::get('login_end_time'));
		$miss_days = Input::get('miss_days');
		$platform_id = Session::get('platform_id');
		$result = array();
		$result_info = array();
		$result_create = '';
		$result_login = '';
		//Log::info(var_export(Input::all(),true));
        //check validity of input
        //判断所选服是不是多个或者判断它是否合法
        if (is_array($server_id)) {
        	
        }else{
        	$server_id = explode(',',$server_id);
        }
		if(in_array(0,$server_id))
		{
			return Response::json(array('error'=>'Server is needed or Server select error!'), 403);
		}
		
		if((($login_end_time-$login_start_time)/86400) < $miss_days)
		{
			return Response::json(array('error'=>'The days you input can not be greater than the difference between the login_start_time and login_end_time.'), 403);
		}
		if($start_time >= $end_time && $login_start_time >= $login_end_time)
		{
			return Response::json(array('error'=>'The start_time can not be equal or greater than the end_time.\n Or the end_time can not be greater than the interval_time.'), 403);
		}
		//选中某个或某几个服务器
		$data = array();
		$msg = array('error'=>'Not find server in servers table.');
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		foreach ($server_id as $val) {
			$server = Server::find($val);
			if(!$server){
				return Response::json($msg, 403);
			}
			$server_internal_id = $server->server_internal_id;
			$response = $api->getRegistByTime($game->game_id, $server_internal_id, $is_pay,$start_time, $end_time, $login_start_time, $login_end_time, $miss_days, $platform_id);
			if('200' != $response->http_code)
			{			
				return Response::json(array('error'=>'Fail to connect the Database.'), 403);
			}
			$body = $response->body;
			if(is_null($body)){
				continue;
			}
			if(empty($body->result_create))
			{
				return Response::json(array('error'=>'During this time, there is no player created.'),403);
			}
			
			$result_create += $body->result_create;
			$result_login += $body->result_login;

			$result = $body->result;
			function object2array(&$object) {
             $object =  json_decode( json_encode( $object),true);
             return  $object;
   			}
   			$result = object2array($result);	
			
			for ($i=0; $i <count($result) ; $i++) { 
				$result[$i]['server_name']= $server->server_name;
			}
			$result_info = $body->result_info;
			$result_info = object2array($result_info);
			
			for ($i=0; $i <count($result_info) ; $i++) { 
				$result_info[$i]['server_name']= $server->server_name;
			}
		}
		$data = array( 
				'msg' => 'During this time , there hava/has '.$result_create.' player created . And '.$result_login.' player logged in later.',
				'result' => $result,
				'result_info' => $result_info
			);
		return Response::json($data);
    }
}