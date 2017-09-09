<?php

class YYSGGiftBagController extends \BaseController
{

    private function initArrayTable($file_name, $area_id = array()){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if (!empty($area_id) && in_array($game_id, $area_id)) {
            $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/'.$file_name.$game_id.'.txt');
        }else {
            $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        }
        $file_table = $table->getData();
        return $file_table;
    }

    public function AllServerGiftbagIndex(){    //夜夜三国全服礼包
        $servers = $this->getUnionServers();
        $gifts = $this->OpenFile(public_path() . '/table/yysg/all_server_gift.txt');
        $temp = array();
        foreach ($gifts as $k => $v) {
                $temp[] = $v;
        }
        $game = Game::find(Session::get('game_id'));
        if('yysg' != $game->game_code){
            return $this->show_message('403', 'Not a YYSG game.');
        }
        
        $data = array(
            'content' => View::make('serverapi.yysg.giftbag.all_server_gift', array(
                'gifts' => $temp,
                'servers' => $servers,
            ))
        );
        return View::make('main', $data);
    }

    public function AllServerGiftbagSend(){
        $server_id = (int)Input::get('server_id');
        $platform_id = (int)Session::get('platform_id');

        if(!$server_id){
            return Response::json(array('error'=>"Please Select A Server."), 403);
        }
        $giftbag_id = (int)Input::get('gift_bag_id');
        if(!$giftbag_id){
            return Response::json(array('error'=>"Please Select A Giftbag."), 403);
        }
        $server = Server::find($server_id);
        if(!$server){
            return Response::json(array('error'=>"Invalid Server."), 403);
        }
        $server_api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

        $result = $server_api->sendGiftBagToAllServer($giftbag_id, $platform_id);

        if(isset($result->result) && 'OK' == $result->result){
            $log = new Operation();
            $log->operator = Auth::user()->username;
            $log->game_id = $server->game_id;
            $log->player_id = 99999999;
            $log->player_name = "All-Server-GiftBag";
            $log->operation_type = "giftbag";
            $log->operate_time = time();
            $log->server_name = $server->server_name;
            $log->giftbag_id = $giftbag_id;
            $log->extra_msg = "OK|All-Server-GiftBag";
            $log->save();
            return Response::json(array('msg'=>"Send Success!"));
        }else{
            if(isset($result->error)){
                $msg = array(
                    'code' => Config::get('errorcode.unknow'),
                    'error' => '访问游戏服务器出错，请在对应地区的游戏内核实是否发放成功'
                );
                Log::info('YYSGGiftBagController---AllServerGiftbagSend:'.var_export($result, true));
                return Response::json($msg, 403);
            }
            if(NULL == $result)
            {
                $msg = array(
                    'code' => Config::get('errorcode.unknow'),
                    'error' => Lang::get('serverapi.gift_return_null').'，请在对应地区的游戏内核实是否发放成功'
                );
                Log::info('YYSGGiftBagController---AllServerGiftbagSend:'.var_export($result, true));
                return Response::json($msg, 403);
            }
            Log::info('YYSGGiftBagController---AllServerGiftbagSend:'.var_export($result, true));
            return Response::json(array('error' => 'Result:Unknown，请在对应地区的游戏内核实是否发放成功'), 403);
        }
    }

    public function index()
    {
        $table = $this->initTable();
        $servers = Server::currentGameServers()->get();
        $gifts = $table->getData();
        $temp = array();
        foreach ($gifts as $k => $v) {
                $temp[] = $v;
        }
        $game = Game::find(Session::get('game_id'));
        if('yysg' != $game->game_code){
            return $this->show_message('403', 'Not a YYSG game.');
        }
        
        $data = array(
            'content' => View::make('serverapi.yysg.giftbag.index', array(
                'gifts' => $temp,
                'servers' =>$servers
            ))
        );
        return View::make('main', $data);
    }

    private function initTable()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/item.txt');
        return $table;
    }

    private function initTable2()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/gift.txt');//allserver_giftbag.txt
        return $table;
    }

    private function initTableitem()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . $game->game_code . '/shopitem.txt');//allserver_giftbag.txt
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
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $server_id = Input::get('server_id');
        if (empty($server_id)) {
            return Response::json(array('error'=>"请选择服务器！"), 403);
        }
        $server = Server::find($server_id);
        $server_internal_id = $server->server_internal_id;
/*      $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();*/
        $operator = Auth::user()->username;
        $action_type = (int)Input::get('action_type');
        $giftbag_num = (int)Input::get('giftbag_num');
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
        if($giftbag_num <= 0){
            return Response::json(array('error'=>"Please input a legal number"), 403);
        }
        $gift_bag_id = (int)Input::get('gift_bag_id');
        if(0 == $gift_bag_id){//没有选择礼包时再从输入框中取
            $gift_bag = Input::get('gift_bag_name');
            if(!$gift_bag){
                return Response::json(array('error'=>"请选择或输入礼包！"), 403);
            }
            $gift_id_name = explode(":", $gift_bag);
            try{
                $gift_bag_id = (int)$gift_id_name[0];
            }catch(\Exception $e){
                return Response::json($msg, 403);
            }
            $table = $this->initTable();

            $gifts = $table->getData();
            $temp = array();
            foreach ($gifts as $k => $v) {
                    $temp[] = $v->id;
            }
            if(!in_array($gift_bag_id, $temp)){
                return Response::json(array('error'=>"不存在的礼包！"), 403);
            }
        }

        $action_type = (int)Input::get('action_type');
        
        $player_names = Input::get('player_names');
        
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

        /*if ($password) { // 对当前服全服发放，需要填写密码
            if (md5($password) == '861d16cc9357375af69e87c522766e97') { // 验证密码再操作.“woshixiaoxin”
                $api->sendGiftBagToPlayers($gift_bag_id);
                return $api->sendResponse();
            } else {
                App::abort(403, 'Method:send. The password does not match.');
            }
        }*/

        // 当前服的单个发放或者批量发放
        /*if ($player_name) { // 单个玩家名字
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
            $player_ids = array(
                $player_id
            );
        } else if ($player_id_or_names) { // 批量玩家*/
        $players = explode("\n", $player_names);
        $players = array_merge(array_unique($players));
        if($action_type == 1){
            $playernames = array();
            $wrongplayernames = array();
            $game = Game::find($game_id);
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            $check = $slave_api->checkYYSGplayer($game_id, array(), $players);
            if('404' == $check->http_code){
                $wrongplayernames = $check->body;
            }elseif('200' == $check->http_code){
                $playernames = $players;
            }else{
                return $slave_api->sendResponse();
            }

            if(count($wrongplayernames) > 0){
                $str = '';
                foreach ($wrongplayernames as $single_playername) {
                    $str .= $single_playername.', ';
                }
                return Response::json(array('error'=>"发现玩家昵称( ".$str." )疑似并非当前游戏的玩家昵称，请删除这些名称后重新提交"), 403);
            }else{
                $response = $api->sendGiftBagToPlayers($gift_bag_id, $playernames, $platform_id, $giftbag_num);
            }
        }elseif($action_type == 2){
            $playersId = array();
            $wrongplayerids = array();
            $game = Game::find($game_id);
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            $check = $slave_api->checkYYSGplayer($game_id, $players, array());
            if('404' == $check->http_code){
                $wrongplayerids = $check->body;
            }elseif('200' == $check->http_code){
                $playersId = $players;
            }else{
                return $slave_api->sendResponse();
            }

            if(count($wrongplayerids) > 0){
                $str = '';
                foreach ($wrongplayerids as $single_playerid) {
                    $str .= $single_playerid.', ';
                }
                return Response::json(array('error'=>"发现玩家ID( ".$str." )疑似并非当前游戏的玩家ID，请删除这些ID后重新提交"), 403);
            }else{
                $response = $api->sendGiftBagToPlayersId($gift_bag_id, $playersId, $platform_id, $giftbag_num);
            }
        }
        if(isset($response->error)){
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '访问游戏服务器出错'
            );
            Log::info('YYSGGiftBagController---Response:'.var_export($response, true));
            return Response::json($msg, 403);
        }
        //Log::info("YYSG send gift bag response==>".var_export($response, true));
        if(NULL == $response)
        {
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('serverapi.gift_return_null')
            );
            Log::info('YYSGGiftBagController---Response:'.var_export($response, true));
            return Response::json($msg, 403);
        }

        if($action_type==1){
            if(!empty($response->success)){
                $this->insert_operation_msg_name($response->success, $gift_bag_id, $game_id, $operator, $platform_id, 'success', $giftbag_num,$server);
            }
            if(!empty($response->fail)){
                $this->insert_operation_msg_name($response->fail, $gift_bag_id, $game_id, $operator, $platform_id, 'fail', $giftbag_num,$server);
            }
        }elseif($action_type==2){
            if(!empty($response->success)){
                $this->insert_operation_msg_id($response->success, $gift_bag_id, $game_id, $operator, $platform_id, 'success', $giftbag_num,$server);
            }
            if(!empty($response->fail)){
                $this->insert_operation_msg_id($response->fail, $gift_bag_id, $game_id, $operator, $platform_id, 'fail', $giftbag_num,$server);
            }
        }
        return Response::json(array(
            'fail' => $response->fail,
            'ok' => $response->success
        ));
    }



    private function insert_operation_msg_name($players, $gift_bag_id, $game_id, $operator, $platform_id, $statu, $giftbag_num,$server){ //夜夜三国礼包信息插入 有playname的情况
        $game = Game::find($game_id);
        /*$server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();*/
        $server_name = $server->server_name;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        foreach ($players as $player) {
            $playerinfo = $api->getplayeridbyname($game_id, $player, $server->server_internal_id, $platform_id);
            if($playerinfo->http_code != '200'){
                $player_id = '';
            }else{
                $playerinfobody = $playerinfo->body;
                $player_id = $playerinfobody[0]->player_id;
            }
            $operation = Operation::insert(array('operate_time' => time(),
                                                     'game_id' => $game_id, 
                                                     'giftbag_id' => $gift_bag_id,
                                                     'player_id' => $player_id,
                                                     'player_name' => $player,
                                                     'operator' => $operator,
                                                     'server_name' => $server_name,
                                                     'operation_type' => 'giftbag',
                                                     'extra_msg' => $statu.'|'.$giftbag_num,

            ));
            
        }
    }

    private function insert_operation_msg_id($playersId, $gift_bag_id, $game_id, $operator, $platform_id, $statu, $giftbag_num,$server){  //夜夜三国礼包信息插入 有playid的情况
        $game = Game::find($game_id);
        /*$server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();*/
        $server_name = $server->server_name;
        foreach ($playersId as $playerid) {
            $playerinfo = explode(':', $playerid, 2);
            $operation = Operation::insert(array('operate_time' => time(),
                                                     'game_id' => $game_id, 
                                                     'giftbag_id' => $gift_bag_id,
                                                     'player_id' => $playerinfo[1],
                                                     'player_name' => $playerinfo[0],
                                                     'operator' => $operator,
                                                     'server_name' => $server_name,
                                                     'operation_type' => 'giftbag',
                                                     'extra_msg' => $statu.'|'.$giftbag_num,

            ));
        }
    }

    public function lookuppage(){//台湾夜夜三国查询礼包信息get方法
        $game = Game::find(Session::get('game_id'));
        $init_app_id = (int)Input::get('app_id');
        $temp_item = array();
        $temp_gift = array();
        $temp_mark = array();
        $temp_award = array();
        $items = $this->initArrayTable('item',$this->area_item_id);
        foreach ($items as $k => $v) {
                $temp_item[] = $v;
        }

        if(in_array($game->game_code, array('flsg','nszj'))){
            $gifts = $this->initArrayTable('gift');
            foreach ($gifts as $k => $v) {
                    $temp_gift[] = $v;
            }

            $marks = $this->initArrayTable('marklevel',$this->area_mark_id);
            foreach ($marks as $k => $v) {
                    $temp_mark[] = $v;
            }
        }

        if(in_array($game->game_code, array('flsg','nszj','mnsg'))){
            $awards = $this->initArrayTable('award');
            foreach ($awards as $k => $v) {
                    $temp_award[] = $v;
            }
        }
        
        $game = Game::find(Session::get('game_id'));
        $app_names = DB::table('apps')->whereIn('app_id',array(33, 34, 64, 146, 193))->lists('app_name','app_id');
        $data = array(
            'content' => View::make('serverapi.yysg.giftbag.lookup', array(
                'items' => $temp_item,
                'gifts' => $temp_gift,
                'marks' => $temp_mark,
                'awards' => $temp_award,
                'game_code' => $game->game_code,
                'app_names' => $app_names,
                'init_app_id' => $init_app_id
            ))
        );
        return View::make('main', $data);
    }

    public function showcertaininfo(){//台湾夜夜三国查询礼包信息post方法
        $msg = array(
            'code' => Lang::get('errorcode.unknown'),
            'msg' => Lang::get('errorcode.server_not_found')
        );

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $gift_bag_id = (int)Input::get('gift_bag_id');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $look_type = (int)Input::get('look_type');
        $result = array();
        $app_id = Input::get('app_id');
        $player_id = (int)Input::get('player_id');

        if(0 == $gift_bag_id){
            return Response::json(array('error'=> Lang::get('serverapi.select_gift_bag')),403);
        }

        if('yysg' == $game->game_code){
            $gift_table = 'item';
            $operation_type = 'giftbag';
        }else{
            switch ($app_id) {
                case '33':
                    $operation_type = 'single_server_gift';
                    $gift_table = 'item';
                    break;
                case '34':
                    $operation_type = 'batch_gift';
                    $gift_table = 'item';
                    break;
                case '64':
                    $operation_type = 'all_server_gift';
                    $gift_table = 'gift';
                    break;
                case '146':
                    $operation_type = 'mail_gift';
                    $gift_table = 'item';
                    break;
                case '193':
                    $operation_type = 'combined_server_gift';
                    $gift_table = 'gift';
                    break;
                default:
                    $operation_type = '';
                    break;
            }
        }
        if(NULL == ($operator = Input::get('operator'))){ //空代表所有操作者
            if($gift_bag_id==-1){ //-1代表所有礼包
                if($look_type==1){ //1代表详细查询
                    $result = Operation::where('operate_time','>',$start_time)
                                        ->where('operate_time','<',$end_time)
                                        ->where('game_id', $game_id)
                                        ->where('operation_type',$operation_type);
                }else{ //2代表汇总查询
                    $result = Operation::selectRaw('*,count(1) as count')
                                        ->where('operate_time','>',$start_time)
                                        ->where('operate_time','<',$end_time)
                                        ->where('game_id', $game_id)
                                        ->where('operation_type',$operation_type)
                                        ->groupBy('operator','giftbag_id');
                }
            }else{ //代表单独礼包
                if($look_type==1){ //1代表详细查询
                    $result = Operation::where('giftbag_id',$gift_bag_id)
                                        ->where('operate_time','>',$start_time)
                                        ->where('operate_time','<',$end_time)
                                        ->where('game_id', $game_id)
                                        ->where('operation_type',$operation_type);
                }else{ //2代表汇总查询
                    $result = Operation::selectRaw('*,count(1) as count')
                                        ->where('operate_time','>',$start_time)
                                        ->where('operate_time','<',$end_time)
                                        ->where('giftbag_id',$gift_bag_id)
                                        ->where('game_id', $game_id)
                                        ->where('operation_type',$operation_type)
                                        ->groupBy('operator');
                }
            }
        }else{ //代表单独操作者
            $operator = Input::get('operator');
            if($gift_bag_id==-1){ //-1代表所有礼包
                if($look_type==1){ //1代表详细查询
                    $result = Operation::where('operator',$operator)
                                        ->where('operate_time','>',$start_time)
                                        ->where('operate_time','<',$end_time)
                                        ->where('game_id', $game_id)
                                        ->where('operation_type',$operation_type);
                }else{ //2代表汇总查询
                    $result = Operation::selectRaw('*,count(1) as count')
                                        ->where('operate_time','>',$start_time)
                                        ->where('operate_time','<',$end_time)
                                        ->where('operator',$operator)
                                        ->where('game_id', $game_id)
                                        ->where('operation_type',$operation_type)
                                        ->groupBy('giftbag_id');
                }
            }else{ //代表单独礼包
                if($look_type==1){ //1代表详细查询
                    $result = Operation::where('giftbag_id',$gift_bag_id)
                                        ->where('operator',$operator)
                                        ->where('operate_time','>',$start_time)
                                        ->where('operate_time','<',$end_time)
                                        ->where('game_id', $game_id)
                                        ->where('operation_type',$operation_type);
                }else{ //2代表汇总查询
                    $result = Operation::selectRaw('*,count(1) as count')
                                        ->where('operate_time','>',$start_time)
                                        ->where('operate_time','<',$end_time)
                                        ->where('operator',$operator)
                                        ->where('giftbag_id',$gift_bag_id)
                                        ->where('game_id', $game_id)
                                        ->where('operation_type',$operation_type);
                }
            }
        }
        if($player_id){
            $result->where('player_id',$player_id);
        }
        $result = $result->get();
        $giftidtoname = array();
        if('item' == $gift_table){
            $table_data = $this->initArrayTable($gift_table,$this->area_item_id);
            foreach ($table_data as $item) {
                $giftidtoname[$item['id']] = $item['name'];
            }
        }else{
            $table_data = $this->initArrayTable($gift_table);
            foreach ($table_data as $item) {
                $giftidtoname[$item['itemid']] = $item['name'];
            }
        }
        if(146 == $app_id){
            $table_data = $this->initArrayTable('award');
            foreach ($table_data as $item) {
                $giftidtoname[$item['id']] = $item['cname'];
            } 

            if('nszj' == $game->game_code){
               $table_data = $this->initArrayTable('marklevel',$this->area_mark_id);
               foreach ($table_data as $item) {
                   $item['markid'] = '8'.$item['markid'];
                   $giftidtoname[$item['markid']] = $item['markname'];
               } 
            }
            
        }

        $result = json_decode($result);
        foreach ($result as &$res) {
           $res->operate_time = date('Y-m-d H:i:s', $res->operate_time);
           if(146 == $app_id && 'nszj' == $game->game_code){
                $res->giftbag_id = substr($res->giftbag_id, -8).':'. (isset($giftidtoname[$res->giftbag_id]) ? $giftidtoname[$res->giftbag_id] : '');
           }else{
                $res->giftbag_id = $res->giftbag_id.':'. (isset($giftidtoname[$res->giftbag_id]) ? $giftidtoname[$res->giftbag_id] : '');
           }
           
        }
        $data['items'] = $result;
        $data['current_page'] = 1;
        $data['per_page'] = count($data['items']);
        $data['count'] = count($data['items']);
        $result = (object)$data;

        return Response::json($result);
    }

    public function allServerIndex()
    {
        $servers = Server::currentGameServers()->get();

        $table = $this->initTable();

        $gifts = $table->getData();
        $temp = array();
        foreach ($gifts as $k => $v) {
            if ($v->type1 == 4 && $v->type2 == 1) {
                $temp[] = $v;
            }
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.giftbag.allserver', array(
                'servers' => $servers,
                'gifts' => (object)$temp
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
        $gift_bag_id = (int)Input::get('gift_bag_id');
        $send_type = Input::get('send_type');
        $gift_datas = Input::get('gift_data');
        $gift_datas = explode("\n", $gift_datas);
        $platform_id = Session::get('platform_id');
        foreach ($gift_datas as &$v) {
            $v = trim($v);
        }
        unset($v);
        $gift_datas = array_unique($gift_datas);
        $result = array();
        $ok = array();
        $error = array();
        foreach ($gift_datas as $gift_data) {
            $gift_data = explode("\t", $gift_data, 2);
            if (count($gift_data) != 2) {
                $error[] = $gift_data[0] . ': No Server Name. ';
                continue;
            }

            $server_name = trim($gift_data[1]);
            $server = Server::currentGameServers()->where('server_track_name', $server_name)->first();
            if (!$server) {
                $error[] = $gift_data[0] . "({$gift_data[1]}) Server Not Found. ";
                continue;
            }
            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $player_name = '';
            $player_id = 0;
            $player_ids = array();
            if ($send_type == 'name') {
                $player_name = trim($gift_data[0]);
                $player = $api->getPlayerInfo($player_name, $platform_id);
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
                $player_id = (int)$gift_data[0];
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
            } else {
                $error[] = $player_name . ' ' . $player_id . "({$gift_data[1]}) Error";
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
        for ($i = 0; $i < $len; $i++) {
            if ($game_id != $server[$i]->gameid) {
                continue;
            } else {
                $ser = array();
                if ($server[$i]->serverid1 == $server_id1) {
                    $arr = explode(',', $server[$i]->serverid2);
                    for ($j = 0; $j < count($arr); $j++) {
                        $ser[$j]['server_id'] = $arr[$j];
                        $ss = Server::where('game_id', '=', $game_id)->get();
                        for ($k = 0; $k < count($ss); $k++) {
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
        for ($i = 0; $i < $len; $i++) {
            $game_arr[$i] = $ser[$i]->gameid;
        }
        $ga = array_unique($game_arr);
        $se = "";
        if (in_array($game_id, $ga)) {//判断是联运
            for ($i = 0; $i < $len; $i++) {
                if ($ser[$i]->gameid == $game_id) {
                    $se .= $ser[$i]->serverid2 . ' , ';
                }
            }
            $se_arr = explode(',', $se);
            unset($se_arr[count($se_arr)]);

            $server = Server::whereNotIn('server_internal_id', $se_arr)->get();
            for ($i = 0; $i < count($server); $i++) {
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
        return $server;
    }


    private function getGameId()
    {
        $ser = $this->getUnionGame();
        $len = count($ser);
        for ($i = 0; $i < $len; $i++) {
            $game_arr[$i] = $ser[$i]->gameid;
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
            for ($i = 0; $i < count($ser); $i++) {
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

    public function count_giftbag_load(){   //载入夜夜三国查询礼包销量界面

        $table = $this->initTableitem();

        $gifts = $table->getData(); //获取礼包信息

        $game_id = Session::get('game_id');

        $servers = Server::currentGameServers()->where('is_server_on', '=', 1)->get();

        $data = array(
            'content' => View::make('serverapi.yysg.giftbag.count_giftbag', array(
                'gifts' => $gifts,
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    public function count_giftbag_check(){   //查询夜夜三国礼包销量 
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server_id = (int)Input::get('server_id');
        if('0' == $server_id){
            $server_id_to_post = 0;
        }else{
            $server = Server::find($server_id);
            if(!$server){
                return Response::json(array('error'=>'无效的服务器。'), 403);
            }
            $server_id_to_post = $server->platform_server_id;
        }
        $gift_bag_id = (int)Input::get('gift_bag_id');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $platform_id = Session::get('platform_id');

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $result = $api->count_giftbag_check($game_id, $server_id_to_post, $gift_bag_id, $start_time, $end_time, $platform_id);
        
        if (count($result->body)>0 && '200' == $result->http_code) { 
            if(file_exists(public_path() . '/table/' . $game->game_code . '/shopitem.txt')){
                $table = Table::initarray(public_path() . '/table/' . $game->game_code . '/shopitem.txt');
                $table_data = $table->getData();
            }else{
                $table_data = array();
            }
            $giftidtoname = array();
            foreach ($table_data as $item) {
                $giftidtoname[$item['giftbag_id']] = $item['name'];
            }
            unset($table_data);

            $result = $result->body;

            foreach ($result as &$res) {
                $res->giftbag_id = isset($giftidtoname[$res->giftbag_id]) ? $res->giftbag_id.':'.$giftidtoname[$res->giftbag_id] : $res->giftbag_id.':不明礼包';
                $single_server = Server::where('platform_server_id', $res->server_id)->where('game_id', $game_id)->take(1)->get();
                if(count($single_server) > 0){  //夜夜三国出现了找不到服务器的问题，先暂时判断如果找不到只显示server_id
                    if(isset($single_server[0])){
                        $single_server = $single_server[0];
                        $res->server_id = $single_server->server_track_name;
                    }
                }
            }

            $data['items'] = $result;
            $data['time_period'] = date('Y-m-d H:i:s', $start_time).'--'.date('Y-m-d H:i:s', $end_time);
            $result = (object)$data;
            unset($data);

            return Response::json($result);
        }else{
            return Response::json(array('error'=>'没有查询到数据(可能此段时间内并没有礼包购买)!'), 403);
        }
    }

    public function count_monetary_load(){  //夜夜三国货币消耗功能载入

        $servers = Server::currentGameServers()->get();
        $game = Game::find(Session::get('game_id'));
        $data = array(
            'content' => View::make('serverapi.yysg.giftbag.count_monetary', array(
                'servers' => $servers,
                'game_code' => $game->game_code,
            ))
        );
        return View::make('main', $data);
    }

    public function count_monetary_check(){  //夜夜三国货币消耗功能查询
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );

        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server_id = (int)Input::get('server_id');
        if('0' == $server_id){  //如果要查询全服建议用foreach对每个服务器进行查询，然后把查询结果集中起来再返回
            return Response::json(array('error'=>'目前不支持查询所有服务器，请选定服务器!'), 403);
        }
        $server = Server::find($server_id);
        $monetary_type = (int)Input::get('monetary_type');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $result = $api->count_monetary_check($game_id, $server->server_internal_id, $monetary_type, $start_time, $end_time);

        if (count($result->body)>0) { 

            $result = $result->body;

            $data['items'] = $result;
            $data['time_period'] = date('Y-m-d H:i:s', $start_time).'--'.date('Y-m-d H:i:s', $end_time);
            switch ($monetary_type) {
                case '0':   //元宝
                    $monetary_name = '元宝';
                    break;
                case '1':   //铜钱
                    $monetary_name = '铜钱';
                    break;
                case '2':   //体力
                    $monetary_name = '体力';
                    break;
                default:
                    break;
            }
            $data['server_name'] = $server->server_track_name.'--'.$server->server_name;
            $data['monetary_name'] = $monetary_name;
            $result = (object)$data;
            
            return Response::json($result);
        }else{
            return Response::json(array('error'=>'No data!'), 403);
        }
    }

    //夜夜三国发送邮件，可携带物品，同发送礼包
    public function mailgiftindex(){
        $table = $this->initTable();

        $gifts = $table->getData();
        $temp = array();
        foreach ($gifts as $k => $v) {
                $temp[] = $v;
        }
        $game = Game::find(Session::get('game_id'));
        if('yysg' != $game->game_code){
            return $this->show_message('403', 'Not a YYSG game.');
        }
        
        $data = array(
            'content' => View::make('serverapi.yysg.giftbag.mailgiftbag', array(
                'gifts' => $temp
            ))
        );
        return View::make('main', $data);
    }

    public function mailgiftsend(){
        $game_id = Session::get('game_id');
        $platform_id = (int)Session::get('platform_id');
        $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        $operator = Auth::user()->username;

        $action_type = (int)Input::get('action_type');
        $giftbag_num = (int)Input::get('giftbag_num');
        $mail_title = Input::get('mail_title');
        $mail_body = Input::get('mail_body');
        $writer = Input::get('writer');
        $available_time = (int)Input::get('available_time');
        $available_time = $available_time * 86400;  //单位转成秒

        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );

        $rules = array(
            'mail_title' => 'required',
            'mail_body' => 'required',
            'available_time' => 'required',
            'writer' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $gift_bag_id = (int)Input::get('gift_bag_id');
        if(0 == $gift_bag_id){  //没有选择礼包时再从输入框中取
            $gift_bag = Input::get('gift_bag_name');
            if($gift_bag){
                $gift_id_name = explode(":", $gift_bag);
                try{
                    $gift_bag_id = (int)$gift_id_name[0];
                }catch(\Exception $e){
                    return Response::json(array('error' => 'Not a right giftbag.'), 403);
                }
                $table = $this->initTable();

                $gifts = $table->getData();
                $temp = array();
                foreach ($gifts as $k => $v) {
                        $temp[] = $v->id;
                }
                if(!in_array($gift_bag_id, $temp)){
                    return Response::json(array('error'=>"不存在的礼包！"), 403);
                }
            }else{
                $gift_bag_id = '';
            }
        }

        $action_type = (int)Input::get('action_type');
        
        $players = Input::get('players');
        
        $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

        $players = explode("\n", $players);
        $players = array_unique($players);
        if($action_type == 1){  //用名字发
            $playernames = array();
            $wrongplayernames = array();
            $game = Game::find($game_id);
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            $check = $slave_api->checkYYSGplayer($game_id, array(), $players);
            if('404' == $check->http_code){
                $wrongplayernames = $check->body;
            }elseif('200' == $check->http_code){
                $playernames = $players;
            }else{
                return $slave_api->sendResponse();
            }
            if(count($wrongplayernames)){
                $str = '';
                foreach ($wrongplayernames as $single_playername) {
                    $str .= $single_playername.', ';
                }
                return Response::json(array('error'=>"发现玩家昵称( ".$str." )疑似并非当前游戏的玩家昵称，请删除这些名称后重新提交"), 403);
            }else{  //------------------------------用新接口
                $response = $api->sendMailGiftToPlayers($mail_title, $mail_body, $writer, $gift_bag_id, $playernames, array(), $available_time, $platform_id);
            }
        }elseif($action_type == 2){ //用id发
            $playersId = array();
            $wrongplayerids = array();
            $game = Game::find($game_id);
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            $check = $slave_api->checkYYSGplayer($game_id, $players, array());
            if('404' == $check->http_code){
                $wrongplayerids = $check->body;
            }elseif('200' == $check->http_code){
                $playersId = $players;
            }else{
                return $slave_api->sendResponse();
            }
            if(count($wrongplayerids)){
                $str = '';
                foreach ($wrongplayerids as $single_playerid) {
                    $str .= $single_playerid.', ';
                }
                $str = substr($str, 0, strlen($str)-1);
                return Response::json(array('error'=>"发现玩家ID( ".$str." )疑似并非当前游戏的玩家ID，请删除这些ID后重新提交"), 403);
            }else{  //------------------------------用新接口
                $response = $api->sendMailGiftToPlayers($mail_title, $mail_body, $writer, $gift_bag_id, array(), $playersId, $available_time, $platform_id);
            }
        }
        
        if(isset($response->error)){
            $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => '访问游戏服务器出错'
            );
            return Response::json($msg, 403);
        }
        Log::info(var_export($response, true));
        if(isset($response->result)){
            if('OK' == $response->result){
                $this->insert_operation($players, $gift_bag_id, $game_id, $operator, $platform_id, 'success', $action_type);
            }
            return Response::json(array('msg' => $response->result));
        }

    }

    private function insert_operation($players, $gift_bag_id, $game_id, $operator, $platform_id, $statu, $insert_type){ //夜夜三国礼包信息插入 有playname的情况
        $game = Game::find($game_id);
        $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        $server_name = $server->server_name;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        foreach ($players as $player) {
            if(1 == $insert_type){  //名字发
                $playerinfo = $api->getplayeridbyname($game_id, trim($player), $server->server_internal_id, $platform_id);
            }elseif(2 == $insert_type){ //id发
                $playerinfo = $api->getplayernamebyid($game_id, trim($player), $server->server_internal_id, $platform_id);
            }
            if($playerinfo->http_code != '200'){
                if(1 == $insert_type){  //名字发
                    $player_name = $player;
                    $player_id = '';
                }elseif(2 == $insert_type){ //id发
                    $player_name = '';
                    $player_id = $player;
                }
            }else{
                $playerinfobody = $playerinfo->body;
                if(isset($playerinfobody[0]->player_id)){
                    $player_id = $playerinfobody[0]->player_id;
                    $player_name = $playerinfobody[0]->player_name;
                }else{
                    if(1 == $insert_type){  //名字发
                        $player_name = $player;
                        $player_id = '';
                    }elseif(2 == $insert_type){ //id发
                        $player_name = '';
                        $player_id = $player;
                    } 
                }
            }
            $operation = Operation::insert(array('operate_time' => time(),
                                                     'game_id' => $game_id, 
                                                     'giftbag_id' => $gift_bag_id,
                                                     'player_id' => $player_id,
                                                     'player_name' => $player_name,
                                                     'operator' => $operator,
                                                     'server_name' => $server_name,
                                                     'operation_type' => 'giftbag',
                                                     'extra_msg' => $statu,

            ));
            
        }
    }

    private function getPartners(){
        $game = Game::find(Session::get('game_id'));
        $partners_file = $this->OpenFile(public_path() . '/table/' . $game->game_code . '/yysgwj.txt');
        $partners = array();
        $num2type = array(
            0 => '无属性',
            1 => '水',
            2 => '火',
            3 => '風',
            4 => '光',
            5 => '暗',
            );
        foreach ($partners_file as $value) {
            $partners[$value->id] = (isset($num2type[$value->type]) ? $num2type[$value->type] : 'Unknown').'-'.$value->name;
        }
        unset($partners_file);
        return $partners;
    }

    public function SendPartnerIndex(){ //夜夜三国发送武将的功能，以后可能移植给萌娘的
        $servers = $this->getUnionServers();
        $partners = $this->getPartners();
        $data = array(
            'content' => View::make('serverapi.yysg.giftbag.sendpartner', array(
                'servers' => $servers,
                'partners' => $partners,
                ))
        );
        return View::make('main', $data);
    }

    public function SendPartnerOperate(){
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $player_id = (int)Input::get('player_id');
        $player_name = Input::get('player_name');
        $partner_id = (int)Input::get('partner_id');
        $type = Input::get('type');
        if('send' == $type){    //发送操作
            $server_id = (int)Input::get('server_id');
            if(!($partner_id && $server_id && ($player_id || $player_name))){   //检测必须项
                return Response::json(array('error' => Lang::get('basic.not_enough_input')), 403); 
            }
            $server = Server::find($server_id);
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            //这里根据玩家的id以及名字获取玩家信息
            if($player_id){ //有玩家id
                $slave_result = $slave_api->getplayernamebyid($game_id, $player_id, $server->server_internal_id, $platform_id);
                if(200 == $slave_result->http_code){
                    $body = $slave_result->body;
                    $player_name = $body[0]->player_name;
                }else{
                    $player_name = '';
                }
            }else{  //有玩家名字
                $slave_result = $slave_api->getplayeridbyname($game_id, $player_name, $server->server_internal_id, $platform_id);
                if(200 == $slave_result->http_code){
                    $body = $slave_result->body;
                    $player_id = $body[0]->player_id;
                }else{
                    $player_id = 0;
                }
            }
            if(!$player_id){    //如果无法获取到玩家的id，无法调用接口
                return Response::json(array('error' => 'Can not find such Player.'), 403);
            }
            $game_api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
            $partners = $this->getPartners();  
            if(!array_key_exists($partner_id, $partners)){  //如果通过页面修改传给后端不合法的值，需要判断
                return Response::json(array('error' => 'No such Partner.'), 403);
            }
            $partner_name = $partners[$partner_id];
            $params = array(
                'player_id' => $player_id,
                'table_id' => $partner_id,
                );
            $game_result = $game_api->sendPartner($params);
            if(isset($game_result->result) && 'OK' == $game_result->result){
                $operation = new Operation();   //做记录
                $operation->operate_time = time();
                $operation->game_id = $game_id;
                $operation->giftbag_id = $partner_id;
                $operation->player_name = $player_name;
                $operation->player_id = $player_id;
                $operation->operator = Auth::user()->username;
                $operation->server_name = $server->server_name;
                $operation->operation_type = 'send_partner';
                $operation->extra_msg = $partner_name;
                $operation->save();
                $msg = 'Send to ('.$player_id.':'.$player_name.') '.$partner_name.' OK';
                return Response::json(array('msg' => $msg));
            }else{
                return Response::json(array('error' => 'Send Failed.'), 403);
            }
        }
        if('check' == $type){   //检查记录
            //获取此类型所需参数
            $start_time = strtotime(trim(Input::get('start_time')));
            $end_time = strtotime(trim(Input::get('end_time')));
            $operator = Input::get('operator');

            $result2view = Operation::where('game_id', $game_id)->where('operation_type', 'send_partner')->whereBetween('operate_time', array($start_time, $end_time));
            //可选项限制
            if($operator){
                $result2view = $result2view->where('operator', $operator);
            }
            if($partner_id){
                $result2view = $result2view->where('giftbag_id', $partner_id);
            }
            //有id优先使用id
            if($player_id){
                $result2view = $result2view->where('player_id', $player_id);
            }elseif($player_name){  //无id有名字的时候用名字
                $result2view = $result2view->where('player_name', $player_name);
            }
            $result = $result2view->selectRaw("*, from_unixtime(operate_time) as time")->orderBy('operate_time', 'desc')->get();

            if(count($result)){
                $keys = array('operator', 'time', 'player_id', 'player_name', 'server_name', 'extra_msg', 'giftbag_id');
                $result2view = array();
                foreach ($result as $value) {
                    $tmp = array();
                    foreach ($keys as $key) {
                        $tmp[$key] = $value->$key;
                    }
                    $result2view[] = $tmp;
                    unset($tmp);
                }
                return Response::json(array('records' => $result2view));
            }else{
                return Response::json(array('error' => 'No record found.'), 403);
            }
        }
    }
}