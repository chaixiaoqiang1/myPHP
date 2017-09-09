<?php

class SlaveApiEconomyLogController extends \BaseController
{
    private function initTable()
    {
        $game = Game::find(Session::get('game_id'));
        if('poker' == $game->game_code){
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/game_message_poker.txt');
        }else{
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/game_message.txt');
        }
        return $table;
    }

    private function initTableByName($file_name)
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        return $table;
    }

    public function allServerIndex()
    {
        //$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('slaveapi.economy.allserver', array(
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    private function getNeiwan($game_id){
        $table = Table::init(public_path() . '/table/neiwan.txt');
        $message = $table->getData();
        $neiwan_uids = array();
        foreach ($message as $k => $v) {
            if($v->game_id == $game_id){
                $neiwan_uids[] = $v->uid;
            }
        }
        return $neiwan_uids;
    }
    public function sendAllServer()
    {
        $msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'error' => Lang::get('error.server_not_found')
        );
        
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server) {
            return Response::json($msg, 403);
        }
        
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $player_level = (int)Input::get('player_level');
        $type_id = (int) Input::get('type');
        switch ($type_id) {
            case 0:
                $type = "yuanbao";
                break;
            case 1:
                $type = "tongqian";
                break;
            case 2:
                $type = "gongxun";
                break;
            default:
        }
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $spend = 0;
        $servers = $this->getUnionServers();


        foreach ($servers as $value) {
            $response = $api->getAllServersConsume($game->game_id,$platform_id,$value->server_internal_id,$type,$start_time,$end_time);
            if($response->http_code==200){
                $spend += $response->body->spend;
                if($value->server_id == $server_id){
                    $this_server_spend = $response->body->spend;
                }
            }else{
                continue;
            }
        }

        $is_filter_neiwan = Input::get("filter_type1") == "true";

        $vip_levels = Input::get('vip_level');
        if(count($vip_levels) == 0){
            $vip_selector = '1,1,1,1,1,1,1,1,1,1,1,1,1';
        }else{
            $tmp_str= '';
            for ($i=0; $i <= 12; $i++) { 
                if(in_array($i, $vip_levels)){
                    $tmp_str .= '1';
                }else{
                    $tmp_str .= '0';
                }
                if($i != 12){
                    $tmp_str .= ',';
                }
            }
            $vip_selector = $tmp_str;
        }

        $response = $api->getServerEconomyStatistics($game->game_id, $platform_id, $server->server_internal_id, $type, $start_time, $end_time, $player_level, $is_filter_neiwan, $vip_selector);

        $body = $response->body;
        if ($response->http_code == 200) {
            $table = $this->initTable();
            $messages = $table->getData();
            $this_server_vip_spend = 0;
            foreach ($body as $x => $y) {
                $this_server_vip_spend += $y->spend;
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

            $all_server = array(
               'spend' => $spend,
                'num' => 'all-player',
                'action_time' => date('Y-m-d H:i:s', time()),
                'action_type' => '全服消费总计',
                'action_name' => 'Total' 
            );
            $selected_server = array(
                'spend' => $this_server_spend,
                'num' => 'this_server_player',
                'action_time' => date('Y-m-d H:i:s', time()),
                'action_type' => '该服消费统计',
                'action_name' => $server->server_name
            );
            $selected_server_vip = array(
                'spend' => $this_server_vip_spend,
                'num' => 'this_server_vip_player',
                'action_time' => date('Y-m-d H:i:s', time()),
                'action_type' => '该服满足相应vip等级及玩家等级消费统计',
                'action_name' => $server->server_name
            );
            $data = array(
                'body' => $body,
                'this_server' => $selected_server,
                'this_server_vip' => $selected_server_vip,
                'total'=> $all_server
            );
            return Response::json($data);
        } else {
            return Response::json($body, $response->http_code);
        }
    }

    public function playerIndex()
    {
        $table = $this->initTable();
        $messages = $table->getData();
        $mids = array();
        foreach($messages as $message){
            $mids[] = array(
                'mid' => $message->id,
                'desc' => $message->desc,
            );
        }
        $servers = Server::currentGameServers()->get();
        $game = Game::find(Session::get('game_id'));
        $data = array(
            'content' => View::make('slaveapi.economy.player', array(
                'servers' => $servers,
                'game_code' => $game->game_code,
                'mids' =>$mids
            ))
        );
        return View::make('main', $data);
    }

    public function sendPlayer()
    {
        $msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'error' => Lang::get('error.server_not_found')
        );
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server) {
            return Response::json($msg, 403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $type1 = (int) Input::get('type1');
        $type2 = (int) Input::get('type2');
        $look_type = Input::get('look_type');
        switch ($type1) {
            case 0:
                $type = "yuanbao";
                break;
            case 1:
                $type = "tongqian";
                break;
            case 2:
                $type = "shengwang";
                break;
            case 3:
                $type = 'tili';
                break;
            case 4:
                $type = 'jingjiedian';
                break;
            case 5:
                $type = 'yueli';
                break;
            case 6:
                $type = 'xianling';
                break;
            case 7:
                $type = 'boat_book';
                break;
            case 8 :
                 $type = "lingshi";
                 break;
            case 9:
                $type = 'star_fragment';
                break;
            case 10:
                $type = 'talent_point';
                break;
            case 11:
                $type = 'heaven_token';
                break;
            case 12:
                $type = 'skill_fragment';
                break;
            case 13:
                $type = 'fight_spirit';
                break;
            case 14:
                $type = 'end_rings_exp';
                break;
            case 15:
                $type = 'power';
                break;
            case 16:
                $type = 'mount_fragment';
                break;
            case 17:
                $type = 'jing_po';
                break;
            case 18:
                $type = 'follow_card';
                break;
            case 19:
                $type = 'fruit_currency';
                break;
            default:
                break;
        }
        $game = Game::find(Session::get('game_id'));
        $player_name = Input::get('player_name');
        $player_id = (int)Input::get('player_id');
        $action_type_num = Input::get('action_type_num');

        if(!$player_name && !$player_id && !$action_type_num){
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }elseif(1 == $type2 && !$player_id && !$player_name){
            return Response::json(array('error'=>'Please enter palyerID or playerName!'), 403);
        }

        try{
            $action_type_num = explode(":", $action_type_num);
            $action_type_num = $action_type_num[0];
        }catch(\Exception $e){
           
        }
        $page = (int) Input::get('page');
        $page = $page > 0 ? $page : 1;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        
        if($player_name && !$player_id){
            $response = $api->getCreatePlayerInfo($uid = '', $player_id = '', $player_name, $game->game_id, $server->server_internal_id);
            //Log::info("player info ======================>".var_export($response, true));
            if($response->http_code == 200 && isset($response->body) && isset($response->body->player_id)){
                $player_id = $response->body->player_id;
            }else{
               $game_api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
               $player_id_from_name = $game_api->getPlayerInfoByName($player_name); 
               if(isset($player_id_from_name->player_id)){
                    $player_id = $player_id_from_name->player_id;
               }else{
                    $msg['error'] = Lang::get('error.slave_player_not_found');
                    return Response::json($msg, 403);
               }
            }
        }
        //Log::info(var_export($player_id, true));
        $table = $this->initTable();
        $messages = $table->getData();
        $result = array();
        foreach($messages as $message){
            if(isset($message->id)){
                $result[$message->id] = array(
                        'desc' => $message->desc,
                        'name' => isset($message->name) ? $message->name : '',
                );
            }else{
                //Log::info('This line data:');
                //Log::info(var_export($message, true));
                //Log::info(var_export(' in '.$game->game_code.'/game_message.txt', true));
            }
        }
        // 个人统计
        if ($type2 == 0) {
            $response = $api->getPlayerEconomyStatistics($game->game_id, $server->server_internal_id, $player_id, $type, $start_time, $end_time, $look_type, $action_type_num);
            $body = $response->body;
            //Log::info("ge ren tong ji ======================>".var_export($response, true));
            if ($response->http_code == 200) {
                foreach ($body as $x => $y) {
                    $body[$x]->action_time = date('Y-m-d H:i:s', $y->action_time);
                    $action_type = $y->action_type;
                    $body[$x]->action_name= '';
                    if(isset($result[$action_type])){
                        $body[$x]->action_name = $result[$action_type]['name'];
                        $body[$x]->action_type = ('' != $result[$action_type]['desc']) ? $result[$action_type]['desc'] : $y->action_type;
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
        // 个人详细
        if ($type2 == 1) {
            $response = $api->getPlayerEconomy($game->game_id, $server->server_internal_id, $player_id, $type, $start_time, $end_time, $look_type, $action_type_num, $page, 30);
            $body = $response->body;
            //Log::info("ge ren xiang xi ======================>".var_export($response, true));
            if ($response->http_code == 200) {
                $items = $body->items;
                foreach ($items as $x => $y) {
                    $action_type = $y->action_type;
                    $items[$x]->left_number = $y->$type;
                    
                    $items[$x]->action_time = date('Y-m-d H:i:s', $y->action_time);
                    $items[$x]->action_name= '';
                    $items[$x]->action_type = $action_type;
                    if(isset($result[$action_type])){
                        $items[$x]->action_name = $result[$action_type]['name'];
                        if($result[$action_type]['desc']){
                            $items[$x]->action_type = ('' != $result[$action_type]['desc']) ? $result[$action_type]['desc'] : $y->action_type;;
                        }
                    }
                }
                return Response::json($body);
            } else {
                return Response::json($body, $response->http_code);
            }
        }

    }

    public function rankIndex()
    {
        $servers = Server::currentGameServers()->get();
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if(in_array($game_id, Config::get('game_config.mobilegames'))){
            $data = array(
                'content' => View::make('slaveapi.economy.syrank', array(
                    'servers' => $servers,
                    'game_code' => $game->game_code,
                ))
            );
        }else{
            $data = array(
                'content' => View::make('slaveapi.economy.rank', array(
                    'servers' => $servers,
                ))
            );
        }
        return View::make('main', $data);
    }

    public function sendRank()
    {
        $msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'error' => Lang::get('error.server_not_found')
        );
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server) {
            return Response::json($msg, 403);
        }
        $type = (int) Input::get('type');
        if ($type == 0) {
            $type = "yuanbao";
        } else if ($type == 1) {
            $type = "tongqian";
        } else if ($type == 2) {
            $type = "gongxun";
        } else if($type == 3){
            $type = "mana";
        } else if($type == 4){
            $type = "crystal";
        } else if($type == 5){
            $type = "energy";
        } else if($type == 6){
            $type = "arena_coin";
        } else if($type == 7){
            $type = "march_coin";
        }
        $game_id = (int)Session::get('game_id');
        $game = Game::find($game_id);
        
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if(in_array($game_id, Config::get('game_config.mobilegames'))){
            $start_time = strtotime(trim(Input::get('start_time')));
            $end_time = strtotime(trim(Input::get('end_time')));
            $response = $api->getPlayerEconomyRankWithTime($game->game_id, $server->server_internal_id, $type, $start_time, $end_time);
        }else{
            $response = $api->getPlayerEconomyRank($game->game_id, $server->server_internal_id, $type);
        }
        $body = $response->body;
        if ($response->http_code == 200) {
            return Response::json($body);
        } else {
            return Response::json($body, $response->http_code);
        }
    }
    public function analysisIndex()
    {
        $table = $this->initTable();
        $messages = $table->getData();
        $game_id = Session::get('game_id');
        $mids = array();
        foreach($messages as $message){
            if(in_array($game_id, Config::get('game_config.mobilegames'))){
                $mids[] = array(
                    'mid' => $message->id,
                    'desc' => $message->desc,
                );
            }else{
                if(1 == $message->is_filter){
                    $mids[] = array(
                        'mid' => $message->id,
                        'desc' => $message->desc,
                    ); 
                }
            }
        }  
        $servers = Server::currentGameServers()->get();
        $game = Game::find($game_id);
        $data = array(
                'content' => View::make('slaveapi.economy.analysis', array(
                        'servers' => $servers,
                        'game_code' => $game->game_code,
                        'mids' => $mids
                ))
        );
        return View::make('main', $data);
    }
    
    public function analysis()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'error' => Lang::get('error.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $lower_bound = (int) Input::get('lower_bound');
        $server_ids = Input::get('server_id');
        $type = Input::get('type');
        $game_id = Session::get('game_id');
        $action_type=(int) Input::get('action_type');
        $no_name = Input::get('no_name');
        if(in_array($game_id, Config::get('game_config.mobilegames'))){
            if ($type == 0) {
                $type = "crystal";
            } else if ($type == 1) {
                $type = "mana";
            }
        }else{
            if ($type == 0) {
                $type = "yuanbao";
            } else if ($type == 1) {
                $type = "tongqian";
            } else if ($type == 2) {
                $type = "shengwang";
            }
        }

        $game = Game::find(Session::get('game_id'));
        $data = array();
        $i =0;
        $platform_id = Session::get('platform_id');
        if((count($server_ids) == 1) && ($server_ids[0] != 0)){
            $server = Server::find($server_ids);
            if (!count($server)) {
                return Response::json($msg, 403);
            }
            $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            $response = $api->getPlayerEconomyAnalysis($platform_id,$game->game_id, $server[0]->server_internal_id, $type,$action_type,$start_time, $end_time, $lower_bound,$upper_bound = '',$no_name);
           /* Log::info("action_type: $action_type=============response:------=>".var_export($response, true));
           var_dump($response);die();*/
            if (isset($response->http_code) && $response->http_code == 200) {
                $body = $response->body;
                foreach ($body as $item){
                    $data[] = array(
                            'server_name'=>$server[0]->server_name,
                            'spend' =>$item->spend,
                            'player_id' =>$item->player_id,
                            'player_name' =>isset($item->player_name) ? $item->player_name :'',
                    );
                }
            }else {
                return $api->sendResponse();
            }
            
        }else{
            foreach ($server_ids as $server_id){
                $server = Server::find($server_id);
                if (! $server) {
                    return Response::json($msg, 403);
                }
                $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                $response = $api->getPlayerEconomyAnalysis($platform_id,$game->game_id, $server->server_internal_id, $type,$action_type,$start_time, $end_time, $lower_bound,$upper_bound = '', $no_name);
                if ($response->http_code == 200) {
                    $body = $response->body;
                }else {
                    continue;
                }
                foreach ($body as $item){
                    $data[] = array(
                            'server_name'=>$server->server_name,
                            'spend' =>$item->spend,
                            'player_id' =>$item->player_id,
                            'player_name' =>isset($item->player_name) ? $item->player_name :'',
                    );
                }
            }
        }
        if (!empty($data)) {
            return Response::json($data);
        } else {
            return Response::json(array('error' => '未查询到数据！'), 403);
        }
    }


    public function getAllServersConsumeIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.economy.consume', array(
                        'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }
    public function getAllServersConsume(){
        $servers = Server::currentGameServers()->get();
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $platform_id = Session::get('platform_id');
        $game = Game::find(Session::get('game_id'));
        $type = (int) Input::get('type');

        if ($type == 0) {
            $type = "yuanbao";
        } else if ($type == 1) {
            $type = "tongqian";
        } else if ($type == 2) {
            $type = "gongxun";
        }
        $spend = 0;
        $server_internal_id = 2;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        foreach ($servers as $server) {
            $response = $api->getAllServersConsume($game->game_id,$platform_id,$server_internal_id,$type,$start_time,$end_time);
            if($response->http_code==200){
                $spend += $response->body->spend;
            }
        }
        if ($response->http_code == 200) {
            $result = array(
                'res' => 'OK',
                'msg' => '所有服玩家消费总计为' . $spend
                );
            return Response::json($result);
        } else {
            return Response::json($response, $response->http_code);
        }

    }
    public function findBossKillerIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.economy.find-boss-killer', array(
                        'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }
    
    public function findBossKiller()
    {
        
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'error' => Lang::get('error.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $start_time = strtotime(date("Y-m-d 12:00:00", $start_time));
        $end_time = strtotime(date("Y-m-d 12:00:00", $end_time));

        $server_ids = Input::get('server_id');
        $game = Game::find(Session::get('game_id'));
        $data = array();
        $i =0;
        foreach ($server_ids as $server_id){
            $server = Server::find($server_id);
            if (! $server) {
                return Response::json($msg, 403);
            }
            $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            $response = $api->findBossKiller($game->game_id, $server->server_internal_id, $start_time, $end_time);
            if ($response->http_code == 200) {
                $body = $response->body;
            }else {
                continue;
            }
            foreach ($body as $item){
                $action_time_array = explode(";", $item->action_time);
                $action_time = "";
                foreach ($action_time_array as $v){
                    $action_time .= date("Y-m-d H:i:s", (int)$v) . " ; ";
                }
                $data[] = array(
                        'server_name'=>$server->server_name,
                        'times' =>$item->times,
                        'player_id' =>$item->player_id,
                        'player_name' =>$item->player_name,
                        'action_time' =>$action_time,
                );
            }
        }
        $data = array('data'=>(object)$data);
        
        return Response::json($data);
    }

    //查询世界Boss玩家个数
    public function findBossKillerNumIndex()
    {
        //$server = Server::currentGameServers()->get();
        $server = $this->getUnionServers();
        $data = array(
            'content' => View::make('slaveapi.economy.find-boss-killer-num', array('server' => $server)),
        );
        return View::make('main', $data);
    }


    public function findBossKillerNumData()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.server_not_found'),
        );
        $rules = array(
            'start_time' => 'required',
            'end_time' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $server_ids = Input::get('server_id');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $data = array();
        if($server_ids[0] == 0){
            $servers=$this->getUnionServers();
            $len = count($servers);
            for($i=0; $i<$len; $i++) {
                $server[$i] = Server::find($servers[$i]->server_id);
                if (!$server[$i]) {
                    return Response::json($msg, 403);
                }
                $response[$i] = $api->findBossKillerNum($game->game_id, $server[$i]->server_internal_id , $start_time, $end_time);
                if ($response[$i]->http_code == 200) {
                    $body[$i] = $response[$i]->body;
                    $data[$i]['num'] = $body[$i][0]->num;
                    $data[$i]['action_time'] = date("Y-m-d H:i:s", $body[$i][0]->action_time);
                    $data[$i]['server_name'] = $server[$i]->server_name;
                    $data[$i] = (object)$data[$i];

                } else {
                    continue;
                }
            }
        }else{
            $len = count($server_ids);
            for ($i=0; $i < $len; $i++) { 
                $server[$i] = Server::find($server_ids[$i]);
                if (!$server[$i]) {
                    return Response::json($msg, 403);
                }
                $response[$i] = $api->findBossKillerNum($game->game_id, $server[$i]->server_internal_id , $start_time, $end_time);
                if ($response[$i]->http_code == 200) {
                    $body[$i] = $response[$i]->body;
                    $data[$i]['num'] = $body[$i][0]->num;
                    $data[$i]['action_time'] = date("Y-m-d H:i:s", $body[$i][0]->action_time);
                    $data[$i]['server_name'] = $server[$i]->server_name;
                    $data[$i] = (object)$data[$i];

                } else {
                    continue;
                }
            }
        }
        
        if (!empty($data)) {
            return Response::json($data);
        } else {
            return Response::json($body, $response->http_code);
        }
        
    }

    public function findRankThreeIndex(){
        //$server = Server::currentGameServers()->get();
        $server = $this->getUnionServers();
        $data = array(
            'content' => View::make('slaveapi.economy.rankthree',array('servers' => $server))
            );
        return View::make('main',$data);
    }
    public function findRankThree(){
        $msg = array(
            'error' => Lang::get('basic.input_error')
            );
        $server_ids = Input::get('server_id');
        $game = Game::find(Session::get('game_id'));
        $result = array();
        foreach($server_ids as $server_id){
            $server = Server::find($server_id);
            if(!$server){
                continue;
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id); 
            $response = $api->getYWRank();
            if(isset($response->fighters)){
                foreach ($response->fighters as $f) {
                    $result []= array(
                        'server_name' => $server->server_name,
                        'player_id' => $f->player_id,
                        'player_name' => $f->name,
                        'rank' => $f->rank,
                    ); 
                }
            }
        }
        if(!empty($result)){
            return Response::json($result);
        }else{
           $msg['error'] = Lang::get('error.slave_result_none');
           return Response::json($msg, 404); 
        }
        

    }
    public function yysgPlayerIndex()
    {   
        $server_init = (int)Input::get('server_init');
        $player_id = (int)Input::get('player_id');
        $servers = Server::currentGameServers()->get();
        $game = Game::find(Session::get('game_id'));
        $data = array(
            'content' => View::make('slaveapi.economy.yysg_player', array(
                'servers' => $servers,
                'game_code' => $game->game_code,
                'server_init' => $server_init,
                'player_id' => $player_id,
            ))
        );
        return View::make('main', $data);
    }

    public function yysgSendPlayer()
    {
        $msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'error' => Lang::get('error.server_not_found')
        );
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server) {
            return Response::json($msg, 403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $type1 = (int) Input::get('type1');
        $type2 = (int) Input::get('type2');
        $look_type = Input::get('look_type');
        $number2key = array(
            0 => 'mana',
            1 => 'crystal',
            2 => 'social',
            3 => 'energy',
            4 => 'invitation',
            5 => 'glory',
            6 => 'point',
            7 => 'arena_coin',
            8 => 'march_coin',
            9 => 'top_coin',
            10 => 'guild_coin',
            11 => 'region_coin',
            );
        if(isset($number2key[$type1])){
            $type = $number2key[$type1];
        }else{
            return Response::json(array('error'=>'No such type!'), 403);
        }
        $game = Game::find(Session::get('game_id'));
        $player_name = Input::get('player_name');
        $player_id = Input::get('player_id');
        $action_type_num = Input::get('action_type_num');
        if(!$player_name && !$player_id && !$action_type_num){
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }elseif(1 == $type2 && !$player_id && !$player_name){
            return Response::json(array('error'=>'Please enter playerID or playerName!'), 403);
        }
        $page = (int) Input::get('page');
        $page = $page > 0 ? $page : 1;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if($player_name && !$player_id){
            $response = $api->getCreatePlayerInfo($uid = '', $player_id = '', $player_name, $game->game_id, $server->server_internal_id);
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
        foreach($messages as $message){
            if(isset($message->id)){
                $result[$message->id] = array(
                    'desc' => $message->desc,
                );
            }else{
                //Log::info('This line data:');
                //Log::info(var_export($message, true));
                //Log::info(var_export(' in '.$game->game_code.'/game_message.txt', true));
            }
        }
        // 个人统计
        if ($type2 == 0) {
            $response = $api->getYysgPlayerEconomyStatistics($game->game_id, $server->server_internal_id, $player_id, $type, $start_time, $end_time, $look_type, $action_type_num);
            $body = $response->body;
            if ($response->http_code == 200) {
                foreach ($body as $x => $y) {
                    $body[$x]->created_at = $y->times;
                    $action_type = $y->mid;
                    $body[$x]->action_type = $action_type;
                    if(isset($result[$action_type])){
                        if($result[$action_type]['desc']){
                            $body[$x]->action_type = $result[$action_type]['desc'];
                        }
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
        // 个人详细
        if ($type2 == 1) {
            $response = $api->getYysgPlayerEconomy($game->game_id, $server->server_internal_id, $player_id, $type, $start_time, $end_time, $look_type, $action_type_num);
            $items = $response->body;
            if ($response->http_code == 200) {
                foreach ($items as $x => $y) {
                    $action_type = $y->mid;
                    $items[$x]->left_number = isset($y->$type) ? $y->$type : '-';
                    $items[$x]->created_at = date('Y-m-d H:i:s', $y->created_at);
                    $items[$x]->action_type = $action_type;
                    if(isset($result[$action_type])){
                        if($result[$action_type]['desc']){
                            $items[$x]->action_type = $result[$action_type]['desc'];
                        }
                    }
                }
                $data = array();
                $data['items'] = $items;
                $data['current_page'] = 1;
                $data['per_page'] = count($items);
                $data['count'] = count($items);
                $body = (object) $data;
                return Response::json($body);
            } else {
                return Response::json($items, $response->http_code);
            }
        }

    }
    public function abnormalIndex(){
        $servers = Server::currentGameServers()->get();
        $game = Game::find(Session::get('game_id'));
        $data = array(
            'content' => View::make('slaveapi.economy.abnormal',array(
                'servers' => $servers,
                'game_code' => $game->game_code
            ))
        );
        return View::make('main', $data);

    }

    public function abnormalDada(){
        $msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'error' => Lang::get('error.server_not_found')
        );
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if(!$server){
            return Response::json($msg,403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $type = (int) Input::get('type');
        switch ($type) {
            case 0:
                $type = "yuanbao";
                break;
            case 1:
                $type = "tongqian";
                break;
            case 2:
                $type = "shengwang";
                break;
            case 3:
                $type = 'tili';
                break;
            case 4:
                $type = 'jingjiedian';
                break;
            case 5:
                $type = 'yueli';
                break;
            case 6:
                $type = 'xianling';
                break;
            case 7:
                $type = 'boat_book';
                break;
            case 8 :
                 $type = "lingshi";
                 break;
            case 9:
                $type = 'star_fragment';
                break;
            case 10:
                $type = 'talent_point';
                break;
            case 11:
                $type = 'heaven_token';
                break;
            case 12:
                $type = 'skill_fragment';
                break;
            case 13:
                $type = 'fight_spirit';
                break;
            case 14:
                $type = 'rings_exp';
                break;
            default:
                break;
        }
        $game = Game::find(Session::get('game_id'));
        $min_limit = (int)Input::get('min_limit');
        $msg = array(
            'error' => Lang::get('basic.input_error')
        );
        if(0 == $min_limit){
            return Response::json($msg,403);
        }
        $table = $this->initTable();
        $messages = $table->getData();
        $result = array();
        foreach($messages as $message){
            if(isset($message->id)){
                $result[$message->id] = array(
                        'desc' => $message->desc,
                        'name' => $message->name,
                );
            }else{
                //Log::info('This line data:');
                //Log::info(var_export($message, true));
                //Log::info(var_export(' in '.$game->game_code.'/game_message.txt', true));
            }
        }

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->getAbnormalDada($game->game_id, $server->server_internal_id, $type, $start_time, $end_time, $min_limit);
        $body = $response->body;
        if($response->http_code == 200){
            foreach ($body as $k => $v) {
                $body[$k]->first_time = date('Y-m-d H:i:s', $v->first_time);
                $body[$k]->last_time = date('Y-m-d H:i:s', $v->last_time);
                $action_type = $v->action_type;
                    if(isset($result[$action_type])){
                        $body[$k]->action_type = $result[$action_type]['desc'];
                    }

            }
            return Response::json($body);
        }else {
            return Response::json($body, $response->http_code);
        }

    }

    public function SpendonPartsIndex(){    //查询游戏时间段内消耗元宝或铜钱在各个活动上的比例
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('slaveapi.economy.spendonparts',array(
                'servers' => $servers,
            ))
        );
        return View::make('main', $data);
    }

    public function SpendonPartsData(){
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $type = Input::get('type');
        $symbol = Input::get('symbol');
        $check_type = Input::get('check_type');

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        if(!$game){
            return Response::json(array('error'=>"invalued game"), 403);
        }

        $response = array();

        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        if(0 == $check_type){   //查询单服各项操作的变动
            $server_id = Input::get('server_id');

            if(0 == $server_id){
                return Response::json(array('error'=>"please select a server"), 403);
            }

            $server = Server::find($server_id);
            if($server){
                $server_internal_id = $server->server_internal_id;
            }else{
                return Response::json(array('error'=>"invalued server"), 403);
            }

            $result = $slaveapi->getSpendonParts($game_id, $server_internal_id, $start_time, $end_time, $type, $symbol);

            $type2midyysg = array(
                'yuanbao'   =>  36,
                'tongqian'  =>  33,
                );

            if('404' == $result->http_code){
                return Response::json(array('error'=>"查询无结果"), 404);
            }elseif('200' != $result->http_code){
                return $slaveapi->sendResponse();
            }

            $sum_action = $result->body;
            unset($result);
            $mid2actionname = array();
            $table = $this->initTable();
            $table = $table->getData();
            foreach ($table as $value) {
                $mid2actionname[$value->id] = $value->desc;
            }
            unset($table);

            if(in_array($game_id, Config::get('game_config.yysggameids'))){
                $result_shop = $slaveapi->getSpendonShops($game_id, $server_internal_id, $start_time, $end_time, $type, $type2midyysg[$type], $symbol);
                if('200' == $result_shop->http_code){
                    $data_shop = $result_shop->body;
                    $allsumvalue = 0;
                    $tmp_result = array();
                    foreach ($sum_action as &$value) {
                        if($value->actionvalue == $type2midyysg[$type]){    //商店购买要区分单价
                            continue;
                        }
                        $allsumvalue += $value->sumvalue;
                        $tmp_result[] = array(
                            'player_num' => $value->player_num,
                            'times' => $value->times,
                            'sumvalue'  =>  $value->sumvalue,
                            'actionvalue'   =>  isset($mid2actionname[(int)$value->actionvalue]) ? $mid2actionname[$value->actionvalue] : $value->actionvalue,
                            );
                    }
                    unset($sum_action);
                    foreach ($data_shop as $value) {
                        if($value->actionvalue == $type2midyysg[$type]){
                            $allsumvalue += $value->sumvalue;
                            $tmp_result[] = array(
                                'player_num' => $value->player_num,
                                'times' => $value->times,
                                'sumvalue'  =>  $value->sumvalue,
                                'actionvalue'   =>  isset($mid2actionname[(int)$value->actionvalue]) ? $mid2actionname[$value->actionvalue] : $value->actionvalue,
                                'singlepirce'   =>  $value->singlepirce,
                                );
                        }
                    }
                    unset($mid2actionname);
                    unset($data_shop);
                    foreach ($tmp_result as &$value) {
                        $value['rate']  = round($value['sumvalue']/$allsumvalue*100, 2).'%';
                    }
                    $result = array(
                        'result' => $tmp_result,
                        'sum'   =>  $allsumvalue,
                        );
                }else{
                    $allsumvalue = 0;
                    foreach ($sum_action as &$value) {
                        $allsumvalue += $value->sumvalue;
                        $value->actionvalue = isset($mid2actionname[$value->actionvalue]) ? $mid2actionname[$value->actionvalue] : $value->actionvalue;
                    }
                    unset($mid2actionname);
                    foreach ($sum_action as &$value) {
                        $value->rate = round($value->sumvalue/$allsumvalue*100, 2).'%';
                    }
                    $result = array(
                        'result' => $sum_action,
                        'sum'   =>  $allsumvalue,
                        );
                }
            }else{
                $allsumvalue = 0;
                foreach ($sum_action as &$value) {
                    $allsumvalue += $value->sumvalue;
                    $value->actionvalue = isset($mid2actionname[$value->actionvalue]) ? $mid2actionname[$value->actionvalue] : $value->actionvalue;
                }
                unset($mid2actionname);
                foreach ($sum_action as &$value) {
                    $value->rate = round($value->sumvalue/$allsumvalue*100, 2).'%';
                }
                $result = array(
                    'result' => $sum_action,
                    'sum'   =>  $allsumvalue,
                    );
            }

            $response['parts'] = $result;
        }

        if(1 == $check_type){   //查询全服各服的变动总量
            $server_ids = Input::get('server_id');  //可多选
            if(!count($server_ids)){
                return Response::json(array('error'=>"please select at least one server"), 403);
            }
            if(is_numeric($server_ids)){
                $server_ids = array($server_ids);
            }

            $server_infos = array();
            foreach ($server_ids as $server_id) {
                $server = Server::find($server_id);
                if($server){
                    $server_internal_id = $server->server_internal_id;
                }else{
                    return Response::json(array('error'=>"invalued server"), 403);
                }
                $server_result = $slaveapi->getWholeServerEconomyChange($game_id, $server_internal_id, $start_time, $end_time, $type, $symbol);
                if(200 == $server_result->http_code){
                    $server_infos[] = array(
                        'server_name' => $server->server_name,
                        'change' => $server_result->body->sumvalue,
                        );
                }else{
                     $server_infos[] = array(
                        'server_name' => $server->server_name,
                        'change' => 'No Result',
                        );
                }
            }
            $response['each_server'] = $server_infos;
        }

        if(2 == $check_type){   //查询单服所有玩家的变动情况
            $server_id = Input::get('server_id');

            if(0 == $server_id){
                return Response::json(array('error'=>"please select a server"), 403);
            }

            $server = Server::find($server_id);
            if($server){
                $server_internal_id = $server->server_internal_id;
            }else{
                return Response::json(array('error'=>"invalued server"), 403);
            }
            $limit_value = (int)Input::get('limit_value');
            $limit_symbol = Input::get('limit_symbol');
            $each_player_result = $slaveapi->getEachPlayerEconomyChange($game_id, $server_internal_id, $start_time, $end_time, $type, $symbol, $limit_symbol, $limit_value);
            if(200 == $each_player_result->http_code){
                $response['each_player'] = $each_player_result->body;
            }else{
                $response['each_player'] = array();
            }
        }
        
        return Response::json($response);
    }

    public function RemainYuanbaoIndex(){   //根据经济日志查询服务器剩余元宝
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('slaveapi.economy.remainyuanbao',array(
                'servers' => $servers,
            ))
        );
        return View::make('main', $data);       
    }

    public function RemainYuanbaoData(){
        $server_id = Input::get('server_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        //接收要查询的类型：元宝、铜钱、体力
        $type = Input::get('type');
        $min_level = Input::get('min_level');
        $max_level = Input::get('max_level');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        //判断是否需要进行等级升级时间限制
        $upgrade_time = Input::get('upgrade_time');
        $upgrade_start_time = strtotime(trim(Input::get('upgrade_start_time')));
        $upgrade_end_time = strtotime(trim(Input::get('upgrade_end_time')));
        //判断是否需要进行注册时间限制
        $by_reg_time = Input::get('by_reg_time');
        $created_start_time = strtotime(trim(Input::get('created_start_time')));
        $created_end_time = strtotime(trim(Input::get('created_end_time')));
        if (empty($max_level) && empty($min_level) && $upgrade_time == 1 ) {
            return Response::json(array('error'=>"请输入等级"), 403);
        }
        if('0' == $server_id){
            return Response::json(array('error'=>"请选择一个服务器"), 403);
        }
        if(!$game){
            return Response::json(array('error'=>"invalued game"), 403);
        }
        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $result = array();
        $result['total'] = array(
            'server_name' => 'total' ,
            'desc_name' =>  '总计',
            'player_num'    =>  0,
            'value' =>  0,
            );
        $id2desc_name = array(
            '0' =>  '0-100',
            '100' =>  '100-300',
            '300' =>  '300-500',
            '500' =>  '500-1000',
            '1000' =>  '1000-5000',
            '5000' =>  '5000-10000',
            '10000' =>  '10000以上',
            );
        $server = Server::find($server_id);
        unset($server_id);
        $params = array(
            'server_internal_id' => $server->server_internal_id, 
            'game_id' => $game_id,
            'type'  =>  $type,
            'start_time'    =>  $start_time,
            'end_time'  =>  $end_time,
            'min_level'  =>  $min_level,
            'max_level'  =>  $max_level,
            'by_reg_time' => $by_reg_time,
            'created_start_time'  =>  $created_start_time,
            'created_end_time'  =>  $created_end_time,
            'upgrade_time' => $upgrade_time,
            'upgrade_start_time' => $upgrade_start_time,
            'upgrade_end_time' => $upgrade_end_time
        );
        $tmp_result = $slaveapi->getRemainYuanbao($params);
        if('200' != $tmp_result->http_code){
            return $slaveapi->sendResponse();
        }
        $tmp_result = $tmp_result->body;
        foreach ($tmp_result as $value) {
            $result[] = array(
                'server_name'   =>  $server->server_track_name,
                'value' =>  $value->remainyuanbao,
                'player_num'    =>  $value->player_num,
                'desc_name' =>  $id2desc_name[$value->desc_name],
                );
            $result['total']['value'] += $value->remainyuanbao;
            $result['total']['player_num'] += $value->player_num;
        }

        if($result['total']['value'] > 0){
            return Response::json($result);
        }else{
            return Response::json(array('error'=>"查询无结果"), 403);
        }
    }

    public function ActivityAnalysisIndex(){  //活动参与分析
        $servers = $this->getUnionServers();
        $activities = array();
        $file_names = array('activities', 'activity', 'turnplate_activities');
        foreach ($file_names as $file_name) {
            $table = $this->initTableByName($file_name);
            $table = $table->getData();
            foreach ($table as $value) {
                if(isset($value->mid) && '' != $value->mid){
                    $activities[] = (isset($value->id) ? $value->id : $value->value).':'. $value->name;
                }
            }
            unset($table);
        }
        $data = array(
            'content' => View::make('slaveapi.economy.activityanalysis',array(
                'servers' => $servers,
                'activities'    =>  $activities,
            ))
        );
        return View::make('main', $data);           
    }

    public function ActivityAnalysis(){
        $game_id = Session::get('game_id');
        $activity_idnames = Input::get('activity_idnames');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));

        $server_ids = Input::get('server_ids');

        $game = Game::find($game_id);
        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        if('0' == $server_ids[0]){  //全服
            $allservers = $this->getUnionServers();
            $server_internal_ids = array();
            foreach ($allservers as $key => $value) {
                $server_internal_ids[] = $value->server_internal_id;
                unset($value);
            }
        }else{
            $server_internal_ids = array();
            foreach ($server_ids as $server_id) {
                $server_internal_ids[] = Server::find($server_id)->server_internal_id;
            }
        }

        $activities = array();
        $file_names = array('activities', 'activity', 'turnplate_activities');
        foreach ($file_names as $file_name) {
            $table = $this->initTableByName($file_name);
            $table = $table->getData();
            foreach ($table as $value) {
                if(isset($value->mid) && '' != $value->mid){    //得到所有活动的名字和对应的mids
                    $activities[(isset($value->id) ? $value->id : $value->value).':'. $value->name] = explode(',', $value->mid);
                }
            }
            unset($table);
        }

        $table = $this->initTableByName('game_message');
        $table = $table->getData();
        $mid2name = array();
        foreach ($table as $value) {
            $mid2name[$value->id] = isset($value->desc) ? $value->desc : $value->id;
        }

        if(in_array('all', $activity_idnames)){ //剔除没有选择的活动
        }else{
            foreach ($activities as $key => $value) {
                if(in_array($key,$activity_idnames)){

                }else{
                    unset($activities[$key]);
                }
            }
        }
        $result = array();

        foreach ($activities as $id => $mids) { //活动id和对应的mid号
            $result[$id] = array();
            foreach ($server_internal_ids as $server_internal_id) {
                $tmp_result = $slaveapi->getActivityAnalysis($game_id, $server_internal_id, $start_time, $end_time, $mids, $type='parts');
                if('200' == $tmp_result->http_code){
                    foreach ($tmp_result->body as $value) {
                        if(isset($result[$id][$value->action_type])){
                            $result[$id][$value->action_type]['player_num'] += $value->player_num;
                            $result[$id][$value->action_type]['times'] += $value->times;
                            $result[$id][$value->action_type]['diff_yuanbao'] += $value->diff_yuanbao;
                            $result[$id][$value->action_type]['diff_tongqian'] += $value->diff_tongqian;
                        }else{
                            $result[$id][$value->action_type] = array(
                                'activity_name' =>  $id,
                                'action_type' => isset($mid2name[$value->action_type]) ? $mid2name[$value->action_type] : $value->action_type,
                                'player_num'    =>  $value->player_num,
                                'times' =>  $value->times,
                                'diff_yuanbao'  =>  $value->diff_yuanbao,
                                'diff_tongqian' =>  $value->diff_tongqian,
                                );
                        }
                    }
                }
                unset($tmp_result);
                $tmp_result = $slaveapi->getActivityAnalysis($game_id, $server_internal_id, $start_time, $end_time, $mids, $type='all');
                if('200' == $tmp_result->http_code){
                    foreach ($tmp_result->body as $value) {
                        if(isset($result[$id]['all'])){
                            $result[$id]['all']['player_num'] += $value->player_num;
                            $result[$id]['all']['times'] += $value->times;
                            $result[$id]['all']['diff_yuanbao'] += $value->diff_yuanbao;
                            $result[$id]['all']['diff_tongqian'] += $value->diff_tongqian;
                        }else{
                            $result[$id]['all'] = array(
                                'activity_name' =>  $id,
                                'action_type' => '总计',
                                'player_num'    =>  $value->player_num,
                                'times' =>  $value->times,
                                'diff_yuanbao'  =>  $value->diff_yuanbao,
                                'diff_tongqian' =>  $value->diff_tongqian,
                                );
                        }
                    }
                }
                unset($tmp_result);
            }
            if(isset($result[$id]['all']['player_num']) &&'0' == $result[$id]['all']['player_num']){   //若某个活动并没有数据，那么将不显示这个活动的信息
                unset($result[$id]);
            }
            if(empty($result[$id])){
                unset($result[$id]);
            }
        }
        if(count($result)){
            return Response::json($result);
        }else{
            return Response::json(array('error'=>"查询无结果"), 404);
        }
    }
}