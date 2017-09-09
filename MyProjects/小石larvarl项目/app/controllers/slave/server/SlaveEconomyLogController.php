<?php
class SlaveEconomyLogController extends \SlaveServerBaseController {
	private $types = array(
			'yuanbao',
			'tongqian',
			'shengwang',
			'tili',
			'jingjiedian',
			'yueli',
			'xianling',
			'boat_book',
			'lingshi',
	);
	private $extra_types = array(
			'star_fragment',
			'talent_point',
			'heaven_token',
			'skill_fragment',
			'fight_spirit',
	);

	private $mnsg_new_types = array(
			"top_coin" => "top_coin",
			"guild_coin" => "guild_coin",
			"region_coin" => "keys1",
			);
	

	public function __construct(EconomyLog $model)
	{
		parent::__construct();
		$this->primary_key = $model->getKeyName();
	}
	public function getPlayerEconomy()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$player_id = ( int ) Input::get('player_id');
		
		$type = Input::get('type');
		$look_type = Input::get('look_type');
		$action_type_num = Input::get('action_type_num');
		$page = ( int ) Input::get('page');
		$per_page = ( int ) Input::get('per_page');
		$page = $page > 0 ? $page : 1;
		$per_page = $per_page > 0 ? $per_page : 30;
		$per_page = min($per_page, 30);
		$field = '';
		$field_2 = '';
		if($type == 'tili'){
			$field = 'diff_tili';
			$field_2 = 'diff_extra_tili'; 
		}if($type == 'end_rings_exp'){
			$field = "(end_rings_exp-start_rings_exp)";
		}else{
			$field = 'diff_' . $type;
		}
		
		$start_time = ( int ) Input::get('start_time');
		$end_time = ( int ) Input::get('end_time');
		if(in_array($type, $this->types)){//查询log_economy表
			$count = EconomyLog::on($this->db_name)->playerEconomyCount($player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)->count();
			//$spend_all = EconomyLog::on($this->db_name)->playerSpend($player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)->first();
			$total = ceil($count / $per_page);
		
			$log = EconomyLog::on($this->db_name)->playerEconomy($player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)->forPage($page, $per_page)->get();
			$result = array(
				'count' => $count,
				'total' => $total,
				'per_page' => $per_page,
				//'spend_all' => $spend_all->spend,
				'current_page' => $page,
				'items' => $log->toArray()
			);
			return Response::json($result);//直接返回
		}else{
			if('end_rings_exp' == $type){//对其他三个表处理
				$table = "log_rings";
			}elseif(in_array($type, $this->extra_types)){
				$table = "log_economy_extra";
			}else{
				$table = "log_economy_third";
			}
			
			$log = DB::connection($this->db_name)->table("$table")
				->whereBetween('action_time',array($start_time,$end_time))
				->where('player_id',$player_id);
				if(1 == $look_type){
					$log->whereRaw($field.' > 0');
				}elseif(2 == $look_type){
					$log->whereRaw($field.' < 0');
				}elseif (3 == $look_type) {
					$log->whereRaw($field.' <> 0');
				}
				
				if($action_type_num){
					$log->where('action_type',$action_type_num);
				}

			$count = $log->count();
			$total = ceil($count / $per_page);

			$log = $log->selectRaw("$field as spend,action_type, action_time,".(('fruit_currency' == $type) ? "fruit_bet,$type" : "$type"))
				->forPage($page, $per_page)->get();

		}
		$result = array(
			'count' => $count,
			'total' => $total,
			'per_page' => $per_page,
			//'spend_all' => $spend_all[0]->spend,
			'current_page' => $page,
			'items' => $log
		);
		return Response::json($result);
		
	}
	public function getSimplePlayerEconomy()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$player_id = ( int ) Input::get('player_id');
		
		$type = Input::get('type');
		
		if(! in_array($type, $this->types))
		{
			$msg['error'] = Lang::get('error.slave_economy_type');
			return Response::json($msg, 403);
		}
		
		$page = ( int ) Input::get('page');
		$per_page = ( int ) Input::get('per_page');
		$page = $page > 0 ? $page : 1;
		$per_page = $per_page > 0 ? $per_page : 30;
		//$per_page = min($per_page, 30);
		$field = '';
		$field_2 = '';
		if($type == $this->types[0])
		{
			$field = 'diff_yuanbao';
		} else if($type == $this->types[1])
		{
			$field = 'diff_tongqian';
		} else if($type == $this->types[2])
		{
			$field = 'diff_shengwang';
		} else if($type == $this->types[3])
		{
			$field = 'diff_tili';
			$field_2 = 'diff_extra_tili';
		} else if($type == $this->types[4])
		{
			$field = 'diff_jingjiedian';
		} else if($type == $this->types[5])
		{
			$field = 'diff_yueli';
		} else if($type == $this->types[6])
		{
			$field = 'diff_xianling';
		}else if($type == $this->types[7])
		{
			$field = 'diff_boat_book';
		}else if($type == $this->types[8])
		{
			$field = 'diff_lingshi';
		}
		
		$start_time = ( int ) Input::get('start_time');
		$end_time = ( int ) Input::get('end_time');
		$test_one = time();
		$log = EconomyLog::on($this->db_name)->simplePlayerEconomy($player_id, $field, $start_time, $end_time, $field_2)->forPage($page, $per_page)->get();
		$select_time = time() - $test_one;
		$time_arr = array('select_time' => $select_time);
		// $s = var_export($time_arr,true);
		// Log::info($s);
		return Response::json($log);
	}
	
	public function getSimplePlayerEconomyTotal()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$player_id = ( int ) Input::get('player_id');
		
		$type = Input::get('type');
		
		if(! in_array($type, $this->types))
		{
			$msg['error'] = Lang::get('error.slave_economy_type');
			return Response::json($msg, 403);
		}
		
		$page = ( int ) Input::get('page');
		$per_page = ( int ) Input::get('per_page');
		$page = $page > 0 ? $page : 1;
		$per_page = $per_page > 0 ? $per_page : 30;
		//$per_page = min($per_page, 30);
		$field = '';
		$field_2 = '';
		$field = 'diff_yuanbao';
		
		$start_time = ( int ) Input::get('start_time');
		$end_time = ( int ) Input::get('end_time');
		$count = EconomyLog::on($this->db_name)->playerEconomyCount($player_id, $field, $start_time, $end_time, $field_2)->count();
		
		$total = ceil($count / $per_page);
		
		$result = array(
				'count' => $count,
				'total' => $total,
				'per_page' => $per_page,
				'current_page' => $page,
		);
		
		return Response::json($result);
	}
	public function getAllPlayerEconomy()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$player_id = ( int ) Input::get('player_id');
		
		$type = Input::get('type');
		
		if(! in_array($type, $this->types))
		{
			$msg['error'] = Lang::get('error.slave_economy_type');
			return Response::json($msg, 403);
		}
		
		$field_2 = '';
		$field = 'diff_yuanbao';
		
		$start_time = ( int ) Input::get('start_time');
		$end_time = ( int ) Input::get('end_time');
		$log = EconomyLog::on($this->db_name)->playerEconomy($player_id, $field, $start_time, $end_time, $field_2)->get();
		return Response::json($log);
	}
	public function getPlayerEconomyStatistics()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$player_id = ( int ) Input::get('player_id');
		
		$type = Input::get('type');
		
		$start_time = ( int ) Input::get('start_time');
		$end_time = ( int ) Input::get('end_time');
		$look_type = (int) Input::get('look_type');
		$action_type_num = Input::get('action_type_num');
		$field = '';
		$field_2 = '';
		if('tili' == $type){
			$field = 'diff_tili';
			$field_2 = 'diff_extra_tili'; 
		}elseif('end_rings_exp' == $type){
			$field = "(end_rings_exp-start_rings_exp)";
		}else{
			$field = 'diff_' . $type;
		}
		if(in_array($type, $this->types)){//三个条件是因为风流三国要在四个表中查询
			$log = EconomyLog::on($this->db_name)->playerEconomyStatistics($player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)->get();
		}else{
			if('end_rings_exp' == $type){//对其他三个表处理
				$table = "log_rings";
			}elseif(in_array($type, $this->extra_types)){
				$table = "log_economy_extra";
			}else{
				$table = "log_economy_third";
			}

			$log = DB::connection($this->db_name)->table("$table")
				->whereBetween('action_time',array($start_time,$end_time));
				if(1 == $look_type){
					$log->whereRaw($field.' > 0');
				}elseif(2 == $look_type){
					$log->whereRaw($field.' < 0');
				}elseif (3 == $look_type) {
					$log->whereRaw($field.' <> 0');
				}

				if($action_type_num){
					$log->where('action_type',$action_type_num);
				}else{
					$log->groupBy('action_type');
				}

				if($player_id){
					$log->where('player_id',$player_id);
				}else{
					$log->groupBy('player_id');
				}
				$log = $log->selectRaw("SUM($field) as spend,action_type, action_time, player_id,COUNT(1) as times")
					->get();
			
		}
		//Log::info("tong ji::get player economy log=========================>".$log);
		if(! $log)
		{
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else
		{
			return Response::json($log);
		}
	}
	public function getPlayerEconomyRank()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$type = Input::get('type');
		$game_id = (int)Input::get('game_id');

		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$start_time = (int)Input::get('start_time');
			$end_time = (int)Input::get('end_time');
			$field = 'diff_'.$type;
			$symbole = '<';
			$order = 'ASC';
			$rank = EconomyLog::on($this->db_name)->playerEconomyRankWithTime($field, $symbole, $order, $start_time, $end_time)->take(50)->get();
		}else{
			if(! in_array($type, $this->types))
			{
				$msg['error'] = Lang::get('error.slave_economy_type');
				return Response::json($msg, 403);
			}		
			$field = '';
			if($type == $this->types[0])
			{
				$field = 'diff_yuanbao';
				$symbole = '<';
				$order = 'ASC';
			} else if($type == $this->types[1])
			{
				$field = 'diff_tongqian';
				$symbole = '<';
				$order = 'ASC';
			} else if($type == $this->types[2])
			{
				$field = 'diff_shengwang';
				$symbole = '<>';
				$order = 'DESC';
			}	
			$rank = EconomyLog::on($this->db_name)->playerEconomyRank($field, $symbole, $order)->take(50)->get();
		}
		if(! $rank)
		{
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else
		{
			return Response::json($rank);
		}
	}
	public function getPlayerEconomyAnalysis()
	{
        $msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$type = Input::get('type');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$lower_bound = (int) Input::get('lower_bound');
		$upper_bound = (int) Input::get('upper_bound');
		$action_type= (int)Input::get('action_type');
		$game_id = (int)Input::get('game_id');
		$no_name = Input::get('no_name');
		/*if(! in_array($type, $this->types))
		{
			$msg['error'] = Lang::get('error.slave_economy_type');
			return Response::json($msg, 403);
		}*/
		
		$field = '';
		if($type == $this->types[2])
		{
			$field = 'diff_shengwang';
			$symbole = '<>';
			$order = 'DESC';
		}else{
			$field = 'diff_'. $type;
			$symbole = '<';
			$order = 'ASC';
		}

		$rank = EconomyLog::on($this->db_name)->playerEconomyAnalysis($this->db_qiqiwu,$field, $symbole, $order, $start_time, $end_time, $lower_bound, $upper_bound,$action_type,$game_id, $no_name)->get();
		if(! $rank)
		{
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else
		{
			return Response::json($rank);
		}
	}

	public function getServersConsume(){
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$type = Input::get('type');
		$field = '';
		if($type == $this->types[0])
		{
			$field = 'diff_yuanbao';
			$symbole = '<';
			$order = 'ASC';
		} else if($type == $this->types[1])
		{
			$field = 'diff_tongqian';
			$symbole = '<';
			$order = 'ASC';
		} else if($type == $this->types[2])
		{
			$field = 'diff_shengwang';
			$symbole = '<>';
			$order = 'DESC';
		}
		$spend = EconomyLog::on($this->db_name)->serverEconomy($field,$symbole,$start_time,$end_time)->first();
		if(!$spend){
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg,404);
		}else{
			return Response::json($spend);
		}
	}
	public function findBossKiller()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
	
		$first_time = strtotime(date("Y-m-d 15:00:00", $start_time));
    	$last_time = strtotime(date("Y-m-d 15:00:00", $end_time));
    	$days = ($last_time - $first_time) / (60*60*24);
    	$result = array();
		for($i = 0; $i <= $days; $i ++){
		    $start_time = $first_time + 86400 * $i;//每天有两次打世界boss
		    $start_time1 = strtotime(date("Y-m-d 15:00:00", $start_time));
		    $end_time1 = strtotime(date("Y-m-d 17:00:00", $start_time));
		    $start_time2 = strtotime(date("Y-m-d 20:00:00", $start_time));
		    $end_time2 = strtotime(date("Y-m-d 23:00:00", $start_time));
		    $single_data1 = EconomyLog::on($this->db_name)->findBossKiller($start_time1, $end_time1)->first();
		    $single_data2 = EconomyLog::on($this->db_name)->findBossKiller($start_time2, $end_time2)->first();
		    if(! empty($single_data1)){
		        if(array_key_exists($single_data1->player_id, $result)){
		            $result[$single_data1->player_id]['times'] ++;
		            $result[$single_data1->player_id]['action_time'] = $result[$single_data1->player_id]['action_time']  . ";" . $single_data1->action_time;
		        } else {
		            $result[$single_data1->player_id] = array(
// 		                    'max_diff_tongqian' => $single_data1->max_diff_tongqian,
// 		                    'start_time' => $start_time1,
// 		                    'end_time' => $end_time1,
		                    'times' => 1,
		                    'player_id' => $single_data1->player_id,
		                    'player_name' => $single_data1->player_name,
		                    'action_time' => $single_data1->action_time,
		            );
		        }
		    }
		    if(! empty($single_data2)){
		        if(array_key_exists($single_data2->player_id, $result)){
		            $result[$single_data2->player_id]['times'] ++;
		            $result[$single_data2->player_id]['action_time'] = $result[$single_data2->player_id]['action_time'] . ";" . $single_data2->action_time;
		        } else {
		            $result[$single_data2->player_id] = array(
// 		                'max_diff_tongqian' => $single_data2->max_diff_tongqian,
// 		                'start_time' => $start_time2,
// 		                'end_time' => $end_time2,
		                'times' => 1,
		                'player_id' => $single_data2->player_id,
		                'player_name' => $single_data2->player_name,
		                'action_time' => $single_data2->action_time,
		           );
		        }
		    }
		}
		
		if(! $result)
		{
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else
		{
			return Response::json($result);
		}
	}
	private function initVipArray($game_id)
	{
	    $game_code = 'flsg';
	    // if($game_id == 8 || $game_id == 36){	//三国女神的vip档位相同，因此都使用三国的
	    //     $game_code = 'nszj';
	    // }
	    $table = Table::init(
	            public_path() . '/table/' . $game_code . '/game_vip.txt');
	     
	    $messages = $table->getData();
	    $vip_arr = array();
	    $key = 0;
	    $vip_arr[$key++] = array(
	    	'min' => 0,
	    	'max' => 100,
	    );
	    foreach ($messages as $k => $v)
	    {
	        $vip_arr[$key++] = array(
                'min' => (int)$v->min,
                'max' => (int)$v->max,
	        );
	    }
	    return $vip_arr;
	}
	private function getNeiwan($game_id)
	{
		$table = Table::init(public_path() . '/table/neiwan.txt');
		$message = $table->getData();
		$neiwan_uids = array();
		foreach ( $message as $k => $v )
		{
			if($v->game_id == $game_id)
			{
				$neiwan_uids[] = $v->uid;
			}
		}
		return $neiwan_uids;
	}
	public function getServerEconomyStatistics()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_server_economy'),
				'error' => ''
		);
		
		$db_qiqiwu = $this->db_qiqiwu;
		$db_payment = $this->db_payment;
		$game_id = $this->game_id;
		$server_internal_id = Input::get('server_internal_id');
		$neiwan_uids = $this->getNeiwan($this->game_id);
		
		$type = Input::get('type');
		$is_filter_neiwan =(int)Input::get('is_filter_neiwan');
		$vip_selector = trim(Input::get('vip_selector'));
		
		$vip_selector_arr = explode(',', $vip_selector, '13');
		
		$vip_arr = $this->initVipArray($this->game_id);

		$vip_parts = $this->getVipDevideParts($vip_selector_arr, $vip_arr);
		$by_player_id = 0;
		
		if(! in_array($type, $this->types))
		{
			$msg['error'] = Lang::get('error.slave_economy_type');
			return Response::json($msg, 403);
		}
		
		$start_time = ( int ) Input::get('start_time');
		$end_time = ( int ) Input::get('end_time');
		$player_level = ( int ) Input::get('player_level');
		
		$field = '';
		$order_by = 'ASC';
		if($type == $this->types[0])
		{
			$field = 'diff_yuanbao';
			$symbole = '<';
		} else if($type == $this->types[1])
		{
			$field = 'diff_tongqian';
			$symbole = '<';
		} else if($type == $this->types[2])
		{
			$field = 'diff_shengwang';
			$symbole = '<>';
			$order_by = 'DESC';
		}
		// 在payment库pay_order表中查询vip_player
		$vip_user_uids = array();
		if(count($vip_parts)){	//获取所有VIP的UID
			$by_player_id = 1;
		    foreach ($vip_parts as $vip_part) {
		        $vip_players = PayOrder::on($this->db_payment)
		        					->join('server_list as sl', function ($join){
		        						$join->on('sl.game_id', '=', 'o.game_id')
		        							 ->on('o.server_id', '=', 'sl.server_id');
		        					})
		        					->where('o.game_id', $game_id)
		        					->where('o.get_payment', 1)
		        					->where('sl.server_internal_id', $server_internal_id)
		        					->groupBy('o.pay_user_id')
		        					->havingRaw("sum(o.yuanbao_amount) between {$vip_part['lower_bound']} and {$vip_part['upper_bound']}")
		        					->selectRaw('pay_user_id')
		        					->get();
		        if(count($vip_players)){
		            foreach ($vip_players as $v){
		                $vip_user_uids[] = $v->pay_user_id;
		            }
		        }
		    }
		}

		$player_level = $player_level>0 ? $player_level : 0;
		// 在levelup表里筛选等级玩家
		$player_ids = array();
		if($player_level){	//找出符合等级条件和vip条件的玩家id
			$by_player_id = 1;
			if($vip_user_uids){
				$player_id_sql = CreatePlayerLog::on($this->db_name)
								->join('log_levelup as ll', function($join) use ($player_level){
									$join->on('p.player_id', '=', 'll.player_id')
										 ->where('ll.new_level', '>=', $player_level);
								})
								->whereIn('p.user_id', $vip_user_uids)
								->selectRaw("distinct p.player_id")
								->get();
			}else{
				$player_id_sql = LevelUpLog::on($this->db_name)
									->where('new_level', '>=', $player_level)
									->selectRaw("distinct player_id")
									->get();
			}
		}else{
			if($vip_user_uids){
				$player_id_sql = CreatePlayerLog::on($this->db_name)
								->whereIn('user_id', $vip_user_uids)
								->selectRaw("distinct player_id")
								->get();
			}else{
				$player_id_sql = array();
			}
		}
		foreach ($player_id_sql as $sql_result) {
			$player_ids[] = $sql_result->player_id;
		}

		if($is_filter_neiwan && $neiwan_uids){	//过滤内玩
			$neiwan_player_id_sql = CreatePlayerLog::whereIn('user_id', $neiwan_uids)->selectRaw('player_id')->get();
			$neiwan_player_ids = array();
			if(count($neiwan_player_id_sql)){
				foreach ($neiwan_player_id_sql as $neiwan_player_info) {
					$neiwan_player_ids[] = $neiwan_player_info->player_id;
				}
			}
			foreach ($player_ids as $key => $player_id) {
				if(in_array($player_id, $neiwan_player_ids)){
					unset($player_ids[$key]);
				}
			}
		}

		$log = EconomyLog::on($this->db_name)
			->selectRaw("SUM({$field}) as spend, count(distinct player_id) as num, action_type, action_time")
			->whereBetween('action_time', array($start_time, $end_time))
			->where($field, $symbole, 0)
			->groupBy('action_type')
			->orderBy('spend', $order_by);
		if($by_player_id)
		{
			if(count($player_ids)){
				$log->whereIn('player_id', $player_ids);
				$log = $log->get();
			}else{
				$log = array();
			}
		} else {
			$log = $log->get();
		} 
		if(!count($log))
		{
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else
		{
			return Response::json($log);
		}
	}

	private function getVipDevideParts($vip_selector_arr, $vip_arr){
		if(13 == array_sum($vip_selector_arr)){
			$res = array();
		}else{
			$res = array();
			$lower_bound = 0;
			$upper_bound = 0;
			foreach ($vip_selector_arr as $key => $value) {
				if(1 == $value){
					if(12 == $key){
						$upper_bound = $vip_arr[$key]['max'];
						$res[] = array(
							'lower_bound' => $lower_bound,
							'upper_bound' => $upper_bound,
							);
					}
					$upper_bound = $vip_arr[$key]['max'];
				}else{
					if($upper_bound > $lower_bound){
						$res[] = array(
							'lower_bound' => $lower_bound,
							'upper_bound' => $upper_bound,
							);
					}
					$upper_bound = 0;
					$lower_bound = $vip_arr[$key]['max'];
				}
			}
		}
		return $res;
	}

	//查看玩家exp

	public function getUserExp()
    {
        $msg = array(
                        'code' => Config::get('errorcode.slave_server_economy'),
                        'error' => ''
        );
        $platform_id = Input::get('platform_id');
        $server_internal_id = Input::get('server_internal_id');
        $game_id = Input::get('game_id');
        $player_id = Input::get('player_id');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $item_id = Input::get('item_id');
        $db = DB::connection($this->db_name);
        $type = Input::get('type');

        $page = ( int ) Input::get('page');
        $per_page = ( int ) Input::get('per_page');
        $page = $page > 0 ? $page : 1;
        if ($type == 1) {
        	$exist_type = $db->table('log_item')->first();
        	$field_type = isset($exist_type->type) ? 'type' : 0;
        	$exp = $db->table('log_item')->whereBetween('time',array($start_time,$end_time));
    		if($item_id){
    			$exp->where('item_id',$item_id);
    		}
    		if($player_id){
    			$exp->where('player_id',$player_id);
    		}
        	$count = $exp->count();
        	$total = ceil($count / $per_page);
        	$exp = $exp->forPage($page, $per_page)
        		->selectRaw("num, FROM_UNIXTIME(time) as time,item_id, player_id,{$field_type} as type")
        		->get();
        }elseif ($type == 2) {
        	$exp = $db->table('log_exp')->whereBetween('time',array($start_time,$end_time))
        			->where('player_id',$player_id);
        	$count = $exp->count();
        	$total = ceil($count / $per_page);
        	$exp = $exp->forPage($page, $per_page)
        		->selectRaw("exp, FROM_UNIXTIME(time) as time , action_type, player_id")
        		->get();
        }
        $result = array(
        	'count' => $count,
        	'total' => $total,
        	'per_page' => $per_page,
        	'current_page' => $page,
        	'items' => $exp
        );
        if ($exp) {
            return Response::json($result);
        } else {
            $msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
        }
    }
   	//全服剩余元宝数量
    public function getServerRemainYuanbao(){
        $server_internal_id = Input::get('server_internal_id');
        $game_id = Input::get('game_id');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');

        $db = DB::connection($this->db_name);
////////
        $sql = $db->select("
			select distinct lcp.player_id as player_id 
			from `{$this->db_payment}`.payment_user as pu
			left join log_create_player as lcp
			on pu.uid = lcp.user_id");
		$result = array();
		if(!empty($sql)){
			foreach ($sql as $value) {
				$result[] = $value->player_id;		
			}
		}
        foreach ($result as $key => $player_id) {
        	if(is_null($player_id)){
        		unset($result[$key]);
        	}
        }
////////
        $res = $db->select("
        	select yuanbao, player_id from log_economy
        	where server_id = {$server_internal_id}
        	and action_time > {$start_time}
        	and action_time < {$end_time}
        	");
        $total = 0;
        foreach ($res as $value) {
        	if(in_array($value->player_id, $result)){
        		$total += $value->yuanbao;
        	}
        }
        $result = array($total);
        return Response::json($result);

     }

	public function findBossKillerNum()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
    	$result = array();
	    $time1 = strtotime(date("Y-m-d 17:00:00", time()));
	    $time2 = strtotime(date("Y-m-d 20:00:00", time()));
	    $time3 = strtotime(date("Y-m-d 15:00:00", time()));
	    $time4 = strtotime(date("Y-m-d 23:00:00", time()));
	    $result = array();
	    if ($end_time <= $time1) { //只查一次的
	    	$single_data = EconomyLog::on($this->db_name)->findBossNum($start_time, $end_time)->first();
	    	if(! empty($single_data)) {
		        $result[0] = array(
		        	'num' => $single_data->num,
		        	'action_time' => $single_data->action_time
		        );
		    }
	    } elseif ($start_time >= $time2) { //只查晚上的
	    	$single_data = EconomyLog::on($this->db_name)->findBossNum($start_time, $end_time)->first();
	    	if(! empty($single_data)) {
		        $result[0] = array(
		        	'num' => $single_data->num,
		        	'action_time' => $single_data->action_time
		        );
		    }
	    } elseif (($start_time <= $time3) && ($end_time >= $time4)) { //查一整天的
			$start_time1 = $start_time;
			$end_time1 = strtotime(date("Y-m-d 17:00:00", $start_time));
			$start_time2 = strtotime(date("Y-m-d 20:00:00", $start_time));
			$end_time2 = strtotime(date("Y-m-d 23:00:00", $start_time));
	    	$single_data1 = EconomyLog::on($this->db_name)->findBossNum($start_time1, $end_time1)->first();
	    	$single_data2 = EconomyLog::on($this->db_name)->findBossNum($start_time2, $end_time2)->first();
	    	if(! empty($single_data1)) {
		        $result[0] = array(
		        	'num' => $single_data1->num,
		        	'action_time' => $single_data1->action_time
		        );
		    }
		    if(! empty($single_data2)) {
		        $result[1] = array(
		        	'num' => $single_data2->num,
		        	'action_time' => $single_data2->action_time
		        );
		    }

	    }		
		if(! $result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}

	public function serverConsumeData()
    {
        $msg = array(
                        'code' => Config::get('errorcode.slave_player_economy'),
                        'error' => ''
        );

        $platform_id = Input::get('platform_id');
        $game_id = Input::get('game_id');
        $type = Input::get('type');
        $server_internal_id = Input::get('server_internal_id');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $db = DB::connection($this->db_name);
        //$sql = "select server_id, player_id, sum(diff_yuanbao) as spend, action_type from log_economy where action_time >= {$start_time} and action_time <= {$end_time} and action_type = {$type} and diff_yuanbao < 0 and server_id = {$server_internal_id} group by player_id order by spend desc";
        $sql = "select le.server_id, le.player_id, sum(le.diff_yuanbao) as spend, le.action_type ,lp.player_name, lp.user_id from log_economy  le left join log_create_player lp on lp.player_id = le.player_id where action_time >= {$start_time} and action_time <= {$end_time} and le.action_type = {$type} and le.diff_yuanbao < 0 and le.server_id = {$server_internal_id} group by le.player_id order by spend desc";
        $info = $db->select($sql);
        if (isset($info)) {
                return Response::json($info);
        } else{
                $msg['error'] = Lang::get('error.slave_result_none');
                return Response::json($msg, 404);
        }
    }


	public function findYwcThree()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_economy'),
			'error' => ''
		);
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$result = EconomyLog::on($this->db_name)->findThree($start_time,$end_time)->get();
		if(!isset($result)){
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg,404);
		} else{
			return Response::json($result);
		}
	}

	public function getServerRemainPlayer()
	{
		$db = DB::connection($this->db_name);
		//Log::info("db_name  ================================> ".var_export($this->db_name, true));
		//Log::info("db  =====================================> ".var_export($db, true));
		$sql = $db->select("
			select distinct lcp.player_id as player_id,player_name 
			from `{$this->db_payment}`.payment_user as pu
			left join log_create_player as lcp
			on pu.uid = lcp.user_id");
		$result = array();
		//Log::info("getServerRemainPlayer=====================> ".var_export($sql, true));
		if(!empty($sql)){
			return Response::json($sql);
		}else{
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg,404);
		}
		
	}
	public function getUserIP()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_economy'),
			'error' => ''
		);
		$player_id = Input::get('player_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$db = DB::connection($this->db_name);
		$sql = $db->select("select remote_host, login_time from log_login where player_id={$player_id} and login_time>{$start_time} and login_time<{$end_time}");
		if($sql){
			return Response::json($sql);
		}else{
			$msg['error'] = "error.slave_result_none";
			return Response::json($msg, 403);
		}
	}

	public function loginPlayersData()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_economy'),
			'error' => ''
		);
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$server_internal_id = Input::get('server_internal_id');
		$db = DB::connection($this->db_name);

		$sql = "SELECT l1.login_time as time1, l2.login_time as time2 , l1.player_id , l1.is_login  as login1, l2.is_login as login2 from log_login l1 left join log_login l2 on l1.player_id = l2.player_id where l1.is_login = 1 and l2.is_login =-1 and l1.login_time >= $start_time and l2.login_time <= $end_time";
		$users = $db->select($sql);
		$num1 = $num2 = $num3 = $num4 = $num5 = $num6 = 0;
		foreach ($users as $key => $value) {
			$value->minutes = "";
			$value->minutes = ceil(($value->time2 - $value->time1)/60);
			if ($value->minutes > 0 && $value->minutes <= 5) {
				$num1 ++;
			}elseif ($value->minutes > 5 && $value->minutes <= 15) {
				$num2 ++;
			}elseif ($value->minutes > 15 && $value->minutes <= 30) {
				$num3 ++;
			}elseif ($value->minutes > 30 && $value->minutes <= 60) {
				$num4 ++;
			}elseif ($value->minutes > 60 && $vaule->minutes <= 120) {
				$num5 ++;
			}elseif ($value->minutes > 120) {
				$num6 ++;
			}
			
		}
		$data = array(
			'num1' => $num1,
			'num2' => $num2,
			'num3' => $num3,
			'num4' => $num4,
			'num5' => $num5,
			'num6' => $num6,
		);
		
		if ($data) {
			return Response::json($data);
		} else {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 403);
		}
 	}

 	/*
	玩家筹码变化  xianshui 2014.11.12
 	*/

 	public function chipsRangeData()
 	{
 		$msg = array(
 			'code' => Config::get('errorcode.slave_player_economy'),
 			'error' => ''
 		);
 		$start_time = Input::get('start_time');
 		$end_time = Input::get('end_time');
 		$player_id = Input::get('player_id');
 		$sort = Input::get('sort');
 		$mid = Input::get('mid');
 		$page = Input::get('page');
 		$per_page = Input::get('per_page');
 		$page = $page > 0 ? $page :1;
 		$per_page = $per_page > 0 ? $per_page : 30;
 		$group_by = Input::get('group_by');
 		if($group_by){
 			if(1 == $group_by){
	 			$count = EconomyLog::on($this->db_name)
					->where('action_type', $mid)
					->whereBetween('action_time', array($start_time, $end_time))
					->selectRaw("count(distinct e.player_id) as count")
					->first()->count;			
			}else{
				$count = 1;
			}
			$players = EconomyLog::on($this->db_name)
				->PokerEconomyChange($start_time, $end_time, $sort, $mid, $group_by)
				->forPage($page, $per_page)
				->get();
 		}else{
	 		$count = EconomyLog::on($this->db_name)
	 			->economys($player_id, $start_time, $end_time, $sort, $mid)
	 			->count();
	 		$players = EconomyLog::on($this->db_name)
	 			->economys($player_id, $start_time, $end_time, $sort, $mid)
	 			->forPage($page, $per_page)
	 			->get(); 
 		}

 		$result = array(
			'count' => $count,
			'total' => ceil($count / $per_page),
			'per_page' => $per_page,
			'current_page' => $page,
			'items' => $players->toArray(),
		);
		
		return Response::json($result);

 	}

 	 public function roundsRangeData()
    {
        $msg = array(
                'code' => Config::get('errorcode.slave_player_economy'),
                'error' => ''
        );
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $player_id = Input::get('player_id');
        $page = Input::get('page');
        $per_page = Input::get('per_page');
        $page = $page > 0 ? $page :1;
        $per_page = $per_page > 0 ? $per_page : 30;
        $db = DB::connection($this->db_name);
        $sql1 = "select room_id, rule_id, players, seat_chips,FROM_UNIXTIME(time) as time  from log_game where players like '%$player_id%' and time >= $start_time and time <= $end_time";
        $offset = $page == 1 ? 0 : ($page-1)*$per_page;
        $sql2 = "select room_id, rule_id,rule_id, period, dealer, public_cards,bet_pools, players, seat_chips,FROM_UNIXTIME(time) as time  from log_game where players like '%$player_id%' and time >= $start_time and time <= $end_time limit $offset, $per_page";
        $cou = $db->select($sql1);
        $count = count($cou);
        $res = $db->select($sql2);
       
        $result = array(
            'count' => $count,
            'total' => ceil($count / $per_page),
            'per_page' => $per_page,
            'current_page' => $page,
            'items' =>$res,
        );
        if ($result) {
            return Response::json($result);
        }else{
            $msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 403);
        }
    }
    public function chipsRecordData()
 	{
 		$msg = array(
 			'code' => Config::get('errorcode.slave_player_economy'),
 			'error' => ''
 		);
 		$start_time = Input::get('start_time');
 		$end_time = Input::get('end_time');
 		$player_id = Input::get('player_id');
 		$page = Input::get('page');
 		$per_page = Input::get('per_page');
 		$page = $page > 0 ? $page :1;
 		$per_page = $per_page > 0 ? $per_page : 30;
 		$count = EconomyLog::on($this->db_name)
 			->findEconomys($player_id, $start_time, $end_time)
 			->count();

 		$players = EconomyLog::on($this->db_name)
 			->findEconomys($player_id, $start_time, $end_time)
 			->forPage($page, $per_page)
 			->get(); 

 		$result = array(
			'count' => $count,
			'total' => ceil($count / $per_page),
			'per_page' => $per_page,
			'current_page' => $page,
			'items' => $players->toArray(),
		);
		
		return Response::json($result);

 	}
 	 public function chipsRecordData2()
 	{
 		$msg = array(
 			'code' => Config::get('errorcode.slave_player_economy'),
 			'error' => ''
 		);
 		$start_time = Input::get('start_time');
 		$end_time = Input::get('end_time');
 		$player_name = Input::get('player_name');
 		$page = Input::get('page');
 		$per_page = Input::get('per_page');
 		$page = $page > 0 ? $page :1;
 		$per_page = $per_page > 0 ? $per_page : 30;
 		$count = EconomyLog::on($this->db_name)
 			->findEconomys2($player_name, $start_time, $end_time)
 			->count();

 		$players = EconomyLog::on($this->db_name)
 			->findEconomys2($player_name, $start_time, $end_time)
 			->forPage($page, $per_page)
 			->get(); 

 		$result = array(
			'count' => $count,
			'total' => ceil($count / $per_page),
			'per_page' => $per_page,
			'current_page' => $page,
			'items' => $players->toArray(),
		);
		
		return Response::json($result);

 	}
 	
	public function getPlayerName()
	{
		$player_id = ( int ) Input::get('player_id');
		$db = DB::connection($this->db_name);
		if ($player_id) {
			$sql="select player_name from log_create_player where player_id={$player_id} limit 1";
		}
		$log=$db->select($sql);
		//Log::info("tong ji::get player economy log=========================>".$log);
		return Response::json($log);
	}
	public function getYysgPlayerEconomyStatistics()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$player_id = ( int ) Input::get('player_id');
		
		$type = Input::get('type');
		$diff_type = 'diff_' . $type;
		$start_time = ( int ) Input::get('start_time');
		$end_time = ( int ) Input::get('end_time');
		$look_type = (int) Input::get('look_type');
		$action_type_num = Input::get('action_type_num');
		$game_id = Input::get('game_id');

		$db = DB::connection($this->db_name);
		$symbol = $look_type == 1 ? '>' : ($look_type == 2 ? '<' : '<>');
		$table = 'log_economy';
		if(array_key_exists($type, $this->mnsg_new_types) && in_array($game_id, Config::get('game_config.mnsggameids'))){
			$table = 'log_economy_new';
			$type = $this->mnsg_new_types[$type];
			$diff_type = 'diff_' . $type;
		}
		$log = $db->table($table)
				  ->selectRaw("player_id,mid,sum($diff_type) as diff,max(created_at) as created_at,count(1) as times")
				  ->where($diff_type, $symbol, 0)
				  ->whereBetween('created_at', array($start_time, $end_time));
		if($action_type_num){
			$log = $log->where('mid', $action_type_num);
		}else{
			$log = $log->groupBy('mid');
		}

		if($player_id){
			$log = $log->where('player_id', $player_id);
		}else{
			$log = $log->groupBy('player_id');
		}

		$possible_keys = array('created_at', 'create_at', 'player_id');	//这几个索引顺序就是效率优先的顺序
		$index_name = $this->getIndex($this->db_name, $table, $possible_keys);

		if($index_name){
			$log = $log->index($index_name);
		}

		$log = $log->get();

		if(count($log) == 0)
		{
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else
		{
			return Response::json($log);
		}
	}
	public function getYysgPlayerEconomy()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_economy'),
				'error' => ''
		);
		$player_id = ( int ) Input::get('player_id');
		
		$type = Input::get('type');
		$key_name = $type;	//这个值是因为后续可能因为新表修改type的值，但是返回的时候不需要master知道我们这边做了特殊的处理
		$diff_type = 'diff_' . $type;
		$start_time = ( int ) Input::get('start_time');
		$end_time = ( int ) Input::get('end_time');
		$look_type = (int) Input::get('look_type');
		$action_type_num = Input::get('action_type_num');
		$game_id = Input::get('game_id');

		$db = DB::connection($this->db_name);
		$symbol = $look_type == 1 ? '>' : ($look_type == 2 ? '<' : '<>');
		$table = 'log_economy';
		if(array_key_exists($type, $this->mnsg_new_types) && in_array($game_id, Config::get('game_config.mnsggameids'))){
			$table = 'log_economy_new';
			$type = $this->mnsg_new_types[$type];
			$diff_type = 'diff_' . $type;
		}

		$possible_keys = array('created_at', 'create_at', 'player_id');	//这几个索引顺序就是效率优先的顺序
		$index_name = $this->getIndex($this->db_name, $table, $possible_keys);

		$log = $db->table($table)
				  ->selectRaw("player_id,mid,$type as $key_name,$diff_type as diff,created_at")
				  ->where('player_id', $player_id)
				  ->where($diff_type, $symbol, 0)
				  ->whereBetween('created_at', array($start_time, $end_time))
				  ->orderBy('created_at');
		if($index_name){
			$log = $log->index($index_name);
		}
		if($action_type_num){
			$log = $log->where('mid', $action_type_num)->get();
		}else{
			$log = $log->get();
		}

		if(count($log) == 0)
		{
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else
		{
			return Response::json($log);
		}
	}

	public function getUserLonelyExp()
    {
        $msg = array(
                        'code' => Config::get('errorcode.slave_server_economy'),
                        'error' => ''
        );
        $platform_id = Input::get('platform_id');
        $server_internal_id = Input::get('server_internal_id');
        $game_id = Input::get('game_id');
        $player_id = Input::get('player_id');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $db = DB::connection($this->db_name);
  
        $exp = $db->select("select exp, FROM_UNIXTIME(action_time) as date ,action_time, action_type  from log_lonely where server_id = {$server_internal_id} and player_id = {$player_id} and action_time > {$start_time} and action_time <= {$end_time} order by action_time");
        if ($exp) {
                return Response::json($exp);
        } else {
                $msg['error'] = Lang::get('error.slave_result_none');
                return Response::json($msg, 404);
        }
    }

    public function getAbnormalDada(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => ''
        );
        $server_internal_id = Input::get('server_internal_id');
        $game_id = Input::get('game_id');
        $type = Input::get('type');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $min_limit = Input::get('min_limit');
        $field = '';
		$field_2 = '';
        if($type == 'tili'){
        	$field = 'diff_tili';
        	$field_2 = 'diff_extra_tili'; 
        }else{
        	$field = 'diff_' . $type;
        }
        if(in_array($type, $this->types)){//三个条件是因为要在三个表中查询
        	if($field_2){
        		$sql = "select player_id,action_type,min(action_time) as first_time,max(action_time) as last_time,(sum($field)+sum($field_2)) as spend from log_economy 
        		where action_time between {$start_time} and {$end_time} and ({$field}>0 or {$field_2}>0) 
        		group by player_id,action_type having sum($field)+sum($field_2)>={$min_limit} order by player_id,spend";
        	}else{
        		$sql = "select player_id,action_type,min(action_time) as first_time,max(action_time) as last_time,sum($field) as spend from log_economy 
        		where action_time between {$start_time} and {$end_time} and {$field}>0 
        		group by player_id,action_type having sum($field)>={$min_limit} order by player_id,spend";
        	}
        	
        }elseif('rings_exp' == $type){
        	$sql = "select player_id,action_type,min(action_time) as first_time,max(action_time) as last_time,sum($field) as spend from log_rings 
        	where action_time between {$start_time} and {$end_time} and {$field}>0 
        	group by player_id,action_type having sum($field)>={$min_limit} order by player_id,spend";
        }else{
        	$sql = "select player_id,action_type,min(action_time) as first_time,max(action_time) as last_time,sum($field) as spend from log_economy_extra 
        	where action_time between {$start_time} and {$end_time} and {$field}>0 
        	group by player_id,action_type having sum($field)>={$min_limit} order by player_id,spend";
        }        
        $db = DB::connection($this->db_name);
        $result = $db->select($sql);
        if ($result) {
                return Response::json($result);
        } else {
                $msg['error'] = Lang::get('error.slave_result_none');
                return Response::json($msg, 404);
        }
    }

    public function getPokerdailydata(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => ''
        );

    	$game_id = Input::get('game_id');
    	$start_time = Input::get('start_time');
        $end_time = Input::get('end_time');

        $db = DB::connection($this->db_name);
        $result = array();

        $sql = "select * from log_dataofday where game_id={$game_id} and created_time between {$start_time} and {$end_time} order by diff_chip desc";
        $result = $db->select($sql);
        unset($db);
        if(count($result) > 0){
        	 return Response::json($result);
        }else{
			$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
        }
    }

    public function getSpendonParts(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => ''
        );
        $game_id = Input::get('game_id');
        $type = Input::get('type');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time'); 
        $mid = (int)Input::get('mid');
        $symbol = Input::get('symbol');
        $result = array();
        if($mid){	//目前只有夜夜三国会查，框架中无法使用强制索引，因此单独写sql
        	$type2type = array(
				'tongqian' => 'mana',
				'yuanbao' => 'crystal'
			);
			if(in_array($game_id, Config::get('game_config.mobilegames'))){
				$key_time = 'created_at';
				$key_action = 'mid';
				$type = $type2type[$type];
			}else{
				$key_time = 'action_time';
				$key_action = 'action_type';
			}
			$diff_type = 'diff_'.$type;
			if($type){
				$type_subsql = " and {$diff_type} {$symbol} 0 ";
			}else{
				$type_subsql = "";
			}
			$sql = "select count(distinct player_id) as player_num, count(1) as times, sum($diff_type) as sumvalue, $key_action as actionvalue, $diff_type as singlepirce from log_economy use index(created_at) where 
			`{$key_time}` between {$start_time} and {$end_time} and `{$key_action}` = {$mid}".$type_subsql." group by {$diff_type}";

        	$result = DB::connection($this->db_name)->select($sql);
        }else{
	        $result = EconomyLog::on($this->db_name)->getSpendonParts($type, $start_time, $end_time, $game_id, $symbol)
	        					->get();
        }

        if (count($result) > 0) {
        	return Response::json($result);
        }else{
        	$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
        }
    }

    public function getEconomyEachPlayer(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => ''
        );
        $game_id = Input::get('game_id');
        $type = Input::get('type');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time'); 
        $mid = (int)Input::get('mid');
        $symbol = Input::get('symbol');
        $limit_symbol = Input::get('limit_symbol');
        $limit_value = (int)Input::get('limit_value');
        $result = EconomyLog::on($this->db_name)->getEconomyEachPlayer($type, $start_time, $end_time, $game_id, $symbol, $limit_symbol, $limit_value)
	        					->get();

	    if(in_array($game_id, Config::get('game_config.mobilegames'))){	//手游循环每个结果取玩家名
	    	foreach ($result as $key => $value) {
	    		$player_name = PlayerNameLog::on($this->db_name)->where('player_id', $value->player_id)->orderBy('id', 'desc')->first();
	    		$result[$key]->player_name = isset($player_name->player_name) ? $player_name->player_name : '';
	    		unset($player_name);
	    		unset($value);
	    	}
	    }

	    if (count($result)) {
        	return Response::json($result);
        }else{
        	$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
        }
    }

    public function getEconomyWholeServer(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => ''
        );
        $game_id = Input::get('game_id');
        $type = Input::get('type');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time'); 
        $mid = (int)Input::get('mid');
        $symbol = Input::get('symbol');
        $result = EconomyLog::on($this->db_name)->getEconomyWholeServer($type, $start_time, $end_time, $game_id, $symbol)
	        					->first();

	    if ($result) {
        	return Response::json($result);
        }else{
        	$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
        }
    }

    public function getRemainYuanbao(){
     	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => ''
        );
        $by_reg_time = Input::get('by_reg_time');
		$game_id = Input::get('game_id');
		$type = Input::get('type');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$min_level = Input::get('min_level');
		$max_level = Input::get('max_level');
		$created_start_time = Input::get('created_start_time');
		$created_end_time = Input::get('created_end_time');
		$upgrade_time = Input::get('upgrade_time');
        $upgrade_start_time = Input::get('upgrade_start_time');
        $upgrade_end_time = Input::get('upgrade_end_time');
		$result = array();
		$db = DB::connection($this->db_name);
		$diff_value = array(0,100,300,500,1000,5000,10000);
		if($min_level && $max_level){
			$term = "having max_lev between $min_level and $max_level";
		}
		elseif ($min_level && !$max_level) {
			$term = "having max_lev >= $min_level ";
		}
		elseif (!$min_level && $max_level) {
			$term = "having max_lev <= $max_level ";
		}
		elseif (!$min_level && !$max_level) {
			$term = "";
		}
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$key = '';
			switch ($type) {
				case 'yuanbao':
					$key = 'crystal';
					break;
				case 'tongqian':
					$key = 'mana';
					break;
				case 'tili':
					$key = 'energy';
					break;
				default:
					$key = 'crystal';
					break;
			}
			for($i=0;$i<count($diff_value);$i++){
				if(isset($diff_value[$i+1])){
					$sql = "select sum(b.$key) as remainyuanbao,count(1) as player_num from 
								(select * from 
									(select player_id,$key from log_economy where created_at between $start_time and $end_time order by id desc) a
								group by a.player_id) b ";
					if($max_level || $min_level){
						if ($upgrade_time) {
							$sql .= "join (select player_id, max(lev) as max_lev from log_levelup
										  where levelup_time between $upgrade_start_time and $upgrade_end_time
										  group by player_id ".$term.") c on c.player_id = b.player_id ";
						}else{
							$sql = $sql."join (select player_id, max(lev) as max_lev from log_levelup group by player_id ".$term.") c on c.player_id = b.player_id ";
						}
					}

					if($by_reg_time){
						$sql= $sql."join log_create_player lcp on lcp.created_time between $created_start_time and $created_end_time and lcp.player_id = b.player_id
								where $key between $diff_value[$i] and {$diff_value[$i+1]}";	
					}else{
						$sql= $sql." where $key between $diff_value[$i] and {$diff_value[$i+1]}";	
					}			
				}else{
					$sql = "select sum(b.$key) as remainyuanbao,count(1) as player_num from 
								(select * from 
									(select player_id,$key from log_economy where created_at between $start_time and $end_time order by id desc) a 
								group by a.player_id) b ";
					if($max_level || $min_level){
						if ($upgrade_time) {
							$sql .= "join (select player_id, max(lev) as max_lev from log_levelup 
									where levelup_time between $upgrade_start_time and $upgrade_end_time
									group by player_id ".$term.") c on c.player_id = b.player_id ";
						}else{
							$sql = $sql."join (select player_id, max(lev) as max_lev from log_levelup group by player_id ".$term.") c on c.player_id = b.player_id ";
						}
					}
					if($by_reg_time){
						$sql = $sql."join log_create_player lcp on lcp.created_time between $created_start_time and $created_end_time and lcp.player_id = b.player_id
								where $key > $diff_value[$i]";
					}else{
						$sql = $sql." where $key > $diff_value[$i]";
					}
				} 
				$tmp_result = $db->select($sql);
				$result[] = array(
					'desc_name'	=>	$diff_value[$i],
					'remainyuanbao'	=>	$tmp_result[0]->remainyuanbao == null ? 0 : $tmp_result[0]->remainyuanbao,
					'player_num'	=>	$tmp_result[0]->player_num,
					'avg_yuanbao'	=>	$tmp_result[0]->remainyuanbao == 0 ? 0 : round($tmp_result[0]->remainyuanbao/$tmp_result[0]->player_num, 1),
					);
				unset($tmp_result);
			}
		}else{
			for($i=0;$i<count($diff_value);$i++){
				if(isset($diff_value[$i+1])){
					$sql = "select sum(b.$type) as remainyuanbao,count(1) as player_num from 
								(select * from 
									(select player_id,$type from log_economy where action_time between $start_time and $end_time order by log_id desc) a 
							group by a.player_id) b ";
					if($max_level || $min_level){
						if ($upgrade_time) {
							$sql .= " join (select player_id, max(new_level) as max_lev from log_levelup 
									where levelup_time between $upgrade_start_time and $upgrade_end_time
									group by player_id ".$term.") c on c.player_id = b.player_id ";
						}else{
							$sql = $sql." join (select player_id, max(new_level) as max_lev from log_levelup group by player_id ".$term.") c on c.player_id = b.player_id ";
						}
						
					}
					if($by_reg_time){
						$sql =  $sql." join log_create_player lcp on lcp.created_time between $created_start_time and $created_end_time and lcp.player_id = b.player_id
								where $type between $diff_value[$i] and {$diff_value[$i+1]}";
					}else{
						$sql =  $sql." where $type between $diff_value[$i] and {$diff_value[$i+1]}";
					}
				}else{
					$sql = "select sum(b.$type) as remainyuanbao,count(1) as player_num from 
								(select * from 
									(select player_id,$type from log_economy where action_time between $start_time and $end_time order by log_id desc) a 
							group by a.player_id) b ";
					if($max_level || $min_level){
						if ($upgrade_time) {
							$sql .= " join (select player_id, max(new_level) as max_lev from log_levelup 
									where levelup_time between $upgrade_start_time and $upgrade_end_time
									group by player_id ".$term.") c on c.player_id = b.player_id ";
						}else{
							$sql = $sql." join (select player_id, max(new_level) as max_lev from log_levelup group by player_id ".$term.") c on c.player_id = b.player_id ";
						}						
					}
					if($by_reg_time){
						$sql= $sql." join log_create_player lcp on lcp.created_time between $created_start_time and $created_end_time and lcp.player_id = b.player_id
								where $type > $diff_value[$i]";
					}else{
						$sql= $sql." where $type > $diff_value[$i]";
					}
				}
				$tmp_result = $db->select($sql);
				$result[] = array(
					'desc_name'	=>	$diff_value[$i],
					'remainyuanbao'	=>	$tmp_result[0]->remainyuanbao == null ? 0 : $tmp_result[0]->remainyuanbao,
					'player_num'	=>	$tmp_result[0]->player_num,
					'avg_yuanbao'	=>	$tmp_result[0]->remainyuanbao == 0 ? 0 : round($tmp_result[0]->remainyuanbao/$tmp_result[0]->player_num, 1),
					);
				unset($tmp_result);
			}
		}

        if (count($result) > 0) {
        	return Response::json($result);
        }else{
        	$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
        }
    }

    public function PokerWriteGiftbag(){
	    $operator = Input::get('operator');
        $created_time = Input::get('created_time');
        $player_array = Input::get('player_array');
        $items_string = Input::get('items_string');

        $player_ids = implode(',', $player_array);

       	$db = DB::connection($this->db_name);

       	$data = array(
       		'operator'	=>	$operator,
       		'player_ids'	=>	$player_ids,
       		'items_string'	=>	$items_string,
       		'created_time'	=>	$created_time,
       		'send_time'	=>	0,
       		'is_send'	=>	0,
       		);
       	try {
       		$result = $db->table('giftbag_message')->insert($data);
       		return Response::json(array('result' => 'OK'), 200);
       	} catch (Exception $e) {
       		return Response::json(array('result' => 'ERROR'), 403);
       	}
    }

    public function PokerGetGiftbag(){
    	$giftbag_id = (int)Input::get('giftbag_id');
    	$db = DB::connection($this->db_name);
    	$result = array();

    	$check_start_time = (int)Input::get('check_start_time');
    	$check_end_time = (int)Input::get('check_end_time');
    	$creater = Input::get('creater');
    	if($check_start_time){
    		$result = $db->table('giftbag_message')->where('is_send', '>', 0)->whereBetween('created_time', array($check_start_time, $check_end_time));
    		if($creater){
    			$result->where('operator', $creater);
    		}
    		$result = $result->selectRaw("operator, items_string, player_ids, FROM_UNIXTIME(created_time) as created_time, FROM_UNIXTIME(send_time) as send_time, if(is_send=1,'发放',if(is_send=9, '否决', '不明状态')) as is_send")->orderBy('created_time', 'desc')->get();
    	}else{
	    	try {
	    		$result = $db->table('giftbag_message')->where('is_send', 0)->orderBy('created_time', 'desc');
	    		if($giftbag_id){
	    			$result->where('id', $giftbag_id);
	    		}
	    		$result = $result->get();
	    	} catch (Exception $e) {
	    	}
    	}
    	if(count($result) > 0){
    		return Response::json($result, 200);
    	}else{
			$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
    	}
    }

    public function PokerChangeGiftbagStatu(){
    	$giftbag_id = (int)Input::get('giftbag_id');
    	$statu = (int)Input::get('statu');
    	$db = DB::connection($this->db_name);

    	try {
    		$result = $db->table('giftbag_message')->where('id', $giftbag_id)->update(array('is_send' => $statu, 'send_time' => time()));
    		return Response::json(array('result' => 'OK'), 200);
    	} catch (Exception $e) {
    		return Response::json(array('result' => 'ERROR'), 403);
    	}
    }

    public function getActivityAnalysis(){
    	$game_id = (int)Input::get('game_id');
    	$mids = Input::get('mids');
    	$start_time = Input::get('start_time');
    	$end_time = Input::get('end_time');
    	$server_internal_id = (int)Input::get('server_internal_id');
    	$type = Input::get('type');
    	
    	$result = array();
	    $result = EconomyLog::on($this->db_name)
				->getActivityAnalysis($game_id, $start_time, $end_time, $mids, $type)
				->get();

    	if(count($result)){
    		return Response::json($result);
    	}else{
    		$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
    	}
    }

    public function getExpenseSum(){
        $msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => ''
        );

		$game_id = Input::get('game_id');
		$server_internal_id = (int)Input::get('server_internal_id');
		$start_time_date = Input::get('start_time');
		$end_time_date = Input::get('end_time');
		$start_time = strtotime(trim(Input::get('start_time')));
		$end_time = strtotime(trim(Input::get('end_time')));
		$interval = (int)Input::get('interval');
		$seconds = $interval * 86400;

		$db_payment = DB::connection($this->db_payment);
		$db_name = DB::connection($this->db_name);

		$result = array();
		

		$sql="select sl.server_name,(floor((pay_time - $start_time)/$seconds)*$seconds+$start_time) as date,count(distinct o.pay_user_id) as player_num, sum(pay_amount*exchange) as sum_dollar from 
				pay_order o join server_list sl on o.server_id = sl.server_id 
				where get_payment = 1 and o.game_id = $game_id and sl.server_internal_id = $server_internal_id and pay_time between $start_time and $end_time
				group by date";

		$sql2 = "select (floor((online_time - $start_time)/$seconds)*$seconds+$start_time) as date,avg(online_value) as avg_online_value
				from log_online where online_time between $start_time and $end_time group by date";

		$sql3 = "select (floor((created_time - $start_time)/$seconds)*$seconds+$start_time) as date,count(1) as create_num
				from log_create_player where created_time between $start_time and $end_time group by date";

		$tmp_result = $db_payment->select($sql);
		if(in_array($game_id, Config::get('game_config.yysggameids'))){
			$tmp_result2 = array();
		}else{
			$tmp_result2 = $db_name->select($sql2);
		}
		$tmp_result3 = $db_name->select($sql3);

		foreach ($tmp_result as $key1 => $value1) {
			$result[$key1] = array(
				'server_name' => $value1->server_name,
				'date'	=>	date("Y-m-d",$value1->date),
				'player_num'	=>	$value1->player_num,
				'sum_dollar'	=>	$value1->sum_dollar,
				'avg_online_value'	=>	0,
				'create_num'	=>	0,
				);
			foreach ($tmp_result2 as $key2 => $value2) {
				if($value1->date == $value2->date){
					$result[$key1]['avg_online_value'] = $value2->avg_online_value;
					unset($tmp_result2[$key2]);
					break;
				}
			}
			foreach ($tmp_result3 as $key3 => $value3) {
				if($value1->date == $value3->date){
					$result[$key1]['create_num'] = $value3->create_num;
					unset($tmp_result3[$key3]);
					break;
				}
			}
		}
        if (count($result) > 0) {
        	return Response::json($result);
        }else{
        	$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
        }

    }

    public function CountCreatePartnerLog(){
    	$game_id = Input::get('game_id');
    	$start_time = (int)Input::get('start_time');
    	$end_time = (int)Input::get('end_time');
    	$wjids = Input::get('wjids');

    	$result = DB::connection($this->db_name)->table('log_create_partner')
    	              ->whereBetween('created_at', array($start_time, $end_time))
    	              ->groupBy('table_id')
    	              ->selectRaw("table_id, count(1) as times");

    	if($wjids){
    		$result = $result->whereIn('table_id', $wjids);
    	}

    	$result = $result->get();

    	if(count($result)){
    		return Response::json($result);
    	}else{
    		$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
    	}
    }


    public function getActivityData(){
    	$game_id = (int)Input::get('game_id');
    	$player_id = (int)Input::get('player_id');
    	$device = Input::get('device');
    	$activity_id = Input::get('activity_id');
    	$reward_id = Input::get('reward_id');
    	$start_time = Input::get('start_time');
    	$end_time = Input::get('end_time');

    	$db = DB::connection($this->db_name);
    	$sql = DB::connection($this->db_name)->table(DB::raw('log_activity_data as lad'));

    	$sql->whereBetween('lad.time', array($start_time, $end_time));
    	if($player_id){
    		$sql->where('lad.player_id', '=', $player_id);	
    		if($device){
    		$sql->where('lad.device', '=', $device);	
	    	}
	    	if($activity_id){
	    		$sql->where('lad.activity_id', '=', $activity_id);	
	    	}
	    	if($reward_id){
	    		$sql->where('lad.reward_id', '=', $reward_id);	
	    	}
	    	$tmp=$sql->selectRaw("lad.player_id as player_id, lad.device as device, lad.activity_id as activity_id, lad.reward_id as reward_id, lad.time as time")->get();
    	}
    	else{
    		if($device){
    		$sql->where('lad.device', '=', $device);	
	    	}
	    	else{
	    		$sql->groupBy('lad.device');
	    	}
	    	if($activity_id){
	    		$sql->where('lad.activity_id', '=', $activity_id);	
	    	}
	    	else{
	    		$sql->groupBy('lad.activity_id');
	    	}
	    	if($reward_id){
	    		$sql->where('lad.reward_id', '=', $reward_id);	
	    	}
	    	else{
	    		$sql->groupBy('lad.reward_id');
	    	}
	    	$tmp=$sql->selectRaw("lad.device as device, lad.activity_id as activity_id, lad.reward_id as reward_id, count(1) as times, count(distinct player_id) as player_num")->get();
	    }
    	$result = $tmp;
    	if($player_id){
	    	foreach ($result as $key => &$value) {	
	    		$value->time = date('Y-m-d H:i:s',$value->time);
	    	}
	    }
		if($result){
			return Response::json($result);
		}else{
			return Response::json(array(), 404);
		}
    }

    public function PokerBankruptcy(){	//德扑破产相关
       $game_id = Input::get('game_id');
	   $start_time =  Input::get('start_time');
       $end_time =  Input::get('end_time');
       $by_create_time =  Input::get('by_create_time');
       $create_start_time =  Input::get('create_start_time');
       $create_end_time =  Input::get('create_end_time');

       $result = array();

       //获取符合条件的用户破产人数和次数
       $tmp_result_count_data = EconomyLog::on($this->db_name)->getPokerBankruptcy($by_create_time, $start_time, $end_time, $create_start_time, $create_end_time)->first();
       //救济人数，每人一次
       $BustReward = EconomyLog::on($this->db_name)->getPokerEconomy($by_create_time, $start_time, $end_time, $create_start_time, $create_end_time, $action_type='getBustReward')->count();
       //获取符合条件的用户当日的玩牌人数
       $PlayedPlayer = EconomyLog::on($this->db_name)->getPokerEconomy($by_create_time, $start_time, $end_time, $create_start_time, $create_end_time, $action_type='endOneRound')
                                  ->selectRaw('count(distinct e.player_id) as playedplayer')->first();

       $sql = "select sum(o.pay_amount*exchange) as bankruptcy_dollar from 
       (select player_id,min(action_time) as first_time from `11.1`.log_economy le 
       	where le.action_type != 'saveChipsToStrongBox|saveChipsToStrongBox' and le.diff_tongqian<0 and le.tongqian < 200 
       	and le.tongqian-le.diff_tongqian >= 200 and le.action_time between {$start_time} and {$end_time} 
       	group by le.player_id) a  
		join `11.1`.log_create_player lcp on a.player_id = lcp.player_id ".($by_create_time? " and lcp.created_time between {$create_start_time} and {$create_end_time} " : "")."
		join {$this->db_payment}.pay_order o on lcp.user_id = o.pay_user_id and o.get_payment = 1 and o.game_id = {$game_id} and o.create_time between a.first_time and {$end_time}";
       $Bankruptcy_dollar = DB::connection($this->db_name)->select($sql);
       $result['count'] = array();
       if(isset($tmp_result_count_data->bankruptcy_user_num)){
       		$result['count'] = array(
       			'bankruptcy_user_num' => $tmp_result_count_data->bankruptcy_user_num,
       			'bankruptcy_times' => $tmp_result_count_data->bankruptcy_times,
       			'bustreward' => $BustReward,
       			'playedplayer' => isset($PlayedPlayer->playedplayer) ? $PlayedPlayer->playedplayer : '-',
       			'bankruptcy_dollar' => isset($Bankruptcy_dollar[0]->bankruptcy_dollar) ? $Bankruptcy_dollar[0]->bankruptcy_dollar : '-',
       			);
       }
       unset($tmp_result_count_data);
       unset($BustReward);
       unset($PlayedPlayer);
       unset($Bankruptcy_dollar);
       $tmp_result_level_data = EconomyLog::on($this->db_name)->getPokerBankruptcyLevel($by_create_time, $start_time, $end_time, $create_start_time, $create_end_time)->get();
       $result['level'] = array();
       for($i=1;$i<=50;$i++){	//初始化一下，方便画图的时候有完整的横坐标
       		$result['level'][($i+10000)] = array(
       			'lev' => $i,
       			'num' => 0,
       			);
       }
       if(count($tmp_result_level_data)){
	       foreach ($tmp_result_level_data as $key => $value) {	//等级加了10000为了在ksort时位数相同
	       		if(isset($value->level)){
	       			if(isset($result['level'][(10000+$value->level)])){
	       				$result['level'][(10000+$value->level)]['num']++;
	       			}else{
	       				$result['level'][10050]['num']++;
	       			}
	       		}
	       		unset($tmp_result_level_data[$key]);
	       }
	       ksort($result['level']);
       }
       unset($tmp_result_level_data);
       return Response::json($result);
    }

    public function geyPlayerYuanbaoIncrease(){	//获取单个玩家的一段时间内的元宝增量
    	$player_id = Input::get('player_id');
    	$start_time = (int)Input::get('start_time');
    	$end_time = (int)Input::get('end_time');
    	$game_id = Input::get('game_id');

    	try{
    		$test = EconomyLog::on($this->db_name)->first();
    		if($test){
    			$has_data = 1;
    		}else{
    			$has_data = 0;
    		}
    	}catch(Exception $e){
    		$has_data = 0;
    	}

    	if($has_data){
    		if(in_array($game_id, Config::get('game_config.mobilegames'))){
    			$player_name = PlayerNameLog::on($this->db_name)->where('player_id', $player_id)->orderBy('id', 'desc')->first();
    		}else{
    			$player_name = CreatePlayerLog::on($this->db_name)->where('player_id', $player_id)->first();
    		}
    		$player_yuanbao = EconomyLog::on($this->db_name)->GetPlayerYuanbaoIncrease($start_time, $end_time, $game_id, $player_id)->first();
    		if(isset($player_yuanbao->yuanbao_increase)){
    			$result = array(
    				'yuanbao_increase' => $player_yuanbao->yuanbao_increase,
    				);
    		}else{
    			$result = array(
    				'yuanbao_increase' => 0,
    				);
    		}
    		if(isset($player_name->player_name)){
    			$result['player_name'] = $player_name->player_name;
    		}else{
    			$result['player_name'] = '';
    		}
    		return Response::json($result);
    	}else{
    		return Response::json(array(), 404);
    	}
    }
}