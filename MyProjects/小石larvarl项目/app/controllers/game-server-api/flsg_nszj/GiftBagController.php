<?php

class GiftBagController extends \BaseController{

    public function index()
    {
        $servers = Server::currentGameServers()->get();
    
        $table = $this->initTable();

        $gifts = $table->getData();
        $temp = array();
        foreach ($gifts as $k => $v) {
            if (($v->type1 == 4 && $v->type2 == 1) || ($v->type1 == 2 && $v->type2 == 4) || ($v->type1 == 14 && $v->type2 == 1)) {
                $temp[] = $v;
            }
        }
        
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.giftbag.index', array(
                'servers' => $servers,
                'gifts' => (object) $temp
            ))
        );
        return View::make('main', $data);
    }
    
    private function initTable()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if(in_array($game_id, $this->area_item_id)){
           $table = Table::init(public_path() . '/table/' . $game->game_code . '/item'.$game_id.'.txt'); 
        }else{
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/item.txt');
        }
        return $table;
    }

    private function initTable2()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/gift.txt');//allserver_giftbag.txt
        return $table;
    }

    private function initTable3()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'flsg' . '/server.txt');
        return $table;
    }

    public function send()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'server_id' => 'required|numeric|min:1',
            'gift_bag_id' => 'required|numeric|min:1'
        ); 
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        $game_id = Session::get('game_id');
        $operator = Auth::user()->username;
        if (! $server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
        $gift_bag_id = (int) Input::get('gift_bag_id');
        $password = trim(Input::get('password'));
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        if ($password) { // 对当前服全服发放，需要填写密码
            if (md5($password) == '861d16cc9357375af69e87c522766e97') { // 验证密码再操作.“woshixiaoxin”
                $response = $api->sendGiftBagToPlayers($gift_bag_id);

                $extra_msg = '单服发送礼包全服在线礼包';
                if (isset($response->result) && $response->result == 'OK') {
                    $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'success',$server, $extra_msg, 'single_server_gift');
                }else{
                    $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'fail',$server, $extra_msg, 'single_server_gift');
                }

                return $api->sendResponse();
            } else {
                App::abort(403, 'Method:send. The password does not match.');
            }
        }
        // 当前服的单个发放或者批量发放
        $player_name = Input::get('player_name');
        $player_id = (int) Input::get('player_id');
        $player_id_or_names = Input::get('player_id_or_names');
        $player_ids = array();
        $player_names = array();
        $result = array();

        $fail_player_name = array();
        $fail_player_id = array();
        $success_player_id = array();
        $success_player_name = array();
        if ($player_name) { // 单个玩家名字
            $send_type = 'name';
            $player = $api->getPlayerInfoByName($player_name);
            if (! isset($player->player_id)) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 404);
            }
            $player_ids = array(
                $player->player_id
            );
            $player_names[$player->player_id] = $player_name;
        } else if ($player_id) { // 单个玩家ID
            $send_type = 'id';
            $player_ids = array(
                $player_id
            );
        } else if ($player_id_or_names) { // 批量玩家
            $players = explode("\n", $player_id_or_names);
            $players = array_unique($players);
            $send_type = Input::get('send_type');
            $player_ids = array_map(function ($v) use ($api, $send_type, &$player_names, &$result) {
                $v = trim($v);
                if ((int) $v > 0 && $send_type == 'id') {
                    if ($v == 0) {
                        $result[] = array(
                            'msg' => $v,
                            'status' => 'error'
                        );
                    }
                    return (int) $v;

                } else if ($send_type == 'name') {
                    $player = $api->getPlayerInfoByName($v);
                    if (isset($player->player_id)) {
                        $player_names[$player->player_id] = $v;
                        return (int) $player->player_id;
                    } else {
                        $result[] = array(
                            'msg' => $v,
                            'status' => 'error'
                        );
                        return 0;
                    }
                }
            }, $players);
        }
        foreach ($player_ids as $v) {
            if ($v == 0) {
                continue;
            }
            $response = $api->sendGiftBagToPlayers($gift_bag_id, array(
                $v
            ));
            if (isset($response->result) && $response->result == 'OK') {
                $player_name = isset($player_names[$v]) ? $player_names[$v] : '';
                $result[] = array(
                    'msg' => $player_name . "({$v})",
                    'status' => 'ok'    
                );

                if('id' == $send_type){
                    $success_player_id[] = $v;
                }elseif('name' == $send_type){
                    $success_player_name[$v] = $player_name;
                }
            } else {
                $player_name = isset($player_names[$v]) ? $player_names[$v] : '';
                $result[] = array(
                    'msg' => $player_name . "({$v})",
                    'status' => 'error' 
                );

                if('id' == $send_type){
                    $fail_player_id[] = $v;
                }elseif('name' == $send_type){
                    $fail_player_name[$v] = $player_name;
                }
            }
        }

        if('name' == $send_type){
            if(!empty($fail_player_name)){
                $this->insert_gift_msg_name($fail_player_name, $gift_bag_id, $game_id, $operator, 'fail','single_server_gift', $server, $api);
            }
            if(!empty($success_player_name)){
                $this->insert_gift_msg_name($success_player_name, $gift_bag_id, $game_id, $operator, 'success','single_server_gift', $server, $api);
            }
        }elseif ('id' == $send_type) {
            if(!empty($fail_player_id)){
                $this->insert_gift_msg_id($fail_player_id, $gift_bag_id, $game_id, $operator, 'fail', 'single_server_gift', $server, $api);
            }
            if(!empty($success_player_id)){
                $this->insert_gift_msg_id($success_player_id, $gift_bag_id, $game_id, $operator, 'success', 'single_server_gift', $server, $api);
            }
        }

        $ok = array();
        $error = array();
        foreach ($result as $v) {
            if ($v['status'] == 'ok') {
                $ok[] = $v['msg']; 
            } else {
                $error[] = $v['msg'];
            }
        }
        $res = array();
        if (!empty($ok)) {
            $res[] = array(
                    'msg' => implode(', ', $ok),
                    'status' => 'ok'
            );
            
        }
        if (!empty($error)) {
            $res[] = array(
                'msg' => implode(', ', $error),
                'status' => 'error'
            );
        }

        return Response::json(array(
            'result' => $res
        ));
    } 

    public function allServerGiftBagIndex()
    {
        $servers = Server::currentGameServers()->where('is_server_on', '=', 1)->get();

        $table = $this->initTable2();
        
        $gifts = $table->getData();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.giftbag.allservergiftbag', array(
                'servers' => $servers,
                'gifts' => $gifts,
            ))
        );
        return View::make('main', $data);
    }

    public function allServerGiftBagData()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'gift_bag_id' => 'required',
            'remark' => 'required',
            'days' => 'required'
        );
        $gift_bag_id = (int) Input::get('gift_bag_id');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $server_ids = Input::get('server_id');
        $remark = Input::get('remark');
        $gift_bag_id = (int) Input::get('gift_bag_id');
        $days = (int) Input::get('days');
        $game_id = Session::get('game_id');
        $operator = Auth::user()->username;

        $extra_msg = 'days:' . $days . '|remark:' . $remark;
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if (! $server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 404);
            }
            
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->createGiftBagForAllServer($gift_bag_id, $days, $gift_bag_id);
            
            if(isset($response->result) && $response->result == 'OK'){
                $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'success',$server, $extra_msg, 'all_server_gift');
            }else{
                $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'fail',$server, $extra_msg, 'all_server_gift');
            }
        }
        return $api->sendResponse();
    }

    public function allServerIndex()
    {
        $servers = Server::currentGameServers()->get();
        
        $table = $this->initTable();
        
        $gifts = $table->getData();
        $temp = array();
        if(!empty($gifts)){
            foreach ($gifts as $k => $v) {
                if (isset($v->type1) && $v->type1 == 4 && isset($v->type2) && $v->type2 == 1) {
                    $temp[] = $v->name . ':' . $v->id;
                }
            }            
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.giftbag.allserver', array(
                'servers' => $servers,
                'gifts' =>  $temp
            ))
        );
        return View::make('main', $data);
    }

    public function sendAllServer()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'gift_bag_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $gift_bag = Input::get('gift_bag_id');
        $gift_bag = explode(":", $gift_bag);
        $gift_bag_id = (int)$gift_bag[1];
        $send_type = Input::get('send_type');
        $gift_datas = Input::get('gift_data');
        $gift_datas = explode("\n", $gift_datas);
        $game_id = Session::get('game_id');
        $operator = Auth::user()->username;
        foreach ($gift_datas as &$v) {
            $v = trim($v);
        }
        unset($v);
        $gift_datas = array_unique($gift_datas);
        $result = array(); 
        $ok = array();
        $error = array();
        $fail_player_name = array();
        $fail_player_id = array();
        $success_player_id = array();
        $success_player_name = array();
        foreach ($gift_datas as $gift_data) {
            $gift_data = explode("\t", $gift_data, 2);
            if (count($gift_data) != 2) {
                $error[] = $gift_data[0] . ': No Server Name. ';
                continue;
            }

            $server_name = trim($gift_data[1]);
            $server = Server::currentGameServers()->where('server_track_name', $server_name)->first();
            if (! $server) {
                $error[] = $gift_data[0] . "({$gift_data[1]}) Server Not Found. ";
                continue;
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $player_name = '';
            $player_id = 0;
            $player_ids = array();
            if ($send_type == 'name') {
                $player_name = trim($gift_data[0]);
                $player = $api->getPlayerInfoByName($player_name);
                if (isset($player->player_id)) {
                    $player_id = $player->player_id;
                    $player_ids = array(
                        $player_id
                    );
                } else {
                    $error[] = $gift_data[0] . ': Player Not Found. ';
                    continue;
                }
            } else if ($send_type == 'id') {
                $player_id = (int) $gift_data[0];
                $player_ids = array(
                    $player_id
                );
            } else {
                return Response::json($msg, 403);
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->sendGiftBagToPlayers($gift_bag_id, $player_ids);
            if (isset($response->result) && $response->result == 'OK') {
                $ok[] = $player_name . ' ' . $player_id . "({$gift_data[1]}) OK. ";

                if('name' == $send_type){
                    $success_player_name[$player_ids[0]] = $player_name;
                }elseif ('id' == $send_type) {
                    $success_player_id[] = $player_ids[0];
                }
            } else {
                $error[] = $player_name . ' ' . $player_id . "({$gift_data[1]}) Error";

                if('name' == $send_type){
                    $fail_player_name[$player_ids[0]] = $player_name;
                }elseif ('id' == $send_type) {
                    $fail_player_id[] = $player_ids[0];
                }
            }

            if('name' == $send_type){
                if(!empty($fail_player_name)){
                    $this->insert_gift_msg_name($fail_player_name, $gift_bag_id, $game_id, $operator, 'fail', 'batch_gift', $server, $api);
                    unset($fail_player_name);
                }
                if (!empty($success_player_name)) {
                    $this->insert_gift_msg_name($success_player_name, $gift_bag_id, $game_id, $operator, 'success', 'batch_gift', $server, $api);
                    unset($success_player_name);
                }
            }elseif ('id' == $send_type) {
               if (!empty($fail_player_id)) {
                   $this->insert_gift_msg_id($fail_player_id, $gift_bag_id, $game_id, $operator, 'fail', 'batch_gift', $server, $api);
                    unset($fail_player_id);
               }
               if(!empty($success_player_id)){
                   $this->insert_gift_msg_id($success_player_id, $gift_bag_id, $game_id, $operator, 'success', 'batch_gift', $server, $api);
                    unset($success_player_id);
               }
            }
        }
        
        if (!empty($ok)) {
            $result[] = array(
                'msg' => implode(',', $ok),
                'status' => 'ok',
            );  
        }
        if (!empty($error)) {
            $result[] = array(
                'msg' => implode(',', $error),
                'status' => 'error',
            );
        }
        $res = array(
            'result' => $result,
        );
        return Response::json($res);
    }

    public function insert_gift_msg_name($players, $gift_bag_id, $game_id, $operator, $status, $operation_type, $server, $api){
        foreach ($players as $id => $name) {
            $operation = Operation::insert(array(
                                'operate_time' => time(),
                                'game_id' => $game_id, 
                                'giftbag_id' => $gift_bag_id,
                                'player_id' => $id,
                                'player_name' => $name,
                                'operator' => $operator,
                                'server_name' => $server->server_name,
                                'operation_type' => $operation_type,
                                'extra_msg' => $status . '|name',

                        ));
        }
    }

    public function insert_gift_msg_id($players, $gift_bag_id, $game_id, $operator, $status, $operation_type, $server, $api){
        foreach ($players as $player) {
            $player_info_from_id = $api->getPlayerInfoByPlayerID($player);
            $operation = Operation::insert(array(
                                'operate_time' => time(),
                                'game_id' => $game_id, 
                                'giftbag_id' => $gift_bag_id,
                                'player_id' => $player,
                                'player_name' => isset($player_info_from_id->name) ? $player_info_from_id->name : '',
                                'operator' => $operator,
                                'server_name' => $server->server_name,
                                'operation_type' => $operation_type,
                                'extra_msg' => $status . '|id',

                        ));
        }
    }

    public function insert_gift_msg($gift_bag_id, $game_id, $operator, $status, $server, $extra_msg ,$operation_type){
        $operation = Operation::insert(array(
                            'operate_time' => time(),
                            'game_id' => $game_id, 
                            'giftbag_id' => $gift_bag_id,
                            'operator' => $operator,
                            'server_name' => $server->server_name,
                            'operation_type' => $operation_type,
                            'extra_msg' => $status . '|' .$extra_msg,

                    ));
    }

    public function allServerGiftBagIndex1()
    {
        $servers = $this->getServers();
        //$servers = Server::currentGameServers()->get();
        
        $table = $this->initTable2();
        
        $gifts = $table->getData();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.giftbag.allservergiftbag1', array(
                'servers' => $servers,
                'gifts' => $gifts
            ))
        );
        return View::make('main', $data);
    }

    public function allServerGiftBagData1()
    {
        //var_dump(Session::get('platform_id'));die();
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'server_id1' => 'required',
            'gift_bag_id' => 'required',
            'remark' => 'required',
            'days' => 'required'
        );
        $server_id1 = Input::get('server_id1');
        $main_server = Server::find($server_id1);
        if (!$main_server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
        $server_id2 = Input::get('server_id2'); 
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $remark = Input::get('remark');
        $gift_bag_id = (int) Input::get('gift_bag_id');
        $days = (int) Input::get('days');
        $type = Input::get('type');
        $game_id = Session::get('game_id');
        $operator = Auth::user()->username;
        $api = GameServerApi::connect($main_server->api_server_ip, $main_server->api_server_port, $main_server->api_dir_id);
        if ($type == 'true') { //给合服后的所有服务器发礼包
            $response = $api->createGiftBagForAllServer($gift_bag_id, $days, $gift_bag_id);
            $result = $main_server->server_track_name .'==' . $response->result;

            $extra_msg = '合服后全服|有效领取天数:' . $days . '|remark:' . $remark;
            if(isset($response->result) && $response->result == 'OK'){
                $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'success',$main_server, $extra_msg, 'combined_server_gift');
            }else{
                $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'fail',$main_server, $extra_msg, 'combined_server_gift');
            }
        } else {
            if (isset($server_id2) && $server_id2 > 0 ) {
                //$server = Server::where('server_internal_id', '=', $server_id2)->get();
                //var_dump($server);die();
                $ser = Server::where('game_id', '=', $game_id)->get();
                for($i=0; $i<count($ser); $i++) {
                    if ($ser[$i]->server_internal_id == $server_id2) {
                        $server = $ser[$i];
                    }
                }
                if (! $server) {
                    $msg['error'] = Lang::get('error.basic_not_found');
                    return Response::json($msg, 404);
                }
                $response = $api->createGiftBagForAllServer1($gift_bag_id, $days, $gift_bag_id, $server_id2);
                $result = $server->server_track_name .'==' . $response->result;

                $extra_msg = '只发合服之前的从服:'.$server->server_name .'|有效领取天数:' . $days . '|remark:' . $remark;
                if(isset($response->result) && $response->result == 'OK'){
                    $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'success',$main_server, $extra_msg, 'combined_server_gift');
                }else{
                    $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'fail',$main_server, $extra_msg, 'combined_server_gift');
                }
            } else{
                $response = $api->createGiftBagForAllServer1($gift_bag_id, $days, $gift_bag_id, $main_server->server_internal_id);
                $result = $main_server->server_track_name .'==' . $response->result;

                $extra_msg = '只发合服之前的主服:'.$main_server->server_name .'|有效领取天数:' . $days . '|remark:' . $remark;
                if(isset($response->result) && $response->result == 'OK'){
                    $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'success',$main_server, $extra_msg, 'combined_server_gift');
                }else{
                    $this->insert_gift_msg($gift_bag_id, $game_id, $operator, 'fail',$main_server, $extra_msg, 'combined_server_gift');
                }
            }   
        }
        //var_dump($response);die();
        return Response::json($result);
    }

    public function getSource()
    {
        $server_id1 = Input::get('server_id1');
        $server_id1 = Server::find($server_id1)->server_internal_id;
        $server = $this->initTable3();
        $server = $server->getData();
        $server = (array)$server;
        //$game_id = 9;
        $len = count($server);
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        for ($i=0; $i < $len; $i++) { 
            if ($game_id != $server[$i]->gameid) { 
                continue;
            } else {
                $ser = array();
                if ($server[$i]->serverid1 == $server_id1) {
                    $arr = explode(',', $server[$i]->serverid2);
                    for ($j=0; $j < count($arr); $j++) { 
                        $ser[$j]['server_id'] = $arr[$j];
                        $ss = Server::where('game_id', '=', $game_id)->get();
                        for ($k=0; $k < count($ss); $k++) { 
                            if ($ss[$k]->server_internal_id == $arr[$j]) {
                                $ser[$j]['server_name'] = $ss[$k]->server_name;
                            }
                        }

                    }
                    return Response::json($ser);
                }
            }       
        }    
    }

    public function getServers()
    {
        $ser = $this->getUnionGame();
        $game_id = Session::get('game_id');
        //$game_id = 9;
        $len = count($ser);
        for ($i=0; $i < $len; $i++) { 
            $game_arr[$i] =  $ser[$i]->gameid;  
        }
        $ga = array_unique($game_arr);
        $se = "";
        if (in_array($game_id, $ga)) {//判断是联运
            for ($i=0; $i < $len; $i++) { 
                if ($ser[$i]->gameid == $game_id) { 
                    $se .= $ser[$i]->serverid2 . ' , '; 
                }
            }
            $se_arr = explode(',' , $se);
            unset($se_arr[count($se_arr)]);

            $server = Server::whereNotIn('server_internal_id', $se_arr)->get();
            for ($i=0; $i < count($server); $i++) { 
                if ($server[$i]->game_id == $game_id) {
                    $servers[] = $server[$i];
                }
            }
        } else {
            $servers = Server::currentGameServers()->get();
        }
        return $servers;
    }

    public function getUnionGame()
    {
        $server = $this->initTable3();
        $server = $server->getData();
        $server = (array)$server;
        return  $server;
    }

    
    private function getGameId()
    {
        $ser = $this->getUnionGame();
        $len = count($ser);
        for ($i=0; $i < $len; $i++) { 
            $game_arr[$i] =  $ser[$i]->gameid;  
        }
        $ga = array_unique($game_arr);
        return $ga;
    }
    
    public function getServersInternal($server_id)
    {
        $game_arr = $this->getGameId();
        $game_id = Session::get('game_id');
        if (in_array($game_id, $game_arr)) {
            $ser = Server::where("game_id", "=", $game_id)->get();
            for ($i=0; $i < count($ser); $i++) { 
                if ($ser[$i]->server_internal_id == $server_id) {
                    $server = $ser[$i];
                    break;
                }
            }
        } else {
            $server = Server::find($server_id);
        }
         return $server; 
    }

}
