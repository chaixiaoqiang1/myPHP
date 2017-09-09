<?php

class PokerPaymentController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function pokerOrderStatIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.payment.order.pokerstat', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function pokerOrderStatData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $platform_id = Session::get('platform_id');
        $server_internal_id = 1;
        $query = Platform::where("platform_id", $platform_id)->first();
        if ($query){
            $currency_id = $query->default_currency_id;
        } else{
            App::abort(404);
        }

        $response = $api->getPokerOrderStat($platform_id, $game->game_id, $currency_id, $start_time, $end_time);
        if ('200' == $response->http_code) {
            $order = $response->body;
        }else{
            return $api->sendResponse();
        }
        unset($order[0]);
        $count = count($order);
        foreach ($order as $val) {
            $start_time = strtotime($val->date);
            $end_time = strtotime($val->date)+86399;
            $user = $api->getLogDay($platform_id, $game->game_id, $server_internal_id, $start_time, $end_time);
            if('200' != $user->http_code){
                continue;
            }
            $val->count_num = isset($user->body[0]->count_formal)?$user->body[0]->count_formal : 0 ;
            $val->pay_rate = ($val->count_num>0) ? (round($val->total_user_count/$val->count_num, 4)) : 0 ;
        }
        if (isset($order))
        {
            return Response::json($order);
        } else {
            return Response::json( $response->http_code);
        }
    }
  

    //用户分析

    public function PokerUserAnaysisIndex()
    {
        $data = array(
            'content' => View::make('slaveapi.user.poker.log')
        );
        return View::make('main', $data);
    } 

    public function PokerUserAnaysisData()
    {
        $msg = array(
            'error' => Config::get('errorcode.unknow'),
            'code' => Lang::get('error.basic_input_error'),
        );
        $rules = array(
            'end_time' => 'required'
        ); 
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server = Server::find(13);
        $server_internal_id = $server->server_internal_id;
        $game_api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
        $api =  SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $log_day = $api->getLogDay($platform_id, $game_id, $server_internal_id , $start_time, $end_time);
        if (isset($log_day->body)) {
            $log = $log_day->body;
            foreach ($log as &$value) {
                if(!isset($value->date)){
                    continue;
                }
                $value->avg1 = '';
                $value->max1 = '';
                $value->avg2 = '';
                $value->max2 = '';
                $date = $value->date;
                $start= strtotime($date)-6*86400;
                $end = strtotime($date)+86399;
                $start1 = strtotime($date);
                $end1 = $start1 + 86399;
                // $pay_dd = $api->getLogDays($platform_id, $game_id, $server_internal_id , $start1, $end1);
                // if (isset($pay_dd->body)) {
                //     $body = $pay_dd->body;
                //     $len = count($body);
                //     $sql = "";
                //     for ($i=0; $i < $len; $i++) { 
                //         if ($i == ($len-1)) {
                //             $sql .= $body[$i]->user_id;
                //         }elseif ($i < ($len-1)) {
                //             $sql .= $body[$i]->user_id .',';
                //         }
                //         $arr[] = $body[$i]->user_id; 
                //     }
                // }
                $pay_num = $api->getPokerPayNum($platform_id, $game_id, $server_internal_id,$start1, $end1);
                if (isset($pay_num->body)) {
                    $pay_num = $pay_num->body;
                    $value->pay_num = $pay_num[0]->paynum;
                }
                $week_log = $api->getPokerWeek($platform_id, $game_id, $server_internal_id,$start, $end);
                if (isset($week_log->body)) {
                    $week = $week_log->body;
                    $value->week_log = $week[0]->num;
                }
                $start_time = strtotime($date);
                $end_time = $start_time+86399;
                $new_users = $api->getPokerRegNew($platform_id, $game_id, $start_time, $end_time);
                if (isset($new_users)) {
                    $new = $new_users->body;
                    $value->new = $new[0]->total_num;
                }
                $value->old = $value->num - $value->new; //老用户活跃
                $value->rate = ($value->num > 0) ? round($value->new/$value->num, 3) : 0 ;
                $result = $game_api->getOnlineNum($date);
                $sum1 = $sum2 = 0;
                $num_array = $play_array = array();
                if (isset($result->player_nums)) {
                    $player_nums = $result->player_nums;

                    $len = count($player_nums);
                    foreach ($player_nums as $key => $val) {
                        array_push($num_array, $val->number);
                        array_push($play_array, $val->playing);
                    }
                    $len = count($num_array);
                    for ($i=0; $i < $len; $i++) { 
                        $num_max = $num_array[0];
                        $play_max = $play_array[0];
                        if ($num_array[$i] > $num_max) {
                            $num_max = $num_array[$i];
                        }
                        $sum1 += $num_array[$i];
                        if ($play_array[$i] > $play_max) {
                            $play_max = $play_array[0];
                            if ($play_array[$i] > $play_max) {
                                $play_max = $play_array[$i]; 
                            }
                        }
                        $sum2 += $play_array[$i]; 
                    }
                    if(0==$len){
                       $value->avg1 = 0;
                       $value->avg2 = 0; 
                    }
                    else{
                        $value->avg1 = round($sum1/$len);
                        $value->avg2 = round($sum2/$len);
                    }
                    $value->max1 = $num_max;
                    $value->max2 = $play_max;
                }
                unset($value);
            }
            $f_num = array();
            $f_pay_num = array();
            $f_week_log = array();
            $f_new = array();
            $f_old = array();
            $f_rate = array();
            $f_max1 = array();
            $f_avg1 = array();
            $f_max2 = array();
            $f_avg2 = array();
            foreach (array_reverse($log) as $value1) {
                $f_date[] = $value1->date;
                $f_num[] = $value1->num;
                $f_pay_num[] = $value1->pay_num;
                $f_week_log[] = $value1->week_log;
                $f_new[] = $value1->new;
                $f_old[] = $value1->old;
                $f_rate[] = $value1->rate;
                $f_max1[] = $value1->max1;
                $f_avg1[] = $value1->avg1;
                $f_max2[] = $value1->max2;
                $f_avg2[] = $value1->avg2;
                unset($value1);
            }
            $log = array(
                'log' => $log,
                'date' => $f_date,
                'f_num' => $f_num,
                'f_pay_num' => $f_pay_num,
                'f_week_log' => $f_week_log,
                'f_new' => $f_new,
                'f_old' => $f_old,
                'f_rate' => $f_rate,
                'f_max1' => $f_max1,
                'f_avg1' => $f_avg1,
                'f_max2' => $f_max2,
                'f_avg2' => $f_avg2,
                );
            if (isset($log)) {
                return Response::json($log);
            } else {
                return Response::json($msg, 403);
            }
        }
    }
    //万家消费排行

    public function pokerUserRankIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.payment.pokerrank')
        );
        return View::make('main', $data);
    }

    public function pokerUserRankData()
    {
        $msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'msg' => Lang::get('errorcode.server_not_found')
        );
        $type = (int) Input::get('type');
        if ($type == 0) {
            $type = "chouma";
        } else if ($type == 1) {
            $type = "jinbi";
        }
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->getPokerPlayerEconomyRank($game->game_id, $type);
        $body = $response->body;
        if ($response->http_code == 200) {
            return Response::json($body);
        } else {
            return Response::json($body, $response->http_code);
        }
    }

    //德州玩家消费数据
    public function pokerUserPayInfoIndex()
    {
    	$data = array(
    		'content' => View::make('serverapi.poker.payment.payinfo'),
    	);
    	return View::make('main', $data);
    }
    public function pokerUserPayInfoData()
    {
    	$msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'msg' => Lang::get('errorcode.basic_input_error')
        );
        $type = (int)Input::get('type');
        $type2 = Input::get('type2');
        $player_name = Input::get('player_name');
        $player_id = Input::get('player_id');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $rules = array(
        	'type1' => 'required',
        	'start_time' => 'required',
        	'end_time' => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
        	return Response::json($msg, 403);
        }
        if (!isset($player_id) && !isset($player_name)) {
        	return Response::json($msg, 403);
        }
        $game_id = Session::get('game_id');
        $page = (int)Input::get('page');
        $page = $page > 0 ? $page : 1;
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        //获取player_id
        if($player_name && !$player_id){
        	$response = $api->getPokerCreatePlayerInfo($uid = '', $player_id = '', $player_name, $game->game_id);
			if($response->http_code == 200 && isset($response->body) && isset($response->body->player_id)){
				$player_id = $response->body->player_id;
			}else{
				$msg['error'] = Lang::get('error.slave_player_not_found');
				return Response::json($msg, 403);
			}
        }

        $table = $this->initTable();
        $messages = $table->getData();
        $result = array();
        foreach ($messages as $message) {
        	$result[$message->id] = array(
        		'desc' => $message->desc,
        		'name' => $message->name
        	);
        }

        if ($type2 == 0) {
        	$response = $api->getPokerEconomyStatistics($game->game_id,  $player_id, $type, $start_time, $end_time);
            $body = $response->body;
            if ($response->http_code == 200) {
                foreach ($body as $x => $y) {
					$body[$x]->action_time = date('Y-m-d H:i:s', $y->action_time);
                    $action_type = $y->action_type;
                    $body[$x]->action_name= '';
                    if(isset($result[$action_type])){
                        $body[$x]->action_name = $result[$action_type]['name'];
                        $body[$x]->action_type = $result[$action_type]['desc'];
                    }
                }
                $data = array();
                $data['items'] = $body;
                $data['current_page'] = 1;
                $data['per_page'] = count($body);
                $data['count'] = count($body);
                $body = (object) $data;
                return Response::json($body);
            } else {
                return Response::json($body, $response->http_code);
            }
        }
        if ($type2 == 1) {
        	$response = $api->getPokerPlayerEconomy($game->game_id, $player_id, $type, $start_time, $end_time, $page, 30);
            $body = $response->body;
            if ($response->http_code == 200) {
                $items = $body->items;
                foreach ($items as $x => $y) {
                    $action_type = $y->action_type;

                    //修要修改
					if ($type == 'yuanbao') {
						$items[$x]->left_number = $y->yuanbao;
					} else if ($type == 'tongqian') {
						$items[$x]->left_number = $y->tongqian;
					} else if ($type == 'gongxun') {
						$items[$x]->left_number = $y->shengwang;
					} else if ($type == 'tili') {
						$items[$x]->left_number = $y->tili + $y->extra_tili;
					} else if ($type == 'jingjiedian') {
						$items[$x]->left_number = $y->jingjiedian;
					} else if ($type == 'yueli') {
						$items[$x]->left_number = $y->yueli;
					} else if ($type == 'xianling') {
						$items[$x]->left_number = $y->xianling;
					}
					//需要修改
					$items[$x]->action_time = date('Y-m-d H:i:s', $y->action_time);
                    $items[$x]->action_name= '';
                    $items[$x]->action_type = $action_type;
                    if(isset($result[$action_type])){
                        $items[$x]->action_name = $result[$action_type]['name'];
                        if($result[$action_type]['desc']){
                            $items[$x]->action_type = $result[$action_type]['desc'];
                        }
                    }
                }
                return Response::json($body);
            } else {
                return Response::json($body, $response->http_code);
            }
        }
    } 


    private function initTable()
	{
       	$game = Game::find(Session::get('game_id'));
       	$table = Table::init(public_path() . '/table/' . $game->game_code . '/game_message.txt');
		return $table;
	}

	//全服消费统计
	public function pokerPayAllServerIndex()
	{
		$data = array(
			'content' => View::make('serverapi.poker.payment.allserver')
		);
		return View::make('main', $data);
	}

	public function pokerPayAllServerData()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknown'),
			'error' => Lang::get('error.basic_input_error')
		);
		$rules = array(
			'start_time' => 'required',
			'end_time' => 'required',
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		}
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$player_level = (int)Input::get('player_level');
		$type_id = (int)Input::get('type');
		switch ($type_id) {
			case 0:
				$type = "chouma";
				break;
			
			case 1:
				$type = "jinbi";
				break;
		}
		$is_filter_neiwan = Input::get('filter_type1') == "true";
		$vip_selector = array();
        $is_select_vip = 0;
        for($i = 0; $i <=12; $i ++){
        	if(Input::get('only_vip'.$i) == 'true'){
        	    $vip_selector[] = 1;
        	    $is_select_vip = 1;
        	} else {
        	    $vip_selector[] = 0;
        	}
        }
        $vip_selector = implode($vip_selector, ',');
        if($is_select_vip == 0){//没选vip，则当成全选
            $vip_selector = '1,1,1,1,1,1,1,1,1,1,1,1,1';
        }
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->getPokerServerEconomy($game->game_id, $platform_id, $type_id, $start_time, $end_time, $player_level, $is_filter_neiwan, $vip_selector);
        $body = $response->body;
        if ($response->http_code == 200) {
			$table = $this->initTable();
            $messages = $table->getData();
            foreach ($body as $x => $y) {
				$body[$x]->action_time = date('Y-m-d H:i:s', $y->action_time);
                $action_type = $y->action_type;
                foreach ($messages as $k => $v) {
                    if ($action_type == $v->id) {
						if ($v->desc) {
                        	$body[$x]->action_type = $v->desc;
						}
						$body[$x]->action_name = $v->name;
                        break;
                    }
                }
            }
            return Response::json($body);
        } else {
            return Response::json($body, $response->http_code);
        }

        
	} 
	public function pokerCashIndex()
    {
        $table = $this->initPoker();
        $messages = $table->getData();
    	$data = array(
    		'content' => View::make('serverapi.poker.payment.cash', array('award' => $messages))
    	);
    	return View::make('main', $data);
    }

    public function pokerCashSend()
    {
    	$msg = array(
    		'code' => Config::get('errorcode.unknown'),
    		'error' => Lang::get('error.basic_input_error')
    	);
        $table = $this->initPoker();
        $messages = $table->getData();
        $messages = (array)$messages;
    	$uid = Input::get('player_id');
    	$player_name = Input::get('player_name');
    	$type1 = Input::get('type1');
    	$start_time = strtotime(trim(Input::get('start_time')));
    	$end_time = strtotime(trim(Input::get('end_time')));
    	$type2 = Input::get('type2');
    	$game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$info = $api->getPokerCashInfo($uid, $platform_id, $player_name, $type1, $start_time, $end_time, $type2);
    	if (isset($info->http_code) && $info->http_code == 200) {
    	    $cash = $info->body;
            foreach ($cash as  $value) {
                $value->award_name = '';
                for ($i=0; $i < count($messages); $i++) { 
                    if ($value->goods_id == $messages[$i]->Id) {
                        $value->award_name = $messages[$i]->Name;
                    }
                }
                $value->get_time = date("Y-m-d H:i:s", $value->get_time);
                $value->create_time = date("Y-m-d H:i:s", $value->create_time);
                $value->status = ($value->status == 1)?'已发送':'未发送';
            }
    	    return Response::json($cash);		
    	}else{
    		return Response::json('error', 'error');
    	}        
    }

    private function initPoker()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'poker' . '/reward_center.txt');
        return $table;
    }

    public function pokerCashUpdate()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
        );
        $id = Input::get('id');
        if (!$id) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403); 
        }
        $data = array(
            'id' => $id,
            'status' => 1
        );
        
        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);
        $url = 'http://id.joyspade.com';
        $api =  PlatformApi::connect($url, $platform->api_key, $platform->api_secret_key);
        $response = $api->updatePokerCash($data);
        if (isset($response->body)) {
            $data = $response->body;
        }
        return Response::json($data);
    }

    //充值金额查询

    public function rechargeCountIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.payment.recharge')
        );
        return View::make('main', $data);
    }

    public function rechargeCountData()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'count' => 'required'
        );
        $count = trim(Input::get('count'));
        $page = (int)Input::get('page');
        $page = $page > 0 ? $page : 1;
        $per_page = (int)Input::get('per_page');
        $per_page = $per_page > 0 ? $per_page : 50;
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        //var_dump(Input::all());die();
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->getRechargeUID($platform_id, $game_id, $count, $page, $per_page);
        if ($response->body && $response->http_code == 200) {
            foreach ($response->body as $key => $value) {
                $data[] = array(
                    'total_pay' => round($value->total_pay, 3),
                    'pay_user_id' => $value->pay_user_id,
                    'player_name' => isset($value->player_name) ? $value->player_name : '',
                    'player_id' => isset($value->player_id) ? $value->player_id : '',
                    'count' => $value->count,
                    'first_pay' => date("Y-m-d H:i:s", $value->first_pay),
                    'last_pay' => date("Y-m-d H:i:s", $value->last_pay),
                );
            }
             return Response::json($data);
        }else{
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 403);
        }

    }

    public function playerPayIndex()
    {
        $data = array(
            'content'=>View::make('serverapi.poker.payment.player-pay')
        );
        return View::make('main', $data);
    }

    public function playerPayData()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'start_time' => 'required',
            'end_time' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
    
        $server = Server::find(13);
        if (!$server) {
            $msg['error'] = Lang::get('error.server_not_found');
            return Response::json($msg);
        }
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->playerPayData($platform_id, $game_id, $server->server_internal_id,$start_time, $end_time);
        //var_dump($response);die();
        if ($response->body && $response->http_code == 200) {
            $body = $response->body;
            $user1 = $body->user1;
            $user2 = $body->user2;
            $login = $body->login;
            $player = $body->player;
            //var_dump($player);die();
            $data = array();
            for ($i=0; $i < count($user1); $i++) { 
                $num1 = $num2 = $num3 = $num4 = $num5 = $num6 = $num7 = 0;
                foreach ($user2[$i] as $key => $value) {
                    $a1 = explode('-', $value->pay_time);
                    $aa = $a1[2];//获得付费日期
                    $b1 = explode(" ", $value->created_time);
                    $bb = substr($b1[0], 8,2); 
                    $days = $aa - $bb;
                    if ($days == 0) {
                        $num1 ++;
                    }
                    elseif ($days == 1) {
                        $num2 ++;
                    }elseif ($days == 2) {
                        $num2 ++;
                    }elseif ($days == 3) {
                        $num3 ++;
                    }elseif ($days == 4) {
                        $num4 ++;
                    }elseif ($days == 5) {
                        $num5++;
                    }elseif ($days == 6) {
                        $num6++;
                    }elseif ($days == 7) {
                        $num7++;
                    }
                }
                $sql = "";
                $start = strtotime($user1[$i]->date);
                $ss = $start - 7 * 86400;
                /*$login[$i] = $api->getLoginPlayerId($platform_id, $game_id, $server->server_internal_id, $ss);
                if (isset($login[$i]->body) && $login[$i]->http_code == 200) {
                    $log = $login[$i]->body;
                    var_dump($log);die();
                    $len = count($log);
                    for ($j=0; $j < $len; $j++) { 
                        if ($j < $len-1) {
                            $sql .= $log[$j]->player_id .","; 
                        }elseif ($j == $len-1) {
                            $sql .= $log[$j]->player_id;
                        }  
                    }
                }
               // var_dump($sql);die(); 
                $user = array();
                foreach ($player as $key => $value) {
                    $user[] = $value->pay_user_id;
                }
                $players = $api->getPlayerUid($platform_id, $game_id, $sql);
                $lost_num = 0;
                if ($players->http_code == 200 && isset($players->body)) {
                    $play = $players->body;
                    foreach ($play as $key => $val) {
                       if (in_array($val->uid, $user)) {
                           $lost_num ++;
                       }
                    }
                }*/
                //$lost = $api->getLoginPlayerId($platform_id, $game_id, $server->server_internal_id, $ss);
                //var_dump($login[$i]);die();
                $lost_num = $login[$i]->lost_num;
                $data[$i] = array(
                    'date' => $user1[$i]->date,
                    'total_dollar' => round($user1[$i]->total_dollar, 2),
                    'avg_dollar' =>  isset($user1[$i]->pay_num) ? round($user1[$i]->total_dollar / $user1[$i]->pay_num, 2) : '0', 
                    'avg_paunums' => isset($user1[$i]->pay_num) ? round($user1[$i]->order_num /$user1[$i]->pay_num, 2) : 0 ,
                    'pay_day' => $user1[$i]->pay_num,
                    'num1' => $num1,
                    'num2' => $num2,
                    'num3' => $num3,
                    'num4' => $num4,
                    'num5' => $num5,
                    'num6' => $num6,
                    'num7' => $num7,
                    'lost_num' => $lost_num
                );
                unset($num1);
                unset($num2);
                unset($num3);
                unset($num4);
                unset($num5); 
                unset($num6);
                unset($num7);
            }
            return Response::json($data);
        }else{
            return Response::json(array('error'=>'查询异常'), 403);
        }
    }
}