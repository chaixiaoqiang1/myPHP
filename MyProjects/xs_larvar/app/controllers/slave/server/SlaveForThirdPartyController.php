<?php 

class SlaveForThirdPartyController extends \SlaveServerBaseController {
    private $platform = array(
        78 => 57,
    );
    private $games = array(
        78 => 'mnsg',
    );
    public function getGameOnlineNum(){
        $game_id = Input::get('game_id');
        Log::info('mnsg api get/online game_id:'.$game_id);
        if(!array_key_exists($game_id,$this->platform)){
            return Response::json(array('error' => 'ArgumentException game_id'),404);
        }
        $game_code = $this->platform[$game_id];
        $platform_id = $this->platform[$game_id];
        $servers = DB::connection($this->db_qiqiwu)->table('server_list')->orderBy('game_id', 'DESC')->get();
        $all_server_num = array();
        while (!empty($servers)) {
            $server = array_shift($servers);
            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
            $response = $api->getonlinenum($server->server_internal_id, $platform_id, $game_code); 
            if(isset($response->num)){
                $temp = array(
                    'date' => date('Y-m-d H:i:s',time()),
                    'serverid' => $server->server_internal_id,
                    'onlie' => $response->num,
                );
                $all_server_num[] = $temp;
                unset($temp);
            }

            unset($server);
            unset($api);
            unset($response);
        }
        if(empty($all_server_num)){
            return Response::json(array(),404);
        }else{
            return Response::json($all_server_num);
        } 
    }

    public function getPlayerCreateNum(){
        $game_id = Input::get('game_id');
        $date = Input::get('date');
        Log::info('mnsg api get/player/create game_id:'.$game_id .' date:'.$date);
        if(!array_key_exists($game_id,$this->platform)){
            return Response::json(array('error' => 'ArgumentException game_id'),404);
        }
        $platform_id = $this->platform[$game_id];
        $time = strtotime(trim($date));

        if(!$game_id){
            return Response::json(array('error' => 'game_id error'),404);
        }
        $start_time = strtotime(trim(date('Y-m-d',$time)));
        $end_time = $start_time+86399;

        $user = $players = SlaveCreatePlayer::on($this->db_qiqiwu)
            ->leftJoin('users as u',function($join) use($game_id){
                $join->on('u.uid','=','p.uid')
                ->where('u.game_source','=',$game_id);
            })
            ->where('p.game_id', $game_id)
            ->whereBetween('p.created_time',array($start_time,$end_time))
            ->selectRaw("FROM_UNIXTIME(p.created_time) as date,p.remote_host_ip as ip,p.server_id as serverid,p.player_id as uid,p.uid as pid,
                case when u.source='1' then 'Android' when u.source='2' then 'IOS' else 'UnKnow' end as os")
            ->get();
        $result = json_decode($user,true);
        if(empty($result)){
            return Response::json(array(),404);
        }else{
            return Response::json($result);
        }
    }

    public function getLoginData(){
        $game_id = Input::get('game_id');
        $date = Input::get('date');
        Log::info('mnsg api get/login game_id:'.$game_id .' date:'.$date);
        if(!array_key_exists($game_id,$this->platform)){
            return Response::json(array('error' => 'ArgumentException game_id'),404);
        }
        $platform_id = $this->platform[$game_id];
        if(!$date){
           $date = date('Y-m-d H:i:s',time()); 
        }
        $time = strtotime(trim($date));

        $start_time = strtotime(trim(date('Y-m-d',$time)));
        $end_time = $start_time+86399;

        $servers = DB::connection($this->db_qiqiwu)->table('server_list')
            ->where('game_id',$game_id)->get();
        $result = array();
        while (!empty($servers)) {
            $server = array_shift($servers);
            $server_internal_id = $server->server_internal_id;
            $db_name = $game_id.'.'.$server_internal_id;
            try{
                $this->setSingleDB($db_name);
            }catch(\Exception $e){
                Log::error($e);
                continue;
            }
            $login = LoginLog::on($db_name)
                ->leftJoin('log_create_player as p',function($join){
                    $join->on('p.player_id','=','ll.player_id');
                })
                ->leftJoin("{$this->db_qiqiwu}.users as u",function($join) use($game_id){
                    $join->on('u.uid','=','p.uid')
                    ->where('u.game_source','=',$game_id);
                })
                ->leftJoin('log_levelup as v',function($join){
                    $join->on('v.player_id','=','ll.player_id');
                })
                ->whereBetween('action_time',array($start_time,$end_time))
                ->groupBy('ll.player_id')
                ->selectRaw("FROM_UNIXTIME(ll.action_time) as date,ll.last_ip as ip,{$server_internal_id} as serverid,
                    ll.player_id as uid,p.uid as pid,MAX(v.lev) as level,
                    case when u.source='1' then 'Android' when u.source='2' then 'IOS' else 'UnKnow' end as os")
                ->get();

            $temp_login = json_decode($login,true);
            $result = array_merge($result,$temp_login); 

            unset($login);
            unset($temp_login);
            unset($server);
        }
        if(empty($result)){
            return Response::json(array(),404);
        }else{
            return Response::json($result);
        } 
    }

    public function getOrderData(){
        $game_id = Input::get('game_id');
        $date = Input::get('date');
        Log::info('mnsg api get/order game_id:'.$game_id .' date:'.$date);
        if(!array_key_exists($game_id,$this->platform)){
            return Response::json(array('error' => 'ArgumentException game_id'),404);
        }
        if(!$date){
           $date = date('Y-m-d H:i:s',time()); 
        }
        $platform_id = $this->platform[$game_id];
        $time = strtotime(trim($date));
        $start_time = strtotime(trim(date('Y-m-d',$time)));
        $end_time = $start_time+86399;
        $result = array();
        $servers = DB::connection($this->db_qiqiwu)->table('server_list')
            ->where('game_id',$game_id)
            ->get();
        while (!empty($servers)) {
            $server = array_shift($servers);
            $server_internal_id = $server->server_internal_id;
            $db_name = $game_id.'.'.$server_internal_id;
            Log::info($db_name);
            try{
                $this->setSingleDB($db_name);
            }catch(\Exception $e){
                Log::error($e);
                continue;
            }
            $order = $players = PayOrder::on($this->db_payment)
                ->leftJoin("{$this->db_qiqiwu}.users as u",function($join) use($game_id){
                    $join->on('u.uid','=','o.pay_user_id')
                    ->where('u.game_source','=',$game_id);
                })
                ->leftJoin(DB::raw("`{$db_name}`.log_create_player as p"),function($join){
                    $join->on('p.uid','=','u.uid');
                })
                ->leftJoin(DB::raw("`{$db_name}`.log_levelup as v"),function($join){
                    $join->on('v.player_id','=','p.player_id')
                    ->where('v.created_at','<','o.pay_time');
                })
                ->where('o.game_id', $game_id)
                ->where('o.get_payment',1)
                ->whereBetween('o.create_time',array($start_time,$end_time))
                ->groupBy('o.pay_user_id')
                ->selectRaw("FROM_UNIXTIME(o.create_time) as date,u.last_visit_ip as ip,{$server_internal_id} as serverid,p.player_id as uid,p.uid as pid,
                    max(v.lev) as level,o.pay_amount as amt,
                    case when u.source='1' then 'Android' when u.source='2' then 'IOS' else 'UnKnow' end as os")
                ->get();

            $temp_order = json_decode($order,true);
            $result = array_merge($result,$temp_order); 

            unset($order);
            unset($temp_order);
            unset($server);
        }

        if(empty($result)){
            return Response::json(array(),404);
        }else{
            return Response::json($result);
        }
    }
}