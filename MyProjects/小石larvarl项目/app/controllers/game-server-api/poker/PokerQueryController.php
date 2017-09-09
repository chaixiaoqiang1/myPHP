<?php

class PokerQueryController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
	//经济日志查询-奇修
	public function queryLogEconomyIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.queryLogEconomy'),
			);
		return View::make('main', $data);
	}
	public function queryLogEconomy()
	{
		$start_time = strtotime(trim(Input::get('start_time')));
		//时间戳
	    $end_time = strtotime(trim(Input::get('end_time')));
		$server = Server::find(13);
		if (!$server) {
			return Response::json($msg, 403);
		}
		$game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id');
       	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
       	//Log::info('slave:'.$game->eb_api_url);
        $platform = Platform::find(Session::get('platform_id'));
        if (! $platform) {
           	return Response::json($msg, 404);
        }
        $response = $api->queryLogEconomy($server->server_internal_id, $game_id, $platform->platform_id,$start_time, $end_time);
        // Log::info(json_encode($response));
        return $api->sendResponse();
	}
	//德扑经济统计
	public function queryEconomyIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.economy'),
			);
		return View::make('main', $data);
	}
	public function queryEconomy()
	{	
		$msg = array(
			'code' => Lang::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'start_time' => 'required',
		);
		$game_id = Session::get('game_id');
		$game = Game::find(Session::get('game_id'));
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$platform = Platform::find(Session::get('platform_id'));
        if (! $platform) {
           	return Response::json($msg, 404);
        }
		$days=ceil(($end_time - $start_time)/86400);
		if ($days){
			$arr = array();
			for($i=0; $i<$days; $i++) { 
				$server = Server::find(13);
				$start = date("Y-m-d", $start_time + 86400*($i));
				//var_dump($start);
				if (!$server) {
    				$msg['error'] = Lang::get('error.basic_not_found');
    				return Response::json($msg, 403);
    			}
				$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);

				$response2=$api->getPokerChips($start);

				$api3 = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
       			//Log::info('slave:'.$game->eb_api_url);
        		$response3= $api3->queryEconomy($server->server_internal_id, $game_id, $platform->platform_id,$start);
        		//Log::info(var_export($response3,true));

        		if('200' == $response3->http_code){
        			$bodyinfo = $response3->body;
        			if(isset($bodyinfo[0]->chips)){
	        			$issue = $bodyinfo[0]->chips;
	        			$recover = $bodyinfo[1]->chips;
        			}else{
        	        	$issue = 0;
        				$recover = 0;			
        			}
        		}else{
        			$issue = 0;
        			$recover = 0;
        		}

				if (isset($response2->sys_chips)) {
				 	$body = $response2->sys_chips;
				 	$data = array(
				 		'date' => date('Y-m-d H:i:s', strtotime($start)),
				 		'all' => isset($body->all) ? $body->all : 0,
				 		'active'=>isset($body->active) ? $body->active: 0,
				 		'anonymous'=>isset($body->anonymous) ? $body->anonymous: 0,
				 		'issue'=>$issue,
				 		'recover'=>$recover,
				 	);
				 	$arr[] = $data;
				}	
			}
			$result = array(
		 		'result' => $arr,
		 	);
		}
		if (isset($result)) {
			return Response::json($result);
		}else{
		 	$msg['error'] = Lang::get('error.basic_not_found');
		 	return Response::json($msg, 403);
		}

	}

	public function queryPlayCountIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.playcount'),
			);
		return View::make('main', $data);
	}

	public function queryPlayCount()
	{
		$msg = array(
			'code' => Lang::get('errorcode.unknow'),
			'error' => '查询异常'
		);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$server = Server::find(13);
		if (!$server) {
			return Response::json($msg, 403);
		}
		$gameserver_api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
		$game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id');
       	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
       	$platform = Platform::find(Session::get('platform_id'));
        if (! $platform) {
           	return Response::json($msg, 404);
        }
        $response = $api->queryPlayCount($server->server_internal_id, $game_id, $platform->platform_id,$start_time, $end_time);
        if(200 != $response->http_code){
        	return Response::json($msg, 403);
        }
        $response = $response->body;
        $result = array();
        foreach ($response as $key => $value) {
        	$result[$key] = $value;
        }
        $result_R = array();
        foreach($result as $key => $value){
        	$result_x = array();
        	$res  = $gameserver_api->dailyTfRecover($key);
        	if (!isset($res->table_fee)) {
        		return $gameserver_api->sendResponse();
        	}
        	$tmp = (array)($res->table_fee);
        	$body = $tmp['0'];
        	$body_pot = $tmp['1'];
        	$blind_array = array(1,2,5,10,20,50,100,200,500,1000,2000,2500,5000,10000,20000,25000,50000,100000,200000,500000);
        	$blind = array();
        	$blind['1']['table_fee'] = 0;
        	$blind['2']['table_fee'] = 0;
        	$blind['5']['table_fee'] = 0;
        	$blind['10']['table_fee']     =isset($body->blind10    )?$body->blind10    :0;
			$blind['20']['table_fee']     =isset($body->blind20    )?$body->blind20    :0;
			$blind['50']['table_fee']     =isset($body->blind50    )?$body->blind50    :0;
			$blind['100']['table_fee']    =isset($body->blind100   )?$body->blind100   :0;
			$blind['200']['table_fee']    =isset($body->blind200   )?$body->blind200   :0;
			$blind['500']['table_fee']    =isset($body->blind500   )?$body->blind500   :0;
			$blind['1000']['table_fee']   =isset($body->blind1000  )?$body->blind1000  :0;
			$blind['2000']['table_fee']   =isset($body->blind2000  )?$body->blind2000  :0;
			$blind['2500']['table_fee']   =isset($body->blind2500  )?$body->blind2500  :0;
			$blind['5000']['table_fee']   =isset($body->blind5000  )?$body->blind5000  :0;
			$blind['10000']['table_fee']  =isset($body->blind10000 )?$body->blind10000 :0;
			$blind['20000']['table_fee']  =isset($body->blind20000 )?$body->blind20000 :0;
			$blind['25000']['table_fee']  =isset($body->blind25000 )?$body->blind25000 :0;
			$blind['50000']['table_fee']  =isset($body->blind50000 )?$body->blind50000 :0;
			$blind['100000']['table_fee'] =isset($body->blind100000)?$body->blind100000:0;
			$blind['200000']['table_fee'] =isset($body->blind200000)?$body->blind200000:0;
			$blind['500000']['table_fee'] =isset($body->blind500000)?$body->blind500000:0;
			$blind['1']['pot_fee'] = 0;
        	$blind['2']['pot_fee'] = 0;
        	$blind['5']['pot_fee'] = 0;
        	$blind['10']['pot_fee']     =isset($body_pot->blind10    )?$body_pot->blind10    :0;
			$blind['20']['pot_fee']     =isset($body_pot->blind20    )?$body_pot->blind20    :0;
			$blind['50']['pot_fee']     =isset($body_pot->blind50    )?$body_pot->blind50    :0;
			$blind['100']['pot_fee']    =isset($body_pot->blind100   )?$body_pot->blind100   :0;
			$blind['200']['pot_fee']    =isset($body_pot->blind200   )?$body_pot->blind200   :0;
			$blind['500']['pot_fee']    =isset($body_pot->blind500   )?$body_pot->blind500   :0;
			$blind['1000']['pot_fee']   =isset($body_pot->blind1000  )?$body_pot->blind1000  :0;
			$blind['2000']['pot_fee']   =isset($body_pot->blind2000  )?$body_pot->blind2000  :0;
			$blind['2500']['pot_fee']   =isset($body_pot->blind2500  )?$body_pot->blind2500  :0;
			$blind['5000']['pot_fee']   =isset($body_pot->blind5000  )?$body_pot->blind5000  :0;
			$blind['10000']['pot_fee']  =isset($body_pot->blind10000 )?$body_pot->blind10000 :0;
			$blind['20000']['pot_fee']  =isset($body_pot->blind20000 )?$body_pot->blind20000 :0;
			$blind['25000']['pot_fee']  =isset($body_pot->blind25000 )?$body_pot->blind25000 :0;
			$blind['50000']['pot_fee']  =isset($body_pot->blind50000 )?$body_pot->blind50000 :0;
			$blind['100000']['pot_fee'] =isset($body_pot->blind100000)?$body_pot->blind100000:0;
			$blind['200000']['pot_fee'] =isset($body_pot->blind200000)?$body_pot->blind200000:0;
			$blind['500000']['pot_fee'] =isset($body_pot->blind500000)?$body_pot->blind500000:0;
			for($i = 0;$i < count($result[$key]);$i ++){
				$result[$key][$i]->table_fee = $blind[$result[$key][$i]->blind]['table_fee'];
				$result[$key][$i]->pot_fee = $blind[$result[$key][$i]->blind]['pot_fee'];
			}
			if(count($result[$key]) == 0){
				unset($result[$key]);
				continue;
			}
			
			$result_x['date'] = $key;
			for($i = 0,$j = 0;$i < count($result[$key]);$j ++){
				if($result[$key][$i]->blind == $blind_array[$j]){
					$result_x['blind'.$blind_array[$j]]['table_fee'] = $result[$key][$i]->table_fee;
					$result_x['blind'.$blind_array[$j]]['pot_fee'] = $result[$key][$i]->pot_fee;
					$result_x['blind'.$blind_array[$j]]['player_num'] = $result[$key][$i]->player_num;
					$result_x['blind'.$blind_array[$j]]['game_num'] = $result[$key][$i]->game_num;
					$i ++;
				}else{
					// $result_x['blind'.$blind_array[$j]]['blind'] = $blind_array[$j];
					$result_x['blind'.$blind_array[$j]]['table_fee'] = 0;
					$result_x['blind'.$blind_array[$j]]['pot_fee'] = 0;
					$result_x['blind'.$blind_array[$j]]['player_num'] = 0;
					$result_x['blind'.$blind_array[$j]]['game_num'] = 0;
				}
			}
			unset($tmp);
			unset($body);
			unset($body_pot);
			unset($result[$key]);
			unset($blind);
			$result_R[] = $result_x;
			unset($result_x);
	
        }
        // Log::info(var_export($result_R,true));
        return $result_R;
	}

	public function dailydataindex(){	//查询日报内容
		$data = array(
			'content' => View::make('serverapi.poker.dailydata'),
			);
		return View::make('main', $data);
	}

	public function dailydataquery(){
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));
		$game_id = Session::get('game_id');
		$platform_id = Session::get('platform_id');
		$params = array(
			'start_time' => $start_time,
			'end_time'	=> $end_time,
			'game_id' => $game_id,
			'platform_id' => $platform_id,
			);
		$singleday = Input::get('singleday');
		if('1' == $singleday){
			return $this->dailydatasingleday($params);
		}else{
			return $this->dailydata($params);
		}
	}

	public function dailydata($params){
		$name2name = array(
            'endOneRound' => 'endOneRound',
            'playSlot' => 'playSlot',
            'betRedBlackCard' => 'betRedBlackCard',
            'betLuckyCard' => 'settleLuckyCardReward',
            'betLuckyPool' => 'settleLuckyPoolReward',
            'startSpinAndGo' => 'matchSpinRank|matchRank',
            'startSitAndGo' => 'matchSitRank|matchRank',
            'regMU' => 'matchMURank|matchRank',
	    );
		$start_time = $params['start_time'];
		$end_time = $params['end_time'];
		$game_id = $params['game_id'];
		$platform_id = $params['platform_id'];

		$game = Game::find($game_id);
		$slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		$date_start = date("Y-m-d", $start_time);
		$start_time = strtotime($date_start) - 7200;
		$result = array();
		while (1) {
			$tmp_end_time = $start_time + 86400;
			if($tmp_end_time > $end_time){
				break;
			}
			$tmp_result = $slaveapi->getPokerDailyData(1, $game_id, $platform_id, $start_time, $tmp_end_time);
			if('200' != $tmp_result->http_code){
				$start_time += 86400;
				continue;
			}
			$tmp_array = array();
			$body = $tmp_result->body;
			$getbackin = 0;
			$getbackout = 0;
			foreach ($body as $info) {
				if('0' == $info->diff_chip){	//并没有实际筹码变动的，不记录
					continue;
				}
				if('getChipsFromStrongBox|getChipsFromStrongBox' == $info->action_type ||
				 'saveChipsToStrongBox|saveChipsToStrongBox' == $info->action_type){	//保险箱操作的，不记录
					continue;
				}
				if(in_array($info->action_type, array('endOneRound', 'standUp'))){
					if('1' == $info->is_fafang){
						$getbackout += $info->diff_chip;
					}else{
						$getbackin += $info->diff_chip;
					}
					continue;
				}
				$type =  1 == $info->is_fafang ? 'out' : 'in';
				$tmp_array[$type][$info->action_type] = array(
					'log_time' => date("Y-m-d", $info->created_time),
					'type' => 1 == $info->is_fafang ? '发放' : '回收',
					'diff_chip' => $info->diff_chip,
					'action_type' => $info->action_type,
					'action_name' => Lang::get("pokerData.$info->action_type"),
					);
			}
			$tmp_array_2 = array();
			$tmp_array_2['getback']['in'] = array(
					'log_time' => '',
					'type' => '回收',
					'diff_chip' => $getbackin,
					'action_type' => 'getback',
					'action_name' => '牌局回收',
				);
			$tmp_array_2['getback']['out'] = array(
					'log_time' => '',
					'type' => '发放',
					'diff_chip' => $getbackout,
					'action_type' => 'getback',
					'action_name' => '牌局回收',
				);
			$tmp_array_2['getback']['sum'] = $tmp_array_2['getback']['in']['diff_chip'] + $tmp_array_2['getback']['out']['diff_chip'];
			foreach ($name2name as $key => $value) {
				if(isset($tmp_array['in'][$key])){
					$tmp_array_2[$key]['in'] = $tmp_array['in'][$key];
					if(isset($tmp_array['out'][$value])){
						$tmp_array_2[$key]['out'] = $tmp_array['out'][$value];
						$tmp_array_2[$key]['sum'] = $tmp_array_2[$key]['out']['diff_chip'] + $tmp_array_2[$key]['in']['diff_chip'];
						unset($tmp_array['in'][$key]);
						unset($tmp_array['out'][$value]);
					}
					unset($tmp_array['in'][$key]);
				}
			}
			$tmp_array_2['other']['in'] = array(
					'log_time' => '',
					'type' => '回收',
					'diff_chip' => 0,
					'action_type' => 'other',
					'action_name' => '所有其他',
				);
			$tmp_array_2['other']['out'] = array(
					'log_time' => '',
					'type' => '发放',
					'diff_chip' => 0,
					'action_type' => 'other',
					'action_name' => '所有其他',
				);
			foreach ($tmp_array['in'] as $key => $value) {
				$tmp_array_2['other']['in']['diff_chip'] += $value['diff_chip'];
			}
			foreach ($tmp_array['out'] as $key => $value) {
				$tmp_array_2['other']['out']['diff_chip'] += $value['diff_chip'];
			}
			$tmp_array_2['other']['sum'] = $tmp_array_2['other']['in']['diff_chip'] + $tmp_array_2['other']['out']['diff_chip'];
			$tmp_array_2['date'] = date("Y-m-d", $tmp_end_time);

			$result[date("Y-m-d", $tmp_end_time)] = $tmp_array_2;
			$start_time += 86400;

			unset($body);
			unset($tmp_end_time);
			unset($tmp_result);
			unset($tmp_array);
			unset($tmp_array_2);
		}

		if(count($result) > 0){
			return Response::json($result);
		}else{
			return Response::json(array('error'=>'没有结果'), 404);
		}
	}

	public function dailydatasingleday($params){
		$name2name = array(
				'endOneRound' => 'endOneRound',
	            'playSlot' => 'playSlot',
	            'betRedBlackCard' => 'betRedBlackCard',
	            'betLuckyCard' => 'settleLuckyCardReward',
	            'betLuckyPool' => 'settleLuckyPoolReward',
	            'startSpinAndGo' => 'matchSpinRank|matchRank',
	            'startSitAndGo' => 'matchSitRank|matchRank',
	            'regMU' => 'matchMURank|matchRank',
	    );
		$start_time = $params['start_time'];
		$game_id = $params['game_id'];
		$platform_id = $params['platform_id'];

		$game = Game::find($game_id);
		$slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

		$date_start = date("Y-m-d", $start_time);
		$start_time = strtotime($date_start) - 7200;
		$result = array();
		while (1) {
			$count = 0;
			$tmp_end_time = $start_time + 86400;
			$tmp_result = $slaveapi->getPokerDailyData(1, $game_id, $platform_id, $start_time, $tmp_end_time);
			if('200' != $tmp_result->http_code){
				break;
			}
			$tmp_array = array();
			$body = $tmp_result->body;
			foreach ($body as $info) {
				if('0' == $info->diff_chip){
					continue;
				}
				$type =  1 == $info->is_fafang ? 'out' : 'in';
				$tmp_array[$type][$info->action_type] = array(
					'log_time' => date("Y-m-d", $info->created_time),
					'type' => 1 == $info->is_fafang ? '发放' : '回收',
					'diff_chip' => $info->diff_chip,
					'action_type' => $info->action_type,
					'action_name' => Lang::get("pokerData.$info->action_type"),
					);
			}
			$tmp_array_2 = array();
			foreach ($name2name as $key => $value) {
				if(isset($tmp_array['in'][$key])){
					$tmp_array_2[$count]['in'] = $tmp_array['in'][$key];
					if(isset($tmp_array['out'][$value])){
						$tmp_array_2[$count]['out'] = $tmp_array['out'][$value];
						$tmp_array_2[$count]['sum'] = $tmp_array_2[$count]['out']['diff_chip'] + $tmp_array_2[$count]['in']['diff_chip'];
						unset($tmp_array['in'][$key]);
						unset($tmp_array['out'][$value]);
					}else{
						$tmp_array_2[$count]['sum'] = $tmp_array_2[$count]['in']['diff_chip'];
					}
					unset($tmp_array['in'][$key]);
					$count++;
				}
			}
			foreach ($tmp_array['in'] as $key => $value) {
				$tmp_array_2[$count]['in'] = $value;
				$count++;
			}
			foreach ($tmp_array['out'] as $key => $value) {
				$tmp_array_2[$count]['out'] = $value;
				$count++;
			}
			$result = $tmp_array_2;

			unset($body);
			unset($tmp_end_time);
			unset($tmp_result);
			unset($tmp_array);
			break;
		}

		if(count($result) > 0){
			return Response::json($result);
		}else{
			return Response::json(array('error'=>'没有结果'), 404);
		}
	}

	public function ActivityDataIndex(){
        $data = array(
            'content' => View::make('serverapi.poker.activitydata'),
        );
        return View::make('main', $data);
    }

    public function ActivityData(){
      $msg = array(
           'code' => Config::get('errorcode.unknow'),
           'error' => Lang::get('error.basic_input_error'),
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');	
        $game = Game::find(11);
        $player_id = Input::get('player_id');
        $device = Input::get('device');
        $activity_id = Input::get('activity_id');
        $reward_id = Input::get('reward_id');
        if($device == -1)
        	$device = null;

        
        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $result = $slaveapi->getActivityData($platform_id, $game_id, $player_id, $device, $activity_id, $reward_id, $start_time, $end_time);

		if('200' != $result->http_code){
            return $slaveapi->sendResponse();
        }
        $result = $result->body;

        if(count($result) > 0){
			return Response::json($result);
		}else{
			return $slaveapi->sendResponse();
		}
    }

    //德扑注册数据
    public function SignupCreateIndex(){
    	$data = array(
            'content' => View::make('slaveapi.user.poker.signup'),
        );
        return View::make('main', $data);
    }

    public function SignupCreateQuery(){
    	$game_id = Session::get('game_id');
    	$platform_id = Session::get('platform_id');
    	$game = Game::find($game_id);

    	$start_time = strtotime(trim(Input::get('start_time')));
    	$end_time = strtotime(trim(Input::get('end_time')));

    	$slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

    	$signup_info = $slaveapi->SignupCreateInfo($game_id, $platform_id, $start_time, $end_time);
    	$result = array();

    	if('200' != $signup_info->http_code){
    		return $slaveapi->sendResponse();
    	}

    	$signup_info = $signup_info->body;

    	$result['all_sign'] = isset($signup_info->all_sign) ? $signup_info->all_sign : 0;
    	$result['single_day_sign'] = isset($signup_info->single_day_sign) ? $signup_info->single_day_sign : 0;
    	$result['all_create'] = isset($signup_info->all_create) ? $signup_info->all_create : 0;
    	$result['single_day_create'] = isset($signup_info->single_day_create) ? $signup_info->single_day_create : 0;
    	$result['all_device'] = isset($signup_info->all_device) ? $signup_info->all_device : 0;
    	$result['single_day_device'] = isset($signup_info->single_day_device) ? $signup_info->single_day_device : 0;
    	$result['payment'] = isset($signup_info->payment) ? $signup_info->payment : array();
    	
    	return Response::json($result);
    }

    //德扑破产相关信息
    public function BankruptcyIndex(){
     	$data = array(
            'content' => View::make('slaveapi.user.poker.bankruptcy'),
        );
        return View::make('main', $data);   	
    }

    public function BankruptcyData(){
    	$game_id = Session::get('game_id');
    	$platform_id = Session::get('platform_id');
    	$game = Game::find($game_id);

    	$by_create_time = Input::get('by_create_time');
    	$start_time = $this->current_time_nodst(strtotime(trim(Input::get('start_time'))));
    	$end_time = $this->current_time_nodst(strtotime(trim(Input::get('end_time'))));
    	$create_start_time = $this->current_time_nodst(strtotime(trim(Input::get('create_start_time'))));
    	$create_end_time = $this->current_time_nodst(strtotime(trim(Input::get('create_end_time'))));

    	$slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

    	$bankruptcy_info = $slaveapi->getPokerBankruptcy($game_id, $platform_id, $by_create_time, $start_time, $end_time, $create_start_time, $create_end_time);

    	if('200' != $bankruptcy_info->http_code){
    		return $slaveapi->sendResponse();
    	}
    	return Response::json($bankruptcy_info->body);
    }
}