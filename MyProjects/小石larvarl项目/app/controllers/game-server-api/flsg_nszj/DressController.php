<?php
class DressController extends \BaseController {
	public function wingIndex()
	{
		$servers = Server::currentGameServers ()->get ();
		
		if (empty ( $servers ))
		{
			App::abort ( 404 );
			exit ();
		}
		$data = array (
				'content' => View::make ( 'serverapi.flsg_nszj.dress.wing', array (
						'servers' => $servers 
				) ) 
		);
		return View::make ( 'main', $data );
	}
	public function wingData()
	{
		$msg = array (
				'code' => Config::get ( 'errorcode.unknow' ),
				'error' => Lang::get ( 'error.basic_input_error' ) 
		);
		$rules = array (
				'server_id' => 'required' 
		);
		$validator = Validator::make ( Input::all (), $rules );
		if ($validator->fails ())
		{
			return Response::json ( $msg, 403 );
		}
		$server_id = ( int ) Input::get ( 'server_id' );
		$choice = ( int ) Input::get ( 'choice' );
		$server = Server::find ( $server_id );
		$player_id = ( int ) Input::get ( 'player_id' );
		$player_name = Input::get ( 'player_name' );
		
		$api = GameServerApi::connect ( $server->api_server_ip, $server->api_server_port, $server->api_dir_id );
		
		if ($player_name)
		{
			$player = $api->getPlayerInfoByName ( $player_name );
			if (! isset ( $player->player_id ))
			{
				$msg['error'] = Lang::get ( 'error.basic_not_found' );
				return Response::json ( $msg, 404 );
			}
			$player_id = ( int ) $player->player_id;
		}
		
		if ($choice == 0)
		{ // add
			$response = $api->addDress ( $player_id, 1 );
		} else
		{ // remove
			$response = $api->removeDress ( $player_id, 1 );
		}
		return $api->sendResponse ();
	}
}