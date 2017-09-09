<?php
class PartyController extends BaseController
{

	public function partyMemberIndex()
	{
		$servers = $this->getUnionServers();
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.party.index', array(
									'servers' => $servers,
									))
			);
		return View::make('main', $data);
	}

	public function getPartyMember()
	{
		$game = Game::find( Session::get('game_id') );
		$game_code = $game->game_code;
		$server_id = (int) Input::get('server_id');
		$party_id = (int) Input::get('party_id');
		$msg = array(
			'error' => Lang::get ( 'error.basic_input_error' )
			);
		$rules = array (
				'server_id' => 'required',
				'party_id' => 'required' 
		);
		$validator = Validator::make ( Input::all (), $rules );
		if ($validator->fails() || $server_id == 0)
		{
			return Response::json ($msg, 400);
		}
		$server = Server::find($server_id);
		if(!isset($server))
		{
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json ( $msg, 404 );
		}
		$api = GameServerApi::connect ( $server->api_server_ip, $server->api_server_port, $server->api_dir_id );
		if(isset($api)){
			switch ($game_code) {
				case 'nszj':
				case 'flsg':
				case 'dld':
					$response = $api->getPartyMember($party_id, $game_code);
					break;
				default:
					return Response::json(array('error'=>$game_code.' is God.'), 403);
			}
		}else{
			$msg = array(
			'error' => 'connect error'
			);
			return Response::json ($msg, 403);
		}
		$player_info = array();
		try{
			if($response->result){
				$msg = array(
					'error' => 'Party ID may be wrong.'
					);
				return Response::json ($msg, 403);
			}
		}
		catch(Exception $e){
			try{
				foreach ($response->members as $value) 
				{	
					$player_info[] = $value;
				}
				if(count($player_info)%3==1){
					$player_info[] = '';
					$player_info[] = '';
				}
				if(count($player_info)%3==2){
					$player_info[] = '';
				}
				array_unshift($player_info, '');
				for ($i=count($player_info)-1; $i>0; $i=$i-3) { 
					$three_col[] = array(next($player_info),next($player_info),next($player_info));
				}
				return Response::json($three_col);
			}catch(Exception $e){
				$msg = array('error'=>'Oooh, response from server is wrong.');
				return Response::json($msg, 403);
			}
		}
	}


}