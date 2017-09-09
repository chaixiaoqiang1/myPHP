<?php

class SlaveApiUserController extends \BaseController {
    public function object2array(&$object) {
             $object =  json_decode( json_encode( $object),true);
             return  $object;
    }
    public function Sec2Time($time){
        if(is_numeric($time)){
        $value = array(
          "days" => 0, "hours" => 0,
          "minutes" => 0, "seconds" => 0,
        );
        if($time >= 86400){
          $value["days"] = floor($time/86400);
          $time = ($time%86400);
        }
        if($time >= 3600){
          $value["hours"] = floor($time/3600);
          $time = ($time%3600);
        }
        if($time >= 60){
          $value["minutes"] = floor($time/60);
          $time = ($time%60);
        }
        $value["seconds"] = floor($time);
        //return (array) $value;
        $t=$value["days"] ."天"." ". $value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
        Return $t;
        
         }else{
        return (bool) FALSE;
        }
    }
    public function weeklyReportIndex()
    {
        $game = Game::find(Session::get('game_id'));
        $data = array(
                'content' => View::make('slaveapi.user.weekly', array('game_type' => $game->game_type))
        );
        return View::make('main', $data);
    }

    public function weeklyReportData()
    {   
        $end_time = strtotime(Input::get('end_time'));
        $start_time = $end_time - 7 * 24 * 60 * 60 + 1;
        $filter_u1 = Input::get('filter_u1');
        $short_u1 = Input::get('short_u1');
        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $other_list = array(
                '-1' => 1,
                '' => 1,
                '0' => 1,
                'fb_app' => 1,
                'not_set' => 1,
                'pr' => 1
        );

        function cmp_u1($a, $b)
        {
            $ret = strcmp($a->source, $b->source);
            if ($ret == 0){
                if(isset($a->u1) && isset($b->u1)){
                    return strcmp($a->u1, $b->u1);
                }else{
                    return $ret;
                }
            }else{
                return $ret;
            }
        }
        
        // register data part
        $user_counts = array();
        $server_start_time = $start_time - 15 * 60 * 60;
        $server_end_time = $end_time;

        $register_response = $api->getWeeklyStat($platform_id, $game->game_id,
                $server_start_time, $server_end_time, $start_time, $end_time, $game->game_type, $filter_u1);

        if(2 == $game->game_type){
            $setup_response = $api->getWeeklySetup($platform_id, $game->game_id, $start_time, $end_time, $filter_u1);
            $setup_body = $setup_response->body;
        }

        $body = $register_response->body;
        if ($register_response->http_code != 200){
            return Response::json($register_response->body, $register_response->http_code);
        }

        if(1 == $game->game_type){  //页游
            $other_register = new stdClass();
            $other_register->source ='Other';
            $other_register->u1 = null;
            $other_register->count_formal =0;
            $other_register->count_player =0;

            $total_register = new stdClass();
            $total_register->source ='Total';
            $total_register->u1 = null;
            $total_register->count_formal = 0;
            $total_register->count_player =0;
            
            foreach ($body as $k => $v)
            {
                $total_register->count_formal += $v->count_formal;
                $total_register->count_player += $v->count_player;
                if (! array_key_exists($v->source, $other_list)) {
                    $user_counts[] = $v;
                } else {
                    $other_register->count_formal += $v->count_formal;
                    $other_register->count_player += $v->count_player;
                }
            } 
           
            if (sizeof($user_counts) > 0)
                usort($user_counts, 'cmp_u1');
            $user_counts[] = $other_register;
            $user_counts[] = $total_register;
        }elseif(2 == $game->game_type){ //手游
            $total_register = new stdClass();
            $total_register->os_type ='Total';
            $total_register->source = '-';
            $total_register->u1 = '-';
            $total_register->count_formal = 0;
            $total_register->count_player =0;
            $total_register->count_device =0;

            $total_register_android = new stdClass();
            $total_register_android->os_type ='Android Total';
            $total_register_android->source = '-';
            $total_register_android->u1 = '-';
            $total_register_android->count_formal = 0;
            $total_register_android->count_player =0;
            $total_register_android->count_device =0;

            $total_register_iOS = new stdClass();
            $total_register_iOS->os_type ='iOS Total';
            $total_register_iOS->source = '-';
            $total_register_iOS->u1 = '-';
            $total_register_iOS->count_formal = 0;
            $total_register_iOS->count_player =0;
            $total_register_iOS->count_device =0;

            $total_register_unknown = new stdClass();
            $total_register_unknown->os_type ='Unknown Total';
            $total_register_unknown->source = '-';
            $total_register_unknown->u1 = '-';
            $total_register_unknown->count_formal = 0;
            $total_register_unknown->count_player =0;
            $total_register_unknown->count_device =0;

            $total_register_android_other = new stdClass();
            $total_register_android_other->os_type ='Android Other';
            $total_register_android_other->source = '-';
            $total_register_android_other->u1 = '-';
            $total_register_android_other->count_formal = 0;
            $total_register_android_other->count_player =0;
            $total_register_android_other->count_device =0;

            $total_register_iOS_other = new stdClass();
            $total_register_iOS_other->os_type ='iOS Other';
            $total_register_iOS_other->source = '-';
            $total_register_iOS_other->u1 = '-';
            $total_register_iOS_other->count_formal = 0;
            $total_register_iOS_other->count_player =0;
            $total_register_iOS_other->count_device =0;

            $total_register_unknown_other = new stdClass();
            $total_register_unknown_other->os_type ='Unknown Other';
            $total_register_unknown_other->source = '-';
            $total_register_unknown_other->u1 = '-';
            $total_register_unknown_other->count_formal = 0;
            $total_register_unknown_other->count_player =0;
            $total_register_unknown_other->count_device =0;

            foreach ($setup_body as $setup) {
                //总量处理，独立部分，不影响其他数据
                $total_register->count_device += $setup->count_device;

                //根据设备统计各自的总量，不影响其他数据
                switch ($setup->os_type) {
                    case 'android':
                        $total_register_android->count_device += $setup->count_device;
                        break;
                    case 'iOS':
                        $total_register_iOS->count_device += $setup->count_device;
                        break;
                    default:
                        $total_register_unknown->count_device += $setup->count_device;
                        break;
                }

                //对每一项的处理
                if(array_key_exists($setup->source, $other_list)){
                    switch ($setup->os_type) {
                        case 'android':
                            $total_register_android_other->count_device += $setup->count_device;
                            break;
                        case 'iOS':
                            $total_register_iOS_other->count_device += $setup->count_device;
                            break;
                        default:
                            $total_register_unknown_other->count_device += $setup->count_device;
                            break;
                    }
                }else{
                    if($short_u1 && ('Facebook Ads' == $setup->source)){    //fb广告进行一定的合并和展开
                        $key_u1 = isset($setup->u1) ? substr($setup->u1, 0, 6) : '-';
                    }elseif($short_u1){  ///其他的不区分到u1
                        $key_u1 = '-';
                    }else{  //若不是short_u1那么则取完整的
                        $key_u1 = isset($setup->u1) ? $setup->u1 : '-';
                    }
                    $key_u1 = mb_convert_encoding($key_u1, "UTF-8");
                    $key = (isset($setup->os_type) ? $setup->os_type : '-') . 
                    (isset($setup->source) ? $setup->source : '-') . $key_u1;
                    if(array_key_exists($key, $user_counts)){   //因为如果选择合并查询并且fbad截取前六位u1可能出现重复的情况，此时应该加起来
                        $user_counts[$key]->count_device += $setup->count_device;
                    }else{
                        $setup->u1 = $key_u1;
                        $setup->count_player = 0;
                        $setup->count_formal = 0;
                        $user_counts[$key] = $setup;
                    }
                }
            }
            foreach ($body as $v)
            {
                //总量处理，独立部分，不影响其他数据
                $total_register->count_formal += $v->count_formal;
                $total_register->count_player += $v->count_player;

                //根据设备统计各自的总量，不影响其他数据
                switch ($v->os_type) {
                    case 'android':
                        $total_register_android->count_formal += $v->count_formal;
                        $total_register_android->count_player += $v->count_player;
                        break;
                    case 'iOS':
                        $total_register_iOS->count_formal += $v->count_formal;
                        $total_register_iOS->count_player += $v->count_player;
                        break;
                    default:
                        $total_register_unknown->count_formal += $v->count_formal;
                        $total_register_unknown->count_player += $v->count_player;
                        break;
                }

                //对每一项的处理
                if(array_key_exists($v->source, $other_list)){  //杂乱小的source统计到other里面
                    switch ($v->os_type) {
                        case 'android':
                            $total_register_android_other->count_formal += $v->count_formal;
                            $total_register_android_other->count_player += $v->count_player;
                            break;
                        case 'iOS':
                            $total_register_iOS_other->count_formal += $v->count_formal;
                            $total_register_iOS_other->count_player += $v->count_player;
                            break;
                        default:
                            $total_register_unknown_other->count_formal += $v->count_formal;
                            $total_register_unknown_other->count_player += $v->count_player;
                            break;
                    }
                }else{
                    if($short_u1 && ('Facebook Ads' == $v->source)){    //对fb广告按照u1前六位合并显示
                        $key_u1 = isset($v->u1) ? substr($v->u1, 0, 6) : '-';
                    }elseif($short_u1){ //非fb的不显示到u1
                        $key_u1 = '-';
                    }else{  //若不按照合并地区查询则取完整值
                        $key_u1 = isset($v->u1) ? $v->u1 : '-';
                    }
                    $key_u1 = mb_convert_encoding($key_u1, "UTF-8");
                    $key = (isset($v->os_type) ? $v->os_type : '-') . 
                    (isset($v->source) ? $v->source : '-') . $key_u1;
                    if(array_key_exists($key, $user_counts)){
                        $user_counts[$key]->count_formal += $v->count_formal;
                        $user_counts[$key]->count_player += $v->count_player;
                    }else{
                        $v->u1 = $key_u1;
                        $v->count_device = 0;
                        $user_counts[$key] = $v;
                    }
                }
                
            }
            if (sizeof($user_counts) > 0)
                usort($user_counts, 'cmp_u1');
            if($total_register_android_other->count_formal > 0)
                $user_counts[] = $total_register_android_other;
            if($total_register_iOS_other->count_formal > 0)
                $user_counts[] = $total_register_iOS_other;
            if($total_register_unknown_other->count_formal > 0)
                $user_counts[] = $total_register_unknown_other;
            if($total_register_android->count_formal > 0)
                $user_counts[] = $total_register_android;
            if($total_register_iOS->count_formal > 0)
                $user_counts[] = $total_register_iOS;
            if($total_register_unknown->count_formal > 0)
                $user_counts[] = $total_register_unknown;
            if($total_register->count_formal > 0)
                $user_counts[] = $total_register;
        }
        // channel part
        if(2 == $game->game_type){
            $channel_msg = array();
            for($i = 1; $i <= 8; $i ++){
                $cre_start_time = $end_time - $i * 7 * 24 * 60 * 60 + 1;
                $cre_end_time = $cre_start_time + 7 * 24 * 60 * 60;
                $channle_order_start_time = $cre_start_time;
                $channle_order_end_time = $end_time;

                $response = $api->getWeeklyChannelStat($platform_id, $game->game_id, $cre_start_time,
                    $cre_end_time, $channle_order_start_time, $channle_order_end_time);
                if(77 == $game->game_id){
                    Log::info('weekly---test----Panda-----po2---'.var_export($response, true));
                }
                if($response->http_code == 404){
                    $channel_msg_temp = array();
                }elseif($response->http_code != 200){
                    return Response::json($response->body, $response->http_code);
                }else{
                    $channel_msg_temp = $response->body;
                }
                $channel_msg[] = array(
                        'title' => Lang::get('slave.weekly_channel', 
                                array(
                                        'num' => $i
                                )),
                        'data' => $channel_msg_temp
                );
            }
        }else{
            $channel_msg = array();
        }
        // recharge part
        $pay_statics = array();
        $params = array(
                'filter' => 'u1',
                'currency_id' => $platform->default_currency_id
        );
        for ($i = 1; $i <= 8; $i ++)
        {
            $reg_start_time = $end_time - $i * 7 * 24 * 60 * 60 + 1;
            $reg_end_time = $reg_start_time + 7 * 24 * 60 * 60;
            $order_start_time = $reg_start_time;
            $order_end_time = $end_time;
            
            $params['reg_start_time'] = $reg_start_time;
            $params['reg_end_time'] = $reg_end_time;
            $params['order_start_time'] = $order_start_time;
            $params['order_end_time'] = $order_end_time;
            $params['game_id'] = $game->game_id;
            $params['game_type'] = $game->game_type;

            $response = $api->getChannelOrderStat($platform_id, $params);

            if ($response->http_code != 200)
            {
                return Response::json($response->body, $response->http_code);
            }
            
            $week_statics = array();
            
            if(1 == $game->game_type){
                $other_order = new stdClass();
                $other_order->source = 'Other';
                $other_order->u1 = '-';
                $other_order->pay_user_count = 0;
                $other_order->total_dollar_amount = 0;
                foreach ($response->body as $k => $v)
                {
                    if ($v->source == 'Total')
                        continue;
                    if (! array_key_exists($v->source, $other_list))
                    {
                        $week_statics[] = $v;
                    } else
                    {
                        $other_order->pay_user_count += $v->pay_user_count;
                        $other_order->total_dollar_amount += $v->total_dollar_amount;
                    }
                }
                
                if (sizeof($week_statics) > 0)
                    usort($week_statics, 'cmp_u1');
                $week_statics[] = $other_order;
                $week_statics[] = $response->body[0]; // assume the first one is total
            }elseif(2 == $game->game_type){
                $other_order_android = new stdClass();
                $other_order_android->os_type = 'Android-OtherSource';
                $other_order_android->source = '-';
                $other_order_android->u1 = '-';
                $other_order_android->pay_user_count = 0;
                $other_order_android->total_dollar_amount = 0;

                $other_order_iOS = new stdClass();
                $other_order_iOS->os_type = 'iOS-OtherSource';
                $other_order_iOS->source = '-';
                $other_order_iOS->u1 = '-';
                $other_order_iOS->pay_user_count = 0;
                $other_order_iOS->total_dollar_amount = 0;

                $other_order_unknown = new stdClass();
                $other_order_unknown->os_type = 'Unknown-OtherSource';
                $other_order_unknown->source = '-';
                $other_order_unknown->u1 = '-';
                $other_order_unknown->pay_user_count = 0;
                $other_order_unknown->total_dollar_amount = 0;

                $order_android = new stdClass();
                $order_android->os_type = 'Android-Total';
                $order_android->source = '-';
                $order_android->u1 = '-';
                $order_android->pay_user_count = 0;
                $order_android->total_dollar_amount = 0;

                $order_iOS = new stdClass();
                $order_iOS->os_type = 'iOS-Total';
                $order_iOS->source = '-';
                $order_iOS->u1 = '-';
                $order_iOS->pay_user_count = 0;
                $order_iOS->total_dollar_amount = 0;

                $order_unknown = new stdClass();
                $order_unknown->os_type = 'Unknown-Total';
                $order_unknown->source = '-';
                $order_unknown->u1 = '-';
                $order_unknown->pay_user_count = 0;
                $order_unknown->total_dollar_amount = 0;

                $all_source_ostype = array();
                $fb_ads_merge = array();
                foreach ($response->body as $k => $v)
                {
                    if ($v->os_type == 'Total')
                        continue;
                    //统计各种设备的充值总额信息
                    switch ($v->os_type) {
                        case 'android':
                            $order_android->pay_user_count += $v->pay_user_count;
                            $order_android->total_dollar_amount += $v->total_dollar_amount;
                            break;
                        case 'iOS':
                            $order_iOS->pay_user_count += $v->pay_user_count;
                            $order_iOS->total_dollar_amount += $v->total_dollar_amount;
                            break;
                        default:
                            $order_unknown->pay_user_count += $v->pay_user_count;
                            $order_unknown->total_dollar_amount += $v->total_dollar_amount;
                            break;
                    }
                    //这里统计的是一些杂乱小source的数据，区分他们的设备
                    if(array_key_exists($v->source, $other_list)){
                        switch ($v->os_type) {
                            case 'android':
                                $other_order_android->pay_user_count += $v->pay_user_count;
                                $other_order_android->total_dollar_amount += $v->total_dollar_amount;
                                break;
                            case 'iOS':
                                $other_order_iOS->pay_user_count += $v->pay_user_count;
                                $other_order_iOS->total_dollar_amount += $v->total_dollar_amount;
                                break;
                            default:
                                $other_order_unknown->pay_user_count += $v->pay_user_count;
                                $other_order_unknown->total_dollar_amount += $v->total_dollar_amount;
                                break;
                        }
                    }else{
                        if($filter_u1 && !$short_u1){ //如果需要筛选到u1，并且没有选择合并查询，需要把每一项都统计进去
                            $week_statics[] = $v;
                        }

                        //如果合并地区查询，需要把fb的数据拿出来整理一下
                        if($short_u1 && "Facebook Ads" == $v->source){
                            $key_u1 = isset($v->u1) ? substr($v->u1, 0, 6) : '-';
                            $key_u1 = mb_convert_encoding($key_u1, "UTF-8");
                            $key_fb = (isset($v->os_type) ? $v->os_type : '-').(isset($v->source) ? $v->source : '-').$key_u1;
                            if(array_key_exists($key_fb, $fb_ads_merge)){
                                $fb_ads_merge[$key_fb]->pay_user_count += $v->pay_user_count;
                                $fb_ads_merge[$key_fb]->total_dollar_amount += $v->total_dollar_amount;
                            }else{
                                $tmp = new stdClass();
                                $tmp->os_type = isset($v->os_type) ? $v->os_type : 'Unknown';
                                $tmp->source = isset($v->source) ? $v->source : '-';
                                $tmp->u1 = $key_u1;
                                $tmp->pay_user_count = $v->pay_user_count;
                                $tmp->total_dollar_amount = $v->total_dollar_amount;
                                $fb_ads_merge[$key_fb] = $tmp;
                                unset($tmp);
                            }
                        }

                        //=======下面的部分用来统计以source+os_tpye作为唯一区分的数据，不影响原有数据，会在结果中新增一些项=====
                        if(array_key_exists($v->source.'-'.$v->os_type, $all_source_ostype)){
                            $all_source_ostype[$v->source.'-'.$v->os_type]->pay_user_count += $v->pay_user_count;
                            $all_source_ostype[$v->source.'-'.$v->os_type]->total_dollar_amount += $v->total_dollar_amount;
                        }else{
                            $tmp = new stdClass();
                            $tmp->os_type = $v->os_type ? $v->os_type : 'Unknown';
                            $tmp->source = $v->source;
                            $tmp->u1 = '-';
                            $tmp->pay_user_count = $v->pay_user_count;
                            $tmp->total_dollar_amount = $v->total_dollar_amount;
                            $all_source_ostype[$v->source.'-'.$v->os_type] = $tmp;
                            unset($tmp);
                        }
                        //======================================================================================================
                    }
                }

                if (sizeof($week_statics) > 0)
                    usort($week_statics, 'cmp_u1');
                foreach ($fb_ads_merge as $value) {
                    $week_statics[] = $value;
                }
                if($other_order_android->pay_user_count > 0)
                    $week_statics[] = $other_order_android;
                if($other_order_iOS->pay_user_count > 0)
                    $week_statics[] = $other_order_iOS;
                if($other_order_unknown->pay_user_count > 0)
                    $week_statics[] = $other_order_unknown;
                foreach ($all_source_ostype as $value) {
                    $week_statics[] = $value;
                }
                if($order_android->pay_user_count > 0)
                    $week_statics[] = $order_android;
                if($order_iOS->pay_user_count > 0)
                    $week_statics[] = $order_iOS;
                if($order_unknown->pay_user_count > 0)
                    $week_statics[] = $order_unknown;
                $week_statics[] = $response->body[0];   //0这一项是总计
            }

            foreach ($week_statics as $key => $data)
            {
                $data->total_dollar_amount = number_format(
                        $data->total_dollar_amount, 2);
            }
            
            
            $pay_statics[] = array(
                    'title' => Lang::get('slave.weekly_report_title', 
                            array(
                                    'num' => $i
                            )),
                    'data' => $week_statics
            );
        }
        
        $data = array(
                'user_counts' => $user_counts,
                'pay_statics' => $pay_statics,
                'channel_counts' => $channel_msg,
        );
        return Response::json($data);
    }

    public function userStatIndex()
    {
        $servers = $this->getUnionServers();
        $data = array(
                'content' => View::make('slaveapi.user.userstat', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function sendUserStatData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $interval = (int) Input::get('interval');
        $filter_id = (int) Input::get('filtrate_id');
        $source = trim(Input::get('source'));
        $u1 = trim(Input::get('u1'));
        $u2 = trim(Input::get('u2'));
        $server_id = Input::get('server_id');
        $classify = (int)Input::get('look_type');
        if($server_id[0] == '0'){ //全服
            $servers = $this->getUnionServers();
            $temp = array();
            foreach ($servers as $value) {
                $temp[] = $value->server_id;
            }
            $server_id = $temp;
        }

        $interl_time = array(
                600,
                3600,
                86400,
                0
        );
        $filter_list = array(
                'source',
                'u1',
                'u2'
        );
        $game_id = Session::get('game_id'); 
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        if (count($server_id) == 1) {  //只选一个服务器
            $server = Server::find($server_id[0]);
            $user = array();
            $user['game_id'] = $game->game_id;
            if ($server_id[0] > 0)
            {
                $user['server_internal_id'] = $server->server_internal_id;
                $user['platform_server_id'] = $server->platform_server_id;
            } else
            {
                $user['server_internal_id'] = 0;
            }
            $user['platform_id'] = Session::get('platform_id');
            $user['start_time'] = $start_time;
            $user['end_time'] = $end_time;
            $user['interval'] = $interl_time[$interval];
            $user['filter'] = $filter_list[$filter_id];
            $user['source'] = $source;
            $user['u1'] = $u1;
            $user['u2'] = $u2;

             $response = $api->getUserStat($user,$classify);
             if ($response->http_code != 200)
             {
                 return Response::json($response->body, $response->http_code);
             }
             $body = $response->body;             
            
            $sum = array(
                    'sum_formal' => 0,
                    'sum_anonymous' => 0,
                    'sum_player_formal' => 0,
                    'sum_player_anonymous' => 0,
                    'sum_lev_formal' => 0,
                    'sum_lev_anonymous' => 0,
                    'sum_count_anonymous_formal' => 0
            );
            foreach ($body as $item)
            {
                if ($server_id[0] == 0)
                {
                    $item->count_player_formal = 0;
                    $item->count_player_anonymous = 0;
                    $item->count_lev_formal = 0;
                    $item->count_lev_anonymous = 0;
                }
                $sum['sum_formal'] += (int) isset($item->count_formal) ? $item->count_formal : 0;
                $sum['sum_anonymous'] += (int) isset($item->count_anonymous) ? $item->count_anonymous : 0;
                $sum['sum_player_formal'] += (int) isset($item->count_player_formal) ? $item->count_player_formal : 0;
                $sum['sum_player_anonymous'] += (int) isset($item->count_player_anonymous) ? $item->count_player_anonymous : 0;
                $sum['sum_lev_formal'] += (int) isset($item->count_lev_formal) ? $item->count_lev_formal : 0;
                $sum['sum_lev_anonymous'] += (int) isset($item->count_lev_anonymous) ? $item->count_lev_anonymous : 0;
                $sum['sum_count_anonymous_formal'] += (int) isset($item->count_anonymous_formal) ? $item->count_anonymous_formal : 0;
                if ($interval == 3)
                {
                    $item->end_time = date('Y-m-d H:i:s', $end_time);
                    $item->ctime = date('Y-m-d H:i:s', $start_time);
                } else
                {
                    $item->end_time = $interl_time[$interval] + (int) $item->ctime;
                    $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                    $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                }
            }
            $statdata = array();
            
            $blank = new stdClass();
            $blank->ctime = null;
            $blank->end_time = null;
            $blank->source = null;
            $blank->u1 = null;
            $blank->u2 = null;
            $blank->count_formal = null;
            $blank->count_anonymous = null;
            $blank->count_anonymous_formal = null;
            $blank->count_player_formal = null;
            $blank->count_player_anonymous = null;
            $blank->count_lev_formal = null;
            $blank->count_lev_anonymous = null;
            
            for ($i = 0; $i < sizeof($body); $i ++)
            {
                if ($i > 0 && $body[$i]->ctime != $body[$i - 1]->ctime)
                {
                    $statdata[] = $blank;
                }
                $statdata[] = $body[$i];
            }

            $data = array(
                    'items' => $statdata,
                    'sum' => $sum
            );
            
            return Response::json($data);
            
        } else {
            $len = count($server_id);
            //
            $server1 = Server::find($server_id[0]);
            $use = array();
            $use['game_id'] = $game->game_id;
            if ($server_id[0] > 0)
            {
                $use['server_internal_id'] = $server1->server_internal_id;
                $use['platform_server_id'] = $server1->platform_server_id;
            } else
            {
                $use['server_internal_id'] = 0;
            }
            $use['platform_id'] = Session::get('platform_id');
            $use['start_time'] = $start_time;
            $use['end_time'] = $end_time;
            $use['interval'] = $interl_time[$interval];
            $use['filter'] = $filter_list[$filter_id];
            $use['source'] = $source;
            $use['u1'] = $u1;
            $use['u2'] = $u2;
            $resp = $api->getUserStat($use, $classify);
            if ($resp->http_code != 200)
            {
                return Response::json($resp->body, $resp->http_code);
            }
            $bod = $resp->body;
            //
            for($k = 0; $k <count($bod); $k ++){
                $arr[$k] = array(
                    'a' => 0,
                    'b' =>0,
                    'c' =>0,
                    'd' =>0
                );
            }

            $sum = array(
                        'sum_formal' => 0,
                        'sum_anonymous' => 0,
                        'sum_player_formal' => 0,
                        'sum_player_anonymous' => 0,
                        'sum_lev_formal' => 0,
                        'sum_lev_anonymous' => 0,
                        'sum_count_anonymous_formal' => 0
                ); 
           

            for ($i=0; $i < $len; $i++) {
                $server[$i] = Server::find($server_id[$i]); 
                $user[$i] = array();
                $user[$i]['game_id'] = $game->game_id;
                if ($server_id[$i] > 0) {
                    $user[$i]['server_internal_id'] = $server[$i]->server_internal_id;
                    $user[$i]['platform_server_id'] = $server[$i]->platform_server_id;
                } else {
                    $user[$i]['server_internal_id'] = 0;
                }
                $user[$i]['platform_id'] = Session::get('platform_id');
                $user[$i]['start_time'] = $start_time;
                $user[$i]['end_time'] = $end_time;
                $user[$i]['interval'] = $interl_time[$interval];
                $user[$i]['filter'] = $filter_list[$filter_id];
                $user[$i]['source'] = $source;
                $user[$i]['u1'] = $u1;
                $user[$i]['u2'] = $u2;
                $response[$i] = $api->getUserStat($user[$i], $classify);
                if ($response[$i]->http_code != 200) {
                    return Response::json($response[$i]->body, $response[$i]->http_code);
                }
                $body[$i] = $response[$i]->body;
               // var_dump($body[$i]);
                //die();
                foreach ($body[$i] as  $item) {
                    
                    if ($server_id[$i] == 0) {
                        $item->count_player_formal = 0;
                        $item->count_player_anonymous = 0;
                        $item->count_lev_formal = 0;
                        $item->count_lev_anonymous = 0;
                    }
                    if ($i == 0) {
                        $sum['sum_formal'] += (int) isset($item->count_formal) ? $item->count_formal : 0;
                        $sum['sum_anonymous'] += (int) isset($item->count_anonymous) ? $item->count_anonymous : 0;
                        $sum['sum_count_anonymous_formal'] += (int) isset($item->count_anonymous_formal) ? $item->count_anonymous_formal : 0;
                    }
                    $sum['sum_player_anonymous'] += (int) isset($item->count_player_anonymous) ? $item->count_player_anonymous : 0;
                    $sum['sum_lev_formal'] += (int) isset($item->count_lev_formal) ? $item->count_lev_formal : 0;
                    $sum['sum_lev_anonymous'] += (int) isset($item->count_lev_anonymous) ? $item->count_lev_anonymous : 0;
                    $sum['sum_player_formal'] += (int) isset($item->count_player_formal) ? $item->count_player_formal : 0;
                    //Log::info('count_player_anonymous:'.(int) $item->count_player_anonymous.'count_player_formal:'.(int) $item->count_player_formal.'sum_player_anonymous:'.$sum['sum_player_anonymous'].'sum_player_formal:'.$sum['sum_player_formal']);
                    if ($interval == 3) {
                        $item->end_time = date('Y-m-d H:i:s', $end_time);
                        $item->ctime = date('Y-m-d H:i:s', $start_time);
                    } else {
                        $item->end_time = $interl_time[$interval] + (int) $item->ctime;
                        $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                        $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                    }
                }
                $statdata = array();
                
                $blank = new stdClass();
                $blank->ctime = null;
                $blank->end_time = null;
                $blank->source = null;
                $blank->u1 = null;
                $blank->u2 = null;
                $blank->count_formal = null;
                $blank->count_anonymous = null;
                $blank->count_anonymous_formal = null;
                $blank->count_player_formal = null;
                $blank->count_player_anonymous = null;
                $blank->count_lev_formal = null;
                $blank->count_lev_anonymous = null;
            
                for ($j = 0; $j < sizeof($body[$i]); $j ++) {
                    if ($j > 0 && $body[$i][$j]->ctime != $body[$i][$j - 1]->ctime) {
                        $statdata[$j] = $blank;
                    }

                    $statdata[$j] = new stdClass();
                    $statdata[$j]->ctime = 0;
                    $statdata[$j]->end_time= 0;
                    $statdata[$j]->source = 0;
                    $statdata[$j]->u1= '';
                    $statdata[$j]->u2= '';
                    $statdata[$j]->count_formal = 0;
                    $statdata[$j]->count_anonymous = 0;
                    $statdata[$j]->count_anonymous_formal =0;
                    
                    $statdata[$j]->ctime = isset($body[$i][$j]->ctime) ? $body[$i][$j]->ctime : '';
                    $statdata[$j]->end_time = isset($body[$i][$j]->end_time) ? $body[$i][$j]->end_time : '';
                    $statdata[$j]->source = isset($body[$i][$j]->source) ? $body[$i][$j]->source : '';
                    $statdata[$j]->u1 = isset($body[$i][$j]->u1) ? $body[$i][$j]->u1 : '';
                    $statdata[$j]->u2 = isset($body[$i][$j]->u2) ? $body[$i][$j]->u2 :'';
                    
                    $statdata[$j]->count_formal = isset($body[$i][$j]->count_formal) ? $body[$i][$j]->count_formal : 0;
                    $statdata[$j]->count_anonymous = isset($body[$i][$j]->count_anonymous) ? $body[$i][$j]->count_anonymous : 0;
                    $statdata[$j]->count_anonymous_formal = isset($body[$i][$j]->count_anonymous_formal) ? $body[$i][$j]->count_anonymous_formal : 0;
                    
                    if(!isset($arr[$j]['a'])){
                        $arr[$j] = array(
                            'a' => 0,
                            'b' =>0,
                            'c' =>0,
                            'd' =>0
                            );
                    }
                    $arr[$j]['a']+= isset($body[$i][$j]->count_player_formal) ? $body[$i][$j]->count_player_formal : 0;
                    $arr[$j]['b']+= isset($body[$i][$j]->count_player_anonymous) ? $body[$i][$j]->count_player_anonymous : 0;
                    $arr[$j]['c']+= isset($body[$i][$j]->count_lev_formal) ? $body[$i][$j]->count_lev_formal : 0;
                    $arr[$j]['d']+= isset($body[$i][$j]->count_lev_anonymous) ? $body[$i][$j]->count_lev_anonymous : 0;
                }
            }
            for($k = 0; $k< count($statdata); $k++) {

                $statdata[$k]->count_player_formal = 0;
                $statdata[$k]->count_player_anonymous= 0;
                $statdata[$k]->count_lev_formal = 0;
                $statdata[$k]->count_lev_anonymous = 0;

                $statdata[$k]->count_player_formal = $arr[$k]['a'];
                $statdata[$k]->count_player_anonymous = $arr[$k]['b'];
                $statdata[$k]->count_lev_formal = $arr[$k]['c'];
                $statdata[$k]->count_lev_anonymous = $arr[$k]['d'];

            }
            $data = array(
                'items' => $statdata,
                'sum' => $sum
            );

            if (isset($data)) {
                return Response::json($data);
            } else {
                return Response::json('error','error');
            }
        }
    }

    public function userStatIndextest(){    //注册用户统计换个方式统计，个人认为会提高查询成功率--Panda
        $servers = $this->getUnionServers();
        $game = Game::find(Session::get('game_id'));
        $data = array(
                'content' => View::make('slaveapi.user.userstattest', 
                        array(
                                'servers' => $servers,
                                'game_type' => $game->game_type
                        ))
        );
        return View::make('main', $data);
    }

    public function sendUserStatDatatest(){
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = $this->current_time_nodst(strtotime(Input::get('start_time')));
        $end_time = $this->current_time_nodst(strtotime(Input::get('end_time')));
        $interval = (int) Input::get('interval');
        $filter_id = (int) Input::get('filtrate_id');
        $source = trim(Input::get('source'));
        $u1 = trim(Input::get('u1'));
        $u2 = trim(Input::get('u2'));
        $server_id = Input::get('server_id');
        if($server_id[0] == '0'){ //全服
            $servers = $this->getUnionServers();
            $temp = array();
            foreach ($servers as $value) {
                $temp[] = $value->server_id;
            }
            $server_id = $temp;
        }

        $interl_time = array(
                600,
                3600,
                86400,
                0
        );
        $filter_list = array(
                'source',
                'u1',
                'u2'
        );

        $game_id = Session::get('game_id'); 
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $users = array(
            'start_time' => $start_time,
            'end_time' => $end_time,
            'interval' => $interl_time[$interval],
            'filter' => $filter_list[$filter_id],
            'source' => $source,
            'u1' => $u1,
            'u2' => $u2,
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            );
        if($u2){
            $users['filter'] = 'u2';
        }
        if($u1 &&  'u2' != $users['filter']){
            $users['filter'] = 'u1';
        }

        $sum = array(
            'sum_formal' => 0,
            'sum_anonymous' => 0,
            'sum_count_anonymous_formal' => 0,
            'sum_player_formal' => 0,
            'sum_player_anonymous' => 0,
            'sum_player_anonymous_formal' => 0,
            'sum_lev_formal' => 0,
            'sum_lev_anonymous' => 0,
            'sum_lev_anonymous_formal' => 0,
            );
        $details = array();
        $all_keys = array(
            'sum_formal' => 'count_formal', 
            'sum_anonymous' => 'count_anonymous', 
            'sum_count_anonymous_formal' => 'count_anonymous_formal', 
            'sum_player_formal' => 'count_player_formal', 
            'sum_player_anonymous' => 'count_player_anonymous', 
            'sum_player_anonymous_formal' => 'count_player_anonymous_formal',
            'sum_lev_formal' => 'count_lev_formal', 
            'sum_lev_anonymous' => 'count_lev_anonymous', 
            'sum_lev_anonymous_formal' => 'count_lev_anonymous_formal',
            );
        $signupjustcountonce = 0;
        foreach ($server_id as $singleserver_id) {
            $server = Server::find($singleserver_id);
            if(!$server){
                continue;
            }
            $result_singleserver = array();
            $users['server_internal_id'] = $server->server_internal_id;

            $temp_sign = $api->getUserStatSignupinfo($users);
            if('200' == $temp_sign->http_code){
                foreach ($temp_sign->body as $value) {
                    $key = (isset($value->ctime) ? $value->ctime : '-').
                            $value->source.
                            (isset($value->u1) ? $value->u1 : '-').
                            (isset($value->u2) ? $value->u2 : '-');
                    $result_singleserver[$key] = array(
                        'ctime' => isset($value->ctime) ? date('Y-m-d H:i:s', $value->ctime) : date('Y-m-d H:i:s', $start_time),
                        'end_time' => isset($value->ctime) ? date('Y-m-d H:i:s', ($value->ctime + $users['interval'])) : date('Y-m-d H:i:s', $end_time),
                        'source' => $value->source,
                        'u1' => isset($value->u1) ? $value->u1 : '',
                        'u2' => isset($value->u2) ? $value->u2 : '',
                        'count_formal' => $value->all_signup - $value->anonymous_signup,
                        'count_anonymous' => $value->anonymous_signup,
                        'count_anonymous_formal' => $value->anonymous_signup - $value->still_anonymous_signup,
                        'count_player_formal' => 0,
                        'count_player_anonymous' => 0,
                        'count_player_anonymous_formal' => 0,
                        'count_lev_formal' => 0,
                        'count_lev_anonymous' => 0,
                        'count_lev_anonymous_formal' => 0,
                        );
                }
            }elseif($temp_sign->http_code >= '500'){
                return Response::json(array('error'=> Lang::get('slave.bad_return')), 403);
            }
            unset($temp_sign);
            $temp_create = $api->getUserStatCreateplayerinfo($users);
            if('200' == $temp_create->http_code){
                foreach ($temp_create->body as $value) {
                    $key = (isset($value->ctime) ? $value->ctime : '-').
                            $value->source.
                            (isset($value->u1) ? $value->u1 : '-').
                            (isset($value->u2) ? $value->u2 : '-');
                    if(isset($result_singleserver[$key])){
                        $result_singleserver[$key]['count_player_formal'] = $value->all_create - $value->anonymous_create;
                        $result_singleserver[$key]['count_player_anonymous'] = $value->anonymous_create;
                        $result_singleserver[$key]['count_player_anonymous_formal'] = $value->anonymous_create - $value->still_anonymous_create;
                    }else{  //为了逻辑严谨，这里理论上执行不到
                        $result_singleserver[$key] = array(
                            'ctime' => isset($value->ctime) ? date('Y-m-d H:i:s', $value->ctime) : '',
                            'end_time' => isset($value->ctime) ? date('Y-m-d H:i:s', ($value->ctime + $users['interval'])) : '',
                            'source' => $value->source,
                            'u1' => isset($value->u1) ? $value->u1 : '',
                            'u2' => isset($value->u2) ? $value->u2 : '',
                            'count_formal' => 0,
                            'count_anonymous' => 0,
                            'count_anonymous_formal' => 0,
                            'count_player_formal' => $value->all_create - $value->anonymous_create,
                            'count_player_anonymous' => $value->anonymous_create,
                            'count_player_anonymous_formal' => $value->anonymous_create - $value->still_anonymous_create,
                            'count_lev_formal' => 0,
                            'count_lev_anonymous' => 0,
                            'count_lev_anonymous_formal' => 0,
                            );
                    }
                }
            }elseif($temp_create->http_code >= '500'){
                return Response::json(array('error'=> Lang::get('slave.bad_return')), 403);
            }
            unset($temp_create);
            $temp_levelten = $api->getUserStatLevelteninfo($users);
            if('200' == $temp_levelten->http_code){
                foreach ($temp_levelten->body as $value) {
                    $key = (isset($value->ctime) ? $value->ctime : '-').
                            $value->source.
                            (isset($value->u1) ? $value->u1 : '-').
                            (isset($value->u2) ? $value->u2 : '-');
                    if(isset($result_singleserver[$key])){
                        $result_singleserver[$key]['count_lev_formal'] = $value->all_levelten - $value->anonymous_levelten;
                        $result_singleserver[$key]['count_lev_anonymous'] = $value->anonymous_levelten;
                        $result_singleserver[$key]['count_lev_anonymous_formal'] = $value->anonymous_levelten - $value->still_anonymous_levelten;
                    }else{  //为了逻辑严谨，这里理论上执行不到
                        $result_singleserver[$key] = array(
                            'ctime' => isset($value->ctime) ? date('Y-m-d H:i:s', $value->ctime) : '',
                            'end_time' => isset($value->ctime) ? date('Y-m-d H:i:s', ($value->ctime + $users['interval'])) : '',
                            'source' => $value->source,
                            'u1' => isset($value->u1) ? $value->u1 : '',
                            'u2' => isset($value->u2) ? $value->u2 : '',
                            'count_formal' => 0,
                            'count_anonymous' => 0,
                            'count_anonymous_formal' => 0,
                            'count_player_formal' => 0,
                            'count_player_anonymous' => 0,
                            'count_player_anonymous_formal' => 0,
                            'count_lev_formal' => $value->all_levelten - $value->anonymous_levelten,
                            'count_lev_anonymous' => $value->anonymous_levelten,
                            'count_lev_anonymous_formal' => $value->anonymous_levelten - $value->still_anonymous_levelten,
                            );
                    }
                }
            }elseif($temp_levelten->http_code >= '500'){
                return Response::json(array('error'=> Lang::get('slave.bad_return')), 403);
            }
            unset($temp_levelten);

            unset($users['server_internal_id']);
            unset($server);
            foreach ($result_singleserver as $key => $value) {  //把多个服务器的数据合并到一起
                foreach ($all_keys as $single_key => $single_value) {
                    if (in_array($single_key, array('sum_formal', 'sum_anonymous', 'sum_count_anonymous_formal'))) {    //这几个值不区分服务器查的，因此不需要重复累加
                        if('0' == $signupjustcountonce){
                            $sum[$single_key] += isset($value[$single_value]) ? $value[$single_value] : 0;
                        }
                    }else{
                        $sum[$single_key] += isset($value[$single_value]) ? $value[$single_value] : 0;
                    }
                    unset($single_key);
                    unset($single_value);
                }
                if(isset($details[$key])){
                    foreach ($all_keys as $single_key) {
                        if(!in_array($single_key, array('count_formal', 'count_anonymous', 'count_anonymous_formal'))){ //因为是不区分服务器的，因此只赋值不累加
                            $details[$key][$single_key] += $result_singleserver[$key][$single_key];
                        }
                        unset($single_key);
                    }
                }else{
                    $details[$key] = $result_singleserver[$key];
                }
            }
            $signupjustcountonce++;
            unset($result_singleserver);
        }

        $count = 0;
        $tmp_result = $details;
        unset($details);
        $details = array();
        foreach ($tmp_result as $value) {   //显式声明下标让页面按一定顺序显式
            $details[$count++] = $value;
        }
        unset($tmp_result);
        $data = array(
            'sum' => $sum,
            'items' => $details,
            );
            
        return Response::json($data);
    }

    public function SetupStatIndexForYY(){
        return $this->SetupStatIndex($is_yy = 1);
    }

    public function sendSetupStatDataForYY(){
        return $this->sendSetupStatData();
    }

    public function SetupStatIndex($is_yy = 0){    //安装用户统计--类似注册用户统计，不过users表换成了device_list表
        $servers = $this->getUnionServers();
        $data = array(
                'content' => View::make('slaveapi.user.setupstat', 
                        array(
                                'servers' => $servers,
                                'is_yy' => $is_yy,
                        ))
        );
        return View::make('main', $data);
    }

    public function sendSetupStatData(){
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $interval = (int) Input::get('interval');
        $filter_id = (int) Input::get('filtrate_id');
        $source = trim(Input::get('source'));
        $u1 = trim(Input::get('u1'));
        $u2 = trim(Input::get('u2'));
        $server_id = Input::get('server_id');
        $os_type = Input::get('os_type');
        if($server_id[0] == '0'){ //全服
            $servers = $this->getUnionServers();
            $server_id = array();
            foreach ($servers as $value) {
                $server_id[] = $value->server_id;
            }
        }

        $interl_time = array(
                600,
                3600,
                86400,
                0
        );
        $filter_list = array(
            0 => '',
            1 => 'source',
            2 => 'u1',
            3 => 'u2',
        );

        $game_id = Session::get('game_id'); 
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        if('2' != $game->game_type){
            return Response::json(array('error'=> 'Not a moblie game'), 403);
        }
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $users = array(
            'start_time' => $start_time,
            'end_time' => $end_time,
            'interval' => $interl_time[$interval],
            'filter' => $filter_list[$filter_id],
            'source' => $source,
            'u1' => $u1,
            'u2' => $u2,
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'os_type' => $os_type,
            );
        if($u2){
            $users['filter'] = 'u2';
        }
        if($u1 &&  'u2' != $users['filter']){
            $users['filter'] = 'u1';
        }

        $sum = array(
            'sum_device' => 0,
            'sum_device_create' => 0,
            'sum_device_create_lev10' => 0,
            );
        $details = array();
        foreach ($server_id as $singleserver_id) {
            $server = Server::find($singleserver_id);
            if(!$server){
                continue;
            }
            $result_singleserver = array();
            $users['server_internal_id'] = $server->server_internal_id;

            $tmp_result = $api->getSetupStat($users);

            if('200' == $tmp_result->http_code){
                $tmp_result = $tmp_result->body;
                foreach ($tmp_result as $value) {
                    $key =  (isset($value->ctime) ? date("Y-m-d H:i:s", $value->ctime) : '-').
                            (isset($value->os_type) ? $value->os_type : '-').
                            (isset($value->source) ? $value->source : '-').
                            (isset($value->u1) ? $value->u1 : '-').
                            (isset($value->u2) ? $value->u2 : '-');

                    $result_singleserver[$key] = array(
                            'ctime' => isset($value->ctime) ? date('Y-m-d H:i:s', $value->ctime) : date('Y-m-d H:i:s', $start_time),
                            'end_time' => isset($value->ctime) ? date('Y-m-d H:i:s', ($value->ctime + $users['interval'])) : date('Y-m-d H:i:s', $end_time),
                            'os_type' => isset($value->os_type) ? $value->os_type : '',
                            'source' => isset($value->source) ? $value->source : '',
                            'u1' => isset($value->u1) ? $value->u1 : '',
                            'u2' => isset($value->u2) ? $value->u2 : '',
                            'device_num' => $value->device_num,
                            'device_create' => $value->device_create,
                            'device_create_lev10' => $value->device_create_lev10,
                        );
                }
            }else{
                continue;
            }

            $sum['sum_device'] = 0; //这个值不区分服务器，因此每次统计出来都是总数，每次都清零
            foreach ($result_singleserver as $key => $value) {
                $sum['sum_device'] += $value['device_num'];
                $sum['sum_device_create'] += $value['device_create'];
                $sum['sum_device_create_lev10'] += $value['device_create_lev10'];
                if(isset($details[$key])){
                    $details[$key]['device_create'] += $value['device_create'];
                    $details[$key]['device_create_lev10'] += $value['device_create_lev10'];
                }else{
                    $details[$key] = $value;
                }
            }
            unset($result_singleserver);
        }

        $data = array(
            'sum' => $sum,
            'items' => $details,
            );
            
        return Response::json($data);
    }

    public function userStatIndexyy(){  //注册用户统计运营用，去掉结果中的很多排序将提高效率
        $servers = $this->getUnionServers();
        $data = array(
                'content' => View::make('slaveapi.user.userstatyy', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function sendUserStatDatayy(){
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $interval = (int) Input::get('interval');
        $filter_id = (int) Input::get('filtrate_id');
        $source = trim(Input::get('source'));
        $u1 = trim(Input::get('u1'));
        $u2 = trim(Input::get('u2'));
        $server_id = Input::get('server_id');
        $classify = (int)Input::get('look_type');
        if($server_id[0] == '0'){ //全服
            $servers = $this->getUnionServers();
            $temp = array();
            foreach ($servers as $value) {
                $temp[] = $value->server_id;
            }
            $server_id = $temp;
        }

        $interl_time = array(
                600,
                3600,
                86400,
                0
        );
        $filter_list = array(
                'source',
                'u1',
                'u2'
        );
        $game_id = Session::get('game_id'); 
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        if (count($server_id) == 1) {  //只选一个服务器
            $server = Server::find($server_id[0]);
            $user = array();
            $user['game_id'] = $game->game_id;
            if ($server_id[0] > 0)
            {
                $user['server_internal_id'] = $server->server_internal_id;
                $user['platform_server_id'] = $server->platform_server_id;
            } else
            {
                $user['server_internal_id'] = 0;
            }
            $user['platform_id'] = Session::get('platform_id');
            $user['start_time'] = $start_time;
            $user['end_time'] = $end_time;
            $user['interval'] = $interl_time[$interval];
            $user['filter'] = $filter_list[$filter_id];
            $user['source'] = $source;
            $user['u1'] = $u1;
            $user['u2'] = $u2;

             $response = $api->getUserStatyy($user,$classify);
             if ($response->http_code != 200)
             {
                 return Response::json($response->body, $response->http_code);
             }
             $body = $response->body;             
            
            $sum = array(
                    'sum_formal' => 0,
                    'sum_anonymous' => 0,
                    'sum_player_formal' => 0,
                    'sum_player_anonymous' => 0,
                    'sum_lev_formal' => 0,
                    'sum_lev_anonymous' => 0,
                    'sum_count_anonymous_formal' => 0
            );
            foreach ($body as $item)
            {
                if ($server_id[0] == 0)
                {
                    $item->count_player_formal = 0;
                    $item->count_player_anonymous = 0;
                    $item->count_lev_formal = 0;
                    $item->count_lev_anonymous = 0;
                }
                $sum['sum_formal'] += (int) isset($item->count_formal) ? $item->count_formal : 0;
                $sum['sum_anonymous'] += (int) isset($item->count_anonymous) ? $item->count_anonymous : 0;
                $sum['sum_player_formal'] += (int) isset($item->count_player_formal) ? $item->count_player_formal : 0;
                $sum['sum_player_anonymous'] += (int) isset($item->count_player_anonymous) ? $item->count_player_anonymous : 0;
                $sum['sum_lev_formal'] += (int) isset($item->count_lev_formal) ? $item->count_lev_formal : 0;
                $sum['sum_lev_anonymous'] += (int) isset($item->count_lev_anonymous) ? $item->count_lev_anonymous : 0;
                $sum['sum_count_anonymous_formal'] += (int) isset($item->count_anonymous_formal) ? $item->count_anonymous_formal : 0;
                if ($interval == 3)
                {
                    $item->end_time = date('Y-m-d H:i:s', $end_time);
                    $item->ctime = date('Y-m-d H:i:s', $start_time);
                } else
                {
                    $item->end_time = $interl_time[$interval] + (int) $item->ctime;
                    $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                    $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                }
            }

            $data = array(
                    'sum' => $sum
            );
            
            return Response::json($data);
            
        } else {
            $len = count($server_id);
            //
            $server1 = Server::find($server_id[0]);
            $use = array();
            $use['game_id'] = $game->game_id;
            if ($server_id[0] > 0)
            {
                $use['server_internal_id'] = $server1->server_internal_id;
                $use['platform_server_id'] = $server1->platform_server_id;
            } else
            {
                $use['server_internal_id'] = 0;
            }
            $use['platform_id'] = Session::get('platform_id');
            $use['start_time'] = $start_time;
            $use['end_time'] = $end_time;
            $use['interval'] = $interl_time[$interval];
            $use['filter'] = $filter_list[$filter_id];
            $use['source'] = $source;
            $use['u1'] = $u1;
            $use['u2'] = $u2;
            $resp = $api->getUserStatyy($use, $classify);
            if ($resp->http_code != 200)
            {
                return Response::json($resp->body, $resp->http_code);
            }
            $bod = $resp->body;
            //
            for($k = 0; $k <count($bod); $k ++){
                $arr[$k] = array(
                    'a' => 0,
                    'b' =>0,
                    'c' =>0,
                    'd' =>0
                );
            }

            $sum = array(
                        'sum_formal' => 0,
                        'sum_anonymous' => 0,
                        'sum_player_formal' => 0,
                        'sum_player_anonymous' => 0,
                        'sum_lev_formal' => 0,
                        'sum_lev_anonymous' => 0,
                        'sum_count_anonymous_formal' => 0
                ); 
           

            for ($i=0; $i < $len; $i++) {
                $server[$i] = Server::find($server_id[$i]); 
                $user[$i] = array();
                $user[$i]['game_id'] = $game->game_id;
                if ($server_id[$i] > 0) {
                    $user[$i]['server_internal_id'] = $server[$i]->server_internal_id;
                    $user[$i]['platform_server_id'] = $server[$i]->platform_server_id;
                } else {
                    $user[$i]['server_internal_id'] = 0;
                }
                $user[$i]['platform_id'] = Session::get('platform_id');
                $user[$i]['start_time'] = $start_time;
                $user[$i]['end_time'] = $end_time;
                $user[$i]['interval'] = $interl_time[$interval];
                $user[$i]['filter'] = $filter_list[$filter_id];
                $user[$i]['source'] = $source;
                $user[$i]['u1'] = $u1;
                $user[$i]['u2'] = $u2;
                $response[$i] = $api->getUserStatyy($user[$i], $classify);
                if ($response[$i]->http_code != 200) {
                    return Response::json($response[$i]->body, $response[$i]->http_code);
                }
                $body[$i] = $response[$i]->body;
               // var_dump($body[$i]);
                //die();
                foreach ($body[$i] as  $item) {
                    
                    if ($server_id[$i] == 0) {
                        $item->count_player_formal = 0;
                        $item->count_player_anonymous = 0;
                        $item->count_lev_formal = 0;
                        $item->count_lev_anonymous = 0;
                    }
                    if ($i == 0) {
                        $sum['sum_formal'] += (int) isset($item->count_formal) ? $item->count_formal : 0;
                        $sum['sum_anonymous'] += (int) isset($item->count_anonymous) ? $item->count_anonymous : 0;
                        $sum['sum_count_anonymous_formal'] += (int) isset($item->count_anonymous_formal) ? $item->count_anonymous_formal : 0;
                    }
                    $sum['sum_player_anonymous'] += (int) isset($item->count_player_anonymous) ? $item->count_player_anonymous : 0;
                    $sum['sum_lev_formal'] += (int) isset($item->count_lev_formal) ? $item->count_lev_formal : 0;
                    $sum['sum_lev_anonymous'] += (int) isset($item->count_lev_anonymous) ? $item->count_lev_anonymous : 0;
                    $sum['sum_player_formal'] += (int) isset($item->count_player_formal) ? $item->count_player_formal : 0;
                    //Log::info('count_player_anonymous:'.(int) $item->count_player_anonymous.'count_player_formal:'.(int) $item->count_player_formal.'sum_player_anonymous:'.$sum['sum_player_anonymous'].'sum_player_formal:'.$sum['sum_player_formal']);
                    if ($interval == 3) {
                        $item->end_time = date('Y-m-d H:i:s', $end_time);
                        $item->ctime = date('Y-m-d H:i:s', $start_time);
                    } else {
                        $item->end_time = $interl_time[$interval] + (int) $item->ctime;
                        $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                        $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                    }
                }
            }
        }

            $data = array(
                'sum' => $sum
            );

            if (isset($data)) {
                return Response::json($data);
            } else {
                return Response::json('error','error');
            }
    }

    public function downloadUserStatIndex()
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

    public function downloadUserStatData(){

        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );

        $tobedownload = Input::get('tobedownload');
        if (empty($tobedownload)){ //下载数据若不存在
            return Response::json(array('error'=>'没有数据需要下载!'), 403);
        }

        $result = array();
        $now = time();
        $file = storage_path() . "/cache/" . $now . ".csv";
        $title = array(
                '开始时间',
                '结束时间',
                'Source',
                'U1',
                'U2',
                '正常注册',
                '匿名注册',
                '匿名注册升级',
                '正常创建',
                '正常创建占正常注册百分比',
                '匿名创建',
                '匿名创建占匿名注册百分比',
                '正常10级创建',
                '正常10级创建占正常创建百分比',
                '匿名10级创建',
                '匿名10级创建占匿名创建百分比',
        );

        $csv = CSV::init($file, $title);
        foreach ($tobedownload as $value) {
            if(isset($value['u1'])){
                $u1 = $value['u1'];
            }else{
                $u1 = '';
            }
            if(isset($value['u2'])){
                $u2 = $value['u2'];
            }else{
                $u2 = '';
            }
            if($value['count_formal'] == '0'){
                $div1 = 1;
            }else{
                $div1 = $value['count_formal'];
            }
            if($value['count_anonymous'] == '0'){
                $div2 = 1;
            }else{
                $div2 = $value['count_anonymous'];
            }
            if($value['count_player_formal'] == '0'){
                $div3 = 1;
            }else{
                $div3 = $value['count_player_formal'];
            }
            if($value['count_player_anonymous'] == '0'){
                $div4 = 1;
            }else{
                $div4 = $value['count_player_anonymous'];
            }
            $result = array(
                '开始时间' => $value['ctime'],
                '结束时间' => $value['end_time'],
                'Source' => $value['source'],
                'U1' => $u1,
                'U2' => $u2,
                '正常注册' => $value['count_formal'],
                '匿名注册' => $value['count_anonymous'],
                '匿名注册升级' => $value['count_anonymous_formal'],
                '正常创建' => $value['count_player_formal'],
                '正常创建占正常注册百分比' => round((($value['count_player_formal']/$div1)*100),2).'%',
                '匿名创建' => $value['count_player_anonymous'],
                '匿名创建占匿名注册百分比' => round((($value['count_player_anonymous']/$div2)*100),2).'%',
                '正常10级创建' => $value['count_lev_formal'],
                '正常10级创建占正常创建百分比' => round((($value['count_lev_formal']/$div3)*100),2).'%',
                '匿名10级创建' => $value['count_lev_anonymous'],
                '匿名10级创建占匿名创建百分比' => round((($value['count_lev_anonymous']/$div4)*100),2).'%',
                );
            $res = $csv->writeData($result);
            unset($u1);
            unset($u2);
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

    public function SXDUserStatIndex()
    {
        $servers = $this->getUnionServers();
        $data = array(
                'content' => View::make('slaveapi.user.sxd.userstat', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function SXDSendUserStatData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $interval = (int) Input::get('interval');
        $filter_id = (int) Input::get('filtrate_id');
        $source = trim(Input::get('source'));
        $u1 = trim(Input::get('u1'));
        $u2 = trim(Input::get('u2'));
        $server_id = Input::get('server_id');
        //$server = Server::find($server_id);
        $interl_time = array(
                600,
                3600,
                86400,
                0
        );
        $filter_list = array(
                'source',
                'u1',
                'u2'
        );
        $game = Game::find(Session::get('game_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if (count($server_id) === 1) { //选择单服
            $server = Server::find($server_id[0]);
            $user = array();
            $user['game_id'] = $game->game_id;
            if ($server_id[0] > 0){
                $user['platform_server_id'] = $server->platform_server_id;
            } else
            {
                $user['platform_server_id'] = 0;
            }
            $user['platform_id'] = Session::get('platform_id');
            $user['start_time'] = $start_time;
            $user['end_time'] = $end_time;
            $user['interval'] = $interl_time[$interval];
            $user['filter'] = $filter_list[$filter_id];
            $user['source'] = $source;
            $user['u1'] = $u1;
            $user['u2'] = $u2;
            $response = $api->SXDGetUserStat($user);
            if ($response->http_code != 200)
            {
                return Response::json($response->body, $response->http_code);
            }
            $body = $response->body;
            $sum = array(
                    'sum_formal' => 0,
                    'sum_anonymous' => 0,
                    'sum_player_formal' => 0,
                    'sum_player_anonymous' => 0,
                    'sum_lev_formal' => 0,
                    'sum_lev_anonymous' => 0,
                    'sum_count_anonymous_formal' => 0
            );
            foreach ($body as $item)
            {
                if ($server_id[0] == 0)
                {
                    $item->count_player_formal = 0;
                    $item->count_player_anonymous = 0;
                    $item->count_lev_formal = 0;
                    $item->count_lev_anonymous = 0;
                }
                
                    $sum['sum_formal'] += (int) $item->count_formal;
                    $sum['sum_anonymous'] += (int) $item->count_anonymous;
                    $sum['sum_count_anonymous_formal'] += (int) $item->count_anonymous_formal;
                
                
                $sum['sum_player_formal'] += (int) $item->count_player_formal;
                $sum['sum_player_anonymous'] += (int) $item->count_player_anonymous;
                $sum['sum_lev_formal'] += (int) $item->count_lev_formal;
                $sum['sum_lev_anonymous'] += (int) $item->count_lev_anonymous;
               
                if ($interval == 3)
                {
                    $item->end_time = date('Y-m-d H:i:s', $end_time);
                    $item->ctime = date('Y-m-d H:i:s', $start_time);
                } else
                {
                    $item->end_time = $interl_time[$interval] + (int) $item->ctime;
                    $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                    $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                }
            }
            $statdata = array();
            
            $blank = new stdClass();
            $blank->ctime = null;
            $blank->end_time = null;
            $blank->source = null;
            $blank->u1 = null;
            $blank->u2 = null;
            $blank->count_formal = null;
            $blank->count_anonymous = null;
            $blank->count_anonymous_formal = null;
            $blank->count_player_formal = null;
            $blank->count_player_anonymous = null;
            $blank->count_lev_formal = null;
            $blank->count_lev_anonymous = null;
            for ($i = 0; $i < sizeof($body); $i ++)
            {
                if ($i > 0 && $body[$i]->ctime != $body[$i - 1]->ctime)
                {
                    $statdata[] = $blank;
                }
                $statdata[] = $body[$i];
            }
            $data = array(
                    'items' => $statdata,
                    'sum' => $sum
            );
            if ($response->http_code == 200)
            {
                return Response::json($data);
            } else
            {
                return Response::json($body, $response->http_code);
            }
        } else {
            //选择多服
           $len  = count($server_id);
             $server1 = Server::find($server_id[0]);
            $use = array();
            $use['game_id'] = $game->game_id;
            if ($server_id[0] > 0)
            {
                $use['server_internal_id'] = $server1->server_internal_id;
                $use['platform_server_id'] = $server1->platform_server_id;
            } else
            {
                $use['server_internal_id'] = 0;
            }
            $use['platform_id'] = Session::get('platform_id');
            $use['start_time'] = $start_time;
            $use['end_time'] = $end_time;
            $use['interval'] = $interl_time[$interval];
            $use['filter'] = $filter_list[$filter_id];
            $use['source'] = $source;
            $use['u1'] = $u1;
            $use['u2'] = $u2;
            $resp = $api->SXDGetUserStat($use);
            if ($resp->http_code != 200)
            {
                return Response::json($resp->body, $resp->http_code);
            }
            $bod = $resp->body;
            //
            for($k = 0; $k <count($bod); $k ++){
                $arr[$k] = array(
                    'a' => 0,
                    'b' =>0,
                    'c' =>0,
                    'd' =>0
                );
            }

           //
            $sum = array(
                        'sum_formal' => 0,
                        'sum_anonymous' => 0,
                        'sum_player_formal' => 0,
                        'sum_player_anonymous' => 0,
                        'sum_lev_formal' => 0,
                        'sum_lev_anonymous' => 0,
                        'sum_count_anonymous_formal' => 0
                );
            for ($i = 0; $i < $len ; $i++) { 
                $server[$i] = Server::find($server_id[$i]);
                $user[$i] = array();
                $user[$i]['game_id'] = $game->game_id;
                if ($server_id[$i] > 0){
                    $user[$i]['platform_server_id'] = $server[$i]->platform_server_id;
                } else{
                    $user[$i]['platform_server_id'] = 0;
                }
                $user[$i]['platform_id'] = Session::get('platform_id');
                $user[$i]['start_time'] = $start_time;
                $user[$i]['end_time'] = $end_time;
                $user[$i]['interval'] = $interl_time[$interval];
                $user[$i]['filter'] = $filter_list[$filter_id];
                $user[$i]['source'] = $source;
                $user[$i]['u1'] = $u1;
                $user[$i]['u2'] = $u2;
                $response[$i] = $api->SXDGetUserStat($user[$i]);
                if ($response[$i]->http_code != 200)
                {
                    return Response::json($response[$i]->body, $response[$i]->http_code);
                }
                $body[$i] = $response[$i]->body;
                foreach ($body[$i] as $item) {
                    if ($server_id[$i] == 0) {
                        $item->count_player_formal = 0;
                        $item->count_player_anonymous = 0;
                        $item->count_lev_formal = 0;
                        $item->count_lev_anonymous = 0;
                    }
                    if ($i == 0) {
                        $sum['sum_formal'] += (int) $item->count_formal;
                        $sum['sum_anonymous'] += (int) $item->count_anonymous;
                        $sum['sum_player_formal'] += (int) $item->count_player_formal;
                    }
                    $sum['sum_player_anonymous'] += (int) $item->count_player_anonymous;
                    $sum['sum_lev_formal'] += (int) $item->count_lev_formal;
                    $sum['sum_lev_anonymous'] += (int) $item->count_lev_anonymous;
                    $sum['sum_count_anonymous_formal'] += (int) $item->count_anonymous_formal;
                    if ($interval == 3) {
                        $item->end_time = date('Y-m-d H:i:s', $end_time);
                        $item->ctime = date('Y-m-d H:i:s', $start_time);
                    } else {
                        $item->end_time = $interl_time[$interval] + (int) $item->ctime;
                        $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                        $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                    }
                }
                
                $statdata = array();
                
                $blank = new stdClass();
                $blank->ctime = null;
                $blank->end_time = null;
                $blank->source = null;
                $blank->u1 = null;
                $blank->u2 = null;
                $blank->count_formal = null;
                $blank->count_anonymous = null;
                $blank->count_anonymous_formal = null;
                $blank->count_player_formal = null;
                $blank->count_player_anonymous = null;
                $blank->count_lev_formal = null;
                $blank->count_lev_anonymous = null;
                
                for ($j = 0; $j < sizeof($body[$i]); $j ++) {
                    if ($j > 0 && $body[$i][$j]->ctime != $body[$i][$j - 1]->ctime) {
                        $statdata[] = $blank;
                    }
                    $statdata[$j] = new stdClass();
                    $statdata[$j]->ctime = 0;
                    $statdata[$j]->end_time= 0;
                    $statdata[$j]->source = 0;
                    $statdata[$j]->u1= '';
                    $statdata[$j]->u2= '';
                    $statdata[$j]->count_formal = 0;
                    $statdata[$j]->count_anonymous = 0;
                    $statdata[$j]->count_anonymous_formal =0;
                    
                   

                    $statdata[$j]->ctime = $body[0][$j]->ctime;
                    $statdata[$j]->end_time = $body[0][$j]->end_time;
                    $statdata[$j]->source = $body[0][$j]->source;
                    $statdata[$j]->u1 = isset($body[0][$j]->u1) ? $body[0][$j]->u1 : '';
                    $statdata[$j]->u2 = isset($body[0][$j]->u2) ? $body[0][$j]->u2 : '';
                    
                    $statdata[$j]->count_formal = $body[0][$j]->count_formal;
                    $statdata[$j]->count_anonymous = $body[0][$j]->count_anonymous;
                    $statdata[$j]->count_anonymous_formal = $body[0][$j]->count_anonymous_formal;
                    
                    $arr[$j]['a']+= $body[$i][$j]->count_player_formal;
                    $arr[$j]['b']+= $body[$i][$j]->count_player_anonymous;
                    $arr[$j]['c']+= $body[$i][$j]->count_lev_formal;
                    $arr[$j]['d']+= $body[$i][$j]->count_lev_anonymous;
                }
                
            }

            for($k = 0; $k< count($statdata); $k++) {

                $statdata[$k]->count_player_formal = 0;
                $statdata[$k]->count_player_anonymous= 0;
                $statdata[$k]->count_lev_formal = 0;
                $statdata[$k]->count_lev_anonymous = 0;

                $statdata[$k]->count_player_formal = $arr[$k]['a'];
                $statdata[$k]->count_player_anonymous = $arr[$k]['b'];
                $statdata[$k]->count_lev_formal = $arr[$k]['c'];
                $statdata[$k]->count_lev_anonymous = $arr[$k]['d'];

            }
            $data = array(
                        'items' => $statdata,
                        'sum' => $sum
                );
            if (isset($data)) {
                return Response::json($data);
            } else {
                return Response::json('error', 'error');
            }

        }
    }

    public function FBStatIndex()
    {
        $game_id = Session::get('game_id');
        if ($game_id == 3 || $game_id == 4 || $game_id == 38 || $game_id == 45)
        { // 印尼泰国 slave
            $current_diff_hours = 14;
        } else if ($game_id == 36)
        { // 越南女神有点问题
            $current_diff_hours = 15;
        } else if ($game_id == 41)
        { // 泰国女神
            $current_diff_hours = - 1;
        } elseif ($game_id == 44 || $game_id == 53){
            $current_diff_hours = - 6;
        } else
        {
            $current_diff_hours = 15;
        }
        $servers = $this->getUnionServers();
        $data = array(
                'content' => View::make('slaveapi.user.fb', 
                        array(
                                'servers' => $servers,
                                'current_diff_hours' => $current_diff_hours
                        ))
        );
        return View::make('main', $data);
    }

    public function FBStatData()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknown'),
                'error' => Lang::get('error.server_not_found')
        );
        
        $game = Game::find(Session::get('game_id'));
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $diff_hours = Input::get('diff_hours');

        $server_ids = Input::get('server_id');
        if (count($server_ids) === 1)
        {
            $server_id = $server_ids[0];
            $server = Server::find($server_id);
            if (! $server)
            {
                return Response::json($msg, 404);
            }
            
            $server_internal_id = Server::where('server_id', '=', $server_id)->pluck(
                    'server_internal_id');
            $game = Game::find(Session::get('game_id'));
            $platform_id = Session::get('platform_id');
            $fb = array(
                    'platform_id' => $platform_id,
                    'game_id' => $game->game_id,
                    'server_internal_id' => $server_internal_id,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'diff_hours' => $diff_hours
            );
            $u1 = trim(Input::get('u1'));
            $u2 = trim(Input::get('u2'));
            if ($u1)
            {
                $fb['u1'] = $u1;
            }
            if ($u2)
            {
                $fb['u2'] = $u2;
            }
            $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                    $game->eb_api_secret_key);
            $response = $api->getFBStat($fb);
            unset($fb);
            $body = $response->body;
            if ($response->http_code == 200)
            {
                foreach ($body as &$v)
                {
                    $v->spent = round($v->spent, 2);
                    $v->click_through_rate = round($v->click_through_rate, 2);
                    $v->count_formal_user = (int) $v->count_formal_user;
                    $v->count_formal_player = (int) $v->count_formal_player;
                    $v->count_formal_lev = (int) $v->count_formal_lev;
                    $v->cost_formal_user = $v->count_formal_user > 0 ? round(
                            $v->spent / $v->count_formal_user, 2) : 0;
                    $v->cost_formal_player = $v->count_formal_player > 0 ? round(
                            $v->spent / $v->count_formal_player, 2) : 0;
                    $v->cost_formal_lev = $v->count_formal_lev > 0 ? round(
                            $v->spent / $v->count_formal_lev, 2) : 0;
                    $v->total_user = (int) $v->total_user;
                    $v->total_player = (int) $v->total_player;
                    $v->total_lev = (int) $v->total_lev;
                    $v->cost_total_player = $v->total_player > 0 ? round(
                            $v->spent / $v->total_player, 2) : 0;
                    $v->cost_total_lev = $v->total_lev > 0 ? round(
                            $v->spent / $v->total_lev, 2) : 0;
                }
                unset($v);
                return Response::json($body);
            } else
            {
                return Response::json($body, $response->http_code);
            }
        } else
        {
            $result = array();
            foreach ($server_ids as $server_id)
            {
                $server = Server::find($server_id);
                $server_internal_id = Server::where('server_id', '=', 
                        $server_id)->pluck('server_internal_id');
                $game = Game::find(Session::get('game_id'));
                $platform_id = Session::get('platform_id');
                $fb = array(
                        'platform_id' => $platform_id,
                        'game_id' => $game->game_id,
                        'server_internal_id' => $server_internal_id,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'diff_hours' => $diff_hours
                );
                $u1 = trim(Input::get('u1'));
                $u2 = trim(Input::get('u2'));
                if ($u1)
                {
                    $fb['u1'] = $u1;
                }
                if ($u2)
                {
                    $fb['u2'] = $u2;
                }
                $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                        $game->eb_api_secret_key);
                $response = $api->getFBStat($fb);
                $body = $response->body;
                if ($response->http_code == 200)
                {
                    $i = 0;
                    foreach ($body as &$v)
                    {
                        if (! isset($result[$i]))
                        {
                            $result[$i] = array(
                                    "count_formal_player" => 0,
                                    "count_formal_lev" => 0,
                                    "total_player" => 0,
                                    "total_lev" => 0
                            );
                        }
                        $result[$i]['count_formal_player'] += (int) $v->count_formal_player;
                        $result[$i]['count_formal_lev'] += (int) $v->count_formal_lev;
                        $result[$i]['total_player'] += (int) $v->total_player;
                        $result[$i]['total_lev'] += (int) $v->total_lev;
                        $result[$i]['campaign'] = $v->campaign;
                        $result[$i]['fb_u1'] = $v->fb_u1;
                        $result[$i]['fb_u2'] = $v->fb_u2;
                        $result[$i]['spent'] = round($v->spent, 2);
                        $result[$i]['click_through_rate'] = round(
                                $v->click_through_rate, 2);
                        $result[$i]['count_formal_user'] = (int) $v->total_user;
                        $result[$i]['total_user'] = (int) $v->total_user;
                        $i ++;
                    }
                    unset($v);
                } else
                {
                    continue;
                }
            }
            //重新计算成本
            foreach ($result as &$item)
            {
                $item['cost_formal_user'] = $item['count_formal_user'] > 0 ? round(
                        $item['spent'] / $item['count_formal_user'], 2) : 0;
                $item['cost_formal_player'] = $item['count_formal_player'] > 0 ? round(
                        $item['spent'] / $item['count_formal_player'], 2) : 0;
                $item['cost_formal_lev'] = $item['count_formal_lev'] > 0 ? round(
                        $item['spent'] / $item['count_formal_lev'], 2) : 0;
                $item['cost_total_player'] = $item['total_player'] > 0 ? round(
                        $item['spent'] / $item['total_player'], 2) : 0;
                $item['cost_total_lev'] = $item['total_lev'] > 0 ? round(
                        $item['spent'] / $item['total_lev'], 2) : 0;
            }
            unset($item);
            return Response::json($result);
        }
    }

    public function createdPlayerIndex()
    {
        $servers = $this->getUnionServers();
        $data = array(
                'content' => View::make('slaveapi.user.player', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function createdPlayerData()
    {
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        
        $server_id = (int) Input::get('server_id');
        if($server_id == '0'){
            $server_internal_id = 0;
        }else{
            $server_internal_id = Server::where('server_id', '=', $server_id)->pluck(
                    'server_internal_id');
        }
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $interval = (int) Input::get('interval');
        $interval_array = array(
                '',
                '600',
                '3600',
                '86400'
        );
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $response = $api->getCreatePlayerStat($platform_id, $game->game_id, 
                $server_internal_id, $start_time, $end_time, 
                $interval_array[$interval]);
        $body = $response->body;
        if ($response->http_code == 200)
        {
            foreach ($body as $item)
            {
                if ($interval == 0)
                {
                    $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                    $item->end_time = date('Y-m-d H:i:s', $item->max_time);
                } else
                {
                    $item->end_time = $item->ctime + $interval_array[$interval];
                    $item->end_time = date('Y-m-d H:i:s', $item->end_time);
                    $item->ctime = date('Y-m-d H:i:s', $item->ctime);
                }
            }
            return Response::json($body);
        } else
        {
            return Response::json($body, $response->http_code);
        }
    }

    public function getChannelStatForYY(){
        return $this->getChannelStat($is_yy = 1);
    }

    public function getChannelStatDataForYY(){
        return $this->getChannelStatData($is_yy = 1);
    }

    public function getChannelStat($is_yy = 0)
    {
        $servers = $this->getUnionServers();
        $game = Game::find(Session::get('game_id'));
        $data = array(
                'content' => View::make('slaveapi.user.channel', 
                        array(
                                'servers' => $servers,
                                'game_type' => $game->game_type,
                                'is_yy' => $is_yy,
                        ))
        );
        return View::make('main', $data);
    }

    public function getChannelStatData($is_yy = 0)
    {
        $msg = array(
                'error' => ''
        );
        
        $source = Input::get('source');
        $u1 = Input::get('u1');
        $u2 = Input::get('u2');
        $filter = Input::get('filter');
        $is_anonymous = Input::get('is_anonymous');
        $reg_start_time = strtotime(Input::get('reg_start_time'));
        $reg_end_time = strtotime(Input::get('reg_end_time'));
        $order_start_time = strtotime(Input::get('order_start_time'));
        $order_end_time = strtotime(Input::get('order_end_time'));
        $channel_type = Input::get('channel_type');
        
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server && $channel_type == 'retention')
        {
            $msg['error'] = Lang::get('server.select_server');
            return Response::json($msg, 403);
        }
        
        $platform = Platform::find(Session::get('platform_id'));
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $params = array(
                'reg_start_time' => $reg_start_time,
                'reg_end_time' => $reg_end_time,
                'filter' => $filter
        );
        if ($source)
        {
            $params['source'] = $source;
        }
        if ($u1)
        {
            $params['u1'] = $u1;
            if('source' == $params['filter']){
                unset($params['filter']);
                $params['filter'] = 'u1';
            }
        }
        if ($u2)
        {
            $params['u2'] = $u2;
            unset($params['filter']);
            $params['filter'] = 'u2';
        }
        if ($is_anonymous < 2)
        {
            $params['is_anonymous'] = $is_anonymous;
        }

        if ($channel_type == 'retention')
        {
            $server_internal_id = $server->server_internal_id;
            $params['server_internal_id'] = $server_internal_id;
            $params['game_id'] = $game->game_id;
            $params['os_type'] = Input::get('os_type');
            $response = $api->getChannelRetentionStat($platform->platform_id, 
                    $params);
        } else if ($channel_type == 'order')
        {
            $params['currency_id'] = $platform->default_currency_id;
            $params['order_start_time'] = $order_start_time;
            $params['order_end_time'] = $order_end_time;
            $params['game_id'] = $game->game_id;
            $response = $api->getChannelOrderStat($platform->platform_id, 
                    $params);
        }
        if ($response->http_code != 200)
        {
            return Response::json(array('error'=>'未查询到数据!'), 403);
        }
        
        foreach ($response->body as &$v)
        {
            if (isset($v->total_amount))
            {
                $v->total_amount = round($v->total_amount, 2);
            }
            if (isset($v->total_dollar_amount))
            {
                $v->total_dollar_amount = round($v->total_dollar_amount, 2);
            }
        }

        if($channel_type == 'order' && $is_yy){
            $response->body = array($response->body[0]);
        }
        unset($v);
        return Response::json($response->body);
    }

    public function userDeviceIndexForYY(){ //给运营用的
        return $this->userDeviceIndex($is_yy = 1);
    }

    public function userDeviceDataForYY(){
        return $this->userDeviceData();
    }

    public function userDeviceIndex($is_yy = 0) //is_yy表明是否运营
    {
        $data = array(
                'content' => View::make('slaveapi.user.userDevice', 
                        array(
                                'is_yy' => $is_yy,
                        ))
        );
        return View::make('main', $data);
    }

    public function userDeviceData()
    {
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $game_id = Session::get('game_id');
        $game = Game::find(Session::get('game_id'));

        if('2' != $game->game_type){
            return Response::json(array('error'=>'请检测当前游戏是否是手游!'), 403);
        }
        $interval = (int) Input::get('interval');
        $check_type = (int) Input::get('check_type');
        $serach_type = (int)Input::get('serach_type');
        $channel_type = Input::get('channel_type');
        $source = Input::get('source');

        $interl_time = array(
                600,
                3600,
                86400,
                0
        );
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        //foreach ($server_ids as $server_id) {
            //$server = Server::find($server_id);
            $user = array();
            $user['start_time'] = $start_time;
            $user['end_time'] = $end_time;
            //$user['game_id'] = $game->game_id;
            $user['platform_id'] = Session::get('platform_id');
            //$user['server_internal_id'] = $server->server_internal_id;
            //$user['platform_server_id'] = $server->platform_server_id;
            $user['interval'] = $interl_time[$interval];
            $user['check_type'] = $check_type;
            $user['game_id'] = $game_id;
            $user['serach_type'] = $serach_type;
            $user['channel_type'] = $channel_type;
            $user['source'] = $source;
            $response = $api->getUserDevice($user);
            if ($response->http_code != 200)
            {
                return Response::json(array('error'=>$response->body), 403);
            }
            $body = $response->body;
            $data = array();
            $emptyLine=array(
                'time' => '--',
                'count' => '--',
                'os_type' => '--',
                'channel' => '--',
                'source' => '--',
                );
            $tmp = 0;
            foreach ($body as $key => $v) {
                if($tmp!=$v->ctime){
                    $data[] = $emptyLine;
                }
                $result['time'] = $interl_time[$interval]==0?
                    date('Y-m-d H:i:s', $start_time).'--'.date('Y-m-d H:i:s', $end_time)
                    :
                    $v->ctime.'--'.date("Y-m-d H:i:s", strtotime($v->ctime)+$interl_time[$interval])
                    ;
                $result['count'] = $v->count;
                if('0' == $serach_type){
                    $result['os_type'] = $v->os_type;
                    $result['channel'] = '--';
                    $result['source'] = '--';
                }elseif('1' == $serach_type){
                    $result['os_type'] = '--';
                    $result['channel'] = $v->channel;
                    $result['source'] = '--';
                }elseif('2' == $serach_type){
                    $result['os_type'] = '--';
                    $result['channel'] = '--';
                    $result['source'] = $v->source;
                }
                $data[] = $result;
                $tmp = $v->ctime;
            }
            return Response::json($data);
        //}
    }

    /*
     *德州扑克获取活跃用户信息
    */
    public function pokerUserActivateIndex()
    {
        $data = array(
            'content' => View::make('serverapi.poker.users.activate_users'),
        );
        return View::make('main', $data);
    }
    public function pokerUserActivateData()
    {
        $msg  = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'start_time' => 'required',
            'end_time' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $day = $api->pokerActivateData($platform_id, $game_id,  2, $start_time, $end_time, $type = 1);

        var_dump($api);
        die();

        $day_revenue = $api->pokerActivateData($platform_id,$game_id,$server_interval_id = 2,$start_time, $end_time, $type = 2)->get();

        //用户活跃度 暂时不懂

        //周活跃
        $week = $api->pokerActivateData($start_time, $end_time, $type = 3)->get(); 
        //月活跃
        $month = $api->pokerActivateData($start_time, $end_time, $type = 4)->get();

        $day_month = $day> 0 ? (round($day / $month, 3) * 100 . "%") : 0 ;

        //$m_value = $day*3 + $m_revenue * 5;
        //新用户注册
        $new_activate = $api->pokerActivateData($start_time, $end_time ,$type = 5)->get();;
        //老用户活跃
        $old_activate = $day - $new_activate;
        //新用户占活跃用户比例
        $new_old = $new_activate > 0 ? (round($new_activate / $day, 2) *100 ."%") : 0; 
        
        $data = array(
            'day' => $day,
            'day_revenue' => $day_revenue,
            'week' => $week,
            'month' => $month,
            'day_month' => $day_month,
        );
    }

    //简略版注册用户统计--给运营用--Panda
    public function signnumusersload(){
        $servers = $this->getUnionServers();
        $game_id = Session::get('game_id');
        $data = array(
                'content' => View::make('slaveapi.user.signup_num', 
                        array(
                                'servers' => $servers,
                                'webgameids' => $this->webgameids,
                                'game_id' => $game_id,
                        ))
        );
        return View::make('main', $data); 
    }

    public function signnumusersdata(){
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $server_ids = Input::get('server_ids');

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        if('0' == count($server_ids)){
            return Response::json(array('error'=>'请选择服务器'), 403);
        }
        if(0 == $server_ids[0]){
            $server_internal_ids = array();
            $result = $api->getsignupnum($start_time, $end_time, $game_id, $platform_id, $server_internal_ids);
            if('200' != $result->http_code){
                return Response::json(array('error'=>'查询时间过长或slave端发生错误，请等待两分钟后重新查询，如果连续出现此情况，请联系技术'), 403);
            }
        }else{
            $server_internal_ids = array();
            foreach ($server_ids as $server_id) {
                $server_internal_ids[] = $server = Server::find($server_id)->server_internal_id;
            }
            $result = $api->getsignupnum($start_time, $end_time, $game_id, $platform_id, $server_internal_ids);
            if('200' != $result->http_code){
                return Response::json(array('error'=>'查询时间过长或slave端发生错误，请等待两分钟后重新查询，如果连续出现此情况，请联系技术'), 403);
            }
        }

        $data = array(
                'result' => (array)$result->body,
            );
        return Response::json($data);
    }

    public function playerinfolike(){   //模糊查询玩家信息
        $servers = Server::currentGameServers()->get(); 
        $data = array(
                'content' => View::make('slaveapi.user.player_info_incomplete', array(
                    'servers' => $servers,
                    ))
        );
        return View::make('main', $data); 
    }
    
    public function playerinfocheck(){   //模糊查询玩家信息
        $server_id = Input::get('server_id');
        $type = Input::get('type');
        $id_or_name = Input::get('id_or_name');
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        if('0' == $server_id){
            $result = $slave_api->getplayerinfolike($type, $id_or_name, $platform_id, $game_id);    //不知道玩家服务器的情况

        }else{
            $server = Server::find($server_id);
            if(!$server){
                return Response::json(array('error'=>'无效的服务器'), 404);
            }
            $server_internal_id = $server->server_internal_id;
            $result = $slave_api->getplayerinfolikeserver($type, $id_or_name, $platform_id, $game_id, $server_internal_id);     //知道玩家服务器的情况

        }

        if('404' == $result->http_code){
            return Response::json(array('error'=>'没有匹配的结果'), 404);
        }
        if('200' == $result->http_code){
            if('0' == $server_id)
                foreach ($result->body as &$value) {
                    $single_server = Server::where('game_id', $game_id)->where('server_internal_id', $value->server_internal_id)->first();
                    $value->server_id = $single_server->server_id;
            }
            return Response::json($result->body);
        }
        return $slave_api->sendResponse();
    }

    public function gmMessageLikeIndex(){
        $data = array(
                'content' => View::make('slaveapi.user.gmMessagelike', array())
        );
        return View::make('main', $data);         
    }

    public function gmMessageLike(){
        $page = ( int ) Input::get('page');
        $per_page = 100;
        $page = $page > 0 ? $page : 1;
        $partofmessage = Input::get('partofmessage');
        $game_id = Session::get('game_id');
        $type = Input::get('type');
        $gm_name = Input::get('gm_name');
        $limit_time = Input::get('limit_time');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        if(!$partofmessage && !$gm_name){   //要求至少输入一个条件，不然回复过多要爆炸了
            return Response::json(array('error' => 'Please Input at least one limit.'), 404);
        }

        $result = array();
        if(in_array($game_id, Config::get('game_config.mobilegames'))){//手游的数据保存比较混乱，需要额外处理
            if('reply_message' == $type){
                $messages_limit = GM::GetGmMessageLikeMG($game_id, $partofmessage);
                if($gm_name){
                   $messages_limit->where('username', $gm_name); 
                }
                if($limit_time){
                    $messages_limit->whereBetween('replied_time',array($start_time,$end_time));
                } 

                $messages_count = GM::GetGmMessageLikeMG($game_id, $partofmessage);
                if($gm_name){
                   $messages_count->where('username', $gm_name); 
                }
                if($limit_time){
                    $messages_count ->whereBetween('replied_time',array($start_time,$end_time));
                }   
                $messages_count = $messages_count->count();
                
                $messages = $messages_limit->forpage($page, 100)->get();
                if(count($messages)){
                    foreach ($messages as $message) {
                        $tmp = array(
                            'reply_message' => $message->message,
                            'reply_time' => date('Y-m-d H:i:s', $message->send_time),
                            'username' => $message->username,
                            'player_id' => $message->player_id,
                        );
                        $question = GM::getPossibleQuestion($message->send_time, $message->player_id)->first();
                        if($question){
                            $tmp['message'] = $question->message;
                            $tmp['send_time'] = date('Y-m-d H:i:s', $question->send_time);
                        }       
                        $result['items'][] = $tmp;
                        unset($tmp);
                    }
                    $result['current_page'] = $page;
                    $result['count'] = $messages_count;
                    $result['per_page'] = $per_page;
                }
            }else{
                $messages = GM::GetGmQuestionLikeMG($game_id, $partofmessage);
                if($limit_time){
                    $messages->whereBetween('send_time',array($start_time,$end_time));
                }
                           
                $messages_count = $messages->count();

                $messages = $messages->forpage($page, 100)->get();
                if(count($messages)){
                    foreach ($messages as $message) {
                        $tmp = array(
                            'message' => $message->message,
                            'send_time' => date('Y-m-d H:i:s', $message->send_time),
                            'player_id' => $message->player_id,
                        );
                        $answer = GM::getPossibleAnswer($message->send_time, $message->player_id)->first();
                        if($answer){
                            $tmp['reply_message'] = $answer->message;
                            $tmp['reply_time'] = date('Y-m-d H:i:s', $answer->send_time);
                            $tmp['username'] = $answer->username;
                        }       
                        $result['items'][] = $tmp;
                        unset($tmp);
                    }
                    $result['current_page'] = $page;
                    $result['count'] = $messages_count;
                    $result['per_page'] = $per_page;
                }
            }
        }else{//页游的GM回复信息处理
            if('reply_message' == $type){
                $messages_limit = GM::GetGmMessageLikeWG($game_id, $partofmessage);
                if($gm_name){
                    $messages_limit->where('username', $gm_name);
                }
                if($limit_time){
                    $messages_limit->whereBetween('replied_time',array($start_time,$end_time));
                }

                $messages_count = GM::GetGmMessageLikeWG($game_id, $partofmessage);
                if($gm_name){
                    $messages_count->where('username', $gm_name);
                }
                if($limit_time){
                    $messages_count->whereBetween('replied_time',array($start_time,$end_time));
                }
                $messages_count = $messages_count->count();
                
                $messages = $messages_limit->forpage($page, 100)->get();
                if(count($messages) > 0){
                    foreach ($messages as $message) {
                        $result['items'][] = array(
                            'reply_message' => $message->reply_message,
                            'reply_time' => date('Y-m-d H:i:s', $message->replied_time),
                            'send_time' => date('Y-m-d H:i:s', $message->send_time),
                            'username' => $message->username,
                            'message' => $message->message,
                            'player_id' => $message->player_id,
                        );
                    }
                    $result['current_page'] = $page;
                    $result['count'] = $messages_count;
                    $result['per_page'] = $per_page;
                }
            }else{
                $messages_limit = GM::GetGmQuestionLikeWG($game_id, $partofmessage);
                if($limit_time){
                    $messages_limit->whereBetween('send_time',array($start_time,$end_time));
                }

                $messages_count = GM::GetGmQuestionLikeWG($game_id, $partofmessage);
                if($gm_name){
                    $messages_count->where('username', $gm_name);
                }
                if($limit_time){
                    $messages_count->whereBetween('send_time',array($start_time,$end_time));
                }
                $messages_count = $messages_count->count();
                
                $messages = $messages_limit->forpage($page, 100)->get();
                if(count($messages) > 0){
                    foreach ($messages as $message) {
                        $result['items'][] = array(
                            'reply_message' => $message->reply_message,
                            'reply_time' => date('Y-m-d H:i:s', $message->replied_time),
                            'send_time' => date('Y-m-d H:i:s', $message->send_time),
                            'username' => $message->username,
                            'message' => $message->message,
                            'player_id' => $message->player_id,
                            );
                    }
                    $result['current_page'] = $page;
                    $result['count'] = $messages_count;
                    $result['per_page'] = $per_page;
                }
            }
        }

        if(count($result) > 0){
            return Response::json($result);
        }else{
            return Response::json(array('error'=>'没有匹配的结果'), 404);
        }
    }

    public function userDeviceSearchIndex()
    {
        $servers = $this->getUnionServers();
        $data = array(
                'content' => View::make('slaveapi.user.userDeviceSearch', 
                        array(
                                'servers' => $servers
                        ))
        );
        return View::make('main', $data);
    }

    public function userDeviceSearchData()
    {
        $start_time = Input::get('start_time');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        if(2 != $game->game_type){
            return Response::json(array('error'=>'请检测当前游戏是否是手游!'), 403);
        }
        $end_time = Input::get('end_time');
        $interval = (int) Input::get('interval');
        $check_type = (int) Input::get('check_type');
        $serach_type = (int)Input::get('serach_type');
        $channel_type = (int)Input::get('channel_type');
        $server_internal_id = Input::get('server_internal_id');
        $platform_id = Session::get('platform_id');
        $interl_time = array(
                600,
                3600,
                86400,
                0
        );
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);

        $user = array();
        $user['start_time'] = $start_time;
        $user['end_time'] = $end_time;
        $user['platform_id'] = $platform_id;
        $user['interval'] = $interl_time[$interval];
        $user['check_type'] = $check_type;
        $user['game_id'] = $game_id;
        $user['serach_type'] = $serach_type;
        $user['channel_type'] = $channel_type;

        $servers = $this->getUnionServers();
        $server_internal_ids = array();
        if ($server_internal_id[0] == "0"){//全服
            foreach ($servers as $server) {
                $server_internal_ids[] = $server->server_internal_id;
            }
        }else{
            foreach ($server_internal_id as $server) {
                    $server_internal_ids[] = $server;
            }
        }

        // $user['server_internal_id'] = $server_internal_id;
        // $tmp_result = $api->getUserInfoDevice($user);
        $allserver = array();
        $server_result = array();
        foreach ($server_internal_ids as $sid) {
            $server_tmp_result = $api->getDevicePlayerInfo($start_time,$end_time, $platform_id, $interl_time[$interval], $check_type, $game_id, $sid);
            $body =  $server_tmp_result->body;
            if('200' == $server_tmp_result->http_code){
                $server_result[$sid] = $body;
                foreach ($server_result[$sid] as $key => $value) {
                    if(isset($allserver[$value->date.'+'.$value->os_type])){
                        $allserver[$value->date.'+'.$value->os_type]['playernum'] += $value->playernum;
                        $allserver[$value->date.'+'.$value->os_type]['payment'] += $value->payment;
                    }else{
                        $allserver[$value->date.'+'.$value->os_type] = array(
                            'server_name' => 'Total',
                            'date' => $value->date,
                            'usernum'    =>  $value->usernum,
                            'signupnum'    =>  $value->signupnum,
                            'playernum'    =>  $value->playernum,
                            'payment'  =>  $value->payment,
                            'os_type'    => $value->os_type,
                            );
                    }
                }
            }
        }
        $tmp = array();
        $servers = array();
        
        foreach ($server_result as $key => $value) {
            foreach ($value as $key1 => $v1) {
                $tmp[] = $v1;
            }
            foreach ($tmp as $key2 => $v2) {
                if($v2->playernum == 0 && $v2->payment == 0)
                unset($tmp[$key2]);
            }
            $servers[$key] = array_values(array_reverse($tmp));
            unset($tmp);
        }
        // if ($tmp_result->http_code != 200)
        // {
        //     return Response::json(array('error'=>$tmp_result->body), 403);
        // }
        $allserver = array_values(array_reverse(array_values($allserver)));
        $result = array(
            'allserver' => $allserver,
            'servers'   => $servers,
            );

            return Response::json($result);
    }

    public function consumptionRankIndex()
    {
        $servers = Server::CurrentGameServers()->get();
        $data = array(
                'content' => View::make('slaveapi.user.consumption',array(
                    'servers' => $servers,
                    ))
        );
        return View::make('main', $data);
    }

    public function consumptionRankData(){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $interval = (int) Input::get('interval');
        $server_id = (int)Input::get('server_id');

        $rank = Input::get('rank');
        if(empty($rank)){
            $rank = 10;//默认显示前10
        }

        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);
        $currency_id = $platform->default_currency_id;

        if(0 == $server_id){
            $platform_server_id = 0;
            $server_internal_id = 0;
        }else{
            $server = Server::find($server_id);
            $platform_server_id = $server->platform_server_id;
            $server_internal_id = $server->server_internal_id;
        }

        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $response = $slave_api->getConsumptionRank($game_id, $platform_id, $interval, $currency_id, $start_time, $end_time, $rank, $platform_server_id, $server_internal_id);
        if($response->http_code == 200){
            $result = array();
            $body = $response->body;
            /*for($i=0; $i<count($body); $i++) {//一个日期一行显示
               $temp_result = array();
               for($j=0; $j<count($body[$i]); $j++) {
                    $temp_result['time'.$j] = $body[$i][$j]->time;
                    $temp_result['uid'.$j] =  $body[$i][$j]->pay_user_id;
                    $temp_result['total_dollar_amount'.$j] = $body[$i][$j]->total_dollar_amount;
               }
               if(!empty($temp_result)){
                    $result[] = $temp_result;
               }
               unset($temp_result);

            }*/
            for($i=0; $i<count($body); $i++){
                if(!empty($body[$i])){

                    if(0 == $interval){
                        $title = $body[$i][0]->time;
                    }elseif(1 == $interval){
                        $title = $body[$i][0]->time.Lang::get('slave.where_week');
                    }elseif(2 == $interval){
                        $title = date('Y-m',strtotime($body[$i][0]->time));
                    }elseif(3 == $interval){
                        $title = 'All';
                    }
                   $result[] = array(
                        'title' => $title,
                        'res' => $body[$i],
                   );
                }
            }
            return Response::json($result);
        }else {
            return Response::json(array('error'=>'查询出现错误！'),403);
        }
        
    }

    public function basicCountIndex()
    {
        $data = array(
                'content' => View::make('slaveapi.user.basiccount')
        );
        return View::make('main', $data);
    }

    public function basicCountData(){
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $interval = (int) Input::get('interval');
        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);
        $currency_id = $platform->default_currency_id;
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if($start_time>$end_time){
           return Response::json(array('error'=>'开始时间不能大于结束时间！'),403); 
        }
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $response = $slave_api->getbasicCount($game_id, $platform_id, $interval, $currency_id, $start_time, $end_time);
        if($response->http_code == 200){
            $result = array();
            $body = $response->body;
            if(!empty($body)){
                $result = $body;
            }else{
                return Response::json(array('error'=>'未查询到数据！'),403);
            }
            return Response::json($result);
        }else {
            return Response::json(array('error'=>'查询出现错误！'),403);
        }
        
    }

    public function vipplayersIndex(){
        $data = array(
                'content' => View::make('specialplayer.vipplayers', array())
        );
        return View::make('main', $data);
    }

    public function vipplayersmodify(){
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $type = Input::get('type');

        if('add' == $type){
            $count = 0;
            $vipplayers = Input::get('new_players');
            if($vipplayers){
                $vipplayers = explode("\n", $vipplayers);
            }else{
                $vipplayers = array();
            }
            $data2store = array(
                'game_id' => $game_id,
                'created_time' => time(),
                'user_name' => Auth::user()->username,
                'type' => 1,
                );
            $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
            foreach ($vipplayers as $vipplayer) {
                $try = SpecialPlayers::where('game_id', $game_id)->where('player_id', $vipplayer)->where('type', 1)->first();
                if(!$try){
                    $data2store['player_id'] = $vipplayer;
                    $data2store['player_name'] = '';
                    $slavecreateplayer = $slave_api->create_player_get($game_id, $platform_id, $data2store['player_id']);
                    if(200 == $slavecreateplayer->http_code){
                        $data2store['player_name'] = isset($slavecreateplayer->body->player_name) ? $slavecreateplayer->body->player_name : '';
                    }
                    SpecialPlayers::insert($data2store);
                    $count++;
                }
                unset($data2store['player_id']);
            }

            return Response::json(array('msg'=>'Insert Success '.$count.' players'));
        }

        if('delete' == $type){
            $id = (int)Input::get('id');
            if($id){
                SpecialPlayers::where('id', $id)->where('game_id', $game_id)->where('type', 1)->delete();
                return Response::json(array('msg'=>'Delete Success!'));
            }else{
                return Response::json(array('error'=>'No such player In Current Game!'), 403);
            }
        }

        if('check' == $type){
            $page = Input::get('page');
            $page = $page > 0 ? $page : 1;
            $array2return = array();
            $total = SpecialPlayers::where('game_id', $game_id)->where('type', 1)->count(); 
            $players = SpecialPlayers::where('game_id', $game_id)->where('type', 1)
                               ->selectRaw('id, player_id, player_name, from_unixtime(created_time) as created_time, user_name')->orderBy('created_time')->forpage($page, 50)->get(); 
            foreach ($players as $player) {
                $array2return[] = array(
                    'id' => $player->id,
                    'player_id' => $player->player_id,
                    'player_name' => $player->player_name,
                    'created_time' => $player->created_time,
                    'user_name' => $player->user_name,
                    );
                unset($player);
            } 

            $result = array(
                'players' => $array2return,
                'count' => $total,
                'current_page' => $page,
                );
            return Response::json($result);       
        }
    }

    public function deviceuserIndex(){
        $servers = $this->getUnionServers();
        $data = array(
                'content' => View::make('slaveapi.user.deivceUser', array(
                    'servers' => $servers,
                    ))
        );
        return View::make('main', $data);
    }

    public function deviceuser(){
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $server_id = Input::get('server_id');
        $data_type = Input::get('data_type');

        if(!$server_id && 'level' == $data_type){
            return Response::json(array('error' => 'Please select a server'), 401);
        }else{
            if($server_id){
                $server = Server::find($server_id);
                $server_internal_id = $server->server_internal_id;
            }else{
                $server_internal_id = 0;
            }
        }

        $device_ids = Input::get('device_ids');
        $device_ids = explode("\n", $device_ids);
        foreach ($device_ids as $key => $value) {
            $device_ids[$key] = trim($value);
            if(!$device_ids[$key]){
                unset($device_ids[$key]);
            }
        }
        $device_ids = array_unique($device_ids);

        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        $result = $slave_api->getDataByDeviceids($game_id, $platform_id, $server_internal_id, $data_type, $device_ids);

        if(200 != $result->http_code){
            return $slave_api->sendResponse();
        }
        $view_data = array(
            'keys' => array(),
            'values' => array(),
            );
        $view_data['values'] = array(); 
        $view_key = 0;
        foreach ($result->body as $key => $value) {
            $tmp = array();
            foreach ($value as $k => $v) {
                $tmp[] = $v;
            }
            $view_data['values'][$view_key] = $tmp;
            $view_key++;
            unset($tmp);
        }

        if('create' == $data_type){
            $view_data['keys'] = array(
                0 => Lang::get('slave.sum_register_player'),
                1 => Lang::get('slave.sum_player_create'),
            );
        }

        if('level' == $data_type){
            $view_data['keys'] = array(
                0 => Lang::get('slave.level'),
                1 => Lang::get('slave.player_nums'),
            );
        }

        if('order' == $data_type){
            $view_data['keys'] = array(
                0 => Lang::get('slave.recharge_number'),
                1 => Lang::get('slave.recharge_count'),
                2 => Lang::get('slave.pay_amount_dollar'),
            );
        }

        if(count($view_data['values'])){
            return Response::json($view_data);
        }else{
            return Response::json(array('error' => 'Not a support type'), 401);
        }
    }

    public function CalculateRetentionIndex(){  //此功能用来统计一段时间内创建的玩家在另一段时间内是否登陆，特殊的，德扑可以用来限制是否玩牌
        $servers = $this->getUnionServers();
        $game_code = Game::find(Session::get('game_id'))->game_code;
        $data = array(
                'content' => View::make('slaveapi.user.calculateretention', array(
                    'servers' => $servers,
                    'game_code' => $game_code,
                    ))
        );
        return View::make('main', $data); 
    }

    public function CalculateRetention(){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');

        //获取页面传入值
        $server_id = Input::get('server_id');
        if(!$server_id){
            return Response::json(array('error' => 'Please select a server.'), 401);
        }else{
            $server = Server::find($server_id);
            if($game_id != $server->game_id){
                return Response::json(array('error' => 'Please refresh the page.'), 401);
            }
            $server_internal_id = $server->server_internal_id;
        }

        $by_create_time = Input::get('by_create_time'); //三个值，0,1,2分别代表不限制创建时间，限制创建时间，限制创建时间且充值
        if($by_create_time){
            $create_start_time = $this->current_time_nodst(strtotime(trim(Input::get('create_start_time'))));
            $create_end_time = $this->current_time_nodst(strtotime(trim(Input::get('create_end_time'))));
        }else{
            $create_start_time = 0;
            $create_end_time = 0;
        }

        $by_what_time = Input::get('by_what_time'); //两个值login,play分别代表限制登录和限制玩牌（德扑用）
        $login_start_time = $this->current_time_nodst(strtotime(trim(Input::get('login_start_time'))));
        $login_end_time = $this->current_time_nodst(strtotime(trim(Input::get('login_end_time'))));
        $by_last_login_time = Input::get('by_last_login_time');
        $last_login_time = $this->current_time_nodst(strtotime(trim(Input::get('last_login_time'))));
        if(!($last_login_time >= $login_start_time && $last_login_time <= $login_end_time)){
            return Response::json(array('error' => Lang::get('slave.last_login_time_limit')), 401);
        }

        $interval = (int)Input::get('interval'); //时间间隔天数

        //调用slave接口
        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $data2slave = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'by_create_time' => $by_create_time,
            'create_start_time' => $create_start_time,
            'create_end_time' => $create_end_time,
            'by_what_time' => $by_what_time,
            'login_start_time' => $login_start_time,
            'login_end_time' => $login_end_time,
            'interval' => $interval,
            'by_last_login_time' => $by_last_login_time,
            'last_login_time' => $last_login_time,
        );
        $result = $slave_api->CalculateRetention($data2slave);
        
        if(200 != $result->http_code){
            return $slave_api->sendResponse();
        }

        //处理返回数据
        $result = $result->body;

        foreach ($result->date_result as &$value) {
            if(isset($value->count_start_time)){
                $value->count_start_time = date("Y-m-d H:i:s", $value->count_start_time);
                $value->count_end_time = date("Y-m-d H:i:s", $value->count_end_time);
            }
        }

        return Response::json($result);
    }

    //查询平台--服下GM回复信息
    public function gmMessageReply(){
        $servers = $this->getUnionServers();
        $data = array(
            'content' => View::make('slaveapi.user.gmMessageReply', array('servers' => $servers))
            );
        return View::make('main', $data);         
    }

        //查询平台--服下GM回复消息响应
    public function gmMessageReplyData(){
        $game_id = Session::get('game_id');
        $server_id = Input::get('server_id');
        $gm_name = Input::get('gm_name');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $result = array();
        $gm_result = array();
        if (empty($game_id) || empty($server_id)) {
            return Response::json(array('error'=>'请选择正确的Game或者服务器'), 404);
        }
        if(in_array($game_id, Config::get('game_config.mobilegames'))){ //手游的数据保存问题与回复属于不同的一条记录，所以 需要额外处理
                        if (!is_array($server_id)) {
                $server_id = explode(',',$server_id);
            }
            //查询出手游平台-服下提问总数、回复总数、回复率
            if(!in_array(0,$server_id)){//选择了部分服务器
                $server_id_str = implode(',', $server_id);
                $where_server = " and `s`.`server_id` in (".$server_id_str.")";
            }else{//选择了全服
                $where_server = "";
            }

            $sql = "select server_name,sum(if(`is_done`=0,1,0)) question,sum(`is_done`) as answer from (
                        select gm.*,u.username,s.server_name,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date from `gm` 
                        join `users` as `u` on `u`.`user_id` = `gm`.`user_id` 
                        join `servers` as `s` on `s`.`server_id` = `gm`.`server_id`
                        where `s`.`game_id` = ".$game_id." and `send_time` between ".$start_time." and ".$end_time.$where_server."
                        group by `player_id`, `date`, `gm`.`server_id`, `gm`.`user_id`
                    ) p
                    group by `server_id`";
            $messages = DB::select(DB::raw($sql));

            foreach ($messages as $val) {
                $result[] = array(
                    'server_name' => $val->server_name, 
                    'question' => $val->question,
                    'answer' => $val->answer,
                    'rate' => round($val->answer/$val->question*100,2).'%'
                );
            }

            /*查询出GM的回复消息--首先查出所有玩家提出的问题，再查询出所有GM回答的问题，通过player_id来join两个结果集,然后再对结果集进行以GM分组，通过分组的结果使用两个结果集的send_time作差取平均值得到平均回复时间，*/
            $sql = "select gm_id,player_id,send_time,`gm`.`server_id`,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date from `gm`
                    join `servers` as `s` on `s`.`server_id` = `gm`.`server_id` 
                    where `is_done` = 0 and `s`.`game_id` = ".$game_id." and `send_time` between ".$start_time." and ".$end_time.$where_server." 
                    group by `player_id`, `date`, `gm`.`server_id`";
            $gm_questions = DB::select(DB::raw($sql));

            //根据玩家问题循环查出相应的GM的回复记录（提问后三天内没有gm回复就视为无人回复）
            $question_id_arr = array();
            foreach ($gm_questions as $gm_q) {
                $question_id_arr[] = $gm_q->gm_id;
            }
            $question_id_str = implode(',', $question_id_arr);

            $sql = "select  from `gm` q
                    left join `gm` a on q.player_id=a.player_id and a.is_done=1 and a.send_time>q.send_time and a.send_time<q.send_time+259200
                    where q.gm_id in (".$question_id_str.")
                    group by q.gm_id";
            $gm_answers = DB::select(DB::raw($sql));

            foreach ($gm_answers as $gm_q) {
                
            }
        }else{
            //查询出页游平台-服下提问总数与回复总数
            if (!is_array($server_id)) {
               $server_id = explode(',',$server_id);
            }
            if (in_array(0,$server_id)) {//当选中全部服务器时
                $server_id = DB::select(DB::raw("
                        select server_id from servers where game_id = ".$game_id."
                    "));
                $server_id = implode(',', $server_id);
                $messages = DB::select(DB::raw("select server_name,count(`is_done`) question,sum(`is_done`) as answer from (select gm.*,u.username,s.server_name,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date from `gm` left join `users` as `u` on `u`.`user_id` = `gm`.`user_id` left join `servers` as `s` on `s`.`server_id` = `gm`.`server_id` 
                    where `gm`.`server_id` in (".$server_id.") and `s`.`game_id` = ".$game_id." and `send_time` between ".$start_time." and ".$end_time." group by `player_id`, `date`, `gm`.`server_id`, `gm`.`user_id`) p group by `server_id`"));
            }else{//选中一个或者某些服务器时
                $server_id = implode(',', $server_id);
                $messages = DB::select(DB::raw("select server_name,count(`is_done`) question,sum(`is_done`) as answer from (select gm.*,u.username,s.server_name,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date from `gm` left join `users` as `u` on `u`.`user_id` = `gm`.`user_id` left join `servers` as `s` on `s`.`server_id` = `gm`.`server_id` where `gm`.`server_id` in (".$server_id.") and  `s`.`game_id` = ".$game_id." and `send_time` between ".$start_time." and ".$end_time." group by `player_id`, `date`, `gm`.`server_id`, `gm`.`user_id`) p group by `server_id`"));
                $server_id = explode(',', $server_id);
            }
            foreach ($messages as $val) {
                $result[] = array(
                    'server_name' => $val->server_name, 
                    'question' => $val->question,
                    'answer' => $val->answer,
                    'rate' => round($val->answer/$val->question*100,2).'%'
                    );
            }
           //查询出页游平台下GM的回复情况
            if (in_array(0,$server_id)) {//当选中全部服务器时
                $server_id = DB::select(DB::raw("
                        select server_id from servers where game_id = ".$game_id."
                    "));
                $server_id = implode(',', $server_id);
                if (empty($gm_name)) {//输入了GM的名字时
                   $gm_messages = DB::select(DB::raw("
                     select username,AVG(`avg_time`) avg_time,sum(`gm_answer`) as answer from (
                         select username,AVG(replied_time-send_time) as avg_time,sum(`is_done`) as gm_answer,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date from `gm` 
                         left join `users` as `u` on `u`.`user_id` = `gm`.`user_id` 
                         left join `servers` as `s` on `s`.`server_id` = `gm`.`server_id` 
                         where `gm`.`server_id` in (".$server_id.") and `s`.`game_id` = ".$game_id." and `send_time` between ".$start_time." and ".$end_time."
                         group by `username`, `player_id`, `date`) p
                     group by username"));
                }else{//为输入专门要查询的GM的名字时
                    $gm_messages = DB::select(DB::raw("
                     select username,AVG(`avg_time`) avg_time,sum(`gm_answer`) as answer from (
                         select username,AVG(replied_time-send_time) as avg_time,sum(`is_done`) as gm_answer,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date from `gm` 
                         left join `users` as `u` on `u`.`user_id` = `gm`.`user_id` 
                         left join `servers` as `s` on `s`.`server_id` = `gm`.`server_id` 
                         where `gm`.`server_id` in (".$server_id.") and `username` = '".$gm_name."' and `s`.`game_id` = ".$game_id." and `send_time` between ".$start_time." and ".$end_time."
                         group by `username`, `player_id`, `date`) p
                     group by username"));
                }
            }else{//选中一个或者某些服务器时
                $server_id = implode(',', $server_id);
                if (empty($gm_name)) {//输入了GM的名字时
                   $gm_messages = DB::select(DB::raw("
                     select username,AVG(`avg_time`) avg_time,sum(`gm_answer`) as answer from (
                         select username,AVG(replied_time-send_time) as avg_time,sum(`is_done`) as gm_answer,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date from `gm` 
                         left join `users` as `u` on `u`.`user_id` = `gm`.`user_id` 
                         left join `servers` as `s` on `s`.`server_id` = `gm`.`server_id` 
                         where `gm`.`server_id` in (".$server_id.") and `send_time` between ".$start_time." and ".$end_time."
                         group by `username`, `player_id`, `date`) p
                     group by username"));
                }else{//为输入专门要查询的GM的名字时
                    $gm_messages = DB::select(DB::raw("
                     select username,AVG(`avg_time`) avg_time,sum(`gm_answer`) as answer from (
                         select username,AVG(replied_time-send_time) as avg_time,sum(`is_done`) as gm_answer,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date from `gm` 
                         left join `users` as `u` on `u`.`user_id` = `gm`.`user_id` 
                         left join `servers` as `s` on `s`.`server_id` = `gm`.`server_id` 
                         where `username` = '".$gm_name."' and `gm`.`server_id` in (".$server_id.") and `send_time` between ".$start_time." and ".$end_time."
                         group by `username`, `player_id`, `date`) p
                     group by username"));
                }
                 
            }
            //测试数据
            /*$gm_message = array(
                array('username' => 'admin', 'avg_time' => '36', 'gm_answer' => '6'), 
                array('username' => 'yuhuaci', 'avg_time' => '32', 'gm_answer' => '30'), 
                );*/
            foreach ($gm_messages as $val) {
                $avg_time = $this->Sec2Time($val->avg_time);
                $gm_result[] = array(
                    'username' => $val->username, 
                    'avg_time' => $avg_time,
                    'gm_answer' => $val->answer,
                    );
            } 

        }

        if(count($result) > 0 || count($gm_result) > 0){
                return Response::json(array('result' => $result, 'gm_result' => $gm_result));
            }else{
                return Response::json(array('error'=>'没有匹配的结果'), 404);
        }
    }
}