<?php

class ChangeTiliController extends \BaseController
{

    public function index()
    {
        $servers = Server::currentGameServers()->get();
        
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $logs = EastBlueLog::where('log_key', 'changeTili')->where('game_id', Session::get('game_id'))
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
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.tili.index', array(
                'servers' => $servers,
                'tili_logs' => $logs
            ))
        );
        return View::make('main', $data);
    }

    public function changeTili()
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
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $operate_type = (int) Input::get('operate_type');
        $player_id = (int) Input::get('player_id');
        $amount = (int) Input::get('amount');
        $amount = $operate_type == 1 ? $amount : - $amount;
        $server_id = (int) Input::get('server_id');
        $server = Server::find($server_id);
        if (! $server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        
        $response = $api->changeTili($player_id, $amount);
        
        if(!isset($response->error_code)){ // 存入数据库
            $tili_log = new EastBlueLog();
            $tili_log->log_key = 'changeTili';
            $tili_log->user_id = Auth::user()->user_id;
            $tili_log->game_id = Session::get('game_id');
            $server_name = Server::find($server_id)->server_name;
            $operate_name = $operate_type == 1 ? '增加' : '减少';
            $change_name = '体力';
            $tili_log->desc = $player_id . '|' . $server_name . '|' . $operate_name . '|' . abs($amount) . '|' . $change_name;
            $tili_log->save();
//             $log = array(
//             	'player_id' => $player_id,
//             	'server_name' => $server_name,
//             	'amount' => abs($amount),
//             	'operate_name' => $operate_name,
//             	'change_name' => $change_name,
//             	'user_id' => $tili_log->user_id,
//             	'created_at' => $tili_log->created_at,
//             );
//             $response->result = array(
//             		'log' => $log
//             	);
            $response->result = 'OK';
        }
        return $api->sendResponse();
    }

    public function beautyIndex()
    {
        $servers = Server::currentGameServers()->get();
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.beauty.index', array(
                'servers' => $servers
            ))
        );
        return View::make('main', $data);
    }

    public function changeBeauty()
    {
        $msg = array(
            'code'  => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error'),
        );
        $rules = array(
            'server_id' => 'required',
            'level'     => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }

        $server_id = Input::get('server_id');
        $level = (int)Input::get('level');
        $len = count($server_id);
        for ($i=0; $i < $len ; $i++) { 
            $serverid = $server_id[$i];
            $server = Server::find($serverid);
            if (!$server) {
                $msg['eror'] = Lang::get('error.basic_not_found');
                return  Response::json($msg, 404);
            }
            $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->changeBeautyLevel($level);
            if (!isset($response->error_code)) {
                $beautyLog = new EastBlueLog;
                $beautyLog->log_key = 'beauty_change_level';
                $beautyLog->user_id = Auth::user()->user_id;
                $beautyLog->game_id = Session::get('game_id');
                $server_name = Server::find($serverid)->server_name;
                $change_name = '修改红颜等级';
                $beautyLog->desc = $server_name . '|' . $change_name . '|' . abs($level);
                $beautyLog->save();
                $response->result = 'OK';
            }
            return $api->sendResponse(); 
        }
        
    }

}