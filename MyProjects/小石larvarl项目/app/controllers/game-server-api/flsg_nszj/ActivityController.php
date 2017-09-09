<?php

class ActivityController extends \BaseController {

    private $player_ids = array();

    private $order_sns = array();

    private $gift_types = array();

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
        $servers = Server::currentGameServers()->get();
        
        $gifts = $this->initTable('item', $this->area_item_id);
        $temp = array();
        foreach ($gifts as $k => $v)
        {
            if ($v->type1 == 4 && $v->type2 == 1)
            {
                $temp[] = $v;
            }
        }
        $data = array(
                'content' => View::make('serverapi.flsg_nszj.activity.index', 
                        array(
                                'servers' => $servers,
                                'gifts' => (object) $temp
                        ))
        );
        return View::make('main', $data);
    }

    public function filterData()
    {
        $filter_data = $this->_filterData();
       // Log::info("return filter_data");
        return Response::json($filter_data);
    }

    public function _filterData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $server_ids = Input::get('server_id');
        if(!$server_ids){
            return Response::json(array('error'=>'请选择服务器'), 403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $activity_type = (int) Input::get('activity_type');
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        if ($activity_type === 1) // 充值送
        {
            $filter_data = array();
            foreach ($server_ids as $server_id)
            {
                $lower_bound = (int) Input::get('recharge_lower_bound');
                $upper_bound = (int) Input::get('recharge_upper_bound');
                $server = Server::find($server_id);
                if (! $server)
                {
                    return Response::json($msg, 404);
                }
                $platform = Platform::find(Session::get('platform_id'));
                $game = Game::find(Session::get('game_id'));
                $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                        $game->eb_api_secret_key);
                $response = $api->getPlayerPaymentFilter($platform->platform_id, $game->game_id, $server->platform_server_id, $start_time, 
                        $end_time, $platform->default_currency_id, 
                        $server->server_internal_id, $lower_bound, $upper_bound);
                //Log::info("server name:".$server->server_name."---response http_code:".$response->http_code);
                if ($response->http_code == 200)
                {
                    $body = $response->body;
                } else
                {
                    continue;
                }
                foreach ($body as $item)
                {
                    $filter_data[] = array(
                            'uid'   =>  isset($item->pay_user_id) ? $item->pay_user_id : 0,
                            'server_id' => $server->server_id,
                            'server_name' => $server->server_name,
                            'player_id' => $item->player_id,
                            'player_name' => $item->player_name,
                            'total_amount' => $item->total_amount,
                            'total_dollar_amount' => $item->total_dollar_amount,
                            'total_yuanbao_amount' => $item->total_yuanbao_amount,
                            'count' => $item->count
                    );
                }
            }
            //Log::info("return _filter_data");
            return $filter_data;
        } else if ($activity_type === 2) // 消费送
        {
            $lower_bound = (int) Input::get('consume_lower_bound');
            $upper_bound = (int) Input::get('consume_upper_bound');
            $type = "yuanbao";
            $filter_data = array();
            foreach ($server_ids as $server_id)
            {
                $server = Server::find($server_id);
                $response = $api->getPlayerEconomyAnalysis($game->game_id, 
                        $server->server_internal_id, $type, $start_time, 
                        $end_time, $lower_bound, $upper_bound);
                if ($response->http_code == 200)
                {
                    $body = $response->body;
                } else
                {
                    continue;
                }
                foreach ($body as $item)
                {
                    $filter_data[] = array(
                            'uid'   =>  isset($item->pay_user_id) ? $item->pay_user_id : 0,
                            'server_id' => $server->server_id,
                            'server_name' => $server->server_name,
                            'spend' => $item->spend,
                            'player_id' => $item->player_id,
                            'player_name' => $item->player_name
                    );
                }
            }
            return $filter_data;
        } else if ($activity_type === 3) // 幸运订单号
        {
            $lucky_number = (int) Input::get('lucky_number');
            $response = $api->getLuckyOrderSN($platform_id, $lucky_number, 
                    $start_time, $end_time);
            if ($response->http_code == 200)
            {
                $body = $response->body;
            } else
            {
                App::abort(404);
            }
            foreach ($body as $item)
            {
                $filter_data[] = array(
                        'server_id' => $item->server_name,
                        'player_id' => $item->player_id,
                        'player_name' => $item->player_name,
                        'order_sn' => $item->order_sn,
                        'pay_amount' => $item->pay_amount,
                        'dollar_amount' => $item->dollar_amount,
                        'yuanbao_amount' => $item->yuanbao_amount,
                );
            }
            return $filter_data;
        }
    }

    public function sendGift()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $filter_data = $this->_filterData();
        $award_type = (int) Input::get('award_type');
        $game = Game::find(Session::get('game_id'));
        $server_player_ids = array();
        foreach ($filter_data as $item)
        {
            $server_player_ids[$item['server_id']][] = (int) $item['player_id'];
        }
        if ($award_type === 1)
        { // 发送道具
            foreach ($server_player_ids as $k => $v)
            {
                $server = Server::find($k);
                if (! $server)
                {
                    return Response::json($msg, 404);
                }
                $api = GameServerApi::connect($server->api_server_ip, 
                        $server->api_server_port, $server->api_dir_id);
                $gift_types = Input::get('gift_types');
                foreach ($gift_types as $gift_type)
                {
                    $response = $api->sendGiftBagToPlayers((int) $gift_type, $v);
                }
            }
        } else if ($award_type === 2)
        { // 发送元宝
            foreach ($server_player_ids as $k => $v)
            {
                $amount = (int) Input::get('award_yuanbao_amount');
                $server_id = (int) Input::get('server_id');
                $server = Server::find($k);
                if (! $server)
                {
                    $msg['error'] = Lang::get('error.basic_not_found');
                    return Response::json($msg, 404);
                }
                $api = GameServerApi::connect($server->api_server_ip, 
                        $server->api_server_port, $server->api_dir_id);
                foreach ($v as $player_id)
                {
                    $response = $api->changeYuanbao($player_id, $amount, $game->game_code);
                }
            }
        } else
        {
        }
    }


    public function uploaditemsload(){  //运营自己上传item表的功能
        $data = array(
                'content' => View::make('serverapi.flsg_nszj.activity.uploaditems')
        );
        return View::make('main', $data);
    }

    public function uploaditemsupload(){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        if ($_FILES["itemsfile"]["error"] > 0){
            return Response::json(array('error'=>'上传文件出错!'), 200);
        }else{
            if('text/plain' == $_FILES["itemsfile"]['type']){
                if(in_array($game_id, $this->area_item_id)){
                    move_uploaded_file($_FILES["itemsfile"]["tmp_name"], "table/".$game->game_code."/item".$game_id.".txt");
                }else{
                    move_uploaded_file($_FILES["itemsfile"]["tmp_name"], "table/".$game->game_code."/item.txt");
                }
                return Response::json(array('error'=>'上传文件成功!'), 200);
            }else{
                return Response::json(array('error'=>'文件类型错误!'), 200);
            }
        }
    }

    public function timingIndex(){
        $data = array(
                'content' => View::make('serverapi.flsg_nszj.activity.timing', array())
        );
        return View::make('main', $data);
    }

    public function timgingOperation(){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $activity_type = Input::get('activity_type');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $id = Input::get('id');

        if($id && $start_time){//更新
            if(($end_time && $start_time >= $end_time) || $start_time < time()+1200){
                return Response::json(array('error'=>'time error'), 403);
            }
            $result = DB::table('timing_activities')->where('id',$id)
               ->update(array('start_time' => $start_time, 'end_time' => $end_time));
            if(1 == $result){
                return 'ok';
            }else{
                return 'error';
            }
        }elseif($id && !$start_time){//删除
            $result = DB::table('timing_activities')->where('id',$id)
               ->update(array('status' => 2));
            if(1 == $result){
                return 'ok';
            }else{
                return 'error';
            }
        }

        $table = $this->initTable('activities');
        $holiday_table = array();
        foreach ($table as $value) {
            if('1' == $value->canopen){
                $holiday_table[$value->value] = $value->name;
            }
        }

        $table = $this->initTable('turnplate_activities');
        $turnplate_table = array();
        foreach ($table as $value) {
            if('1' == $value->canopen){
                $turnplate_table[$value->label] = $value->name;
            }
        }

       $holiday_set_table = array(
            1 => Lang::get('serverapi.total_consumption'),
            2 => Lang::get('serverapi.total_refill'),
            3 => Lang::get('serverapi.single_refill'),
            21 => Lang::get('serverapi.total_sign'),
            24 => Lang::get('serverapi.single_refill2'),
        );

        $type_name = array(
            1 => '转盘类活动',
            2 => '假日活动',
            3 => '假日活动设置活动奖励',
        );
        $activities = DB::table('timing_activities')->where('status',0)
           ->whereBetween('start_time',array($start_time,$end_time))
           ->where('start_time','>',time())
           ->where('game_id',$game_id)
           ->orderBy('start_time','ASC');
        if($activity_type){
            $activities->where('type',$activity_type);
        }
        $activities = $activities->get();

        if(!empty($activities)){
            foreach ($activities as $activity) {
                $params = json_decode($activity->params);
                $payload = json_decode($params->payload);
                if(1 == $activity->type){
                    $activity_name = $turnplate_table[$payload->label];
                }elseif(2 == $activity->type){
                    $activity_name = $holiday_table[$payload->type];
                }elseif (3 == $activity->type) {
                   $activity_name = $holiday_set_table[$payload->type];
                }
                $server_ids = explode(",", $activity->main_server);
                $server_names = Server::whereIn('server_id',$server_ids)
                    ->lists('server_name');
                $temp = array(
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'type_name' => $type_name[$activity->type],
                    'activity_name' => $activity_name,
                    'start_time' => date('Y-m-d H:i:s', $activity->start_time),
                    'end_time' => (0 != $activity->end_time) ? date('Y-m-d H:i:s', $activity->end_time) : '',
                    'server_name' => $server_names,
                );
                $result[] =  $temp;
                unset($temp);
            }
            return Response::json($result);
        }else{
            return Response::json(array('error'=> Lang::get('error.activity_not_found')),403);
        }
    }
}