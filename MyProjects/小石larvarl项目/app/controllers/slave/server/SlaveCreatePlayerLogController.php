<?php 

class SlaveCreatePlayerLogController extends \SlaveServerBaseController {

	public function __construct(CreatePlayerLog $model)
	{
		parent::__construct();
		$this->primary_key = $model->getKeyName();
	}

	/*public function getLevelRank()
	{
		$page = (int)Input::get('page');
		$per_page = (int)Input::get('per_page');
		$page = $page > 0 ? $page : 1;
		$per_page = $per_page > 0 ? $per_page : 30; 
		$per_page = min($per_page, 30);

		$count = CreatePlayerLog::on($this->db_name)->count();

		$total = ceil($count / $per_page);

		/*$log = CreatePlayerLog::on($this->db_name)
			->levelRank()
			->forPage($page, $per_page)
			->get();
		$log = SlaveCreatePlayer::on($this->db_qiqiwu)->levelRank($this->db_name)->forPage($page, $per_page)->get();
		$db = DB::connection($this->db_qiqiwu);
		$offset = $page == 1 ? 0 : ($page-1)*$per_page;
		$sql = "SELECT IFNULL( MAX( le.new_level ) , 1 ) AS level, IFNULL( MAX( le.levelup_time ) , created_time ) AS levelup_time, player_name, p.player_id FROM `create_player` AS `p`
				LEFT JOIN {$this->db_name}.`log_levelup` AS `le` ON `le`.`player_id` = `p`.`player_id`
				GROUP BY `p`.`player_id`
				ORDER BY `level` DESC , `le`.`levelup_time` ASC
				limit $offset, $per_page ";
		$log = $db->select($sql);
		$items = $log->toArray();
		foreach ($items as $k => $v) {
			$items[$k]['rank'] = ($page - 1) * $per_page + ($k+1);
		}
		$result = array(
			'count' => $count,
			'total' => $total,
			'per_page' => $per_page,
			'current_page' => $page,
			'items' => $items,
		);
		return Response::json($result);
	}*/

	public function getLevelRank()
    {
    	$game_id = (int)Input::get('game_id');
    	$is_created_time = Input::get('is_created_time');
    	$start_time = (int)Input::get('start_time');
    	$end_time = (int)Input::get('end_time'); 
    	$levelup_time = (int)Input::get('levelup_time');
    	$level_lower_bound = (int)Input::get('level_lower_bound');
    	$level_upper_bound = (int)Input::get('level_upper_bound');
        $page = (int)Input::get('page');
        $per_page = (int)Input::get('per_page');
        $page = $page > 0 ? $page : 1;
        $per_page = $per_page > 0 ? $per_page : 30;
        $per_page = min($per_page, 30);

        $level_limit_sql = '';
        if($level_lower_bound && $level_upper_bound){
        	$level_limit_sql = " having level between {$level_lower_bound} and {$level_upper_bound} ";
        }elseif($level_lower_bound){
        	$level_limit_sql = " having level >= {$level_lower_bound} ";
        }elseif($level_upper_bound){
        	$level_limit_sql = " having level <= {$level_upper_bound} ";
        }

        if(in_array($game_id, Config::get('game_config.mobilegames'))){ //手游日志库字段有区别
        	$db = DB::connection($this->db_name);
        	$str1 = "SELECT IFNULL( MAX( le.lev ) , 1 ) AS level, IFNULL( MAX( le.created_at ) , created_time ) AS levelup_time, p.player_id, p.created_time, p.created_ip FROM `log_create_player` AS `p`
	                        LEFT JOIN `log_levelup` AS `le` ON `le`.`player_id` = `p`.`player_id` and `le`.`created_at`<={$levelup_time} ";
	        if('1' == $is_created_time){
	        	$str2 = " where `p`.`created_time` between {$start_time} and {$end_time} GROUP BY `p`.`player_id` {$level_limit_sql}
	                        ORDER BY `level` DESC , `le`.`created_at` ASC";
	        }else{
	        	$str2 = " GROUP BY `p`.`player_id` {$level_limit_sql}
	                        ORDER BY `level` DESC , `le`.`created_at` ASC";
	        }
	        $no_page = $str1.$str2;//不分页

        	if(99999999 == $page){//下载的时候不提供玩家的名字，太占用效率
        		$count = $db->select($no_page);
        		$items = $count;
            	$items[0]->rank = 1;
            	foreach ($items as $k => $v) {
        			if(0 == $k){
        				continue;
        			}
        	        $items[$k]->rank  = $items[$k-1]->rank + 1;
            	}
            	$result = array(
            	        'items' => $items,
            	);
            	return Response::json($result);
        	}else{
        		$count = CreatePlayerLog::on($this->db_name)->selectRaw("count(1) as player_num")->first();
	        	$count = $count->player_num;
	        	$total = ceil($count / $per_page);
		        $offset = $page == 1 ? 0 : ($page-1)*$per_page;
				$str3 = " limit $offset, $per_page";
        	}
        }else{
        	$db = DB::connection($this->db_qiqiwu);
			$str1 = "SELECT cp.player_name, ll.player_id, ll.level,IFNULL(ll.last_levelup_time, created_time) as levelup_time, cp.created_time, cp.remote_host_ip as created_ip
			        		from (SELECT player_id,max(new_level) as level,max(levelup_time) AS last_levelup_time from `{$this->db_name}`.`log_levelup` where levelup_time <={$levelup_time} group by player_id {$level_limit_sql} ORDER by level DESC) as ll
			        		left join `create_player` as cp on `ll`.`player_id` = `cp`.`player_id`";
			if('1' == $is_created_time){
				$str2 = " where cp.created_time between {$start_time} and {$end_time}";
			}
			$no_page = isset($str2) ? $str1.$str2 : $str1;//不分页

			if(99999999 == $page){//下载的时候不提供玩家的名字，太占用效率
				$count = $db->select($no_page);
				$items = $count;
		    	$items[0]->rank = 1;
		    	foreach ($items as $k => $v) {
		    			if(0 == $k){
		    				continue;
		    			}
		    	        $items[$k]->rank  = $items[$k-1]->rank + 1;
		    	}
		    	$result = array(
		    	        'items' => $items,
		    	);
		    	return Response::json($result);
			}else{
        		$count = CreatePlayerLog::on($this->db_name)->selectRaw("count(1) as player_num")->first();
	        	$count = $count->player_num;
	        	$total = ceil($count / $per_page);
		        $offset = $page == 1 ? 0 : ($page-1)*$per_page;
				$str3 = " limit $offset, $per_page";
        	}
        }
	    $sql = $no_page.$str3;//分页

        $log = $db->select($sql);

        $items = $log;
        foreach ($items as $k => $v) {		//手游的玩家名字可以更改，因此最新的玩家名要从数据库中获取
        	if(in_array($game_id, Config::get('game_config.mobilegames'))){
        		$player_name = PlayerNameLog::on($this->db_name)->where('player_id', $items[$k]->player_id)->orderBy('id', 'desc')->first();
        		$items[$k]->player_name = $player_name ? $player_name->player_name : '';
        		unset($player_name);
        	}
            $items[$k]->rank = '';
            $items[$k]->rank = ($page - 1) * $per_page + ($k+1);
        }
        $result = array(
                'count' => $count,
                'total' => $total,
                'per_page' => $per_page,
                'current_page' => $page,
                'items' => $items,
        );
        return Response::json($result);
    }


	public function getLevelTrend()
	{
		$is_anonymous = (int)Input::get('is_anonymous');
		$game_id = (int)Input::get('game_id');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');

		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$tmp_result = CreatePlayerLog::on($this->db_name);
					if(2 != $is_anonymous){
						$tmp_result->Join("{$this->db_qiqiwu}.users as u", function($join) use ($is_anonymous){
							$join->on('p.uid', '=', 'u.uid')
							  ->where('u.is_anonymous', '=', $is_anonymous);
						});
					}
					$tmp_result->leftJoin('log_levelup as ll', 'll.player_id', '=', 'p.player_id')
					->selectRaw("IFNULL(max(lev), 1) as level")
					->groupby('p.player_id');
			if($start_time && $end_time){
				$tmp_result = $tmp_result->whereBetween('p.created_time', array($start_time, $end_time))->get();
			}else{
				$tmp_result = $tmp_result->get();
			}
		}else{
			$tmp_result = CreatePlayerLog::on($this->db_name);
					if(2 != $is_anonymous){
						$tmp_result->Join("{$this->db_qiqiwu}.users as u", function($join) use ($is_anonymous){
							$join->on('p.user_id', '=', 'u.uid')
								->where('u.is_anonymous', '=', $is_anonymous);
						});
					}
					$tmp_result->leftJoin('log_levelup as ll', 'll.player_id', '=', 'p.player_id')
					->selectRaw("IFNULL(max(new_level), 1) as level")
					->groupby('p.player_id');
			if($start_time && $end_time){
				$tmp_result = $tmp_result->whereBetween('p.created_time', array($start_time, $end_time))->get();
			}else{
				$tmp_result = $tmp_result->get();
			}
		}

		$total = count($tmp_result);
		$result = array();
		
		if($total){
			foreach ($tmp_result as $value) {
				if (isset($value->level)) {
					if(isset($result[$value->level])){
						$result[$value->level]++;
					}else{
						$result[$value->level] = 1;
					}
				}
			}
			unset($tmp_result);
			foreach ($result as $key => $value) {
				$result[$key+10000] = (object)array(	//统一加10000这样key的位数相同，ksort可以正确排序
					'level' => $key,
					'count'	=> $value,
					'rate' => sprintf('%0.2f', $value / $total * 100),
					);
				unset($result[$key]);
			}
			ksort($result);
		}else{
			return Response::json(array('error'=>'没有查到数据!'), 403);
		}
		return Response::json($result);
	}

	public function getCreatedNumberByTime()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$range = array(
			600, 3600, 86400
		);	

		$start_time = (int)Input::get('start_time');	
		$end_time = (int)Input::get('end_time');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.slave_time_limit_rang');
			return Response::json($msg, 403);
		}

		$interval = (int)Input::get('interval');
		if (!in_array($interval, $range)) {
			$msg['error'] = Lang::get('error.slave_not_have_params');
			return Response::json($msg, 403);
		}
		
		if ($interval == $range[0]) {	
			$result = CreatePlayerLog::on($this->db_name)
				->createdNumByTenMinute($start_time, $end_time)
				->get();	
		} else if ($interval == $range[1]) {
			$result = CreatePlayerLog::on($this->db_name)
				->createdNumByHour($start_time, $end_time)
				->get();	
		} else if ($interval == $range[2]) {
			$result = CreatePlayerLog::on($this->db_name)
				->createdNumByDay($start_time, $end_time)
				->get();	
		}
		if (!$result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}
	
	public function getRetention()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);

		$is_anonymous = (int)Input::get('is_anonymous');

		$start_time = (int)Input::get('start_time');	
		$end_time = (int)Input::get('end_time');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.slave_time_limit_rang');
			return Response::json($msg, 403);
		}

		$result = RetentionLog::on($this->db_name)
			->retentionByTime($start_time, $end_time, $is_anonymous)
			->get();
		return Response::json($result);
	}

	public function getChannelRetention(){
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);

		$is_anonymous = (int)Input::get('is_anonymous');
		$channel_name = Input::get('channel_name');
		$start_time = (int)Input::get('start_time');	
		$end_time = (int)Input::get('end_time');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.slave_time_limit_rang');
			return Response::json($msg, 403);
		}
		$db = DB::connection($this->db_name);
		if($channel_name){
			$result = $db->table('log_channel_retention')
				->whereBetween('retention_time', array($start_time, $end_time))
				->where('is_anonymous', $is_anonymous)
				->where('channel', $channel_name)
			    ->orderBy('retention_time', 'ASC')
			    ->get();
		}else{
			$result = $db->table('log_channel_retention')
				->whereBetween('retention_time', array($start_time, $end_time))
				->where('is_anonymous', $is_anonymous)
			    ->orderBy('retention_time', 'ASC')
			    ->get();
		}
		return Response::json($result);		
	}

	public function getCreatePlayerInfo()
	{
		$msg = array(
				'code' => Config::get('errorcode.slave_player_created'),
				'error' => ''
		);
		$uid = trim(Input::get('uid'));
		$player_id = (int)Input::get('player_id');
		$player_name = Input::get('player_name');
		$game_id = (int)Input::get('game_id');
        //Log::info("get create player info===>uid:".$uid."--player_id".$player_id."--player_name:".$player_name);
		$result = CreatePlayerLog::on($this->db_name)->getCreatePlayerInfo($uid, $player_id, $player_name, $game_id)->first();

        // Log::info(var_export("yysg" . $this->db_name . $result, true));
		if (!$result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}

	public function getidbyname(){//使用玩家name得到玩家id
		$msg = array(
				'code' => Config::get('errorcode.slave_player_created'),
				'error' => ''
		);

		$player_name = Input::get('player_name');
		$game_id = (int)Input::get('game_id');
		$server_internal_id = (int)Input::get('server_internal_id');

		$db = DB::connection($this->db_name);
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$result = $db->select("select player_id,player_name from log_player_name where binary player_name = '".$player_name."' limit 1");
		}else{
			$result = $db->select("select player_id,player_name from log_create_player where binary player_name = '".$player_name."' limit 1");
		}
		
		if (!$result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}

	public function getnamebyid(){//使用玩家id得到玩家name
		$msg = array(
				'code' => Config::get('errorcode.slave_player_created'),
				'error' => ''
		);

		$player_id = (int)Input::get('player_id');
		$game_id = (int)Input::get('game_id');
		$server_internal_id = (int)Input::get('server_internal_id');

		$db = DB::connection($this->db_name);
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$result = $db->select("select player_id,player_name from log_player_name where player_id = $player_id order by id DESC limit 1");
		}else{
			$result = $db->select("select player_id,player_name from log_create_player where player_id = $player_id limit 1");
		}
        
		if (!$result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}

	 public function getUserInfoFromLog()
    {
        $uid = Input::get('uid');
        $game_id = (int)Input::get('game_id');
        $msg = array(
                'code' => Config::get('errorcode.slave_player_created'),
                'error' => ''
        );
        $db = DB::connection($this->db_name);
        if(in_array($game_id, Config::get('game_config.mobilegames'))){
        	$query = $db->select("select player_id,player_name from log_create_player where uid = {$uid} ORDER BY created_time DESC limit 1");
        }else{
        	$query = $db->select("select player_id,player_name from log_create_player where user_id = {$uid}");
        }
        if (!$query) {
                $msg['error'] = Lang::get('error.slave_result_none');
                return Response::json($msg, 404);
        } else {
                return Response::json($query);
        }
    }

    public function getScoreRankData(){
    	$start_time = (int)Input::get('start_time');
    	$end_time = (int)Input::get('end_time');
    	$type = Input::get('type');
    	if('baseRank' == $type){
    		$sql_str = " and log_type in ('baseRank_','baseRank_2','baseRank_3')";
    	}else{
    		$sql_str = " and log_type='{$type}'";
    	}
    	$db = DB::connection($this->db_name);
    	$result = $db->select("select created_time,json_data,log_type from log_ranks where created_time between {$start_time} and {$end_time}" . $sql_str);
   		if($result){
   			return Response::json($result);
   		}else{
   			return Response::json(array(),404);
   		}
    }

    public function CalculateRetention(){
    	$params = (array)Input::all();

    	//获取所有创建玩家

    	$total_create = CreatePlayerLog::on($this->db_name)->TotalCreate($params, $this->db_qiqiwu, $this->db_payment)->first()->total;


    	if($params['by_last_login_time']){	//要用到子查询，因此不用框架的数据查询方式
    		$interval_second = $params['interval'] * 86400;
			if(in_array($params['game_id'], Config::get('game_config.mobilegames'))){	//手游和页游的某些字段名字不同
				$uid_key = 'uid';
				$login_time_key = 'll.action_time';
			}else{
				$uid_key = 'user_id';
				$login_time_key = 'll.login_time';
			}
    		$date_result = array();
    		$sql = "select ll.player_id,max({$login_time_key}) as last_login_time from ";
    		if($params['by_create_time']){
    			if('1' == $params['by_create_time']){	//仅限制创建
    				$sql .= " log_create_player lcp join log_login ll on lcp.player_id = ll.player_id and lcp.created_time between {$params['create_start_time']} and {$params['create_end_time']} ";
    			}
    			if('2' == $params['by_create_time']){	//限制创建且有过充值
    				$sql .= " log_create_player lcp join log_login ll on lcp.player_id = ll.player_id and lcp.created_time between {$params['create_start_time']} and {$params['create_end_time']} 
    					join `{$this->db_payment}`.pay_order o on lcp.{$uid_key} = o.pay_user_id and o.get_payment = 1 and o.game_id = {$params['game_id']} ";
    			}
    		}else{
    			$sql .= " log_login ll ";
    		}
    		$sql .= " where {$login_time_key} between {$params['login_start_time']} and {$params['login_end_time']} group by ll.player_id having last_login_time < {$params['last_login_time']} ";
    		if($interval_second){
	    		$sql = "select ({$params['login_start_time']} + floor((last_login_time-{$params['login_start_time']})/{$interval_second})*{$interval_second}) as count_start_time,
	    		 count(player_id) as result_num from ({$sql}) a group by count_start_time";
    		}else{
    			$sql = "select ({$params['login_start_time']}) as count_start_time,
	    		 count(player_id) as result_num from ({$sql}) a group by count_start_time";
    		}
    		$date_result = DB::connection($this->db_name)->select($sql);
    	}else{
	    	if($params['by_create_time']){
	    		$date_result = CreatePlayerLog::on($this->db_name)->CalculateRetention($params, $this->db_qiqiwu, $this->db_payment)->get();
	    	}else{
	    		if('login' == $params['by_what_time']){	//不限制创建时间的登陆
	    			$date_result = LoginLog::on($this->db_name)->CalculateRetention($params, $this->db_qiqiwu, $this->db_payment)->get();
	    		}
	    		if('play' == $params['by_what_time']){	//不限制创建时间的玩牌
	    			$date_result = EconomyLog::on($this->db_name)->CalculateRetention($params, $this->db_qiqiwu, $this->db_payment)->get();
	    		}
	    	}
    	}
    	$tmp_result = array();
    	if($date_result){
    		$interval_second = $params['interval'] * 86400;	//天转时间
    		foreach ($date_result as $key => $value) {
    			if($interval_second){
    				$end_time = ($value->count_start_time + $interval_second > $params['login_end_time']) ? $params['login_end_time'] : ($value->count_start_time + $interval_second);
    			}else{
    				$end_time = $params['login_end_time'];
    			}
    			$tmp_result[] = array(
    				'count_start_time' => $value->count_start_time,
    				'count_end_time' => $end_time,
    				'result_num' => $value->result_num,
    				);
    		}
		}
		unset($date_result);

    	if($total_create){
    		$result = array(
    			'total_create' => $total_create,
    			'date_result' => $tmp_result,
    			);
    		return Response::json($result);
    	}else{
    		return Response::json(array(),404);
    	}
    }

     public function getOperationData(){
     	$operation_id = Input::get('operation_id');
     	$db = DB::connection($this->db_name);
     	$result = $db->select("SELECT COUNT(1) as num,AVG(level) as lev 
     		from(select player_id,MAX(level) as level 
     			from log_operations where operation_id={$operation_id} GROUP BY player_id) tab");
		if($result){
			return Response::json($result);
		}else{
			return Response::json(array(),404);
		}
     }

     public function getServerCreatePlayers(){	//获取服务器某一段时间内注册的玩家的最后登录以及充值信息
     	$data2slave = array('game_id','platform_id','server_internal_id','start_time','end_time','page','download');
     	$datafrommaster = array();
     	foreach ($data2slave as $key) {
     		$datafrommaster[$key] = Input::get($key);
     	};
     	if(!($single = CreatePlayerLog::on($this->db_name)->first())){
     		return Response::json(array(),404); 
     	}
     	$uid_key = isset($single->uid) ? 'uid' : 'user_id';
     	unset($single);
     	if(!($single = LoginLog::on($this->db_name)->first())){
     		return Response::json(array(),404); 
     	}
     	$login_time_key = isset($single->login_time) ? 'login_time' : 'action_time';
     	unset($single);
     	$count = CreatePlayerLog::on($this->db_name)->whereBetween('created_time', array($datafrommaster['start_time'], $datafrommaster['end_time']))->count();
     	$result = CreatePlayerLog::on($this->db_name)
 					->leftJoin(DB::raw('log_login as ll'), 'll.player_id', '=', 'p.player_id')
 					->leftJoin(DB::raw("`{$this->db_payment}`.pay_order as o"), function($join) use ($uid_key, $datafrommaster){
 						$join->on("p.{$uid_key}", '=', 'o.pay_user_id')
 							 ->where('o.get_payment', '=', 1);
 						if(!in_array($datafrommaster['game_id'], array(11,52,57))){	//非德扑的游戏区分订单game_id
 							$join->where('o.game_id', '=', $datafrommaster['game_id']);
 						}
 					})
 					->whereBetween('p.created_time', array($datafrommaster['start_time'], $datafrommaster['end_time']))
 					->groupby('p.player_id')
 					->selectRaw("p.*, from_unixtime(max(ll.{$login_time_key})) as last_login_time, sum(pay_amount*exchange) as pay_dollar")
 					->orderBy('player_id', 'ASC');
 		if($datafrommaster['download']){
 			$result = $result->get();
 		}else{
 			$result = $result->forPage($datafrommaster['page'], 100)->get();
 		}
 		$result['count'] = $count;

 		return Response::json($result); 
     }

}