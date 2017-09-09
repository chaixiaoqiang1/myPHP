<?php

class PokerUserInfoController extends \BaseController {
/*
    查看玩家信息
    */
  const SERVER_IP =  "119.81.84.118";
  
  public function userIndex()
  {
    $data = array(
      'content' => View::make('serverapi.poker.user'),
    );
    return View::make('main', $data);
  }

    public function getUser()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
                'value' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        }
        $value = trim(Input::get('value'));
        $choice = (int) Input::get('choice');
        $game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id');  //8
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key,
                $game->eb_api_secret_key);
        $platform = Platform::find(Session::get('platform_id'));
        if (! $platform)
        {
            return Response::json($msg, 404);
        }
        if ($choice == 0)
        { // 根据官网账号查询
            $email = $value;
            $response = $api->getUserByEmail($platform->platform_id, $email, $server_internal_id = 1, $game->game_id);
            if(empty($response->body)){
              return Response::json(array('error'=>$email.' No data.'), 403);
            }
            if (isset($response->body->players[0]->player_id)) {
                $player_id = $response->body->players[0]->player_id;
            } 
            
         
        } else if($choice == 1)
        { // 根据UID查询
            $uid = $value;
            $response = $api->getUserByUID($platform->platform_id, $uid, $server_internal_id = 1, $game->game_id);
            if(200 != $response->http_code){
              return Response::json(array('error'=>'似乎无法根据此UID找到玩家'), 403);
            }
            if(empty($response->body)){
              return Response::json(array('error'=>$uid.' No data.'), 403);
            }
            $val  = $response->body;
            if (isset($val)) {
              $player_id = (int)$val->players[0]->player_id;
            }
            
        } else if($choice == 2) { // 根据player_name查询
            $player_name=  $value;
            $response = $api->getUserByPlayerName($platform->platform_id, $player_name, $server_internal_id = 1, $game_id, $tp_code='fb');
            if(empty($response->body)){
              return Response::json(array('error'=>$player_name.' No data.'), 403);
            }
            $response->body = (array)$response->body;
            $v = $response->body[0];
           if (isset($v)) {
             $player_id = $v->player_id;
           }
           
        } else if($choice == 3) { // 根据player_id查询
            $player_id = (int)$value;
            $response = $api->getUserByPlayerID($platform->platform_id, $player_id, $server_internal_id = 1, $game_id, $tp_code='fb');
            if(empty($response->body)){
              return Response::json(array('error'=>$player_id.' No data.'), 403);
            }
        }
        //获得游戏端数据
        $server = Server::find(13);
        if (!$server) {
          return Response::json($msg, 403);
        }
        if (isset($player_id)) {
            $api_server =  PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
            $response1 = $api_server->getPlayer($player_id);
        }
        $user = array();
        if ($choice == 0 || $choice == 1) {
        $v = $response->body;
        //$user['nickname'] = isset($v->nickname) ? $v->nickname : '';
        $user['uid']   = isset($v->uid) ? $v->uid : '';
        $user['login_email']   = isset($v->login_email) ? $v->login_email : '';
        $user['contact_email']   = isset($v->contact_email) ? $v->contact_email : '';
        $user['created_ip']   = isset($v->created_ip) ? $v->created_ip : '';
        $user['created_time']   = isset($v->created_time) ? $v->created_time : '';
        $user['last_visit_ip']   = isset($v->last_visit_ip) ? $v->last_visit_ip : '';
        $user['last_visit_time']   = isset($v->last_visit_time) ? $v->last_visit_time : '';
        $user['is_anonymous']   = isset($v->is_anonymous) ? $v->is_anonymous : '';
        $user['u']   = isset($v->u) ? $v->u : '';
        $user['u2']   = isset($v->u2) ? $v->u2 : '';
        $user['source']   = isset($v->source) ? $v->source : '';
        $user['u2']   = isset($v->u2) ? $v->u2 : '';
        $user['name'] = isset($v->name) ? $v->name : '';

      if (isset($v->players[0])) {

        $vv = $v->players[0];
        $user['player_id'] = $vv->player_id;
        $user['player_name'] = $vv->player_name;
        $user['all_pay_amount'] = round($vv->all_pay_amount, 2) .'  USD';
        $user['all_pay_times']  = $vv->all_pay_times;
        $user['avg_pay_amount']  = $vv->all_pay_times > 0 ? round($vv->all_pay_amount/$vv->all_pay_times, 2) : 0;
        $user['first_lev']      = $vv->first_lev;
    }
      
        } elseif ($choice == 2 || $choice == 3) {
          if(is_array($response->body)){
            $v = $response->body[0];
            $user ['uid'] = isset($v->uid) ? $v->uid : '';
            //$user ['nickname'] = isset($v->nickname) ? $v->nickname : '';
            $user ['player_id'] = isset($v->player_id) ? $v->player_id : '';
            $user ['login_email'] = isset($v->login_email) ? $v->login_email : '';
            $user ['player_name'] = isset($v->player_name) ? $v->player_name : '';
            $user ['u'] = isset($v->u) ? $v->u : '';
            $user ['u2'] = isset($v->u2) ? $v->u2 : '';
            $user ['is_anonymous'] = isset($v->is_anonymous) ? $v->is_anonymous : 0;
            $user ['all_pay_times'] = isset($v->all_pay_times) ? $v->all_pay_times : 0;
            if ($game_id == 11) {
                $user ['all_pay_amount'] = isset($v->all_pay_amount) ? round($v->all_pay_amount, 2) : 0;
                $user ['all_pay_amount'] = $user ['all_pay_amount'] . '  USD';
            } else {
                $user ['all_pay_amount'] = isset($v->all_pay_amount) ? $v->all_pay_amount : 0;
            }
            $user ['avg_pay_amount'] = $v->all_pay_times > 0 ? round($v->all_pay_amount/$v->all_pay_times, 2) : 0;
            $user ['first_lev'] = isset($v->first_lev) ? $v->first_lev : '';
            $user ['source'] = isset($v->source) ? $v->source : '';
            $user ['last_visit_ip'] = isset($v->last_visit_ip) ? $v->last_visit_ip : '';
            $user ['last_visit_time'] = isset($v->last_visit_time) ? $v->last_visit_time : '';
            $user ['created_ip'] = isset($v->created_ip) ? $v->created_ip : '';
            $user ['created_time'] = isset($v->created_time) ? $v->created_time : '';
            $user ['contact_email'] = isset($v->contact_email) ? $v->contact_email : '';
            $user ['name'] = isset($v->name) ? $v->name : '';
          }
        }
        if (!empty($response1) && isset($response1->lev)) {
//             $s = var_export($response1,true);
//             Log::info($s);
          $user['lev'] = $response1->lev;
          $user['exp'] = $response1->exp;
          $user['chips'] = $response1->chips;
          $user['gold'] = $response1->gold;
          $user['online'] = isset($response1->online) ? '是' : '否';
          $user['room_id'] = isset($response1->room_id) ? $response1->room_id : '';
          $user['firend_num'] = $response1->friend_num;
          $user['max_chips'] = $response1->max_chips;
          $user['strongbox_chips'] = isset($response1->strongbox_chips) ? $response1->strongbox_chips :"";
          $user['strongbox_password'] = isset($response1->strongbox_password) ? $response1->strongbox_password : '';
          //$user['win_times'] =$response1->win_times;
          $user['play_times'] = $response1->play_times;
          if(isset($response1->is_ban) && $response1->is_ban==0){
            $user['is_ban'] = '正常';
          }elseif(isset($response1->is_ban) && $response1->is_ban>0){
            $user['is_ban'] = '封号';
          }else{
            $user['is_ban'] = '数据异常';
          }
          //$user['vip'] = $response1->vip;
          switch ($response1->vip) {
            case 1:
              $user['vip'] = '红卡';
              break;
            case 2:
              $user['vip'] = '紫卡';
              break;
            case 3:
              $user['vip'] = '白金';
              break;
            case 4:
              $user['vip'] = '钻石';
              break;
          }
          $user['vip_lev'] = $response1->vip_lev;
          $user['vip_exp'] = $response1->vip_exp;
          $user['is_recharge'] = isset($response1->is_recharge) ? '是' : '否';
          $user['allin_rate'] = $response1->allin_rate;
        }

        if ($response->http_code != 200)
        {
            return Response::json($response->body, 404);
        }    
        if(isset($player_id)){
          $player_id = $player_id;
        }elseif(isset($user['player_id'])){
          $player_id = $user['player_id'];
        }else{
          unset($player_id);
        }
        $operations = array();
        if(isset($player_id)){
          $tmp_operations = Operation::join('users as u', 'operator', '=', 'u.user_id')
                                  ->where('player_id', $player_id)
                                  ->whereIn('game_id', array(11,52))
                                  ->whereIn('operation_type', array('poker-freeze', 'poker-giftbag', 'poker-chips'))
                                  ->selectRaw('u.username, from_unixtime(operate_time) as time, extra_msg as msg')
                                  ->get();
          if($tmp_operations){
            foreach ($tmp_operations as $tmp_operation) {
              $operations[] = array(
                'username' => $tmp_operation->username,
                'time' => $tmp_operation->time,
                'msg' => $tmp_operation->msg,
                );
            }
          }
        }

        $user = array(
          'user' => $user,
          'operations' => $operations,
          );   
        return Response::json($user);
    }

    public function onlinePlayerIndex()
    {
      //0xfcf6
      $data = array(
          'content' => View::make('serverapi.poker.users.online'),
      );
      return View::make('main',$data);
    }

    public function onlinePlayerData()
    {
      $msg = array(
          'code' => Config::get('errorcode.unknow'),
          'error' => '查询异常',
      );
      $rules = array(
          'start_time' => 'required',
          'end_time' => 'required'
      );

      $validator = Validator::make(Input::all(), $rules);
      if ($validator->fails()){
          $msg['error'] = Lang::get('error.basic_input_error');
          return Response::json($msg, 403);
      }
      $start_time = strtotime(trim(Input::get('start_time')));
      $end_time = strtotime(trim(Input::get('end_time')));
      $server = Server::find(13);
      $api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
      $start = date('Y-m-d', $start_time);
      $end = date('Y-m-d', $end_time);
      if($start == $end){
        $response = $api->getOnlineNum($start);
      }else{
        $response = $api->getOnlineNum($start);
        $response2 = $api->getOnlineNum($end);
      }

      $data = array();
      if (isset($response->player_nums)) {
          $day1 =array();
          $player_nums = $response->player_nums;
          $len = count($player_nums);
          $largest_time = 0;
          for ($i=0; $i < $len; $i++) { 
              $day1[$i] = new stdClass();
              $day1[$i]->time = "";
              $day1[$i]->num = "";
              $day1[$i]->playing = "";
              $day1[$i]->time = date("H:i", $player_nums[$i]->time);  
              if($player_nums[$i]->time >= $largest_time){
                $largest_time = $player_nums[$i]->time;
              }
              $day1[$i]->num = $player_nums[$i]->number;
              $day1[$i]->playing = isset($player_nums[$i]->playing) ? $player_nums[$i]->playing : 0;
          }
          if($len < 144){ //一天应该有144条数据，若不足则填空的
            for($i = $len; $i < 144; $i++){
                $largest_time += 600;
                $day1[$i] = new stdClass();
                $day1[$i]->time = "";
                $day1[$i]->num = "";
                $day1[$i]->playing = "";
                $day1[$i]->time = date("H:i", $largest_time);  
            }
          }
          $lenn = count($day1);
          for ($i=1; $i < $lenn; $i++) { 
              for ($j=$lenn-1; $j >= $i; $j--) { 
              	  if ($day1[$j]->time < $day1[$j-1]->time) {
              	  	  $x = $day1[$j];
              	  	  $day1[$j] = $day1[$j-1];
              	  	  $day1[$j-1] = $x;
              	  }
              }
          }
          $data['day1'] = $day1;
          unset($day1);
          unset($player_nums);
      }
      if(isset($response2) && isset($response2->player_nums)){
          $day2 = array();
          $player_nums = $response2->player_nums;
            $len = count($player_nums);
            $largest_time = 0;
            for ($i=0; $i < $len; $i++) { 
                $day2[$i] = new stdClass();
                $day2[$i]->time = "";
                $day2[$i]->num = "";
                $day2[$i]->playing = "";
                $day2[$i]->time = date("H:i", $player_nums[$i]->time);  
                if($player_nums[$i]->time >= $largest_time){
                  $largest_time = $player_nums[$i]->time;
                }
                $day2[$i]->num = $player_nums[$i]->number;
                $day2[$i]->playing = isset($player_nums[$i]->playing) ? $player_nums[$i]->playing : 0;
            }
            if($len < 144){ //一天应该有144条数据，若不足则填空的
              for($i = $len; $i < 144; $i++){
                  $largest_time += 600;
                  $day2[$i] = new stdClass();
                  $day2[$i]->time = "";
                  $day2[$i]->num = "";
                  $day2[$i]->playing = "";
                  $day2[$i]->time = date("H:i", $largest_time);  
              }
            }
            $lenn = count($day2);
            for ($i=1; $i < $lenn; $i++) { 
                for ($j=$lenn-1; $j >= $i; $j--) { 
                    if ($day2[$j]->time < $day2[$j-1]->time) {
                        $x = $day2[$j];
                        $day2[$j] = $day2[$j-1];
                        $day2[$j-1] = $x;
                    }
                }
            }
          $data['day2'] = $day2;
          unset($day2);
          unset($player_nums);
      }

      $data['length'] = count($data);
      return Response::json($data);

    }

    private function array_sort($arr,$keys,$type='asc')
    {
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v){
          $keysvalue[$k] = $v[$keys];
        }
        if($type == 'asc'){
          asort($keysvalue);
        }else{
          arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){
          $new_array[$k] = $arr[$k];
        }
        return $new_array; 
    }

    //统计玩家碎片

    public function userPieceIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.users.piece'),
        );
        return View::make('main', $data);
    }

    public function userPieceData()
    {
        $server = Server::find(13);
        $api = PokerGameServerApi::connect(self::SERVER_IP, $server->api_server_port);
        $response = $api->userPieceData();
        $data = array();
        foreach ($response as $key => $value) {
            $data[] = array(
                'player_name' => $value->player_name,
                'player_id' => $value->player_id,
                'wn' => $value->fragment1,
                'up' => $value->fragment5,
                'wxjp' => $value->fragment12,
                'wj' => $value->fragment2,
                'sb' => $value->fragment8,
                'dzb' => $value->fragment10,
                'Flux' => $value->fragment9,
                'PS2' => $value->fragment19,
                'ltcj' => $value->fragment16,
                'sxpb' => $value->fragment26,
                'sxxj' => $value->fragment20,
                'sxsj' => $value->fragment25,
                'yjds' => $value->fragment22
            );
        }
        if (isset($data)) {
            return Response::json($data);
        } else{
          return Response::json('error');
        }
    }
    //同IP查询
    public function sameIpIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.users.same_ip'),
        );
        return View::make('main', $data);
    }

    public function sameIpData()
    {   
        $msg = array(
           'code' => Config::get('errorcode.unknow'),
           'error' => Lang::get('error.basic_input_error'),
        );
        $ip = Input::get('ip');
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server = Server::find(13);
       // var_dump($game);die();
        if (!$server) {
            return Response::json($msg, 403);
        }
        $server_internal_id = $server->server_internal_id;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->sameIpData($platform_id, $game_id, $server_internal_id,$ip);
        //var_dump($count);die();
        $data = array();
        $datas=array();
      /* foreach ($response as $key => $value) {
            $data[] = array(
                'ip'=>$value->ip,
                'player_id' => $value->player_id,
                'player_name' => $value->player_name,
                'created_time'=>$value->created_time,
                'tongqian'=>$value->tongqian
            );
        }*/
        if ($response->http_code == 200 && isset($response->body)) {
          $body = $response->body;
          $user1 = $body->user1;
          $user2 = $body->user2;
          $sums=count($user2);
          //var_dump($user1);die();
          for($i=0;$i<$sums;$i++){
              $datas[$i] = array(
                    'ip' => isset($user1[$i]->ip) ? $user1[$i]->ip : '',
                    'player_id' => isset($user2[$i][0]->player_id) ? $user2[$i][0]->player_id : '',
                    'player_name'=>isset($user2[$i][0]->player_name) ? $user2[$i][0]->player_name : '',
                    'time'=>isset($user1[$i]->time) ? $user1[$i]->time : '',
                    'type'=>isset($user1[$i]->type) ? $user1[$i]->type : '',
                    'tongqian'=>isset($user2[$i][0]->tongqian) ? $user2[$i][0]->tongqian : ''
             );
          }
          
        }
        $data=$datas;
        //var_dump($data);die();
        if (isset($data)) {
            return Response::json($data);
        } else{
          return Response::json('error');
        }
    }
    //德扑比赛查询
    public function matchRankIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.users.rank'),
        );
        return View::make('main', $data);
    }
    public function matchRankData()
    {   
        $msg = array(
           'code' => Config::get('errorcode.unknow'),
           'error' => Lang::get('error.basic_input_error'),
        );
        $player_id = Input::get('player_id');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        //var_dump($game_id);die();
        $game = Game::find($game_id);
        $server = Server::find(13);
       // var_dump($game);die();
        if (!$server) {
            return Response::json($msg, 403);
        }
        $server_internal_id = $server->server_internal_id;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->matchRankData( $game_id,$platform_id, $server_internal_id,$player_id,$start_time,$end_time);
        //var_dump($response);die();
        $data = array();
        if ($response->http_code == 200 && isset($response->body)) {
            $body = $response->body;
            //var_dump($body);die();
            foreach ($body as $key => $value) {
              //var_dump($value->match_type);die();
                $data[] = array(
                    'match_type' => isset($value->match_type) ? $value->match_type : '',
                    'match_id' => isset($value->match_id) ? $value->match_id : '',
                    'rank' => isset($value->player_ranking) ? $value->player_ranking : '',
                    'get_tongqian' => isset($value->acquire_tongqian) ? $value->acquire_tongqian : '',
                    'get_token' => isset($value->acquire_token) ? $value->acquire_token : '',
                    'get_fragment' => isset($value->acquire_fragment) ? $value->acquire_fragment : '',
                    'get_googs' => isset($value->acquire_goods) ? $value->acquire_goods : '',
                    'get_integral' => isset($value->acquire_integral) ? $value->acquire_integral : '',
                );
            }
      }
        //var_dump($data);die();
        if (isset($data)) {
            return Response::json($data);
        } else{
          return Response::json('error');
        }
    }
    //德扑比赛场用户玩牌
    public function matchAreaIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.users.matchArea'),
        );
        return View::make('main', $data);
    }
    public function matchAreaData()
    {   
        $msg = array(
           'code' => Config::get('errorcode.unknow'),
           'error' => Lang::get('error.basic_input_error'),
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server = Server::find(13);
        if (!$server) {
            return Response::json($msg, 403);
        }
        $server_internal_id = $server->server_internal_id;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->matchAreaData( $game_id,$platform_id, $server_internal_id,$start_time,$end_time);
        //var_dump($response);die();
        $data = array();
        if ($response->http_code == 200 && isset($response->body)) {
            $body = $response->body;
            //var_dump($body);die();
             $col='玩牌人数';
            foreach ($body as $key => $value) {
              //var_dump($value->match_type);die();
                $data[] = array(
                    'col' => $col,
                    'total' =>$value->sit+$value->kej+$value->round+$value->spin+$value->iphone6+$value->dewa,
                    'sit' => $value->sit,
                    'kej' =>$value->kej,
                    'round' =>$value->round,
                    'spin' =>$value->spin,
                    'iphone6' =>$value->iphone6,
                    'dewa' =>$value->dewa,
                );
                $col='发放筹码';
            }
      }
        //var_dump($data);die();
        if (isset($data)) {
            return Response::json($data);
        } else{
          return Response::json('error');
        }
    }
    //德扑游戏场用户玩牌
    public function gameAreaIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.users.gameArea'),
        );
        return View::make('main', $data);
    }
    public function gameAreaData()
    {   
        $msg = array(
           'code' => Config::get('errorcode.unknow'),
           'error' => Lang::get('error.basic_input_error'),
        );
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server = Server::find(13);
        if (!$server) {
            return Response::json($msg, 403);
        }
        $server_internal_id = $server->server_internal_id;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->gameAreaData( $game_id,$platform_id, $server_internal_id,$start_time,$end_time);
        //var_dump($response);die();
        $data = array();
        if ($response->http_code == 200 && isset($response->body)) {
            $body = $response->body;
            //var_dump($body);die();
           
            foreach ($body as $key => $value) {
                
                $data[] = array(
                    'total' =>$value->num1+$value->num2+$value->num3+$value->num4+$value->num5+$value->num6+$value->num7+$value->num8+$value->num9+$value->num10+$value->num11+$value->num12+$value->num13+$value->num14+$value->num15,
                    'num1' => $value->num1,
                    'num2' => $value->num2,
                    'num3' => $value->num3,
                    'num4' => $value->num4,
                    'num5' => $value->num5,
                    'num6' => $value->num6,
                    'num7' => $value->num7,
                    'num8' => $value->num8,
                    'num9' => $value->num9,
                    'num10' => $value->num10,
                    'num11' => $value->num11,
                    'num12' => $value->num12,
                    'num13' => $value->num13,
                    'num14' => $value->num14,
                    'num15' => $value->num15,
                );
            }
        }
        //var_dump($data);die();
        if (!empty($data)) {
            return Response::json($data);
        } else{
          return Response::json('error');
        }
    }
}

