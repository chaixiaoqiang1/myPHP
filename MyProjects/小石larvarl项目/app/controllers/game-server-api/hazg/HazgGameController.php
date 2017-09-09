<?php

class HazgGameController extends \BaseController {

	public function userIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('serverapi.hazg.user.index', array(
                        'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }

    public function userData()
    {
        $msg = array(
        	'code' => Config::get('errorcode.unknow'),
        	'error' => ''
        );
        $rules = array(
        	'email_or_uid' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
        	$msg['error'] = Lang::get('error.basic_input_error');
        	return Response::json($msg, 403);
        }
        $email_or_uid = trim(Input::get('email_or_uid'));
        $choice = intval(Input::get('choice'));
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $platform = Platform::find($platform_id);
        $game = Game::find($game_id);
        if (!$platform || !$game) {
        	$msg['error'] = Lang::get('error.basic_not_found');
        	return Response::json($msg, 403);
        }
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if ($choice == 0) { //根据官网账号查询
        	$resposne = $api->getUserByEmailTH($platform_id, $email_or_uid, $server_internal_id = 0, $game_id );
        } elseif ($choice == 1) { //根据UID查
        	$response = $api->getUserByUIDTH($platform_id, $email_or_uid, $server_internal_id = 0 , $game_id);
        } else {
        	$mag['error'] = Lang::get('error.basic_input_error');
        	return Response::json($msg, 403);
        }
        //var_dump($response);die();
        if ($response->http_code != 200) {
        	$msg['error'] = Lang::get('player_not_found');
        	return Response::json($msg, 403);
        }
        $user_info = $response->body;
        $user_basic = array(
        	'uid' => $user_info->uid,
        	'name' => isset($user_info->name) ? $user_info->name : '',
        	'nickname' => isset($user_info->nickname) ? $user_info->nickname : '',
        	'login_email' => isset($user_info->login_email) ? $user_info->name : '',
        	'contact_email' => isset($user_info->contact_email) ? $user_info->contact_email :'' , 
        	'created_ip' => isset($user_info->created_ip) ? $user_info->created_ip : '',
        	'last_visit_ip' => isset($user_info->last_visit_ip) ? $user_info->last_visit_ip :'', 
            'created_time' => isset($user_info->created_time) ? $user_info->created_time : '',
            'last_visit_time' => isset($user_info->last_visit_time) ? $user_info->last_visit_time :'', 
        	'nums_created_player' => isset($user_info->nums_created_player) ?$user_info->nums_created_player : '' ,
        	'u' => isset($user_info->u) ? $user_info->u : '',
        	'u2' => isset($user_info->u2) ? $user_info->u2 : '',
        	'source' => isset($user_info->source) ? $user_info->source :'' ,
        	'is_anonymous' => isset($user_info->is_anonymous) ? $user_info->is_anonymous : '' 
        );

        if ($user_info->players) {
        	$created_players =  $user_info->players;
	        foreach ($created_players as $key => $item) {
	        	$server_name = "";
	        	$server = Server::currentGameServers()->where('server_internal_id', $item->server_id)->first();
	        	//$item->last_login = "";
	        	/*if ($server) {
	                $server_name = $server->server_name;
	                $player_api = GameServerApi::connect($server->api_server_ip, 
	                        $server->api_server_port, $server->api_dir_id);
	                $player_info = $player_api->getPlayerInfoByName(
	                        $item->player_name);
	                if (! isset($player_info->error_code))
	                {
	                    $item->last_login = date('Y-m-d H:i:s', 
	                            $player_info->last_login);
	                }
	            }*/
	            if ($server) {
	             	$item->server_name = isset($server->server_name) ? $server->server_name : '';
	             } 
	             //$item->created_time = date("Y-m-d H:i:s", $player_info->last_login);
	             if (isset($item->all_pay_amount)) {
	             	$item->avg_amount = $item->all_pay_times > 0 ?  round($item->all_pay_amount / $item->all_pay_times, 2) : '';
	             }
	             $user = array(
	             	'user_basic' => $user_basic,
	             	'created_players' => $user_info->players
	             );
	             $user = (object) $user;

	             return Response::json($user);
	        }
        }
    }
    
    public function playerIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('serverapi.hazg.player.index', array(
                        'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }
    public function playerData()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
       	    'id_or_name' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Resposne::json($msg, 403);
        }
        $choice = Input::get('choice');
        $server_id = Input::get('server_id');
        $server = Server::find($server_id);
        $server_internal_id = 0;
        if ($server) {
            $server_internal_id = $server->server_internal_id;
        }else {
       	    $msg['error'] = Lang::get('error.basic_not_found');
       	    return Response::josn($msg, 403);
        }
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');

        $id_or_name = trim(Input::get('id_or_name'));
        $game = Game::find($game_id);
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if ($choice == 0) { //玩家昵称
            $response = $slave_api->getUserByPlayerNameTH($platform_id, $id_or_name, $server_internal_id, $game_id); 
        } elseif ($choice == 1) {//玩家player_id 查询
            $response = $slave_api->getUserByPlayerIDTH($platform_id, $id_or_name, $server_internal_id, $game_id); 
        }
        //var_dump($response);die();
        $player = array();
        foreach ($response->body as  $v) {
            $server_name = "";
            $server = Server::currentGameServers()->where('server_internal_id', $v->server_internal_id)->first();
            if ($server) {
                $server_name = $server->server_name;
            }else{
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 403);    
            }
            $player[] = array(
                'nickname' => isset($v->nickname) ? isset($v->nickname) : '',
                'uid' => isset($v->uid) ? $v->uid : '',
                'server_name' => isset($server_name) ? $server_name : '',
                'login_email' => isset($v->login_email) ? $v->login_email : '',
                'player_id' => isset($v->player_id) ? $v->player_id : '',
                'player_name' => isset($v->player_name) ? $v->player_name : '',
                'first_lev' => isset($v->first_lev) ? $v->first_lev : 0,
                'all_pay_amount' => isset($v->all_pay_amount) ? $v->all_pay_amount : 0,  
                'all_pay_times' => isset($v->all_pay_times) ? $v->all_pay_times : 0,
                'avg_amount' => $v->all_pay_times > 0 ? round($v->all_pay_amount/$v->all_pay_times, 2) : '',
                'u' => isset($v->u) ? $v->u : '',
                'u2' => isset($v->u2) ? $v->u2 : '',
                'source' => isset($v->source) ? $v->source : '',
                'is_anonymous' => isset($v->is_anonymous) ? $v->is_anonymous : '',
            );
        }
        if ($response->http_code != 200) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg , 403);
        }else{
            return Response::json($player, $response->http_code);
        }

    }

   //第三方游戏 -- 一平台多游戏 -- 黑暗之光
    public function THUserStatIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.user.th.userstat', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function THSendUserStatData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $interval = (int) Input::get('interval');
        $filter_id = (int) Input::get('filtrate_id');
        $source = trim(Input::get('source'));
        $u1 = trim(Input::get('u1'));
        $u2 = trim(Input::get('u2'));
        $server_id = Input::get('server_id');
        //$server = Server::find($server_id);
        $interl_time = array(
                600,
                3600,
                86400,
                0
        );
        $filter_list = array(
                'source',
                'u1',
                'u2'
        );
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if (count($server_id) === 1) { //选择单服
            $server = Server::find($server_id[0]);
            $user = array();
            $user['game_id'] = $game->game_id;
            if ($server_id[0] > 0){
                $user['platform_server_id'] = $server->platform_server_id;
            } else
            {
                $user['platform_server_id'] = 0;
            }
            $user['platform_id'] = Session::get('platform_id');
            $user['start_time'] = $start_time;
            $user['end_time'] = $end_time;
            $user['interval'] = $interl_time[$interval];
            $user['filter'] = $filter_list[$filter_id];
            $user['source'] = $source;
            $user['u1'] = $u1;
            $user['u2'] = $u2;
            $response = $api->THGetUserStat($user);
            if ($response->http_code != 200)
            {
                return Response::json($response->body, $response->http_code);
            }
            $body = $response->body;
            //Log::info(var_export($response, true));
            //var_dump($body);die();
            $sum = array(
                    'sum_formal' => 0,
                    'sum_anonymous' => 0,
                    'sum_player_formal' => 0,
                    'sum_player_anonymous' => 0,
                    'sum_lev_formal' => 0,
                    'sum_lev_anonymous' => 0,
                    'sum_count_anonymous_formal' => 0
            );
            foreach ($body as $item)
            {
                if ($server_id[0] == 0)
                {
                    $item->count_player_formal = 0;
                    $item->count_player_anonymous = 0;
                    $item->count_lev_formal = 0;
                    $item->count_lev_anonymous = 0;
                }
                
                    $sum['sum_formal'] += (int) $item->count_formal;
                    $sum['sum_anonymous'] += (int) $item->count_anonymous;
                    $sum['sum_count_anonymous_formal'] += (int) $item->count_anonymous_formal;
                
                
                //$sum['sum_player_formal'] += (int) $item->count_player_formal;
                //$sum['sum_player_anonymous'] += (int) $item->count_player_anonymous;
                //$sum['sum_lev_formal'] += (int) $item->count_lev_formal;
                //$sum['sum_lev_anonymous'] += (int) $item->count_lev_anonymous;
               
                if ($interval == 3)
                {
                    $item->end_time = date('Y-m-d H:i:s', $end_time);
                    $item->ctime = date('Y-m-d H:i:s', $start_time);
                } else
                {
                    $item->end_time = $interl_time[$interval] + (int) $item->ctime;
                    $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                    $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                }
            }
            $statdata = array();
            
            $blank = new stdClass();
            $blank->ctime = null;
            //$blank->end_time = null;
            $blank->source = null;
            $blank->u1 = null;
            $blank->u2 = null;
            $blank->count_formal = null;
            $blank->count_anonymous = null;
            $blank->count_anonymous_formal = null;
            //$blank->count_player_formal = null;
            //$blank->count_player_anonymous = null;
            //$blank->count_lev_formal = null;
            //$blank->count_lev_anonymous = null;
            for ($i = 0; $i < sizeof($body); $i ++)
            {
                if ($i > 0 && $body[$i]->ctime != $body[$i - 1]->ctime)
                {
                    $statdata[] = $blank;
                }
                $statdata[] = $body[$i];
            }
            $data = array(
                    'items' => $statdata,
                    'sum' => $sum
            );
            if ($response->http_code == 200)
            {
                return Response::json($data);
            } else
            {
                return Response::json($body, $response->http_code);
            }
        } else {
            //选择多服
           $len  = count($server_id);
             $server1 = Server::find($server_id[0]);
            $use = array();
            $use['game_id'] = $game->game_id;
            if ($server_id[0] > 0)
            {
                $use['server_internal_id'] = $server1->server_internal_id;
                $use['platform_server_id'] = $server1->platform_server_id;
            } else
            {
                $use['server_internal_id'] = 0;
            }
            $use['platform_id'] = Session::get('platform_id');
            $use['start_time'] = $start_time;
            $use['end_time'] = $end_time;
            $use['interval'] = $interl_time[$interval];
            $use['filter'] = $filter_list[$filter_id];
            $use['source'] = $source;
            $use['u1'] = $u1;
            $use['u2'] = $u2;
            $resp = $api->getUserStat($use);
            if ($resp->http_code != 200)
            {
                return Response::json($resp->body, $resp->http_code);
            }
            $bod = $resp->body;
            //
            for($k = 0; $k <count($bod); $k ++){
                $arr[$k] = array(
                    'a' => 0,
                    'b' =>0,
                    'c' =>0,
                    'd' =>0
                );
            }

           //
            $sum = array(
                        'sum_formal' => 0,
                        'sum_anonymous' => 0,
                        'sum_player_formal' => 0,
                        'sum_player_anonymous' => 0,
                        'sum_lev_formal' => 0,
                        'sum_lev_anonymous' => 0,
                        'sum_count_anonymous_formal' => 0
                );
            for ($i = 0; $i < $len ; $i++) { 
                $server[$i] = Server::find($server_id[$i]);
                $user[$i] = array();
                $user[$i]['game_id'] = $game->game_id;
                if ($server_id[$i] > 0){
                    $user[$i]['platform_server_id'] = $server[$i]->platform_server_id;
                } else{
                    $user[$i]['platform_server_id'] = 0;
                }
                $user[$i]['platform_id'] = Session::get('platform_id');
                $user[$i]['start_time'] = $start_time;
                $user[$i]['end_time'] = $end_time;
                $user[$i]['interval'] = $interl_time[$interval];
                $user[$i]['filter'] = $filter_list[$filter_id];
                $user[$i]['source'] = $source;
                $user[$i]['u1'] = $u1;
                $user[$i]['u2'] = $u2;
                $response[$i] = $api->SXDGetUserStat($user[$i]);
                if ($response[$i]->http_code != 200)
                {
                    return Response::json($response[$i]->body, $response[$i]->http_code);
                }
                $body[$i] = $response[$i]->body;
                foreach ($body[$i] as $item) {
                    if ($server_id[$i] == 0) {
                        $item->count_player_formal = 0;
                        $item->count_player_anonymous = 0;
                        $item->count_lev_formal = 0;
                        $item->count_lev_anonymous = 0;
                    }
                    if ($i == 0) {
                        $sum['sum_formal'] += (int) $item->count_formal;
                        $sum['sum_anonymous'] += (int) $item->count_anonymous;
                        $sum['sum_player_formal'] += (int) $item->count_player_formal;
                    }
                    $sum['sum_player_anonymous'] += (int) $item->count_player_anonymous;
                    $sum['sum_lev_formal'] += (int) $item->count_lev_formal;
                    $sum['sum_lev_anonymous'] += (int) $item->count_lev_anonymous;
                    $sum['sum_count_anonymous_formal'] += (int) $item->count_anonymous_formal;
                    if ($interval == 3) {
                        $item->end_time = date('Y-m-d H:i:s', $end_time);
                        $item->ctime = date('Y-m-d H:i:s', $start_time);
                    } else {
                        $item->end_time = $interl_time[$interval] + (int) $item->ctime;
                        $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                        $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                    }
                }
                
                $statdata = array();
                
                $blank = new stdClass();
                $blank->ctime = null;
                $blank->end_time = null;
                $blank->source = null;
                $blank->u1 = null;
                $blank->u2 = null;
                $blank->count_formal = null;
                $blank->count_anonymous = null;
                $blank->count_anonymous_formal = null;
                $blank->count_player_formal = null;
                $blank->count_player_anonymous = null;
                $blank->count_lev_formal = null;
                $blank->count_lev_anonymous = null;
                
                for ($j = 0; $j < sizeof($body[$i]); $j ++) {
                    if ($j > 0 && $body[$i][$j]->ctime != $body[$i][$j - 1]->ctime) {
                        $statdata[] = $blank;
                    }
                    $statdata[$j] = new stdClass();
                    $statdata[$j]->ctime = 0;
                    $statdata[$j]->end_time= 0;
                    $statdata[$j]->source = 0;
                    $statdata[$j]->u1= '';
                    $statdata[$j]->u2= '';
                    $statdata[$j]->count_formal = 0;
                    $statdata[$j]->count_anonymous = 0;
                    $statdata[$j]->count_anonymous_formal =0;
                    
                   

                    $statdata[$j]->ctime = $body[0][$j]->ctime;
                    $statdata[$j]->end_time = $body[0][$j]->end_time;
                    $statdata[$j]->source = $body[0][$j]->source;
                    $statdata[$j]->u1 = isset($body[0][$j]->u1) ? $body[0][$j]->u1 : '';
                    $statdata[$j]->u2 = isset($body[0][$j]->u2) ? $body[0][$j]->u2 : '';
                    
                    $statdata[$j]->count_formal = $body[0][$j]->count_formal;
                    $statdata[$j]->count_anonymous = $body[0][$j]->count_anonymous;
                    $statdata[$j]->count_anonymous_formal = $body[0][$j]->count_anonymous_formal;
                    
                    $arr[$j]['a']+= $body[$i][$j]->count_player_formal;
                    $arr[$j]['b']+= $body[$i][$j]->count_player_anonymous;
                    $arr[$j]['c']+= $body[$i][$j]->count_lev_formal;
                    $arr[$j]['d']+= $body[$i][$j]->count_lev_anonymous;
                }
                
            }

            for($k = 0; $k< count($statdata); $k++) {

                $statdata[$k]->count_player_formal = 0;
                $statdata[$k]->count_player_anonymous= 0;
                $statdata[$k]->count_lev_formal = 0;
                $statdata[$k]->count_lev_anonymous = 0;

                $statdata[$k]->count_player_formal = $arr[$k]['a'];
                $statdata[$k]->count_player_anonymous = $arr[$k]['b'];
                $statdata[$k]->count_lev_formal = $arr[$k]['c'];
                $statdata[$k]->count_lev_anonymous = $arr[$k]['d'];

            }
            $data = array(
                        'items' => $statdata,
                        'sum' => $sum
                );
            if (isset($data)) {
                return Response::json($data);
            } else {
                return Response::json('error', 'error');
            }

        }
    }

    //FB

     public function THFBStatIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.user.th.fb', array(
                        'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }
    
    public function THFBStatData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknown'),
                'error' => Lang::get('error.server_not_found')
        );
    
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = trim(Input::get('end_time'));
    
        if ($end_time) {
            $end_time = strtotime($end_time);
        } else {
            $end_time = strtotime(date('Y-m-d 23:59:59'));
        }
    
        $server_ids = Input::get('server_id');
        if(count($server_ids) === 1){
            $server_id = $server_ids[0];
            $server = Server::find($server_id);
            if (!$server) {
                return Response::json($msg, 404);
            }
            
            $server_internal_id = Server::where('server_id', '=', $server_id)->pluck('server_internal_id');
            $game = Game::find(Session::get('game_id'));
            $platform_id = Session::get('platform_id');
            $fb = array(
                    'platform_id' => $platform_id,
                    'game_id' => $game->game_id,
                    'server_internal_id' => $server_internal_id,
                    'start_time' => $start_time,
                    'end_time' => $end_time
            );
            $u1 = trim(Input::get('u1'));
            $u2 = trim(Input::get('u2'));
            if ($u1) {
                $fb['u1'] = $u1;
            }
            if ($u2) {
                $fb['u2'] = $u2;
            }
            $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            $response = $api->THGetFBStat($fb);
            $body = $response->body;
            if ($response->http_code == 200) {
                foreach ($body as &$v) {
                    $v->spent = round($v->spent, 2);
                    $v->click_through_rate = round($v->click_through_rate, 2);
                    $v->count_formal_user = (int)$v->count_formal_user;
                    $v->count_formal_player = (int)$v->count_formal_player;
                    $v->count_formal_lev = (int)$v->count_formal_lev;
                    $v->cost_formal_user = $v->count_formal_user > 0 ? round($v->spent / $v->count_formal_user, 2) : 0;
                    $v->cost_formal_player = $v->count_formal_player > 0 ? round($v->spent / $v->count_formal_player, 2) : 0;
                    $v->cost_formal_lev = $v->count_formal_lev > 0 ? round($v->spent / $v->count_formal_lev, 2) : 0;
                    $v->total_user = (int)$v->total_user;
                    $v->total_player = (int)$v->total_player;
                    $v->total_lev = (int)$v->total_lev;
                    $v->cost_total_player = $v->total_player > 0 ? round($v->spent / $v->total_player, 2) : 0;
                    $v->cost_total_lev = $v->total_lev > 0 ? round($v->spent / $v->total_lev, 2) : 0;
                }
                unset($v);
                return Response::json($body);
            } else {
                return Response::json($body, $response->http_code);
            }
        } else {
            $result = array();
            foreach ($server_ids as $server_id)
            {
                $server = Server::find($server_id);
                $server_internal_id = Server::where('server_id', '=',
                        $server_id)->pluck('server_internal_id');
                $game = Game::find(Session::get('game_id'));
                $platform_id = Session::get('platform_id');
                $fb = array(
                        'platform_id' => $platform_id,
                        'game_id' => $game->game_id,
                        'server_internal_id' => $server_internal_id,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                );
                $u1 = trim(Input::get('u1'));
                $u2 = trim(Input::get('u2'));
                if ($u1)
                {
                    $fb['u1'] = $u1;
                }
                if ($u2)
                {
                    $fb['u2'] = $u2;
                }
                $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key,
                        $game->eb_api_secret_key);
                $response = $api->SXDGetFBStat($fb);
                $body = $response->body;
                if ($response->http_code == 200)
                {
                    $i = 0;
                    foreach ($body as &$v)
                    {
                        if (! isset($result[$i]))
                        {
                            $result[$i] = array(
                                    "count_formal_player" => 0,
                                    "count_formal_lev" => 0,
                                    "total_player" => 0,
                                    "total_lev" => 0
                            );
                        }
                        $result[$i]['count_formal_player'] += (int) $v->count_formal_player;
                        $result[$i]['count_formal_lev'] += (int) $v->count_formal_lev;
                        $result[$i]['total_player'] += (int) $v->total_player;
                        $result[$i]['total_lev'] += (int) $v->total_lev;
                        $result[$i]['campaign'] = $v->campaign;
                        $result[$i]['fb_u1'] = $v->fb_u1;
                        $result[$i]['fb_u2'] = $v->fb_u2;
                        $result[$i]['spent'] = round($v->spent, 2);
                        $result[$i]['click_through_rate'] = round(
                                $v->click_through_rate, 2);
                        $result[$i]['count_formal_user'] = (int) $v->total_user;
                        $result[$i]['total_user'] = (int) $v->total_user;
                        $i ++;
                    }
                    unset($v);
                } else
                {
                    continue;
                }
            }
            //重新计算成本
            foreach ($result as &$item)
            {
                $item['cost_formal_user'] = $item['count_formal_user'] > 0 ? round(
                        $item['spent'] / $item['count_formal_user'], 2) : 0;
                $item['cost_formal_player'] = $item['count_formal_player'] > 0 ? round(
                        $item['spent'] / $item['count_formal_player'], 2) : 0;
                $item['cost_formal_lev'] = $item['count_formal_lev'] > 0 ? round(
                        $item['spent'] / $item['count_formal_lev'], 2) : 0;
                $item['cost_total_player'] = $item['total_player'] > 0 ? round(
                        $item['spent'] / $item['total_player'], 2) : 0;
                $item['cost_total_lev'] = $item['total_lev'] > 0 ? round(
                        $item['spent'] / $item['total_lev'], 2) : 0;
            }
            unset($item);
            return Response::json($result);
        }
        
    }
}