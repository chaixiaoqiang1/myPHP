<?php

class YYSGServerPlayerController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }
    private function initTable()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/yysgwj.txt');
        return $table;
    }

    private function initTableByFilename($file_name)
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        return $table;
    }

    private function initTableItem()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/item.txt');
        return $table;
    }
    /**
     * 设置游戏管理员 @get
     */
    public function setGameMasterIndex()
    {
        $game = Game::find(Session::get('game_id'));
        $data = array(
            'content' => View::make('serverapi.yysg.player.gm',array(
                        'game_code' => $game->game_code
                )
            )
        );
        return View::make('main', $data);
    }

    /**
     * 设置游戏管理员 @post
     */
    public function setGameMaster()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $server = Server::where('game_id', $game_id)->first();
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );

        $rules = array(
            'is_gm' => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Response::json($msg, 403);
        }

        $player_name = Input::get('player_name');
        $player_id = (int)Input::get('player_id');
        $is_true = ( int ) Input::get('is_gm') == 1 ? 1 : 0;

        /*$server_id = 10001;
            //( int ) Input::get('server_id');
        $server = Server::find($server_id);
        if(! $server)
        {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }*/

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);//实际上没用到传入的参数。这里需要如何修改？

        /*$player = $api->getPlayerInfo($player_name, $platform_id);

        if(! isset($player->player_id))
        {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }*/

        $res = $api->setGameMaster($player_name, $player_id, $is_true, $platform_id, $game->game_code);
        if(isset($res->error)){
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '访问游戏服务器出错'
            );
            return Response::json($msg, 403);
        }
        return $api->sendResponse();
    }
    public function yysgPlayerIndex()
    {
        $player_id = (int)Input::get('player_id');
        $player_id = $player_id == 0 ? '' : $player_id;
        $server_init = Input::get('server_init');
        $server_init = $server_init ? $server_init : '';
        if($player_id){
            $game_id = Session::get('game_id');
            $game = Game::find($game_id);
            if('mnsg' == $game->game_code){
                $server_internal_id = floor(($player_id-25000)/100000);
                $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
                if($server){
                    $server_init = $server->server_id;
                }
            }
        }
        $servers = Server::currentGameServers()->get();
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $data = array(
                'content' => View::make('serverapi.yysg.player.yysg_player', array(
                        'servers' => $servers,
                        'game_code' => $game->game_code,
                        'player_id' => $player_id,
                        'server_init' => $server_init,
                ))
        );
        return View::make('main', $data);
    }
    public function yysgPlayerSearch()
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
        $game_id = (int)Session::get('game_id');
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $platform = Platform::find(Session::get('platform_id'));
        $id_or_name = Input::get('id_or_name');
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        
        if($choice == 0)    //昵称查询，必须有服务器信息
        {
            $id_or_name= addslashes($id_or_name);
            if(!$server_internal_id){
                return Response::json(array('error'=>'Please Select a Server.'), 401);
            }
            $response = $slave_api->getUserByPlayerName($platform_id, $id_or_name, $server_internal_id, $game->game_id);
        } else if($choice == 1)
        {
            if('mnsg' == $game->game_code){
                $mnsg_server_internal_id = floor(((int)$id_or_name)/100000);
                $tmp_server = Server::currentGameServers()->where('server_internal_id', $mnsg_server_internal_id)->first();
                if(!$tmp_server){
                    return Response::json(array('error'=>'不合法的player_id'),403);
                }else{
                    $server_internal_id = $mnsg_server_internal_id;
                }
            }else{
                if(!$server_internal_id){
                    return Response::json(array('error'=>'Please Select a Server.'), 401);
                }
            }
            $response = $slave_api->getUserByPlayerID($platform_id, $id_or_name, $server_internal_id, $game->game_id);

        }
        if($response->http_code != 200)
        {
            return Response::json($response->body, $response->http_code);
        }
        if(isset($response->body) && empty($response->body))
        {
            if(! $server)
            {
                $server = ( object ) array();
            }
            if($choice == 0)
            {
                $server->player_name = $id_or_name;
            } else if($choice == 1)
            {
                $server->player_id = ( int ) $id_or_name;
            }
            $response->body = array(
                    $server
            );
        }
        $players = array();
        foreach ( $response->body as $v )
        {
            if('yysg' == $game->game_code){
                $yysg_server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
                $server = Server::currentGameServers()->where('server_internal_id', $yysg_server_internal_id)->first();
            }elseif('mnsg' == $game->game_code){
                if(isset($v->player_id)){
                    $mg_player_id = $v->player_id;
                    $mnsg_server_internal_id = floor(((int)$v->player_id)/100000);
                }else{
                    return Response::json(array('error'=>'没有查询到玩家的信息'),403);
                }
                $server = Server::currentGameServers()->where('server_internal_id', $mnsg_server_internal_id)->first();
            }else{
               $server = Server::currentGameServers()->where('server_internal_id', $v->server_internal_id)->first(); 
            }
            if(! $server)
            {
                continue;
            }
            $player = array();
            if(isset($v->uid))
            {
                $player = array(
                        'server_internal_id' => $v->server_internal_id,
                        'player_name'=>isset($v->player_name) ? $v->player_name : '',
                        'device_type'=>isset($v->device_type) ? $v->device_type : '',
                        'nickname' => $v->nickname,
                        'uid' => $v->uid,
                        'login_email' => $v->login_email,
                        'first_lev' => $v->first_lev,
                        'all_pay_amount' => $v->all_pay_amount,
                        'all_pay_times' => $v->all_pay_times,
                        'avg_amount' => $v->all_pay_times > 0 ? round($v->all_pay_amount / $v->all_pay_times, 2) : 0,
                        'tp_user_id' => $v->tp_user_id,
                        'u' => $v->u,
                        'u2' => $v->u2,
                        'source' => $v->source,
                        'is_anonymous' => $v->is_anonymous,
                        'player_time'   =>  isset($v->player_time) ? $v->player_time : '',
                );

                //这里更新VIP玩家表中的player_name，并不判断是否是VIP玩家，如果不是则语句条件并不会找到对应需要更新的项
                if(isset($v->player_id) && isset($v->player_name)){
                    $data2update = array('player_name' => $v->player_name);
                    SpecialPlayers::where('game_id', $game_id)->where('player_id', $v->player_id)->update($data2update);
                    unset($data2update);
                }
            }
            
            /*
             * 添加first_login_ip和last_login_ip
            */
            $my_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            if (isset($player['login_email'])) {
                $login_email = $player['login_email'];
                $my_response = $my_api->getUserByEmail($platform->platform_id, $login_email, $server_internal_id , $game->game_id);
                if ($my_response->http_code == 200) {
                    $user_info = $my_response->body;

                    $ip_api = "http://freegeoip.net/json/";
                    $ip_api_created = $ip_api.$user_info->created_ip;
                    $ip_api_last = $ip_api.$user_info->last_visit_ip;
                    $response_created = Curl::url($ip_api_created)->get();
                    $response_last = Curl::url($ip_api_last)->get();
                    $created_ip_country = '';
                    $last_ip_country = '';
                    if ($response_created->http_code == 200)
                    {
                        if(isset($response_created->body->country_name)){
                            $created_ip_country = '('.$response_created->body->country_name.')';
                        }
                    }
                    if ($response_last->http_code == 200)
                    {
                        if(isset($response_last->body->country_name)){
                            $last_ip_country = '('.$response_last->body->country_name.')';
                        }
                    }

                    $player['created_ip'] = $user_info->created_ip.$created_ip_country;
                    $player['last_visit_ip'] = $user_info->last_visit_ip.$last_ip_country;

                }
            } else if (isset($player['uid'])) {
                $uid = $player['uid'];
                $my_response = $my_api->getUserByUID($platform->platform_id, $uid, $server_internal_id , $game->game_id);
                if ($my_response->http_code == 200) {
                    $user_info = $my_response->body;

                    $ip_api = "http://freegeoip.net/json/";
                    $ip_api_created = $ip_api.$user_info->created_ip;
                    $ip_api_last = $ip_api.$user_info->last_visit_ip;
                    $response_created = Curl::url($ip_api_created)->get();
                    $response_last = Curl::url($ip_api_last)->get();
                    $created_ip_country = '';
                    $last_ip_country = '';
                    if ($response_created->http_code == 200)
                    {
                        if(isset($response_created->body->country_name)){
                            $created_ip_country = '('.$response_created->body->country_name.')';
                        }
                    }
                    if ($response_last->http_code == 200)
                    {
                        if(isset($response_last->body->country_name)){
                            $last_ip_country = '('.$response_last->body->country_name.')';
                        }
                    }
                     
                    $player['created_ip'] = $user_info->created_ip.$created_ip_country;
                    $player['last_visit_ip'] = $user_info->last_visit_ip.$last_ip_country;
                }
            } 

            /*
             *操作结束
            */
            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
            if('yysg' == $game->game_code){
                if(isset($v->uid))
                {
                    $player_info_from_name = $api->getYYSGPlayerInfoByUID($v->uid, $platform_id);
                }elseif(isset($v->player_name)){
                    $player_info_from_name = $api->getYYSGPlayerInfo($v->player_name, $platform_id);
                }elseif($choice == 0 && isset($id_or_name)){
                    $player_info_from_name = $api->getYYSGPlayerInfo($id_or_name, $platform_id);
                }
            }elseif('mnsg' == $game->game_code){
                if($choice == 1){
                    $player_info_from_name = $api->getMNSGPlayerInfo($id_or_name, $platform_id);
                }else{
                    $player_info_from_name = $api->getMNSGPlayerInfo($mg_player_id, $platform_id);
                }
            }
            // 获取exp
            $player_exp = '';
            if(isset($player_info_from_name->exp))
            {
                $player_exp = $player_info_from_name->exp;
            }
            if('tstx' == $game->game_code){
                $player_server = array(
                    'which_server' => $server->server_name,
                    'player_id'=>isset($v->player_id) ? $v->player_id : '',
                    'last_login' => isset($v->last_visit_time) ? $v->last_visit_time : '',
                    'active' => isset($v->last_visit_time) ? floor(( int ) (time() - strtotime($v->last_visit_time)) / 86400) : '',
                );
            }else{
                $player_server = array(
                        'which_server' => $server->server_name,
                        'player_id' => isset($player_info_from_name->player_id) ? $player_info_from_name->player_id : '',
                        'last_login' => isset($player_info_from_name->last_time) ? date('Y-m-d H:i:s', $player_info_from_name->last_time) : '',
                        'level' => isset($player_info_from_name->lev) ? $player_info_from_name->lev : '',
                        'active' => isset($player_info_from_name->last_time) ? floor(( int ) (time() - $player_info_from_name->last_time) / 86400) : '',
                        'tongqian' => isset($player_info_from_name->mana) ? $player_info_from_name->mana : '',
                        'yuanbao' => isset($player_info_from_name->crystal) ? $player_info_from_name->crystal : '',
                        'exp' => $player_exp,
                        'rank' => isset($player_info_from_name->rank) ? $player_info_from_name->rank : '',
                        'energy' => isset($player_info_from_name->energy) ? $player_info_from_name->energy : '',
                        'point' => isset($player_info_from_name->point) ? $player_info_from_name->point : '',
                        'glory' => isset($player_info_from_name->glory) ? $player_info_from_name->glory : '',
                        'invitation' => isset($player_info_from_name->invitation) ? $player_info_from_name->invitation : '',
                        'social' => isset($player_info_from_name->social) ? $player_info_from_name->social : '',
                        //萌娘的一些独有字段
                        'vip' => isset($player_info_from_name->vip) ? $player_info_from_name->vip : '',
                        'arena_coin' => isset($player_info_from_name->arena_coin) ? $player_info_from_name->arena_coin : '',
                        'march_coin' => isset($player_info_from_name->march_coin) ? $player_info_from_name->march_coin : '',
                        'region_coin' => isset($player_info_from_name->region_coin) ? $player_info_from_name->region_coin : '',
                        'guild_id' => isset($player_info_from_name->guild_id) ? $player_info_from_name->guild_id : '',
                        //夜夜三国新增某些独有字段
                        'player_location' => isset($player_info_from_name->player_location) ? $player_info_from_name->player_location : '',
                );
            }

            if(isset($player_info_from_name->player_name)){
                $player['player_name'] = $player_info_from_name->player_name;
            }
            $players[] = $player + $player_server;
        }
        if(! empty($players))
		{
			return Response::json($players);
		} else
		{
			return Response::json(array(
					'error' => Lang::get('basic.not_found')
			), 404);
		}
	}

    /**
     * 封禁账号 @get
     */
    public function closeAccountIndex()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $data = array(
            'content' => View::make('serverapi.yysg.player.closeAccount',
                array(
                    'game_code' => $game->game_code,
                ))
        );
        return View::make('main', $data);
    }

    /**
     * 封禁账号 @post
     */
    public function closeAccountSend()  //此功能目前仅适用于夜夜三国和萌娘三国，不选择服务器
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');

        $url_type = Input::get('url_type');
        $player_id = Input::get('player_id');
        $player_name = Input::get('player_name');
        $reason = Input::get('close_reason');
        $is_banned = ( int ) Input::get('is_banned') == 1 ? true : false;
        $ban_time = (int)Input::get('ban_time');
        $explain_ban_type = array(
                '0' => '永久封禁',
                '3600' => '封禁1小时',
                '86400' =>  '封禁1天',
                '259200' => '封禁3天',
            );
        if(1 == $url_type){//页面加载自动提交
            $operation_data = Operation::where('game_id',$game_id)
                ->whereIn('operation_type',array('ban_game','unban_game'))
                ->orderBy('operation_id','DESC')
                ->take(10)
                ->get();
            $operation_data = $operation_data->toArray();
            $items = array();
            foreach ($operation_data as $item) {
                $item['operate_time'] = date('Y-m-d H:i:s',$item['operate_time']);
                $temp_extra_msg = explode('|', $item['extra_msg']);
                $temp_ban_type = isset($temp_extra_msg[1]) ? $temp_extra_msg[1] : '0';
                if('ban_game' == $item['operation_type']){
                    $item['operation_type'] = $explain_ban_type[$temp_ban_type];
                }else{
                    $item['operation_type'] = (0 == $temp_ban_type) ? '手动解封' : '自动解封';
                }
                $item['reason'] = $temp_extra_msg[0];
                $items[] = $item;
            }
            return Response::json($items);
        }elseif(2 == $url_type){//查询记录
            $start_time = strtotime(trim(Input::get('start_time')));
            $end_time = strtotime(trim(Input::get('end_time')));
            $operation_data = Operation::where('game_id',$game_id)
                ->whereBetween('operate_time',array($start_time,$end_time))
                ->whereIn('operation_type',array('ban_game','unban_game'))
                ->orderBy('operation_id','DESC')
                ->get();
            $operation_data = $operation_data->toArray();
            $items = array();
            foreach ($operation_data as $item) {
                $item['operate_time'] = date('Y-m-d H:i:s',$item['operate_time']);
                $temp_extra_msg = explode('|', $item['extra_msg']);
                $temp_ban_type = isset($temp_extra_msg[1]) ? $temp_extra_msg[1] : '0';
                if('ban_game' == $item['operation_type']){
                    $item['operation_type'] = $explain_ban_type[$temp_ban_type];
                }else{
                    $item['operation_type'] = (0 == $temp_ban_type) ? '手动解封' : '自动解封';
                }
                $item['reason'] = $temp_extra_msg[0];
                $items[] = $item;
            }
            return Response::json($items);
        }
        
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->first();
        }
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );

        $rules = array(
            'close_reason' => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Response::json($msg, 403);
        }
        if(empty($player_name) && empty($player_id)){
            return Response::json(array('error'=>'请输入玩家昵称或ID!'), 403);
        }
        if(!empty($player_name)){
            $player_names = array(Input::get('player_name'));
        }elseif(!empty($player_id)){
            $player_ids = array((int)Input::get('player_id'));
        }
        
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);//实际上没用到传入的参数。这里需要如何修改？

        if(isset($player_names)){
            $res = $api->closeAccountName($player_names, $is_banned, $platform_id);
        }elseif(isset($player_ids)){
            $res = $api->closeAccountId($player_ids, $is_banned, $platform_id, $game->game_code);
        }
        if(isset($res->errors)){
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '访问游戏服务器出错'
            );
            return Response::json($msg, 403);
        }elseif(is_array($res) && (true == $res[0]->is_banned)){
            $msg = array(
                'msg' => '封禁成功',
                'status' => 'ok',
            );
        }elseif(is_array($res) && (false == $res[0]->is_banned)){
            $msg = array(
                'msg' => '解封成功',
                'status' => 'ok',
            );
        }else{
            $msg = array(
                'error' => '操作失败',
            );
            return Response::json($msg, 403);
        }


        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $operator = Auth::user()->username;
        if($is_banned){
            $type = 'ban_game';
        }else{
            $type = 'unban_game';
        }
        if(!empty($player_names)){
            foreach ($player_names as $single_name) {
                $server_name = $server->server_name;
                $playerinfo = $slave_api->getplayeridbyname($game_id, $single_name, $server->server_internal_id, $platform_id);
                $playerinfobody = $playerinfo->body;
                if($playerinfo->http_code != '200'){
                    $player_id = '';
                }else{
                    $player_id = $playerinfobody[0]->player_id;
                }
                    $operation = Operation::insert(array('operate_time' => time(),
                                                         'game_id' => $game_id, 
                                                         'player_id' => $player_id,
                                                         'player_name' => $single_name,
                                                         'operator' => $operator,
                                                         'server_name' => $server_name,
                                                         'operation_type' => $type,
                                                         'extra_msg' => $reason.'|'.$ban_time,

                    ));
                
            }
        }elseif(!empty($player_ids)){
            foreach ($player_ids as $single_id) {
                $server_name = $server->server_name;
                $playerinfo = $slave_api->getplayernamebyid($game_id, $single_id, $server->server_internal_id, $platform_id);
                $playerinfobody = $playerinfo->body;
                if($playerinfo->http_code != '200'){
                    $player_name = '';
                }else{
                    $player_name = $playerinfobody[0]->player_name;
                }
                    $operation = Operation::insert(array('operate_time' => time(),
                                                         'game_id' => $game_id, 
                                                         'player_id' => $single_id,
                                                         'player_name' => $player_name,
                                                         'operator' => $operator,
                                                         'server_name' => $server_name,
                                                         'operation_type' => $type,
                                                         'extra_msg' => $reason.'|'.$ban_time,

                    ));
                
            }
        }

        $operation_data = Operation::where('game_id',$game_id)
            ->whereIn('operation_type',array('ban_game','unban_game'))
            ->orderBy('operation_id','DESC')
            ->take(10)
            ->get();
        $operation_data = $operation_data->toArray();
        $items = array();
        foreach ($operation_data as $item) {
            $item['operate_time'] = date('Y-m-d H:i:s',$item['operate_time']);
            $temp_extra_msg = explode('|', $item['extra_msg']);
            $temp_ban_type = isset($temp_extra_msg[1]) ? $temp_extra_msg[1] : '0';
            if('ban_game' == $item['operation_type']){
                $item['operation_type'] = $explain_ban_type[$temp_ban_type];
            }else{
                $item['operation_type'] = (0 == $temp_ban_type) ? '手动解封' : '自动解封';
            }
            $item['reason'] = $temp_extra_msg[0];
            $items[] = $item; 
        }
        $result = array(
            'result' => $msg,
            'items' => $items,
            );
        return Response::json($result);
    }


    /**
     * 封禁聊天 @get
     */
    public function bannedTalkIndex()   
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $data = array(
            'content' => View::make('serverapi.yysg.player.bannedTalk',
            array('game_code' =>  $game->game_code)
            )
        );
        return View::make('main', $data);
    }

    /**
     * 封禁聊天 @post
     */
    public function bannedTalkSend()    //此功能目前仅适用于夜夜三国和萌娘三国，不选择服务器
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');

        $url_type = Input::get('url_type');
        $player_id = Input::get('player_id');
        $player_name = Input::get('player_name');
        $reason = Input::get('close_reason');
        $is_banned = ( int ) Input::get('is_banned') == 1 ? true : false;
        $ban_time = (int)Input::get('ban_time');
        $explain_ban_type = array(
                '0' => '永久封禁',
                '3600' => '封禁1小时',
                '86400' =>  '封禁1天',
                '259200' => '封禁3天',
        );
        if(1 == $url_type){//页面加载自动提交
            $operation_data = Operation::where('game_id',$game_id)
                ->whereIn('operation_type',array('ban_room','unban_room'))
                ->orderBy('operation_id','DESC')
                ->take(10)
                ->get();
            $operation_data = $operation_data->toArray();
            $items = array();
            foreach ($operation_data as $item) {
                $item['operate_time'] = date('Y-m-d H:i:s',$item['operate_time']);
                $temp_extra_msg = explode('|', $item['extra_msg']);
                $temp_ban_type = isset($temp_extra_msg[1]) ? $temp_extra_msg[1] : '0';
                if('ban_room' == $item['operation_type']){
                    $item['operation_type'] = $explain_ban_type[$temp_ban_type];
                }else{
                    $item['operation_type'] = (0 == $temp_ban_type) ? '手动解封' : '自动解封';
                }
                $item['reason'] = $temp_extra_msg[0];
                $items[] = $item;
            }
            return Response::json($items);
        }elseif(2 == $url_type){//查询记录
            $start_time = strtotime(trim(Input::get('start_time')));
            $end_time = strtotime(trim(Input::get('end_time')));
            $operation_data = Operation::where('game_id',$game_id)
                ->whereBetween('operate_time',array($start_time,$end_time))
                ->whereIn('operation_type',array('ban_game','unban_game'))
                ->orderBy('operation_id','DESC')
                ->get();
            $operation_data = $operation_data->toArray();
            $items = array();
            foreach ($operation_data as $item) {
                $item['operate_time'] = date('Y-m-d H:i:s',$item['operate_time']);
                $temp_extra_msg = explode('|', $item['extra_msg']);
                $temp_ban_type = isset($temp_extra_msg[1]) ? $temp_extra_msg[1] : '0';
                if('ban_game' == $item['operation_type']){
                    $item['operation_type'] = $explain_ban_type[$temp_ban_type];
                }else{
                    $item['operation_type'] = (0 == $temp_ban_type) ? '手动解封' : '自动解封';
                }
                $item['reason'] = $temp_extra_msg[0];
                $items[] = $item;
            }
            return Response::json($items);
        }

        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->first();
        }
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );

        $rules = array(
            'close_reason' => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Response::json($msg, 403);
        }

        $player_names = array(Input::get('player_name'));
        $player_ids = array(Input::get('player_id'));

        if(empty($player_names[0]) && empty($player_ids[0])){
            return Response::json(array('error'=>'请输入玩家昵称或ID!'), 403);
        }

        $reason = Input::get('close_reason');
        $is_banned = ( int ) Input::get('is_banned') == 1 ? true : false;

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);//实际上没用到传入的参数。这里需要如何修改？

       
        $res = $api->bannedTalk($player_names, $player_ids, $is_banned, $platform_id, $game->game_code);
        

        //Log::info(var_export($res,true));

        if(isset($res->errors)){
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '访问游戏服务器出错'
            );
            return Response::json($msg, 403);
        }elseif(is_array($res) && (true == $res[0]->is_banned)){
            $msg = array(
                'msg' => '封禁成功',
                'status' => 'ok',
            );
        }elseif(is_array($res) && (false == $res[0]->is_banned)){
            $msg = array(
                'msg' => '解封成功',
                'status' => 'ok',
            );
        }else{
            $msg = array(
                'error' => '操作失败',
            );
            return Response::json($msg, 403);
        }

        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $operator = Auth::user()->username;
        if($is_banned){
            $type = 'ban_room';
        }else{
            $type = 'unban_room';
        }
        if(($player_names[0])){
            foreach ($player_names as $single_name) {
                $server_name = $server->server_name;
                $playerinfo = $slave_api->getplayeridbyname($game_id, $single_name, $server->server_internal_id, $platform_id);
                $playerinfobody = $playerinfo->body;
                if($playerinfo->http_code != '200'){
                    $player_id = '';
                }else{
                    $player_id = $playerinfobody[0]->player_id;
                }
                    $operation = Operation::insert(array('operate_time' => time(),
                                                         'game_id' => $game_id, 
                                                         'player_id' => $player_id,
                                                         'player_name' => $single_name,
                                                         'operator' => $operator,
                                                         'server_name' => $server_name,
                                                         'operation_type' => $type,
                                                         'extra_msg' => $reason.'|'.$ban_time,

                    ));
                
            }
        }elseif(($player_ids[0])){
            foreach ($player_ids as $single_id) {
                $server_name = $server->server_name;
                $playerinfo = $slave_api->getplayernamebyid($game_id, $single_id, $server->server_internal_id, $platform_id);
                $playerinfobody = $playerinfo->body;
                if($playerinfo->http_code != '200'){
                    $player_name = '';
                }else{
                    $player_name = $playerinfobody[0]->player_name;
                }
                    $operation = Operation::insert(array('operate_time' => time(),
                                                         'game_id' => $game_id, 
                                                         'player_id' => $single_id,
                                                         'player_name' => $player_name,
                                                         'operator' => $operator,
                                                         'server_name' => $server_name,
                                                         'operation_type' => $type,
                                                         'extra_msg' => $reason.'|'.$ban_time,

                    ));
                
            }
        }

        $operation_data = Operation::where('game_id',$game_id)
            ->whereIn('operation_type',array('ban_room','unban_room'))
            ->orderBy('operation_id','DESC')
            ->take(10)
            ->get();
        $operation_data = $operation_data->toArray();
        $items = array();
        foreach ($operation_data as $item) {
            $item['operate_time'] = date('Y-m-d H:i:s',$item['operate_time']);
            $temp_extra_msg = explode('|', $item['extra_msg']);
            $temp_ban_type = isset($temp_extra_msg[1]) ? $temp_extra_msg[1] : '0';
            if('ban_room' == $item['operation_type']){
                $item['operation_type'] = $explain_ban_type[$temp_ban_type];
            }else{
                $item['operation_type'] = (0 == $temp_ban_type) ? '手动解封' : '自动解封';
            }
            $item['reason'] = $temp_extra_msg[0];
            $items[] = $item; 
        }
        $result = array(
            'result' => $msg,
            'items' => $items,
            );
        return Response::json($result);
    }

    public function checkAccountStatuIndex(){   //夜夜三国查询玩家账号封禁状态 禁言状态
        $data = array(
            'content' => View::make('serverapi.yysg.player.checkplayerstatu'
            )
        );
        return View::make('main', $data);
    }

    public function checkAccountStatuSend(){    //夜夜三国查询玩家账号封禁状态 禁言状态

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $player_name = Input::get('player_name');
        $player_id = Input::get('player_id');

        if(empty($player_name) && empty($player_id)){
            return Response::json(array('error'=>'请输入玩家昵称或ID!'), 403);
        }

        if(!empty($player_name)){
            $response = Operation::where('player_name',$player_name)
                                ->whereIn('operation_type',array('ban_game','ban_room','unban_room','unban_game'));
            if(in_array($game_id, Config::get('game_config.yysggameids'))){
                $response->whereIn('game_id', Config::get('game_config.yysggameids'));
            }else{
                $response->where('game_id', $game_id);
            }
            $response = $response->get();
        } elseif (!empty($player_id)) {
            $response = Operation::where('player_id',$player_id)
                                ->whereIn('operation_type',array('ban_game','ban_room','unban_room','unban_game'));
            if(in_array($game_id, Config::get('game_config.yysggameids'))){
                $response->whereIn('game_id', Config::get('game_config.yysggameids'));
            }else{
                $response->where('game_id', $game_id);
            }
            $response = $response->get();
        }

        $result = json_decode($response);
        if(!empty($result)){
            $returnresult = array();
            foreach ($result as $value) {
                switch ($value->operation_type) {
                    case 'ban_game':
                        $operation = '封禁账号';
                        break;
                    case 'unban_game':
                        $operation = '解封账号';
                        break;                   
                    case 'ban_room':
                        $operation = '禁言玩家';
                        break;
                    case 'unban_room':
                        $operation = '解封禁言';
                        break;                                            
                    default:
                        $operation = '不明操作';
                        break;
                }
                $returnresult[] = array(
                    'player_name' => $value->player_name,
                    'player_id' => $value->player_id,
                    'ban_type' => $operation,
                    'time' => date('Y-m-d H:i:s',$value->operate_time),
                    'operator' => $value->operator,
                    'reason' => $value->extra_msg,
                    );
            }
        }else{
            return Response::json(array('error'=>'未能查询到数据。'), 403);
        }
        return Response::json($returnresult); 
    }

    public function playerLogIndex(){//Log查询
        $data = array(
            'content'=> View::make('serverapi.yysg.player.log_search')
        );
        return View::make('main',$data);
    }
    public function playerLogSearch(){

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
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $id_or_name = Input::get('id_or_name');
        $choice = (int)Input::get('choice');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $server_internal_id = 1;
        $api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key);
        if(0 == $choice){
            $player = $api->getplayeridbyname($game_id, $id_or_name, $server_internal_id, $platform_id);
            if(200 == $player->http_code){
                $id_or_name = (int)$player->body[0]->player_id;
            }else{
              return Response::json(array('error'=>'Not Found player_name'),403);
            } 
        }
        $result = array(); 
        $response = $api->playerLogDate($game_id, $id_or_name, $start_time, $end_time);
        if($response->http_code != 200){
            return Response::json($response->body, $response->http_code);
        }
        $body = $response->body;

        foreach ($body as $key => $value) {
            $result[] = array(
                'player_id' => $value->player_id,
                'scroll_id' => Lang::get("yysgwj.$value->scroll_id"),
                'table_id' => Lang::get("yysgwj.$value->table_id"),
                'created_at' => date('Y-m-d H:i:s',$value->created_at)
            );
        }
        return Response::json($result);

    }

    public function playerLogItemIndex(){ //查询道具日志log_item表载入方法
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        $items_data = array();
        if('yysg' == $game->game_code){ //夜夜三国要查两张表，一个item表一个giftbox表
            $giftboxnames = Table::initarray(public_path() . '/table/' . $game->game_code . '/giftbox.txt');
            $giftboxnames = $giftboxnames->getData();
            foreach ($giftboxnames as $giftboxname) {
                $items_data[$giftboxname['id']] = $giftboxname['name'];
            }
            unset($giftboxnames);
            $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/table.txt');
        }else{
            $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/item.txt');
        }
        $table_data = $table->getData();
        unset($table);
        foreach ($table_data as $item) {
            $items_data[$item['id']] = $item['name'];
        }
        unset($table_data);

        $player_id = (int)Input::get('player_id');
        $servers = Server::currentGameServers()->get();
        $game = Game::find(Session::get('game_id'));
        $data = array(
            'content'=> View::make('serverapi.mnsg.player.log_item',array(
                    'servers' => $servers,
                    'player_id' => $player_id,
                    'game_code' => $game->game_code,
                    'items_data' => $items_data,
                )),
        );
        return View::make('main',$data);
    }

    public function playerLogItemSearch(){   //查询道具日志log_item表查询方法-mnsg，
        $msg = array(                       //注意，目前萌娘是靠玩家id来确定服务器的，未来有需要更改的地方的话最好在页面增加一个选择服务器
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'player_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if($validator->fails())
        {
            return Response::json($msg, 403);
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $player_id = (int)Input::get('player_id');
        $table_id = Input::get('table_id');
        if(strstr($table_id, ':')){
            $table_info = explode(':', $table_id);
            $table_id = $table_info[0];
        }
        if($player_id<100000){
            return Response::json(array('error'=>'请输入完整的玩家ID!'), 403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $result = array();
        $api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key); 
        $response = $api->playerLogItemData($game_id, $player_id, $table_id, $start_time, $end_time);
        if($response->http_code != 200){
            return Response::json($response->body, $response->http_code);
        }
        $body = $response->body;

        if('yysg' == $game->game_code){ //夜夜三国要查两张表，一个item表一个giftbox表
            $giftboxnames = Table::initarray(public_path() . '/table/' . $game->game_code . '/giftbox.txt');
            $giftboxnames = $giftboxnames->getData();
            $giftboxid2name = array();
            foreach ($giftboxnames as $giftboxname) {
                $giftboxid2name[$giftboxname['id']] = $giftboxname['name'];
            }
            unset($giftboxnames);
            $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/table.txt');
        }else{
            $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/item.txt');
        }
        $table_data = $table->getData();
        unset($table);
        $items = array();
        foreach ($table_data as $item) {
            $items[$item['id']] = $item['name'];
        }
        unset($table_data);

        $talbe_game_message = Table::initarray(public_path() . '/table/' . $game->game_code . '/game_message.txt');
        $data_game_message = $talbe_game_message->getData();
        $operations = array();
        foreach ($data_game_message as $operation) {
            $operations[$operation['id']] = $operation['desc'];
        }
        unset($talbe_game_message);
        unset($data_game_message);
        if(in_array($game_id, Config::get('game_config.yysggameids'))){ //返回值类型不同，夜夜三国的话需要分成两部分统计
            $returneditems = (array)$body->item;
            $returnedgiftboxs = (array)$body->giftbox;
            unset($body);
            foreach ($returneditems as $key1 => $value1) {  //这里通过循环把两部分的时间进行排序整理,查询结果是自动按照时间排序的
                if(isset($items[$value1->table_id])){
                    $itemname = $items[$value1->table_id];
                }else{
                    $itemname = $value1->table_id;
                }
                if(isset($operations[$value1->mid])){
                    $operation_name = $operations[$value1->mid];
                }else{
                    $operation_name = $value1->mid;
                }
                foreach ($returnedgiftboxs as $key2 => $value2) {   //如果第一部分中的时间大于第二部分中的时间，则循环第二部分插入返回结果中并删除第二部分中的这一项
                    if($value1->created_at >=  $value2->created_at){
                        if(isset($operations[$value2->mid])){
                            $operation_name2 = $operations[$value2->mid];
                        }else{
                            $operation_name2 = $value2->mid;
                        }
                        if(isset($giftboxid2name[$value2->table_id])){
                            $giftbagname = $giftboxid2name[$value2->table_id];
                        }else{
                            $giftbagname = $value2->table_id;
                        }
                        $result[] = array(
                            'player_id' => $player_id,
                            'mid' => $operation_name2,
                            'table_id' => '邮箱--'.$giftbagname,
                            'num' => 1,
                            'created_at' => date('Y-m-d H:i:s',$value2->created_at)
                        );
                        unset($returnedgiftboxs[$key2]);
                    }else{  //如果第一部分中的时间小于第二部分中的时间，则跳出循环插入第一部分中此条的数据
                        break;
                    }
                }
                $result[] = array(
                    'player_id' => $player_id,
                    'mid' => $operation_name,
                    'table_id' => $itemname,
                    'num' => $value1->num,
                    'created_at' => date('Y-m-d H:i:s',$value1->created_at)
                );
                unset($returneditems[$key1]);   //这里能保证所有的处于第一部分中的数据完全插入，所以结束后要重新循环一次第二部分
            }
            foreach ($returnedgiftboxs as $key2 => $value2) {
                if(isset($operations[$value2->mid])){
                    $operation_name2 = $operations[$value2->mid];
                }else{
                    $operation_name2 = $value2->mid;
                }
                if(isset($giftboxid2name[$value2->table_id])){
                    $giftbagname = $giftboxid2name[$value2->table_id];
                }else{
                    $giftbagname = $value2->table_id;
                }
                $result[] = array(
                    'player_id' => $player_id,
                    'mid' => $operation_name2,
                    'table_id' => '邮箱--'.$giftbagname,
                    'num' => 1,
                    'created_at' => date('Y-m-d H:i:s',$value2->created_at)
                );
                unset($returnedgiftboxs[$key2]);
            }
        }else{
            foreach ($body as $key => $value) {
                if(isset($items[$value->table_id])){
                    $itemname = $items[$value->table_id];
                }else{
                    $itemname = $value->table_id;
                }
                if(isset($operations[$value->mid])){
                    $operation_name = $operations[$value->mid];
                }else{
                    $operation_name = $value->mid;
                }
                $result[] = array(
                    'player_id' => $player_id,
                    'mid' => $operation_name,
                    'table_id' => $itemname,
                    'num' => $value->num,
                    'created_at' => date('Y-m-d H:i:s',$value->created_at)
                );
            }
        }
        return Response::json($result);
    }

    public function userlifetimeIndex(){ //夜夜三国查询玩家生命周期载入方法
        $servers = Server::currentGameServers()->get();
        $data = array(
            'content'=> View::make('serverapi.yysg.player.lifetime',array('servers' => $servers))
        );
        return View::make('main',$data);
    }

    public function userlifetimeData(){ //夜夜三国查询玩家生命周期查询方法
        $game_id = (int)Session::get('game_id');
        $platform_id = (int)Session::get('platform_id');

        $server_id = (int)Input::get('server_id');
        $server_internal_id = Server::find($server_id)->server_internal_id;
        $platform_server_id = Server::find($server_id)->platform_server_id;
        $check_type = (int)Input::get('check_type');
        $check_data_type = (int)Input::get('check_data_type');

        if('0' == $server_id){
            return Response::json(array('error'=>'请选择服务器!'), 403);
        }
        if('0' == $check_type){
            return Response::json(array('error'=>'请选择查看类型!'), 403);
        }
        if('0' == $check_data_type){
            return Response::json(array('error'=>'请选择查看时长!'), 403);
        }

        $result = array();
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key); 
        $response = $api->playerlifetime($game_id, $platform_id, $platform_server_id, $check_type, $check_data_type-1, $server_internal_id);

        if($response->http_code != 200){
                return Response::json($response->body, $response->http_code);
        }
        $body = $response->body;
        switch ($check_data_type-1) {
            case '0':
                    $time_stamp = '3天内';
                    break;
            case '1':
                    $time_stamp = '3-7天';
                    break;    
            case '2':
                    $time_stamp = '7-30天';
                    break;
            case '3':
                    $time_stamp = '30天以上';
                    break;           
            default:
                    break;
        }
        if(!empty($body)){
            foreach ($body as $key => $value) {
                $result[] = array(
                    'time_stamp' => $time_stamp,
                    'num' => $value->count,
                    'avgtime' => $value->avgtime/86400,
                );
            }
        }
        return Response::json($result);
    }
        /**
     * 设置新手指导员 @get
     */
    public function setBeginnerMasterIndex()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        if('yysg' == $game->game_code){
            $player_key = 'player_name';
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
            $masters = $api->getBeginnerMaster($platform_id);
        }else{
            $player_key = 'player_id';
            $server = Server::where('game_id', $game_id)->first();
        }
        if(isset($masters->list)){
            $masters = $masters->list;
        }else{
            $masters = array();
        }
        $data = array(
            'content' => View::make('serverapi.yysg.player.beginner', 
                array(
                    'masters' => $masters,
                    'player_key' => $player_key,
                    )
            )
        );
        return View::make('main', $data);
    }

    /**
     * 设置新手指导员 @post
     */
    public function setBeginnerMaster()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->first();
        }
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );

        $player_name = Input::get('player_name');
        $player_id = Input::get('player_id');
        if(!$player_name && !$player_id){
            return Response::json($msg, 403);
        }
        $player_info = $player_name ? $player_name : $player_id;
        $is_true = (int) Input::get('is_beginner') == 1 ? 1 : 0;

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

        $res = $api->setBeginnerMaster($player_info, $is_true, $platform_id, $game->game_code);
        $result = array();
        if(isset($res->error)){
            $result[] = array(
                'status' => 'error',
                'msg' => $res->error
            );
        }elseif(isset($res->result) && $res->result == 'OK'){
            $result[] = array(
                'status' => 'ok',
                'msg' => '设置成功'
            );
        }else{
            $result[] = array(
                'msg' => ' 设置 : ' . 'error' . "\n",
                'status' => 'error'
            );
        }
        $msg = array(
                'result' => $result
        );
        return Response::json($msg);
    }

    //手游查询当前在线人数
    public function getonlinenumload(){
        $servers = $this->getUnionServers();
        $data = array(
            'content'=> View::make('serverapi.yysg.player.online_num',array('servers' => $servers))
        );
        return View::make('main',$data);
    }

    public function getonlinenumpost(){
        $server_ids = Input::get('server_id');
        if('0' == $server_ids){
            return Response::json(array('error'=>'请选择服务器!'), 403);
        }
        if(1 == count($server_ids) && '0' == $server_ids[0]){
            $servers = $this->getUnionServers();
        }else{
            foreach ($server_ids as $value) {
                $server=Server::find($value);
                if($server){
                   $servers[] = $server; 
                }else{
                     continue;
                }
            }
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $time = date('Y-m-d H:i:s', time());
        $platform_id = Session::get('platform_id');
        $result = array();
        $total_online = 0;
        foreach ($servers as $server) {
                if($game_id != $server->game_id){
                    return Response::json(array('error'=>'游戏与服务器不对应，请确认未切换游戏!'), 403);
                }
                if(in_array($game_id, Config::get('game_config.mobilegames'))){
                    $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
                    $response = $api->getonlinenum($server->server_internal_id, $platform_id, $game->game_code);
                    if(isset($response->num)){
                        $total_online = $total_online + $response->num;
                        $result[] = array(
                            'time' => $time,
                            'server_name' => $server->server_track_name,
                            'online_num' => $response->num,
                            );
                    }
                }else{
                    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port ,$server->api_dir_id);
                    $response = $api->getOnlinePlayersNumber();
                    if(isset($response->num_online)){
                        $total_online = $total_online + $response->num_online;
                        $result[] = array(
                            'time' => $time,
                            'server_name' => $server->server_track_name,
                            'online_num' => $response->num_online,
                            );
                    }
                }
            
            
        }
        $result = array_reverse($result);
        $total = array(
            'total' => 'Total',
            'total_online' => $total_online,
            'time' => $time,
        );
        $data = array(
            'total' => $total,
            'result' => $result,
        );
        //Log::info(var_export($result,true));die();
        if(count($data['result']) > 0){
            return Response::json($data);
        }else{
            return Response::json(array('error'=>'未查询到数据'), 403);
        }
    }
    private function initAwardTable($file_name)
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        return $table;
    }

    public function playerWjIndex(){//夜夜三国查询武将是否被吃掉
       /*$game_id = Session::get('game_id');//夜夜三國转武将表
       $game = Game::find($game_id);
       $table = $this->initAwardTable('yysgwj');
       $table = $table->getData();
       $attribute = array(1 => '水',2 => '火',3 => '風',4 => '光',5 => '暗');
       $yysgwj = array();
       foreach ($table as $value) {
            $yysgwj[$value->id] = $attribute[$value->type] . $value->name;
       } 
       Log::info(var_export($yysgwj,true));die();*/

        $yysgwj = Lang::get('yysgwj');
        $data = array(
            'content'=> View::make('serverapi.yysg.player.log_wj',
                array(
                    'yysgwj' => $yysgwj
                ))
        );
        return View::make('main',$data);
    }
    public function playerWjData(){

        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'player_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if($validator->fails())
        {
            return Response::json($msg, 403);
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if('yysg' != $game->game_code){
            return Response::json(array('error'=>'确保所选游戏是夜夜三国'),403);
        }
        $player_id = (int)Input::get('player_id');
        $wj = (int)Input::get('yysgwj_id_name');
        $wj_id = (int)Input::get('yysgwj_id');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        /*if(0 == $wj && 0 == $wj_id){
            return Response::json($msg, 403);
        }*/
        if(0 != $wj){
            $wj_id = $wj;
        }
        $result = array();
        $api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key); 
        $response = $api->playerWjData($game_id, $player_id, $wj_id, $start_time, $end_time);
        if($response->http_code != 200){
            return Response::json($response->body, $response->http_code);
        }
        $body = $response->body;
        if(empty($body)){
            return Response::json(array('error'=>'该玩家在该时段没有被吃掉的武将'),403);
        }

        foreach ($body as $key => $value) {
            $eaten = explode(',', $value->material_table_ids);
            $eatemwj = '';
            foreach ($eaten as $wjid) {
                $eatemwj .= Lang::get("yysgwj.$wjid").',';
            }
            $result[] = array(
                'player_id' => $value->player_id,
                'mid' => $value->mid,
                'table_id' => Lang::get("yysgwj.$value->table_id"),
                'partner_id' => $value->partner_id,
                'lev' => $value->lev,
                'star' => $value->star,
                'material_table_ids' => $eatemwj,
                'created_at' => date('Y-m-d H:i:s',$value->created_at)
            );
        }
        return Response::json($result);

    }

    //手游查询时间段内玩家平均在线时长
    public function mgonlinetimeload(){
        $servers = Server::currentGameServers()->get();
        $data = array(
            'content'=> View::make('serverapi.yysg.player.online_time',array('servers' => $servers))
        );
        return View::make('main',$data);       
    }

    public function mgonlinetimepost(){
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        $server_ids = Input::get('server_ids');
        //获取页面其他值
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $limit_pay_user = (int)Input::get('limit_pay_user');    //准备定这个值域为-1,0,1，其中-1代表不付费玩家，0代表所有玩家，1代表付费玩家
        $lev_low = (int)Input::get('lev_low');
        $lev_up = (int)Input::get('lev_up');

        if($lev_low > $lev_up && 0 != $lev_up){
            return Response::json(array('error'=>'等级下限大于了等级上限'),403);
        }

        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $response = array();
        $sum = array(
            'server_name' => 'Total',
            'online_num' => 0,
            'all_online_time' => 0,
            'all_login_times' => 0,
            );
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            $result = $slave_api->getMGavgonlinetime($server->server_internal_id, $game_id, $platform_id, $start_time, $end_time, $lev_low, $lev_up, $limit_pay_user);
            if(200 != $result->http_code){
                $response[] = array(
                    'server_name' => $server->server_name,
                    'online_num' => 0,
                    'all_online_time' => 0,
                    'all_login_times' => 0,
                );
            }else{
                $result = $result->body;
                $sum['online_num'] +=  $result->playernum;
                $sum['all_online_time'] +=  $result->all_online_time;
                $sum['all_login_times'] +=  $result->all_login_times;
                $response[] = array(
                    'server_name' => $server->server_name,
                    'online_num' => $result->playernum,
                    'all_online_time' => $result->all_online_time,
                    'all_login_times' => $result->all_login_times,
                    );
            }
        }
        $response[] = $sum;
        return Response::json($response);
    }

    //萌娘三国增加VIP经验
    public function mnsgrestoreload(){
        $servers = Server::currentGameServers()->get();
        $data = array(
            'content'=> View::make('serverapi.yysg.player.mnsgrestore',array('servers' => $servers))
        );
        return View::make('main',$data); 
    }

    public function mnsgrestorepost(){
        $server_id = Input::get('server_id');
        if('0' == $server_id){
            return Response::json(array('error'=>'请选择服务器'),403);
        }
        $server = Server::find($server_id);
        $game_id = Session::get('game_id');
        if(!$server){
            return Response::json(array('error'=>'无效的服务器'),403);
        }
        if($server->game_id != $game_id){
            return Response::json(array('error'=>'游戏和服务器不对应，请刷新页面查看当前游戏和平台'),403);
        }
        $increase_type = Input::get('increase_type');
        if('0' == $increase_type){
            return Response::json(array('error'=>'请选择操作类型'),403);
        }       
        $player_id = (int)Input::get('player_id');
        $delta = (int)Input::get('delta');
        $platform_id = Session::get('platform_id');
        if('0' == $player_id || '0' == $delta){
            return Response::json(array('error'=>'无效的参数，请重新输入'),403);
        }

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

        $result = $api->mnsgrestore($platform_id, $player_id, $delta, $increase_type);
        $result = (array)$result;
        $result = $result['economy_']->charge;

        if(isset($result->error)){
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '访问游戏服务器出错'
            );
            return Response::json($msg, 403);
        }
        return Response::json(array('result'=>'操作成功!用户当前VIP经验为:'.$result));
    }

    public function adviceload(){
        $data = array(
            'content'=> View::make('serverapi.yysg.player.uploadadvice',array())
        );
        return View::make('main',$data); 
    }

    public function adviceupload(){
        $advice_to = Input::get('advice_to');
        if('0' == $advice_to){
            return Response::json(array('error'=>'请选择分组'),403);
        }
        $explain = Input::get('explain');
        if(!$explain){
            return Response::json(array('error'=>'请输入处理记录'),403);
        }  
        $username = Input::get('username');
        if(!$username){
            return Response::json(array('error'=>'请输入花名'),403);
        }  
        $time = time();
        $data = array(
                'task_type' => $advice_to,
                'task_msg' => $explain,
                'username' => $username,
                'created_time' => $time,
                'task_status' => 0,
                'last_update_time' => 0,
            );
        $operation = DB::table('log_tasks')->insert($data);
        if(false == $operation){
            return Response::json(array('error'=>'未能成功创建'),403);
        }else{
            return Response::json(array('result'=>'创建成功'));
        }
    }

    public function taskload(){
        $data = array(
            'content'=> View::make('serverapi.yysg.player.check_tasks',array())
        );
        return View::make('main',$data);         
    }

    public function taskdeal(){
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $username = Input::get('username_check');
        $check_type = Input::get('check_type');
        $is_finished = Input::get('is_finished');

        $result = DB::table('log_tasks')->whereBetween('created_time', array($start_time, $end_time));
        if($username){
            $result->where('username', $username);
        }
        if('2' == $is_finished){
            $result->where('task_status', 4);
        }else{
            $result->whereIn('task_status', array(0,1,2,3));
        }
        if($check_type){
            $result->where('task_type', $check_type);
        }
        $result = $result->get();
        $response = array();
        $task_type = array(
                        '1' => '官网',
                        '2' => '安卓SDK',
                        '3' => 'IOS-SDK',
                        '4' => 'eastblue',
                    );
        $task_status = array(
                        '0' => '尚未开始',
                        '1' => '正在进行',
                        '2' => '线下测试中',
                        '3' => '线上测试中',
                        '4' => '已完成',
                    );
        foreach ($result as $task) {
           $response[] = array(
                            'id' => $task->id,
                            'time' => date('Y-m-d H:i:s',$task->created_time),
                            'task_type' => $task_type[$task->task_type],
                            'finish_time' => 0 == $task->last_update_time? 0:date('Y-m-d H:i:s',$task->last_update_time),
                            'task_msg' => $task->task_msg,
                            'username' => $task->username,
                            'is_finished' => $task_status[$task->task_status], 
                        );
        }
        return Response::json($response);
    }

    public function change_task_status(){
        $id = Input::get('change_task_id');
        $change_username = Input::get('change_username');
        $change_status = Input::get('change_status');
        $change_type = Input::get('change_type');
        $change_explain = Input::get('change_explain');
        $time = time();
        $data = array();
        $data['last_update_time'] = $time;
        if($change_username){
            $data['username'] = $change_username;
        }
        if($change_status){
            $data['task_status'] = $change_status;
        }
        if($change_type){
            $data['task_type'] = $change_type;
        }
        if($change_explain){
            $data['task_msg'] = $change_explain;
        }
        Log::info(var_export($data, true));
        Log::info(var_export($id, true));
        $result = DB::table('log_tasks')->where('id', $id)->update($data);

        if(false == $result){
            return Response::json(array('error'=>'更新状态失败'),403);
        }else{
            return Response::json(array('result'=>'更新状态成功'));
        }
    }

    public function show_task(){
        $id = Input::get('task_id');
        Log::info(var_export($id, true));
        $result = DB::table('log_tasks')->find($id);
        $result = (array)$result;
        $response = array();
        $task_type = array(
                        '1' => '官网',
                        '2' => '安卓SDK',
                        '3' => 'IOS-SDK',
                        '4' => 'eastblue',
                    );
        $task_status = array(
                        '0' => '尚未开始',
                        '1' => '正在进行',
                        '2' => '线下测试中',
                        '3' => '线上测试中',
                        '4' => '已完成',
                    );
        $response[] = array(
                        'id' => $result['id'],
                        'created_time' => date('Y-m-d H:i:s', $result['created_time']),
                        'task_type' => $task_type[(int)$result['task_type']],
                        'last_update_time' => 0 == $result['last_update_time']? 0:date('Y-m-d H:i:s', $result['last_update_time']),
                        'task_msg' => $result['task_msg'],
                        'username' => $result['username'],
                        'task_status' => $task_status[(int)$result['task_status']], 
                    );
        return Response::json($response);
    }
    //萌娘三国开启或关闭五星好评活动
    public function fivestarsload(){
        $servers = Server::currentGameServers()->get();
        $data = array(
            'content'=> View::make('serverapi.mnsg.gm.fivestars_switch',array('servers' => $servers))
        );
        return View::make('main',$data);    
    }

    public function fivestarsswitch(){
        $server_ids = Input::get('server_id');
        if('0' == count($server_ids) || ('1' == count($server_ids) && '0' == $server_ids[0])){
            return Response::json(array('error'=>'请选择服务器'),403);
        }
        $switch_type = Input::get('switch_type');
        if('0' == $switch_type){
            return Response::json(array('error'=>'请选择开启或关闭'),403);
        }
        $switch_type--;
        $platform_id = Session::get('platform_id'); 
        $result = array();
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if($server){
                $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
                $response = $api->switch_fivestars($server->server_internal_id, $switch_type, $platform_id);
                $response = (array)$response;
                if(isset($response['result']) && 'OK' == $response['result']){
                    $result[] = array(
                        'msg' => $server->server_track_name.' OK',
                        'status' => 'ok',
                        );
                }else{
                    $result[] = array(
                        'msg' => $server->server_track_name.' ERROR',
                        'status' => 'error',
                        );                    
                }
            }
        }
        if(count($result) > 0){
            return Response::json($result);
        }else{
            return Response::json(array('error'=>'没有操作有效的服务器'),403);
        }
    }

    public function playerEquipmentIndex(){//夜夜三国查询装备
        $data = array(
            'content'=> View::make('serverapi.yysg.player.equipment')
        );
        return View::make('main',$data);
    }
    public function playerEquipmentData(){

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
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        if('yysg' != $game->game_code){
            return Response::json(array('error'=>'确保所选游戏是夜夜三国'),403);
        }
        $id_or_name = Input::get('id_or_name');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $choice = (int)Input::get('choice');
        $server_internal_id = 1;

        $api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key);
        if(0 == $choice){
            $player = $api->getplayeridbyname($game_id, $id_or_name, $server_internal_id, $platform_id);
            if(200 == $player->http_code){
                $id_or_name = (int)$player->body[0]->player_id;
            }else{
              return Response::json(array('error'=>'Not Found player_name'),403);
            } 
        }

        $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/equipment.txt');
        $table_data = $table->getData();
        $equipments = array();
        foreach ($table_data as $equipment) {
            $equipments[$equipment['id']] = $equipment['name'];
        }
        $tab = Table::initarray(public_path() . '/table/' . $game->game_code . '/game_message.txt');
        $tab_data = $tab->getData();
        $mids = array();
        foreach ($tab_data as $mid) {
            $mids[$mid['id']] = $mid['desc'];
        }

        $result = array();
         
        $response = $api->playerEquipmentData($game_id, $id_or_name, $start_time, $end_time);
        if($response->http_code == 200){
            $body = $response->body;
            if(!empty($body)){
                foreach ($body->get as $key => $value) {
                    $result['get'][] = array(
                        'player_id' => $value->player_id,
                        'mid' => isset($mids[$value->mid]) ? $mids[$value->mid] : $value->mid,
                        'rune_id' => $value->rune_id,
                        'table_id' => isset($equipments[$value->table_id]) ? $equipments[$value->table_id] : $value->table_id,
                        'star' => $value->star,
                        'rarity' => $value->rarity,
                        'attr' => $value->attr,
                        'created_at' => date('Y-m-d H:i:s',$value->created_at)
                    );
                }
                foreach ($body->powerup as $key => $value) {
                    $result['powerup'][] = array(
                        'player_id' => $value->player_id,
                        'rune_id' => $value->rune_id,
                        'table_id' => isset($equipments[$value->table_id]) ? $equipments[$value->table_id] : $value->table_id,
                        'lev' => $value->lev,
                        'star' => $value->star,
                        'rarity' => $value->rarity,
                        'attr_id' => $value->attr_id,
                        'attr_value' => $value->attr_value,
                        'created_at' => date('Y-m-d H:i:s',$value->created_at)
                    );
                }
                foreach ($body->equip as $key => $value) {
                    $result['equip'][] = array(
                        'player_id' => $value->player_id,
                        'partner_id' => Lang::get("yysgwj.$value->partner_table_id"),
                        'slot' => $value->slot,
                        'on_id' => $value->on_rune_id.':'.(isset($equipments[$value->on_table_id]) ? $equipments[$value->on_table_id] : $value->on_table_id),
                        'on_star' => $value->on_star,
                        'on_rarity' => $value->on_rarity,
                        'on_attr' => $value->on_attr,
                        'off_id' => $value->off_rune_id.':'.(isset($equipments[$value->off_table_id]) ? $equipments[$value->off_table_id] : $value->off_table_id),
                        'off_star' => $value->off_star,
                        'off_rarity' => $value->off_rarity,
                        'off_attr' => $value->off_attr,
                        'created_at' => date('Y-m-d H:i:s',$value->created_at)
                    );
                }
                foreach ($body->sell as $key => $value) {
                    $result['sell'][] = array(
                        'player_id' => $value->player_id,
                        'rune_id' => $value->rune_id,
                        'rune_table_id' => isset($equipments[$value->rune_table_id]) ? $equipments[$value->rune_table_id] : $value->rune_table_id,
                        'star' => $value->star,
                        'rarity' => $value->rarity,
                        'attr' => $value->attr,
                        'created_at' => date('Y-m-d H:i:s',$value->created_at)
                    );
                }
            }
        }
        return Response::json($result);
    }

    public function playerGetWjIndex(){//夜夜三国武将获得日志
        $data = array(
            'content'=> View::make('serverapi.yysg.player.playerwj')
        );
        return View::make('main',$data);
    }

    public function playerGetWjData(){

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
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        if('yysg' != $game->game_code){
            return Response::json(array('error'=>'确保所选游戏是夜夜三国'),403);
        }
        $id_or_name = Input::get('id_or_name');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $choice = (int)Input::get('choice');
        $server_internal_id = 1;
        $table_id = Input::get('table_id');
        if(strstr($table_id, ':')){
            $table_info = explode(':', $table_id);
            $table_id = $table_info[0];
        }

        $api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key);
        if(0 == $choice){
            $player = $api->getplayeridbyname($game_id, $id_or_name, $server_internal_id, $platform_id);
            if(200 == $player->http_code){
                $id_or_name = (int)$player->body[0]->player_id;
            }else{
              return Response::json(array('error'=>'Not Found player_name'),403);
            } 
        }
        $result = array();
         
        $response = $api->playerGetWjData($game_id, $id_or_name, $start_time, $end_time, $table_id);
        if($response->http_code != 200){
            return Response::json($response->body, $response->http_code);
        }
        $body = $response->body;
        if(empty($body)){
            return Response::json(array('error'=>'该玩家在该时段没有获得武将'),403);
        }

        $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/game_message.txt');
        $table_data = $table->getData();
        $mids = array();
        foreach ($table_data as $mid) {
            $mids[$mid['id']] = $mid['desc'];
        }

        foreach ($body as $key => $value) {
            $result[] = array(
                'player_id' => $value->player_id,
                'mid' => isset($mids[$value->mid]) ? $mids[$value->mid] : $value->mid,
                'partner_id' => $value->partner_id,
                'table_id' => Lang::get("yysgwj.$value->table_id"),
                'lev' => $value->lev,
                'star' => $value->star,
                'created_at' => date('Y-m-d H:i:s',$value->created_at)
            );
        }
        return Response::json($result);

    }

    public function NewerPointIndex(){  //夜夜三国及萌娘三国新手打点
        $data = array(
            'content'=> View::make('serverapi.yysg.player.point')
        );
        return View::make('main',$data);        
    }

    public function NewerPointData(){
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));

        $game_id = (int)Session::get('game_id');
        $platform_id = (int)Session::get('platform_id');

        $game = Game::find($game_id);

        $slave_api = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key);

        $result = $slave_api->getNewerPointInfo($game_id, $start_time, $end_time);

        $table = $this->initTableByFilename('point');
        $table = $table->getData();
        $point_names = array();
        foreach ($table as $value) {
            $point_names[$value->id] = $value->operation;
        }
        unset($table);
        unset($value);

        if('200' == $result->http_code){
            $result = $result->body;
            if(isset($result->point_info)){
                foreach ($result->point_info as &$value) {
                    $value->point = isset($point_names[$value->point]) ? $point_names[$value->point] : $value->point;
                }
            }
            return Response::json($result);
        }else{
            return $slave_api->sendResponse();
        }
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

}