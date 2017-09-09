<?php

class MGActivityController extends \BaseController{

    private function getGiftbags(){
        $game = Game::find(Session::get('game_id'));
        $giftbags = $this->OpenFile(public_path() . '/table/' . $game->game_code . '/item.txt');    //礼包数据来源是item文件
        $giftbag_array = array();
        foreach ($giftbags as $value) {
            $giftbag_array[$value->id] = $value->name;
        }
        unset($giftbags);
        return $giftbag_array;
    }

    private function getFlashSaleTypes(){   //皮肤礼包暂时不可用
        return array(
            1 => Lang::get('serverapi.equipment_giftbag'),
            //2 => Lang::get('serverapi.skin_giftbag'),
            3 => Lang::get('serverapi.normal_giftbag'),
            );
    }

    public function FlashSaleIndex(){   //限时抢购，目前只有夜夜三国有
        $servers = $this->getUnionServers();
        $flash_types = $this->getFlashSaleTypes();
        $giftbags = $this->getGiftbags();
        $data = array(
            'content' => View::make('serverapi.yysg.activity.flashsale', array(
                'servers' => $servers,
                'flash_types' => $flash_types,
                'giftbags' => $giftbags,
                ))
        );
        return View::make('main', $data);
    }

    public function FlashSaleOperate(){ //限时抢购，目前只有夜夜三国有
        $flash_types = $this->getFlashSaleTypes();
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $type = Input::get('type');
        $server_ids = Input::get('server_ids');
        if(!is_array($server_ids)){
            return Response::json(array('error' => 'Please Select at least one server.'), 401);
        }
        if('set' == $type){ //设置内容
            //获取页面传入的值，并检测是否必要的条件都输入
            foreach ($flash_types as $id => $name) {    //循环查找所有类型的礼包
                if(Input::get('flash_types_'.$id)){ //为真说明有这种类型的礼包
                    $start_time = strtotime(Input::get('start_time_'.$id));
                    $end_time = strtotime(Input::get('end_time_'.$id));
                    $price = (int)Input::get('price_'.$id);
                    $limit_count = (int)Input::get('limit_count_'.$id);
                    $player_limit_count = (int)Input::get('player_limit_count_'.$id);
                    if(!($price*$limit_count*$player_limit_count)){
                        return Response::json(array('error' => 'Not Enough Input.'), 401);
                    }
                    $giftbag_ids = Input::get('giftbag_ids_'.$id);
                    if(!(is_array($giftbag_ids) && count($giftbag_ids))){
                        return Response::json(array('error' => 'Please Select at least one giftbag.'), 401);
                    }
                    $giftbag_id_str = '';
                    foreach ($giftbag_ids as $giftbag_id) {
                        $giftbag_id_str .= $giftbag_id.'|';
                    }
                    $giftbag_id_str = substr($giftbag_id_str, 0, strlen($giftbag_id_str)-1);
                    //将合法的页面传入值整理准备调用游戏接口
                    $params['infos'][] = array(
                        'qianggou_type' => $id,
                        'giftbag_id' => $giftbag_id_str,
                        'limit_count' => $limit_count,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'player_limit_count' => $player_limit_count,
                        'price' => $price,
                    );
                }
            }
            if(!isset($params['infos'])){
                return Response::json(array('error' => 'No Type Selected.'), 401);
            }
            //初始化结果数组，准备调用游戏接口
            $result2view = array(
                'success' => '',
                'fail' => '',
                );
            foreach ($server_ids as $server_id) {
                $server = Server::find($server_id);
                if(!$server){
                    $result2view['fail'] .= 'server_id:'.$server_id.',';
                }

                $server_api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

                $server_result = $server_api->setFlashSaleContent($params);

                if(isset($server_result->result) && 'OK' == $server_result->result){    //标准格式的返回添加进成功数组
                    $result2view['success'] .= $server->server_name.',';
                }else{  //否则添加进失败数组
                    $result2view['fail'] .= $server->server_name.',';
                    Log::info('MGActivityController--FlashSaleOperate--'.$server->server_name.'--'.var_export($server_result, true));
                }
            }
            if($result2view['success']){
                $result2view['success'] .= 'OK';
            }
            if($result2view['fail']){
                $result2view['success'] .= 'Fail';
            }
            return Response::json($result2view);
        }
        if('get' == $type){ //查看内容
            $giftbags = $this->getGiftbags();
            $server_id = $server_ids[0];
            $server = Server::find($server_id);
            if(!$server){
                return Response::json(array('error' => 'No Such Server.'), 401);
            }

            $server_api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

            $result = $server_api->getFlashSaleContent();

            if($result && !is_array($result)){
                Log::info('MGActivityController--FlashSaleOperate--'.var_export($result, true));
                return Response::json(array('error' => Lang::get('slave.bad_return')), 401);
            }

            if(count($result)){ //这里将游戏返回值做简单处理便于阅读
                foreach ($result as &$value) {
                    $value->start_time = date("Y-m-d H:i:s", $value->start_time);
                    $value->end_time = date("Y-m-d H:i:s", $value->end_time);
                    $giftbag_ids = explode('|', $value->giftbag_id);
                    $tmp_str = '';
                    foreach ($giftbag_ids as $giftbag_id) {
                        $tmp_str .= (isset($giftbags[$giftbag_id]) ? $giftbags[$giftbag_id] : '$giftbag_id').',';
                    }
                    $value->giftbags = $tmp_str;
                    $value->flash_types = isset($flash_types[$value->qianggou_type]) ? $flash_types[$value->qianggou_type] : $value->qianggou_type;
                }
                return Response::json($result);
            }else{
                return Response::json(array('error' => 'Result:Empty.'), 401);
            }
        }
    }
}