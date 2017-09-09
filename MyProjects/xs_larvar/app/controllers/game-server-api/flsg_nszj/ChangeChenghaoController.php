<?php
class ChangeChenghaoController extends \BaseController {

	private $table_data = array();
	private $usable_id = array();

	private function usableId(){
		for($i=1;$i<=46;$i++)
			$tmp_arr[] = $i;
		return $tmp_arr;
	}

	public function index()
	{
		$servers = Server::currentGameServers ()->get ();
		
		if (empty ( $servers ))
		{
			App::abort ( 404 );
			exit ();
		}
		$logs = EastBlueLog::where ( 'log_key', 'changeChenghao' )->where ( 'game_id', Session::get ( 'game_id' ) )->orderBy ( 'created_at', 'desc' )->get ();
		foreach ( $logs as $v )
		{
			$username = User::where('user_id',$v->user_id)->pluck('username');
			$desc_array = explode ( "|", $v->desc);
			$v->player_id = $desc_array[0];
			$v->server_name = $desc_array[1];
			$v->operate_type = $desc_array[2];
			$v->chenghao_name = $desc_array[3];
			$v->user_id = $username ? $username : $v->user_id;
		}
		try {
			$this->table_data = $this->initTable();
		} catch (Exception $e) {
			App::abort(404);
		}
		//$this->usable_id = $this->usableId();
		$data_to_view = array();
		foreach ($this->table_data as $key => $value) {
			//if(in_array($value->id, $this->usable_id))
			if($value->id == ''){
				continue;
			}
				$data_to_view[] = (object) array (
										'id' => $value->id,
										'des' => $value->des,
										);
		}
		$data = array (
				'content' => View::make ( 'serverapi.flsg_nszj.chenghao.index', array (
						'servers' => $servers,
						'chenghaos' => $data_to_view,
						'chenghao_logs' => $logs 
				) ) 
		);
		return View::make ( 'main', $data );
	}

	private function initTable()
	{
       	$game_id = Session::get('game_id');
       	$game = Game::find($game_id);
       	if(in_array($game_id,$this->area_chenghao_id)){
       		$data = Table::init(public_path() . '/table/' . $game->game_code . '/chenghao' . $game_id . '.txt')->getData();
       	}else{
       		$data = Table::init(public_path() . '/table/' . $game->game_code . '/chenghao.txt')->getData();
       	}	
		return $data;
	}


	public function ChangeChenghao()
	{
		$msg = array (
				'code' => Config::get ( 'errorcode.unknow' ),
				'error' => Lang::get ( 'error.basic_input_error' ) 
		);
		$rules = array (
				'server_id' => 'required',
				'player_id' => 'required',
				'chenghao' => 'required' 
		);
		$validator = Validator::make ( Input::all (), $rules );
		if ($validator->fails ())
		{
			return Response::json ( $msg, 403 );
		}
		$operate_type = ( int ) Input::get ( 'operate_type' );
		$chenghao_id = ( int ) Input::get ( 'chenghao' );
		$player_id = ( int ) Input::get ( 'player_id' );
		$server_id = ( int ) Input::get ( 'server_id' );
		$server = Server::find ( $server_id );
		if (! $server)
		{
			$msg['error'] = Lang::get ( 'error.basic_not_found' );
			return Response::json ( $msg, 404 );
		}
		$api = GameServerApi::connect ( $server->api_server_ip, $server->api_server_port, $server->api_dir_id );

		$chenghao_name = '';
		$this->table_data = $this->initTable();
		foreach ( $this->table_data as $v )
		{
			if ($v->id == $chenghao_id)
			{
				$chenghao_name = $v->des;
				break;
			}
		}
		if ($operate_type == 1)
		{ // 添加称号
			$response = $api->addTitle ( $player_id, $chenghao_id );
		} else
		{ // 移除称号
			$response = $api->deleteTitle ( $player_id, $chenghao_id );
		}
		// var_dump($response);die();
		if (! isset ( $response->error_code ))
		{ // 存入数据库
			$chenghao_log = new EastBlueLog ();
			$chenghao_log->log_key = 'changeChenghao';
			$chenghao_log->user_id = Auth::user ()->user_id;
			$chenghao_log->game_id = Session::get ( 'game_id' );
			$server_name = Server::find ( $server_id )->server_name;
			$operate_name = $operate_type == 1 ? '添加' : '移除';
			$chenghao_log->desc = $player_id . '|' . $server_name . '|' . $operate_name . '|' . $chenghao_name . '|' . $chenghao_id;
			$chenghao_log->save ();
			$response->result = "OK";
		}
		return $api->sendResponse ();
	}
}