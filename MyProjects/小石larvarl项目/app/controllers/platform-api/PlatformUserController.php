<?php

class PlatformUserController extends \BaseController {

    private function initTable($file_name, $area_id = array()){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if (!empty($area_id) && in_array($game_id, $area_id)) {
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.$game_id.'.txt');
        }else {
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        }
        $file_table = $table->getData();
        return $file_table;
    }

    public function index()
    {
        $data = array(
                'content' => View::make('platformapi.user.index'));
        return View::make('main', $data);
    }

    public function editPassword()
    {
        $data = array(
                'content' => View::make('platformapi.user.password'));
        return View::make('main', $data);
    }

    public function updatePassword()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error'));
        $rules = array(
                'uid' => 'required',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        }
        $platform = Platform::find(Session::get('platform_id'));
        if (! $platform)
        {
            return Response::json($msg, 404);
        }
        
        $api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $uid = trim(Input::get('uid'));
        $new_password = trim(Input::get('password'));
        $api->editUserPassword($uid, $new_password);
        
        return $api->sendResponse();
    }

    public function getUserInfo()
    {

        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error'));
        $rules = array(
                'email_or_uid' => 'required');
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
        $game_id = Session::get('game_id');
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
//        Log::info('get user by uid'.var_export($response, true));
        if ($response->http_code != 200)
        {
            return Response::json($response->body, 404);
        }
        $user_info = $response->body;
       
//          $s = var_export($user_info,true);
//          Log::info($s);
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
                'is_anonymous' => $user_info->is_anonymous);
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
                if(in_array($game_id,array(38, 51, 55, 58, 65, 77))){//神仙道
                    //
                } else {
                    $server_internal_id = $server->server_internal_id;
                    $player_name = $item->player_name;
                    $player_id = (int)$item->player_id;
                    $platform_id = Session::get('platform_id');
                    $game = Game::find(Session::get('game_id'));
                    $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                    if (isset($player_id)) { //得到player_id
                        $response1_id = $slave_api->getUserByPlayerID($platform_id, $player_id, $server_internal_id, $game->game_id);
                    } else if (isset($player_name)) { //得到player_name
                       $response1_name = $slave_api->getUserByPlayerName($platform_id, $player_name, $server_internal_id, $game->game_id);
                    }
                    //Log::info(var_export($response1, true));
                    if(!empty($response1_id->body)){
                        $response1 = $response1_id;
                    }else if(!empty($response1_name->body)){
                        $response1 = $response1_name;
                    }else {
                        continue;
                    }
                    //Log::info(var_export($response1, true));

                    foreach ($response1->body as $v) {
                        $item->tp_user_id = isset($v->tp_user_id) ? $v->tp_user_id :'';
                        $server = Server::currentGameServers()->where('server_internal_id', $server_internal_id)->first();
                        if (! $server) {
                            continue;
                        }
                        $api1 = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                        if(isset($v->player_id))
                        {
                            
                            $player_info_from_id = $api1->getPlayerInfoByPlayerID($v->player_id);
                        
                            if(! isset($player_info_from_id->PlayerID))
                            {
                                if(! empty($player))
                                {
                                    $players[] = $player;
                                }
                                continue;
                            }
                            $player_info_from_name = $api1->getPlayerInfoByName($player_info_from_id->Name);
                        } elseif(isset($v->player_name)) {
                            $player_info_from_name = $api1->getPlayerInfoByName($v->player_name);
                            if(! isset($player_info_from_name->player_id))
                            {
                                if(! empty($player))
                                {
                                    $players[] = $player;
                                }
                                continue;
                            }
                            $player_info_from_id = $api1->getPlayerInfoByPlayerID($player_info_from_name->player_id);
                        }
                        
                        // 获取exp
                        $player_exp = '';
                        if(isset($player_info_from_id->Roles))
                        {
                            $roles_array = $player_info_from_id->Roles;
                            foreach ( $roles_array as $role )
                            {
                                if($role->Is_ZhuJue == 1)
                                {
                                    $player_exp = $role->Exp;
                                    break;
                                }
                            }
                        } else if(isset($player_info_from_id->Exp))
                        {
                            $player_exp = $player_info_from_id->Exp;
                        }
                        $item->is_online = isset($player_info_from_name->is_online) ? $player_info_from_name->is_online : '';
                        $item->level = isset($player_info_from_name->level) ? $player_info_from_name->level : 0;
                        $item->last_login = isset($player_info_from_name->last_login) ? date('Y-m-d H:i:s', $player_info_from_name->last_login) : '';
                        $item->active = isset($player_info_from_name->last_login) ? floor(( int ) (time() - $player_info_from_name->last_login) / 86400) : '';
                        $item->vip_level = isset($player_info_from_id->VIPLevel) ? $player_info_from_id->VIPLevel : '';
                        $item->tongqian = isset($player_info_from_id->TongQian) ? $player_info_from_id->TongQian : '';
                        $item->yuanbao = isset($player_info_from_id->YuanBao) ? $player_info_from_id->YuanBao : '';
                        $item->exp = $player_exp; 
                        if(isset($player_info_from_name->rank))
                        {
                            $item->rank = $player_info_from_name->rank;
                        }
                        if(isset($player_info_from_name->league_id))
                        {
                            $item->league_id = $player_info_from_name->league_id;
                        }
                        if(isset($player_info_from_name->league_name))
                        {
                            $item->league_name = $player_info_from_name->league_name ? $player_info_from_name->league_name : 'null';
                        }
                        if(isset($player_info_from_id->ShengWang))
                        {
                            $item->shengwang = $player_info_from_id->ShengWang;
                        }
                        if(isset($player_info_from_id->TiLi))
                        {
                            $item->tili = $player_info_from_id->TiLi;
                        }
                        if(isset($player_info_from_id->YueLi))
                        {
                            $item->yueli = $player_info_from_id->YueLi;
                        }
                        if(isset($player_info_from_name->attack))
                        {
                            $item->attack = $player_info_from_name->attack;
                        }
                        if(isset($player_info_from_id->JingJieDian))
                        {
                            $item->jingjiedian = $player_info_from_id->JingJieDian;
                        }
                        if(isset($player_info_from_id->LingShi))
                        {
                            $item->lisngshi = $player_info_from_id->LingShi;
                        }
                    }
                }
               
            }
            
            $item->server_id = $server_name ? $server_name : 'none';
            $item->created_time = date('Y-m-d H:i:s', $item->created_time);
            $item->aa = 'aa';
            if (isset($item->all_pay_amount))
            {
                $item->avg_amount = $item->all_pay_times > 0 ? round(
                        $item->all_pay_amount / $item->all_pay_times, 2) : 0;
            }
        }
        $user = array(
                'user_basic' => $user_basic,
                'created_players' => $user_info->players,
                
        );
        return Response::json($user);
    }

    public function loginMasterIndex()
    {
        $data = array(
                'content' => View::make('platformapi.user.loginmaster', array()));
        return View::make('main', $data);
    }

    public function loginMasterData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error'));
        $login_email = trim(Input::get('login_email'));
        $uid = (int) Input::get('uid');
        $choice = (int) Input::get('choice');
        $operate_type = (int) Input::get('operate_type');
        
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        if ($login_email)
        {
            $game = Game::find(Session::get('game_id'));
            $game_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                    $game->eb_api_secret_key);
            $game_response = $game_api->getUserByEmail($platform->platform_id, 
                    $login_email);
            if ($game_response->http_code != 200)
            {
                return Response::json($game_response->body, 404);
            }
            $uid = (int) $game_response->body->uid;
        }
        
        if ($operate_type == 0)
        { // 打开
            $response = $api->setLoginMasterByUID($uid, "on");
        } else
        { // 关闭
            $response = $api->setLoginMasterByUID($uid, "");
        }
        return $api->sendResponse();
    }

    public function bindEmailIndex()
    {
        $data = array(
                'content' => View::make('platformapi.user.bindemail', array()));
        return View::make('main', $data);
    }

    public function bindEmailData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error'));
        $rules = array(
                'login_email' => 'required');
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        }
        
        $login_email = trim(Input::get('login_email'));
        $uid = trim(Input::get('uid'));
        $password = trim(Input::get('password'));
        
        $platform = Platform::find(Session::get('platform_id'));
        if (! $platform)
        {
            return Response::json($msg, 404);
        }
        $old_login_email = '';
        if ($uid)
        {
            $game = Game::find(Session::get('game_id'));
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                    $game->eb_api_secret_key);
            $response = $slave_api->getUserByUID($platform->platform_id, $uid, 
                    $server_internal_id = 0, $game->game_id);
            if ($response->http_code != 200)
            {
                return Response::json($response->body, 404);
            }
            $user_info = $response->body;
            if (isset($user_info->login_email))
            {
                $old_login_email = $user_info->login_email;
            }
        }
        $api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $response = $api->bindEmail($old_login_email, $login_email);
        if ($response->http_code != 200)
        {
            return Response::json($response->body, 404);
        }
        $body = $response->body;
        if ($password)
        {
            $response = $api->editUserPassword($uid, $password);
        }
        return Response::json($body);
    }

    public function upgradeAnonymousIndex()
    {
        $data = array(
                'content' => View::make('platformapi.user.anonymous'));
        return View::make('main', $data);
    }

    public function upgradeAnonymous()
    {
        $email = Input::get('email');
        $uid = Input::get('uid');
        $password = Input::get('password');
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $api->upgradeAnonymous($email, $uid, $password);
        return $api->sendResponse();
    }

    public function neiwanIndex()
    {
        $table = Table::init(public_path() . '/table/neiwan.txt');
        $neiwans = $table->getData();
        $game_id = Session::get('game_id');
        $data2view = array();
        foreach ($neiwans as $k => $v)
        {
            if($game_id == $v->game_id){
                $game = Game::find($v->game_id);
                if ($game)
                {
                    $data2view[] = array(
                        'game_id' => $game->game_name,
                        'uid' => $v->uid,
                        'user' => isset($v->user) ? $v->user : '',
                        'created_time' => isset($v->created_time) ? ($v->created_time ? date("Y-m-d H:i:s", $v->created_time) : '') : '',
                        'creater' => isset($v->creater) ? $v->creater : '',
                        );
                }
            }
        }
        $data = array(
                'content' => View::make('platformapi.user.neiwan', 
                        array(
                                'neiwans' => (object) $data2view)));
        return View::make('main', $data);
    }

    public function neiwan()    //注意，这个文件在master端和slave端的格式是不一样的，slave端只有game_id和uid两列
    {
        $user = trim(Input::get('user'));
        $uid = trim(Input::get('uid'));
        $delete_user = trim(Input::get('delete_user'));
        $delete_uid = trim(Input::get('delete_uid'));
        $creater = Auth::user()->username;
        $created_time = time();
        if($delete_uid){
            $uid = $delete_uid;
            $user = $delete_user;
            $game_id = Session::get('game_id');
            $table = Table::init(public_path() . '/table/neiwan.txt');
            $message_arr = array(
                    'game_id' => $game_id,
                    'neiwan_uid' => $uid,
                    'user' => $user,
                    'created_time' => $created_time,
                    'creater' => $creater,
                    );
            $res = $table->deleteNeiWan($message_arr);
            $game = Game::find(Session::get('game_id'));
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                    $game->eb_api_secret_key);
            $response = $slave_api->neiwan($message_arr, $is_delete=1);
            if ($response->http_code = 200 && $response->body && $res)
            {
                $msg = array(
                        'res' => 'OK');
                return Response::json($msg);
            } else
            {
                $msg = array(
                        'res' => 'Error');
                return Response::json($msg, 404);
            }
        }elseif($uid){
            $game_id = Session::get('game_id');
            $table = Table::init(public_path() . '/table/neiwan.txt');
            $message_arr = array(
                    'game_id' => $game_id,
                    'neiwan_uid' => $uid,
                    'user' => $user,
                    'created_time' => $created_time,
                    'creater' => $creater,
                    );
            $res = $table->addData($message_arr);
            $game = Game::find(Session::get('game_id'));
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                    $game->eb_api_secret_key);
            $response = $slave_api->neiwan($message_arr, $is_delete=0);
            if ($response->http_code = 200 && $response->body && $res)
            {
                $msg = array(
                        'res' => 'OK');
                return Response::json($msg);
            } else
            {
                $msg = array(
                        'res' => 'Error');
                return Response::json($msg, 404);
            }
        }else{
            return Response::json(array('error' => 'Bad Input.'), 404);
        }
    }

    public function mobilePushIndex()
    {   
        $game_id = Session::get('game_id');

        $result = EastBlueLog::where('game_id', Session::get('game_id'))
                        ->where('log_key', 'mobilepush')
                        ->selectRaw("`desc` , new_value")
                        ->orderBy('log.log_id', 'DESC')
                        ->take(5)
                        ->get();
        $data = array(
                'content' => View::make('platformapi.user.mobilepush', array(
                    'mobiledata' => $result
                    )));
        return View::make('main', $data);
    }

    public function mobilePushUpdate()
    {
        $msg = array(
           'code' => Config::get('errorcode.unknow'),
           'error' => Lang::get('error.basic_input_error'),
        );
        $game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id'); 
        $title = Input::get('title');
        $content = Input::get('content');
        $except_os_type = implode(" ", Input::get('except_os_type'));
        Log::info($except_os_type);
        $platform = Platform::find(Session::get('platform_id'));
        $device_num = Input::get('device_num');
        $user_id = Auth::user()->user_id;
        $username = Auth::user()->username;
        if(!$title){
            return Response::json($msg, 404);
        }
        if(!$content){
            return Response::json($msg, 404);
        }
        $api = PlatformApi::connect($platform->platform_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $data = array(
            'game_id' => $game_id,
            'title' => $title,
            'content' => $content, 
            'except_os_type' =>$except_os_type,
            'time' => time(),               
            );
        $now_time = time();
        $time = date('Y-m-d H:i:s',$now_time);
        $response = $api->mobilepush($data);
        if ($response->http_code == 200 && $response->body)
        {   
            if($response->body->error == 0){
                $data2store = array(
                    'log_key' => 'mobilepush',
                    'desc' => $username.'于'.$time.'推送了一条消息(排除了)'.$except_os_type.'设备',
                    'new_value' => 'title:'.$title.'  '.'content:'.$content,
                    'user_id' => $user_id,
                    'game_id' => $game_id,
                    'created_at' => $now_time,
                    'updated_at' => $now_time
                    );
                EastBlueLog::insert($data2store);
                return Response::json($response->body);
            }
            else{
                return Response::json($response->body, 403);
            }
        } else
        {
            return $api->sendResponse();
        }
        // }
    }

    public function awardSetIndex(){
        $table = $this->initTable('item');
        foreach ($table as $v) {
            $gifts[$v->id] = $v->id . ':' . $v->name;
        }
        $table2 = $this->initTable('yysgwj');
        //查询yysgwj.txt文件内容，将数据与item数据进行合并整合
        foreach ($table2 as $v) {
            switch ($v->type) {
                case '1':
                    $gifts[$v->id] = $v->id . ':水-' . $v->name;
                    break;
                case '2':
                    $gifts[$v->id] = $v->id . ':火-' . $v->name;
                    break;
                case '3':
                    $gifts[$v->id] = $v->id . ':風-' . $v->name;
                    break;
                case '4':
                    $gifts[$v->id] = $v->id . ':光-' . $v->name;
                    break;
                default:
                    $gifts[$v->id] = $v->id . ':暗-' . $v->name;
                    break;
            }
        }
        $data = array(
                'content' => View::make('platformapi.user.award', array(
                    'gifts' => $gifts
                    )));
        return View::make('main', $data);
    }

    public function awardSet(){
        $id = Input::get('id');
        $url_type = Input::get('url_type');
        $operation_type = Input::get('operation_type');
        $gift_id = Input::get('gift_id');
        $img_url = Input::get('img_url');
        $gift_num = Input::get('number');
        $received_num = Input::get('received_num');
        $is_used = (int)Input::get('is_used');
        $uid = Input::get('uid');
        $player_id = Input::get('player_id');
        $total_chance = (int)Input::get('total_chance');
        $end_time = strtotime(trim(Input::get('end_time')));

        $table = $this->initTable('item');
        foreach ($table as $v) {
            $gifts[$v->id] = $v->id . ':' . $v->name;
        }
        //查询yysgwj.txt文件内容，将数据与item数据进行合并整合
        $table2 = $this->initTable('yysgwj');        
        foreach ($table2 as $v) {
            switch ($v->type) {
                case '1':
                    $gifts[$v->id] = $v->id . ':水-' . $v->name;
                    break;
                case '2':
                    $gifts[$v->id] = $v->id . ':火-' . $v->name;
                    break;
                case '3':
                    $gifts[$v->id] = $v->id . ':風-' . $v->name;
                    break;
                case '4':
                    $gifts[$v->id] = $v->id . ':光-' . $v->name;
                    break;
                default:
                    $gifts[$v->id] = $v->id . ':暗-' . $v->name;
                    break;
            }
        }    
        if(1 == $url_type && 1 == $operation_type){//增加奖励---修改奖励
            $img = substr($img_url,0,strrpos($img_url,'.'));
            if (preg_match("/.*(\_\d+)$/",$img, $matches)) {
                return $this->updateAward($id, $gift_id, $img_url,$gift_num,$received_num,$is_used);
            }else{
                return Response::json(array('error'=>'图片地址必须是以下划线加数字结尾！例如：abc_1.png'),404);
            }
           
        }elseif(2 == $url_type && 1 == $operation_type){//查询奖励
            $award_item = $this->searchAward($gift_id);
            if($award_item->http_code == 200){
                $items = $award_item->body;
                foreach ($items as $item) {
                    $item->gift_name = isset($gifts[$item->gift_id]) ? $gifts[$item->gift_id] : $item->gift_id;
                }
                return Response::json($items);
            }else{
                return Response::json(array('error'=>'查询出现错误！'),404);
            }   
        }elseif(1 == $url_type && 2 == $operation_type){//增加抽奖用户
            return $this->updateAwardUser($id, $uid, $player_id, $total_chance, $end_time);
        }elseif(2 == $url_type && 2 == $operation_type){//查询抽奖用户
            $award_record = $this->searchAwardUser($uid, $player_id, $total_chance, $end_time);
            if($award_record->http_code == 200){
                $items = $award_record->body;
                foreach ($items as $item) {
                    $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                }
                return Response::json($items);
            }else{
                return Response::json(array('error'=>'查询出现错误！'),404);
            }
        }
        
    }

    private function updateAward($id, $gift_id, $img_url,$gift_num,$received_num,$is_used){
        $game_id = Session::get('game_id');
        if($id>0){//修改操作
            if(!$img_url){
                return Response::json(array('error'=>Lang::get('error.basic_input_error')),404);
            }
            $data = array(
                'id' => $id,
                'img_url' => $img_url,
                'num' => $gift_num,
                'received_num'=>$received_num,
                'is_used' => $is_used,
                'time' => time(),
            );
        }else{//增加操作
            if(!$gift_id || !$img_url || !$gift_num){
                return Response::json(array('error'=>Lang::get('error.basic_input_error')),404);
            }
            $data = array(
                'gift_id' => $gift_id,
                'img_url' => $img_url,
                'num' => $gift_num,
                'is_used' => $is_used,
                'game_id' => $game_id,
                'time' => time(),
            );
        }
        $game_id = Session::get('game_id');
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
        $response = $api->updateAward($data);
        if ($response->http_code == 200 && 0 == $response->body->error){
            return Response::json(array('msg'=>'操作成功','status' =>'ok' ));
        }else{
            Log::info('award:'.var_export($response,true));
            return Response::json(array('error' => '操作失败！'),404);
        }
    }

    private function searchAward($gift_id){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'gift_id' => $gift_id,
        );
        return $response = $slave_api->getYYSGAward($params);
    }

    private function updateAwardUser($id, $uid, $player_id, $total_chance, $end_time){
        $game_id = Session::get('game_id');
        if($id>0){
            $data = array(
                'id' => $id,
                'total_chance' => $total_chance,
                'end_time' => $end_time,
                'time' => time(),
            );
        }else{
            if(!$uid || !$player_id){
                return Response::json(array('error'=>Lang::get('error.basic_input_error')),404);
            }
            $data = array(
                'uid' => $uid,
                'player_id' => $player_id,
                'total_chance' => $total_chance,
                'game_id' => $game_id,
                'created_time' => time(),
                'end_time' => $end_time,
                'time' => time(),
            );
        }
        $game_id = Session::get('game_id');
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
        $response = $api->updateAwardUser($data);
        if ($response->http_code == 200 && 0 == $response->body->error){
            return Response::json(array('msg'=>'操作成功','status' =>'ok' ));
        }else{
            Log::info('user:'.var_export($response,true));
            return Response::json(array('error' => '操作失败！'),404);
        }
    }

    private function searchAwardUser($uid, $player_id, $total_chance, $end_time){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'uid' => $uid,
            'player_id' => $player_id,
            'total_chance' => $total_chance,
            'end_time' => $end_time,
        );
        return $response = $slave_api->getYYSGAwardUser($params);
    }
}