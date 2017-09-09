<?php

class PokerGiveChipsController extends \BaseController {

	/*
		发送筹码
	*/
	const SERVER_IP =  "119.81.84.118";
	public function chipIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.chips'),
		);
		return View::make('main', $data);
	}
	//玩家最后20个动作查询
	public function recentActionIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.recentaction'),
		);
		return View::make('main',$data);
	}
	public function recentAction()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 	
		);
		$player_id = Input::get('player_id');
		$server = Server::find(13);
		if(!$server){
			return Resposen::json($msg,403);
		}
		$api = PokerGameServerApi::connect($server->api_server_ip,$server->api_server_port);
		$response = $api->getRecentAction($player_id);
		$table = $this->initTable();
        $action = $table->getData();
        for($i = 0;$i < count($response);$i ++){
        	$flag = 0;
        	foreach ($action as $key => $value) {
        		if($response[$i] == $value->Pro){
        			$response[$i] = array();
        			$response[$i]['pro'] = $value->Pro;
        			$response[$i]['name'] = $value->Name;
        			$response[$i]['other'] = $value->Other;
      				$flag = 1;
        			break;
        		}
        	}
        	if($flag == 0){
        		$tmp_pro = $response[$i];
        		$response[$i] = array();
        		$response[$i]['pro'] = $tmp_pro;
        		$response[$i]['name'] = '---未收录---';
        		$response[$i]['other'] = '---未收录---';
        	}
        }
        
		return Response::json($response);
	}
	//相同保险箱密码玩家查询
	public function sameStrongboxPasswdIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.samestrongboxpasswd'),
			);
		return View::make('main',$data);
	}
	public function sameStrongboxPasswd()
	{
		$msg = array(
			'code'	=> Config::get('error_code.unknow'),
			'error'	=> Lang::get('error.basic_input_error'),
			);
		$player_id = Input::get('player_id');
		$server = Server::find(13);
		if(!$server){
			return Response::json($msg,403);
		}
		$api = PokerGameServerApi::connect($server->api_server_ip,$server->api_server_port);
		$response = $api->getSameStrongboxPasswd();
		if(!isset($response->error_code)){
			return $api->sendResponse();
		}else{
			return Response::json($msg,403);
		}
		return Response::json($response);
	}
	public function sameStrongboxPasswdPlayer()
	{
		$msg = array(
			'code'	=> Config::get('error_code.unknow'),
			'error'	=> Lang::get('error.basic_input_error'),
		);
		$player_id = Input::get('player_id');
		$server = Server::find(13);
		if(!$server){
			return Response::json($msg,403);
		}
		$api = PokerGameServerApi::connect($server->api_server_ip,$server->api_server_port);
		$response = $api->getSameStrongboxPasswdPlayer($player_id);
		if(!isset($response->error_code)){
			return $api->sendResponse();
		}else{
			return Response::json($msg,403);
		}
		return Response::json($response);
	}
	public function sameStrongboxPasswdPassword()
	{
		$msg = array(
			'code'	=> Config::get('error_code.unknow'),
			'error'	=> Lang::get('error.basic_input_error'),
		);
		$password = Input::get('password');
		$server = Server::find(13);
		if(!$server){
			return Response::json($msg,403);
		}
		$api = PokerGameServerApi::connect($server->api_server_ip,$server->api_server_port);
		$response = $api->getSameStrongboxPasswdPassword($player_id);
		if(!isset($response->error_code)){
			return $api->sendResponse();
		}else{
			return Response::json($msg,403);
		}
		return Response::json($response);
	}
	//GM设置
	public function setGMIndex()
	{

	}
	public function setGM()
	{

	}
	public function getGM()
	{

	}
	//币商设置
	public function setBusinessmanIndex()
	{
		$level = array("撤销","普通币商","认证币商");
		$data = array(
			'content' => View::make('serverapi.poker.business',array('level' => $level)),
			);
		return View::make('main',$data);
	}
	public function setBusinessman()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$player_id = Input::get('player_id');
		$level = Input::get('level');
		$server = Server::find(13);
 	  	if (!$server) {
				return Response::json($msg, 403);
		}
      	$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
      	$response = $api->setBusinessman($player_id,$level);
      	var_export($response);
    	if (!isset($response->error_code)) { 
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
     	 return Response::json($response);		
	}
	//币商查询
	public function getBusinessman()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$server = Server::find(13);
 	  	if (!$server) {
				return Response::json($msg, 403);
		}
      	$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
      	$response = $api->getBusinessman();
      	$response = (array)$response;
      	$response['data'] = (array)$response['data'];
    	if (!isset($response->error_code)) { 
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
     	 return Response::json($response);		
	}
	//开活动 by taishou
	public function activityStatusIndex()
	{
		$activity_name = array(
				1 => '充值转盘',
				2 => '红包福袋',
				3 => '复活节彩蛋',
				4 => '累计充值活动',
				5 => '豪华礼包',
				6 => 'Token Table',
				7 => 'Secretary',
				8 => '印尼开斋节玩牌',
				9 => '大场聚宝盘',
				10 => 'Small Token Table',
				11 => '谁是赢家',
				12 => '幸运宝盒',
				13 => '限时玩牌',
				14 => '11月充值',
				15 => '手机大场100M',
				16 => '手机大场500M',
				17 => '筹码转盘',
				18 => '手游玩牌抽奖',
				19 => '白手套',
				20 => '手游圣诞',
				21 => '手游元旦充值',
				22 => '新手签到',
				23 => '页游-玩牌转盘',
				24 => '页游-玩牌领奖',
				25 => '手游-幸运牌型',
				26 => '手游-50M牌局',
				27 => '手游-积分抽奖',
				28 => '手游-幸运牌型',
				29 => '手游-问卷调查',
				30 => 'mog渠道支付',
				31 => '积分玩牌',
			);
		$data = array(
			'content' => View::make('serverapi.poker.activitystatus',array("activity_name" => $activity_name)),
			);
		return View::make('main',$data);
	}

	public function activityStatus()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$activity_id = Input::get('activity_id');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$status = Input::get('status');
		$server = Server::find(13);
 	  	if (!$server) {
				return Response::json($msg, 403);
		}
      	$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
      	$response = $api->setActivityStatus($activity_id,$start_time,$end_time,$status);
      	// var_export($response);
    	if (!isset($response->error_code)) { 
    		$platform = Platform::find(Session::get('platform_id'));
    		$platform_api =  PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
    		$data2post = array(
    			'time' => time(),
    			'activity_id' => $activity_id,
    			'start_time' => $start_time,
    			'end_time' => $end_time,
    			'status' => $status,
    			);
    		$response = $platform_api->set_joyspade_activity($data2post);
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
     	return Response::json($response);
	}
	//禁言设置 by taishou
	public function speakAuthorityIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.speakauthority'),
			);
		return View::make('main',$data);
	}

	public function speakAuthority()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$player_id = Input::get('player_id');
		$is_ban_speak = Input::get('is_ban_speak');
		$server = Server::find(13);
 	  	if (!$server) {
				return Response::json($msg, 403);
		}
      	$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
      	$response = $api->setSpeakAuthority($player_id,$is_ban_speak);
      	//var_dump($response);die();
    	if (!isset($response->error_code)) { 
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
     	 return Response::json($response);
	}
	//带处理订单查询 by taishou
	public function delayOrderIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.delayorder'),
			);
		return View::make('main', $data);
	}

	public function delayOrder()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);	
		$is_check = Input::get('is_check');
		$server = Server::find(13);
 	  	if (!$server) {
				return Response::json($msg, 403);
		}
		$game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id');
       	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $platform = Platform::find(Session::get('platform_id'));
        if (! $platform) {
           	return Response::json($msg, 404);
        }
        $response = $api->queryDelayOrder($server->server_internal_id, $game_id, $platform->platform_id, $is_check);
        if('200' == $response->http_code){
        	$result = $response->body;
        	return Response::json($result);
        }elseif('404' == $response->http_code){
        	return Response::json(array('error'=>'没有查询到数据'), 404);
        }else{
        	return Response::json(array('error'=>'连接超时或出现系统错误'), $response->http_code);
        }
	}

	//2人刷chip by taishou
	public function tradeChipsIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.tradechips'),
			);
		return View::make('main', $data);
	}

	public function tradeChips()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$server = Server::find(13);

 	  	if (!$server) {
				return Response::json($msg, 403);
		}

      	$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
      	$response = $api->tradeChips($start_time,$end_time);
      	//var_dump($response);die();
    	if (!isset($response->error_code)) { 
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
     	 return Response::json($response);
	}
	//连胜玩家 by taishou
	public function steadPlayerIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.steadplayer'),
			);
		return View::make('main', $data);
	}

	public function steadPlayer()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$server = Server::find(13);

 	  	if (!$server) {
				return Response::json($msg, 403);
		}

      	$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
      	$response = $api->steadWinPlayer($start_time,$end_time);
      	//var_dump($response);die();
    	if (!isset($response->error_code)) { 
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
     	 return Response::json($response);
	}
	//筹码流向查询by mumu
	public function queryChipIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.querychip'),
			);
		return View::make('main', $data);
	}

	public function queryChip()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$player_id = (int)Input::get('player_id');
	
		$server = Server::find(13);
		if (!$server) {
			return Response::json($msg, 403);
		}

		/*$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
		$response = $api->queryChip($start_time,$end_time,$player_id);
      	//var_dump($response);die();
    	if (!isset($response->error_code)) { 
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
     	 return Response::json($response);*/
		
		$game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id');

       	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		// Log::info($game->eb_api_url.' '.$game->eb_api_key .' '. $game->eb_api_secret_key);
		$platform = Platform::find(Session::get('platform_id'));


		if (! $platform) {
 		   	return Response::json($msg, 404);
	    }

  		$response = $api->queryChips($server->server_internal_id, $game_id, $platform->platform_id,$start_time,$end_time,$player_id);
 		if (!isset($response->error_code)) { 
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
     		return Response::json($response);
     	
	}
	//牌局统计 by mumu
	public function queryPokerIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.querypoker'),
			);
		return View::make('main', $data);
	}

	public function queryPoker()
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
       	Log::info('slave:'.$game->eb_api_url);
        $platform = Platform::find(Session::get('platform_id'));
        if (! $platform) {
           	return Response::json($msg, 404);
        }
        $response = $api->queryPoker($server->server_internal_id, $game_id, $platform->platform_id,$start_time, $end_time);
        Log::info(json_encode($response));
        return $api->sendResponse();
	}

	public function giveChip()
	{
		$msg = array(
			'code'  => Config::get('error_code.unknow'),
			'error' =>Lang::get('error.basic_input_error'), 
		);
		$rules = array(
			'chips' => 'required',
		);
		$validator = Validator::make( Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}

		$chips = Input::get('chips');
		$player_id = (int)Input::get('player_id');
		$player_uid = (string)Input::get('player_uid');
		$player_name = Input::get('player_name');
		//Log::info(var_export(Input::all(), true));
		if (isset($player_name)) {  //通过player_name查看
			$game = Game::find(Session::get('game_id'));
        	$game_id = Session::get('game_id');  //8
       		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        	$platform = Platform::find(Session::get('platform_id'));
        	if (! $platform) {
           		return Response::json($msg, 404);
        	}
        	$response = $api->getUserByPlayerName($platform->platform_id, $player_name, 1, $game_id, $tp_code = 'fb');
            $v = $response->body[0];
            $player_id = $v->player_id;
		}

		if (!isset($player_id) && !isset($player_uid)) {
			return Response::json($msg,403);
		}
		$server = Server::find(13);
		if (!$server) {
			return Response::json($msg, 403);
		}
		//Log::info(var_export($server, true));
		$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
		$response = $api->giveChips($chips, $player_id, $player_uid);
		if (!isset($response->error_code)) { //成功
			
			return $api->sendResponse();
		} else {
			return Response::json($msg, 403);
		}
	}

	//比赛信息查看
	public function pokerRoundsIndex()
	{
		$servers = Server::currentGameServers()->get();
		$table = $this->initPoker();
        $messages = $table->getData();
		$data = array(
			'content' => View::make('serverapi.poker.rounds',array('rounds' => $messages)),
		);
		return View::make('main', $data); 
	}
	
	public function pokerRoundsData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknown'),
		);
		$rules = array(
			'start_time' => 'required',
			'end_time' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);		
		}
		$table = $this->initPoker();
        $messages = $table->getData();
        $message = (array)$messages;
		$table2 = $this->initPoker2();
        $messages2 = $table->getData();
        $message2 = (array)$messages2;

		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		if ($start_time > $end_time) {
			$msg['error'] = Lang::get('serverapi.wrong_time');
			return Response::json($msg, 403);
		}
		$uid = trim(Input::get('uid'));
		$blind_type = trim(Input::get('blind_type'));
		$game = Game::find(Session::get('game_id'));
		$game_id = $game->game_id;
        $platform_id = Session::get('platform_id');
        //$rounds = intval(trim(Input::get('rounds')));
        $click_id = trim(Input::get('click_id'));
        $send_type = trim(Input::get('send_type'));
        $server = Server::find(13);
        
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $data = array();
        if ($send_type == 1) { //选择比赛名称
        	if ($click_id != 0) {
	        	$inter = ($end_time - $start_time)/86400;
	        	$days = intval(ceil($inter));
	        	$da = array();
	     		$gm_api = PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
	        		
	        	for ($i=0; $i < $days; $i++) {
	        		$start_time1 = date("Y-m-d 00:00:00", $start_time);
	        		$start_time2 = strtotime($start_time1);
	        		$start_time2 = $start_time2 + 86400*$i;
	        		$start = date("Y-m-d", $start_time2);
	        		//var_dump($start);die();
	        		$end_time = $start_time2+86399;
	        		$click[$i] = $api->pokerSignData($platform_id, $game->game_id, $start_time2, $end_time, $click_id)->body;
	        		//Log::info(var_export($click[$i], true));
	        		//Log::info(var_export($click[$i], true));	
		    		$sig[$i] = $gm_api->getRoomPlayer($start);
		    		//var_dump($click[$i][0]);die();
		    		//Log::info(var_export($sig[$i], true));
		    		$sign[$i] = $sig[$i]->regs;
		    		//Log::info(var_export($sign[$i], true));
		    		if (isset($click[$i][0])) {
		    			//$click[$i][0] = (array)$click[$i][0];
		    		//var_dump($click[$i][0]);die();
			    		$click[$i][0]->reg_num = 0;
			    		$click[$i][0]->mu_id = $click_id;
			    		$click[$i][0]->round_name = 0;
			    		$click[$i][0]->round_time = 0;
			    		//var_dump($click[$i]);die();
			    		$count = count($sign[$i]);
			    		for ($j=0; $j < $count; $j++) { 
			    			if ($sign[$i][$j]->mu_id == $click_id) {
			    				$click[$i][0]->reg_num = $sign[$i][$j]->reg_num;
			    			}
			    		}
			    		for ($k=0; $k < count($message); $k++) { 
			    			if ($click[$i][0]->mu_id == $message[$k]->Id) {
			    				$click[$i][0]->round_name = $message[$k]->Name;
			    			}
			    		}
		    		}else{
		    			continue;
		    		}
		    		//var_dump($click[$i][0]);die();
		    		
	        	}
	        	//var_dump($click);die();
	        	if (isset($click)) {
	        		return Response::json($click);
	        	}else {
	        		return Response::json($msg, 403);
	        	}
        	}else{
        		$inter = ($end_time - $start_time)/86400;
	        	$days = intval(ceil($inter));
	        	$da = array();
	     		$gm_api = PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
	        		
	        	for ($i=0; $i < $days; $i++) {
	        		$start_time1 = date("Y-m-d 00:00:00", $start_time);
	        		$start_time2 = strtotime($start_time1);
	        		$start_time2 = $start_time2 + 86400*$i;
	        		$start = date("Y-m-d", $start_time2);
	        		$end_time = $start_time2+86399;
	        		$click[$i] = $api->pokerSignData($platform_id, $game->game_id, $start_time2, $end_time, $click_id = 0)->body;	
		    		$sig[$i] = $gm_api->getRoomPlayer($start);
		    		//Log::info(var_export($sig[$i],true));
		    		$sign[$i] = $sig[$i]->regs;
		
		    		if (isset($click[$i][0])) {
		    			$click[$i][0]->reg_num = 0;
			    		$count = count($sign[$i]);
			    		for ($j=0; $j < $count; $j++) { 
			    			//if ($sign[$i][$j]->mu_id == $click_id) {
			    			$click[$i][0]->reg_num += $sign[$i][$j]->reg_num;
			    			
			    		}
		    		}else{
		    			continue;
		    		}
		    		
		    		
	        	}
	        	if (isset($click)) {
	        		return Response::json($click);
	        	}else {
	        		return Response::json($msg, 403);
	        	}
        	}

	    } 
	    elseif ($send_type == 2) { //通过uid查询
	    	$response = $api->getPlayerIdByUID($platform_id, $uid, $game_id);
	    	if (!empty($response->body)) {
	    		$player_id = $response->body[0]->player_id;
	    	}
	    	if (isset($player_id)) {
	    		$gm_res = $api->getGamesByUID($platform_id, $game_id, $server->server_internal_id, $player_id, $start_time, $end_time);
	    		if (isset($gm_res)) {
	    			$sign = $gm_res->body;
	    			$sign = (array)$sign;
	    		}
	    		for ($i=0; $i < count($sign); $i++) { 
	    			$sign[$i]->round_name = '';
	    			for ($j=0; $j < count($message2); $j++) {
	    				if (intval($sign[$i]->game_name) == intval($message2[$j]->R_Id)) {
	    					$sign[$i]->round_name = $message2[$j]->R_Name;
	    				}
			        }
	    		}
	    	}
	    	if (isset($sign)) {
	    		return Response::json($sign);
	    	} else{
	    		return Response::json($msg, 403);
	    	}	
	    }
	}

	// 牌局查询
	public function pokerInfoIndex()
	{
		$servers = Server::currentGameServers()->get();
		$table = $this->initPoker3();
        $blind = $table->getData();
		$data =array(
			'content' => View::make('serverapi.poker.info', array('blinds' => $blind)),
		);
		return View::make('main', $data);

	}

	public function pokerInfoData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
		);
		$rules = array(
			'blind_type' => 'required',
			'start_time' => 'required',
			'end_time' => 'required',
		);
		$validator = Validator::make(Input::all(),$rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		//获取输入信息
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$uid = trim(Input::get('uid'));
		$blind_type = trim(Input::get('blind_type'));
	    $table2 = $this->initPoker2();
        $messages2 = $table2->getData();
        $message2 = (array)$messages2;
        $table = $this->initPoker3();
        $blind = $table->getData();
        $blind = (array)$blind;
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $server = Server::find(13);
        if ($blind_type > 0) {
        	for ($i=0; $i < count($blind); $i++) { 
	        	if ($blind[$i]->id == $blind_type) {
	        		$arr = explode('/', $blind[$i]->blind);
	        		break;
	        	}
	        }
	        $small_blind = $arr[0];
	        $big_blind = $arr[1];
	        $str = array();
	        for ($i=0; $i < count($message2); $i++) { 
	        	if (($message2[$i]->R_BigBlind == $big_blind) && $message2[$i]->R_Blind == $small_blind) {
	        		$str[]= $message2[$i]->R_Id;
	        	}
	        }
	        $str = implode(',', $str);
        } else {
        	$str = '';
        }
        //var_dump($str);die();
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        /*if (isset($uid)) {
        	$response = $api->getPlayerIdByUID($platform_id, $uid, $game_id);
        }
        if (isset($response->body[0])) {
        	$body = $response->body[0];
        	$player_id = $body->player_id;
        }*/
        $ids = '';
        $data_arr = array();
        $info = $api->getPokerGameInfo($platform_id, $server->server_internal_id, $game_id, $start_time, $end_time, $str);
        //var_dump($info);die();
        if (isset($info->body)) {
        	$players = $info->body;
        	if ($players) {
        		$len = count($players);
	        	for ($i=0; $i < $len; $i++) { 
	        		$data_arr[]= $players[$i]->date;  
	        	}
	        
		        $aa = array_unique($data_arr);
		        $straa = implode(',', $aa);
		        $data_array = explode(',', $straa); 
		        $count = '';
		        for ($i=0; $i < count($data_array); $i++) { 
		        	$data[$i] = new stdClass();
		        	$data[$i]->date = '';
		        	$data[$i]->players = '';
		        	for ($j=0; $j < count($players); $j++) { 
		        		if ($data_array[$i]== $players[$j]->date) {
		        			$data[$i]->date = $data_array[$i];
		        			$data[$i]->players .= '|'.$players[$j]->players;
		        		}
		        	} 
		        	$player_arr[$i] = explode('|', $data[$i]->players);
			        for ($k=0; $k < count($player_arr[$i]); $k++) { 
			        	if (($player_arr[$i][$k] == '') ) {
			        		unset($player_arr[$i][$k]);
			        	}
			        	
			        }
			        foreach ($player_arr[$i] as $key => $value) {
			        	if ($value == 0) {
			        		unset($player_arr[$i][$key]);
			        	}
			        }
		        }
		        //var_dump($player_arr);die();
		        for ($h=0; $h < count($player_arr); $h++) { 
		        	$player_array[$h] = array_count_values($player_arr[$h]);
		        	$dd[$h] = new stdClass();
		        	$dd[$h]->date =0;
		        	$dd[$h]->num1 =0;
		        	$dd[$h]->num2 =0;
		        	$dd[$h]->num3 =0;
		        	$dd[$h]->num4 =0;
		        	$dd[$h]->num5 =0;
		        	$dd[$h]->num6 =0;
		        	$dd[$h]->num7 =0;
		        	$len = count($player_array[$h]);
		        	foreach ($player_array[$h] as $key => $val) {
		        		if (($val>0) && ($val <=10)) {
		        			$dd[$h]->num2 ++;
		        		}elseif (($val>11) && ($val <=20)) {
		        			$dd[$h]->num3 ++;
		        		}elseif (($val>21) && ($val <=40)) {
		        			$dd[$h]->num4 ++;
		        		}elseif (($val>40) && ($val <=50)) {
		        			$dd[$h]->num5 ++;
		        		}elseif (($val>50) && ($val <=100)) {
		        			$dd[$h]->num6 ++;
		        		}elseif (($val>100) ) {
		        			$dd[$h]->num7 ++;
		        		}
		        	}
		        	$dd[$h]->date = $data[$h]->date;
		        	
		        }
		        for ($i=0; $i < count($dd); $i++) { 
		        	$start_time = strtotime($dd[$i]->date);
		        	$end_time = $start_time + 86399;
		        	$res = $api->getPokerUser($platform_id, $game_id,$server->server_internal_id,$start_time, $end_time);
		        	$res2 = $api->getAllUsers($platform_id, $game_id,$server->server_internal_id,$start_time, $end_time);
		        	if (isset($res->body[0]) && isset($res2->body[0])) {
		        		$ress = $res->body[0];
		        		$ress2 = $res2->body[0];
		        		$dd[$i]->num1 = $ress2->user_num-$ress->log_num;	
		        	}
		        	
		        }
		        if (isset($dd)) {
		        	return Response::json($dd);
		        }else{
		        	return Response::json($msg, 403);
		        }
        	}else{
        		$dd[0] = new stdClass;
        		$dd[0]->date =0;
	        	$dd[0]->num1 =0;
	        	$dd[0]->num2 =0;
	        	$dd[0]->num3 =0;
	        	$dd[0]->num4 =0;
	        	$dd[0]->num5 =0;
	        	$dd[0]->num6 =0;
	        	$dd[0]->num7 =0;
	        	return Response::json($dd);
        	}
    	} else{
    		$msg['error'] = "没有数据";
    		return Response::json($msg, 403);
    	}
        
	}

	public function allChipsIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.payment.userchips'),
		);
		return View::make('main', $data);
	}

	public function allChipsData()
	{
		$msg = array(
			'code' => Lang::get('errorcode.unknow'),
			'error' => ''
		);
		$rules = array(
			'start_time' => 'required',
		);
		/*$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			$msg['error'] = Lang::get('error.basic_input_error'); 
			return Response::json($msg, 403);
		}*/
		$game_id = Session::get('game_id');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$chips = DB::table('log')->where('game_id', $game_id)->where('log_key', 'chips')->where('user_id','>=', $start_time)->where('user_id','<=', $end_time)->get();
		if (!empty($chips)) {
			foreach ($chips as $key => $value) {
				$chips = $value->old_value;
				$chips_array = explode(",", $chips);
				unset($chips_array[count($chips_array)-1]);
				$data = array(
					'date' => date("Y-m-d H:i:s", $value->user_id-86400),
			 		'all' => $chips_array[0],
			 		'chips1'=>isset($chips_array[1]) ? $chips_array[1] : 0,
			 		'chips2'=>isset($chips_array[2]) ? $chips_array[2] : 0,
			 		'chips3'=>isset($chips_array[3]) ? $chips_array[3] : 0,
			 		'chips4'=>isset($chips_array[4]) ? $chips_array[4] : 0,
			 		'chips5'=>isset($chips_array[5]) ? $chips_array[5] : 0,
			 		'chips6'=>isset($chips_array[6]) ? $chips_array[6] : 0,
			 		'chips7'=>isset($chips_array[7]) ? $chips_array[7] : 0,
			 		'chips8'=>isset($chips_array[8]) ? $chips_array[8] : 0,
			 		'chips9'=>isset($chips_array[9]) ? $chips_array[9] : 0,
			 		'chips10'=>isset($chips_array[10]) ? $chips_array[10] : 0,
			 		'gold'=>isset($chips_array[11]) ? $chips_array[11] : 0,
			 		
			 	);
			 	$arr[] = $data;
			 	$data = (object)$arr;
			 	$result = array(
			 		'result' => $data
			 	); 
			}
		}else{
			return Response::json(array('error'=>'没有查询到日志数据'), 403);
		}
		$aa = $arr[count($arr)-1];
		$date1 = strtotime($aa['date']);

		$days = ceil(($end_time - $date1)/86400);
		if ($days) {
			 
			for ($i=1; $i < $days-1; $i++) { 
				$server = Server::find(13);
				$start = date("Y-m-d", $date1 + 86400*($i+1));
				$api = PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
				$response = $api->getPokerChips($start);
				if (isset($response->sys_chips)) {
				 	$body = $response->sys_chips;
				 	$data = array(
				 		//'date' => date('Y-m-d H:i:s', $start_time),
				 		'all' => isset($body->all) ? $body->all : 0,
				 		'chips1'=>isset($body->chips1) ? $body->chips1 : 0,
				 		'chips2'=>isset($body->chips2) ? $body->chips2 : 0,
				 		'chips3'=>isset($body->chips3) ? $body->chips3 : 0,
				 		'chips4'=>isset($body->chips4) ? $body->chips4 : 0,
				 		'chips5'=>isset($body->chips5) ? $body->chips5 : 0,
				 		'chips6'=>isset($body->chips6) ? $body->chips6 : 0,
				 		'chips7'=>isset($body->chips7) ? $body->chips7 : 0,
				 		'chips8'=>isset($body->chips8) ? $body->chips8 : 0,
				 		'chips9'=>isset($body->chips9) ? $body->chips9 : 0,
				 		'chips10'=>isset($body->chips10) ? $body->chips10 : 0,
				 		'gold'=>isset($body->gold) ? $body->gold: 0,
				 	);
				 	//$length = count($data);
				 	$sql = "";
				 	foreach ($data as $key => $value) {
				 		$sql .= $value . ',';
				 	}
				 	$data['date'] = date('Y-m-d', strtotime($start)-86400);
				 	$log = new EastBlueLog;
				 	$log->log_key = "chips";
				 	$log->game_id = $game_id;
				 	$log->user_id = strtotime($start);
				 	$log->old_value = $sql;
				 	$aa = DB::table('log')->where('game_id', $game_id)->where('log_key', 'chips')->where('user_id','=', $log->user_id)->get();
				 	if (!$aa && $data['all'] != 0) {
				 		$log->save();
				 	}
				 	$arr[] = $data;
				 	$result = array(
				 		'result' => $arr,
				 	);
			 	
				}
			}
		}
		
		if (isset($result)) {
			return Response::json($result);
		}else{
		 	$msg['error'] = Lang::get('error.basic_not_found');
		 	return Response::json($msg, 403);
		 }
	}

	private function initPoker()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'poker' . '/Tournament.txt');
        return $table;
    }
    private function initPoker2()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'poker' . '/RoomListRule.txt');
        return $table;
    }
    private function initPoker3()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'poker' . '/blind1.txt');
        return $table;
    }

    /*
		Created By XianShui  德州 发送玩家信息 -- 单人   批量
    */

    public function sendMessageIndex()
    {
    	$data = array(
    		'content' => View::make('serverapi.poker.users.message')
    	);
    	return View::make('main', $data);
    } 
    public function sendMessageOperate()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error' => ''
    	);
    	$rules = array(
    		'message' => 'required'
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg['error'] = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$player_name = Input::get('player_name');
    	$player_id = Input::get('player_id');
    	$message = Input::get('message');
    	$platform_id = Session::get('platform_id');
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	$server = Server::find(13);
    	$server_internal_id = $server->server_internal_id;
    	if (!$player_id && $player_name) { //输入player_name
    		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    		$response = $api->getIdByName2($platform_id, $game_id, $server_internal_id,$player_name, '');
    		if ($response->http_code == 200) {
    			if (isset($response->body)) {
    				$body = $response->body;
    				if (isset($body[0]->player_id)) {
    					$player_id = $body[0]->player_id;
    				}else{
    					$msg['error'] = Lang::get('serverapi.player_id_not_found');
    					return Response::json($msg, 403);
    				}
    			}
    		}
    	}
    	if (isset($player_id)) {
    		$game_api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
    		$response = $game_api->sendMessage($player_id, $message);
			if ($response->is_ok) {
				$result = array(
					'status' => 'ok',
					'msg' => (isset($player_name) ? $player_name : '') . ' ( ' . $player_id . ')' . '--OK--' . $response->is_ok
				);
			}
			else{
				$result = array(
					'status' => 'ok',
					'msg' => (isset($player_name) ? $player_name : '') . ' ( ' . $player_id . ')' . '--FAIL--' . $response->is_ok
				);
			}
    	
    	}
    	return Response::json($result);
    }



    public function sendMessageGroupIndex()
    {
    	$data = array(
    		'content' => View::make('serverapi.poker.users.message2')
    	);
    	return View::make('main', $data);
    }

    public function sendMessageGroupOperate()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error' => ''
    	);
    	$rules = array(
    		'type' => 'required',
    		'players' => 'required',
    		'message' => 'required'
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg['error'] = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$type = Input::get('type');
    	$players = Input::get('players');
    	$message = Input::get('message');
    	$platform_id = Session::get('platform_id');
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	$server = Server::find(13);
    	if (!$server) {
    		$msg['error'] = Lang::get('error.basic_not_found');
    		return Response::json($msg, 403);
    	}
    	$server_internal_id = $server->server_internal_id;
    	$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$game_api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
    	$success = $fail = '';
    	$result1 = array();
    	$result2 = array();
    	$data = array();
    	if ($type == 1) { //player_name
    		$player_arr = explode("\n", $players);
    		foreach ($player_arr as $key => $value) {
    			$player = $slave_api->getIdByName2($platform_id, $game_id, $server_internal_id,$value, '');
    			if ($player->http_code == 200 && isset($player->body)) {
    					$body = $player->body;
    				 	$player_id = $body[0]->player_id;
    				 	if (isset($player_id)) {
    				 		$response = $game_api->sendMessage($player_id, $message);
	    				 	if ($response->is_ok) {
	    				  		$success .= "--(" . (isset($value) ? $value : '') . ':'. (isset($player_id) ? $player_id : '')  .')';
	    					} else{
	    						$fail .= "--(" . (isset($value) ? $value : '') . ':'. (isset($player_id) ? $player_id : '')  .')';
	    					}
    				 	}else{
    				 		$fail .=  "--(" . (isset($value) ? $value : '') . ':' . (isset($player_id) ? $player_id : '') .')';
    				 	}
    			}else{
    				$fail .=  "--(" . (isset($value) ? $value : '') . ':' . (isset($player_id) ? $player_id : '') .')';
    			}
    			unset($player_id);
    			unset($response);
    		}
    		$result1 = array(
    			'status' => 'ok',
    			'msg' => $success
    		);
    		$result2 = array(
    			'status' => 'error',
    			'msg' => $fail
    		);
    	}elseif ($type == 2) { // player_id
    		$player_arr = explode("\n", $players);
    		foreach ($player_arr as $key => $value) {
    			$response = $game_api->sendMessage($value, $message);
    			if ($response->is_ok) { //发送成功
    				if ($response->is_ok) {
				  		$success .= "--(" .  (isset($value) ? $value : '')  .')';
					} else{
						$fail .= "--(" .  (isset($value) ? $value : '')  .')';
					}
    			}
    			//unset($player_id);
    			unset($response);
    		}
    		$result1 = array(
    			'status' => 'ok',
    			'msg' => $success
    		);
    		$result2 = array(
    			'status' => 'error',
    			'msg' => $fail
    		);
    	}

    	$data = array(
    		'result1' => $result1,
    		'result2' => $result2
    	);
    	return Response::json($data);
    }

    //德扑退款查询
    public function userPokerIndex(){
    	$server = Server::currentGameServers();
		$data = array(
			'content' => View::make('serverapi.poker.users.refund', array('server' => $server)),
		);
		return View::make('main', $data);
    }
    public function userPokerData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error'),
		);
		//找到server表中id为13的数据，代表德州扑克
		$server = Server::find(13);
		if (!$server) {
			return Response::json($msg, 403);
		}
		$start_time = strtotime(trim(Input::get('start_time')));//strtotime() 函数将任何英文文本的日期时间描述解析为 Unix 时间戳。
		$end_time = strtotime(trim(Input::get('end_time')));
		$page = Input::get('page');
		$per_page = Input::get('per_page');
		$page = isset($page) ? $page : 1;
		$per_page = isset($per_page)? $per_page : 30;
		$game_id = Session::get('game_id');//对应的游戏
		$platform_id = Session::get('platform_id');//对应的平台
		$game = Game::find($game_id);
		if (!$server) {
			return Response::json($msg, 403);
		}
		//var_dump($game);
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$res = $api->getPokerRefund($platform_id, $start_time, $end_time,$page, $per_page);
		//var_dump($res);die();
		if ($res->http_code== 200 && isset($res->body)) {
			$body = $res->body;
			return Response::json($body);
		}
	    else{
			return Response::json($msg, 403);
		}

	}
	public function initTable()
	{
		 $table = Table::init(
                public_path() . '/table/poker/Action.txt');
        return $table;
	}


}