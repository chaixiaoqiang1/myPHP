<?php

class MailController extends \BaseController
{
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
    
    public function index()
    {
        //$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
        $game_id = Session::get('game_id');
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.mail.index',
                array(
                    'servers' => $servers,
                    'game_id' => $game_id
                ))
        );
        return View::make('main', $data);
    }

    public function makeClickableLinks($text)
    {
        // 真的不是我写的=.=
        return preg_replace(
            '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i',
            '<a href="$1" target="_blank"><u>$1</u></a>', $text);
    }

    public function send()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'player_id' => 'required',
            'mail_title' => 'required',
            'mail_body' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $result = array();
        $server_ids = Input::get('server_id');
        $title = trim(Input::get('mail_title'));
        $body = trim(Input::get('mail_body'));
        $game_id = Session::get('game_id');
        $area_id = (int)Input::get('area_id');//针对台湾和英文世界服分国旗
        //
        $body = $this->makeClickableLinks($body);
        //
        $player_id = (int)Input::get('player_id');
        if ($player_id == -1) { // 向全服发送，可以多服
            $i = 0;
            foreach ($server_ids as $server_id) {
                $server = Server::find($server_id);
                if (!$server) {
                    $msg['error'] = Lang::get('error.basic_not_found');
                    return Response::json($msg, 404);
                }
                if($game_id != $server->game_id){
                    return Response::json(array('error'=>'please check the current platform and servers!'), 403);
                }
                $data = array(
                    '1' => $server->api_server_ip,
                    '2' => $server->api_server_port,
                    '3' => $server->api_dir_id
                );
                //Log::info(var_export($data, true));
                $api = GameServerApi::connect($server->api_server_ip,
                    $server->api_server_port, $server->api_dir_id);
                if(in_array($game_id, $this->world_edition_list)) {
                    if(($game_id == 59 || $game_id == 63) && $area_id != 0){//针对台湾和英文分国旗
                        $response = $api->sendMail(-1, $title, $body, array(), $area_id);
                    }else{
                        $response = $api->sendMail(-1, $title, $body, array(), $game_id);
                    }
                    
                }
                else {
                    $response = $api->sendMail(-1, $title, $body);
                }
                $i++;
                //Log::info(var_export($response, true));
                if (isset($response->result) && $response->result == 'OK') {
                    $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' .
                            $response->result . "\n",
                        'status' => 'ok'
                    );
                } else {
                    $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' .
                            'error' . $i . "\n",
                        'status' => 'error'
                    );
                }

            }
        } else { // 向单服的某个id发送
            if (count($server_ids) != 1) {
                return Response::json($msg, 403);
            } else {
                $server_id = $server_ids[0];
                $server = Server::find($server_id);
                if (!$server) {
                    $msg['error'] = Lang::get('error.basic_not_found');
                    return Response::json($msg, 404);
                }
                if($game_id != $server->game_id){
                    return Response::json(array('error'=>'please check the current platform and servers!'), 403);
                }
                $api = GameServerApi::connect($server->api_server_ip,
                    $server->api_server_port, $server->api_dir_id);
                if(in_array($game_id, $this->world_edition_list)){
                    if(($game_id == 59 || $game_id == 63) && $area_id != 0){//针对台湾和英文分国旗
                        $game_id = $area_id;
                    }
                    $response = $api->sendMail($player_id, $title, $body, array(),$game_id);
                }else {
                    $response = $api->sendMail($player_id, $title, $body);
                }
                if (isset($response->result) && $response->result == 'OK') {
                    $result[] = array(
                        'msg' => ' player_id( ' . $player_id . ' ) : ' .
                            $response->result . '  ',
                        'status' => 'ok'
                    );
                } else {
                    $result[] = array(
                        'msg' => ' player_id( ' . $player_id . ' ) : ' .
                            $response->error_code . '  ',
                        'status' => 'error'
                    );
                }
            }
        }
        $msg = array(
            'result' => $result
        );
        return Response::json($msg);
    }

    public function giftMailIndex()
    {
        $servers = $this->getUnionServers();
        $award = $this->initTable('award');
        $items = $this->initTable('item', $this->area_item_id);
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if('nszj' == $game->game_code){
            $marks = $this->initTable('marklevel',$this->area_mark_id);
        }
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $item = array(); 
        foreach ($items as $k => $v) {
            $item[] = $v->name . ':' . $v->id;
        }

        $data = array(
            'content' => View::make('serverapi.flsg_nszj.mail.gift-mail',
                array(
                    'servers' => $servers,
                    'award' => $award,
                    'item' => $item,
                    'game_id' => $game_id,
                    'game_code' => $game->game_code,
                    'marks' => ('nszj' == $game->game_code) ? $marks : ''
                ))
        );
        return View::make('main', $data);
    }

    public function giftMail()
    {
        //Log::info(var_export(Session::get('game_id'),true));
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'mail_title' => 'required',
            'mail_body' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $result = array();
        //
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $send_type = (int)Input::get('send_type');
        //Log::info(var_export($game_id,true));

        $title = trim(Input::get('mail_title'));
        $body = trim(Input::get('mail_body'));
        $area_id = (int)Input::get('area_id');//针对台湾和英文世界服分国旗
        $need_level = (int)Input::get('need_level');//风流三国全服邮件最小玩家等级
        $attachments = array();
        $operator = Auth::user()->username;
        for ($i = 1; $i <= 6; $i++) {
            $award_id = (int)Input::get('award_id' . $i);
            $amount = (int)Input::get('amount' . $i);
            if($amount > 100000){   //不允许100000以上的操作
                if('mnsg' == $game->game_code && 1 == $award_id){   //铜钱不应该限制100000--mnsg
                    if($amount > 100000000){
                        return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
                    }
                }elseif('mnsg' != $game->game_code && 2 == $award_id){  //铜钱不应该限制100000--!mnsg
                    if($amount > 100000000){
                        return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
                    }
                }elseif('flsg' == $game->game_code || 'nszj' == $game->game_code){  //在这里排除一部分页游不需要判断数值的项目
                    if(14 == $award_id){    //玩家经验值，不需要判断

                    }else{  //不是玩家经验值
                        return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
                    }
                }else{
                    return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
                }
            }

            if('mnsg' != $game->game_code){
                if(30 != $game_id && 45 != $game_id && 4 != $game_id && 50 != $game_id){                 //对英文风流三国和印尼女神发放邮件物品不做数量限制    //印尼三国印尼大乱斗也加入此名单5.15
                    if ($amount > 5000 && $award_id == 1) {
                        $msg['error'] = Lang::get('error.amount_exceed');
                        return Response::json($msg, 404);
                    }
                    if ($amount > 100000000 && $award_id == 2) {
                        $msg['error'] = Lang::get('error.amount_exceed');
                        return Response::json($msg, 404);
                    }
                    if ($amount > 500 && $award_id == 5) {
                        $msg['error'] = Lang::get('error.amount_exceed');
                        return Response::json($msg, 404);
                    }
                    if ($amount > 2000 && $award_id == 13) {
                        $msg['error'] = Lang::get('error.amount_exceed');
                        return Response::json($msg, 404);
                    }
                    // if ($amount > 100000000 && $award_id == 14) {    //经验的限制取消
                    //     $msg['error'] = Lang::get('error.amount_exceed');
                    //     return Response::json($msg, 404);
                    // }
                }
            }


            if ($award_id) {
                if('mnsg' == $game->game_code){ //因为萌娘三国数据发送接口不同，因此特殊处理
                    if(!isset($mail_data)){ //从award文件中读取所有数值类型和限制
                        $mnsg_mail_data = $this->initTable('award');
                        $mail_data = array();
                        foreach ($mnsg_mail_data as $value) {
                            $mail_data[$value->id] = array(
                                'name' => $value->ename,
                                'limit' => $value->limit,
                                'cname' => $value->cname,
                                );
                        }
                    }
                    if('9' == $award_id){
                        $item_id_name = Input::get('item_id' . $i);
                        $gift_id_name = explode(":", $item_id_name);
                        try{
                            $item_id = (int)$gift_id_name[1];
                        }catch(\Exception $e){
                            return Response::json($msg, 403);
                        }
                        $attachments[] = array(
                            'item' => $item_id,
                            'num' => $amount
                        );
                        if('2' == $send_type){  //萌娘三国全服邮件要对物品数量等进行一定的限制
                            if($amount > 50){
                                return Response::json(array('error'=>'全服邮件物品数量不可超过50'), 403);
                            }
                        }
                    }else{
                        if(isset($mail_data[$award_id])){
                            $attachments[] = array(
                                'item' => $mail_data[$award_id]['name'],
                                'num' => $amount
                            );
                            if('2' == $send_type){  //萌娘三国全服邮件要对物品数量等进行一定的限制
                                if($amount > $mail_data[$award_id]['limit']){
                                    return Response::json(array('error'=>'全服邮件'.$mail_data[$award_id]['cname'].'数量不可超过'.$mail_data[$award_id]['limit']), 403);
                                }
                            }
                        }
                    }
                }else{
                    if (9 == $award_id) {
                        $item_id_name = Input::get('item_id' . $i);
                        $gift_id_name = explode(":", $item_id_name);
                        try{
                            $item_id = (int)$gift_id_name[1];
                        }catch(\Exception $e){
                            return Response::json($msg, 403);
                        }
                        
                    }elseif('nszj' == $game->game_code && 8 == $award_id){
                        $item_id_name = Input::get('mark_id' . $i);
                        $gift_id_name = explode(":", $item_id_name);
                        try{
                            $item_id = (int)$gift_id_name[1];
                            $amount = 1;
                        }catch(\Exception $e){
                            return Response::json($msg, 403);
                        }
                    } else {
                        $item_id = 1; // 任意数值
                    }
                    $attachments[] = array(
                        'attach_id' => $i,
                        'award_id' => $award_id,
                        'award_itemid' => $item_id,
                        'award_value' => $amount
                    );
                } 
            }else {
                break;
            }
        }
        if('mnsg' != $game->game_code){
            $body = $this->makeClickableLinks($body);
        }
        //
        if ($send_type == 1) { // 对多个玩家发
            $player_ids = (int)Input::get('player_id');
            $gift_datas = Input::get('gift_data');
            $name_or_id = Input::get('name_or_id');
            $gift_datas = explode("\n", $gift_datas);
            $fail_player_name = array();
            $fail_player_id = array();
            $success_player_id = array();
            $success_player_name = array();
            foreach ($gift_datas as &$v) {
                $v = trim($v);
            }
            unset($v);
            $gift_datas = array_unique($gift_datas);
            $result = array();
            $ok = array();
            $error = array();
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            foreach ($gift_datas as $gift_data) {
                $gift_data = explode("\t", $gift_data, 2);
                if (count($gift_data) != 2) {
                    $error[] = $gift_data[0] . ': No Server Name. ';
                    continue;
                }
                $server_name = trim($gift_data[1]);
 //               Log::info(var_export($server_name,true));
                $server = Server::currentGameServers()->where('server_track_name', $server_name)->first();
//                Log::info(var_export($server,true));
                if (!$server) {
                    $error[] = $gift_data[0] .
                        "({$gift_data[1]}) Server Not Found. ";
                    continue;
                }
                if(Session::get('game_id') != $server->game_id){
                    return Response::json(array('error'=>'please check the current platform and servers!'), 403);
                }
                if('mnsg' == $game->game_code){
                    $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
                }else{
                    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                }
                $player_id = 0;
                if('1' == $name_or_id){
                    $player_id = (int)$gift_data[0];
                    if(0 == $player_id){
                       $error[] = $gift_data[0] . ' ' . "({$gift_data[1]}) Not Found player_id"; 
                    }
                }elseif('2' == $name_or_id){
                    if('mnsg' == $game->game_code){
                        $player = $slave_api->getplayeridbyname($game_id, $gift_data[0], $server->server_internal_id, $platform_id);
                        if(200 == $player->http_code){
                            $player_id = (int)$player->body[0]->player_id;
                        }else{
                           $error[] = $gift_data[0] . ' ' . "({$gift_data[1]}) Not Found player_id";
                           continue; 
                        } 
                    }else{
                        $player = $api->getPlayerInfoByName($gift_data[0]);
                        if(isset($player->player_id) && $player->player_id){
                            $player_id = (int)$player->player_id;
                        }else{
                            $error[] = $gift_data[0] . ' ' . "({$gift_data[1]}) Not Found player_id";
                            continue;
                        }
                        
                    }       
                    
                }
                
                if(in_array($game_id, $this->world_edition_list)){
                    if($area_id != 0){//针对台湾和英文分国旗
                        $response = $api->sendMail($player_id, $title, $body, $attachments, $area_id);
                    }else{
                        $response = $api->sendMail($player_id, $title, $body, $attachments, $game_id);
                    } 
                }elseif('mnsg' == $game->game_code){
                    $response = $api->mnsgsendMail($player_id, $title, $body, $attachments);
                }else{
                    $response = $api->sendMail($player_id, $title, $body, $attachments);
                }
                if (isset($response->result) && $response->result == 'OK') {
                    $ok[] = $gift_data[0] . ' ' . "({$gift_data[1]}) OK. ";

                    if('1' == $name_or_id){
                        $success_player_id[] = $player_id;
                    }elseif('2' == $name_or_id){
                        $success_player_name[$player_id] = $gift_data[0];
                    }
                } else {
                    $error[] = $gift_data[0] . ' ' . "({$gift_data[1]}) Error";

                    if('1' == $name_or_id){
                        $fail_player_id[] = $player_id;
                    }elseif('2' == $name_or_id){
                        $fail_player_name[$player_id] = $gift_data[0];
                    }

                }
                if('1' == $name_or_id){
                    if(!empty($fail_player_id)){
                        $this->insert_gift_msg_id($fail_player_id, $attachments, $game_id, $operator, 'fail', $server, $api);
                        unset($fail_player_id);
                    }
                    if(!empty($success_player_id)){
                        $this->insert_gift_msg_id($success_player_id, $attachments, $game_id, $operator, 'success', $server, $api);
                        unset($success_player_id);
                    }
                }elseif('2' == $name_or_id){
                    if(!empty($fail_player_name)){
                        $this->insert_gift_msg_name($fail_player_name, $attachments, $game_id, $operator, 'fail', $server, $api);
                        unset($fail_player_name);
                    }
                    if(!empty($success_player_name)){
                        $this->insert_gift_msg_name($success_player_name, $attachments, $game_id, $operator, 'success', $server, $api);
                        unset($success_player_name);
                    }
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
        } else if ($send_type == 2) { // 对全服发邮件
            $server_ids = Input::get('server_id');
            if(empty($server_ids)){
               return Response::json(array('error'=>'Did you select a server?'), 403); 
            }
            if('flsg' == $game->game_code){
                $extra_msg = $need_level . '级以上玩家全服物品邮件|';
            }else{
                $extra_msg = '全服物品邮件';
            }
            
            // 向全服发送，可以多服
            foreach ($server_ids as $server_id) {
                $server = Server::find($server_id);
                if (!$server) {
                    $msg['error'] = Lang::get('error.basic_not_found');
                    return Response::json($msg, 404);
                }
                $game_id = Session::get('game_id'); //循环的时候每次都要重新对game_id赋值
                if($game_id != $server->game_id){
                    return Response::json(array('error'=>'please check the current platform and servers!'), 403);
                }
                if('mnsg' == $game->game_code){
                    $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
                }else{
                    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                }
                if(in_array($game_id, $this->world_edition_list)) {
                    if(($game_id == 59 || $game_id == 63) && $area_id != 0){//针对台湾和英文分国旗
                        $response = $api->sendMail(-1, $title, $body, $attachments, $area_id, $need_level);
                    }else{
                        $response = $api->sendMail(-1, $title, $body, $attachments, $game_id, $need_level);
                    }
                }elseif('mnsg' == $game->game_code){
                    $response = $api->mnsgsendMailServer(array((int)$server->server_internal_id), $title, $body, $attachments);
                }else {
                    $response = $api->sendMail(-1, $title, $body, $attachments, $game_id = 0, $need_level);
                }
                //Log::info("send mail response=>" . var_export($response, true));
                if (isset($response->result) && $response->result == 'OK') {
                    $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' .
                            $response->result . "\n",
                        'status' => 'ok'
                    );

                    $this->insert_gift_msg($attachments, Session::get('game_id'), $operator, 'success',$server, $extra_msg, 'mail_gift');
                } else {
                    $result[] = array(
                        'msg' => ' ( ' . $server->server_name . ' ) : ' . "\n",
                        'status' => 'error'
                    );

                    $this->insert_gift_msg($attachments, Session::get('game_id'), $operator, 'fail',$server, $extra_msg, 'mail_gift');
                }
                unset($server);
            }
            $res = array(
                'result' => $result
            );
            return Response::json($res);
        }
    }

    public function insert_gift_msg_name($players, $attachments, $game_id, $operator, $status, $server, $api){
        $game = Game::find($game_id);
        $awards = $this->initArrayTable('award');//学妹需要
        foreach ($players as $id => $name) {
            foreach ($attachments as $v) {
                if('mnsg' == $game->game_code){//item  num
                    $v['award_id'] = $v['item'];
                    $v['award_value'] = $v['num'];
                    foreach ($awards as $award) {
                        if($award['ename'] == $v['item']){
                            $v['award_id'] = $award['id'];
                            break;
                        }
                    }
                }
                if(9 == $v['award_id']){//学妹不会出现此情况
                    $gift_bag_id = $v['award_itemid'];
                }else{
                    if('nszj' == $game->game_code && 8 == $v['award_id']){
                       $gift_bag_id = '8'.$v['award_itemid']; //为了区别星座和item
                    }else{
                        $gift_bag_id = $v['award_id'];
                    }
                }
                $operation = Operation::insert(array(
                                    'operate_time' => time(),
                                    'game_id' => $game_id, 
                                    'giftbag_id' => $gift_bag_id,
                                    'player_id' => $id,
                                    'player_name' => $name,
                                    'operator' => $operator,
                                    'server_name' => $server->server_name,
                                    'operation_type' => 'mail_gift',
                                    'extra_msg' => $status . '|name|数量:' . $v['award_value'] ,

                            ));
            }
            
        }
    }

    public function insert_gift_msg_id($players, $attachments, $game_id, $operator, $status, $server, $api){
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $awards = $this->initArrayTable('award');//学妹需要
        foreach ($players as $player) {
            if('mnsg' == $game->game_code){
               $player_info_from_name = $api->getMNSGPlayerInfo($player, $platform_id);
               $player_name = isset($player_info_from_name->player_name) ? $player_info_from_name->player_name : '';
            }elseif(in_array($game->game_code, array('flsg','nszj'))){
                $player_info_from_id = $api->getPlayerInfoByPlayerID($player);
                $player_name = isset($player_info_from_id->name) ? $player_info_from_id->name : '';
            }else{//目前不会执行这里
                $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                $player_names = $slave_api->getplayernamebyid($game_id, $player, $server->server_internal_id, $platform_id);
                if(200 == $player_names->http_code){
                    $player_name = $player_names->body[0]->player_name;
                }else{
                    $player_name = '';
                } 
            }
            
            foreach ($attachments as $v) {
                if('mnsg' == $game->game_code){
                    $v['award_id'] = $v['item'];
                    $v['award_value'] = $v['num'];
                    foreach ($awards as $award) {
                        if($award['ename'] == $v['item']){
                            $v['award_id'] = $award['id'];
                            break;
                        }
                    }
                }
                if(9 == $v['award_id']){
                    $gift_bag_id = $v['award_itemid'];
                }else{
                    if('nszj' == $game->game_code && 8 == $v['award_id']){
                       $gift_bag_id = '8'.$v['award_itemid']; 
                    }else{
                        $gift_bag_id = $v['award_id'];
                    }
                }
                $operation = Operation::insert(array(
                            'operate_time' => time(),
                            'game_id' => $game_id, 
                            'giftbag_id' => $gift_bag_id,
                            'player_id' => $player,
                            'player_name' => $player_name,
                            'operator' => $operator,
                            'server_name' => $server->server_name,
                            'operation_type' => 'mail_gift',
                            'extra_msg' => $status . '|id|数量:' . $v['award_value'],

                    ));
            }
        }
    }

    public function insert_gift_msg($attachments, $game_id, $operator, $status, $server, $extra_msg ,$operation_type){
        $game = Game::find($game_id);
        $awards = $this->initArrayTable('award');//学妹需要
        foreach ($attachments as $v) {
            if('mnsg' == $game->game_code){
                $v['award_id'] = $v['item'];
                $v['award_value'] = $v['num'];
                foreach ($awards as $award) {
                    if($award['ename'] == $v['item']){
                        $v['award_id'] = $award['id'];
                        break;
                    }
                }
            }
            if(9 == $v['award_id']){
                $gift_bag_id = $v['award_itemid'];
            }else{
                if('nszj' == $game->game_code && 8 == $v['award_id']){
                   $gift_bag_id = '8'.$v['award_itemid']; 
                }else{
                    $gift_bag_id = $v['award_id'];
                }
            }
            $operation = Operation::insert(array(
                                'operate_time' => time(),
                                'game_id' => $game_id, 
                                'giftbag_id' => $gift_bag_id,
                                'operator' => $operator,
                                'server_name' => $server->server_name,
                                'operation_type' => $operation_type,
                                'extra_msg' => $status . '|' .$extra_msg . '|数量:'. $v['award_value'],

                        ));
        }
        
    }

}