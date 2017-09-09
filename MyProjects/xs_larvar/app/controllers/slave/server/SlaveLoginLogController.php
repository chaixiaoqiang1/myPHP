<?php 

class SlaveLoginLogController extends \SlaveServerBaseController {

	const INTERVAL_TEN_MINUTE = 600;
	const INTERVAL_ONE_HOUR = 3600;
	const INTERVAL_ONE_DAY = 86400;

	public function __construct(LoginLog $model)
	{
		parent::__construct();
		$this->primary_key = $model->getKeyName();
	}

	public function getLog()
	{
		$page = (int)Input::get('page');
		$per_page = (int)Input::get('per_page');
		$page = $page > 0 ? $page : 1;
		$per_page = $per_page > 0 ? $per_page : 30; 
		$per_page = min($per_page, 30);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$count = LoginLog::on($this->db_name)
			->where('login_time', '>=', $start_time)
			->where('login_time', '<=', $end_time)
			->count();

		$total = ceil($count/$per_page);
		$log = LoginLog::on($this->db_name)->orderBy('login_time', 'DESC')
			->where('login_time', '>=', $start_time)
			->where('login_time', '<=', $end_time)
			->forPage($page, $per_page)->get();
			
		$result = array(
			'count' => $count,
			'total' => $total,
			'per_page' => $per_page,
			'current_page' => $page,
			'items' => $log->toArray(),
		);
		return Response::json($result);
	}

	/*
	 * 获取单个玩家的登陆日志
	 */
	public function getPlayerLoginLog()
	{
		$msg = array(
 			'code' => Config::get('errorcode.unknow'),
 			'error' => ''
 		);
 		$start_time = Input::get('start_time');
 		$end_time = Input::get('end_time');
 		$player_id = Input::get('player_id');
 		$page = Input::get('page');
 		$per_page = Input::get('per_page');
 		$page = $page > 0 ? $page :1;
 		$per_page = $per_page > 0 ? $per_page : 30;

 		$count = LoginLog::on($this->db_name)
			->where('login_time', '>=', $start_time)
			->where('login_time', '<=', $end_time)
			->where('player_id', '=', $player_id)
			->count();

 		$onepage = LoginLog::on($this->db_name)
 			->where('login_time', '>=', $start_time)
			->where('login_time', '<=', $end_time)
			->where('player_id', '=', $player_id)
 			->forPage($page, $per_page)
 			->get(); 

 		$result = array(
			'count' => $count,
			'total' => ceil($count / $per_page),
			'per_page' => $per_page,
			'current_page' => $page,
			'items' => $onepage->toArray(),
		);
		
		return Response::json($result);

	}

	/*
	 * 获取时间范围内的在线人数的数据
	 */
	public function getTrendByTime()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_login_trend'),
			'error' => ''
		);

		$interval = (int)Input::get('interval');

		$interval_array = array(
			self::INTERVAL_TEN_MINUTE, 
			self::INTERVAL_ONE_HOUR, 
			self::INTERVAL_ONE_DAY	
		);
		if (!in_array($interval, $interval_array)) {
			$msg['error'] = Lang::get('error.slave_not_have_params');
			return Response::json($msg, 403);
		}

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$server_internal_id = (int)Input::get('server_internal_id');
		$game_id = (int)Input::get('game_id');
		
		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.slave_time_limit_rang');
			return Response::json($msg, 403);
		}
		
		$first = OnlineLog::on($this->db_name)->select('online_time')
			->where('online_time', '>=', $start_time)
			->first();
		$last = OnlineLog::on($this->db_name)->select('online_time')
			->where('online_time', '<=', $end_time)
			->orderBy('online_time', 'DESC')
			->first();
		if ($first) {
			$start_time = $first->online_time;
		} else {
			$msg['error'] = Lang::get('error.slave_login_trend_time');
			return Response::json($msg, 403);
		}
		if ($last) {
			$end_time = $last->online_time;
		} else {
			$msg['error'] = Lang::get('error.slave_login_trend_time');
			return Response::json($msg, 403);
		}

		if ($interval == self::INTERVAL_ONE_DAY) {
			$start_time = strtotime(date('Y-m-d', $start_time));
			$end_time = strtotime(date('Y-m-d 23:59:59', $end_time));
			$result = OnlineLog::on($this->db_name)
				->select(DB::raw('UNIX_TIMESTAMP(FROM_UNIXTIME(online_time, \'%Y-%m-%d\')) as time, FLOOR(AVG(online_value)) as avg_value, MAX(online_value) as max_value'))
				->where('online_time', '>=', $start_time)
				->where('online_time', '<=', $end_time)
				->groupBy('time');
		} else if ($interval == self::INTERVAL_ONE_HOUR) {
			$start_time = strtotime(date('Y-m-d H:00:00', $start_time));
			$end_time = strtotime(date('Y-m-d H:59:59', $end_time));
			if ($end_time - $start_time > self::INTERVAL_ONE_DAY * 7) {
				$msg['error'] = Lang::get('error.slave_time_day_limit');
				return Response::json($msg, 403);
			}
			$result = OnlineLog::on($this->db_name)
				->select(DB::raw('UNIX_TIMESTAMP(FROM_UNIXTIME(online_time, \'%Y-%m-%d %H:00:00\')) as time, FLOOR(AVG(online_value)) as avg_value, MAX(online_value) as max_value'))
				->where('online_time', '>=', $start_time)
				->where('online_time', '<=', $end_time)
				->groupBy('time');
		} else if ($interval == self::INTERVAL_TEN_MINUTE) {
			$start_time = strtotime(date('Y-m-d H:00:00', $start_time));
			$end_time = strtotime(date('Y-m-d H:59:59', $end_time));
			if ($end_time - $start_time > self::INTERVAL_ONE_DAY) {
				$msg['error'] = Lang::get('error.slave_time_min_limit');
				return Response::json($msg, 403);
			}
			$result = OnlineLog::on($this->db_name)
				->select(DB::raw('online_time as time, online_value as avg_value, online_value as max_value'))
				->where('online_time', '>=', $start_time)
				->where('online_time', '<=', $end_time);
		}	
		if(in_array($game_id, Config::get('game_config.mnsggameids'))){
			$result->where('server_internal_id', $server_internal_id);
		}
		$result = $result->get();
		if (!$result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}

	/*
	 * 获得时间范围内的唯一登录用户与唯一IP数据
	 */

	public function getLoginTotalByTime()
	{
		$interval = (int)Input::get('interval');
		$level = (int)Input::get('level');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$game_id = (int)Input::get('game_id');
		switch ($interval) {
			case self::INTERVAL_ONE_DAY:
				if ($end_time - $start_time > self::INTERVAL_ONE_DAY * 31) {
					$msg['error'] = Lang::get('error.slave_time_limit_31_days');
					return Response::json($msg, 403);
				}
				break;
			case self::INTERVAL_ONE_HOUR:
				if ($end_time - $start_time > self::INTERVAL_ONE_DAY) {
					$msg['error'] = Lang::get('error.slave_time_limit_1_day');
					return Response::json($msg, 403);
				}
				break;
			case self::INTERVAL_TEN_MINUTE:
				if ($end_time - $start_time > self::INTERVAL_ONE_DAY / 4) {
					$msg['error'] = Lang::get('error.slave_time_limit_6_hours');
					return Response::json($msg, 403);
				}
				break;
			default:
				break;
		}
		$result = LoginLog::on($this->db_name)
			->getLoginTotal($start_time, $end_time, $interval,$game_id, $level)
			->get();
		if (!$result) {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);
		} else {
			return Response::json($result);
		}
	}

//玩家流失
	public function getRegistByTime()
	{
		$game_id = (int)Input::get('game_id');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$is_pay = Input::get('is_pay');
		$interval_time = Input::get('interval_time');
		$login_start_time = Input::get('login_start_time');
		$login_end_time = Input::get('login_end_time');
		$miss_days = Input::get('miss_days');

		$result_create = array();
		$result_login = array();
		$result = array();
		//select the player cerated at $start_time between $end_time
		$result_create = DB::connection($this->db_name)->select("select player_id from log_create_player 
														 where created_time between {$start_time} and {$end_time} 
														 order by player_id");
		$result_create = count($result_create);
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$time_key = 'action_time';
			$uid_key = 'uid';
			$ip_key = 'created_ip';
			$level_key = 'lev';
		}else{
			$time_key = 'login_time';
			$uid_key = 'user_id';
			$ip_key = 'remote_host';
			$level_key = 'level';
		}
		$result_login = DB::connection($this->db_name)->select("select count(1) 
								from log_create_player lcp join log_login ll on lcp.player_id=ll.player_id
								where created_time between {$start_time} and {$end_time} and {$time_key} between {$login_start_time} and {$login_end_time} 
			                    group by lcp.player_id ");
		$result_login = count($result_login);

		$sql = "select t.player_id,t.player_name,t.created_ip,from_unixtime(t.created_time) as created_time,from_unixtime(t.action_time) as action_time,
				t.last_level,t.times,h.pay_amount  from(
						select lcp.{$uid_key} as uid,lcp.player_id,lcp.player_name,lcp.{$ip_key} as created_ip,created_time as created_time,
								max({$time_key}) as action_time, max({$level_key}) as last_level, count(1) as times from log_create_player lcp 
						join log_login ll on lcp.player_id=ll.player_id 
						where created_time between {$start_time} and {$end_time} and {$time_key} between {$login_start_time} and {$login_end_time} 
						group by player_id) t 
		                left join (select pay_user_id,";
		if ($is_pay == 0) {
			$sql .=  	"sum(pay_amount*exchange) as pay_amount from `{$this->db_payment}`.pay_order 
		        		where create_time between {$login_start_time} and {$login_end_time} and get_payment= 1 and game_id = $game_id
		                group by pay_user_id) h  on t.uid = h.pay_user_id";
		}else{
			$sql .=  	"sum( if(create_time between {$login_start_time} and {$login_end_time},pay_amount*exchange,0)) as pay_amount from `{$this->db_payment}`.pay_order 
		        		where get_payment= 1 and game_id = $game_id
		                group by pay_user_id) h  on t.uid = h.pay_user_id";
		}
		       

		$result_info = DB::connection($this->db_name)->select($sql);
	    $result = array();
		if($miss_days > 0 && ($login_end_time-$login_start_time)/86400>(int)$miss_days){
			$player_ids = array();
			$result = $result_info;
			foreach ($result as $value) {
				$player_ids[$value->player_id] = array(
						'tmp_days' => 0,
						'longest_days' => 0,
					);
			}
			$tmp_time = $login_start_time;
			while(1){
				if($tmp_time+86399 > $login_end_time){
					break;
				}
				$tmp_end_time = $tmp_time+86400;
				$result_day_player_ids = DB::connection($this->db_name)->select("
								select distinct lcp.player_id from log_login ll join log_create_player lcp on ll.player_id = lcp.player_id	
								where lcp.created_time between {$start_time} and {$end_time} and ll.{$time_key} between {$tmp_time} and {$tmp_end_time}
											");		//得到一天内登陆的所有不冲突的且创建时间在选定时间内的player_id
				$day_player_ids = array();
				foreach ($result_day_player_ids as $value) {
					$day_player_ids[] = $value->player_id;
				}	//用数组保存所有的player_id
				unset($result_day_player_ids);	//数据量大，及时释放
				foreach ($player_ids as $id => &$value) {	//如果当天有登陆，那么清空连续天数，反之增加并判断是否超过最长时间
					//Log::info($id.' '.var_export($value,true));
					if(in_array($id, $day_player_ids)){
						$value['tmp_days'] = 0;
					}else{
						$value['tmp_days']++;
						$value['longest_days'] = $value['tmp_days'] > $value['longest_days'] ? $value['tmp_days'] : $value['longest_days'];
					}
					unset($value);
				}
				unset($day_player_ids);	//数据量大，及时释放
				$tmp_time += 86400;	//下一天
			}
			foreach ($result as $key => &$value) {	//去掉返回的数组中未达到连续未登录天数的数据
				if($player_ids[$value->player_id]['longest_days'] < $miss_days){
					unset($result[$key]);
				}else{
					$value->miss_days = $player_ids[$value->player_id]['longest_days'];
				}
			}
		}       
		$datas = array( 'result_create' => $result_create,
						'result_login' => $result_login,
					   'result' => $result,
					   'result_info' =>$result_info
					 );
		return Response::json($datas);
	}
}