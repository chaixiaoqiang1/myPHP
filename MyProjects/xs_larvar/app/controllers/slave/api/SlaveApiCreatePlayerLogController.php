<?php
class SlaveApiCreatePlayerLogController extends \BaseController {
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
	public function getPlayerRank()
	{
		$servers = Server::currentGameServers()->get();
		$data = array(
				'content' => View::make('slaveapi.rank.index', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function getPlayerRankData()
	{
		$msg = array(
				'code' => Lang::get('errorcode.unknown'),
				'msg' => Lang::get('errorcode.server_not_found')
		);
		$page = ( int ) Input::get('page');
		$page = $page > 0 ? $page : 1;
		$server_id = ( int ) Input::get('server_id');
		$server = Server::find($server_id);
		$is_created_time = Input::get('is_created_time');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time'))); 
		$levelup_time = strtotime(trim(Input::get('levelup_time')));
		$level_lower_bound = (int)Input::get('level_lower_bound');
		$level_upper_bound = (int)Input::get('level_upper_bound');
		if(! $server)
		{
			return Response::json($msg, 403);
		}
		$platform_id = Session::get('platform_id');
		if(!isset($platform_id))
		{
			Log::info(var_export('can not get platform_id' ,true));
		}
		$game = Game::find(Session::get('game_id'));
		if(!isset($game))
		{
			Log::info(var_export('can not get game info' ,true));
		}
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if(!isset($api))
		{
			Log::info(var_export('can not connect slave server' ,true));
		}
		$response = $api->getPlayerRank($platform_id, $game->game_id, $server->server_internal_id, $is_created_time, $start_time, $end_time, $levelup_time, 
											$page, 30, $level_lower_bound, $level_upper_bound);
		if(!isset($response))
		{
			Log::info(var_export('can not get data from slave DB' ,true));
		}
		$body = $response->body;
		if($response->http_code == 200)
		{
			foreach ( $body->items as &$v )
			{
				$v->levelup_time = date('Y-m-d H:i:s', $v->levelup_time);
				$v->created_time = date('Y-m-d H:i:s', $v->created_time);
			}
			unset($v);
			return Response::json($body);
		} else
		{
			return Response::json($body, $response->http_code);
		}
	}

	public function downloadGetPlayerRank()
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

    public function downloadGetPlayerRankData()
    {
    	$msg = array(
    			'code' => Lang::get('errorcode.unknown'),
    			'msg' => Lang::get('errorcode.server_not_found')
    	);
    	$server_id = ( int ) Input::get('server_id');
    	$server = Server::find($server_id);
    	$is_created_time = Input::get('is_created_time');
    	$start_time = strtotime(trim(Input::get('start_time')));
    	$end_time = strtotime(trim(Input::get('end_time'))); 
    	$levelup_time = strtotime(trim(Input::get('levelup_time')));
    	$level_lower_bound = (int)Input::get('level_lower_bound');
		$level_upper_bound = (int)Input::get('level_upper_bound');

    	if(! $server)
    	{
    		return Response::json($msg, 403);
    	}
    	$platform_id = Session::get('platform_id');
    	if(!isset($platform_id))
    	{
    		Log::info(var_export('can not get platform_id' ,true));
    	}
    	$game = Game::find(Session::get('game_id'));
    	if(!isset($game))
    	{
    		Log::info(var_export('can not get game info' ,true));
    	}
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	if(!isset($api))
    	{
    		Log::info(var_export('can not connect slave server' ,true));
    	}
    	$response = $api->getPlayerRank($platform_id, $game->game_id, $server->server_internal_id, $is_created_time, $start_time, $end_time, $levelup_time, 
    										$page = 99999999, 30, $level_lower_bound, $level_upper_bound);
    	if(!isset($response))
    	{
    		Log::info(var_export('can not get data from slave DB' ,true));
    	}

    	$data = array();
    	$title = array(
    	        'rank',
    	        'player_id',
    	        'player_name',
    	        'level',
    	        'levelup_time',
    	        'created_time',
    	        'created_ip',
    	);
    	$now = time();
    	$file = storage_path() . "/cache/" . $now . ".csv";
    	$csv = CSV::init($file, $title);

    	$body = $response->body;
    	if($response->http_code == 200)
    	{
    		foreach ( $body->items as &$v )
    		{
    			$v->levelup_time = date('Y-m-d H:i:s', $v->levelup_time);
    			$v->created_time = date('Y-m-d H:i:s', $v->created_time);

    			$data = array(
        			     'rank' =>$v->rank,
        			     'player_id' => $v->player_id,
        			     'player_name' => isset($v->player_name) ? $v->player_name : '',
        			     'level' => $v->level,
        			     'levelup_time' => $v->levelup_time,
        			     'created_time' => $v->created_time,
        			     'created_ip' => $v->created_ip,

        		);
        		$csv->writeData($data);
    		}
    		unset($v);
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

	public function getPlayerLevelTrend()
	{
		$servers = Server::currentGameServers()->get();
		$data = array(
				'content' => View::make('slaveapi.leveltrend.index', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function getPlayerLevelTrendData()
	{
		$msg = array(
				'code' => Lang::get('errorcode.unknown'),
				'error' => Lang::get('error.server_not_found')
		);
		$server_id = ( int ) Input::get('server_id');
		$server = Server::find($server_id);
		if(! $server)
		{
			return Response::json($msg, 403);
		}
		$is_anonymous = ( int ) Input::get('is_anonymous');
		$by_create_time = Input::get('by_create_time');
		if($by_create_time){
			$start_time = strtotime(trim(Input::get('start_time')));
			$end_time = strtotime(trim(Input::get('end_time')));
		}else{
			$start_time = 0;
			$end_time = 0;
		}
		
		$game = Game::find(Session::get('game_id'));
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$response = $api->getPlayerLevelTrend(Session::get('platform_id'), $game->game_id, $server->server_internal_id, $is_anonymous, $start_time, $end_time);
		$body = $response->body;
		if($response->http_code == 200)
		{
			return Response::json($body);
		} else
		{
			return Response::json($body, $response->http_code);
		}
	}
	public function getPlayerRetention()
	{
		$servers = Server::currentGameServers()->get();
		$game_id = Session::get('game_id');
		$ifshowanonymous = 1;
		$ifshow30days = 0;
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$ifshowanonymous = 0;
			$ifshow30days = 1;
		}
		$data = array(
				'content' => View::make('slaveapi.retention.index', array(
						'servers' => $servers,
						'ifshowanonymous' => $ifshowanonymous,
						'ifshow30days' => $ifshow30days,
				))
		);
		return View::make('main', $data);
	}
	public function getPlayerRetentionData()
	{
		$msg = array(
				'code' => Lang::get('errorcode.unknown'),
				'msg' => Lang::get('errorcode.server_not_found')
		);
		
		$server_id = (int)Input::get('server_id');
		$game_id = (int)Session::get('game_id');
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$is_anonymous = 9;
		}else{
			$is_anonymous = ( int ) Input::get('is_anonymous');
		}
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$game = Game::find($game_id);
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if ($server_id == 0){
			$k=0;
			$data = array();
			foreach ($this->servers as $key => $server) {
                $server_internal_id = $server->server_internal_id;
                if (!$server_internal_id) {
                    return Response::json($msg, 403);
                }
                $response = $api->getPlayerRetention($game->game_id, $server_internal_id, $start_time, $end_time, $is_anonymous);
                $body = $response->body;
                $data1[] = (array)$body;
                $j=0;
                $day=ceil(($end_time-$start_time)/86400);
   				for($i=0;$i<$day;$i++){
   					$data[$k][$i] = new stdClass();
   					$data[$k][$i]->retention_time = 0;
   					$data[$k][$i]->created_player_number = 0;
   					$data[$k][$i]->days_2 = 0;
   					$data[$k][$i]->days_3 = 0;
   					$data[$k][$i]->days_4 = 0;
   					$data[$k][$i]->days_5 = 0;
   					$data[$k][$i]->days_6 = 0;
   					$data[$k][$i]->days_7 = 0;
   					$data[$k][$i]->days_14 = 0;
   					if(in_array($game_id, Config::get('game_config.mobilegames'))){
   						$data[$k][$i]->days_30 = 0;
   					}
   					if(isset($data1[0][$j]) && (date('Y-m-d',$data1[0][$j]->retention_time) == date('Y-m-d',$start_time + 86400 * $i))){
   						$data[$k][$i] = $data1[0][$j];
   						$j++;
   					}else{
   						$data[$k][$i]=$data[$k][$i];
   					}

   				}
   				$k++;
   				unset($data1);
   				if(in_array($game_id, Config::get('game_config.yysggameids'))){	//这两个游戏只有一个服务器，slave端计算日志库的方式写死的，因此多次查询查询的都是同一个服务器下的留存
   					break;
   				}
   				//Log::info(var_export($data,true));die();
            }
            $len = count($data);
            $total = array();
            $lenn = count($data[0]);
            $day=ceil(($end_time-$start_time)/86400);
            for ($i=0; $i < $day; $i++) { 
            	$total[$i] = new stdClass();
            	$total[$i]->retention_time = 0;
            	$total[$i]->created_player_number= 0;
            	$total[$i]->days_2 = 0;
            	$total[$i]->days_3 = 0;
            	$total[$i]->days_4 = 0; 
            	$total[$i]->days_5 = 0; 
            	$total[$i]->days_6 = 0; 
            	$total[$i]->days_7 = 0; 
            	$total[$i]->days_14 =0;
            	if(in_array($game_id, Config::get('game_config.mobilegames'))){
            		$total[$i]->days_30 =0;
            	}
            }
            for ($i=0; $i < $day; $i++) { 
            	 for ($j=0; $j < $len; $j++) { 
             		$time=$start_time + 86400 * $i;
             		$total[$i]->retention_time = date('Y-m-d H:i:s', $time);
             		$total[$i]->created_player_number += $data[$j][$i]->created_player_number;
             		$total[$i]->days_2 += $data[$j][$i]->days_2;
             		$total[$i]->days_3 += $data[$j][$i]->days_3;
            		$total[$i]->days_4 += $data[$j][$i]->days_4;
            		$total[$i]->days_5 += $data[$j][$i]->days_5;
            		$total[$i]->days_6 += $data[$j][$i]->days_6;
            		$total[$i]->days_7 += $data[$j][$i]->days_7;
            		$total[$i]->days_14 += $data[$j][$i]->days_14;
            		if(in_array($game_id, Config::get('game_config.mobilegames'))){
            			$total[$i]->days_30 += $data[$j][$i]->days_30;
            		}
            	 }
            }
            return Response::json($total);
		}elseif($server_id != 0){

            $server = Server::find($server_id);
            if (!$server) {
                return Response::json($msg, 403);
            }
            $server_internal_id = $server->server_internal_id;
            if (!$server_internal_id) {
                return Response::json($msg, 403);
            }
            $response = $api->getPlayerRetention($game->game_id, $server->server_internal_id, $start_time, $end_time, $is_anonymous);
            $body = $response->body;
            if($response->http_code == 200)
            {
            	foreach ( $body as &$v )
            	{
            		$v->retention_time = date('Y-m-d H:i:s', $v->retention_time);
            	}
            	unset($v);
            	if(empty($body)){
            		return Response::json(array('error'=>'No data from slave.'), 403);
            	}
            	return Response::json($body);
            } else
            {
            	return Response::json($body, $response->http_code);
            }
        }	
	}

	public function getPlayerChannelRetention(){	//手游专用根据游戏包的channel信息查询留存率
		$servers = Server::currentGameServers()->get();
		$game_id = Session::get('game_id');
		if(!in_array($game_id, Config::get('game_config.mobilegames'))){
			App::abort(404);
		}
		$data = array(
				'content' => View::make('slaveapi.retention.channel_index', array(
						'servers' => $servers,
				))
		);
		return View::make('main', $data);
	}

	public function getPlayerChannelRetentionData(){
		$msg = array(
				'code' => Lang::get('errorcode.unknown'),
				'msg' => Lang::get('errorcode.server_not_found')
		);

		$game_id = (int)Session::get('game_id');
		$is_anonymous = 9;
		$channel_name = Input::get('channel_name');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$game = Game::find($game_id);
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$server_id = (int)Input::get('server_id');

		if('0' == $server_id){
			return Response::json(array('error'=>'请选择服务器'), 403);
		}else{
			$server = Server::find($server_id);
			$result = $api->getPlayerChannelRetention($game_id, $server->server_internal_id, $start_time, $end_time, $is_anonymous, $channel_name);
			if('200' != $result->http_code){
				return Response::json(array('error'=>'查询出错'), 403);
			}else{
				$result = $result->body;
				foreach ($result as &$value) {
					$user = array();
		            $user['start_time'] = $value->retention_time;
		            $user['end_time'] = $value->retention_time+86400;
		            $user['platform_id'] = Session::get('platform_id');
		            $user['interval'] = 0;
		            $user['check_type'] = 0;
		            $user['game_id'] = $game_id;
		            $user['serach_type'] = 1;
		            $user['channel_type'] = $value->channel;
		            $user['source'] = '';
		            $response = $api->getUserDevice($user);
		            if(200 == $response->http_code){
		            	if(isset($response->body[0]->count)){
		            		$value->setupnum = $response->body[0]->count;
		            	}else{
		            		$value->setupnum = 0;
		            	}
		            }else{
		            	$value->setupnum = 0;
		            }
		            unset($user);
		            unset($response);
					$value->retention_time = date('Y-m-d H:i:s', $value->retention_time);
				}
				return Response::json($result);
			}
		}
	}

	public function getPlayerLevelUp()
	{
		$servers = Server::currentGameServers()->get();
		$data = array(
				'content' => View::make('slaveapi.player.levelup', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function getPlayerLevelUpData()
	{
		$msg = array(
				'code' => Lang::get('errorcode.unknown'),
				'msg' => Lang::get('errorcode.server_not_found')
		);
		$player_name = Input::get('player_name');
		$player_id = Input::get('player_id');
		$server_id = ( int ) Input::get('server_id');
		$server = Server::find($server_id);
		
		if(!($player_name || $player_id) || ! $server)
		{
			$msg['error'] = Lang::get('error.basic_input_error');
			return Response::json($msg, 403);
		}
		$game = Game::find($server->game_id);
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$response = $api->getPlayerLevelUp($player_id, $player_name, $game->game_id, $server->server_internal_id);
		if($response->http_code != 200)
		{
			return Response::json($response->body, $response->http_code);
		}
		foreach ( $response->body as $v )
		{
			$v->levelup_time = date('Y-m-d H:i:s', $v->levelup_time);
		}
		return Response::json($response->body);
	}
	public function getCreatePlayerInfo()
	{
		$servers = Server::currentGameServers()->get();
		$data = array(
				'content' => View::make('slaveapi.player.playerinfo', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function getCreatePlayerInfoData()
	{
		$msg = array(
				'code' => Lang::get('errorcode.unknown'),
				'error' => Lang::get('error.server_not_found')
		);
		$server_id = ( int ) Input::get('server_id');
		$uid = trim(Input::get('uid'));
		$player_name = Input::get('player_name');
		$player_id = ( int ) Input::get('player_id');
		
		$server = Server::find($server_id);
		if(! $server)
		{
			return Response::json($msg, 403);
		}
		
		$game = Game::find($server->game_id);
		$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		if($uid)
		{ // 如果uid查询
			$response = $api->getCreatePlayerInfo($uid, $player_id = '', $player_name = '', $game->game_id, $server->server_internal_id);
			// var_dump($response);die();
			if($response->http_code == 200)
			{
				return Response::json($response->body);
			} else
			{
				$msg = array(
						'error' => 'Player not found'
				);
				return Response::json($msg);
			}
		} else if($player_id)
		{ // 如果player_id查询
			$response = $api->getCreatePlayerInfo($uid = '', $player_id, $player_name = '', $game->game_id, $server->server_internal_id);
			if($response->http_code == 200)
			{
				return Response::json($response->body);
			} else
			{
				$msg = array(
						'error' => 'Player not found'
				);
				return Response::json($msg);
			}
		}else if($player_name)
		{ // 如果player_name查询
			$response = $api->getCreatePlayerInfo($uid = '', $player_id='', $player_name, $game->game_id, $server->server_internal_id);
			if($response->http_code == 200)
			{
				return Response::json($response->body);
			} else
			{
				$msg = array(
						'error' => 'Player not found'
				);
				return Response::json($msg);
			}
		}

	}
	
	//选取服务器
	public function init_table()
	{
	    $game = Game::find(Session::get('game_id'));
	    $table = Table::init(
	            public_path() . '/table/' . $game->game_code . '/consume.txt');
	    return $table;
	}

	private function initTable3()
	{
	    $game = Game::find(Session::get('game_id'));
	    $table = Table::init(public_path() . '/table/' . 'flsg'. '/server.txt');
	    return $table;
	}

	public function ScoreRankIndex(){
		$servers = Server::currentGameServers()->get();
		$data = array(
			'content' => View::make('slaveapi.player.scorerank',array(
					'servers' => $servers
				))
		);
		return View::make('main',$data);

	}

	public function ScoreRankData(){
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$server_ids = Input::get('server_id');
	    if('0' == $server_ids){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $start_time = strtotime(Input::get('start_time'));
	    $end_time = strtotime(Input::get('end_time'))+10;
		$activity_type = (int)Input::get('activity_type');
		switch ($activity_type) {
			case '0':
				$type = 'shenShu';
				break;
			case '1':
				$type = 'daLuanDou';
				break;
			case '2':
				$type = 'baseRank';
				break;
			case '3':
				$type = 'baseRank_3';
				break;
			case '4':
				$type = 'baseRank_4';
				break;
			case '5':
				$type = 'baseRank_5';
				break;
			case '6':
				$type = 'baseRank_6';
				break;
			default:
				break;
		}
		$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$result = array();
		foreach ($server_ids as $server_id) {
			$server = Server::find($server_id); 
			if(! $server)
			{
			    $msg['error'] = Lang::get('error.basic_not_found');
			    return Response::json($msg, 404);
			}
			$response = $slave_api->getScoreRankData($type, $start_time, $end_time, $game_id, $server->server_internal_id);
			if($response->http_code == 200){
				$body = $response->body;
				foreach ($body as $value) {
					if('baseRank' == $type){
						switch ($value->log_type) {
							case 'baseRank_':
								$temp_title = '不能区分的鲜花排行';
								break;
							case 'baseRank_2':
								$temp_title = '送花排行';
								break;
							case 'baseRank_3':
								$temp_title = '收花排行';
								break;
							default:
								break;
						}
					}else{
						$temp_title = '';
					}
					$temp = json_decode($value->json_data);
					foreach($temp as $key => $row){
						if(in_array($type, array('shenShu','baseRank','baseRank_4','baseRank_5','baseRank_6'))){
							$temp_res[$key] = $row->Rank;
						}else{
							$temp_res[$key] = $row->Score;
						}  
					    $server2 = Server::where('server_internal_id',$row->ServerID)->where('game_id',$game_id)->first();
						if($server2){
							$row->ServerID = $server2->server_name;
						}else{
							$row->ServerID = 'server_internal_id:'. $row->ServerID;
						}
					} 
					if(in_array($type, array('shenShu','baseRank','baseRank_4','baseRank_5','baseRank_6'))){
						array_multisort($temp_res, SORT_ASC, $temp);
					}else{
						array_multisort($temp_res, SORT_DESC, $temp);
						//分组
						foreach ($temp as $k => $v) {
							$temp2[$v->GroupID][] = $v;
						}
						ksort($temp2);
						$test = array();
						foreach ($temp2 as $k2 => $v2) {
							$test = array_merge($test,$v2);
						}
						$temp = $test;
					}  
					$result[] = array(
						'title' => '主服:'.$server->server_name . '(打日志时间：' .date('Y-m-d H:i:s',$value->created_time)  . ')日期:' . ('00:00:00' == date('H:i:s',$value->created_time) ? date('Y-m-d',$value->created_time-1) : date('Y-m-d',$value->created_time)) . $temp_title,
						'res' =>  array_slice($temp,0,120)//其实是取所有玩家
					); 
					unset($temp_res);unset($temp);unset($temp2);
				}
				
			}else{
				return Response::json(array('error'=>'没有结果'), 403);
			}
		}
		if(count($result) > 0){
			return Response::json($result);
		}else{
			return Response::json(array('error'=>'没有结果'), 403);
		}
	}

	public function ServerCreatePlayersIndex(){	//创建玩家信息
		$filename = Input::get('filename');
		if($filename && file_exists(storage_path() . "/cache/" . $filename . ".csv")){
	        $file = storage_path() . "/cache/" . $filename . ".csv";
	        $data = array(
                'content' => View::make('download', 
                    array(
                            'file' => $file
                    ))
	        );
	        return View::make('main', $data);
		}
		$servers = $this->getUnionServers();
		$data = array(
			'content' => View::make('slaveapi.player.create_player_info',array(
					'servers' => $servers
				))
		);
		return View::make('main',$data);
	}

	public function ServerCreatePlayersData(){
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$platform_id = Session::get('platform_id');

		$page = (int)Input::get('page');
		$page = $page > 0 ? $page : 1;
		$download = (int)Input::get('download');
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));
		$server_id = (int)Input::get('server_id');
		if(!$server_id){
			return Response::json(array('error'=>'Please Select A Server.'), 403);
		}
		$server = Server::find($server_id);
		if(!$server){
			return Response::json(array('error'=>'No Such Server.'), 403);
		}
		$server_internal_id = $server->server_internal_id;

		$data2slave = array(
			'game_id' => $game_id,
			'platform_id' => $platform_id,
			'server_internal_id' => $server_internal_id,
			'start_time' => $start_time,
			'end_time' => $end_time,
			'page' => $page,
			'download' => $download,
			);

		$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		$result = $slave_api->getCreatePlayers($data2slave);
		if(200 != $result->http_code){
			return $slave_api->sendResponse();
		}
		$result = $result->body;
		$players = array();
		$total = 0;
		foreach ($result as $key => &$value) {
			if('count' == $key){
				$total = $value;
			}else{
				$value->created_time = date("Y-m-d H:i:s", $value->created_time);
				$players[] = $value;
			}
		}
		unset($result);
		if($download){
			return $this->downloadCreatePlayers($players);
		}else{
			$result = array(
				'current_page' => $page,
				'count' => $total,
				'players' => $players,
				);
			return Response::json($result);
		}
	}

	private function downloadCreatePlayers($players){
        if (empty($players)){ //下载数据若不存在
            return Response::json(array('error'=>'没有数据需要下载!'), 403);
        }

        $file_name = time().'CreatePlayers';
        $file = storage_path() . "/cache/" . $file_name . ".csv";
        $title = array();
        $ignore_keys = array('operator_id','server_id','player_name','table_id', 'log_id');
        foreach ($players as $key => $value) {
        	foreach ($value as $k => $v) {
        		if(!in_array($k, $ignore_keys)){
        			$title[] = $k;
        		}
        	}
        	break;
        }

        $csv = CSV::init($file, $title);
        foreach ($players as &$value) {
        	foreach ($ignore_keys as $ignore_key) {	//删除一些不需要的键
        		if(isset($value->$ignore_key)){
        			unset($value->$ignore_key);
        		}
        	}
        	$value->pay_dollar = round($value->pay_dollar, 2);
            $res = $csv->writeData($value);
            unset($value);
        }
        $res = $csv->closeFile();
        $data = array(
           	'filename' => $file_name
        );
        return Response::json($data);
	}

}