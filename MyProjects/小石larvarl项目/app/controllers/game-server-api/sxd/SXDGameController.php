<?php

class SXDGameController extends \BaseController {

    public function userIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('serverapi.sxd.user.index', array(
                        'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }

    public function userData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
                'email_or_uid' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        }
        $email_or_uid = trim(Input::get('email_or_uid'));
        $choice = (int) Input::get('choice');

        $platform = Platform::find(Session::get('platform_id'));
    
       
        if (! $platform)
        {
            return Response::json($msg, 404);
        }
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        if ($choice == 0)
        { // 根据官网账号查询
            $response = $api->getUserByEmail($platform->platform_id, 
                    $email_or_uid, $server_internal_id = 0, $game->game_id);
        } else
        { // 根据UID查询
            $response = $api->getUserByUID($platform->platform_id, 
                    $email_or_uid, $server_internal_id = 0, $game->game_id);
        }
        if ($response->http_code != 200)
        {
            return Response::json($response->body, 404);
        }
        $user_info = $response->body;
        //$s = var_export($user_info, true);
        //Log::info($s);
        $user_basic = array(
                'uid' => $user_info->uid,
                'name' => $user_info->name ? $user_info->name : 'none',
                'nickname' => $user_info->nickname ? $user_info->nickname : 'none',
                'login_email' => $user_info->login_email,
                'contact_email' => $user_info->contact_email,
                'created_ip' => $user_info->created_ip,
                'last_visit_ip' => $user_info->last_visit_ip,
                'created_time' => $user_info->created_time,
                'last_visit_time' => $user_info->last_visit_time,
                'nums_created_player' => count($user_info->players),
                'u' => $user_info->u,
                'u2' => $user_info->u2,
                'source' => $user_info->source,
                'is_anonymous' => $user_info->is_anonymous
        );
        $created_players = $user_info->players;
        foreach ($created_players as $item)
        {
            $server_name = '';
            $server = Server::currentGameServers()->where('server_internal_id', 
                    $item->server_id)->first();
            $item->last_login = '';
            if ($server)
            {
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
            }
            
            $item->server_id = $server_name ? $server_name : 'none';
            $item->created_time = date('Y-m-d H:i:s', $item->created_time);
            if (isset($item->all_pay_amount))
            {
                $item->avg_amount = $item->all_pay_times > 0 ? round(
                        $item->all_pay_amount / $item->all_pay_times, 2) : 0;
            }
        }
        $user = array(
                'user_basic' => $user_basic,
                'created_players' => $user_info->players
        );
        $user = (object) $user;
        return Response::json($user);
    }
    
    public function playerIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('serverapi.sxd.player.index', array(
                        'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }
    public function playerData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
                'id_or_name' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
    
        if($validator->fails())
        {
            return Response::json($msg, 403);
        }
    
        $choice = ( int ) Input::get('choice');
    
        $server_id = ( int ) Input::get('server_id');
    
        $server = Server::find($server_id);
        $server_internal_id = 0;
        if($server)
        {
            $server_internal_id = $server->server_internal_id;
        }
    
        $servers = array();
    
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
    
        $id_or_name = trim(Input::get('id_or_name'));
    
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        //Log::info(var_export(Input::all(), true));
        //Log::info(var_export($slave_api, true));
        if($choice == 0)
        {
            $response = $slave_api->getUserByPlayerName($platform_id, $id_or_name, $server_internal_id, $game->game_id);
        } else if($choice == 1)
        {
            $response = $slave_api->getUserByPlayerID($platform_id, $id_or_name, $server_internal_id, $game->game_id);
        }
        $player = array();
        //Log::info(var_export($response,true));
        foreach($response->body as $v){
            $server_name = '';
            $server = Server::currentGameServers()->where('server_internal_id', $v->server_internal_id)->first();
            if($server){
                $server_name = $server->server_name;
            }
            $player[] = array(
                    'nickname' => $v->nickname,
                    'uid' => $v->uid,
                    'server_name' => $server_name,
                    'login_email' => $v->login_email,
                    'player_id' => $v->player_id,
                    'player_name' => $v->player_name,
                    'first_lev' => $v->first_lev,
                    'all_pay_amount' => $v->all_pay_amount ? $v->all_pay_amount : 0 ,
                    'all_pay_times' => $v->all_pay_times,
                    'avg_amount' => $v->all_pay_times > 0 ? round($v->all_pay_amount / $v->all_pay_times, 2) : 0,
                    'tp_user_id' => $v->tp_user_id,
                    'u' => $v->u,
                    'u2' => $v->u2,
                    'source' => $v->source,
                    'is_anonymous' => $v->is_anonymous
            );
        }
        if($response->http_code != 200)
        {
            return Response::json($response->body, 404);
        } else {
            return Response::json($player, $response->http_code);
        }
    }
    public function sendGiftIndex()
    {
        $servers = Server::currentGameServers()->get();
        
        $table = $this->initTable();
        
        $gifts = $table->getData();
        
        $data = array(
                'content' => View::make('serverapi.sxd.giftbag.index', 
                        array(
                                'servers' => $servers,
                                'gifts' => $gifts
                        ))
        );
        return View::make('main', $data);
    }

    private function initTable()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/sxd/gift.txt');
        return $table;
    }

    public function sendGiftData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
                'server_id' => 'required|numeric|min:1',
                'gift_bag_id' => 'required|numeric|min:1',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        }
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server)
        {
            $msg['error'] = Lang::get('error.server_not_found');
            return Response::json($msg, 404);
        }
        $gift_bag_id = (string) Input::get('gift_bag_id');
        $player_name = trim(Input::get('player_name'));
        $player_id = (int)Input::get('player_id');
        //
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if($player_name){
            $response = $slave_api->getUserByPlayerName($platform_id, $player_name, $server->server_internal_id, $game->game_id);
        } else {
            $response = $slave_api->getUserByPlayerID($platform_id, $player_id, $server->server_internal_id, $game->game_id);
        }
        
        // $s = var_export($response,true);
        // Log::info($s);
        $uid = '';
        $body = $response->body;
        if($response->http_code != 200)
        {
            return Response::json($response->body, 404);
        } else {
            foreach($body as $v){
                $uid = $v->uid;
                break;
            }
        }
        //
        $server_ip = $server->server_ip;
        //Log::info(var_export($uid.'--'.$gift_bag_id .'--'. $server_ip, true));
        $response = SXDGameServerApi::sxd_sendGift($uid, $gift_bag_id, $server_ip);
        //Log::info(var_export($response, true));
        if(isset($response->error_code)){
            $msg['error'] = Lang::get('error.server_not_found');
            return Response::json($msg, 404);
        } 
        
        $code_arr = array(
        	'1' => Lang::get('sxd.success'),
        	'0' => Lang::get('sxd.failure'),
        	'2' => Lang::get('sxd.server_not_found'),
        	'3' => Lang::get('sxd.wrong_params'),
        	'4' => Lang::get('sxd.wrong_ip'),
        	'5' => Lang::get('sxd.wrong_sig'),
        	'7' => Lang::get('sxd.wrong_player'),
        	'-7' => Lang::get('sxd.limit_times'),
        	'-8' => Lang::get('sxd.limit_platform'),
        	'-1' => Lang::get('sxd.activity_not_open'),
        );
        if($response == '1'){
        	$result = array(
        		'status' => 'ok',
        		'msg' => $code_arr[$response],
        	);
        } else {
            if(isset($code_arr[$response])){
                $result = array(
                        'status' => 'error',
                        'msg' => $code_arr[$response],
                );
            } else {
                $result = array(
                        'status' => 'error',
                        'msg' => 'Something is wrong',
                );
            }
        }
        $msg = array (
                'result' => $result
        );
        return Response::json ( $msg );
    }
    public function SXDFBStatIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.user.sxd.fb', array(
                        'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }
    
    public function SXDFBStatData()
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
            $response = $api->SXDGetFBStat($fb);
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

    public function sxdGiftGroupIndex()
    {
        $server = Server::currentGameServers()->get();
        $table = $this->initTable();
        $gifts = $table->getData();
        $data = array(
            'content' => View::make('serverapi.sxd.giftbag.giftbag_group', array('server' => $server, 'gifts' => $gifts))
        );
        return View::make('main', $data);
    }
    public function sxdGiftGroupSend()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => '',
        );
        $rules = array(
            'gift_id' => 'required',
            'gift_data' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $send_type = trim(Input::get('send_type'));
        $game_id = Session::get('game_id');
        $gift_id = Input::get('gift_id');
        $text = trim(Input::get('gift_data'));
        $player = array();
        $player = explode("\n", $text);
        foreach ($player as $key => $value) {
            $player_info[] = explode("\t", $value);
        }
        //Log::info(var_export($player_info, true));
        $game = Game::find($game_id);
        //Log::info(var_export($game, true));
        $platform_id = Session::get('platform_id');
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
       // Log::info(var_export($slave_api, true));
        $msg = array();
        $result = array();
        foreach ($player_info as &$value) {
            $server = Server::whereRaw("game_id = {$game_id} and server_track_name = '{$value[1]}'")->first();
            //Log::info(var_export($server, true));
            if (!$server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 403);
            }
            if ($send_type != 3) {
                if($send_type == 1){ //player_name
                    $response = $slave_api->getUserByPlayerName($platform_id, $value[0], $server->server_internal_id, $game->game_id);
                } elseif ($send_type == 2) { //player_id
                    $response = $slave_api->getUserByPlayerID($platform_id, $value[0], $server->server_internal_id, $game->game_id);
                }
                //$uid = '';
                $body = $response->body;
                //Log::info( 'SXD send gift player body'.var_export($body, true));
                if($response->http_code != 200)
                {
                    return Response::json($response->body, 404);
                } else {
                    foreach($body as $v){
                        $value[2] = $v->uid;
                        break;
                    }
                }
            }else{
                $value[2] = $value[0];
            }
            $server_ip = $server->server_ip;
            $response = SXDGameServerApi::sxd_sendGift($value[2], $gift_id, $server_ip);
            if(isset($response->error_code)){
                $msg['error'] = Lang::get('error.server_not_found')."====".$server->srever_name;
                return Response::json($msg, 404);
            } 
            
            $code_arr = array(
                '1' => Lang::get('sxd.success'),
                '0' => Lang::get('sxd.failure'),
                '2' => Lang::get('sxd.server_not_found'),
                '3' => Lang::get('sxd.wrong_params'),
                '4' => Lang::get('sxd.wrong_ip'),
                '5' => Lang::get('sxd.wrong_sig'),
                '7' => Lang::get('sxd.wrong_player'),
                '-7' => Lang::get('sxd.limit_times'),
                '-8' => Lang::get('sxd.limit_platform'),
                '-1' => Lang::get('sxd.activity_not_open'),
            );
            if($response == '1'){
                $result[] = array(
                    'status' => 'ok',
                    'msg' => $value[0]."==".$code_arr[$response],
                );
            } else {
                //Log::info('SXD send all git result'.$response);
                if(isset($code_arr[$response])){
                    $result[] = array(
                            'status' => 'error',
                            'msg' => $value[0]."==".$code_arr[$response],
                    );
                } else {
                    $result[] = array(
                            'status' => 'error',
                            'msg' => $value[0]."==".'Something is wrong',
                    );
                }
            }


        }
        $msg = array (
                'result' => $result
        );
        return Response::json ( $msg );
    }
}