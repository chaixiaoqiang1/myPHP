<?php

class PokerCheaterController extends \BaseController {

	public function cheaterIndex()
	{
		$time_now_s = date('Y-m-d H', time()-3600);
		$time_now_e = date('Y-m-d H', time());
		$data = array(
			'content' => View::make('serverapi.poker.cheater', array(
				'time_now_s'=>$time_now_s,
				'time_now_e'=>$time_now_e
				)),
		);
		return View::make('main', $data);
	}

	public function getCheaterPlayerId()
	{
		$start_time = strtotime(Input::get('start_time'));
		$end_time = strtotime(Input::get('end_time'));

		$game_id = Session::get('game_id');
		$game_id = 13;
		$server = Server::find($game_id);
		$type = 1;
		$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
		$response = $api->getCheaterPlayerId($type, $start_time, $end_time);
		var_dump($response);die();
		$body = $response->body;
	}



}