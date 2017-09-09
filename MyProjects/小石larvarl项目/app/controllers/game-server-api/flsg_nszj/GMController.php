<?php

class GMController extends \BaseController
{
    private function initTable($file_name)
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        return $table;
    }
    
    public function index()
    {
        $servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.gm.index', array(
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    public function load()
    {
        $msg = array('error'=>'Input error.');
        $rules = array(
            'server_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $server_ids = Input::get('server_id');//应该是一个数组
        if($server_ids == '0'){
            return Response::json(array('error'=>'Did you select a server?'), 403);
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $result = array();
        //Log::info(var_export($api , true));
        foreach ($server_ids as $key => $server_id)
        {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = (Lang::get('error.server_not_found') . $server_id);
                return Response::json($msg, 403);
            }

            if(in_array($game_id, $this->world_edition_list))       //世界版GM未回复消息：由于各Slave无法直连游戏服务器，改为由Master连接游戏服务器。
            {
                $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                $response =$api->getGMQuestions();
//                Log::info('GM questions response:'.var_export($response, true));
                $wresult = array();
                if (!empty($response->GM_Logs))
                {
                    $log = $response->GM_Logs;
                    $types = array(
                        1 => Lang::get('serverapi.gm_type_bug'),
                        2 => Lang::get('serverapi.gm_type_complaint'),
                        3 => Lang::get('serverapi.gm_type_advice'),
                        4 => Lang::get('serverapi.gm_type_other')
                    );
                    foreach ($log as $key => &$v) {
                        $v->type_name = $types[(int) $v->GMType];
                        $v->ser_id = $server->server_id;
                        $v->server_name = $server->server_name;
                        if (! isset($v->Name)) {
                            $player = $api->getPlayerInfoByPlayerID((int) $v->PlayerID);
                            if ($player && isset($player->Name)) {
                                $v->Name = $player->Name;
                            }
                        }
                        $v->SendTime = date('Y-m-d H:i:s', $v->SendTime);
                        $wresult[] = $v;
                    }
                }
                 $body = $wresult;
            }else{
                 $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                 $response = $api->getGMQuestions($server_id, $server->server_name, $server->api_server_ip, 
                                                $server->api_server_port, $server->api_dir_id);
                 $body = $response->body;
            }
            //Log::info(var_export($response , true));
            if(empty($body)){
                continue;
            }
            $gm_arr = array();
            foreach ($body as $v) {
                if(!isset($v->GMID)){
                    continue;
                }
                $server_gm  = GM::findServerGMID($v->GMID, $server_id)->first();
                if (! $server_gm) {
                    $gm = array();
                    $gm['server_gm_id'] = $v->GMID;
                    $gm['server_root_gm_id'] = $v->RootGMID;
                    $gm['message'] = $v->Message;
                    $gm['send_time'] = strtotime($v->SendTime);
                    $gm['gm_type'] = $v->GMType;
                    $gm['is_question'] = $v->IsQuestion;
                    $gm['player_id'] = $v->PlayerID;
                    $gm['server_id'] = $server_id;
                    if (isset($v->Name)) {
                        $gm['player_name'] = $v->Name;
                    }
                    //$val =
                        GM::insert($gm);
                    //Log::info("Function load why load? ##insert to GM table:----------->return value:$val------->gm-inserted:".var_export($gm, true));
                    $gm_arr[] = $gm;
                //$v->SendTime = date('Y-m-d H:i:s', $v->SendTime); 返回的response对象不允许修改SendTime属性，应该在前端用JS自带的函数修改时间格式
                }
                $result[] = $v;
            }
        }
       // Log::info(var_export($result , true));
        if (isset($result) && count($result) > 0) {
            return Response::json($result);
        } else{
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '没有要回复玩家的内容'
            );
            return Response::json($msg, 403);
        }

    } 

    public function reply()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'player_id' => 'required',
            'type' => 'required',
            'server_gm_id' => 'required',
            'ser_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $player_id = (int) Input::get('player_id');
        //var_dump($player_id);die();
        $server_id = (int) Input::get('ser_id');
        $type = (int) Input::get('type');
        $server_gm_id = (int) Input::get('server_gm_id');
        $reply_message = trim(Input::get('reply_message'));
        $server = Server::find($server_id);
        //var_dump($server);die();
        if (! $server) {
            return Response::json($msg, 404);
        }
        $gm = GM::findServerGMID($server_gm_id, $server_id)->first();  //在本地数据库中取出已保存的未回复的消息
        if (! $gm) {
            return Response::json(array('error'=> 'Can not find gm in database'), 403);
        }

        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id); //将GM回复信息发送到游戏服务器
        $response = $api->replyGMQuestion($server_gm_id, $player_id, $type, $reply_message);

        if (isset($response->result) && $response->result == 'OK') {
            $gm->reply_message = $reply_message;
            $gm->replied_time = time();
            $gm->user_id = Auth::user()->user_id;
            $gm->is_done = 1;       //在本地数据库中未回复标志改为已回复
            $val = $gm->save();
//            Log::info("Function reply ##insert to GM table:----------->return value:$val------->gm-inserted:".var_export($gm, true));
        } else {
            return Response::json(array('error'=> 'Message send failed!'), 403);
        }
        
        return $api->sendResponse();
    }

    public function repliedIndex()
    {
        $server_name = Input::get('server_name');
        $player_name = Input::get('player_name');
        $server_init = 0;
        if($server_name && $player_name){
            $server = Server::where('game_id',Session::get('game_id'))->where('server_name',$server_name)->first();
            if($server){
                $server_init = $server->server_id;
            }
        }
        //$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.gm.replied', array(
                'servers' => $servers,
                'server_init' => (int)$server_init,
                'player_name' => $player_name
            ))
        );
        return View::make('main', $data);
    }

    public function sendReplied()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'server_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $type = (int) Input::get('type');
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        /*Log::info(var_export($start_time , true));
        Log::info(var_export($end_time , true));
        Log::info(var_export($type , true));*/
        $player_name = Input::get('player_name');
        //Log::info(var_export($player_name , true));
        $page = (int)Input::get('page');
        $server_ids = Input::get('server_id');
//        Log::info("---------servers-you-selected--------------->".var_export($server_ids, true));
        if($server_ids == 0){
            return Response::json(array('error'=>'Did you select a server?'), 403);
        }
        $page = $page > 0 ? $page : 1;
        $gm_types = array(
            0 => Lang::get('serverapi.gm_type_unknow'),
            1 => Lang::get('serverapi.gm_type_bug'),
            2 => Lang::get('serverapi.gm_type_complaint'),
            3 => Lang::get('serverapi.gm_type_advice'),
            4 => Lang::get('serverapi.gm_type_other')
        );
        $items = array();
		foreach ($server_ids as $key => $server_id) {
            $server = Server::find($server_id);
            if (! $server) {
                return Response::json($msg, 404);
            }
            $count = GM::repliedGM($player_name, $start_time, $end_time, $server_id, $type)->count();
            $per_page = 30;
//            Log::info("player_name--start_time--end_time--server_id--type--->".json_encode($player_name.'-'.$start_time.'-'.$end_time.'-'.$server_id.'-'.$type));
            $gm_list = GM::repliedGM($player_name, $start_time, $end_time, $server_id, $type)->forPage($page, $per_page)->get();
//            Log::info("---------gm-list:repliedGM---------------->".var_export($gm_list , true));
            foreach ($gm_list as &$v) {
                $v->gm_type_name = $gm_types[$v->gm_type];
                $v->send_time = date('Y-m-d H:i:s', $v->send_time);
                $v->replied_time = date('Y-m-d H:i:s', $v->replied_time);
            }
            $gm_list = $gm_list->toArray();
            $items = array_merge($items, $gm_list);
            $reply_done_one_page = array(
                'items' => $items,
                'current_page' => $page,
                'per_page' => $per_page,
                'count' => $count
            );
        }
		
        return Response::json($reply_done_one_page);
    }

    public function gmOrderIndex()
    {
        $servers = Server::currentGameServers()->whereIn('server_track_name',array('T1XYDXIAOXIN','T1XYD9061','N0XYD9061','S899XYD9061'))->get();
        /*foreach ($servers as $key => $value) {
            if ($value->server_track_name == "T1XYDXIAOXIN" || $value->server_track_name == "T1XYD9061" || $value->server_track_name == "N0XYD9061") {
                $server = $value;
                break;
            }
        }*/
        //Log::info("t1xydxiaoxin server:".var_export($server, true));
        //var_dump($server);die();
        $game = Game::find(Session::get('game_id'));
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.gm.gmorder', array(
                    'servers' => $servers,
                    'game_code' => $game->game_code
                )),
        );
        return View::make('main', $data);
    }

    public function gmOrderOpen()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => '',
        );
        $server_id = (int)Input::get('server_id');
        $server = Server::find($server_id);
        $is_close = (int)Input::get('is_close');
        if (!$server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg);
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        if(0 == $is_close){
            $response = $api->openGmOrder();
        }elseif(1 == $is_close){
            $response = $api->closeGmOrder();
        }
        if(isset($response->result) && $response->result == 'OK'){
            $result[] = array(
                'msg' => ' ( ' . $server->server_name . ' ) ' . $response->result . "\n",
                'status' => 'ok'
            );
        }else{
            $result[] = array (
                    'msg' => ' ( ' . $server->server_name . ' ) ' . 'error' . "\n",
                    'status' => 'error'
            );
        }
        $msg = array (
            'result' => $result 
        );
        return Response::json($msg);
    }

    public function battleChampionIndex()
    {
        $server = Server::currentGameServers()->get();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.gm.champion', array('server' => $server))
        );
        return View::make('main', $data);
    }

    public function battleChampionData()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error'=>''
        );
        $rules = array(
            'server_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg ,403);
        }
        $server_ids = Input::get('server_id');
        $game_id = Session::get('game_id');
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $data1 = array();
        $data2 = array();
        foreach ($server_ids as $key => $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 403);
            }
            $info = DB::table('log')->where('game_id', $game_id)->where('user_id', '=', $server->server_internal_id)->where('log_key', 'battle')->whereBetween('created_at', array($start_time, $end_time))->get();
            
            if (!empty($info)) {
                foreach ($info as $key => $value) {
                    $sql1 = $value->new_value;
                    $sql2 = $value->old_value;
                    $arr1 = explode("|", $sql1);
                    $name = explode("|", $value->desc);
                    $league_name1 = isset($name[0]) ? $name[0] : '';
                    $league_name2 = isset($name[1]) ? $name[1] : '';
                    $data_arr1 = explode(",", $arr1[0]);
                    $data_arr2 = explode(",", $arr1[1]);
                    $arr2 = explode("|", $sql2);
                    $data_arr3 = explode(",", $arr2[0]);
                    $data_arr4 = explode(",", $arr2[1]);
                    for ($i=0; $i < count($data_arr1); $i++) { 
                        $data1[] = array(
                            'name' => $data_arr1[$i],
                            'player_id' => $data_arr2[$i]
                        );
                    }
                    for ($j=0; $j < count($data_arr3); $j++) { 
                        $data2[] = array(
                            'name' => $data_arr3[$j],
                            'player_id' => $data_arr4[$j]
                        );
                    }
                }
            } else{
                $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                $response1 = $api->getBattleChampion();
                if (isset($response1)) {
                    $response1 = (object)$response1;
                    $frist = $response1->frist;
                    $second = $response1->second;
                    $battle1 = $api->getBattleChampionMember(intval($frist));
                    $battle2 = $api->getBattleChampionMember(intval($second));
                    $data1 = $battle1->members;
                    $data2 = $battle2->members; 
                    $len1 = count($data1);
                    $sql11 = "";
                    $sql12 = "";
                    $sql21 = "";
                    $sql22 = "";
                    for ($i=0; $i < $len1; $i++) { 
                        if ($i < $len1-1) {
                            $sql11 .= $data1[$i]->name . ",";
                        }else if($i == $len1-1){
                            $sql11 .= $data1[$i]->name;
                        }
                    }
                    $league_name1 = isset($data1[0]->league_name) ? $data1[0]->league_name : '';
                    for ($i=0; $i < $len1; $i++) { 
                        if ($i < $len1-1) {
                            $sql12 .= $data1[$i]->player_id . ",";
                        }else if($i == $len1-1){
                            $sql12 .= $data1[$i]->player_id;
                        }
                    }
                    $sql1 = $sql11 . "|" . $sql12;

                    $len2 = count($data2);
                    for ($i=0; $i < $len2; $i++) { 
                        if ($i < $len2 -1) {
                            $sql21 .= $data2[$i]->name . "," ; 
                        }elseif ($i == $len2-1) {
                            $sql21 .= $data2[$i]->name; 
                        }
                    }
                    for ($i=0; $i < $len2; $i++) { 
                        if ($i < $len2-1) {
                            $sql22 .= $data2[$i]->player_id . "," ;
                        }elseif ($i == $len2 -1) {
                            $sql22 .= $data2[$i]->player_id;
                        }
                    }
                    $league_name2 = isset($data2[0]->league_name) ? $data2[0]->league_name : '';
                    $sql2 = $sql21 . "|" . $sql22;

                    //存入数据库
                    $log = new EastBlueLog;
                    $log->log_key = "battle";
                    $log->game_id = $game_id;
                    $log->desc = $league_name1 . '|' . $league_name2;
                    $log->user_id = $server->server_internal_id;
                    $log->new_value = $sql1; //冠军
                    $log->old_value = $sql2; // 亚军
                    $log->created_at = time();
                    $log->save();

                }
            }
        }
        $data = array(
            'league_name1' => $league_name1,
            'league_name2' => $league_name2,
            'data1' => $data1,
            'data2' => $data2
        );
        if (isset($data)) {
            return Response::json($data);
        }
    }

    public function downloadBattleChampionIndex()
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

    public function downloadBattleChampionData()
    {
        $msg = array(
            'code' => Lang::get('errorcode.unknow'),
            'error' => '',
        );
        $server_ids = Input::get('server_id');
        $game_id = Session::get('game_id');
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        
        $result = array();
        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";
        $title = array(
            Lang::get('serverapi.server_name'),
            Lang::get('serverapi.battle_name'),
            Lang::get('serverapi.player_name'),
            Lang::get('serverapi.player_id'),
            Lang::get('serverapi.guanjun'),
            Lang::get('serverapi.operate_time')
        );
        $csv = CSV::init($file, $title);
        foreach ($server_ids as $key => $server_id){
            $data1 = array();
            $data2 = array();
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.server_not_found');
                return Response::json($msg, 403);
            }

            $info = DB::table('log')->where('game_id', $game_id)->where('user_id', '=', $server->server_internal_id)->where('log_key', 'battle')->whereBetween('created_at', array($start_time, $end_time))->get();
            if (!empty($info)) {
                foreach ($info as $key => $value) {
                    $sql1 = $value->new_value;
                    $sql2 = $value->old_value;
                    $arr1 = explode("|", $sql1);
                    $data_arr1 = explode(",", $arr1[0]);
                    $data_arr2 = explode(",", $arr1[1]);
                    $arr2 = explode("|", $sql2);
                    $data_arr3 = explode(",", $arr2[0]);
                    $data_arr4 = explode(",", $arr2[1]);
                    $name = $value->desc;
                    $name = explode('|', $name);
                    //得到冠军
                    for ($i=0; $i < count($data_arr1); $i++) { 
                        $data1[] = array(
                            'server_name' => $server->server_name,
                            'league_name' => isset($name[0]) ? $name[0] : '',
                            'name' => $data_arr1[$i],
                            'player_id' => $data_arr2[$i],
                            'guanjun' => '冠军',
                            'battle_time' => isset($value->created_at) ? date('Y-m-d H:i:s', $value->created_at) : '' 
                        );    
                    }
                    //得到亚军
                    for ($i=0; $i < count($data_arr3); $i++) { 
                        $data2[] = array(
                            'server_name' => $server->server_name,
                            'league_name' => isset($name[1]) ? $name[1] : '',
                            'name' => $data_arr3[$i],
                            'player_id' => $data_arr4[$i],
                            'guanjun' => '亚军',
                            'battle_time' => isset($value->created_at) ? date('Y-m-d H:i:s', $value->created_at) : ''
                        );
                    }
                    //写入csv
                    for ($i=0; $i < count($data1); $i++) { 
                        $res = $csv->writeData($data1[$i]); 
                    }
                    
                    for ($i=0; $i < count($data2); $i++) { 
                        $res = $csv->writeData($data2[$i]);
                    }
                }
            }else{
                //Log::info(var_export($server->server_name, true));
                $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                $response1 = $api->getBattleChampion();
                if (isset($response1)) {
                    $league_name1 = "";
                    $league_name2 = "";
                    $response1 = (object)$response1;
                    $frist = $response1->frist;
                    $second = $response1->second;
                    $battle1 = $api->getBattleChampionMember(intval($frist));
                    $battle2 = $api->getBattleChampionMember(intval($second));
                    $data1 = $battle1->members;
                    $data2 = $battle2->members;
                    //Log::info(var_export($data1, true));
                    //Log::info(var_export($data2, true));
                    $len1 = count($data1);
                    $sql11 = "";
                    $sql12 = "";
                    $sql21 = "";
                    $sql22 = "";
                    for ($i=0; $i < $len1; $i++) { 
                        if ($i < $len1-1) {
                            $sql11 .= $data1[$i]->name . ",";
                        }else if($i == $len1-1){
                            $sql11 .= $data1[$i]->name;
                        }
                        
                    }
                    for ($i=0; $i < $len1; $i++) { 
                        if ($i < $len1-1) {
                            $sql12 .= $data1[$i]->player_id . ",";
                        }else if($i == $len1-1){
                            $sql12 .= $data1[$i]->player_id;
                        }
                    }
                    $league_name1 = isset($data1[0]->league_name) ? $data1[0]->league_name : '';
                    for ($i=0; $i < $len1; $i++) { 
                        $res1 = array(
                            'server_name' => $server->server_name,
                            'league_name' => isset($data1[$i]->league_name) ? $data1[$i]->league_name : '',
                            'name' => isset($data1[$i]->name) ? $data1[$i]->name : '',
                            'player_id' => isset($data1[$i]->player_id) ? $data1[$i]->player_id : '' ,
                            'guanjun' => '冠军',
                            'battle_time' =>  date('Y-m-d H:i:s', time()) 
                        );
                        //Log::info(var_export($data1[$i], true));
                        $res =  $csv->writeData($res1);
                        //unset($res1);
                    }


                    $sql1 = $sql11 . "|" . $sql12;

                    $len2 = count($data2);
                    for ($i=0; $i < $len2; $i++) { 
                        if ($i < $len2 -1) {
                            $sql21 .= $data2[$i]->name . "," ; 
                        }elseif ($i == $len2-1) {
                            $sql21 .= $data2[$i]->name; 
                        }
                    }
                    for ($i=0; $i < $len2; $i++) { 
                        if ($i < $len2-1) {
                            $sql22 .= $data2[$i]->player_id . "," ;
                        }elseif ($i == $len2 -1) {
                            $sql22 .= $data2[$i]->player_id;
                        }
                    }
                    $league_name2 = isset($data2[0]->league_name) ? $data2[0]->league_name : '';
                    for ($i=0; $i < $len2; $i++) { 
                        $res2 = array(
                            'server_name' => $server->server_name,
                            'league_name' => isset($data2[$i]->league_name) ? $data2[$i]->league_name : '',
                            'name' => isset($data2[$i]->name) ? $data2[$i]->name : '',
                            'player_id' => isset($data2[$i]->player_id) ? $data2[$i]->player_id : '' ,
                            'guanjun' => '亚军',
                            'battle_time' =>  date('Y-m-d H:i:s', time()) 
                        );
                        //Log::info(var_export($data2[$i], true));
                        $res =  $csv->writeData($res2);
                        //unset($res2);
                    }

                    $sql2 = $sql21 . "|" . $sql22;

                    //存入数据库
                    $log = new EastBlueLog;
                    $log->log_key = "battle";
                    $log->game_id = $game_id;
                    $log->desc = $league_name1 . '|' . $league_name2;
                    $log->user_id = $server->server_internal_id;
                    $log->new_value = $sql1; //冠军
                    $log->old_value = $sql2; // 亚军
                    $log->created_at = time();
                    $log->save();
                    unset($log);
                    unset($league_name1);
                    unset($league_name2);
                    unset($api);
                    unset($response1);
                }
                
            }
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

    public function firstRechargeIndex()
    {
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.backpack.recharge', array('servers' => $servers)),
        );
        return View::make('main', $data);
    } 

    public function firstRechargeOperate()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => ''
        );
        $rules = array(
            'server_id' => 'required',
            'type' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
        }
        $server_ids = Input::get('server_id');
        $type = Input::get('type');
        $result = array();
        foreach ($server_ids as $key => $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.server_not_found');
                return Response::json($msg, 403);
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            if ($type == "open" || $type== "close") { //开启或者关闭
                $response = $api->firstRechargeOperate($type);
                if ($response->active) { // 返回成功
                    if ($type == "open") {
                        $result[] = array(
                            'statu' => 'OK',
                            'msg' => $server->server_name . '   开启OK' . '---' . $response->active
                        );
                    }
                }else {
                    if ($type == "close") {
                        $result[] = array(
                            'statu' => 'OK',
                            'msg' => $server->server_name . '    关闭OK' . '---' . $response->active
                        );
                    }
                }
                 
            }else{ 
                $response  = $api->firstRechargeOperate($type);
                if ($response->active) {
                   $result[] = array(
                        'statu' => 'OK',
                        'msg' => $server->server_name . '   已开启' . '---' . $response->active
                    );
                } else {
                    $result[] = array(
                        'statu' => 'OK',
                        'msg' => $server->server_name . '   已关闭' . '---' . $response->active
                    );
                }
            } 
        }
        if (isset($result)) {
            return Response::json($result);
        }
    }
    /**
     * 风流三国添加删除坐骑
     */
    public function setmountIndex()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $game_code = $game->game_code;
        $table = $this->initTable('mount');
        $table = $table->getData();
        $mount = array();
        foreach ($table as $value) {
            $mount[] = array(
                'name' => $value->name,
                'mountid' => $value->id
            ); 
        }
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.gm.mount',array(
                'servers' => $servers,
                'mount' => $mount
            ))
        );
        return View::make('main', $data);
    }

    public function setMount()
    {
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );

        $rules = array(
            'name_or_id' => 'required',
            'mount' => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);

        if($validator->fails())
        {
            return Response::json($msg, 403);
        }
        $server_id = (int)Input::get('server_id');
        $name_or_id = Input::get('name_or_id');
        $choice = (int)Input::get('choice');
        $mount = Input::get('mount');        
        $is_mount = ( int ) Input::get('is_mount') == 1 ? 1 : 0;
        if(0 == $server_id){
            return Response::json(array('error'=>'Did you select a server?'), 403);
        }
        $server = Server::find($server_id);
        if(!$server)
        {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
        $mount = explode(":", $mount);
        try{
           $mount_id = (int)$mount[1];
        }catch(\Exception $e){
            return Response::json($msg, 403);
        }
        $table = $this->initTable('mount');
        $table = $table->getData();
        $temp = array();
        foreach ($table as $value) {
            $temp[] = $value->id;
        }
        if(!in_array($mount_id, $temp)){
            return Response::json(array('error'=>"不存在的坐骑id！"), 403);
        }
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        if(0 == $choice){
            $player = $api->getPlayerInfoByName($name_or_id);
            if(isset($player->player_id)){
                $name_or_id = (int)$player->player_id;
            }else{
                $error = $name_or_id . ' ' . 'Not Found player_id';
                return Response::json(array('error'=>"$error"), 403);
            }  
        }

        $response = $api->setMount((int)$name_or_id, $mount_id, $is_mount);
        $result = array();
        if (isset($response->result) && $response->result == 'OK' && $is_mount == 0) {
            $result[] = array(
                'msg' => ' ( ' . $name_or_id .' ) : '. '添加' . $mount[0] . 'OK' . "\n",
                'status' => 'ok'
            );
        } elseif (isset($response->result) && $response->result == 'OK' && $is_mount == 1) {
            $result[] = array(
                'msg' => ' ( ' . $name_or_id .' ) : '. '删除' . $mount[0] . 'OK' . "\n",
                'status' => 'ok'
            );
        } else {
            $result[] = array(
                'msg' => ' ( ' . $server->server_name .' ) : ' . $name_or_id . 'error' . "\n",
                'status' => 'error'
            );
        }
        $msg = array(
                'result' => $result
        );
        return Response::json($msg);
    }


    /*public function getServers()
    {
        $ser = $this->getUnionGame();
        $game_id = Session::get('game_id');
        $len = count($ser);
        for ($i=0; $i < $len; $i++) { 
            $game_arr[$i] =  $ser[$i]->gameid;  
        }
        $ga = array_unique($game_arr);
        $se = "";
        $servers = array();
        if (in_array($game_id, $ga)) {
            for ($i=0; $i < $len; $i++) { 
                if ($ser[$i]->gameid == $game_id) { //判断是联运
                    $se .= $ser[$i]->serverid2 . ' , '; 
                }
            }
            $se_arr = explode(',' , $se);
            unset($se_arr[count($se_arr)]);
            $server = Server::whereNotIn('server_internal_id', $se_arr)->get();
            for ($i=0; $i < count($server); $i++) { 
                if ($server[$i]->is_server_on == 1) {
                    if ($server[$i]->game_id == $game_id) {
                        $servers[] = $server[$i];
                    }
                }
            }
        } else {
            $servers = Server::currentGameServers()->get();
        }
        return $servers;
    }*/

   
    /*private function getUnionServerId($server_id)
    {
        $ser = $this->getUnionGame();
        $server_internal_id = Server::find($server_id)->server_internal_id;
        $game_id = Session::get('game_id');
        $len = count($ser);
        for ($i=0; $i < $len; $i++) { 
            $game_arr[$i] = $ser[$i]->gameid;
        }
        $game_arr = array_unique($game_arr);
        if (in_array($game_id, $game_arr)) {
            for ($i=0; $i < $len; $i++) { 
                if ($game_id == $ser[$i]->gameid) {
                    $ser1[] = $ser[$i]->serverid1;
                    $ser2[] = $ser[$i]->serverid2;
                    $target = 0;
                    for ($j=0; $j < count($ser2); $j++) { 
                        $arr[$j] = explode(',', $ser2[$j]);
                        if (in_array($server_internal_id, $arr[$j])) {
                            $target = $ser1[$j];
                            break;
                            // $server_id = Server::whereRaw("server_internal_id = $target and game_id = $game_id")->pluck('server_id');
                            // return $server_id;
                        }else{
                            continue;
                        }    
                    }
                    if (isset($target)) {
                        $server_id = Server::whereRaw("server_internal_id = $target and game_id = $game_id")->pluck('server_id');
                        return $server_id;
                    }else{
                        return $server_id;
                    }

                }
            }
        }else{
            return $server_id;
        }
    }*/
}