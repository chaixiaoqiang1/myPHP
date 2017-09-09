<?php
//error_reporting( E_ALL&~E_NOTICE );//除去 E_NOTICE 之外的所有错误信息
class PokerGMController extends \BaseController {
/*
		获取GM问题
	*/

	const SERVER_IP =  "119.81.84.118";
	
	public function gmIndex()
	{
		
		$data = array(
			'content' => View::make('serverapi.poker.question'),
	    );
		return View::make('main', $data);
	} 

	public function getGM()
	{
		$server = Server::find(13);
		if (!$server) {
			return Response::json($msg, 403);
		}
		$api = PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
		$response = $api->getSupportQuestion();
		$len = count($response);
		//var_dump($len);die();
		if (!isset($response->error_code)) {  //成功
			foreach ($response as $key => $value) {
	            
				//存入数据库
				$server_id = $server->server_id;
		    	$server_gm  = GM::findServerGMID($value->id, $server_id)->first();
		    	if (!$server_gm) {
		    		$gm = array(
		    			'server_gm_id' => $value->id,
		    			'message'      => $value->msg,
		    			'player_id'    => $value->player_id,
		    			'send_time'    => $value->created_at,
		    			'gm_type'      => $value->type,
		    			'server_id'    => $server_id,
		    		);
		    		
		    	}
		  
				if ($value->player_id) {
					$game = Game::find(Session::get('game_id'));
        			$game_id = Session::get('game_id');  //8
       				$api_gm = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        			$platform = Platform::find(Session::get('platform_id'));
        			$response_gm = $api_gm->getUserByPlayerID($platform->platform_id, $value->player_id, $server_internal_id = 1, $game_id, $tp_code='fb');
					$value->player_name = isset($response_gm->body[0]->player_name) ? $response_gm->body[0]->player_name: '';

				}
				if (isset($value->player_name)) {
					$gm['player_name'] = $value->player_name;
				}
				if (!$server_gm) {
		    		GM::insert($gm);
		    	}
				if ($value->type) {
					switch ($value->type) {
					case '1':
						$value->type = Lang::get('serverapi.gm_type_bug');
						break;
					
					case '2':
						$value->type = Lang::get('serverapi.gm_type_complaint');
						break;
					case '3':
						$value->type = Lang::get('serverapi.gm_type_advice');
						break;
					case '4':
						$value->type = Lang::get('serverapi.gm_type_other');
						break;
					default:
						$value->type = Lang::get('serverapi.gm_type_unknow');
					}
				}
				if (isset($value->created_at)) {
					$value->created_at = date("Y-m-d H:i:s" , $value->created_at);		
		    	}
	    	}  
    	}
    	//将信息插入数据库
    	$data = array(
    		'num' => $len,
    		'result'=> $response
    	);
    	return Response::json($data);
	}

	
	public function replyGM()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => Lang::get('error.basic_input_error'),
		);
		$action = Input::get('action');
		if ($action == 1) {
			$rules = array(
				'server_gm_id' => 'required',
			);
		} else if ($action == 2) {
			$rules = array(
				'server_gm_id' => 'required',
				'reply_message' => 'required',
			);
		}
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}

		$server_gm_id = (int)Input::get('server_gm_id');
		$reply_message = (string)Input::get('reply_message');

		$server = Server::find(13); //获得服务器信息
		if (!$server) {
			return Response::json($msg, 403);
		}
		$gm = GM::findServerGMID($server_gm_id, $server->server_id)->first();
        if (! $gm) {
            App::abort(500, 'Can not find gm in database' . json_encode(Input::all()));
        }
		$api = PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
		$response = $api->replySupportQuestion($server_gm_id, $reply_message);
		 if (isset($response->result) && $response->result == 'OK') {
            $gm->reply_message = $reply_message;
            $gm->replied_time = time();
            $gm->user_id = Auth::user()->user_id;
            $gm->is_done = 1;
            $gm->save();
        } else {
            App::abort(500, 'Reply GM Message Server Error' . json_encode($gm));
        }
         return $api->sendResponse();
	}


	//获取GM问题列表

	public function repliedIndex()
    {
      
        $data = array(
            'content' => View::make('serverapi.poker.reply')
        );
        return View::make('main', $data);
    }

    public function repliedGM()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        
        $server_id = 13;
        $type = (int) Input::get('type');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $player_name = Input::get('player_name');
        $page = (int)Input::get('page');
        $page = $page > 0 ? $page : 1;
        $gm_types = array(
            1 => Lang::get('serverapi.gm_type_bug'),
            2 => Lang::get('serverapi.gm_type_complaint'),
            3 => Lang::get('serverapi.gm_type_advice'),
            4 => Lang::get('serverapi.gm_type_other')
        );
        $server = Server::find($server_id);
        if (! $server) {
            return Response::json($msg, 404);
        }
		
		$count = GM::repliedGM($player_name, $start_time, $end_time, $server_id, $type)->count();
        $per_page = 30;
		$gm_list = GM::repliedGM($player_name, $start_time, $end_time, $server_id, $type)->forPage($page, $per_page)->get();
		foreach ($gm_list as $k => $v) {
			$v->gm_type_name = $gm_types[$v->gm_type];
			$v->send_time = date('Y-m-d H:i:s', $v->send_time);
			$v->replied_time = date('Y-m-d H:i:s', $v->replied_time);
		}

        $reply_done_one_page = array(
            'items' => $gm_list->toArray(),
            'current_page' => $page,
            'per_page' => $per_page,
            'count' => $count
        );
        return Response::json($reply_done_one_page);
    }

    /*
	每日游戏人数统计  created_by  xianshui  2014.11.13
    */
    public function dayGamesIndex()
    {
    	$data = array(
    		'content' => View::make('serverapi.poker.users.games')
    	);
    	return View::make('main', $data);
    }

    public function dayGamesData()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error' => ''
    	);

    	$start_time = strtotime(trim(Input::get('start_time')));
    	$end_time = strtotime(trim(Input::get('end_time')));
    	$platform_id = Session::get('platform_id');
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	$server = Server::find(13);
    	if (!$server) {
    		$msg['error'] = Lang::get('error.basic_not_found');
    		return Response::json($msg, 403);
    	}
    	$server_internal_id = $server->server_internal_id;
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$data = array();
    	$days = ceil(($end_time - $start_time )/86400);
    	$start = $start_time;
    	for ($i=0; $i < $days; $i++) { 
    		$end = $start + 86399;
            $response = $api->getGamesData($platform_id, $game_id, $server_internal_id, $start, $end);
    		//$response = $api->getGamesData($platform_id, $game_id, $server_internal_id, $start, $end);
            if ($response->http_code == 200 && isset($response->body)) {
    			$body = $response->body;
    			$log1 = $body->log1;
    			$log2 = $body->log2;
    			$log3 = $body->log3;
    			$data[] = array(
                    'date' => date("Y-m-d", $start+86400/2),
                    'num1' => isset($log1[0]->num1) ? $log1[0]->num1 : 0,
                    'num2' => isset($log2[0]->num) ? $log2[0]->num : 0,
                    'login_out_times' => isset($log2[0]->times) ? $log2[0]->times : 0,
                    'num3' => isset($log1[0]->num2) ? $log1[0]->num2 : 0, 
                    'num5' => isset($log1[0]->num3) ? $log1[0]->num3 : 0,
                    'num6' => isset($log3[0]->num) ? $log3[0]->num: 0,
                    'endOneRound_times' => isset($log3[0]->times) ? $log3[0]->times : 0,
    			);
    		}
    		$start += 86400;
    	}
    	foreach ($data as $key => $value) {
    		if ($value['date'] == '1970-01-01' && $value['num1'] == 0 ) {
    			unset($data[$key]);
    		}
    	}
        if(isset($data))
        {
            $arr = array_reverse($data);
            //var_dump($arr);die();
    		return Response::json($arr);
    	}else{
    		$msg['error'] = Lang::get('error.basic_not_found');
    		return Response::json($msg, 403);
    	}	
    }

    /*
	扣除筹码  xianshui 2014.11.15
    */

    public function deleteChipsIndex()
    {
        $type = array('illegal transactions','hacking activity','system bug','chargeback');
        $data = array(
            'content' => View::make('serverapi.poker.users.delete-chips',array('type' => $type))
        );
        return View::make("main", $data);
    }

    public function deleteChipsOperate()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknow'),
    		'error' => ''
    	);
    	$rules = array(
    		'chips' => 'required'
    	); 
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		$msg = Lang::get('error.basic_input_error');
    		return Response::json($msg, 403);
    	}
    	$player_id = Input::get('player_id');
    	$player_name = Input::get('player_name');
    	$chips = Input::get('chips');
        $type_source = array('illegal transactions','hacking activity','system bug','chargeback');
        $type = Input::get('type');
    	$platform_id = Session::get('platform_id');
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	$server = Server::find(13);
    	if (!$server) {
    		$msg['error'] = Lang::get('error.basic_not_found');
    		return Response::json($msg, 403);
    	}
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	if (isset($player_name) && $player_id == '') {
    		$player = $api->getIdByName2($platform_id, $game_id, $server_internal_id,$player_name, '');
			if ($player->http_code == 200 && isset($player->body)) {
				$body = $player->body[0];
				$player_id = $body->player_id;
			}else{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 403);
			}
    	}
    	if ($player_id) {
    		$game_api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
    		$response = $game_api->deleteChips($player_id, $chips, $type);
            if (isset($response->amount)) {
                $result = array(
                    'status' => 'ok',
                    'msg' => (isset($type_source[$type-1]) ? $type_source[$type-1] : 'unselected').'--'.(isset($player_name) ? $player_name : '') . '(' . $player_id . ')---' . '应扣除筹码:' . $response->amount . '---实际扣除筹码:' . $response->deduct_amount . ($response->is_ban == 1 ? '--已封号' : '--未封号'),
                );
                $datatostore = array(   //将操作插入数据库中
                    'operate_time' => time(),
                    'game_id' => $game_id,
                    'player_name' => $player_name ? $player_name : '',
                    'player_id' => $player_id ? $player_id : '',
                    'operator' => Auth::user ()->user_id,
                    'server_name' => $server->server_name,
                    'operation_type' => 'poker-chips',
                    'extra_msg' => $result['msg'],
                    );
                Operation::insert($datatostore);
                unset($datatostore);
                return Response::json($result);
            }else{
                $msg['error'] = 'Fail';
                return Response::json($msg, 403);
            }
        }else{
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 403);
        }

    }

    /*
    封号 xianshui 2014.11.15    oxfcf
    */
    public function freezePlayerIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.users.freeze')
        );
        return View::make('main', $data);
    }

    public function freezePlayerOperate()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'is_freeze' => 'required'
        );
        //var_dump(Input::all());die();
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = lang::get("basic_input_error");
            return Response::json($msg, 403);
        }
        $player_name = Input::get('player_name');
        $player_id = Input::get('player_id');
        $is_freeze = Input::get('is_freeze');
        $why_freeze = Input::get('why_freeze');
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server = Server::find(13);
        if (!$server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg ,403);
        }
        $server_internal_id = $server->server_internal_id;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if ($player_name && $player_id == '') {
            $player = $api->getIdByName2($platform_id, $game_id, $server_internal_id,$player_name, '');
            if ($player->http_code == 200 && isset($player->body)) {
                $body = $player->body[0];
                $player_id = $body->player_id;
            }else{
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 403);
            }
        }
        if ($player_id) {
            $game_api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
            $response = $game_api->freezePlayers($player_id, $is_freeze);
            if (isset($response->is_ok)) {
                $result = array(
                    'status' => 'ok',
                    'msg' => $why_freeze.'--'.(isset($player_name) ? $player_name : '') . '(' . $player_id . ')---'.($is_freeze ? '封号' : '解封').'OK--' . $response->is_ok,
                );
                $datatostore = array(   //将操作插入数据库中
                    'operate_time' => time(),
                    'game_id' => $game_id,
                    'player_name' => $player_name ? $player_name : '',
                    'player_id' => $player_id ? $player_id : '',
                    'operator' => Auth::user()->user_id,
                    'server_name' => $server->server_name,
                    'operation_type' => 'poker-freeze',
                    'extra_msg' => $result['msg'],
                    );
                Operation::insert($datatostore);
                unset($datatostore);
                return Response::json($result);
            }else{
                $msg['error'] = 'Fail';
                return Response::json($msg, 403);
            }
            
        }else{
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 403);
        }
    }

}