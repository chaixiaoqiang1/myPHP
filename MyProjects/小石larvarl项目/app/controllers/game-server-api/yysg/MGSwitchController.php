<?php

class MGSwitchController extends \BaseController
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

    public function MnsgsetSoulCasketIndex(){
        $table = $this->initTable('soul_casket',$this->area_soulcasket_id);
        $main = array();
        $second = array();
        $mana_partner_ids = array();
        $crystal_partner_ids = array();
        foreach ($table as $value) {
            if('1' == $value->type){
                $main[$value->id] = Lang::get("mnsgwj.$value->partner_id");
            }
            if('2' == $value->type){
                $second[$value->id] = Lang::get("mnsgwj.$value->partner_id");
            }
            if('3' == $value->type){
                $mana_partner_ids[$value->id] = Lang::get("mnsgwj.$value->partner_id");
            }
            if('4' == $value->type){
                $crystal_partner_ids[$value->id] = Lang::get("mnsgwj.$value->partner_id");
            }
            unset($value);
        }
        $data = array(
            'content' => View::make('serverapi.mnsg.switch.setSoulCasket', array(
                'main' => $main,
                'second' => $second,
                'mana_partner_ids' => $mana_partner_ids,
                'crystal_partner_ids' => $crystal_partner_ids,
                ))
        );
        return View::make('main', $data);
    }

    public function MnsgsetSoulCasket(){
        $game_id = Session::get('game_id');
        $type = Input::get('type');
        $is_auto = Input::get('is_auto');
        if($is_auto == 'SX' || $is_auto == 'MC'){           
            $start_time = strtotime(Input::get('start_time'));
            $end_time = strtotime(Input::get('end_time'));
            Log::info($start_time);
            Log::info($end_time);
            $Log_data = EastBlueLog::where('log_key',$is_auto)
                ->leftJoin('users as u', 'u.user_id', '=', 'log.user_id')
                ->leftJoin('games as g', 'g.game_id', '=', 'log.game_id')
                ->whereBetween("log.created_at", array( $start_time,$end_time))
                ->orderBy('log.created_at','DESC')
                ->take(20)
                ->selectRaw("u.username,g.game_name,log.log_key,log.desc,log.created_at")
                ->get();
            $Log_data = $Log_data->toArray();
            $items = array();
            foreach ($Log_data as $item) {
                $item['created_at'] = date('Y-m-d H:i:s',$item['created_at']);
                $items[] = $item;
            }
            if (!empty($items)) {
                return Response::json(array('msg'=> '已查询出相关数据','items'=>$items));
            }else{
                return Response::json(array('error'=> Lang::get('basic.no_result'),'items'=>$items),404);
            }
        }

        if('SX' == $type){
            $time = (int)strtotime(Input::get('time'));
            $main = (int)Input::get('main');
            $second = Input::get('second');

            // if(is_array($second) && !empty($second)){
            //     if('3' != count($second)){
            //         return Response::json(array('error'=> Lang::get('basic.input_error')), 403);
            //     }
            // }

            // $second = $second[0].'|'.$second[1].'|'.$second[2];

            if(!$main){
                return Response::json(array('error'=> Lang::get('basic.input_error')), 403);
            }

            $main = $main+'';
            $game = Game::find($game_id);
            if('mnsg' != $game->game_code){
                return Response::json(array('error'=> Lang::get('basic.current_game').Lang::get('basic.input_error')), 403);
            }
            $server = Server::where('game_id', $game_id)->where('is_server_on', '1')->first();

            if(!$server){
                return Response::json(array('error'=> Lang::get('basic.invalid').Lang::get('basic.server')), 403);
            }

            $api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

            $result = $api->MnsgSetSoulCasket($main, $second, $time);

            if(isset($result->main_partner)){
                $SX_log = new EastBlueLog();
                $SX_log->log_key = $type;
                $SX_log->user_id = Auth::user()->user_id;
                $SX_log->game_id = Session::get('game_id');
                $SX_log->desc = $type.':有效时间'.date('Y-m-d H:i:s',"$time").'|主要热点' . $main;
                $SX_log->created_at = time();
                $SX_log->save();
                return Response::json(array('msg'=> 'Success.'));
            }else{
                return Response::json(array('error'=> Lang::get('basic.set_fail')), 403);
            }
        }
        if('MC' == $type){
            $mana_id = (int)Input::get('mana_id');
            $crystal_id = (int)Input::get('crystal_id');
            if(!($mana_id || $crystal_id)){
                return Response::json(array('error'=> Lang::get('basic.not_enough_input')), 403);
            }

            $server = Server::where('game_id', $game_id)->where('is_server_on', '1')->first();

            if(!$server){
                return Response::json(array('error'=> Lang::get('basic.invalid').Lang::get('basic.server')), 403);
            }

            $game_api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);

            $params = array(
                'mana_id' => $mana_id,
                'crystal_id' => $crystal_id,
                );

            $result = $game_api->MnsgSetManaCrystalCasket($params);
            if(($mana_id && isset($result->mana_partner_id)) || ($crystal_id && isset($result->crystal_partner_id))){
                $SX_log = new EastBlueLog();
                $SX_log->log_key = $type;
                $SX_log->user_id = Auth::user()->user_id;
                $SX_log->game_id = Session::get('game_id');
                $SX_log->desc = $type.':铜钱贩卖机武将'.$mana_id.' | 钻石贩卖机武将' . $crystal_id;
                $SX_log->created_at = time();
                $SX_log->save();
                return Response::json(array('msg'=> 'Success.'));
            }else{
                Log::info('MGSwitchController--MnsgsetSoulCasket--MC--'.var_export($result, true));
                return Response::json(array('error'=> Lang::get('basic.set_fail')), 403); 
            }
        }

        return Response::json(array('error'=> 'Bad Operation.'), 403);
    }
}