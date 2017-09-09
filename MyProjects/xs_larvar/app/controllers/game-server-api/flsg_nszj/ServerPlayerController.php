<?php
class ServerPlayerController extends \BaseController {
	
	public function index()
	{
	    $server_name = Input::get('server_name');
	    $player_id = (int)Input::get('player_id');
	    $server_init = 0;
	    if($server_name && $player_id){
	    	$server = Server::where('game_id', Session::get('game_id'))->where('server_name', $server_name)->first();
	    	if($server){
	    		$server_init = $server->server_id;
	    	}
	    }else{
		    $server_init = (int)Input::get('server_init');
	    }
	    $servers = Server::currentGameServers()->get();
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.player.index', array(
	                    'servers' => $servers,
	                    'player_id' => $player_id,
	                    'server_init' => $server_init,
	            ))
	    );
	    return View::make('main', $data);
	}
	
	public function search()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$rules = array(
				'id_or_name' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if($validator->fails())
		{
			return Response::json($msg, 403);
		}
		$choice = ( int ) Input::get('choice');
		
		$server_id = ( int ) Input::get('server_id');
		
		$server = Server::find($server_id);
		$server_internal_id = 0;
		$game_id = (int)Session::get('game_id');
		if($server)
		{
			if($server->game_id != $game_id){
				return Response::json(array('error'=>'please check the current platform and servers!'), 403);
			}
			$server_internal_id = $server->server_internal_id;
			$main_server = Server::where('game_id', $game_id)->where('server_internal_id', $this->getMainServer($game_id, $server_internal_id))->first();
		}else{
			return Response::json(array('error'=>'Please Select A Server!'), 403);
		}
		$servers = array();
		$game = Game::find(Session::get('game_id'));
		$platform_id = Session::get('platform_id');
		$platform = Platform::find(Session::get('platform_id'));
		$id_or_name = Input::get('id_or_name');
		$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		
		if($choice == 0)
		{
			$response = $slave_api->getUserByPlayerName($platform_id, $id_or_name, $server_internal_id, $game->game_id);
        } else if($choice == 1)
		{
			$response = $slave_api->getUserByPlayerID($platform_id, $id_or_name, $server_internal_id, $game->game_id);
		}
		if($response->http_code != 200)
		{
			return Response::json($response->body, $response->http_code);
		}
		if(isset($response->body) && empty($response->body))
		{
			if(! $server)
			{
				$server = ( object ) array();
			}
			if($choice == 0)
			{
				$server->player_name = $id_or_name;
			} else if($choice == 1)
			{
				$server->player_id = ( int ) $id_or_name;
			}
			$response->body = array(
					$server
			);
		}
		$players = array();
		foreach ( $response->body as $v )
		{
			if($game_id == 54){
				$server = Server::currentGameServers()->where('server_internal_id', 1)->first();
			}else{
				if(isset($v->server_internal_id)){
					$server = Server::currentGameServers()->where('server_internal_id', $v->server_internal_id)->first();
				}else{
					continue;
				}
			}
			
			if(! $server)
			{
				continue;
			}
			$player = array();
			if(isset($v->uid))
			{
				$player = array(
						'nickname' => $v->nickname,
						'uid' => $v->uid,
						'login_email' => $v->login_email,
						'first_lev' => $v->first_lev,
						'all_pay_amount' => $v->all_pay_amount,
						'all_pay_times' => $v->all_pay_times,
						'avg_amount' => $v->all_pay_times > 0 ? round($v->all_pay_amount / $v->all_pay_times, 2) : 0,
						'tp_user_id' => $v->tp_user_id,
						'u' => $v->u,
						'u2' => $v->u2,
						'source' => $v->source,
						'is_anonymous' => $v->is_anonymous
				);
			}
			/*
			 * 添加first_login_ip和last_login_ip
			*/
			$my_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
			if (isset($player['login_email'])) {
				$login_email = $player['login_email'];
				$my_response = $my_api->getUserByEmail($platform->platform_id, $login_email, $server_internal_id , $game->game_id);
				if ($my_response->http_code == 200) {
					$user_info = $my_response->body;

					$ip_api = "http://freegeoip.net/json/";
					$ip_api_created = $ip_api.$user_info->created_ip;
					$ip_api_last = $ip_api.$user_info->last_visit_ip;
					$response_created = Curl::url($ip_api_created)->get();
					$response_last = Curl::url($ip_api_last)->get();
					$created_ip_country = '';
					$last_ip_country = '';
					if ($response_created->http_code == 200)
					{
					    if(isset($response_created->body->country_name)){
					        $created_ip_country = '('.$response_created->body->country_name.')';
					    }
					}
					if ($response_last->http_code == 200)
					{
					    if(isset($response_last->body->country_name)){
					        $last_ip_country = '('.$response_last->body->country_name.')';
					    }
					} 

					$player['created_ip'] = $user_info->created_ip.$created_ip_country;
                	$player['last_visit_ip'] = $user_info->last_visit_ip.$last_ip_country;

				}
			} else if (isset($player['uid'])) {
				$uid = $player['uid'];
				$my_response = $my_api->getUserByUID($platform->platform_id, $uid, $server_internal_id , $game->game_id);
				if ($my_response->http_code == 200) {
					$user_info = $my_response->body; 

					$ip_api = "http://freegeoip.net/json/";
					$ip_api_created = $ip_api.$user_info->created_ip;
					$ip_api_last = $ip_api.$user_info->last_visit_ip;
					$response_created = Curl::url($ip_api_created)->get();
					$response_last = Curl::url($ip_api_last)->get();
					$created_ip_country = '';
					$last_ip_country = '';
					if ($response_created->http_code == 200)
					{
					    if(isset($response_created->body->country_name)){
					        $created_ip_country = '('.$response_created->body->country_name.')';
					    }
					}
					if ($response_last->http_code == 200)
					{
					    if(isset($response_last->body->country_name)){
					        $last_ip_country = '('.$response_last->body->country_name.')';
					    }
					} 

					$player['created_ip'] = $user_info->created_ip.$created_ip_country;
                	$player['last_visit_ip'] = $user_info->last_visit_ip.$last_ip_country;
				}
			} 

			/*
			 *操作结束
			*/
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			if(isset($v->player_id))
			{
				$player_info_from_id = $api->getPlayerInfoByPlayerID($v->player_id);
				if(! isset($player_info_from_id->PlayerID))
				{
					if(! empty($player))
					{
						$players[] = $player;
					}
					continue;
				}
				$player_info_from_name = $api->getPlayerInfoByName($player_info_from_id->Name);
				//Log::info(var_export($player_info_from_id, true));
				//Log::info(var_export($player_info_from_name, true));
			} elseif(isset($v->player_name))
			{
				$player_info_from_name = $api->getPlayerInfoByName($v->player_name);
				if(! isset($player_info_from_name->player_id))
				{
					if(! empty($player))
					{
						$players[] = $player;
					}
					continue;
				}
				$player_info_from_id = $api->getPlayerInfoByPlayerID($player_info_from_name->player_id);
            }
			// 获取exp
			$player_exp = '';
			if(isset($player_info_from_id->Roles))
			{
				$roles_array = $player_info_from_id->Roles;
				foreach ( $roles_array as $role )
				{
					if($role->Is_ZhuJue == 1)
					{
						$player_exp = $role->Exp;
						break;
					}
				}
			} else if(isset($player_info_from_id->Exp))
			{
				$player_exp = $player_info_from_id->Exp;
			}
			if(empty($player_info_from_id)){
				$player_info_from_id->CreateTime = 0;
				$player_info_from_id->VIPLevel = -1;
				$player_info_from_id->TongQian = -1;
				$player_info_from_id->YuanBao = -1;
			}
			$player_server = array(
					'player_id' => isset($player_info_from_name->player_id) ? $player_info_from_name->player_id : '',
					'name' => isset($player_info_from_name->name) ? $player_info_from_name->name : '',
					'is_online' => isset($player_info_from_name->is_online) ? $player_info_from_name->is_online : '',
					'last_login' => isset($player_info_from_name->last_login) ? date('Y-m-d H:i:s', $player_info_from_name->last_login) : '',
					'level' => isset($player_info_from_name->level) ? $player_info_from_name->level : '',
					'which_server' => $server->server_name,
					'active' => isset($player_info_from_name->last_login) ? floor(( int ) (time() - $player_info_from_name->last_login) / 86400) : '',
					'first_login' => isset($player_info_from_id->CreateTime) ? date('Y-m-d H:i:s', $player_info_from_id->CreateTime) : '',
					'vip_level' => isset($player_info_from_id->VIPLevel) ? $player_info_from_id->VIPLevel : 0,
					'tongqian' => $player_info_from_id->TongQian,
					'yuanbao' => $player_info_from_id->YuanBao,
					'exp' => $player_exp,
					'uid' => $v->uid,
					'login_email' => $v->login_email,
					'xian_ling' => isset($player_info_from_id->XianLing) ? $player_info_from_id->XianLing : '',
					'nei_li' => isset($player_info_from_id->NeiLi) ? $player_info_from_id->NeiLi : '',
					'main_server' => isset($main_server->server_name) ? $main_server->server_name : '',
			);
			if(isset($player_info_from_name->rank))
			{
				$player_server['rank'] = $player_info_from_name->rank;
			}
			if(isset($player_info_from_name->league_id))
			{
				$player_server['league_id'] = $player_info_from_name->league_id;
			}
			if(isset($player_info_from_name->league_name))
			{
				$player_server['league_name'] = $player_info_from_name->league_name ? $player_info_from_name->league_name : 'null';
			}
			if(isset($player_info_from_id->ShengWang))
			{
				$player_server['shengwang'] = $player_info_from_id->ShengWang;
			}
			if(isset($player_info_from_id->TiLi))
			{
				$player_server['tili'] = $player_info_from_id->TiLi;
			}
			if(isset($player_info_from_id->YueLi))
			{
				$player_server['yueli'] = $player_info_from_id->YueLi;
			}
			if(isset($player_info_from_name->attack))
			{
				$player['attack'] = $player_info_from_name->attack;
			}
			if(isset($player_info_from_id->JingJieDian))
			{
				$player['jingjiedian'] = $player_info_from_id->JingJieDian;
			}
			if(isset($player_info_from_id->LingShi))
			{
				$player['lingshi'] = $player_info_from_id->LingShi;
			}
			$players[] = $player + $player_server;
		}
        if(! empty($players))
		{
			return Response::json($players);
		} else
		{
			return Response::json(array(
					'error' => Lang::get('basic.not_found')
			), 404);
		}
	}
	/* 禁言和封号操作的入口方法 */
	public function accountIndex()
	{
		$servers = $this->getUnionServers();
		//$servers = Server::currentGameServers()->get();
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.player.account', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	
	/*
	 * 处理禁言和封号操作
	 */
	public function account()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$form = ( int ) Input::get('form');
		$game_id = Session::get('game_id');
		// 玩家昵称和id操作
		if($form == 1)
		{
			$rules = array(
					'server_id' => 'required|numeric|min:1',
					'choice1' => 'required|numeric|min:1',
					'ban_days1' => 'required|numeric'
			);
			$validator = Validator::make(Input::all(), $rules);
			if($validator->fails())
			{
				return Response::json($msg, 403);
			}
			$server_id = ( int ) Input::get('server_id');
			$server = Server::find($server_id);
			
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$player_name = Input::get('player_name');
			$player_id = (int)Input::get('player_id');
			if(! $player_name && ! $player_id)
			{
				return Response::json($msg, 403);
			}
			if($player_name)
			{
				$player = $api->getPlayerInfoByName($player_name);
				if(! isset($player->name))
				{
					return Response::json($player, 404);
				}
				$player_id = ( int ) $player->player_id;
			}
			if($player_id)
			{
				$player_id = ( int ) $player_id;
				$player = $api->getPlayerInfoByPlayerID($player_id);
				if(! isset($player->Name))
				{
					return Response::json($player, 404);
				}
				$player_name = $player->Name;
			}
			$choice1 = ( int ) Input::get('choice1');
			$ban_days1 = ( int ) Input::get('ban_days1');
			if($choice1 == 1)
			{
				$api->freezeAccount($player_id, $ban_days1);
				//记录封号操作
				
				$freeze_log = new EastBlueLog();
				$freeze_log->user_id = Auth::user()->user_id;
				$freeze_log->log_key = 'freeze';
				$freeze_log->game_id = $game_id;
				$freeze_log->desc =  $player_id;
				$freeze_log->new_value =  $ban_days1;
				$freeze_log->old_value =  $server->server_name;
				$freeze_log->platform_uid =  $player_name;
				$freeze_log->save();
			} else
			{
				$response = $api->banChat($player_id, $ban_days1);

				$freeze_log = new EastBlueLog();
				$freeze_log->user_id = Auth::user()->user_id;
				$freeze_log->log_key = 'banner';
				$freeze_log->game_id = $game_id;
				$freeze_log->desc =  $player_id;
				$freeze_log->new_value =  $ban_days1;
				$freeze_log->old_value =  $server->server_name;
				$freeze_log->platform_uid =  $player_name;
				$freeze_log->save();
			}
			return $api->sendResponse();
		}
		// 官网账号操作
		if($form == 2)
		{
			$rules = array(
					'choice2' => 'required|numeric|min:1',
					'ban_days2' => 'required|numeric'
			);
			$validator = Validator::make(Input::all(), $rules);
			
			if($validator->fails())
			{
				return Response::json($msg, 403);
			}
			$email = trim(Input::get('email'));
			$uid = trim(Input::get('user_uid'));
			$game = Game::find(Session::get('game_id'));
			$platform = Platform::find(Session::get('platform_id'));
			$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
			if(! $email && ! $uid)
			{
				return Response::json($msg, 403);
			}
			if($email)
			{
				$response = $slave_api->getUserByEmail($platform->platform_id, $email);
			}
			if($uid)
			{
				$response = $slave_api->getUserByUID($platform->platform_id, $uid);
			}
			if($response->http_code != 200)
			{
				return Response::json($response->body, 404);
			}
			$choice2 = ( int ) Input::get('choice2');
			$ban_days2 = ( int ) Input::get('ban_days2');
			if(isset($response->body) && isset($response->body->players)) {
			    $created_players = $response->body->players;
			    $result = array();
			    foreach ( $created_players as $player )
			    {
			        $server = Server::where('game_id', Session::get('game_id'))->where('server_internal_id', $player->server_id)->first();
			        if (!$server) {
			            continue;
			        }
			        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			        if($choice2 == 1)
			        {
			            $response = $api->freezeAccount(( int ) $player->player_id, $ban_days2);
			            if(isset($response->result) && $response->result == 'OK'){
			                //记录封号操作
			                $game_id = Session::get('game_id');
			                $freeze_log = new EastBlueLog();
			                $freeze_log->user_id = Auth::user()->user_id;
			                $freeze_log->log_key = 'freeze';
			                $freeze_log->game_id = $game_id;
			                $freeze_log->desc =  ( int ) $player->player_id;
			                $freeze_log->new_value =  $ban_days2;
							$freeze_log->old_value =  $server->server_name;
							$freeze_log->platform_uid =  $player->player_name;
			                $freeze_log->save();
			                $result[] = array(
			                        'msg' => ' ( ' . $server->server_name . ' ) : ' . ' [ ' .$player->player_id.' ] '. $response->result . "\n",
			                        'status' => 'ok');
			            }else {
			                $result[] = array(
			                        'msg' => ' ( ' . $server->server_name . ' ) : ' . ' [ ' .$player->player_id.' ] '. $response->error . "\n",
			                        'status' => 'error');
			            }
			        } else
			        {
			            $response = $api->banChat(( int ) $player->player_id, $ban_days2);
			            $game_id = Session::get('game_id');
		                $freeze_log = new EastBlueLog();
		                $freeze_log->user_id = Auth::user()->user_id;
		                $freeze_log->log_key = 'freeze';
		                $freeze_log->game_id = $game_id;
		                $freeze_log->desc =  ( int ) $player->player_id;
		                $freeze_log->new_value =  $ban_days2;
						$freeze_log->old_value =  $server->server_name;
						$freeze_log->platform_uid =  $player->player_name;
		                $freeze_log->save();
			            if(isset($response->result) && $response->result == 'OK'){
			                $result[] = array(
			                        'msg' => ' ( ' . $server->server_name . ' ) : ' . ' [ ' .$player->player_id.' ] '. $response->result . "\n",
			                        'status' => 'ok');
			            }else {
			                $result[] = array(
			                        'msg' => ' ( ' . $server->server_name . ' ) : ' . ' [ ' .$player->player_id.' ] '. $response->error . "\n",
			                        'status' => 'error');
			            }
			        }
			    }
			    $msg = array(
			            'result' => $result
			    );
			    return Response::json($msg);
			} else {
				return Response::json($msg, 404);
			}
		}
	}
	public function setGameMasterIndex()
	{
		$servers = $this->getUnionServers();
		//$servers = Server::currentGameServers()->get();
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.player.gm', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function setGameMaster()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		
		$rules = array(
				'server_id' => 'required|numeric|min:1',
				'player_name' => 'required',
				'is_gm' => 'required'
		);
		
		$validator = Validator::make(Input::all(), $rules);
		
		if($validator->fails())
		{
			return Response::json($msg, 403);
		}
		
		$server_id = ( int ) Input::get('server_id');
		$server = Server::find($server_id);
		if(! $server)
		{
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 404);
		}
		
		$player_name = Input::get('player_name');
		
		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		
		$player = $api->getPlayerInfoByName($player_name);
		
		if(! isset($player->player_id))
		{
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 404);
		}
		$is_true = ( int ) Input::get('is_gm') == 1 ? true : false;
		$api->setGameMaster($player->player_id, $is_true);
		//Log::info('setGM'.var_export($api->sendResponse(), true));
		return $api->sendResponse();
	}
	public function dissolveIndex()
	{
		//$servers = Server::currentGameServers()->get();
		$servers = $this->getUnionServers();
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.player.dissolve', array(
						'servers' => $servers
				))
		);
		return View::make('main', $data);
	}
	public function dissolve()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$rules = array(
				'server_id' => 'required|numeric|min:1'
		);
		$validator = Validator::make(Input::all(), $rules);
		if($validator->fails())
		{
			return Response::json($msg, 403);
		}
		
		$server_id = ( int ) Input::get('server_id');
		$server = Server::find($server_id);
		if(! $server)
		{
			$msg['error'] = Lang::get('error.basic_not_found');
			return Response::json($msg, 404);
		}
		
		$player_name = Input::get('player_name');
		
		$player_id = ( int ) Input::get('player_id');
		
		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		
		if($player_name)
		{
			$player = $api->getPlayerInfoByName($player_name);
			if(! isset($player->player_id))
			{
				$msg['error'] = Lang::get('error.basic_not_found');
				return Response::json($msg, 404);
			}
			$player_id = $player->player_id;
		}
		
		$response = $api->dissolve($player_id);
		return $api->sendResponse();
	}
	public function qqLoginMasterIndex(){
	    $servers = Server::currentGameServers()->get();
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.player.qq-loginmaster', array(
	                    'servers' => $servers
	            ))
	    );
	    return View::make('main', $data);
	}
	public function qqLoginMaster(){
	    $msg = array(
	            'code' => Config::get('errorcode.unknow'),
	            'error' => Lang::get('error.basic_input_error')
	    );
	    $rules = array(
	            'id_or_name' => 'required'
	    );
	    $validator = Validator::make(Input::all(), $rules);
	    
	    if($validator->fails())
	    {
	        return Response::json($msg, 403);
	    }
	    
	    $choice = ( int ) Input::get('choice');
	    
	    $server_id = ( int ) Input::get('server_id');
	    
	    $server = Server::find($server_id);
	    $server_internal_id = 0;
	    if($server)
	    {
	        $server_internal_id = $server->server_internal_id;
	    }
	    
	    $game = Game::find(Session::get('game_id'));
	    $platform_id = Session::get('platform_id');
	    
	    $id_or_name = trim(Input::get('id_or_name'));
	    
	    $slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
	    //
	    $tp_code = 'qq';
	    if($choice == 0)
	    {
	        $response = $slave_api->getUserByPlayerName($platform_id, $id_or_name, $server_internal_id, $game->game_id, $tp_code);
	    } else if($choice == 1)
	    {
	        $response = $slave_api->getUserByPlayerID($platform_id, $id_or_name, $server_internal_id, $game->game_id, $tp_code);
	    }
	    if($response->http_code != 200)
	    {
	        return Response::json($response->body, 403);
	    }
	    $body = $response->body;
	    $tp_user_id = '';
	    if(isset($body[0]) && $body[0]->tp_user_id){
	    	$tp_user_id = $body[0]->tp_user_id;
	    } else {
	        return Response::json($response->body, 403);
	    }
	    return Response::json(array('tp_user_id' => $tp_user_id));
	}

	//和服后查看玩家信息
	public function indexUnion()
	{
		$servers = $this->getUnionServers();
	    //$servers = Server::currentGameServers()->get();
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.player.index-union', array(
	                    'servers' => $servers
	            ))
	    );
	    return View::make('main', $data);
	}
	public function searchUnion()
	{
		$msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$rules = array(
				'id_or_name' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if($validator->fails())
		{
			return Response::json($msg, 403);
		}
		
		$choice = ( int ) Input::get('choice');
		
		$server_id1 = ( int ) Input::get('server_id1');
		$server_id2 = (int)Input::get('server_id2');
		$game_id = Session::get('game_id');

		$server = Server::find($server_id1);
		
		$server_internal_id = 0;
		if($server)
		{
			$server_internal_id = $server->server_internal_id;
		}
		
		$servers = array();
		
		$game = Game::find(Session::get('game_id'));
		$platform_id = Session::get('platform_id');
		$platform = Platform::find(Session::get('platform_id'));
		$id_or_name = trim(Input::get('id_or_name'));
		$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
		
		if($choice == 0)
		{
			if (isset($server_id2) && $server_id2 > 0) {
				$response = $slave_api->getUserByPlayerName($platform_id, $id_or_name, $server_id2, $game->game_id);
			} else{
				$response = $slave_api->getUserByPlayerName($platform_id, $id_or_name, $server_internal_id, $game->game_id);
			}
		} else if($choice == 1)
		{
			if (isset($server_id2) && $server_id2 >0 ) {
				$response = $slave_api->getUserByPlayerID($platform_id, $id_or_name, $server_id2, $game->game_id);
			} else {
				$response = $slave_api->getUserByPlayerID($platform_id, $id_or_name, $server_internal_id, $game->game_id);
			}
		}
		if($response->http_code != 200)
		{
			return Response::json($response->body, $response->http_code);
		}
		if(isset($response->body) && empty($response->body))
		{
			if(! $server)
			{
				$server = ( object ) array();
			}
			if($choice == 0)
			{
				$server->player_name = $id_or_name;
			} else if($choice == 1)
			{
				$server->player_id = ( int ) $id_or_name;
			}
			$response->body = array(
					$server
			);
		}
		$players = array();
		foreach ( $response->body as $v )
		{
			$server = Server::currentGameServers()->where('server_internal_id', $v->server_internal_id)->first();
			if(! $server)
			{
				continue;
			}
			$player = array();
			if(isset($v->uid))
			{
				$player = array(
						'nickname' => $v->nickname,
						'uid' => $v->uid,
						'login_email' => $v->login_email,
						'first_lev' => $v->first_lev,
						'all_pay_amount' => $v->all_pay_amount,
						'all_pay_times' => $v->all_pay_times,
						'avg_amount' => $v->all_pay_times > 0 ? round($v->all_pay_amount / $v->all_pay_times, 2) : 0,
						'tp_user_id' => $v->tp_user_id,
						'u' => $v->u,
						'u2' => $v->u2,
						'source' => $v->source,
						'is_anonymous' => $v->is_anonymous
				);
			}
			
			/*
			 * 添加first_login_ip和last_login_ip
			*/
			$my_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
			if (isset($player['login_email'])) {
				$login_email = $player['login_email'];
				if (isset($server_id2) && $server_id2 > 0 ) {
					$my_response = $my_api->getUserByEmail($platform->platform_id, $login_email, $server_id2 , $game->game_id);
				} else{
					$my_response = $my_api->getUserByEmail($platform->platform_id, $login_email, $server_internal_id , $game->game_id);
				}
				if ($my_response->http_code == 200) {
					$user_info = $my_response->body; 
					$player['created_ip'] = $user_info->created_ip;
                	$player['last_visit_ip'] = $user_info->last_visit_ip;

				}
			} else if (isset($player['uid'])) {
				$uid = $player['uid'];
				if (isset($server_id2) && $server_id2 > 0) {
					$my_response = $my_api->getUserByUID($platform->platform_id, $uid, $server_id2 , $game->game_id);
				} else {
					$my_response = $my_api->getUserByUID($platform->platform_id, $uid, $server_internal_id , $game->game_id);
				}
				if ($my_response->http_code == 200) {
					$user_info = $my_response->body; 
					$player['created_ip'] = $user_info->created_ip;
                	$player['last_visit_ip'] = $user_info->last_visit_ip;
				}
			} 

			/*
			 *操作结束
			*/
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			
			if(isset($v->player_id))
			{
				$player_info_from_id = $api->getPlayerInfoByPlayerID($v->player_id);
				if(! isset($player_info_from_id->PlayerID))
				{
					if(! empty($player))
					{
						$players[] = $player;
					}
					continue;
				}
				$player_info_from_name = $api->getPlayerInfoByName($player_info_from_id->Name);
			} elseif(isset($v->player_name))
			{
				$player_info_from_name = $api->getPlayerInfoByName($v->player_name);
				if(! isset($player_info_from_name->player_id))
				{
					if(! empty($player))
					{
						$players[] = $player;
					}
					continue;
				}
				$player_info_from_id = $api->getPlayerInfoByPlayerID($player_info_from_name->player_id);
			}
			// 获取exp
			$player_exp = '';
			if(isset($player_info_from_id->Roles))
			{
				$roles_array = $player_info_from_id->Roles;
				foreach ( $roles_array as $role )
				{
					if($role->Is_ZhuJue == 1)
					{
						$player_exp = $role->Exp;
						break;
					}
				}
			} else if(isset($player_info_from_id->Exp))
			{
				$player_exp = $player_info_from_id->Exp;
			}
			$player_server = array(
					'player_id' => $player_info_from_name->player_id,
					'name' => $player_info_from_name->name,
					'is_online' => $player_info_from_name->is_online,
					'last_login' => date('Y-m-d H:i:s', $player_info_from_name->last_login),
					'level' => $player_info_from_name->level,
					'which_server' => $server->server_name,
					'active' => floor(( int ) (time() - $player_info_from_name->last_login) / 86400),
					'first_login' => date('Y-m-d H:i:s', $player_info_from_id->CreateTime),
					'vip_level' => $player_info_from_id->VIPLevel,
					'tongqian' => $player_info_from_id->TongQian,
					'yuanbao' => $player_info_from_id->YuanBao,
					'exp' => $player_exp,
					'uid' => $v->uid,
					'login_email' => $v->login_email
			);
			if(isset($player_info_from_name->rank))
			{
				$player_server['rank'] = $player_info_from_name->rank;
			}
			if(isset($player_info_from_name->league_id))
			{
				$player_server['league_id'] = $player_info_from_name->league_id;
			}
			if(isset($player_info_from_name->league_name))
			{
				$player_server['league_name'] = $player_info_from_name->league_name ? $player_info_from_name->league_name : 'null';
			}
			if(isset($player_info_from_id->ShengWang))
			{
				$player_server['shengwang'] = $player_info_from_id->ShengWang;
			}
			if(isset($player_info_from_id->TiLi))
			{
				$player_server['tili'] = $player_info_from_id->TiLi;
			}
			if(isset($player_info_from_id->YueLi))
			{
				$player_server['yueli'] = $player_info_from_id->YueLi;
			}
			if(isset($player_info_from_name->attack))
			{
				$player['attack'] = $player_info_from_name->attack;
			}
			if(isset($player_info_from_id->JingJieDian))
			{
				$player['jingjiedian'] = $player_info_from_id->JingJieDian;
			}
			if(isset($player_info_from_id->LingShi))
			{
				$player['lingshi'] = $player_info_from_id->LingShi;
			}
			$players[] = $player + $player_server;
		}
// 		var_dump($players);die();
		if(! empty($players))
		{
			return Response::json($players);
		} else
		{
			return Response::json(array(
					'error' => Lang::get('basic.not_found')
			), 404);
		}
	}

	public function updateexcelload(){  //策划热更excel
		$servers = $this->getUnionServers();

	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.player.updateexcel', array(
	                    'servers' => $servers
	            ))
	    );
	    return View::make('main', $data);
	}

	public function updateexcelupdate(){
		$game_id = (int)Session::get('game_id');
		$game = Game::find($game_id);
		$servers = Input::get('server_id');
		if(count($servers) == 0){
			return Response::json(array('error'=>'请选择服务器!'), 403);
		}
		if((count($servers) == 1) && 0 == $servers[0]){
			return Response::json(array('error'=>'请选择服务器!'), 403);
		}
		$result = array();
		foreach ($servers as $server_id) {
			$server = Server::find($server_id);
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->updateexcel($game->game_code);
			if(isset($response->result)){
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
		                    'status' => 'ok'
		            );
		    }else{
		            $result[] = array(
		                    'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
		                    'status' => 'error'
		            );
		    }
		}
		$msg = array(
			'result' => $result
		);
		return Response::json($msg);  

	}

    public function getSource()
    {
        $server_id1 = Input::get('server_id1');
        $server_id1 = Server::find($server_id1)->server_internal_id;
        $server = $this->initTable3();
        $server = $server->getData();
        $server = (array)$server;
        //$game_id = 9;
        $len = count($server);
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        for ($i=0; $i < $len; $i++) { 
            if ($game_id != $server[$i]->gameid) { 
                continue;
            } else {
                $ser = array();
                if ($server[$i]->serverid1 == $server_id1) {
                    $arr = explode(',', $server[$i]->serverid2);
                    for ($j=0; $j < count($arr); $j++) { 
                        $ser[$j]['server_id'] = $arr[$j];
                        $ss = Server::where('game_id', '=', $game_id)->get();
                        for ($k=0; $k < count($ss); $k++) { 
                            if ($ss[$k]->server_internal_id == $arr[$j]) {
                                //$ser[$j]['server_name'] = $ss[$k]->server_name;
                            	$ser[] = $ss[$k];
                            }
                        }

                    }
                    return Response::json($ser);
                }
            }       
        }
       
    }

    public function playerLoginIndex()
	{
	    //$servers = $this->getUnionServers();
	    $server_init = (int)Input::get("server_init");
	    $player_id = (int)Input::get("player_id");
	    $servers = $this->getUnionServers();
	    $game_id = Session::get('game_id');
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.player.loginIndex', array(
	                    'servers' => $servers,
	                    'game_id' => $game_id,
	                    'server_init' => $server_init,
	                    'player_id' => $player_id
	            ))
	    );
	    return View::make('main', $data);
	}
	 public function playerLoginData()
    {
        $msg = array(
				'code' => Config::get('errorcode.unknow'),
				'error' => Lang::get('error.basic_input_error')
		);
		$rules = array(
				'id_or_name' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if($validator->fails())
		{
			return Response::json($msg, 403);
		}
		$choice = ( int ) Input::get('choice');
		
		$server_id = ( int ) Input::get('server_id');
		
		$server = Server::find($server_id);
		$server_internal_id = 0;
		if($server)
		{
			$server_internal_id = $server->server_internal_id;
		}
		$servers = array();
		
		$game = Game::find(Session::get('game_id'));
		$game_id = Session::get('game_id');
		$platform_id = Session::get('platform_id');
		$platform = Platform::find(Session::get('platform_id'));
		$id_or_name = Input::get('id_or_name');
		$start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
		$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);	
		if($choice == 0)
		{
			$response = $slave_api->getPlayerLoginName($platform_id, $id_or_name, $server_internal_id, $game->game_id, $start_time, $end_time);
        } else if($choice == 1)
		{
			$response = $slave_api->getPlayerLoginID($platform_id, $id_or_name, $server_internal_id, $game->game_id, $start_time, $end_time);
		}
		$data = array();
        if ($response->http_code == 200) {
        	$body = $response->body;
            foreach ($body as $key => $value) {
                $data[] = array(
                    'time' => $value->time,
                    'statu' =>  $value->statu,
                    'lev' => $value->level,
                    'last_ip' =>  $value->last_ip,
                );
            }
        }
        if (!empty($data)) {
            return Response::json($data);
        } else{
          return Response::json(array('error'=> 'Not Found data'),403);
        }
       
    }
    //查看补储记录
    public function restorelogload(){
    	$games_info = Game::selectRaw('game_id, game_name')->get();
    	$games = array();
    	foreach ($games_info as $value) {
    		$games[$value->game_id] = $value->game_name;
    	}
	    $data = array(
	            'content' => View::make('serverapi.flsg_nszj.player.restorelog', array(
	                    'games' => $games
	            ))
	    );
	    return View::make('main', $data);    	
    }

    public function restorelogserach(){
    	$game_id = Input::get('game_id');
    	if(!$game_id){
    		$game_id = Session::get('game_id');
    	}
    	$game_name = array();
    	if('999' != $game_id){
    		$game_name[$game_id] = Game::find($game_id)->game_name;
    	}else{
    		$games_info = Game::selectRaw('game_id, game_name')->get();
	    	foreach ($games_info as $value) {
	    		$game_name[$value->game_id] = $value->game_name;
	    	}
    	}

    	$start_time = strtotime(Input::get('start_time'));
    	$end_time = strtotime(Input::get('end_time'));

    	if('999' == $game_id){
	    	$logs = EastBlueLog::where('log_key', 'restore')->whereBetween('created_at', array($start_time, $end_time))
					        ->orderBy('created_at', 'desc')
					        ->get();
    	}else{
	    	$logs = EastBlueLog::where('log_key', 'restore')->where('game_id', $game_id)->whereBetween('created_at', array($start_time, $end_time))
					        ->orderBy('created_at', 'desc')
					        ->get();
		}

		foreach ($logs as $v) {
            $desc_array = explode("|", $v->desc);
            $v->order_id = $desc_array[0];
            $v->pay_amount = $desc_array[1];
            $v->yuanbao_amount = $desc_array[2];
            $v->giftbag_id = isset($desc_array[3]) ? $desc_array[3] : '';
            $v->server_name = isset($desc_array[4]) ? $desc_array[4] : '';
            $v->player_name = isset($desc_array[5]) ? $desc_array[5] : '';
            $v->player_id = isset($desc_array[6]) ? $desc_array[6] : '';
            $v->username = DB::table('users')->where('user_id',$v->user_id)->first()->username;
            $time = (array)$v->created_at;
            $v->operate_time = $time['date'];
            $v->game_name = $game_name[$v->game_id];
        }
        if(count($logs) > 0){
        	return Response::json($logs);
        }else{
        	return Response::json(array('error'=>'此段时间内无数据'), 403);
        }
    }
    
    //合服--修改server.txt中的内容
    public function mergeserversload(){
    	$servers = $this->getUnionServers();
    	$data = array(
	            'content' => View::make('serverapi.mergeservers', array(
	                    'servers' => $servers
	            ))
	    );
	    return View::make('main', $data);    
    }

    public function mergeserversmerge(){
    	$master_server_id = Input::get('master_server_id');
    	$slave_server_ids = Input::get('slave_server_ids');
    	if('0' == $master_server_id){
    		return Response::json(array('error'=>'请选定主服'), 403);
    	}
    	if('0' == count($slave_server_ids)){
    		return Response::json(array('error'=>'请选定从服'), 403);
    	}
    	$game_id = Session::get('game_id');
    	$game_name = Game::find($game_id)->game_name;
    	$master_server_internal_id = Server::find($master_server_id)->server_internal_id;
    	$message = '合并游戏-'.$game_name.'-的服务器-将';
    	$slave_server_internal_ids = array();
    	$slave_server_names = array();

    	$table = Table::initArray(public_path() . '/table/' . 'flsg' . '/server.txt');
        $table = $table->getData();

    	foreach ($slave_server_ids as $server_id) {
    		$slave_server_internal_ids[] = Server::find($server_id)->server_internal_id;
    		$slave_server_names[] = Server::find($server_id)->server_name;
    	}
    	$towrite = $game_id."\t".$master_server_internal_id."\t";
    	if(in_array($master_server_internal_id, $slave_server_internal_ids)){
    		return Response::json(array('error'=>'从服中请不要再选定主服'), 403);
    	}
    	foreach ($slave_server_internal_ids as $value) {	
    		$towrite .= $value.',';
    		foreach ($table as $key => $serverdata) {
    			if($serverdata['serverid1'] == $value && $serverdata['gameid'] == $game_id){
    				$towrite .= $serverdata['serverid2'].',';
    				unset($table[$key]);
    			}
    		}
    	}
    	foreach ($table as $key => $serverdata) {
    		if($serverdata['serverid1'] == $master_server_internal_id  && $serverdata['gameid'] == $game_id){
				$towrite .= $serverdata['serverid2'].',';
				unset($table[$key]);
    		}
    	}
    	foreach ($slave_server_names as $value) {
    		$message .= $value.',';
    	}

        unlink(public_path() . '/table/' . 'flsg' . '/server.txt');
        $titletowrite = "id\t主服\t从服\ngameid\tserverid1\tserverid2\n";
        file_put_contents(public_path().'/table/flsg/server.txt', $titletowrite, FILE_APPEND);

    	$towrite = substr($towrite,0,strlen($towrite)-1)."\n";
    	$charnum = file_put_contents(public_path().'/table/flsg/server.txt', $towrite, FILE_APPEND);

        foreach ($table as $value) {
        	if($value['gameid']){
	        	$towrite = $value['gameid']."\t". $value['serverid1']."\t". $value['serverid2']."\n";
	        	file_put_contents(public_path().'/table/flsg/server.txt', $towrite, FILE_APPEND);
        	}
        }

    	$message .= '-合并为-'.Server::find($master_server_id)->server_name.'--成功';
    	if($charnum > 0){
    		$result = array('result' => $message);
    		return Response::json($result);
    	}else{
    		return Response::json(array('error'=>'合服出现错误'), 403);
    	}
    }

    public function WebGameVipPlayerIndex(){	//页游查询服务器玩家VIP等级在某个值之上的玩家
    	$servers = $this->getUnionServers();
    	$game = Game::find(Session::get('game_id'));
    	$data = array(
	            'content' => View::make('serverapi.flsg_nszj.player.getvipplayer', array(
	                    'servers' => $servers,
	                    'game_code' => $game->game_code
	            ))
	    );
	    return View::make('main', $data);    
    }

    public function WebGameVipPlayerGet(){
    	$game = Game::find(Session::get('game_id'));
    	$game_code = $game->game_code;
    	$server_ids = Input::get('server_ids');
    	$min_vip_level = (int)Input::get('min_vip_level');
    	if(count($server_ids) == 0){
    		return Response::json(array('error'=>'请选择服务器'), 403);
    	}
    	if('nszj' == $game_code && (!$min_vip_level || $min_vip_level < 1 || $min_vip_level > 12)){
    		return Response::json(array('error'=>'请输入合法的最低VIP等级'), 403);
    	}

    	if('flsg' == $game_code){
    		$vip_levels = Input::get('vip_level');
    		if(count($vip_levels) == 0){
    			return Response::json(array('error'=>'请选择vip等级'), 403);
    		}
    		$min_vip_level = (int)$vip_levels[0];
    		$show_vip = array();
    		foreach ($vip_levels as $value) {
    			$show_vip['vip'.$value] = 0;
    		}
    	}

    	$result = array();

    	if('flsg' == $game_code){
	    	foreach ($server_ids as $server_id) {
	    		//Log::info(var_export($show_vip,true));die();
	    		$server = Server::find($server_id);
	    		if(!$server){
	    			return Response::json(array('error'=>'无效的服务器'), 403);
	    		}
				$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
				$tmp_result = $api->getvipplayer($min_vip_level,$game->game_code);
				if(isset($tmp_result->players)){	//判断返回值是否标准
					$vip_players = $tmp_result->players;
					unset($tmp_result);
					if($vip_players){
						$rank_array = array();
						$temp_players = array();
						foreach ($vip_players as $key => $value) {
							if(in_array($value->vip, $vip_levels)){
								$rank_array[$key] = $value->vip;
								$temp_players[$key] = $value;
							}
						}
						array_multisort($rank_array, SORT_ASC, $temp_players);
						foreach ($temp_players as $key => $value) {
							$show_vip['vip'.$value->vip]++;//将改玩家统计在其所在的等级中
							$result['player'][] = array(
									'server_name'	=>	$server->server_name,
									'player_id'	=>	$value->player_id,
									'player_name'	=>	$value->name,
									'vip' =>$value->vip,
								);
							
						}
						unset($rank_array);
						unset($temp_players);
					}	
				}else{
					//return Response::json(array('error'=>"连接游戏服务器 {$server->server_name} 异常"), 403);
				}
				unset($server);
	    	}
	    	foreach ($show_vip as $key => $value) {//将一维数组show_vip转化为result数组的number元素
	    		$result['number'][] = array(
					'vip_level'	=>	$key,
					'vipnum'	=>	$value,
					);
	    	}
    	}else{
	    	foreach ($server_ids as $server_id) {
	    		$server = Server::find($server_id);
	    		if(!$server){
	    			return Response::json(array('error'=>'无效的服务器'), 403);
	    		}
				$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
				$tmp_result = $api->getvipplayer($min_vip_level,$game->game_code);
				if(isset($tmp_result->vip_players)){	//判断返回值是否标准
					$vip_players = $tmp_result->vip_players;
					unset($tmp_result);
					if($vip_players){
						$result['number'][] = array(
							'server_name'	=>	$server->server_name,
							'vipnum'	=>	count($vip_players),
							);
						foreach ($vip_players as $value) {
							$result['player'][] = array(
								'server_name'	=>	$server->server_name,
								'player_id'	=>	$value->player_id,
								'player_name'	=>	$value->player_name,
								'vip'	=>	$value->vip_level,
							);
						}
					}
				}else{
					//return Response::json(array('error'=>"连接游戏服务器 {$server->server_name} 异常"), 403);
				}
				unset($server);
	    	}
    	}
    	
    	if(count($result) > 0){
    		return Response::json($result);
    	}else{
    		return Response::json(array('error'=>'没有结果'), 403);
    	}
    }

    public function playerEmbattleIndex()
    {
        $servers = $this->getUnionServers();
        if (empty($servers)) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('serverapi.flsg_nszj.player.player_embattle',
                array(
                    'servers' => $servers,
                ))
        );
        return View::make('main', $data);
    }
    
    public function getPlayerEmbattle()
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
            $response = $api->getPlayerEmbattle($player_id);
            $tmp_result = $player_id.'--'.var_export($response, true);
			$result[] = $tmp_result; 
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
                $response = $api->getPlayerEmbattle($text_data[0]);
            	$tmp_result = $text_data[0].'--'.var_export($response, true);
				$result[] = $tmp_result;
				unset($tmp_result);
            }
            
        }else{
            return Response::json($msg, 403);
        }
        return Response::json(array('result' => $result));
    }
  
}