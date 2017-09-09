<?php

class SlaveApiPaymentController extends \BaseController {

    private $servers = array();

    public function __construct()
    {
        $this->servers = $this->getUnionServers();
    }
    public function index()
    {
    }

    public function getData()
    {
    }

    public function orderStatIndex()
    {
        $platform = Platform::find(Session::get('platform_id'));
        $default_currency_id = $platform->default_currency_id;
        $currency = Currency::find($default_currency_id);
        $game = Game::find(Session::get('game_id'));
        if($currency){
            $currency_code = $currency->currency_code;
        }else{
            $currency_code = '';
        }
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.payment.order.stat', 
                        array(
                                'servers' => $servers,
                                'currency_code' => $currency_code,
                                'game_code' => $game->game_code,
                        ))
        );
        return View::make('main', $data);
    }

    public function sendOrderStatData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));

        $start_time = $this->current_time_nodst($start_time);
        $end_time = $this->current_time_nodst($end_time);

        $server_id = (int) Input::get('server_id');
        $game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id');
        
        //$game_id = 1;//测试
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        
        $platform_id = Session::get('platform_id');
        $query = Platform::where("platform_id", $platform_id)->first();
        if ($query)
        {
            $currency_id = $query->default_currency_id;
        } else
        {
            App::abort(404);
        }
        if ($server_id == 0)
        {
            $devide_servers = 0;
            $response = $api->getGameOrderStatistics($platform_id, 
                    $game->game_id, $currency_id, $start_time, $end_time, $devide_servers);
        } 
        if ($server_id == -1)
        {
            $devide_servers = 1;
            $response = $api->getGameOrderStatistics($platform_id, 
                    $game->game_id, $currency_id, $start_time, $end_time, $devide_servers);

            $body = $response->body;
            if('200' != $response->http_code){
                return Response::json($body, $response->http_code);
            }
            $dealpart = $body->order;
            foreach ($dealpart as &$value) {
                if(isset($value->server_id)){
                    if(Server::where('game_id', $game_id)->where('platform_server_id', $value->server_id)->first()){
                        $value->date = $value->date.' : '.Server::where('game_id', $game_id)->where('platform_server_id', $value->server_id)->first()->server_name;
                    }else{
                        $value->date = $value->date.' : '.'unknown server';
                    }
                }
            }
            if ($response->http_code == 200){
                return Response::json($body);
            } else {
                return Response::json($body, $response->http_code);
            }
        }
        if ($server_id == -2)
        {
            $devide_servers = 2;
            $response = $api->getGameOrderStatistics($platform_id, 
                    $game->game_id, $currency_id, $start_time, $end_time, $devide_servers);

            $body = $response->body;
            $dealpart = $body->order;

            $servers = Server::where('game_id', $game_id)->selectRaw("platform_server_id as server_id, 0 as total_amount,0 as total_dollar_amount,
              0 as total_yuanbao_amount, 0 as total_count,0 as total_user_count, server_name as date")->orderBy('platform_server_id','ASC')->get();
            $servers = json_decode($servers);
           // Log::info(var_export($servers,true));
            foreach ($servers as &$v) {//将消费为0的服务器也显示
                foreach ($dealpart as &$value) {
                    if(isset($value->server_id) && $v->server_id == $value->server_id){
                        $value->date = $v->date;
                        $v = $value;
                        break;
                    }
                }
            }
            array_unshift($servers, $dealpart[0]);
            $body->order = $servers;

            if ($response->http_code == 200){
                return Response::json($body);
            } else {
                return Response::json($body, $response->http_code);
            }
        }
        if ($server_id > 0)
        {
            $server = Server::find($server_id);
            if (! $server)
            {
                return Response::json($msg, 403);
            }
            if($game_id != $server->game_id){
                return Response::json('Please check the current platform and game', 403);
            }
            if ($query)
            {
                $query = Server::where("server_id", $server_id)->first();
            } else
            {
                App::abort(404);
            }
            $open_server_time = $query->open_server_time;
            $platform_server_id = $query->platform_server_id;
            $server_internal_id = $query->server_internal_id;
            $response = $api->getServerOrderStatistics($platform_id, 
                    $platform_server_id, $currency_id, $open_server_time, 
                    $start_time, $end_time, $game_id, $server_internal_id, $game->game_code);
            // $response = $api->getServerOrderStatistics(1, 66, 1, 1380556800,
            // $start_time, $end_time);
        }
        $body = $response->body;
        //Log::info(var_export($body, true));
        if ($response->http_code == 200)
        {
            return Response::json($body);
        } else
        {
            return Response::json($body, $response->http_code);
        }
    }

    public function orderPlayerIndex()
    {
        $game_id = (int)Input::get('game_id');

        if($game_id){   //如果是财务功能进入，则判断当前游戏是否是应该切到的游戏，如果不是则切换至目标游戏
            $game = Game::find($game_id);
            if(!$game){
                return $this->show_message('404', "No Such Game");
            }
            if(Auth::user()->is_admin || in_array($game_id, Auth::user()->games())){
                if($game_id != Session::get('game_id')){
                    Session::forget('game_id');
                    Session::forget('platform_id');
                    Session::put('platform_id', $game->platform_id);
                    Session::put('game_id', $game_id);
                }
            }else{
                return $this->show_message('403', "You have no permission of game:".$game->game_name);
            }
        }

        $uid = Input::get('uid');
        $order_id_init = Input::get('order_id');
        if(!$uid){
            $server_name = Input::get('server_name');
            $player_id = (int)Input::get('player_id');
            if($server_name && $player_id){
                $server = Server::where('game_id', Session::get('game_id'))->where('server_name', $server_name)->first();
                if(isset($server->server_internal_id)){
                    $server_internal_id = $server->server_internal_id;
                }else{
                    $server_internal_id = 0;
                }
                if($server_internal_id){
                    $game_id = Session::get('game_id');
                    $platform_id = Session::get('platform_id');
                    $game = Game::find(Session::get('game_id'));
                    $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                    $result = $slaveapi->getUidByPlayerInfo($game_id, $platform_id, $server_internal_id, $player_id, $player_name='');
                    if(200 == $result->http_code){
                        $uid = $result->body->uid;
                    }
                }
            }
        }
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.payment.order.order_player', 
                        array(
                                'servers' => $servers,
                                'uid' => $uid,
                                'order_id_init' => $order_id_init,
                        ))
        );
        return View::make('main', $data);
    }

    public function orderPlayerData()
    {
        if(Input::get('is_record')){
            return $this->recordOrder();
        }
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $google_time = (int)strtotime(Input::get('google_time'));
        if($google_time){
            $google_tradeseq = Input::get('tradeseq');
            return $this->find_google_order($google_tradeseq, $google_time);
        }
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $order_id = trim(Input::get('order_id'));
        $order_sn = trim(Input::get('order_sn'));
        $tradeseq = trim(Input::get('tradeseq'));
        $uid = trim(Input::get('player_uid'));
        $player_name = trim(Input::get('player_nickname'));
        $player_id = (int)Input::get('player_id');
        $bank_account = trim(Input::get('bank_account'));
        $game = Game::find(Session::get('game_id'));
        $game_id = Session::get('game_id');
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $platform_id = Session::get('platform_id');
        $response = '';
        $folder = Input::get('folder');
        $filename = Input::get('filename');
        $filetype = Input::get('filetype');
        if($folder){
            return $this->uploadPic($game_id,$folder,$filename,$filetype);
        }

        $get_payment = Input::get('get_payment');
        $offer_yuanbao = Input::get('offer_yuanbao');
        $limit_order = Input::get('limit_order');
        $server_id = Input::get('server_id');
        $platform_server_id = 0;
        if($server_id>0){
            $server = Server::find($server_id);
            if($server){
                $platform_server_id = $server->platform_server_id;
            }
        }

        //if(54 == $game_id)Log::info("start time:".$start_time."---end time:".$end_time."---uid".$uid."---player name:".$player_name."---game id:".$game_id."---eb api url:".$game->eb_api_url."---eb api key:".$game->eb_api_key);
        // 填写了订单号
        if($order_id){
            $response = $api->getOrderByOrderID($platform_id, $order_id, $game->game_id);
            $body = $response->body;
            if ($response->http_code != 200)
            {
                return Response::json($body, $response->http_code);
            }
            $items = array(
                    $body
            );
            if(isset($body->combined_order) && $body->combined_order){
                return $this->getCombinedOrders($body->combined_order);
            }
            $this->getOrderDetail($items);
            return Response::json($items);
        }else if ($order_sn)
        {
            $response = $api->getOrderByOrderSN($platform_id, $order_sn, $game->game_id);
            $body = $response->body;
            if ($response->http_code != 200)
            {
                return Response::json($body, $response->http_code);
            }
            $items = array(
                    $body
            );
            if(isset($body->combined_order) && $body->combined_order){
                return $this->getCombinedOrders($body->combined_order);
            }
            $this->getOrderDetail($items);
            return Response::json($items);
        } else if ($tradeseq)
        { // 如果填写了外部订单号
            $response = $api->getOrderByTradeseq($platform_id, $tradeseq, $game_id);
            $items = $response->body;
            if ($response->http_code != 200)
            {
                return Response::json($items, $response->http_code);
            }
            $this->getOrderDetail($items);
            return Response::json($items);
        } else if ($player_id)
        { //玩家名称
            $response = $api->getOrdersByUser($platform_id, "", "", $player_id,
                    $start_time, $end_time, '',$game_id, $get_payment, $offer_yuanbao, $platform_server_id, $limit_order);

           // if($game_id==54) Log::info("get order by user:".var_export($response, true));
        }else if ($player_name)
        { //玩家名称
            $response = $api->getOrdersByUser($platform_id, "", $player_name, 0,
                    $start_time, $end_time, '',$game_id, $get_payment, $offer_yuanbao, $platform_server_id,$limit_order);

           // if($game_id==54) Log::info("get order by user:".var_export($response, true));
        } else if ($uid)
        { //UID
            $response = $api->getOrdersByUser($platform_id, $uid, "", 0,
                    $start_time, $end_time, '',$game_id, $get_payment, $offer_yuanbao, $platform_server_id, $limit_order);
        } else if ($bank_account)
        { //银行账号
            $response = $api->getOrdersByUser($platform_id, '', "", 0, $start_time, 
                    $end_time, $bank_account, $game_id, $get_payment, $offer_yuanbao, $platform_server_id, $limit_order);
        } elseif($limit_order){
            $response = $api->getOrdersByUser($platform_id, "", "", 0,
                    $start_time, $end_time, '',$game_id, $get_payment, $offer_yuanbao, $platform_server_id, $limit_order);
        }else
        { //其他
            $msg['error'] = Lang::get('error.slave_not_have_params');
            return Response::json($msg, 403);
        }
        $items = $response->body;
        if ($response->http_code != 200)
        {
            return Response::json($items, $response->http_code);
        }
        if($limit_order){
            foreach ($items as &$item) {
                if(isset($item->server_id)){
                    $temp_Server = Server::where('game_id',$game_id)->where('platform_server_id',$item->server_id)->pluck('server_name');
                    $item->server_name = $temp_Server;
                }else{
                    $item->server_name = '';
                }
            }
        }else{
           $this->getOrderDetail($items); 
        }
        return Response::json($items);
    }

    private function getCombinedOrders($combined_orders){
        $combined_orders = json_decode($combined_orders);
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        if($combined_orders){
            $items = array();
            $all_payed_dollar = 0;
            foreach ($combined_orders as $value) {
                $response = $slaveapi->getOrderByOrderSN($platform_id, $value, $game->game_id);
                $body = $response->body;
                if ($response->http_code != 200)
                {
                    continue;
                }
                $items[] = $body;
                if($body->get_payment){
                    $all_payed_dollar += $body->pay_amount*$body->exchange;
                }
                unset($response);
                unset($body);
            }
            $this->getOrderDetail($items);
            $items[] = array(
                'order_id' => 'total',
                'order_sn' => 'paid',
                'tradeseq' => $all_payed_dollar.'$',
                );
            return Response::json($items);
        }
    }

    private function find_google_order($google_tradeseq, $google_time){
        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);

        $platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);

        $order = array(
            'tradeseq' =>  $google_tradeseq,
            'date' => date('Y-m-d', $google_time)
            );

        $result = $platform_api->find_google_order($order);
        
        if(200 == $result->http_code){
            $result = $result->body;
            $msg = $result->message;
            if(isset($result->order_sn) && isset($result->tradeseq)){
                $msg .= ' order_sn: '.$result->order_sn.' And another tradeseq is: '.$result->tradeseq;
            }
            return Response::json(array('msg'=> $msg));
        }else{
            return Response::json(array('error'=> Lang::get('slave.bad_return')), 403);
        }
    }

    public function orderListIndex()
    {
        $servers = Server::currentGameServers()->get();
        $platform_id = Session::get('platform_id');
        $pay_types = PayType::where('platform_id', $platform_id)->get();
        $child_pay = array();
        foreach ($pay_types as $key => $paytype) {
            $child_pay_types = 
            Payment::where('platform_id', $platform_id)
            ->where('pay_type_id', $paytype->pay_type_id)
            ->get();
            $child_pay[$paytype->pay_type_id] = $child_pay_types;
        }
        $data = array(
                'content' => View::make('slaveapi.payment.order.order_list', 
                        array(
                                'servers' => $servers,
                                'pay_types' => $pay_types,
                                'child_pay' => $child_pay
                        ))
        );
        return View::make('main', $data);
    }

    public function orderListData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        
        $server_id = (int) Input::get('server_id');
        $pay_type_id = (int) Input::get('pay_type_id');
        $method_id = (int) Input::get('child_pay_type');
        
        $get_payment = (int) Input::get('get_payment');
        $statistics_time = (int) Input::get('statistics_time');//0:create_time 1:pay_time

        $low_amount = (int) Input::get('lower_bound');
        $high_amount = (int) Input::get('upper_bound');
        
        $low_gold = (int) Input::get('lower_gold');
        $high_gold = (int) Input::get('upper_gold');

        $sdk_id = Input::get('sdk_id');
        
        $per_page = (int) Input::get('per_page');
        
        $page = (int) Input::get('page');
        $page = $page > 0 ? $page : 1;
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
//        Log::info(var_export($start_time . ',' . $end_time,true));
        $offer_yuanbao_id = (int) Input::get('is_recharge_in_game');
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $platform_id = Session::get('platform_id');
        
        $game_id = Session::get('game_id');
        $order = array();
        $order['platform_id'] = $platform_id;
        $order['start_time'] = $start_time;
        $order['end_time'] = $end_time;
        $order['high_amount'] = $high_amount;
        $order['low_amount'] = $low_amount;
        $order['high_gold'] = $high_gold;
        $order['low_gold'] = $low_gold;
        $order['sdk_id'] = $sdk_id;
        $order['pay_type_id'] = $pay_type_id;
        $order['method_id'] = $method_id;
        if ($offer_yuanbao_id < 2)
        {
            $order['offer_yuanbao'] = $offer_yuanbao_id;
        }
        
        if ($get_payment < 2)
        {
            $order['get_payment'] = $get_payment;
        }
        
        $get_payment_txt = array(
                Lang::get('slave.order_statics_un_pay'),
                Lang::get('slave.order_statics_complete')
        );
        $order['game_id'] = $game_id;

        if($server_id >0 ){
            if ($query = Server::where("server_id", $server_id)->first())
            {
                $platform_server_id = $query->platform_server_id;
            }
            $order['platform_server_id'] = $platform_server_id;
        }
        $response = $api->getOrders($order, $page, $per_page, $statistics_time);

        $body = $response->body;
        if ($response->http_code != 200)
        {
            return $api->sendResponse();
        }
        if(empty($body)){
            $tmp = '';
            foreach ($order as $key => $value) {
                $tmp .= $key.$value;
            }
            return Response::json(array('error'=>'Cannot get orders. '.$tmp), 403);
        }
        if(isset($body->items)){
            $items = $body->items;
            $this->getOrderDetail($items); 
        }
        return Response::json($body);
    }

    public function downloadOrderListIndex()
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

    public function downloadOrderListData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
   
        $server_id = (int) Input::get('server_id');
    
        $pay_type_id = (int) Input::get('pay_type_id');
      
        $get_payment = (int) Input::get('get_payment');
        $statistics_time = (int) Input::get('statistics_time');//0:create_time 1:pay_time

        $low_amount = (int) Input::get('lower_bound');
        $high_amount = (int) Input::get('upper_bound');
       
        $low_gold = (int) Input::get('lower_gold');
        $high_gold = (int) Input::get('upper_gold');
       
        $per_page = (int) Input::get('per_page');
        $method_id = (int) Input::get('child_pay_type');
        
        $page = (int) Input::get('page');
        $page = $page > 0 ? $page : 1;
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        
        $offer_yuanbao_id = (int) Input::get('is_recharge_in_game');
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $platform_id = Session::get('platform_id');
        
        $game_id = Session::get('game_id');
        $order = array();
        $order['platform_id'] = $platform_id;
        $order['start_time'] = $start_time;
        $order['end_time'] = $end_time;
        $order['high_amount'] = $high_amount;
        $order['low_amount'] = $low_amount;
        $order['high_gold'] = $high_gold;
        $order['low_gold'] = $low_gold;
        $order['pay_type_id'] = $pay_type_id;
        $order['method_id'] = $method_id;
        if($offer_yuanbao_id<2)
        {
            $order['offer_yuanbao'] = $offer_yuanbao_id;
        }
        
        if ($get_payment < 2)
        {
            $order['get_payment'] = $get_payment;
        }
        
        $get_payment_txt = array(
                Lang::get('slave.order_statics_un_pay'),
                Lang::get('slave.order_statics_complete')
        );
        
        if ($server_id == 0)
        {
            $order['game_id'] = $game_id;
        } else
        {
            if ($query = Server::where("server_id", $server_id)->first())
            {
                $platform_server_id = $query->platform_server_id;
            }
            $order['platform_server_id'] = $platform_server_id;
        }
        $title = array(
                Lang::get("slave.order"),
                Lang::get("slave.order_by_channel"),
                Lang::get("slave.order_external"),
                Lang::get("slave.order_type"),
                Lang::get("slave.order_child_type"),
                Lang::get("slave.goods_type"),
                Lang::get("slave.order_recharge_money"),
                Lang::get("slave.order_recharge_unit"),
                Lang::get("slave.order_recharge_exchange"),
                Lang::get("slave.order_recharge_dollar"),
                Lang::get("slave.goods_value"),
                Lang::get("slave.giftbag_name"),
                Lang::get("slave.order_recharge_yuanbao"),
                Lang::get("slave.is_recharge_in_game"),
                Lang::get("slave.order_date"),
                Lang::get("slave.pay_time"),
                Lang::get("slave.order_stat"),
                Lang::get("slave.mycard_code"),
                Lang::get("slave.player_nickname"),
                Lang::get("slave.player_id"),
                Lang::get("slave.player_uid"),
                Lang::get("slave.web_id"),
                Lang::get("slave.server")
        );
        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";
        $csv = CSV::init($file, $title);
        $first_response = $api->getOrders($order, $page, $per_page, $statistics_time);
   // Log::info(var_export($first_response,true));//items=null;
        if ($first_response->http_code != 200)
        {
            return Response::json($first_response->body, $first_response->http_code);
        }
        $total = $first_response->body->total;
        for ($i = 1; $i <= $total; $i ++)
        {
            $response = $api->getOrders($order, $i, $per_page, $statistics_time);
            $body = $response->body;
            $items = $body->items;
            $this->getOrderDetail($items);
            foreach ($items as $item)
            {
                $message_arr = array(
                    'order_sn' => $item->order_sn,
                    'sdk_id' => isset($item->sdk_id) ? $item->sdk_id : '',
                    'tradeseq' => $item->tradeseq,
                    'pay_type_name' => $item->pay_type_name,
                    'method_name' => $item->method_name . ' | ' .$item->money_flow_name,
                    'goods_type' => $item->goods_type,
                    'pay_amount' => round($item->pay_amount, 2),
                    'currency_code' => $item->currency_code,
                    'exchange' => $item->exchange,
                    'dollar_amount' => round($item->dollar_amount, 2),
                    'goods_value' => isset($item->goods_value) ? round($item->goods_value,2) : '',
                    'giftbag_name' => isset($item->giftbag_name) ? $item->giftbag_name : '',
                    'yuanbao_amount' => round($item->yuanbao_amount, 2),
                    'offer_yuanbao_txt' => $item->offer_yuanbao_txt,
                    'create_time' => $item->create_time,
                    'pay_time' => $item->pay_time,
                    'get_payment_txt' => $item->get_payment_txt,
                    'mycard_activity_code' => $item->mycard_activity_code,
                    'player_name' => $item->player_name,
                    'player_id' => $item->player_id,
                    'pay_user_id' => $item->pay_user_id,
                    'login_email' => $item->login_email,
                    'server_name' => $item->server_name
                );
                $csv->writeData($message_arr);
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

    private function getOrderDetail(&$items)
    {
        $offer_yuanbao = array(
                "NO",
                "YES"
        );
        $get_payment_txt = array(
                Lang::get('slave.order_statics_un_pay'),
                Lang::get('slave.order_statics_complete')
        );
        $game_id = session::get('game_id');
        if($game_id == 51 || $game_id == 55 || $game_id == 58 || $game_id == 64){
            foreach ($items as &$item)
            {
                $item->pay_type_name = '';
                $pay_type = PayType::getPayType($item->pay_type_id)->first();
                if ($pay_type)
                {
                    $item->pay_type_name = $pay_type->pay_type_name;
                }
                $item->nickname = isset($item->nickname) ? $item->nickname : '';
                $item->goods_type = isset($item->goods_type) &&
                         $item->goods_type == 2 ? "Yes" : 'No';
                $currency = Currency::find($item->currency_id);
                $item->currency_code = '';
                if ($currency)
                {
                    $item->currency_code = $currency->currency_code;
                }
                $item->offer_yuanbao_txt = $offer_yuanbao[$item->offer_yuanbao];
                $item->get_payment_txt = $get_payment_txt[$item->get_payment];
                $item->method_name = $item->method_id;
                if (isset($item->zone))
                {
                    $payment = Payment::getPayments($item->pay_type_id, 
                            $item->method_id, $item->zone)->first();
                } else
                {
                    $payment = Payment::getPayments($item->pay_type_id, 
                            $item->method_id)->first();
                }
                if ($payment)
                {
                    $item->method_name = $payment->method_name;
                }
                if (! isset($item->mycard_activity_code))
                {
                    $item->mycard_activity_code = '';
                }
                $item->record_result = '';
                if(!$item->offer_yuanbao){
                    if($order = RecordOrders::where('game_id', $game_id)->where('order_id', $item->order_id)->where('type', 'fail')->first()){
                        if($order->deal_time > 0){
                            $item->already_record = 9;  //代表已经处理
                            $item->record_result = $order->result;
                        }else{
                            $item->already_record = 1;  //代表已经记录
                        }
                    }else{
                        $item->already_record = 0;  //代表未记录
                    }
                    $item->already_award = 0;
                    unset($order);
                }else{
                    if($order = RecordOrders::where('game_id', $game_id)->where('order_id', $item->order_id)->where('type', 'award')->first()){
                        if($order->deal_time > 0){
                            $item->already_award = 9;   //代表已经处理
                            $item->record_result = $order->result;
                        }else{
                            $item->already_award = 1;   //代表已经记录
                        }
                    }else{
                        $item->already_award = 0;   //代表未记录
                    }
                    $item->already_record = 0;
                }
                $item->create_time = date('Y-m-d H:i:s', $item->create_time);
                $item->pay_time = date('Y-m-d H:i:s', $item->pay_time);
                if (isset($item->bank_pay_time))
                {
                    $item->bank_pay_time = date('Y-m-d H:i:s', $item->bank_pay_time);
                }
            }
        }else{
            foreach ($items as &$item)
            {
                $item->pay_type_name = '';
                $pay_type = PayType::getPayType($item->pay_type_id)->first();
                if ($pay_type)
                {
                    $item->pay_type_name = $pay_type->pay_type_name;
                }
                $item->nickname = isset($item->nickname) ? $item->nickname : '';
                $item->goods_type = isset($item->goods_type) &&
                         $item->goods_type == 2 ? "Yes" : 'No';
                if (isset($item->player_name) && isset($item->player_id) &&
                         isset($item->server_name) && $item->player_name == '')
                {   
                    $server = Server::where('server_name', $item->server_name)->first();
                    if ($server)
                    {
                        $game = Game::find(Session::get('game_id'));
                        $api = SlaveApi::connect($game->eb_api_url, 
                                $game->eb_api_key, $game->eb_api_secret_key);
                        $response = $api->getCreatePlayerInfo($uid = '', 
                                $item->player_id, $player_name = '', $game->game_id, 
                                $server->server_internal_id);
                        if ($response->http_code == 200)
                        {
                            $item->player_name = $response->body->player_name;
                        }
                    }
                }

                if (($item->player_name == "") && ($item->player_id == "") ) {
                    $server = Server::where('server_name', $item->server_name)->first();
                    if ($server) {
                        $platform_id = Session::get('platform_id');
                        $game = Game::find($game_id);
                        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                        $response = $api->getPlayerInfoFromLog($platform_id, $game_id, $server->server_internal_id, $item->pay_user_id);
                        //var_dump($response);die();
                        if ($response->http_code == 200) {
                            $item->player_id = isset($response->body[0]->player_id) ? $response->body[0]->player_id :'';
                            $item->player_name = isset($response->body[0]->player_name) ? $response->body[0]->player_name : '';
                        }
                    }
                }
                /*if(($game_id == 54 || $game_id == 69) && ($item->player_name == "") && ($item->player_id == "")){
                    //$server = Server::where('server_internal_id', $item->server_internal_id)->where('game_id', $game_id)->first();
                    $platform_id = Session::get('platform_id');
                    $game = Game::find($game_id);
                    $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
                    $response = $api->getPlayerInfoFromLog($platform_id, $game_id, 1, $item->pay_user_id);
                    if ($response->http_code == 200) {
                        $item->player_id = isset($response->body[0]->player_id) ? $response->body[0]->player_id :'';
                        $item->player_name = isset($response->body[0]->player_name) ? $response->body[0]->player_name : '';
                    } 
                }*/
                
                $currency = Currency::find($item->currency_id);
                $item->currency_code = '';
                if ($currency)
                {
                    $item->currency_code = $currency->currency_code;
                }
                $item->offer_yuanbao_txt = $offer_yuanbao[$item->offer_yuanbao];
                $item->get_payment_txt = $get_payment_txt[$item->get_payment];
                $item->method_name = $item->method_id;
                if (isset($item->zone))
                {
                    $payment = Payment::getPayments($item->pay_type_id, 
                            $item->method_id, $item->zone)->first();
                } else
                {
                    $payment = Payment::getPayments($item->pay_type_id, 
                            $item->method_id)->first();
                }
                if ($payment)
                {
                    $item->method_name = $payment->method_name;
                }
                if (! isset($item->mycard_activity_code))
                {
                    $item->mycard_activity_code = '';
                }
                $item->record_result = '';
                if(!$item->offer_yuanbao){
                    if($order = RecordOrders::where('game_id', $game_id)->where('order_id', $item->order_id)->where('type', 'fail')->first()){
                        if($order->deal_time > 0){
                            $item->already_record = 9;  //代表已经处理
                            $item->record_result = $order->result;
                        }else{
                            $item->already_record = 1;  //代表已经记录
                        }
                    }else{
                        $item->already_record = 0;  //代表未记录
                    }
                    $item->already_award = 0;
                    unset($order);
                }else{
                    if($order = RecordOrders::where('game_id', $game_id)->where('order_id', $item->order_id)->where('type', 'award')->first()){
                        if($order->deal_time > 0){
                            $item->already_award = 9;   //代表已经处理
                            $item->record_result = $order->result;
                        }else{
                            $item->already_award = 1;   //代表已经记录
                        }
                    }else{
                        $item->already_award = 0;   //代表未记录
                    }
                    $item->already_record = 0;
                }
                $item->create_time = date('Y-m-d H:i:s', $item->create_time);
                $item->pay_time = date('Y-m-d H:i:s', $item->pay_time);
                if (isset($item->bank_pay_time))
                {
                    $item->bank_pay_time = date('Y-m-d H:i:s', $item->bank_pay_time);
                }
            }
        }
        
        unset($item);
    }

    public function unpayIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.payment.order.unpay', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function unpayData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
                'failed_times' => 'required|numeric|min:1'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        }
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        $platform_server_id = $server ? $server->platform_server_id : 0;
        $failed_times = (int) Input::get("failed_times");
        $order_by = Input::get('order_by');
        $order_desc = Input::get('order_desc');
        $show_type = Input::get('show_type');   //显示哪些记录
        
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $platform_id = Session::get('platform_id');
        $response = $api->getFailedOrderUser($platform_id, $game->game_id, 
                $start_time, $end_time, $failed_times, $platform_server_id, $order_by, $order_desc);
        $body = $response->body;
        //Log::info(var_export($body, true));
        if ($response->http_code != 200)
        {
            return Response::json($body, $response->http_code);
        } else
        {
        foreach ($body as $key => &$v)
        {
            $v->msg = array();
            $create_timestamp = isset($v->create_time) ? $v->create_time : 0;
            $v->create_time = isset($v->create_time) ? date("Y-m-d H:i:s", $v->create_time) : "";
            $msg = EastBlueLog::where('log_key', 'unpay')->leftJoin(
                    'users as u', 'u.user_id', '=', 'log.user_id')
                ->selectRaw(
                    "FROM_UNIXTIME(log.created_at, '%Y-%m-%d %H:%i:%s') as ctime, log.desc, u.username")
                ->where('log.platform_uid', $v->uid)
                ->where('log.game_id', Session::get('game_id'))
                ->orderBy('log.log_id', 'desc');
            if(in_array($platform_id, array(2, 36, 53))){   //越南限制了取同一个玩家三天内的最后一个记录当做处理结果
                $msg = $msg->whereBetween('log.created_at', array($create_timestamp-1*86400, $create_timestamp + 3*86400));
            }else{
                $msg = $msg->where('log.created_at', '>', $create_timestamp);
            }
            $msg = $msg->first();
            if ($msg)
            {
                $v->msg['ctime'] = $msg->ctime;
                $v->msg['desc'] = $msg->desc;
                $v->msg['username'] = $msg->username;
            }else{
                $v->msg = false;
            }
            if('all' == $show_type){
                //所有订单，什么都不做
            }elseif('dealt' == $show_type){ //已处理订单，指处理结果为成功或者失败的
                if($msg) {
                    if(in_array($msg->desc, array('成功', '失败'))){

                    }else{
                        unset($body[$key]);
                    }
                }else{
                    unset($body[$key]);
                }
            }elseif('not_deal' == $show_type){
                if($msg) {
                    if(in_array($msg->desc, array('成功', '失败'))){
                        unset($body[$key]);
                    }else{
                    }
                }else{
                }
            }else{//尚未定义的情况
            }
            unset($msg);
        }
        unset($v);
        return Response::json($body);
        }
    }

    public function unPayMsg()
    {
        $msg = array(
                'msg' => Lang::get('error.basic_input_error')
        );
        $uid = Input::get('uid');
        $msg = Input::get('msg');
        
        if (! $msg || ! $uid)
        {
            return Response::json($msg, 403);
        }
        $log = new EastBlueLog();
        $log->log_key = 'unpay';
        $log->platform_uid = $uid;
        $log->desc = $msg;
        $log->user_id = Auth::user()->user_id;
        $log->game_id = Session::get('game_id');
        if ($log->save())
        {
            return Response::json(array());
        } else
        {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
    }

    public function yuanbaoIndex()
    {
        $servers = Server::currentGameServers()->get();
            $data = array(
                    'content' => View::make('slaveapi.payment.order.rank', 
                            array(
                                    'servers' => $servers
                            ))
            );
        return View::make('main', $data);
    }

    public function yuanbaoIndexforMG(){
        $servers = Server::currentGameServers()->get();
            $data = array(
                    'content' => View::make('slaveapi.payment.order.syrank', 
                            array(
                                    'servers' => $servers
                            ))
            );
        return View::make('main', $data);
    }

    public function yuanbaoDataforMG(){
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'error' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $start_time = $start_time ? $start_time : "";
        $end_time = $end_time ? $end_time : "";
        $per_page = (int) Input::get('per_page');
        
        $page = (int) Input::get('page');
        $page = $page > 0 ? $page : 1;
        //$per_page = 30;
        $server_ids = Input::get('server_ids');
        if('0' == count($server_ids)){
            return Response::json(array('error'=>'请选择服务器!'), 403);
        }
        $servers = Server::find($server_ids);
        $platform_server_ids = array();
        foreach ($servers as $server) {
            $platform_server_ids[] = $server->platform_server_id;
        }
        $platform = Platform::find(Session::get('platform_id'));
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $response = $api->getYuanbaoRankforMG($platform->platform_id, $game->game_id, 
                $platform_server_ids, $start_time, $end_time, 
                $platform->default_currency_id, $page, $per_page);
        //$s = var_export($response,true);
        //Log::info($s);

        if ($response->http_code != 200)
        {
            return Response::json($response->body, $response->http_code);
        }
        $body = $response->body;
        $i = $per_page * ($page - 1) + 1;
        
        if(!isset($body->items)){
            return Response::json($response->body, $response->http_code);
        }

        foreach ($body->items as $item)
        {
            $item->no_recharge_days = floor((time() - $item->last_order_time) / 86400);
            $amount = $item->total_yuanbao_amount;
            $item->rank = $i ++;

            $item->created_ip_country = '';
            if ($item->created_ip)
            {
                @$item->created_ip_country = geoip_country_name_by_name($item->created_ip);
                if(empty($item->created_ip_country)){
                    $response_created = Curl::url("http://www.geoplugin.net/json.gp?ip=".$item->created_ip)->get();
                    if(isset($response_created->body->country_name)){
                        $item->created_ip_country = $response_created->body->country_name; 
                    }
                }
            }
        }
        return Response::json($body);
    }


    public function yuanbaoData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'error' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $start_time = $start_time ? $start_time : "";
        $end_time = $end_time ? $end_time : "";
        $per_page = (int) Input::get('per_page');
        
        $page = (int) Input::get('page');
        $page = $page > 0 ? $page : 1;
        //$per_page = 30;
        $server_id = (int) Input::get('server_id');
        if('0' == $server_id){
            return Response::json(array('error'=>'请选择服务器'), 403);
        }
        $server = Server::find($server_id);
        if (! $server)
        {
            return Response::json(array('error'=>'错误的服务器'), 403);
        }
        $server_internal_id = $server->server_internal_id;
        $platform = Platform::find(Session::get('platform_id'));
        $game = Game::find(Session::get('game_id'));
        if($game->game_id != $server->game_id){
            return Response::json(array('error'=>'平台与服务器不对应，请刷新页面'), 403);
        }
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $response = $api->getYuanbaoRank($platform->platform_id, $game->game_id, 
                $server->platform_server_id, $start_time, $end_time, 
                $platform->default_currency_id, $server->server_internal_id, 
                $page, $per_page);
         // $s = var_export($response,true);
         // Log::info($s);
        if ($response->http_code != 200)
        {
            return Response::json($response->body, 404);
        }
        $body = $response->body;
        $i = $per_page * ($page - 1) + 1;
        $table = $this->initTable();
        $messages = $table->getData();
        
        foreach ($body->items as $item)
        {
            if ($item->last_visit_time > 0)
            {
                $item->no_visit_days = floor(
                        (time() - $item->last_visit_time) / 86400);
            } else
            {
                $item->no_visit_days = 0;
            }
            $item->no_recharge_days = floor(
                    (time() - $item->last_order_time) / 86400);
            $item->is_anonymous_ever = 'NO';
            if ($item->is_anonymous == 1)
            {
                $item->is_anonymous_ever = 'YES';
            }
            $amount = $item->total_yuanbao_amount;
            foreach ($messages as $k => $v)
            {
                if ($amount >= $v->min && $amount <= $v->max)
                {
                    $vip_level = $v->level;
                    break;
                }
            }
            /*changed by xs*/
            $game_api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            if(isset($item->player_id)) {
                $player_info_from_id = $game_api->getPlayerInfoByPlayerID($item->player_id);
                if (isset($player_info_from_id->Name)) {
                   $item->vip_level = isset($player_info_from_id->VIPLevel) ? $player_info_from_id->VIPLevel : $vip_level;   
                } else {
                    $item->vip_level = $vip_level;
                }
               
            } elseif (isset($item->player_name)) {
                $player_info_from_name = $game_api->getPlayerInfoByName($v->player_name);
                if (isset($player_info_from_name)) {
                    $player_info_from_id = $game_api->getPlayerInfoByPlayerID($player_info_from_name->player_id);
                    $item->vip_level = isset($player_info_from_id->VIPLevel) ? $player_info_from_id->VIPLevel : $vip_level ;  
                } else {
                    $item->vip_level = $vip_level;
                }
            }
            /*changed over*/
            $item->rank = $i ++;
            $region = '';
            $game_id = Session::get('game_id');
            if ($game_id == '30' || $game_id == '1' || $game_id == '8')
            {                $ip_api = "http://freegeoip.net/json/";
                $ip_api .= $item->created_ip;
                
                $response = Curl::url($ip_api)->get();
                
                if ($response->http_code == 200)
                {
                    if(isset($response->body->country_name)){
                        $region = $response->body->country_name;
                    }
                }
            }
            $item->region = $region;
        }
        return Response::json($body);
    }

    public function downloadYuanbaoIndex()
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

    public function downloadYuanbaoData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $server_id = (int) Input::get('server_id');
        $player_id = (int) Input::get('player_id');
        $rank = (int) Input::get('rank');
        $player_name = Input::get('player_name');
        $uid = Input::get('uid');
        $vip_level = Input::get('vip_level');
        $server = Server::find($server_id);
        if (! $server)
        {
            return Response::json($msg, 403);
        }
        
        $result = array();
        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";
        $title = array(
                Lang::get("slave.rank"),
                Lang::get("slave.player_name"),
                Lang::get("slave.player_id"),
                Lang::get("slave.player_uid"),
                Lang::get("slave.vip_level"),
                
                Lang::get("slave.consumption_statics"),
                Lang::get("slave.economy_left_number"),
                Lang::get("slave.operation_name"),
                "Message",
                Lang::get("slave.operation_time")
        );
        $type = 'yuanbao';
        $csv = CSV::init($file, $title);
        // $platform = Platform::find(Session::get('platform_id'));
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        
        $game_message_table = Table::init(
                public_path() . '/table/' . $game->game_code .
                         '/game_message.txt');
        $game_messages = $game_message_table->getData();
        $message_arr = array();
        foreach ($game_messages as $message)
        {
            $message_arr[$message->id] = array(
                    'desc' => $message->desc,
                    'name' => $message->name
            );
        }
        $response = $api->getAllPlayerEconomy($game->game_id, 
                $server->server_internal_id, $player_id, $type, $start_time = '', 
                $end_time = '');
        $items = $response->body;
        foreach ($items as $x => $y)
        {
            $action_type = $y->action_type;
            $items[$x]->left_number = $y->yuanbao;
            $items[$x]->action_time = date('Y-m-d H:i:s', $y->action_time);
            $items[$x]->action_name = '';
            $items[$x]->action_type = $action_type;
            if (isset($message_arr[$action_type]))
            {
                $items[$x]->action_name = $message_arr[$action_type]['name'];
                if ($message_arr[$action_type]['desc'])
                {
                    $items[$x]->action_type = $message_arr[$action_type]['desc'];
                }
            }
            $result = array(
                    'rank' => $rank,
                    'player_name' => $player_name,
                    'player_id' => $player_id,
                    'uid' => $uid,
                    'vip_level' => $vip_level,
                    'spend' => $y->spend,
                    'left_number' => $y->left_number,
                    'action_type' => $y->action_type,
                    'action_name' => $y->action_name,
                    'action_time' => $y->action_time
            );
            $res = $csv->writeData($result);
            unset($result);
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

    private function initTable()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(
                public_path() . '/table/' . $game->game_code . '/game_vip.txt');
        return $table;
    }

    public function disputeOrderIndex()
    {
        $data = array(
                'content' => View::make('slaveapi.payment.order.dispute')
        );
        return View::make('main', $data);
    }

    public function disputeOrder()
    {
        $order_sn = Input::get('order_sn');
        $fb_name = Input::get('fb_name');
        $fb_id = Input::get('fb_id');
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $status = (int) Input::get('status');
        $order = array();
        if ($order_sn)
        {
            $order['order_sn'] = $order_sn;
        }
        if ($fb_name)
        {
            $order['fb_name'] = $fb_name;
        }
        if ($fb_id)
        {
            $order['fb_id'] = $fb_id;
        }
        if ($start_time && $end_time)
        {
            $order['start_time'] = $start_time;
            $order['end_time'] = $end_time;
        }
        if ($status < 2)
        {
            $order['status'] = $status;
        }
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $response = $api->getFBDisputeOrders(Session::get('platform_id'), 
                $order);
        
        if ($response->http_code == 200)
        {
            foreach ($response->body as $v)
            {
                $v->status_name = $v->status == 0 ? Lang::get(
                        'slave.fb_dispute_status_no') : ($v->execute_refund == 1 ? Lang::get(
                        'slave.fb_dispute_status_yes_refund') : Lang::get(
                        'slave.fb_dispute_status_refund'));
                $v->execute_refund_name = $v->execute_refund == 0 ? Lang::get(
                        'slave.execute_refund_name_no') : Lang::get(
                        'slave.execute_refund_name_yes');
                $v->create_time = date('Y-m-d H:i:s', $v->create_time);
                if ($v->execute_time > 0)
                {
                    $v->execute_time = date('Y-m-d H:i:s', $v->execute_time);
                } else
                {
                    $v->execute_time = '';
                }
            }
            return Response::json($response->body);
        } else
        {
            return Response::json($response->body, $response->http_code);
        }
    }

    public function disputeOrderAct()
    {
        $msg = array(
                'code' => '',
                'error' => ''
        );
        $act_type = Input::get('act_type');
        $type_val = Input::get('type_val');
        $dispute_id = (int) Input::get('dispute_id');
        $tradeseq = Input::get('tradeseq');
        $currency_code = Input::get('currency_code');
        $refund_amount = Input::get('refund_amount');
        $reason_id = (int) Input::get('reason_id');
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        if ($act_type == 'refund')
        {
            $api->executeRefund($dispute_id, $tradeseq, $currency_code, 
                    $refund_amount, $reason_id);
        } else if ($act_type == 'edit')
        {
            $api->changeDispute($dispute_id, $refund_amount, $type_val);
        }
        return $api->sendResponse();
    }

    public function getRefundOrders()
    {
        $data = array(
                'content' => View::make('slaveapi.payment.order.refund')
        );
        return View::make('main', $data);
    }

    public function getRefundOrdersData()
    {
        $order_sn = Input::get('order_sn');
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $pay_type_id = (int) Input::get('pay_type_id');
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $response = $api->getRefundOrders(Session::get('platform_id'), 
                $order_sn, $start_time, $end_time, $pay_type_id);
        if ($response->http_code != 200)
        {
            return Response::json($response->body, $response->http_code);
        }
        foreach ($response->body as $k => $v)
        {
            $v->pay_time = date('Y-m-d H:i:s', $v->pay_time);
            $v->time_updated = date('Y-m-d H:i:s', $v->time_updated);
            $pay_type = PayType::getPayType($v->pay_type_id)->first();
            if ($pay_type)
            {
                $v->pay_type_name = $pay_type->pay_type_name;
            } else
            {
                $v->pay_type_name = $pay_type->pay_type_id;
            }
            $currency = Currency::where('currency_code', $v->currency_code)->first();
            if($currency){
                $exchanges = Rate::where('from', $currency->currency_id)->first();
                $v->refund_amount_dollar = $v->refund_amount * $exchanges->multiplier_rate;
            }
            else{
                $v->refund_amount_doller = 'unknown exchange rate:'.$v->currency_code;
            }
        }
        return Response::json($response->body);
    }

    public function refundOrderAct()
    {
        //用来生成订单
        $order_id = (int)Input::get('order_id');
        $order_sn = Input::get('order_sn');
        $tradeseq = Input::get('tradeseq');
        $pay_user_id = (int) Input::get('pay_user_id');
        $currency_code = Input::get('currency_code');
        $pay_type_name = Input::get('pay_type_name');
        $pay_amount = (float)Input::get('pay_amount');
        $basic_yuanbao_amount = (float)Input::get('basic_yuanbao_amount');
        $extra_yuanbao_amount = (float)Input::get('extra_yuanbao_amount');
        $huodong_yuanbao_amount = (float)Input::get('huodong_yuanbao_amount');
        $yuanbao_amount = (float)Input::get('yuanbao_amount');
        $server_internal_id = (int)Input::get('server_internal_id');
        $game_id = Session::get('game_id');
        if($game_id == 52){//印尼德州手游没有自己的服务器
            $by_game_id = 52;
            $game_id = 11;
        }
        $platform_server_id = Server::getPlatformServerId($game_id, $server_internal_id)->first();
        $platform_server_id = $platform_server_id->platform_server_id;
        //
        $refund_amount = (float) Input::get('refund_amount');
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $responseRefund = $api->createRefundOrder($order_id, $refund_amount, $currency_code);
        
        if(!empty($responseRefund->body)){
            $bodyRefund = $responseRefund->body;
            if($bodyRefund->error != 0){
                $refundInfo = $bodyRefund->error_description;
            }else{
                $refundInfo = "Refund Success! \n";
            }
        }else{
            //Log::info('Step: refund. No data from Platform.');
           // Log::info(var_export($responseRefund, true));
            return 'Step: refund. No data from Platform.';
        }
        //以下是生成“负订单”. 
        //2015-03-05 去掉该行为，pay_order表格中清除“负订单”，所有退款记录应在refund_order表格中查询.
        // $order = array(
        //     'tradeseq' => 'RO'.$order_sn,
        //     'platform_server_id' => (int)$platform_server_id,
        //     'game_id' => (int)(isset($by_game_id)?$by_game_id:$game_id),
        //     'pay_user_id' => $pay_user_id,
        //     'currency' => $currency_code,
        //     'pay_amount' => -$refund_amount,//实际退的数目，作为充值数
        //     'basic_yuanbao_amount' => $basic_yuanbao_amount,
        //     'extra_yuanbao_amount' => $extra_yuanbao_amount,
        //     'huodong_yuanbao_amount' => $huodong_yuanbao_amount,
        //     'yuanbao_amount' => $yuanbao_amount
        //     );
        // //var_dump($orders);die();
        // if ($game_id == 11) { //德州扑克
        //     $platform->payment_api_url = 'http://payid.joyspade.com' ;
        // }
        // $newOrder_api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
        // $responseNewOrder = $newOrder_api->createOrder($order);

        // if(!empty($responseNewOrder->body)){
        //     $body = $responseNewOrder->body;
        //     if($body->error != 0){
        //         $orderInfo = $body->error_description;
        //     }else{
        //         $orderInfo = "New Order Create Successfully!";
        //     }
        // }else{
        //     Log::info('Step: new order. No data from Platform.');
        //     Log::info(var_export($responseNewOrder, true));
        //     return 'Step: new order. No data from Platform.';
        // }

        return $refundInfo;
        //return $refundInfo.$orderInfo;
        
    }

    public function payTypeIndex()
    {
        $data = array(
                'content' => View::make('slaveapi.payment.paytype.index')
        );
        return View::make('main', $data);
    }

    public function getPayTypeStat()
    {
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $platform_id = Session::get('platform_id');
        $pay_type_id = (int) Input::get('pay_type_id');
        $game = Game::find(Session::get('game_id'));
        $platform = Platform::find(Session::get('platform_id'));
        $currency_id = $platform->default_currency_id;
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $response = $api->getPayTypeStat($platform_id, $pay_type_id, 
                $start_time, $end_time, $game->game_id, $currency_id);
        if ($response->http_code != 200)
        {
            return Response::json($response->body, $response->http_code);
        }
        $all_amount = 0;
        foreach ($response->body as $v)
        {
            $all_amount += $v->total_amount;
        }
        foreach ($response->body as $k => $v)
        {
            $v->amount_rate = $all_amount ? round($v->total_amount / $all_amount * 100, 2) : 0;
            $v->pay_time_first = date('Y-m-d H:i:s', $v->pay_time_first);
            $v->pay_time_last = date('Y-m-d H:i:s', $v->pay_time_last);
            $v->get_payment_rate = round(
                    ($v->get_payment_count / $v->count) * 100, 2);
            $pay_type = PayType::getPayType($v->pay_type_id)->first();
            if ($pay_type)
            {
                $v->pay_type_name = $pay_type->pay_type_name;
            }
            $payment = Payment::getPayments($v->pay_type_id, $v->method_id, 
                    $v->zone)->first();
            if ($payment)
            {
                $v->pay_method_name = isset($payment->method_name) ? $payment->method_name: 'none';
            }
        }
        $result = array('body' => $response->body);
        return Response::json($result);
    }

    public function getServerRevenueByDay()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make(
                        'slaveapi.payment.order.server_day_revenue', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function getServerDataCompareByDay(){
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make(
                        'slaveapi.payment.order.server_day_compare', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function getServerDataCompareByDayData(){
        $this->getServerRevenueByDayData($all_data=1);
    }

    public function getServerRevenueByDayData($all_data = 0)
    {
        $msg = array(
                'code' => Config::get('errorcode.slave_server_day_revenue'),
                'error' => Lang::get('error.basic_input_error')
        );
        
        $server_ids = Input::get('server_id');
        
        if (empty($server_ids))
        {
            return Response::json($msg, 403);
        }
        $days_start = (int) Input::get('days_start');
        $days_end = (int) Input::get('days_end');
        $days_start = $days_start > 0 ? $days_start : 1;
        $days_end = $days_end > 0 ? $days_end : 30;
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $platform_id = Session::get('platform_id');
        
        $servers = Server::whereIn('server_id', $server_ids)->orderBy('platform_server_id', 'desc')->get();
        $platform_server_ids = array();
        $server_log_info = array();
        foreach ($servers as $v)
        {
            if($all_data){
                $server_log_info[$v->server_internal_id] = array(
                    'server_name' => $v->server_name,
                    'open_server_time' => $v->open_server_time,
                    );
                $tmp_result = $api->getOpenServerFrontDays($v->open_server_time, $game_id, $v->server_internal_id, $days_start, $days_end);
                if(200 == $tmp_result->http_code){
                    $number_info = $tmp_result->body->num_info;
                    $days_info = array();
                    foreach ($number_info as $days => $days_data) {
                        $days_info[$days] = array(
                            'login_num' => isset($days_data->login_num) ? $days_data->login_num : '',
                            'create_num' => isset($days_data->create_num) ? $days_data->create_num : '',
                            );
                    }
                    unset($number_info);
                    $days_retention = $tmp_result->body->retention_info;
                    $retention_info = array();
                    foreach ($days_retention as $key => $value) {
                        $retention_info[$key] = $value;
                    }
                    unset($days_retention);
                    unset($tmp_result);
                    $server_log_info[$v->server_internal_id]['num_info'] = $days_info;
                    $server_log_info[$v->server_internal_id]['retention_info'] = $retention_info;
                    unset($days_info);
                    unset($retention_info);
                }else{
                    $server_log_info[$v->server_internal_id]['num_info'] = array();
                    $server_log_info[$v->server_internal_id]['retention_info'] = array();
                }
            }
            $platform_server_ids[] = $v->platform_server_id;
        }
        $response_payment = $api->getServerRevenueByDay($platform_id, 
                $platform_server_ids, $days_start, $days_end);
        if ($response_payment->http_code != 200)
        {
            return Response::json($response_payment->body, $response_payment->http_code);
        }

        $data = array(
            'days_start' => $days_start,
            'days_end' => $days_end,
            'servers_payment' => $response_payment->body,
            'servers_log' => $server_log_info,
            'all_data' => $all_data,
            );

        Excel::loadView('slaveapi.payment.order.server_day_revenue_excel')->with(
                'data', $data)
            ->setTitle('ServerRevenue')
            ->export('xls');
    }

    public function serverConsumeIndex()
    {
        $server = Server::currentGameServers()->get();
        $table = $this->init_table();
        $type = $table->getData();
        $data = array(
            'content' => View::make('slaveapi.economy.player-server', array('server' => $server, 'type' => $type)),
        );
        return View::make('main', $data);
    }

    public function serverConsumeData()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknown'),
            'error' => Lang::get('error.server_not_found'),
        );
        $rules = array(
            'start_time' => 'required',
            'end_time' => 'required',
            'server_id' => 'required',
            'type' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $server_id = Input::get('server_id');
        $server = Server::find($server_id);
        if (!$server) {
            return Response::json($msg, 403);
        }

        $server_internal_id = $server->server_internal_id;
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $type = Input::get('type');
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $response = $api->serverConsumeData($platform_id, $game_id, $server_internal_id, $start_time, $end_time, $type);
        $aa = Excel::loadView('slaveapi.economy.player-server-excel')->with(
                'data', $response->body)
            ->setTitle('ServerRevenue')
            ->export('xls');
        return Response::json($aa);
    }

    public function downloadServerConsumeIndex()
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

    public function downloadServerConsumeData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $server_id = Input::get('server_id');
        /*$server = Server::find($server_id);
        if (!$server) {
            return Response::json($msg, 403);
        }*/
        //$server_internal_id = $server->server_internal_id;
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";
        $title = array(
                Lang::get("slave.player_id"),
                Lang::get("slave.player_name"),
                Lang::get("slave.player_uid"),
                Lang::get("slave.server_id"),
                Lang::get("slave.server_name"),
                Lang::get("slave.diff_yuanbao"),
                Lang::get("slave.action_type"),
                Lang::get("slave.action_name"),
        );
        $action_type = (int)Input::get('action_type');
        $csv = CSV::init($file, $title);
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        
        $game_message_table = Table::init(
                public_path() . '/table/' . $game->game_code .
                         '/consume.txt');
        $game_messages = $game_message_table->getData();
        $message_arr = (array) $game_messages;
        /*foreach ($game_messages as $message)
        {
            $message_arr[$message->id] = array(
                    'desc' => $message->action_type,
                    'name' => $message->action_name
            );
        }*/
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $data = array();
        foreach ($server_id as $key => $server_id) {
            $server = Server::find($server_id);
            if (!$server) {
                return Response::json($msg, 403);
            }
            $response = $api->serverConsumeData($platform_id, $game_id, $server->server_internal_id, $start_time, $end_time, $action_type);
            $items = $response->body;
            foreach ($items as $x => $y)
            {
                /*$action_type = $y->action_type;
                $items[$x]->action_name = '';
                $items[$x]->action_type = $action_type;
                if (isset($message_arr[$action_type]))
                {
                    $items[$x]->action_name = $message_arr[$action_type]['action_type'];
                    if ($message_arr[$action_type]['action_type'])
                    {
                        $items[$x]->action_type = $action_type;
                    }
                }*/
                foreach ($message_arr as $key => $value) {
                    if ($value->action_type == $y->action_type) {
                        $action_name = $value->action_name;
                    }
                }
                $result = array(
                        'player_id' => $y->player_id,
                        'player_name' => $y->player_name,
                        'player_uid' => $y->user_id,
                        'server_id' => $y->server_id,
                        'server_name' => $server->server_name,
                        'spend' => $y->spend,
                        'action_type' => $y->action_type,
                        'action_name' => $action_name,
                );
                $res = $csv->writeData($result);
                unset($result);
                unset($action_name);
                
            }
            
        }
        $res = $csv->closeFile();
        //$res = $csv->writeData($data);
        if ($res)
        {
            $da = array(
                    'now' => $now
            );
            return Response::json($da);
        } else
        {
            return Response::json($msg, 403);
        }
        /*$response = $api->serverConsumeData($platform_id, $game_id, $server_internal_id, $start_time, $end_time, $action_type);
        $items = $response->body;
        foreach ($items as $x => $y)
        {
            $action_type = $y->action_type;
            $items[$x]->action_name = '';
            $items[$x]->action_type = $action_type;
            if (isset($message_arr[$action_type]))
            {
                $items[$x]->action_name = $message_arr[$action_type]['desc'];
                if ($message_arr[$action_type]['desc'])
                {
                    $items[$x]->action_type = $action_type;
                }
            }
            $result = array(
                    'server_id' => $y->server_id,
                    'player_id' => $y->player_id,
                    'spend' => $y->spend,
                    'action_type' => $y->action_type,
                    'action_name' => $y->action_name,
            );
            $res = $csv->writeData($result);
            unset($result);
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
        }*/
    }

    //选取服务器
    public function init_table()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(
                public_path() . '/table/' . $game->game_code . '/consume.txt');
        return $table;
    }

    private function initTable3()
    {
        $game = Game::find(Session::get('game_id'));
        $table = Table::init(public_path() . '/table/' . 'flsg'. '/server.txt');
        return $table;
    }

    public function yuanbaoSearchIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.payment.order.rank_search', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function yuanbaoSearchData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $server_ids = Input::get('server_id');
        $platform_id = Session::get('platform_id');
        $platform = Platform::find(Session::get('platform_id'));
        $game_id = Session::get('game_id');
        $game = Game::find(Session::get('game_id'));
        $time_type = Input::get('time_type');
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $table = $this->initTable();
        $messages = $table->getData();
        $data = array();
        if('0' == $server_ids){
            return Response::json(array('error'=>'Please select a server'),404);
        }
        if(count($server_ids) > 0 ){//多服
            foreach ($server_ids as $key => $server_id) {
                $server = Server::find($server_id);
                if (!$server) {
                    return Response::json($msg, 403);
                }
                $server_internal_id = $server->server_internal_id;
                if (!$server_internal_id) {
                    return Response::json($msg, 403);
                }
                if(1 == $time_type){
                    $start_time = $server->open_server_time;
                }
                $response = $api->yuanbaoRankSearch($platform->platform_id, $game->game_id, 
                        $server->platform_server_id, $start_time, $end_time, 
                        $platform->default_currency_id, $server->server_internal_id
                      );
                //Log::info(var_export($response,true));
                if ($response->http_code != 200)
                {
                    return Response::json($response->body, 404);
                }
                $body = $response->body;
                
                foreach ($body->items as $item)
                {
                    if ($item->last_visit_time > 0)
                    {
                        $item->no_visit_days = floor(
                                (time() - $item->last_visit_time) / 86400);
                    } else
                    {
                        $item->no_visit_days = 0;
                    }
                   if(1 == $time_type){
                       $item->no_recharge_days = floor(
                               (time() - $item->last_order_time) / 86400);
                       $amount = $item->total_yuanbao_amount;
                       $item->vip_level = 0;
                       foreach ($messages as $k => $v)
                       {
                           if ($amount >= $v->min && $amount <= $v->max)
                           {
                               $item->vip_level = $v->level;
                               break;
                           }
                       }
                   }else{
                        $item->no_recharge_days = floor(
                            (time() - $item->last_order_time) / 86400);
                       if(in_array($game_id, Config::get('game_config.mobilegames'))){
                           $game_api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
                       }else{
                           $game_api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
                       }
                       if('yysg' == $game->game_code){
                           if(isset($item->uid))
                           {
                               $player_info_from_name = $api->getYYSGPlayerInfoByUID($item->uid, $platform_id);
                           }elseif(isset($item->player_name)){
                               $player_info_from_name = $api->getYYSGPlayerInfo($item->player_name, $platform_id);
                           }else{
                               $item->vip_level = 0;
                           }
                           $item->vip_level = isset($player_info_from_name->vip) ? $player_info_from_name->vip : 0;
                       }elseif('mnsg' == $game->game_code){
                           if(isset($item->player_id)){
                               $player_info_from_name = $api->getMNSGPlayerInfo($item->player_id, $platform_id);
                               $item->vip_level = isset($player_info_from_name->vip) ? $player_info_from_name->vip : 0;
                           }else{
                               $item->vip_level = 0;
                           }
                       }else{
                           if(isset($item->player_id)){
                               $player_info_from_id = $game_api->getPlayerInfoByPlayerID($item->player_id);
                               $item->vip_level = isset($player_info_from_id->VIPLevel) ? $player_info_from_id->VIPLevel : 0;   
                           }elseif(isset($item->player_name)){
                               if (isset($player_info_from_name)) {
                                   $player_info_from_id = $game_api->getPlayerInfoByPlayerID($player_info_from_name->player_id);
                                   $item->vip_level = isset($player_info_from_id->VIPLevel) ? $player_info_from_id->VIPLevel : 0 ;  
                               } else {
                                   $item->vip_level = 0;
                               }
                           }
                              
                       }
                    }
                }
                if (isset($body)) {
                    foreach ($body->items as $item) {
                        $data[] = array(
                            'server_name' => $server->server_name,
                            'uid' =>$item->uid,
                            'player_id' =>$item->player_id,
                            'player_name' =>$item->player_name,
                            'vip_level' => isset($item->vip_level) ? $item->vip_level : '',
                            'total_yuanbao_amount' =>$item->total_yuanbao_amount,
                            'total_dollar_amount' =>$item->total_dollar_amount,
                            'no_recharge_days' =>$item->no_recharge_days,
                            'no_visit_days' =>$item->no_visit_days,
                        );
                    }
                }
                unset($body);
            }
        }
        return Response::json($data);
    }

    public function PaymentInfoIndex(){ //充值分析
        $data = array(
            'content' => View::make('slaveapi.economy.infooftime',array())
        );
        return View::make('main', $data);
    }

    public function PaymentInfoData(){
        $start_time = $this->current_time_nodst(strtotime(Input::get('start_time')));
        $end_time = $this->current_time_nodst(strtotime(Input::get('end_time')));
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $result = array();

        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $first_pay_info = $slaveapi->getfirstpayinfo($game_id, $start_time, $end_time, $platform_id);
        if('200' != $first_pay_info->http_code){
            return $slaveapi->sendResponse();
        }
        $first_pay_info = $first_pay_info->body;
        $result['firstpayinfo'] = $first_pay_info->first_pay;
        $result['pay_newer'] = $first_pay_info->pay_newer;
        unset($first_pay_info);

        $amount_info = $slaveapi->getamountinfo($game_id, $start_time, $end_time, $platform_id);
        if('200' != $amount_info->http_code){
            return $slaveapi->sendResponse();
        }
        $amount_info = $amount_info->body;
        $result['arppu'] = $amount_info->arppu;
        $result['devide_parts'] = $amount_info->devide_parts;
        $result['time_group'] = $amount_info->time_group;
        unset($amount_info);
        $parts = 288;
        $interval = (($end_time+1)-$start_time) / $parts;
        $pay_trend = $slaveapi->getPayTrendinfo($game_id, $start_time, $end_time, $platform_id, $interval);
        if('200' != $pay_trend->http_code){
            return $slaveapi->sendResponse();
        }
        $result['pay_trend'] = $pay_trend->body;
        return Response::json($result);
    }

    public function expenseSumIndex(){
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('slaveapi.payment.order.expense',array(
                'servers' => $servers,
                ))
        );
        return View::make('main', $data);
    }

    //查询时间段内服务器付费人数和金额
    public function expenseSumData(){
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );

        $server_internal_id = Input::get('server_internal_id');
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $interval = Input::get('interval');
        $game = Game::find($game_id);
        $type = Input::get('type');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');

        if('0' == $server_internal_id){
            return Response::json(array('error'=>"请选择一个服务器"), 403);
        }
        if(!$game){
            return Response::json(array('error'=>"invalued game"), 403);
        }
        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $data = array();
        if ($server_internal_id[0] == "0"){//全服
            foreach ($this->servers as $server) {
                $server_internal_ids[] = $server->server_internal_id;
            }
        }else{
            foreach ($server_internal_id as $server) {
                if(0 != $server){
                    $server_internal_ids[] = $server;
                }
            }
        }
                                

        unset($server_internal_id);
        $result = array();
        $allserver = array();
        foreach ($server_internal_ids as $server_internal_id) {
            $tmp_result = $slaveapi->getExpenseSum($server_internal_id, $game_id, $platform_id, $start_time, $end_time, $interval);
            if('200' == $tmp_result->http_code){
                $result[$server_internal_id] = $tmp_result->body;
                foreach ($result[$server_internal_id] as $key => $value) {
                    if(isset($allserver[$value->date])){
                        $allserver[$value->date]['player_num'] += $value->player_num;
                        $allserver[$value->date]['avg_online_value'] += $value->avg_online_value;
                        $allserver[$value->date]['sum_dollar'] += $value->sum_dollar;
                        $allserver[$value->date]['create_num'] += $value->create_num;
                    }else{
                        $allserver[$value->date] = array(
                                'server_name' => 'Total',
                                'date'  =>  $value->date,
                                'player_num'    =>  $value->player_num,
                                'sum_dollar'    =>  $value->sum_dollar,
                                'avg_online_value'  =>  $value->avg_online_value,
                                'create_num'    => $value->create_num,
                                'usernum'   => 0,

                            );
                    }
                }
            }
            unset($tmp_result);
        }

        $all_play_register_num = $slaveapi->getUserNum($game_id, $platform_id, $start_time, $end_time, $interval);

        if('200' == $all_play_register_num->http_code){
            $all_play_register_num = $all_play_register_num->body;
            foreach ($allserver as $key1 => $value1) {
                foreach ($all_play_register_num as $key2 => $value2) {
                    if($key1==date("Y-m-d",$value2->date)){
                        $allserver[$key1]['usernum']=$value2->usernum;
                        break;
                    }
                    else
                        $allserver[$key1]['usernum']=0;
                }
            }
        }
        unset($all_play_register_num);
        $result = array(
            'result'    =>  $result,
            'allserver' =>  $allserver,
            );
        return Response::json($result);
    }

    public function FirstPayInfoIndex(){
        $data = array(
            'content' => View::make('slaveapi.payment.analysis.firstpayinfo',array())
        );
        return View::make('main', $data);        
    }

    public function FirstPayInfoDo(){
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $interval = (int)Input::get('interval');

        if($start_time >= $end_time){
            return Response::json(array('error'=>'时间选择异常'), 403);
        }

        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');

        $game = Game::find($game_id);

        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $result = $slaveapi->getfirstpayanalysis($game_id, $platform_id, $start_time, $end_time);

        if('200' == $result->http_code){
            $result = $result->body;
        }else{
            return Response::json(array('error'=>'slave端发生异常'), 403);
        }
        $interval = $interval > 0 ? $interval : 1;
        $count = 0;
        $datatoreturn = array();
        for($i=0;$i<count($result);$i+=$interval){  //返回数据的最小单位是天，根据页面选择的间隔时间再处理数据
            $datatoreturn[$count] = array(
                'start_time' => '',
                'end_time'  =>  '',
                'allnum'    =>  0,
                'newnum'    =>  0,
                'oldnum'    =>  0,
                'rate'  =>  '',
                );
            for($j=0;$j<$interval;$j++){
                if(0 == $j){
                    $datatoreturn[$count]['start_time'] = date('Y-m-d H:i:s', ($result[$i+$j]->start_time));
                }
                if($j+1 == $interval && $i+$j< count($result)){
                    $datatoreturn[$count]['end_time'] = date('Y-m-d H:i:s', ($result[$i+$j]->end_time));
                }

                if($i+$j < count($result)){
                    $datatoreturn[$count]['allnum'] += $result[$i+$j]->allnum;
                    $datatoreturn[$count]['newnum'] += $result[$i+$j]->newnum;
                    $datatoreturn[$count]['oldnum'] += $result[$i+$j]->oldnum;
                }else{
                    $datatoreturn[$count]['end_time'] = date('Y-m-d H:i:s', ($result[$i+$j-1]->end_time));
                    break;
                }
            }
            if('0' == $datatoreturn[$count]['allnum']){
                $datatoreturn[$count]['rate'] = '00.00%';
            }else{
                $datatoreturn[$count]['rate'] = round($datatoreturn[$count]['newnum']/$datatoreturn[$count]['allnum']*100, 2).'%';
            }
            $count ++;  //显式命名下标，否则返回给页面的数据排序异常
        }
        unset($count);
        unset($result);
        if(count($datatoreturn)){
            return Response::json($datatoreturn);
        }else{
            return Response::json(array('error'=>'查询无结果'), 403);
        }
    }

    public function GiftbagMessageIndex(){  //查看手游礼包信息
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);

        $mobile_game = $game->game_type - 1;

        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $paymethods = $slaveapi->getAllMobliePaymethods($platform_id, $game_id, $mobile_game);
        $pay_type_id_name = array();
        $method_id_name = array();
        if('200' == $paymethods->http_code){
            $paymethods = $paymethods->body;
            foreach ($paymethods->pay_type as $value) {
                if(!in_array($value->pay_type_name, $pay_type_id_name)){
                    $pay_type_id_name[$value->pay_type_id] = $value->pay_type_name;
                }
                if(isset($method_id_name[$value->pay_type_id])){
                    $method_id_name[$value->pay_type_id][$value->method_id] = $value->method_name;
                }else{
                    $method_id_name[$value->pay_type_id] = array();
                    $method_id_name[$value->pay_type_id][$value->method_id] = $value->method_name;
                }
                unset($value);
            }
        }else{
            App::abort(404);
        }

        $data = array(
            'content' => View::make('slaveapi.payment.product.giftbagIndex',array(
                    'pay_type_id_name' => $pay_type_id_name,
                    'method_id_name' => $method_id_name,
                    'currencies' => $paymethods->currency,
                    'mobile_game' => $mobile_game,
                ))
        );
        return View::make('main', $data);   
    }

    public function GiftbagMessage(){
        $pay_type_id = Input::get('pay_type_id');
        $method_id = Input::get('method_id');
        $product_type = Input::get('product_type');
        $currency_id = Input::get('currency_id');

        $game_id = Session::get('game_id');

        $platform_id = Session::get('platform_id');

        $game = Game::find($game_id);

        $mobile_game = $game->game_type - 1;

        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $result = $slaveapi->getGameProduct($platform_id, $game_id, $pay_type_id, $method_id, $product_type, $currency_id, $mobile_game);

        if('200' != $result->http_code){
            return $slaveapi->sendResponse();
        }

        $result = $result->body;

        return Response::json($result);
    }

    public function recordOrder(){
        $data = array(
            'order_id' => Input::get('order_id'),
            'order_sn' => Input::get('order_sn'),
            'pay_user_id' => Input::get('pay_user_id'),
            'tradeseq' => Input::get('tradeseq'),
            'pay_amount' => round((int)Input::get('pay_amount'), 0),
            'currency_code' => Input::get('currency_code'),
            'pay_type_name' => Input::get('pay_type_name'),
            'method_name' => Input::get('method_name'),
            'player_id' => Input::get('player_id'),
            'player_name' => Input::get('player_name'),
            'server_name' => Input::get('server_name'),
            'reason' => Input::get('reason'),
            'created_time' => time(),
            'order_created_time' => Input::get('order_created_time'),
            'created_operator' => Auth::user()->username,
            'game_id' => Session::get('game_id'),
            'last_operator' => '-',
            'deal_time' => 0,
            'result' => '-',
            'type' => Input::get('order_type'),
            'updated_at' => time(),
            'is_done' => 0,
        );

        $already =  RecordOrders::where('game_id', $data['game_id'])->where('order_id', $data['order_id'])->where('type', $data['type'])->first();
        if($already){
            if($already->deal_time > 0){
                $msg = Lang::get('slave.order_record_dealt');
            }else{
                $data2update = array(
                    'reason' => $already->reason.';'.$data['reason'],
                    );
                if(Input::get('tradeseq')){
                    $data2update['tradeseq'] = Input::get('tradeseq');
                }
                DB::table('record_orders')
                    ->where('game_id', $data['game_id'])
                    ->where('order_id', $data['order_id'])
                    ->where('type', $data['type'])
                    ->update($data2update);
                unset($data);
                $msg = Lang::get('slave.order_record_update');
            }
        }else{
            DB::table('record_orders')->insert($data);
            $msg = Lang::get('slave.order_record');
        }
        return Response::json(array('msg' => $msg));
    }

    public function getRecordOrders(){
        if(is_numeric(strpos(Request::url(), 'gm'))){
            $is_gm = 1;
        }else{
            $is_gm = 0;
        }
        $data = array(
            'content' => View::make('slaveapi.payment.order.record_orders',array(
                    'is_gm' => $is_gm,
                ))
        );
        return View::make('main', $data);   
    }

    public function getAwardOrders(){
        if(is_numeric(strpos(Request::url(), 'gm'))){
            $is_gm = 1;
        }else{
            $is_gm = 0;
        }
        $game = Game::find(Session::get('game_id'));
        $award_link = '/';
        switch ($game->game_code) {
            case 'flsg':
                $award_link = '/game-server-api/mail/gift-mail';
                break;
            case 'nszj':
                $award_link = '/game-server-api/mail/gift-mail';
                break;
            case 'yysg':
                $award_link = '/game-server-api/yysg/gift-bag';
                break;
            default:
                break;
        }
        $data = array(
            'content' => View::make('slaveapi.payment.order.award_orders',array(
                    'is_gm' => $is_gm,
                    'award_link' => $award_link,
                ))
        );
        return View::make('main', $data);          
    }

    public function dealRecordOrders(){
        $type = Input::get('type');
        $game_id = Session::get('game_id');
        if('search' == $type){
            $page = Input::get('page');
            $page = $page > 0 ? $page : 1;
            $by_time = Input::get('by_time');
            $already_deal = Input::get('already_deal');
            $start_time = strtotime(Input::get('start_time'));
            $end_time = strtotime(Input::get('end_time'));
            $order_type = Input::get('order_type');
            $is_gm = Input::get('is_gm');
            $order_msg = array(
                'order_id' => Input::get('order_id'),
                'order_sn' => Input::get('order_sn'),
                'tradeseq' => Input::get('tradeseq'),
                'pay_user_id' => Input::get('pay_user_id'),
                'player_id' => Input::get('player_id'),
                'player_name' => Input::get('player_name'),
                );
            $count = RecordOrders::getRecordOrders($by_time, $already_deal, $start_time, $end_time, $is_gm, $order_msg, $game_id, $order_type)->count();
            $result = RecordOrders::getRecordOrders($by_time, $already_deal, $start_time, $end_time, $is_gm, $order_msg, $game_id, $order_type)->forpage($page, 50)->get();
            if($result){
                $orders = array();
                foreach ($result as &$order) {
                    if($order->deal_time > 0){
                        $order->deal_time = date('Y-m-d H:i:s', $order->deal_time);
                    }else{
                        $order->deal_time = '-';
                    }
                    $if_img = file_exists(public_path().'/img/order_img/'.$order->game_id.'_'.$order->order_id.'_fail.jpg') ? 1 : 0;
                    $orders[] =  array(
                        'order_id' => $order->order_id,
                        'order_sn' => $order->order_sn,
                        'tradeseq' => $order->tradeseq,
                        'pay_user_id' => $order->pay_user_id,
                        'pay_amount' => $order->pay_amount,
                        'currency_code' => $order->currency_code,
                        'pay_type_name' => $order->pay_type_name,
                        'method_name' => $order->method_name,
                        'player_id' => $order->player_id,
                        'player_name' => $order->player_name,
                        'server_name' => $order->server_name,
                        'reason' => $order->reason,
                        'created_time' => $order->created_time,
                        'order_created_time' => $order->order_created_time,
                        'created_operator' => $order->created_operator,
                        'game_name' => $order->game_name,
                        'last_operator' => $order->last_operator,
                        'deal_time' => $order->deal_time,
                        'result' => $order->result,
                        'id' => $order->id,
                        'game_id' => $order->game_id,
                        'is_done' => $order->is_done,
                        'if_img' => $if_img,
                        );
                    unset($order);
                }
                unset($result);
                $response = array(
                    'orders' => $orders,
                    'current_page' => $page,
                    'count' => $count,
                    );
                return Response::json($response);
            }else{
                return Response::json(array('error' => "No data"), 404);
            }
        }

        if('update' == $type){
            $id = Input::get('id'); //修改的记录订单的主键ID
            $data = array(
                'deal_time' => time(),
                'last_operator' => Auth::user()->username,
                'result' => Input::get('result'),
                );
            if($id){
                RecordOrders::where('id', $id)->update($data);
                return Response::json(array('msg' => "Deal Success"));
            }else{
                return Response::json(array('error' => "No such order"), 404);
            }
        }

        if('finish' == $type){
            $id = Input::get('id'); //修改的记录订单的主键ID
            $data = array(
                'is_done' => 1,
                );
            if($id){
                RecordOrders::where('id', $id)->update($data);
                return Response::json(array('msg' => "Deal Success"));
            }else{
                return Response::json(array('error' => "No such order"), 404);
            }
        }

        if('reset' == $type){
            $id = Input::get('id'); //修改的记录订单的主键ID
            $data = array(
                'tradeseq' => Input::get('tradeseq'),
                'reason' => Input::get('reason'),
                'deal_time' => 0,
                'last_operator' => '-',
                'result' => '-',
                'is_done' => 0,
                'created_time' => time(),
                );
            if(!$data['tradeseq']){
                unset($data['tradeseq']);
            }
            if($id){
                RecordOrders::where('id', $id)->update($data);
                return Response::json(array('msg' => "Deal Success"));
            }else{
                return Response::json(array('error' => "No such order"), 404);
            }
        }
    }

    public function uploadPic($game_id,$folder,$filename,$filetype){
        if(file_exists($_FILES["file_upload"]["tmp_name"])){
            $name = explode(".", $_FILES["file_upload"]["name"]);
            if(2 != count($name)){
                return Response::json(array('error'=>Lang::get('slave.pic_stand_name')), 403);
            }
            if($name[1] != "jpg" && $name[1] != "png"){
                return Response::json(array('error'=>Lang::get('slave.file_formmat_error')), 403);
            }

            if ($_FILES["file_upload"]["error"] > 0){
                return Response::json(array('error'=>Lang::get('slave.upload_fail')), 200);
            }else{
                move_uploaded_file($_FILES["file_upload"]["tmp_name"], $folder."/".$game_id."_".$filename.".".$filetype);
                return Response::json(array('error'=>Lang::get('slave.upload_success')), 200);
            }
        }else{
            return Response::json(array('error'=>Lang::get('slave.no_file')), 403);
        }
    }

    public function payorderfilterIndex(){
        if($now = Input::get('filename')){
            $file = storage_path() . "/cache/" . $now . ".csv";
            $data = array(
                    'content' => View::make('download', 
                            array(
                                    'file' => $file
                            ))
            );
            return View::make('main', $data);
        }
        $data = array(
                'content' => View::make('payment.filter.filterorder', 
                        array(
                        ))
        );
        return View::make('main', $data);
    }

    public function payorderfilter(){
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);

        $filter_data = array(
            'filter_type' => '',
            'by_pay_time' => '',
            'by_reg_time' => '',
            'by_last_login_time' => '',
            'by_dollar_amount' => '',
            'by_yuanbao_amount' => '',
            'reg_start_time' => '',
            'reg_end_time' => '',
            'pay_start_time' => '',
            'pay_end_time' => '',
            'last_login_time' => '',
            'yuanbao_amount' => '',
            'dollar_amount' => '',
            );

        $time_key = array(
            'reg_start_time',
            'reg_end_time',
            'pay_start_time',
            'pay_end_time',
            'last_login_time',
        );

        foreach ($filter_data as $key => $value) {
            if(in_array($key, $time_key)){
                $filter_data[$key] = strtotime(Input::get($key));
            }else{
                $filter_data[$key] = Input::get($key);
            }
        }

        $filter_data['game_id'] = $game_id;
        $filter_data['platform_id'] = $platform_id;

        $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $response = $slaveapi->filterOrders($filter_data);

        if(200 != $response->http_code){
            return $slaveapi->sendResponse();
        }

        $keys = array();
        if('all' == $filter_data['filter_type']){
            $keys = array(Lang::get('slave.recharge_number'), Lang::get('slave.recharge_count'), 
                Lang::get('slave.order_recharge_dollar'), Lang::get('slave.order_recharge_yuanbao'));
            $values = array();
            foreach ($response->body as $key => $value) {
                $tmp = array();
                foreach ($value as $k => $v) {
                    $tmp[] = $v;
                }
                $values[] = $tmp;
                unset($tmp);
            }
        }else{
            $keys = array('playerID', Lang::get('slave.server_name'),Lang::get('slave.last_login'),'UID', Lang::get('slave.recharge_count'), Lang::get('slave.order_sn'), 
                Lang::get('slave.order_recharge_dollar'), Lang::get('slave.order_recharge_yuanbao'));
            $values = array();
            foreach ($response->body as $key => $value) {
                $null = '-';
                $tmp = array();
                foreach ($value as $k => $v) {
                    if(is_null($v)){
                        $tmp[] = ($null.='-');
                    }else{
                        $tmp[] = $v;
                    }
                }
                $values[] = $tmp;
                unset($tmp);
            }
        }
        $result = array(
            'keys' => $keys,
            'values' => $values,
            );

        if(Input::get('download') || count($result['values']) > 5000){
            return $this->downloadOrderFilter($result);
        }
        return Response::json($result);
    }

    private function downloadOrderFilter($result){
        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";
        $csv = CSV::init($file, $result['keys']);
        $res = $csv->writeData(array());    //编码无法正常显示，加一个空行防止数据和列名出现混乱

        foreach ($result['values'] as $key => $value) {
            $res = $csv->writeData($value);
            unset($value);
        }
        $res = $csv->closeFile();
        $result = array(
            'now' => $now,
            );
        return Response::json($result);
    }
}