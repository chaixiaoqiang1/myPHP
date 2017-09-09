<?php

class TexaxPokerRewardController extends \BaseController
{

    public function index()
    {
        $servers = Server::currentGameServers()->get();
        
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.player.texaspokerreward', array(
                'servers' => $servers,
            ))
        );
        return View::make('main', $data);
    }

    public function sendMoney()
    {
        $msg = array(
            'code' => Config::get('errorcode.unknow'),
            'error' => Lang::get('error.basic_input_error')
        );
        $rules = array(
			'money' => 'required',
			'uid' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json($msg, 403);
        }
		
        $server = Server::currentGameServers()->first();

        if (! $server) {
            $msg['error'] = Lang::get('error.basic_not_found');
            return Response::json($msg, 404);
        }

        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

		$rewards = array(
			'money' => (int)Input::get('money')
		);

		$uid = Input::get('uid');

		$api->sendTexasPokerReward($rewards, 9, $uid);

        return $api->sendResponse();
    }
}