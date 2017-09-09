<?php

class YYSGGMController extends \BaseController
{
    private function initTableActivity()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/activity.txt');
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
            'content' => View::make('serverapi.yysg.gm.index', array(
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    public function load()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->first();
        }
        $platform_id = Session::get('platform_id');

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

        $response = $api->getGMQuestions($platform_id);
        if(isset($response->error)){
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '访问游戏服务器出错'
            );
            return Response::json($msg, 403);
        }
        $body = $response->list;
        $result = array();
        //Log::info("YYSG gm question response==>".var_export($response, true));
        if(!empty($body)){
            foreach ($body as $v) {
                if(isset($v->id)){
                    $server_gm = GM::findServerGMID($v->id, $server->server_id)->first();
                    if (!$server_gm) {
                        $gm = array();
                        $gm['server_gm_id'] = $v->id;
                        //$gm['server_root_gm_id'] = $v->RootGMID;
                        $gm['message'] = $v->question;
                        $gm['send_time'] = $v->question_time;
                        /*$gm['gm_type'] = $v->GMType;
                        $gm['is_question'] = $v->IsQuestion;*/
                        $gm['player_id'] = $v->player_id;
                        $gm['server_id'] = $server->server_id;
                        $gm['title'] = $v->title;
                        if (isset($v->player_name)) {
                            $gm['player_name'] = $v->player_name;
                        }
                        //$val =
                        GM::insert($gm);
                        //Log::info("Function load why load? ##insert to GM table:----------->return value:$val------->gm-inserted:".var_export($gm, true));
                    }
                    $v->question_time = date('Y-m-d H:i:s', $v->question_time);
                    $result[] = $v;
                }
            }
        }

        //Log::info(var_export($result,true));
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
        //Log::info("reply ()----------");
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'player_id' => 'required',
            'server_gm_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        //$player_id = (int)Input::get('player_id');
        //var_dump($player_id);die();
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->first();
        }
        //$type = (int)Input::get('type');
        $server_gm_id = (int)Input::get('server_gm_id');
        $reply_message = trim(Input::get('reply_message'));
        /*Log::info("game_id:".$game_id."----server_id:".var_export($server->server_id, true)."----reply_message:".$reply_message."----gm_id:".$server_gm_id);
        die();*/
        //$server = Server::find($server_id);
        //var_dump($server);die();
        $gm = GM::findServerGMID($server_gm_id, $server->server_id)->first();  //在本地数据库中取出已保存的未回复的消息
        if (!$gm) {
            App::abort(500, 'Can not find gm in database' . json_encode(Input::all()));
        }

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port); //将GM回复信息发送到游戏服务器
        $response = $api->replyGMQuestion($server_gm_id, $reply_message, $platform_id);

        //Log::info("reply response".var_export($response, true));
        if (isset($response->result) && $response->result == 'OK') {
            $gm->reply_message = $reply_message;
            $gm->replied_time = time();
            $gm->user_id = Auth::user()->user_id;
            $gm->is_done = 1;       //在本地数据库中未回复标志改为已回复
            $val = $gm->save();
            //Log::info("Function reply ##insert to GM table:----------->return value:$val------->gm-inserted:".var_export($gm, true));
        } else {
            App::abort(500, 'Reply GM Message Server Error' . json_encode($gm));
        }

        return $api->sendResponse();
    }

    public function repliedIndex()
    {
        //$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('serverapi.yysg.gm.replied', array(
                'servers' => $servers
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
        $type = 0;
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));

        $player_name = Input::get('player_name');
        $page = (int)Input::get('page');
        $server_ids = Input::get('server_id');

        //Log::info("type:".$type."--start time:".$start_time."--end time:".$end_time."--server ids".var_export($server_ids, true)."--page:".$page."--player name:".$player_name);
        if ($server_ids == 0) {
            return Response::json(array('error' => 'Did you select a server?'), 403);
        }
        $page = $page > 0 ? $page : 1;
        $items = array();
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                return Response::json($msg, 404);
            }
            $count = GM::repliedYYSGGM($player_name, $start_time, $end_time, $server_id, $type)->count();
            $per_page = 30;
//            Log::info("player_name--start_time--end_time--server_id--type--->".json_encode($player_name.'-'.$start_time.'-'.$end_time.'-'.$server_id.'-'.$type));
            $gm_list = GM::repliedYYSGGM($player_name, $start_time, $end_time, $server_id, $type)->forPage($page, $per_page)->get();
            //Log::info("gm-list:repliedGM-->".var_export($gm_list , true));
            foreach ($gm_list as &$v) {
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

    public function gmVipTalk(){
        $vip = 1;
        return $this->gmTalk($vip);
    }

    public function gmTalk($vip = 0)//先查出需要回复的玩家（player_id）列表
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->orderBy('server_internal_id', 'asc')->first();
        }
        if(!$server){
            App::abort(404);
        }
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        $response = $api->getGMTalkPlayers($platform_id);

        if(isset($response->error)){
            return $this->show_message('--', json_encode($response));
        }
        if(!isset($response->list)){
            return $this->show_message(500, 'Response:'.json_encode($response));
        }

        $player_list = $response->list;
        if('yysg' == $game->game_code){
            if(count($player_list) > 0){
                $all_playerids = array();
                foreach ($player_list as $player) {
                   $all_playerids[] = $player->player_id;
                }
                $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                $result = $slave_api->getPlayerImportance($game_id, $platform_id, $all_playerids);
                if('200' == $result->http_code){
                    $player_list = $result->body;
                }else{
                    $player_list = array();
                }
            }else{
                $player_list = array();
            }
        }

        $player_ids = array();

        $vip_players = array();
        $vip_players_db = SpecialPlayers::where('game_id', $game_id)->where('type', 1)->selectRaw('player_id')->get();
        foreach ($vip_players_db as $vip_player_db) {
            $vip_players[] = $vip_player_db->player_id;
        }
        unset($vip_players_db);
        if($vip){
            foreach ($player_list as $single_player) {
                if(in_array($single_player->player_id, $vip_players)){
                    $player_ids[] = $single_player;
                }
            }
        }else{
            foreach ($player_list as $single_player) {
                if(!in_array($single_player->player_id, $vip_players)){
                    $player_ids[] = $single_player;
                }
            }
        }
        $data = array(
            'content' => View::make('serverapi.yysg.gm.talk', array(
                'player_list' => $player_ids
            ))
        );
        return View::make('main', $data);
    }

    public function gmMessage()//再查出所选玩家的聊天（问题）信息
    {
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->orderBy('server_internal_id', 'asc')->first();
        }
        Log::info('gmtalk test0:'.microtime());
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

        $player_id = Input::get('player_id');
        $enter_player_id = Input::get('enter_player_id');
        $page_num = (int)Input::get('page_num');
        if(NULL != $enter_player_id) {
            $player_id = $enter_player_id;
        }
        else {
            $player_id = $player_id[0];
        }
        Log::info('gmtalk test1:'.microtime());
        $response = $api->getGMMessages($player_id, $page_num, $platform_id);
        Log::info('gmtalk test2:'.microtime());
        if(isset($response->error)){
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '访问游戏服务器出错'
            );
            return Response::json($msg, 403);
        }
        $body = $response->list;
        //Log::info(var_export($body,true));
        $result = array();
        foreach ($body as &$v) {
            //Log::info("YYSG gm question response==>".var_export($v, true));die();
            $server_gm = GM::findServerGMID($v->id, $server->server_id)->first();//检查这条信息是不是已经插入过gm表了
            if (!$server_gm && 10000 != $v->talker_id) {//10000是GM的回复，在回复的时候存储。
                $gm = array();
                $gm['server_gm_id'] = $v->id;
                //$gm['server_root_gm_id'] = $v->RootGMID;
                $gm['message'] = $v->msg;
                $gm['send_time'] = $v->created_time;
                /*$gm['gm_type'] = $v->GMType;
                $gm['is_question'] = $v->IsQuestion;*/
                $gm['player_id'] = $v->player_id;//玩家的角色id
                $gm['server_id'] = $server->server_id;
                $gm['user_id'] = 0;//GM的eastblue账号id，玩家的问题信息没有user_id
                $gm['player_name'] = $v->talker_name;

                try {
                    GM::insert($gm);
                } catch (Exception $e) {
                    //Log::info('yysg--error'.var_export($e, true));
                }
                //Log::info("Function load why load? ##insert to GM table:----------->return value:$val------->gm-inserted:".var_export($gm, true));
            }

            if(10000 == $v->talker_id){//GM回复的信息需要找出GM的eastblue账号的username
                $tmp = GM::where('player_id', $player_id)->where('message', $v->msg)->where('user_id', '>', 0)
                          ->whereBetween('send_time', array(($v->created_time - 60), ($v->created_time + 60)))->first();
                if($tmp && isset($tmp->player_name)){
                    $v->gm_name = $tmp->player_name;
                }else{
                    $v->gm_name = '';
                }
            }else{
                $v->gm_name = '';
            }
            $v->question_time = date('Y-m-d H:i:s', $v->created_time);
            $result[] = $v;
        }
        Log::info('gmtalk test3:'.microtime());
        //Log::info(var_export($result,true));
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

    public function gmMessageSend()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'msg' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        //$player_id = (int)Input::get('player_id');
        //var_dump($player_id);die();
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->orderBy('server_internal_id', 'asc')->first();
        }
        //$type = (int)Input::get('type');
        //$server_gm_id = (int)Input::get('server_gm_id');
        $reply_message = trim(Input::get('msg'));
        $player_id = Input::get('player_id');
        $enter_player_id = Input::get('enter_player_id');
        if(NULL != $enter_player_id) {
            $player_id = $enter_player_id;
        }
        else {
            $player_id = $player_id[0];
        }
        //$server = Server::find($server_id);
        //var_dump($server);die();
        Log::info('gmtalk test4:'.microtime());
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port); //将GM回复信息发送到游戏服务器
        Log::info('gmtalk test5:'.microtime());
        $response = $api->replyGMTalk($player_id, $reply_message, $platform_id);
        Log::info('gmtalk test6:'.microtime());

        $gm = array();
        if (isset($response->id)) {
            $gm['server_gm_id'] = $response->id;
            //$gm['server_root_gm_id'] = $v->RootGMID;
            $gm['message'] = $reply_message;
            $gm['send_time'] = time();
            $gm['replied_time'] =time();
            /*$gm['gm_type'] = $v->GMType;
            $gm['is_question'] = $v->IsQuestion;*/
            $gm['user_id'] = Auth::user()->user_id;//GM的eastblue账号id
            $gm['server_id'] = $server->server_id;
            $gm['player_id'] = $player_id;//GM所回复的玩家的角色id
            $gm['player_name'] = Auth::user()->username;//GM的eastblue账号的username
            $gm['is_done'] = 1;

            GM::insert($gm);
        } else {
            return Response::json(array('error'=> 'Message send failed!'), 403);
        }
        Log::info('gmtalk test7:'.microtime());
        return $api->sendResponse();
    }

    public function gmOrderIndex()
    {
        $servers = Server::currentGameServers()->get();
        foreach ($servers as $key => $value) {
            if ($value->server_track_name == "T1XYDXIAOXIN") {
                $server = $value;
                break;
            }
        }
        //var_dump($server);die();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.gm.gmorder', array('server' => $server)),
        );
        return View::make('main', $data);
    }

    public function gmOrderOpen()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => '',
        );
        $server_id = Input::get('server_id');
        $server = Server::find($server_id);
        if (!$server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg);
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        $response = $api->openGmOrder();
        //var_dump($response);
        return Response::json($response);
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
            'error' => ''
        );
        $rules = array(
            'server_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $msg['error'] = Lang::get('error.basic_input_error');
            return Response::json($msg, 403);
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
                    for ($i = 0; $i < count($data_arr1); $i++) {
                        $data1[] = array(
                            'name' => $data_arr1[$i],
                            'player_id' => $data_arr2[$i]
                        );
                    }
                    for ($j = 0; $j < count($data_arr3); $j++) {
                        $data2[] = array(
                            'name' => $data_arr3[$j],
                            'player_id' => $data_arr4[$j]
                        );
                    }
                }
            } else {
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
                    for ($i = 0; $i < $len1; $i++) {
                        if ($i < $len1 - 1) {
                            $sql11 .= $data1[$i]->name . ",";
                        } else if ($i == $len1 - 1) {
                            $sql11 .= $data1[$i]->name;
                        }
                    }
                    $league_name1 = isset($data1[0]->league_name) ? $data1[0]->league_name : '';
                    for ($i = 0; $i < $len1; $i++) {
                        if ($i < $len1 - 1) {
                            $sql12 .= $data1[$i]->player_id . ",";
                        } else if ($i == $len1 - 1) {
                            $sql12 .= $data1[$i]->player_id;
                        }
                    }
                    $sql1 = $sql11 . "|" . $sql12;

                    $len2 = count($data2);
                    for ($i = 0; $i < $len2; $i++) {
                        if ($i < $len2 - 1) {
                            $sql21 .= $data2[$i]->name . ",";
                        } elseif ($i == $len2 - 1) {
                            $sql21 .= $data2[$i]->name;
                        }
                    }
                    for ($i = 0; $i < $len2; $i++) {
                        if ($i < $len2 - 1) {
                            $sql22 .= $data2[$i]->player_id . ",";
                        } elseif ($i == $len2 - 1) {
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
        foreach ($server_ids as $key => $server_id) {
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
                    for ($i = 0; $i < count($data_arr1); $i++) {
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
                    for ($i = 0; $i < count($data_arr3); $i++) {
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
                    for ($i = 0; $i < count($data1); $i++) {
                        $res = $csv->writeData($data1[$i]);
                    }

                    for ($i = 0; $i < count($data2); $i++) {
                        $res = $csv->writeData($data2[$i]);
                    }
                }
            } else {
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
                    for ($i = 0; $i < $len1; $i++) {
                        if ($i < $len1 - 1) {
                            $sql11 .= $data1[$i]->name . ",";
                        } else if ($i == $len1 - 1) {
                            $sql11 .= $data1[$i]->name;
                        }

                    }
                    for ($i = 0; $i < $len1; $i++) {
                        if ($i < $len1 - 1) {
                            $sql12 .= $data1[$i]->player_id . ",";
                        } else if ($i == $len1 - 1) {
                            $sql12 .= $data1[$i]->player_id;
                        }
                    }
                    $league_name1 = isset($data1[0]->league_name) ? $data1[0]->league_name : '';
                    for ($i = 0; $i < $len1; $i++) {
                        $res1 = array(
                            'server_name' => $server->server_name,
                            'league_name' => isset($data1[$i]->league_name) ? $data1[$i]->league_name : '',
                            'name' => isset($data1[$i]->name) ? $data1[$i]->name : '',
                            'player_id' => isset($data1[$i]->player_id) ? $data1[$i]->player_id : '',
                            'guanjun' => '冠军',
                            'battle_time' => date('Y-m-d H:i:s', time())
                        );
                        //Log::info(var_export($data1[$i], true));
                        $res = $csv->writeData($res1);
                        //unset($res1);
                    }


                    $sql1 = $sql11 . "|" . $sql12;

                    $len2 = count($data2);
                    for ($i = 0; $i < $len2; $i++) {
                        if ($i < $len2 - 1) {
                            $sql21 .= $data2[$i]->name . ",";
                        } elseif ($i == $len2 - 1) {
                            $sql21 .= $data2[$i]->name;
                        }
                    }
                    for ($i = 0; $i < $len2; $i++) {
                        if ($i < $len2 - 1) {
                            $sql22 .= $data2[$i]->player_id . ",";
                        } elseif ($i == $len2 - 1) {
                            $sql22 .= $data2[$i]->player_id;
                        }
                    }
                    $league_name2 = isset($data2[0]->league_name) ? $data2[0]->league_name : '';
                    for ($i = 0; $i < $len2; $i++) {
                        $res2 = array(
                            'server_name' => $server->server_name,
                            'league_name' => isset($data2[$i]->league_name) ? $data2[$i]->league_name : '',
                            'name' => isset($data2[$i]->name) ? $data2[$i]->name : '',
                            'player_id' => isset($data2[$i]->player_id) ? $data2[$i]->player_id : '',
                            'guanjun' => '亚军',
                            'battle_time' => date('Y-m-d H:i:s', time())
                        );
                        //Log::info(var_export($data2[$i], true));
                        $res = $csv->writeData($res2);
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
        if ($res) {
            $data = array(
                'now' => $now
            );
            return Response::json($data);
        } else {
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
            if ($type == "open" || $type == "close") { //开启或者关闭
                $response = $api->firstRechargeOperate($type);
                if ($response->active) { // 返回成功
                    if ($type == "open") {
                        $result[] = array(
                            'statu' => 'OK',
                            'msg' => $server->server_name . '   开启OK' . '---' . $response->active
                        );
                    }
                } else {
                    if ($type == "close") {
                        $result[] = array(
                            'statu' => 'OK',
                            'msg' => $server->server_name . '    关闭OK' . '---' . $response->active
                        );
                    }
                }

            } else {
                $response = $api->firstRechargeOperate($type);
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

    public function opencomments(){ //夜夜三国评论页面get方法
        $data = array(
            'content' => View::make('serverapi.yysg.gm.yysg_comment')
        );
        return View::make('main', $data);
    }

    public function getcomments(){ //夜夜三国获取武将评论

        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'current_page' => 'required',
            'num_per_page' => 'required',
            'show_delete' => 'required',
            'is_like' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }

        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $current_page = (int)Input::get('current_page');
        $num_per_page = (int)Input::get('num_per_page');
        $player_id = (int)Input::get('player_id');
        $table_id = (int)Input::get('table_id');
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->first();
        }

        if((int)Input::get('show_delete')==1){ //代表显示已删除的评论
            $show_delete = true;
        }elseif((int)Input::get('show_delete')==0){ //代表显示未删除的评论
            $show_delete = false;
        }

        if((int)Input::get('is_like')==1){ //代表按照赞数排序显示评论
            $is_like = true;
        }elseif((int)Input::get('is_like')==0){ //代表按照时间排序显示评论
            $is_like = false;
        }

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        $response = $api->getyysgcomments($current_page, $num_per_page, $show_delete, $is_like, $game_id, $player_id, $table_id, $platform_id);
        if(empty($response->list)){
            return Response::json(array('error'=>'no data!'), 403);
        }
        foreach ($response->list as &$value) {
            $value->created_at = date('Y-m-d H:i:s', $value->created_at);
        }
        //Log::info(var_export($response,true));
        return Response::json($response->list);
    }

    public function deal_comments(){ //夜夜三国处理武将评论

        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'comment_id' => 'required',
            'deal_type' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }

        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->first();
        }
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        $comment_id = (int)Input::get('comment_id');
        $deal_type = (int)Input::get('deal_type');

        if($deal_type==1){//1代表修改赞数
            $likes_num = (int)Input::get('likes_num');
            $response = $api->changecommentlikesnum($comment_id, $likes_num, $platform_id);
        }elseif($deal_type==2){//2代表删除评论
            $response = $api->deletecomment($comment_id, true, $platform_id);
        }elseif($deal_type==3){//3代表撤销删除
            $response = $api->deletecomment($comment_id, false, $platform_id);
        }
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
    public function announceIndex()
    {
        
        //$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
        /*$position = array(
            '1' => Lang::get('serverapi.announce_pos_center'),
            '2' => Lang::get('serverapi.announce_pos_chat'),
            '3' => Lang::get('serverapi.announce_pos_all')
        );*/
        $data = array(
            'content' => View::make('serverapi.mnsg.announce.index', array(
                'servers' => $servers
                //'pos' => $position
            )),
        );
        return View::make('main', $data);
    }

    public function announceSend()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error'),
        );
        $rules = array(
            'server_id' => 'required',
            'content' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }

        $server_ids = Input::get('server_id');
        $result = array();
        
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 404);
            }
            if($game_id!=$server->game_id){
                return Response::json(array('error'=>'please check the current platform and servers!'), 403);
            }
            $content = trim(Input::get('content'));             
    
            //Log::info(var_export(Input::all(), true));
            if('mnsg' == $game->game_code || 'yysg' == $game->game_code) {
                $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                $response = $api->announceSend($content, $platform_id, $game_id, $game->game_code);
            }
            if(isset($response))
            {
                // Cache::add('promotion-close-time', $end_time, 100000);
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . $string=implode("房间：",$response) . "\n",
                        'status' => 'ok'
                );
            } else
            {
                $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
                        'status' => 'error'
                );
            }
        }
        $msg = array(
                'result' => $result
        );
        return Response::json($msg);
    }
    public function ActivityIndex()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $game_code = $game->game_code;
        $table = $this->initTableActivity();
        $activity = $table->getData();
        $servers = Server::CurrentGameServers()->get();
        $data = array(
                'content' => View::make('serverapi.yysg.giftbag.activity_index', array(
                        'game_code'=>$game_code,
                        'activity'=>$activity,
                        'servers'=>$servers,
                ))
        );
        return View::make('main', $data);
    }
    public function ActivityOpen()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));

        if($start_time >= $end_time)
        { // to add
            $msg = array(
                    'code' => Config::get('errorcode.unknow'),
                    'error' => Lang::get('error.basic_time_error')
            );
            return Response::json($msg, 404);
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $result = array();
        $activity_ids = Input::get('activity_id');
        $is_lang = Input::get('is_lang');
        $server_ids = Input::get('server_internal_ids');
        if(empty($server_ids)){
            return Response::json(array('error'=>'Did you select a server?'), 403);
        }

        if(empty($activity_ids)){
            return Response::json(array('error'=>'Did you select an activity?'), 403);
        }

        $table = $this->initTableActivity();
        $activity = $table->getData();
        $id2name = array();
        foreach ($activity as $value) {
            $id2name[$value->id]= $value->name;
        }

        $strtostore = (Input::get('start_time')).'|'.(Input::get('end_time')).'|';

        foreach ($activity_ids as $key => $value) {
            $table_ids[]= (int)$value;
        }
        //Log::info(var_export($table_ids,true));die();

        if('yysg' == $game->game_code){
            if(count($server_ids)>1){
                return Response::json(array('error'=>'每次只能选择一个服！'), 403);
            }
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_ids[0])->first();
            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->openActivity($table_ids, $start_time, $end_time, $platform_id, $game_id, $is_lang);
        }elseif('mnsg' == $game->game_code){
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_ids[0])->first();
            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->mnsgopenActivity($table_ids, $start_time, $end_time, $platform_id, $server_ids);
        }
        if(isset($response->error_code) && isset($response->error)){
            return Response::json(array('error'=>'操作失败'), 403);
        }
        $databasedata = array();
        if(isset($response))
        {
            $str = '';$str2 = '';
            if('mnsg' == $game->game_code){
                if(!isset($response->regions)){
                    return Response::json(array('error'=>'操作失败'), 403);
                }
                foreach ($response->regions as $value) {
                    $str .= $value.',';
                }
                $str .= '服的活动:';
                $response = $response->ids;
            }
            foreach ($response as $key => $value) {
                if($value == 'true' || $value > 0){
                    if('mnsg' == $game->game_code){
                        $str = $str . $value . ',';
                        $tmp_str = $strtostore.$value.':'.$id2name[$value];
                        $tmp_str .= '|';
                        foreach ($server_ids as $server_id) {
                            $tmp_str .= $server_id.' ';
                        }
                    }elseif('yysg' == $game->game_code){
                        $str = $str . $key . ',';
                        $tmp_str = $strtostore.$key.':'.$id2name[$key];
                        $tmp_str .= '|1';                    
                    }
                    $databasedata[] = array(
                        'log_key' => 'yysg-activity',
                        'desc' => $tmp_str,
                        'user_id' => Auth::user()->user_id,
                        'game_id' => $game_id,
                        'created_at' => time(),
                        'updated_at' => $end_time,
                        );
                    unset($tmp_str);
                }else{
                    $str2 = $str2 . $key . ',';
                }
            }
            $result[] = array(
                    'msg' => $str . ' 开启OK' .  "\n",
                    'status' => 'ok'
            );

        } else
        {
            $result2[] = array(
                    'msg' => '开启活动 : ' . 'error' . "\n",
                    'status' => 'error'
            );
        }
        $msg = array(
                'result' => isset($result) ? $result : array(),
                'result2' => isset($result2) ? $result2 : array(),
        );
        $result = EastBlueLog::insert($databasedata);   //将数据记录到eastblue的数据库中
        return Response::json($msg);
    }
        public function ActivityClose()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $result = array();
        $activity_ids = Input::get('activity_id');
        $server_ids = Input::get('server_internal_ids');
        $is_lang = Input::get('is_lang');
        if(empty($server_ids)){
            return Response::json(array('error'=>'Did you select a server?'), 403);
        }
        if(empty($activity_ids)){
            return Response::json(array('error'=>'Did you select an activity?'), 403);
        }
        foreach ($activity_ids as $key => $value) {
            $table_ids[]= (int)$value;
        }

        $table = $this->initTableActivity();
        $activity = $table->getData();
        $id2name = array();
        foreach ($activity as $value) {
            $id2name[$value->id]= $value->name;
        }

        //Log::info(var_export($table_ids,true));die();
        if('mnsg' == $game->game_code){
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_ids[0])->first();
            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->mnsgcloseActivity($table_ids, $platform_id, $server_ids);
        }else{
            if(count($server_ids)>1){
                return Response::json(array('error'=>'每次只能选择一个服！'), 403);
            }
            if('yysg' == $game->game_code){
                $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_ids[0])->first();
            }else{
                $server = Server::where('game_id', $game_id)->first();
            }
            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->closeActivity($table_ids, $platform_id, $game_id, $is_lang);
        }
        if(isset($response->error_code) && isset($response->error)){
            return Response::json(array('error'=>'操作失败'), 403);
        }
        if(isset($response))
        {
            $str = '';$str2 = '';
            if('mnsg' == $game->game_code){
                if(!isset($response->regions)){
                    return Response::json(array('error'=>'操作失败'), 403);
                }
                foreach ($response->regions as $value) {
                    $str .= $value.',';
                }
                $str .= '服的活动:';
                $response = $response->ids;
            }
            foreach ($response as $key => $value) {
                if($value == 'true' || $value > 0){
                    if('mnsg' == $game->game_code){
                        $str = $str . $value . ',';
                        $tmp_str = $value.':'.$id2name[$value];
                        $tmp_str = $tmp_str.'|';
                        $database = EastBlueLog::where('log_key', 'yysg-activity')->where('game_id', $game_id)->where('desc', 'like', '%'.$tmp_str.'%')->update(array('log_key' => 'yysg-activity-done','updated_at' => 0));
                        unset($tmp_str);
                    }else{
                        $str = $str . $key . ',';
                        $tmp_str = $key.':'.$id2name[$key];
                        $database = EastBlueLog::where('log_key', 'yysg-activity')->where('game_id', $game_id)->where('desc', 'like', '%'.$tmp_str)->update(array('log_key' => 'yysg-activity-done','updated_at' => 0));
                        unset($tmp_str);
                    }
                }else{
                    $str2 = $str2 . $key . ',';
                }
            }
            $result[] = array(
                    'msg' => $str .' 关闭OK' ."\n",
                    'status' => 'ok'
            );
        } else
        {
            $result2[] = array(
                    'msg' => '关闭活动 : ' . 'error' . "\n",
                    'status' => 'error'
            );
        }
        $msg = array(
                'result' => isset($result) ? $result : array(),
                'result2' => isset($result2) ? $result2 : array(),
        );
        return Response::json($msg);
    }

    public function ActivityCheck(){
        $time = time();
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $result = array();
        if('mnsg' == $game->game_code){
            $server_ids = Input::get('server_internal_ids');
            $table = $this->initTableActivity();
            $activity = $table->getData();
            $id2name = array();
            foreach ($activity as $value) {
                $id2name[$value->id]= $value->name;
            }
            if(empty($server_ids)){
                return Response::json(array('error'=>'Did you select a server?'), 403);
            }
            foreach ($server_ids as $server_id) {
                $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_id)->first();
                $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                $tmp_result = $api->mnsgcheckActivity($server_id);
                foreach ($tmp_result as $value) {
                    $result[] = array(
                        'start_time' => date('Y-m-d H:i:s' ,$value->start_time),
                        'end_time' => date('Y-m-d H:i:s' ,$value->end_time),
                        'activity' => isset($id2name[$value->id]) ? $id2name[$value->id] : $value->id,
                        'servers' => $server->server_name,
                        'operator' => '',                        
                        );
                }
            }
        }else{
            $response = EastBlueLog::where('log_key', 'yysg-activity')->where('game_id', $game_id)->where('updated_at', '>', $time)->get();
            foreach ($response as $value) {
                $desc = $value->desc;
                try {
                    $desc = explode('|', $desc, 4);
                } catch (Exception $e) {
                    $desc = explode('|', $desc, 3);
                }
                $result[] = array(
                    'start_time' => $desc[0],
                    'end_time' => $desc[1],
                    'activity' => $desc[2],
                    'servers' => isset($desc[3]) ? $desc[3] : 1,
                    'operator' => User::find($value->user_id)->username,
                    );
            }
        }
         return Response::json($result);
    }

    public function ActivityAnnounce()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform = Platform::find(Session::get('platform_id'));
        $game_code = $game->game_code;
        $table = $this->initTableActivity();
        $activity = $table->getData();
        $data = array(
                'content' => View::make('serverapi.yysg.giftbag.activity_announce', array(
                        'game_code'=>$game_code,
                        'activity' => $activity,
                        'platform' => $platform,
                        'game_id' => $game_id,
                ))
        );
        return View::make('main', $data);
    }
    public function ActivityAnnounceRelease()
    {
       $msg = array(
               'code' => Config::get('errorcode.unknow'),
               'error' => Lang::get('error.basic_input_error')
       );
       $start_time = strtotime(Input::get('start_time'));
       $end_time = strtotime(Input::get('end_time'));

       if($start_time >= $end_time)
       { // to add
           $msg = array(
                   'code' => Config::get('errorcode.unknow'),
                   'error' => Lang::get('error.basic_time_error')
           );
           return Response::json($msg, 404);
       }
       $game_id = Session::get('game_id');
       $platform_id = Session::get('platform_id');
       $result = array();
       $title = Input::get('announce_title');
       $type = (int)Input::get('choice');
       $banner = (String)Input::get('announce_banner');
       $url = (String)Input::get('announce_url');
       $is_open = 1;
       $is_show = (int)Input::get('is_show');
       $activity_id = (int)Input::get('activity_id');
        $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
       if(!in_array($game_id, Config::get('game_config.yysggameids'))){
            return Response::json(array('error'=>'the current game is not yysg!'),403);
       }
       $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
       $response = $api->releaseAnnounce($title, $type, $url, $banner, $start_time, $end_time, $is_open, $game_id, $platform_id, $is_show, $activity_id);
       if(isset($response->result) && $response->result == 'OK')
       {
           $result[] = array(
                   'msg' => '发布' . $response->result .  "\n",
                   'status' => 'ok'
           );

       } else
       {
           Log::info('YYSG(0x7002):' . var_export($response,true));
           $result[] = array(
                   'msg' => '发布 : ' . 'error' . "\n",
                   'status' => 'error'
           );
       }
       $msg = array(
               'result' => $result
       );
       return Response::json($msg);
    }
     public function ActivityAnnounceLook()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_not_found')
        );
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $type = (int)Input::get('type');
        $limit = 20;//默认获得最近发布的公告条数
        $result = array();
      
        $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        $response = $api->ActivityAnnounceLook($type, $limit, $game_id, $platform_id);
        //Log::info(var_export($response,true));die();
        if(isset($response->error) && $response->error){
            $msg['error'] = 'response error';
            return Response::json($msg,404);
        }elseif(isset($response->list) && $response->list){
            foreach ($response->list as $value) {
                $result[] = array(
                    'id'=>$value->id,
                    'is_open'=>$value->is_open,
                    'title'=>$value->title,
                    'url'=>$value->url,
                    'banner'=>$value->banner,
                    'type'=>$value->type,
                    'created_time'=>date('Y-m-d H:i:s',$value->created_at),
                    'update_time'=>(0!=$value->updated_at) ? date('Y-m-d H:i:s',$value->updated_at) : '',
                    'start_time'=>date('Y-m-d H:i:s',$value->start_time),
                    'end_time'=>date('Y-m-d H:i:s',$value->end_time),
                    'activity_id'=>$value->activity_id,
                    'is_show'=>$value->is_begin,
                ); 
            }
           return Response::json((object)$result);
        }else{
            return Response::json($msg, 404);
        }
    }
    public function ActivityAnnounceUpdate()
    {
       $msg = array(
               'code' => Config::get('errorcode.unknow'),
               'error' => Lang::get('error.basic_input_error')
       );
       $start_time = strtotime(Input::get('start_time'));
       $end_time = strtotime(Input::get('end_time'));

       if($start_time > $end_time)
       { // to add
           $msg = array(
                   'code' => Config::get('errorcode.unknow'),
                   'error' => Lang::get('error.basic_time_error')
           );
           return Response::json($msg, 404);
       }
       $game_id = Session::get('game_id');
       $platform_id = Session::get('platform_id');
       $result = array();
       $title = (String)Input::get('announce_title');
       $type = (int)Input::get('choice');
       $banner = (String)Input::get('announce_banner');
       $url = (String)Input::get('announce_url');
       $is_open = (int)Input::get('is_open');
       $id = (int)Input::get('id');
       $is_show = (int)Input::get('is_show');

        $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();

       $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
       $response = $api->updateAnnounce($id, $title, $type, $url, $banner, $start_time, $end_time, $is_open, $platform_id, $is_show);
       if(isset($response->result) && $response->result == 'OK' && $is_open == 1)
       {
           $result[] = array(
                   'msg' => '更新' . $response->result .  "\n",
                   'status' => 'ok'
           );

       }elseif(isset($response->result) && $response->result == 'OK' && $is_open == 0){
           $result[] = array(
                   'msg' => '删除' . $response->result .  "\n",
                   'status' => 'ok'
           );
       }else{
           Log::info('GX:' . var_export($response,true));
           $result[] = array(
                   'msg' => '操作 : ' . 'error' . "\n",
                   'status' => 'error'
           );
       }
       $msg = array(
               'result' => $result
       );
       return Response::json($msg);
    }

    public function logindeviceIndex(){
        $uid = (int)Input::get('uid');
        $game_id = Session::get('game_id');
        $operations = Operation::where('game_id', $game_id)->where('operation_type', 'ban_device')->where('operate_time', '>', (time() - 30*86400))->selectRaw("from_unixtime(operate_time) as time, extra_msg")->get();
        $data = array(
            'content' => View::make('serverapi.yysg.player.logindevice',array('uid' => $uid, 'operations' => $operations))
        );
        return View::make('main', $data);
    }

    public function logindeviceSerach(){ //玩家UID和设备号互查,对应官网的login_device表

        $platform_id = (int)Session::get('platform_id');
        $uid = Input::get('uid');
        $device_id = Input::get('device_id');
        $limit_type = Input::get('limit_type');
        $game_id = (int)Session::get('game_id');
        $game = Game::find($game_id);
        $baned = (Input::get('baned')) ? 1 : 0;

        if(in_array($limit_type, array(-1,1))){
            return $this->switch_device_statu($device_id, $limit_type, $platform_id, $game_id);
        }
        if(empty($uid) && empty($device_id) && !$baned){
            return Response::json(array('error'=>'UID或设备号请至少输入一个!'), 403);
        }
        if(2 != $game->game_type){
            return Response::json(array('error'=>'Not a mobile game!'), 403);
        }

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        if($baned){
            $response = $api->getlogindeviceinfo($game_id, $platform_id, '', '', 1);
        }elseif(!empty($uid)){
            $response = $api->getlogindeviceinfo($game_id, $platform_id, $uid, '', '');
        }elseif(!empty($device_id)){
            $response = $api->getlogindeviceinfo($game_id, $platform_id, '', $device_id, '');
        }

        if($response->http_code != 200){
            return $api->sendResponse();
        }
        $body = $response->body;
        if(!empty($body)){
            foreach ($body as $key => $value) {
                $result[] = array(
                    'uid' => $value->uid,
                    'device_id' => $value->device_id,
                    'device_type' => $value->device_type,
                    'os_type' => $value->os_type,
                    'create_time' => date('Y-m-d H:i:s', $value->create_time),
                    'login_time' => date('Y-m-d H:i:s', $value->login_time),
                    'limit_type' => $value->limit_type ? Lang::get('slave.baned') : Lang::get('slave.normal'),
                );
            }
        }
        return Response::json($result);
    }

    private function switch_device_statu($device_id, $limit_type, $platform_id, $game_id){
        if(-1 == $limit_type){
            $limit_type = 0;
        }

        $platform = Platform::find($platform_id);

        $platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);

        $params = array(
                'time' => time(),
                'device_id' => $device_id,
                'limit_type' => $limit_type,
                'game_id' => $game_id,
            );

        $result = $platform_api->change_device_statu($params);

        if(200 == $result->http_code){
            $result = $result->body;
            if(0 == $result->error){
                $data2store = array(   //将操作插入数据库中
                    'operate_time' => time(),
                    'game_id' => $game_id,
                    'player_name' => '',
                    'player_id' => '',
                    'operator' => Auth::user()->user_id,
                    'server_name' => '',
                    'operation_type' => 'ban_device',
                    'extra_msg' => Lang::get('slave.operator').':'.Auth::user()->username.' device_id:'.$device_id.' operation:'.($limit_type ? Lang::get('slave.ban') : Lang::get('slave.un_ban')),
                );
                Operation::insert($data2store);
                unset($data2store);
                return Response::json($result);
            }else{
                return Response::json($result, 403);
            }
        }else{
            $result = $result->body;
            return Response::json($result, 403);
        }
    }

    public function switchShowServer(){ //萌娘三国开新服以及切换服务器在游戏中是否可见
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('serverapi.mnsg.server.openandswitch', array(
                    'servers' => $servers
                ))
        );
        return View::make('main', $data);
    }

    public function switchShowServerDo(){
        $operation_type = Input::get('operation_type'); //0代表切换服务器显示状态，1代表新开服务器
        if('0' == $operation_type){ //切换服务器显示状态
            $is_hide = (int)Input::get('is_hide');
            if(in_array($is_hide, array(0,1))){
                $server_id = (int)Input::get('server_id');
                if ($server_id > 0) {
                    $server = Server::find($server_id);
                    if($server){
                        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                        $result = $api->switchShowServer($is_hide, $server->server_internal_id);
                        if (isset($result->result) && $result->result == 'OK') {
                            return Response::json(array('msg'=>'操作成功!'), 200);
                        }else{
                            if (isset($result->error)) {
                                return Response::json(array('error'=>$result->error), 403);
                            }else{
                                Log::info('mnsg----switchShowServer-----bad----return_result----'.var_export($result, true));
                                return Response::json(array('error'=>'异常的返回值，请联系技术查看日志'), 403);
                            }
                        }
                    }else{
                        return Response::json(array('error'=>'无效的服务器!'), 403);
                    }
                }else{
                    return Response::json(array('error'=>'无效的参数!'), 403);
                }
            }else{
                return Response::json(array('error'=>'无效的参数!'), 403);
            }
        }elseif('1' == $operation_type){
            $server_name = Input::get('server_name');
            if('' == $server_name){
                return Response::json(array('error'=>'请输入新服的名字'), 403);
            }
            $server_id = (int)Input::get('server_id');
            if ($server_id > 0) {
                $server = Server::find($server_id);
                if($server){
                    $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                    $result = $api->mnsgOpenServer($server_name, $server->server_internal_id);
                    if (isset($result->result) && $result->result == 'OK') {
                        return Response::json(array('msg'=>'操作成功!'), 200);
                    }else{
                        if (isset($result->error)) {
                            return Response::json(array('error'=>$result->error.'，服务器可能已经开启'), 403);
                        }else{
                            Log::info('mnsg----mnsgOpenServer-----bad----return_result----'.var_export($result, true));
                            return Response::json(array('error'=>'异常的返回值，请联系技术查看日志'), 403);
                        }
                    }
                }else{
                    return Response::json(array('error'=>'无效的服务器!'), 403);
                }
            }else{
                return Response::json(array('error'=>'无效的参数!'), 403);
            }            
        }
    }

    public function editplayereconomyIndex(){   //手游修改玩家的货币值
        $game = Game::find(Session::get('game_id'));
        $logs = EastBlueLog::leftJoin('users', 'users.user_id', '=', 'log.user_id')->where('game_id', Session::get('game_id'))->where('log_key', 'mobilechangeYuanbao')
                            ->orderBy('log.created_at', 'desc')->take(30)->selectRaw("users.username, log.created_at, log.desc")->get();
        $data = array(
            'content' => View::make('serverapi.mnsg.player.editeconomy', array(
                    'game_code' => $game->game_code,
                    'logs'  =>  $logs
                ))
        );
        return View::make('main', $data);
    }

    public function editplayereconomyDo(){
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->orderBy('server_internal_id', 'asc')->first();
        }
        if(!$server){
            return Response::json(array('error'=>'无效的服务器!'), 403);
        }
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        $player_id = (int)Input::get('player_id');
        if(!$player_id){
            return Response::json(array('error'=>'无效玩家ID'), 403);
        }

        if('mnsg' == $game->game_code){
            $name = array(
                'mana' =>  Lang::get('slave.tongqian'),
                'crystal'  =>  Lang::get('slave.crystalmnsg'),
                'energy'   =>  Lang::get('slave.energy'),
                'top_coin' =>  Lang::get('slave.top_coin'),
                'arena_coin'    =>  Lang::get('slave.arena_coin'),
                'march_coin'    =>  Lang::get('slave.march_coin'),
                'guild_coin'    =>  Lang::get('slave.guild_coin'),
                'region_coin'   =>  Lang::get('slave.region_coin'),
                );
            $mana = (int)Input::get('mana');
            $crystal = (int)Input::get('crystal');
            $energy = (int)Input::get('energy');
            $top_coin = (int)Input::get('top_coin');
            $arena_coin = (int)Input::get('arena_coin');
            $march_coin = (int)Input::get('march_coin');
            $guild_coin = (int)Input::get('guild_coin');
            $region_coin = (int)Input::get('region_coin');
            foreach ($name as $key => $value) { //每个值都不能超过10000
                if($$key > 10000 || $$key < -10000){
                    if('mana' == $key){
                        if($$key > 100000000 || $$key < -100000000){
                            return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
                        }
                    }else{
                        return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
                    }
                }
            }
            unset($key);
            unset($value);
            $result = $api->mnsgeditplayereconomy($player_id, $mana, $energy, $top_coin, $arena_coin, $march_coin, $crystal, $guild_coin, $region_coin);
            if(!isset($result->error_code) && $result != NULL){
                $str = '修改成功，玩家'.$player_id.'目前有';
                $yuanbao_log = new EastBlueLog();
                $yuanbao_log->log_key = 'mobilechangeYuanbao';
                $yuanbao_log->created_at = time();
                $yuanbao_log->user_id = Auth::user()->user_id;
                $yuanbao_log->game_id = Session::get('game_id');
                $tmp_str = $player_id . '|' . '萌娘三国修改玩家经济' . '|';
                $modify = 0;
                foreach ($name as $key => $value) {
                    if($$key != 0){
                        $tmp_str .= '修改'.$value.$$key.' ';
                        $modify++;
                    }
                    if(isset($result->$key)){
                        $str .= $value.':'.$result->$key.' ';
                    }
                }
                $yuanbao_log->desc =  $tmp_str;
                if($modify == 0){
                    unset($yuanbao_log);
                }else{
                    $yuanbao_log->save();
                }
                return Response::json(array('msg'=>$str), 200);
            }else{
                return $api->sendResponse();
            }
        }elseif('yysg' == $game->game_code){
            $mana = (int)Input::get('mana');
            $crystal = (int)Input::get('crystal');
            if($crystal > 10000 || $crystal < -10000){
                return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
            }
            $energy = (int)Input::get('energy');
            if($mana > 0 || $energy>0){
                return Response::json(array('error'=>'夜夜三国铜钱和体力只能扣除'), 403);
            }
            $str = '修改id为'.$player_id.'的玩家';
            if($mana != 0){
                $result = $api->changeTongqian($player_id, $mana, $platform_id);
                if(!isset($result->error_code) && $result != NULL){
                    $yuanbao_log = new EastBlueLog();
                    $yuanbao_log->log_key = 'mobilechangeYuanbao';
                    $yuanbao_log->created_at = time();
                    $yuanbao_log->user_id = Auth::user()->user_id;
                    $yuanbao_log->game_id = Session::get('game_id');
                    $yuanbao_log->desc = $player_id . '|' . '夜夜三国修改铜钱' . '|' . abs($mana);
                    $yuanbao_log->save();
                    $str .= ' 修改铜钱 '.$mana.' 成功';
                }else{
                    $str .= ' 修改铜钱 '.$mana.' 失败';
                }
                unset($result);
            }
            if($crystal != 0){
                $result = $api->changeYuanbao($player_id, $crystal, $platform_id);
                if(!isset($result->error_code) && $result != NULL){
                    $yuanbao_log = new EastBlueLog();
                    $yuanbao_log->log_key = 'mobilechangeYuanbao';
                    $yuanbao_log->created_at = time();
                    $yuanbao_log->user_id = Auth::user()->user_id;
                    $yuanbao_log->game_id = Session::get('game_id');
                    $yuanbao_log->desc = $player_id . '|' . '夜夜三国修改元宝' . '|' . abs($crystal);
                    $yuanbao_log->save();
                    $str .= ' 修改元宝 '.$crystal.' 成功';
                }else{
                    $str .= ' 修改元宝 '.$crystal.' 失败';
                }
                unset($result);
            }
            if($energy != 0){
                $result = $api->changeTili($player_id, $energy, $platform_id);
                if(!isset($result->error_code) && $result != NULL){
                    $yuanbao_log = new EastBlueLog();
                    $yuanbao_log->log_key = 'mobilechangeYuanbao';
                    $yuanbao_log->created_at = time();
                    $yuanbao_log->user_id = Auth::user()->user_id;
                    $yuanbao_log->game_id = Session::get('game_id');
                    $yuanbao_log->desc = $player_id . '|' . '夜夜三国修改体力' . '|' . abs($energy);
                    $yuanbao_log->save();
                    $str .= ' 修改体力 '.$energy.' 成功';
                }else{
                    $str .= ' 修改体力 '.$energy.' 失败';
                }
                unset($result);
            }
            return Response::json(array('msg'=>$str), 200);
        }else{
            return Response::json(array('error'=>'当前游戏不适用于此功能'), 403);
        }
    }

    public function RepairPlayerShopIndex(){    //萌娘三国修复玩家商店功能
        $data = array(
            'content' => View::make('serverapi.mnsg.player.repairshop', array(
                ))
        );
        return View::make('main', $data);
    }

    public function RepairPlayerShopDo(){
        $player_ids = Input::get('player_ids');
        $player_ids = explode("\n", $player_ids);
        if(count($player_ids) == 0){
            return Response::json(array('error'=>'没有输入数据'), 403);
        }
        foreach ($player_ids as $key => $value) {
            if(0 == $value || '' == $value){
                unset($player_ids[$key]);
            }
        }
        $player_ids = array_unique($player_ids);
        $server = Server::where('game_id', Session::get('game_id'))->where('is_server_on', 1)->first();
        if(!$server){
            return Response::json(array('error'=>'无效的服务器'), 403);
        }
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

        $result = $api->mnsgrepairplayershop($player_ids);

        $msg = '';

        if('' == $result || !isset($result[0])){
            return Response::json(array('error'=>'操作异常'), 403);
        }

        foreach ($result as $value) {
            $msg .= ' '.$value;
        }

        $msg .= '修复成功';

        return Response::json(array('msg'=> $msg), 200); 
    }

    public function UpdateNewIndex(){   //萌娘三国版本预告功能
        $data = array(
            'content' => View::make('serverapi.mnsg.announce.update_new', array(
                ))
        );
        return View::make('main', $data);
    }

    public function UpdateNewDeal(){
        $title = Input::get('title');
        $version = Input::get('version');
        if('' == $title){
            return Response::json(array('error'=> Lang::get('basic.title').Lang::get('basic.input_error')), 403);
        }
        if('' == $version){
            return Response::json(array('error'=> Lang::get('basic.version').Lang::get('basic.input_error')), 403);
        }

        $contents = array();
        for($i=1;$i<=4;$i++){
            $tmp_title = Input::get('title'.$i);
            if('' == $tmp_title){
                continue;
            }
            $tmp_title = trim($tmp_title);
            $tmp_content = Input::get('content'.$i);
            $tmp_content = trim($tmp_content);
            $tmp_content = explode("\n", $tmp_content);
            $content = array();
            foreach ($tmp_content as $key => $value) {
                if('' != $value){
                    $content[] = $value;
                }
            }
            $contents[] = array(
                'contents' => $content,
                'title' => $tmp_title,
                );
            unset($tmp_title);
            unset($tmp_content);
            unset($content);
        }


        $time = date('Y.m.d');

        $server = Server::where('game_id', Session::get('game_id'))->orderBy('server_internal_id', 'asc')->first();
        if(!$server){
            return Response::json(array('error'=> Lang::get('basic.invalid').Lang::get('basic.server')), 403);
        }

        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

        $result = $api->MnsgUpdateNew($title, $contents, $time, $version);

        if(isset($result->result) && 'OK' == $result->result){
            return Response::json(array('msg'=> Lang::get('basic.set_success')));
        }else{
            return Response::json(array('error'=> Lang::get('basic.set_fail')), 403);
        }
    }

    public function CountPartnerLogIndex(){ //手游查询武将召唤情况
        $game = Game::find(Session::get('game_id'));
        $wjs = Lang::get($game->game_code.'wj');
        if($wjs == $game->game_code.'wj'){
            $wjs = array();
        }
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('slaveapi.player.createpartner', array(
                'wjs' => $wjs,
                'servers' => $servers,
                ))
        );
        return View::make('main', $data);        
    }

    public function CountPartnerLog(){
        $server_ids = Input::get('server_ids');
        if(empty($server_ids)){
            return Response::json(array('error'=> Lang::get('slave.server').Lang::get('basic.input_error')), 403);
        }
        $wjids = Input::get('wj_ids');
        if(empty($wjids)){
            return Response::json(array('error'=> Lang::get('slave.partner').Lang::get('basic.input_error')), 403);
        }
        if(in_array('0', $wjids)){  //所有武将
            $wjids = array();
        }
        $start_time = (int)strtotime(Input::get('start_time'));
        $end_time = (int)strtotime(Input::get('end_time'));

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $result = array();
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            $tmp_result = $slaveapi->getcountpartnerlog($game_id, $server->server_internal_id, $wjids, $start_time, $end_time);
            if('200' == $tmp_result->http_code){
                $body = $tmp_result->body;
                foreach ($body as $value) {
                    if(isset($result[Lang::get($game->game_code.'wj.'.$value->table_id)])){
                        $result[Lang::get($game->game_code.'wj.'.$value->table_id)] += $value->times;
                    }else{
                        $result[Lang::get($game->game_code.'wj.'.$value->table_id)] = $value->times;
                    }
                }
            }
            unset($server);
            unset($tmp_result);
            if(in_array($game_id, Config::get('game_config.yysggameids'))){
                break;
            }
        }

        arsort($result);
        $response = array();
        $count = 0;
        foreach ($result as $key => $value) {
            $response[$count++] = array(
                'partner' => $key,
                'count' => $value,
                );
        }
        unset($result);
        if(count($response)){
            return Response::json($response);
        }else{
            return Response::json(array('error'=> Lang::get('basic.no_result')), 403);
        }
    }

    public function gmMassTalk()
    {
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $server = Server::where('game_id', $game_id)->orderBy('server_internal_id', 'asc')->first();
        if(!$server){
            App::abort(404);
        }
        $data = array(
            'content' => View::make('serverapi.yysg.gm.MassTalk', array())
        );
        return View::make('main', $data);
    }

    public function gmMassMessageSend()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'gift_data' => 'required',
            'msg' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        if('yysg' == $game->game_code){
            $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
            $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        }else{
            $server = Server::where('game_id', $game_id)->orderBy('server_internal_id', 'asc')->first();
        }
        $reply_message = trim(Input::get('msg'));
        $name_or_id = input::get('name_or_id');

        $gift_datas = Input::get('gift_data');
        $gift_datas = explode("\n", $gift_datas);
        foreach ($gift_datas as &$v) {
            $v = trim($v);
        }
        unset($v);
        $gift_datas = array_unique($gift_datas);
        $result = array();
        $ok = array();
        $error = array();
        $gm = array();
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port); //将GM回复信息发送到游戏服务器
        foreach ($gift_datas as $gift_data) {
            $gift_data = explode("\t", $gift_data, 1);
            if (count($gift_data) != 1) {
                $error[] = $gift_data[0] . ': 输入格式错误！ ';
                continue;
            }
            if('2' == $name_or_id){
                $player = $slave_api->getplayeridbyname($game_id, $gift_data[0], $server->server_internal_id, $platform_id);
                if(200 == $player->http_code){
                    $gift_data[0] = (int)$player->body[0]->player_id;
                }else{
                   $error[] = $gift_data[0] . "Not Found player_id";
                   continue; 
                }      
            }
            $player_id = $gift_data[0];
            $response = $api->replyGMTalk($player_id, $reply_message, $platform_id);
            if (isset($response->id)) {
                $gm['server_gm_id'] = $response->id;
                $gm['message'] = $reply_message;
                $gm['send_time'] = time();
                $gm['replied_time'] =time();
                $gm['user_id'] = Auth::user()->user_id;//GM的eastblue账号id
                $gm['server_id'] = $server->server_id;
                $gm['player_id'] = $player_id;//GM所回复的玩家的角色id
                $gm['player_name'] = Auth::user()->username;//GM的eastblue账号的username
                $gm['is_done'] = 1;

                GM::insert($gm);
                unset($gm);

                $ok[] = $gift_data[0] . ' ' . "OK. ";
            } else {
                $error[] = $gift_data[0] . ' ' . "Error";
            }
        }
        if (!empty($ok)) {
            $result[] = array(
                'msg' => implode(',', $ok),
                'status' => 'ok'
            );
        }
        if (!empty($error)) {
            $result[] = array(
                'msg' => implode(',', $error),
                'status' => 'error'
            );
        }
        $res = array(
            'result' => $result
        );
        return Response::json($res);
    }
}