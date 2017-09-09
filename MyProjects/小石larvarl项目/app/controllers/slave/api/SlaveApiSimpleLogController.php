<?php

class SlaveApiSimpleLogController extends \BaseController {
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

    public function MergeGemIndex(){
        $servers = Server::currentGameServers()->get();
        $data = array('content' => View::make('slaveapi/player/mergegem',array(
                'servers' => $servers,
            ))
        );
        return View::make('main',$data);
    }

    public function getMergeGemData(){
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server_id = Input::get('server_id');
        if (! $server_id)
        {
            return Response::json(array('error' => Lang::get('server.select_server')), 403);
        }
        $server = Server::find($server_id);
        if(!$server){
            return Response::json($msg, 403);
        }

        $id_or_name = Input::get('id_or_name');
        $player = Input::get('player');
        $start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));

        $table_f = $this->initTable('fengling');
        $fenglings = array();
        foreach ($table_f as $fengling) {
            $fenglings[$fengling->id] = $fengling->name;
        }
        $table_m = $this->initTable('game_message');
        $messages = array();
        foreach ($table_m as $message) {
            $messages[$message->id] = $message->desc;
        }

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);

        if(2 == $id_or_name && $player){
            $players = $api->getplayeridbyname($game_id, $player, $server_internal_id, $platform_id);
            if(200 == $players->http_code){
                $player = (int)$players->body[0]->player_id;
            }else{
               return Response::json(array('error' => $player . 'Not Found player_id'), 403);
            } 
        }

        $response = $api->getMergeGemData($game_id, $start_time, $end_time, $player, $server->server_internal_id);
        if(200 != $response->http_code){
           return Response::json($response->body, $response->http_code); 
        }

        foreach ($response->body as $v) {
            $temp_result = array(
                'player_id' => $v->player_id,
                'main_id' => isset($fenglings[$v->main_id]) ? $fenglings[$v->main_id] : $v->main_id,
                'secondary_id' => isset($fenglings[$v->secondary_id]) ? $fenglings[$v->secondary_id] : $v->secondary_id,
                'action_type' => isset($messages[$v->action_type]) ? $messages[$v->action_type] : $v->action_type,
                'time' => date('Y-m-d H:i:s',$v->time),
            );
            
            $result[] =  $temp_result;
            unset($temp_result);
        }
        return Response::json($result);

    }

    public function operationIndex(){
        $servers = Server::currentGameServers()->get();
        $data = array('content' => View::make('slaveapi/player/operation',array(
                'servers' => $servers,
            ))
        );
        return View::make('main',$data);
    }

    public function getOperationData(){
        $msg = array(
                'code' => Lang::get('errorcode.unknown'),
                'msg' => Lang::get('errorcode.server_not_found')
        );
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server_ids = Input::get('server_id');
        if('0' == $server_ids){
            return Response::json(array('error'=>'Did you select a server?'), 403);
        }
        $operation_id = Input::get('operation_id');
        if(!$operation_id){
            return Response::json(array('error'=>'Did you enter operationId?'), 403);
        }

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $total_num = 0; $total_lev=0;
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id); 
            if(! $server)
            {
                $msg['error'] = Lang::get('error.basic_not_found');
                return Response::json($msg, 404);
            }
            $response = $api->getOperationData($game_id,$operation_id, $server->server_internal_id);
            if(200 == $response->http_code){
               $body = $response->body;
               $total_num = $total_num+$body[0]->num;
               $total_lev = $total_lev+$body[0]->lev;
               $result[] = array(
                    'server_name' => $server->server_name,
                    'num' => $body[0]->num,
                    'lev' => round($body[0]->lev),
                ); 
            }else{
                return Response::json($response->body, $response->http_code);
            }
        }
        array_unshift($result, array(
            'server_name' => 'TOTAL',
            'num' => $total_num,
            'lev' => round($total_lev/count($result)),
            ));
        return Response::json($result);

    }

    public function formationIndex(){
        $servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $heros = Lang::get('mnsgwj');
        foreach ($heros as $k => $v) {
            $tmp_k = substr_replace($k, '1', 1, 1);
            $tem_v = '觉醒-'.$v;
            $tem_heros[$tmp_k] = $tem_v;
        }
        $heros = $heros+$tem_heros;
        
        $data = array(
            'content' => View::make('slaveapi.summon.mnsgformation', array(
                'servers' => $servers,
                'heros' => $heros,
            ))
        );
        return View::make('main', $data);
    }

    public function formationData(){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $server_ids = Input::get('server_id');
        if(empty($server_ids)){
            return Response::json(array('error'=>'Did you select a server?'),403);
        }
        $formation1 = Input::get('formation1');
        $formation2 = Input::get('formation2');
        $formation3 = Input::get('formation3');
        $formation4 = Input::get('formation4');
        $formation5 = Input::get('formation5');

        $params['search_type'] = Input::get('search_type');
        if((1 == $params['search_type'] || 2 == $params['search_type'])){
            $params['formation'] = $formation1.'|'.$formation2.'|'.$formation3.'|'.$formation4.'|'.$formation5;
            if(strlen($params['formation'])<40){
                return Response::json(array('error'=>'查询阵容必须选择5个英雄！'),404);
            }
        }
        $params['game_id'] = $game_id; 
        $params['start_time'] = strtotime(trim(Input::get('start_time')));
        $params['end_time'] = strtotime(trim(Input::get('end_time')));
        $params['vip'] = Input::get('vip');
        $params['player_lev'] = Input::get('player_lev');
        $params['hero_id'] = Input::get('hero_id');
        $params['hero_type'] = Input::get('hero_type');
        $params['formation_type'] = Input::get('formation_type');

        if(in_array($params['search_type'], array(3,4,5)) && empty($params['hero_id'])){
            return Response::json(array('error'=>'请选择英雄！'),404);
        }
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $all_total = 0;
        $all_count = 0;
        $appear_total = 0;
        $win_total = 0;
        $all_total_appear = 0;
        $data = array();
        foreach ($server_ids as $server_id) {
            $server = Server::find($server_id);
            if(!$server){
                continue;
            }
            $params['server_internal_id'] = $server->server_internal_id;
            $response = $api->getFormationData($params);
            if(5 == $params['search_type']){
                $heros = Lang::get('mnsgwj');
                foreach ($heros as $k => $v) {
                    $tmp_k = substr_replace($k, '1', 1, 1);
                    $tem_v = '觉醒-'.$v;
                    $tem_heros[$tmp_k] = $tem_v;
                }
                $heros = $heros+$tem_heros;
                if(200 == $response->http_code){
                    $body = $response->body;
                    $total = $body->total;
                    $hero_appear = $body->count->hero_appear;
                    $hero_win = $body->count->hero_win;
                    $all_total_appear = $all_total_appear+$total/5;
                    if(!empty($hero_appear)){
                        $tem_appear_total = $total;
                        foreach ($hero_appear as $a) {
                            if(isset($data[$a->hero_id])){
                                $data[$a->hero_id]['appear_total'] = $data[$a->hero_id]['appear_total']+$tem_appear_total/5;
                                $data[$a->hero_id]['hero_appear'] = $data[$a->hero_id]['hero_appear']+$a->hero_appear;
                            }else{
                                $data[$a->hero_id]['appear_total'] = $tem_appear_total/5;
                                $data[$a->hero_id]['win_total'] = 0;
                                $data[$a->hero_id]['hero_id'] = $a->hero_id;
                                $data[$a->hero_id]['hero_appear'] = $a->hero_appear;
                                $data[$a->hero_id]['hero_win'] = 0;
                            }
                            //$tem_appear_total = 0;//同一个服的total只计算一次
                            
                        }
                    }
                    if(!empty($hero_win)){
                        $tem_win_total = $total;
                        foreach ($hero_win as $w) {
                            if(isset($data[$w->hero_id])){
                                $data[$w->hero_id]['win_total'] = $data[$w->hero_id]['win_total'] + $tem_win_total;
                                $data[$w->hero_id]['hero_win'] = $data[$w->hero_id]['hero_win']+$w->hero_win;
                            }else{
                                $data[$w->hero_id]['appear_total'] = 0;
                                $data[$w->hero_id]['win_total'] = $tem_win_total;
                                $data[$w->hero_id]['hero_id'] = $w->hero_id;
                                $data[$w->hero_id]['hero_appear'] = 0;
                                $data[$w->hero_id]['hero_win'] = $w->hero_win;
                            }
                           // $tem_win_total = 0;  
                        }
                    }
                }
               // return Response::json($data);
            }else{
                if(200 == $response->http_code){
                    $body = $response->body;
                    if($body){
                        $all_total = $all_total + (int)$body->total; 
                        $all_count = $all_count + (int)$body->count;
                        $data[] = array(
                            'server_name' => $server->server_name,
                            'total' => $body->total,
                            'count' => $body->count,
                        );
                        
                    }
                }
            } 
        }
        if(5 == $params['search_type']){
            foreach ($data as $k => $v) {
                $data[$k]['all_total_appear'] = $all_total_appear;
                $data[$k]['hero_name'] = isset($heros[$k]) ? $heros[$k] : $v['hero_id'];
            }
            return Response::json($data);
        }
        $all_data = array(
            'server_name' => 'Total',
            'total' => $all_total,
            'count' => $all_count,
        );
        array_unshift($data, $all_data);
        return Response::json($data);

    }

    //美人猜猜猜日志查询
    public function belleLogIndex(){//Log查询
        $servers = Server::currentGameServers()->get();
        $data = array(
            'content'=> View::make('slaveapi.player.bellelog',array('servers' => $servers))
        );
        return View::make('main',$data);
    }

    //美人猜猜猜日志查询提交
    public function belleLogSearch(){
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        //获取提交表单信息
        $server_id = ( int ) Input::get('server_id');
        $choice = ( int ) Input::get('choice');       
        $id_or_name = Input::get('id_or_name');
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $server = Server::find($server_id);
        $server_internal_id = 0;
        if($server)
        {
            $server_internal_id = $server->server_internal_id;
        }
        $servers = array();
        $game_id = (int)Session::get('game_id');
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $platform = Platform::find(Session::get('platform_id'));
        $table_m = $this->initTable('game_message');

        $messages = array();
        foreach ($table_m as $message) {
            $messages[$message->id] = $message->desc;
        }

        $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
        if(!$server_internal_id){
                return Response::json(array('error'=>'Please Select a Server.'), 401);
        }
        if ($choice == 0) { //根据玩家昵称查询
            //根据玩家昵称查询到玩家的ID
            $response = $slave_api->getplayeridbyname($game_id, $id_or_name, $server_internal_id,$platform_id);
            if($response->http_code != 200)
            {
                return Response::json($response->body, $response->http_code);
            }else{
                $player_id = $response->body[0]->player_id;
                //查询玩家美人猜猜猜日志信息
                $playerRes = $slave_api->getguessData($game_id,$server_internal_id,$server_id,$player_id,$start_time,$end_time);
            }
            
        }else if($choice == 1){ //根据玩家ID进行查询
            $playerRes = $slave_api->getguessData($game_id,$server_internal_id,$server_id,$id_or_name,$start_time,$end_time);
        }

        if($playerRes->http_code != 200)
        {
            return Response::json($playerRes->body, $playerRes->http_code);
        }

        if(isset($playerRes->body) && !empty($playerRes->body))
        {
            foreach ($playerRes->body as $v) {
                $v->time = date('Y-m-d H:i:s',$v->time);
                $v->action_type = isset($messages[$v->action_type]) ? $messages[$v->action_type] : $v->action_type;
            }
            $data = $playerRes->body;
            return Response::json($data);
        }else{
            return Response::json(array(
                    'error' => Lang::get('basic.not_found')
            ), 404);
        }

    }
    public function partnerDelIndex(){
        $yysgwj = Lang::get('yysgwj');
        $data = array(
           'content' => View::make('slaveapi.player.partnerdel',array(
                'yysgwj' => $yysgwj,
           )) 
        );
        return View::make('main',$data);
    }

    public function partnerDel(){
        $game_id = Session::get('game_id');
        $platform_id = Session::get('platform_id');
        $game = Game::find($game_id);
        $start_time = strtotime(trim(Input::get('start_time')));
        $end_time = strtotime(trim(Input::get('end_time')));
        $choice = Input::get('choice');
        $id_or_name = Input::get('id_or_name');
        $mid = Input::get('mid');
        $yysgwj = Lang::get('yysgwj');
        $table_m = $this->initTable('game_message');
        $messages = array();
        foreach ($table_m as $message) {
            $messages[$message->id] = $message->desc;
        }
        if(!$id_or_name){
            return Response::json(array('error' => Lang::get('slave.id_or_name')),403);
        }

        $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
        $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
        
        $slaveapi = SlaveApi::connect($game->eb_api_url,$game->eb_api_key,$game->eb_api_secret_key);
        if(1 == $choice){
            $player = $slaveapi->getplayeridbyname($game_id, $id_or_name, $server->server_internal_id, $platform_id);
            if(200 == $player->http_code){
                $player_id = (int)$player->body[0]->player_id;
            }else{
               return Response::json(array('error' => 'Not Found player_id by '.$id_or_name),404);
            } 
        }else{
            $player_id = (int)$id_or_name;
        }
        $response = $slaveapi->getPartnerDel($game_id, $start_time, $end_time, $player_id, $mid);
        if(200 == $response->http_code){
            $body = $response->body;
            foreach ($body as $v) {
                $v->time = date('Y-m-d H:i:s',$v->time);
                $v->table_id = isset($yysgwj[$v->table_id]) ? $yysgwj[$v->table_id] : $v->table_id;
                $v->mid = isset($messages[$v->mid]) ? $messages[$v->mid] : $v->mid;
            }
            return Response::json($body);
        }else{
            return Response::json(array('error' => 'slave error'),500);
        }

    }

}