<?php

class ChangeLingshiAndQiyundianController extends \BaseController
{
	const CHANGE_LINGSHI = 'change_lingshi';
	const CHANGE_QIYUNDIAN = 'change_qiyundian';
	const CHANGE_ZAOCHUANLING= 'change_zaochuanling';
	const CHANGE_XINFA= 'change_xinfa';

    public function index()
    {
        $servers = Server::currentGameServers()->get();
        
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
		$self = $this;
		$logs = EastBlueLog::where('game_id', Session::get('game_id'))
			->where(function($query) use ($self) {
				$query->where('log_key', $self::CHANGE_LINGSHI)
					->orWhere('log_key', $self::CHANGE_QIYUNDIAN)
					->orWhere('log_key', $self::CHANGE_ZAOCHUANLING)
					->orWhere('log_key', $self::CHANGE_XINFA);
			})
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($logs as $v) {
            $desc_array = explode("|", $v->desc, 5);
            $v->player_id = $desc_array[0];
            $v->server_name = $desc_array[1];
            $v->num = $desc_array[2];
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.lingshi.index', array(
                'servers' => $servers,
				'logs' => $logs,
				'types' => array(
					self::CHANGE_LINGSHI,
					self::CHANGE_QIYUNDIAN,
					self::CHANGE_ZAOCHUANLING,
					self::CHANGE_XINFA
				)
            ))
        );
        return View::make('main', $data);
    }

    public function change()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
            'server_id' => 'required',
            'player_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
        $player_id = (int) Input::get('player_id');
        $num = (int) Input::get('num');
        $server_id = (int) Input::get('server_id');
		$type = Input::get('type');
        $server = Server::find($server_id);
        if (! $server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }
		if ($type != self::CHANGE_LINGSHI && 
			$type != self::CHANGE_QIYUNDIAN &&
			$type != self::CHANGE_ZAOCHUANLING &&
			$type != self::CHANGE_XINFA
		) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
		}
        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		if ($type == self::CHANGE_LINGSHI) {
        	$response = $api->changeLingshi($player_id, $num);
		} else if ($type == self::CHANGE_QIYUNDIAN) {
        	$response = $api->changeQiyundian($player_id, $num);
		} else if ($type == self::CHANGE_ZAOCHUANLING) {
			$response = $api->changeZaoChuanLing($player_id, $num);
		} else if ($type == self::CHANGE_XINFA) {
			$response = $api->changeXinfa($player_id, $num);
		}

        if(!isset($response->error_code)){ // 存入数据库
			$this->addLog($player_id, $num, $server->server_name, $type);
        }
        return $api->sendResponse();
    }

	private function addLog($player_id, $num, $server_name, $type_name)
	{
		$log = new EastBlueLog();
		$log->log_key = $type_name;
		$log->user_id = Auth::user()->user_id;
		$log->game_id = Session::get('game_id');
		$log->desc = $player_id . '|' . $server_name . '|' . $num;
		$log->save();

	}
}