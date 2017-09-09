<?php

class ChangeYuanbaoController extends \BaseController
{

    public function index()
    {
        $servers = Server::currentGameServers()->get();
        //$servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $logs = EastBlueLog::where('log_key', 'changeYuanbao')->where('game_id', Session::get('game_id'))
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($logs as $v) {
            $desc_array = explode("|", $v->desc, 5);
            $v->player_id = $desc_array[0];
            $v->server_name = $desc_array[1];
            $v->operate_type = $desc_array[2];
            $v->amount = $desc_array[3];
            $v->change_type = $desc_array[4];
        }
        $game = Game::find(Session::get('game_id'));
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.yuanbao.index', array(
                'servers' => $servers,
                'yuanbao_logs' => $logs,
                'game_code' => $game->game_code,
            ))
        );
        return View::make('main', $data);
    }

    public function changeYuanbao()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'server_id' => 'required',
            'player_id' => 'required',
            'amount' => 'required|min:0',
            'operate_type' => 'required',
            'change_type' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $game = Game::find(Session::get('game_id'));
        $platform_id = Session::get('platform_id');
        $operate_type = (int) Input::get('operate_type');
        $change_type = (int) Input::get('change_type');
        if ('0' == $operate_type) {
            $msg['error'] = '请选择扣除或增加';
            return Response::json($msg, 404);
        }
        $player_id = (int) Input::get('player_id');
        $amount = (int) Input::get('amount');
        $amount = $operate_type == 1 ? $amount : - $amount;
        if(($amount > 100000 || $amount < -100000) && !in_array($change_type, array(3,6))){   //不允许绝对值100000以上的某些操作
            return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
        }
        if(3 == $change_type && ($amount > 100000000 || $amount < -100000000)){ //铜钱操作不允许超过1亿
            return Response::json(array('error'=> Lang::get('slave.please_check_confirm')), 403);
        }
        $server_id = (int) Input::get('server_id');
        
        $sub_yuanbao_type = 1;
        $consumeDelta = 1;
        if(2 == $operate_type){
            $sub_yuanbao_type = (int)Input::get('sub_yuanbao_type');
            $consumeDelta = $amount * $sub_yuanbao_type;
        }
        if ('0' == $server_id) {
            $msg['error'] = '请选择服务器';
            return Response::json($msg, 404);
        }
        $server = Server::find($server_id);
        if (! $server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
        if($game->game_code == 'yysg'){
            $yapi = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        }else{
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        }
        switch ($change_type) {
            case 1:
                $change_name ='元宝';
                if($game->game_code == 'yysg'){
                    $response = $yapi->changeYuanbao($player_id, $amount, $platform_id);
                }else{
                    $response = $api->changeYuanbao($player_id, $amount, $game->game_code, $consumeDelta);
                }
                break;// 修改元宝
            case 2: 
                $change_name ='VIP';
                $response = $api->changeVipYuanbao($player_id, $amount);
                break;// 修改VIP元宝
            case 3:
                $change_name ='铜钱';
                if($game->game_code == 'yysg'){
                    if($amount > 0){
                        return Response::json(array('error'=>'夜夜三国铜钱和体力只能扣除!'), 403);
                    }else{
                        $response = $yapi->changeTongqian($player_id, $amount, $platform_id);
                    }
                }else{
                    $response = $api->changeTongqian($player_id, $amount);
                }
                break;// 修改铜钱
            case 4:
                $change_name ='阅历';
                $response = $api->changeYueli($player_id, $amount);
                break;// 修改阅历
            case 5:
                $change_name ='功勋';
                $response = $api->changeGongxuan($player_id, $amount);
                break;//修改功勋
            case 6:
                $change_name ='经验';
                $response = $api->changeJingyan($player_id, $amount);
                break;// 修改经验
            case 7:
                $change_name = '天赋点';
                $response = $api->changeTianfudian($player_id, $amount);
                break;//修改天赋点
            case 8:
                $change_name = '祭天令';
                $response = $api->changeJitianling($player_id, $amount);
                break;//修改祭天令
            case 9:
                $change_name = Lang::get('serverapi.chongwu_shilian');
                $response = $api->changeChongwu($change_type, $player_id, $amount);
                break;
            case 10:
                $change_name = Lang::get('serverapi.chongwu_yuanshi');
                $response = $api->changeChongwu($change_type, $player_id, $amount);
                break;
            case 11:
                $change_name = Lang::get('serverapi.chongwu_jinengjinghua');
                $response = $api->changeChongwu($change_type, $player_id, $amount);
                break;
            case 12:
                $change_name = Lang::get('serverapi.jiezhijingyan');
                $response = $api->changeJiezhi($player_id, $amount, $game->game_code);
                break;
            case 13:
                $change_name = '体力';
                if($game->game_code == 'yysg'){
                    if($amount > 0){
                        return Response::json(array('error'=>'夜夜三国铜钱和体力只能扣除!'), 403);
                    }else{
                        $response = $yapi->changeTili($player_id, $amount, $platform_id);
                    }
                }
                break;
            case 14:
                $change_name = Lang::get('serverapi.power');
                $response = $api->changePower($player_id, $amount);
                break;
            case 15:
                $change_name = Lang::get('serverapi.battle_spirits');
                $response = $api->changeBattleSpirits($player_id, $amount);
                break;
            case 16:
                $change_name = Lang::get('serverapi.start_fragment');
                $response = $api->changeStartFragment($player_id, $amount);
                break;
            case 17:
                $change_name = Lang::get('serverapi.yuanshen_jingpo');
                $response = $api->changeJingPo($player_id, $amount);
                break;
            case 18:
                $change_name = Lang::get('serverapi.jinengshu');
                $response = $api->changeJiNengShu($player_id, $amount);
                break;
            default:
                return Response::json(array('error'=>'What do you want to change?'), 403);
        }
        if(!isset($response->error_code)){ // 存入数据库
            $yuanbao_log = new EastBlueLog();
            $yuanbao_log->log_key = 'changeYuanbao';
            $yuanbao_log->user_id = Auth::user()->user_id;
            $yuanbao_log->game_id = Session::get('game_id');
            $server_name = Server::find($server_id)->server_name;
            $operate_name = $operate_type == 1 ? '增加' : '减少';
            $yuanbao_log->desc = $player_id . '|' . $server_name . '|' . $operate_name . '|' . abs($amount) . '|' . $change_name;
            $yuanbao_log->save();
            $response->result = "OK";
        }
        if($game->game_code == 'yysg'){
            return $yapi->sendResponse();
        }else{
            return $api->sendResponse();
        }
    }
    public function updateLevelIndex()
    {
        //$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $logs = EastBlueLog::where('log_key', 'updateLevel')->where('game_id', Session::get('game_id'))
        ->orderBy('created_at', 'desc')
        ->get();
        foreach ($logs as $v) {
            $desc_array = explode("|", $v->desc, 3);
            $v->player_id = $desc_array[0];
            $v->server_name = $desc_array[1];
            $v->new_level = $desc_array[2];
        }
        $data = array(
                'content' => View::make('serverapi.flsg_nszj.level.index', array(
                        'servers' => $servers,
                        'level_logs' => $logs
                ))
        );
        return View::make('main', $data);
    }
    
    public function updateLevel()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
                'server_id' => 'required',
                'player_id' => 'required',
                'new_level' => 'required|min:0',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $player_id = (int) Input::get('player_id');
        $new_level = (int) Input::get('new_level');
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        
        $response = $api->updateLevel($new_level, $player_id);
        if(!isset($response->error_code)){ // 存入数据库
            $level_logs = new EastBlueLog();
            $level_logs->log_key = 'updateLevel';
            $level_logs->user_id = Auth::user()->user_id;
            $level_logs->game_id = Session::get('game_id');
            $server_name = Server::find($server_id)->server_name;
            $level_logs->desc = $player_id . '|' . $server_name . '|' . $new_level ;
            $level_logs->save();
            $response->result = "OK";
        }
        return $api->sendResponse();
    }
    public function monthCardIndex()
    {
    	//$servers = Server::currentGameServers()->get();
        $servers = $this->getUnionServers();
    	if (empty($servers)) {
    		App::abort(404);
    		exit();
    	}
    	$data = array(
    			'content' => View::make('serverapi.flsg_nszj.yuanbao.month-card', array(
    					'servers' => $servers,
    			))
    	);
    	return View::make('main', $data);
    }
    
    public function monthCard()
    {
    	$msg = array(
    			'code' => Config::get('errorcode.unknow'),
    			'error' => Lang::get('error.basic_input_error')
    	);
    	$rules = array(
    			'server_id' => 'required',
    			'uid' => 'required',
    			'amount' => 'required|min:0',
    			'order_sn' => 'required',
    			'month_card_type' => 'required'
    	);
    	$validator = Validator::make(Input::all(), $rules);
    	if ($validator->fails()) {
    		return Response::json($msg, 403);
    	}
    	
    	$month_card_type = (int) Input::get('month_card_type');
    	$order_sn = (string) Input::get('order_sn');
    	$uid = trim(Input::get('uid'));
    	$amount = (int) Input::get('amount');
    	$amount = $amount ? $amount : 0; 
    	$server_id = (int) Input::get('server_id');
    	$server = Server::find($server_id);
    	if (! $server) {
    		$msg['error'] = Lang::get('error.basic_not_found');
    		return Response::json($msg, 404);
    	}
    	$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
    	$response = $api->recharge($order_sn, $uid, $amount, $month_card_type);
    	return $api->sendResponse();
    }

    public function redPacketIndex()
    {
        $servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.activity.red_packet',
                array(
                    'servers' => $servers,
                ))
        );
        return View::make('main', $data);
    }
    
    public function replacementRedPacket()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $enter_type = (int)Input::get('enter_type');
        $server_id = Input::get('server_id');
        $operation_type = (int)Input::get('operation_type');
        if(!$server_id){
            return Response::json(array('error'=>'Did you select a server?'), 403);
        }
        $server = Server::find($server_id);
        if(!$server){
            return Response::json(array('error'=>'Not Found Server'), 403);
        }
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

        if(1 == $enter_type){
            $player_id = Input::get('player_id');
            if(!$player_id){
                return Response::json($msg, 403);
            }
            $response = $api->replacementRedPacket($player_id);
            Log::info(var_export($response,true));
            if(isset($response->result) && ('OK' == $response->result)){
                $result[] = array(
                   'msg' => ' ( ' . $player_id . ' ) : OK'  . "\n",
                   'status' => 'ok'
                );
            }else{
                $result[] = array(
                       'msg' => ' ( ' . $player_id . ' ) : ' . 'error' . "\n",
                       'status' => 'error'
                );
            } 
        }elseif (2 == $enter_type) {
            $text_datas = Input::get('text_data');
            $text_datas = explode("\n", $text_datas);
            if(!$text_datas){
                return Response::json($msg, 403);
            }
            foreach ($text_datas as &$v) {
                $v = trim($v);
            }
            unset($v);
            $text_datas = array_unique($text_datas);
            foreach ($text_datas as $text_data) {
                $text_data = explode("\t", $text_data);
                $response = $api->replacementRedPacket($text_data[0]);
                if(isset($response->result) && ('OK' == $response->result)){
                    $result[] = array(
                       'msg' => ' ( ' . $text_data[0] . ' ) : OK'  . "\n",
                       'status' => 'ok'
                    );
                }else{
                    $result[] = array(
                           'msg' => ' ( ' . $text_data[0] . ' ) : ' . 'error' . "\n",
                           'status' => 'error'
                    );
                }
            }
            
        }else{
            return Response::json($msg, 403);
        }

        return Response::json($result);
    }

}