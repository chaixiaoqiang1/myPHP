<?php

class SlaveBasicGameInfoQueryController extends SlaveServerBaseController {

	public function BasicGameInfoQuery(){
		$params = array();
		$params['game_id'] = Input::get('game_id');
		$params['platform_id'] = Input::get('platform_id');
		$params['start_time'] = Input::get('start_time');
		$params['end_time'] = Input::get('end_time');
		$params['all_server'] = Input::get('all_server');
		$params['all_server_internal_ids'] = Input::get('all_server_internal_ids');
		$params['server_internal_id'] = Input::get('server_internal_id');

		$type = Input::get('type');

		switch ($type) {
			case 'new_sign':
				return $this->BasicSignInfoQuery($params);	//当日注册信息
				break;

			case 'new_create':
				return $this->BasicCreateInfoQuery($params);	//当日创建信息
				break;
			
			case 'day_payment':
				return $this->BasicPaymentInfoQuery($params);	//付费信息
				break;

			case 'day_payment_os':
				return $this->BasicPaymentOSInfoQuery($params);	//不同设备付费信息
				break;

			case 'new_payment':
				return $this->BasicNewPaymentInfoQuery($params);	//新增付费用户
				break;

			case 'single_server':
				return $this->BasicServerInfoQuery($params);	//单服日志库的一些活跃在线和留存信息
				break;

			default:
				return Response::json(array(), 404); 
				break;
		}
	}	

	private function BasicSignInfoQuery($params){
		$test = SlaveUser::on($this->db_qiqiwu)->first();
		$game_key = '';
		if(isset($test->game_source)){
			$game_key = 'game_source';
		}elseif(isset($test->game_id)){
			$game_key = 'game_id';
		}
		unset($test);

		$time_limit = array(
			date("Y-m-d H:i:s", $params['start_time']),
			date("Y-m-d H:i:s", $params['end_time'])
			);
		$result = SlaveUser::on($this->db_qiqiwu)->whereBetween('created_time', $time_limit)->selectRaw("date_format(created_time, '%Y-%m-%d') as date, count(1) as sign_num");
		if($game_key){
			$result = $result->where($game_key, $params['game_id']);
		}
		$result = $result->groupBy('date')->get();
		
		return Response::json($result); 
	}

	private function BasicCreateInfoQuery($params){
		$test = SlaveCreatePlayer::on($this->db_qiqiwu)->first();
		$game_key = '';
		if(isset($test->game_id)){
			$game_key = 'game_id';
		}

		$result = SlaveCreatePlayer::on($this->db_qiqiwu)->whereBetween('created_time', array($params['start_time'], $params['end_time']))
									->selectRaw("from_unixtime(created_time, '%Y-%m-%d') as date, count(1) as create_num");
		if(!$params['all_server']){
			$result = $result->whereIn('server_id', $params['all_server_internal_ids']);
		}
		if($game_key){
			$result = $result->where($game_key, $params['game_id']);
		}

		$result = $result->groupBy('date')->get();
		
		return Response::json($result); 
	}

	private function BasicPaymentOSInfoQuery($params){
		$test = PayOrder::on($this->db_payment)->first();	//检测对应的订单表里是否有设备信息
		if(!isset($test->device_id)){
			return Response::json(array(), 404);
		}

		$result = PayOrder::on($this->db_payment)
					->leftJoin(DB::raw("{$this->db_qiqiwu}.device_list as dl"), function($join) use ($params){
						$join->on('dl.device_id', '=', 'o.device_id')
					         ->where('dl.game_id', '=', $params['game_id']);
					})
		            ->where('o.game_id', $params['game_id'])
					->where('get_payment', 1)
					->whereBetween('pay_time', array($params['start_time'], $params['end_time']))
					->selectRaw("from_unixtime(pay_time, '%Y-%m-%d') as date, ifnull(dl.os_type, 'Unknown') as pay_os_type, sum(pay_amount*exchange) as pay_dollar");

		if(!$params['all_server']){
			$result= $result->join("server_list as sl", 'o.server_id', '=', 'sl.server_id')
					->whereIn('sl.server_internal_id', $params['all_server_internal_ids']);
		}

		$result = $result->groupBy('date')->groupBy('pay_os_type')->get();

		return Response::json($result); 
	}

	private function BasicPaymentInfoQuery($params){
		$result = PayOrder::on($this->db_payment)->where('o.game_id', $params['game_id'])
					->where('get_payment', 1)
					->whereBetween('pay_time', array($params['start_time'], $params['end_time']))
					->selectRaw("from_unixtime(pay_time, '%Y-%m-%d') as date, count(distinct o.pay_user_id) as pay_user_num, count(1) as pay_times, sum(pay_amount*exchange) as pay_dollar");

		if(!$params['all_server']){
			$result= $result->join("server_list as sl", 'o.server_id', '=', 'sl.server_id')
					->whereIn('sl.server_internal_id', $params['all_server_internal_ids']);
		}

		$result = $result->groupBy('date')->get();

		return Response::json($result); 
	}

	private function BasicNewPaymentInfoQuery($params){
		$result = PayOrder::on($this->db_payment)->where('o.game_id', $params['game_id'])
					->where('get_payment', 1)
					->selectRaw("min(pay_time) as first_pay_time");

		if(!$params['all_server']){
			$result = $result->join("server_list as sl", 'o.server_id', '=', 'sl.server_id')
					->whereIn('sl.server_internal_id', $params['all_server_internal_ids']);
		}

		$result = $result->groupBy('o.pay_user_id')
						 ->havingRaw("first_pay_time between {$params['start_time']} and {$params['end_time']}")
						 ->get();
		$tmp_result = array();
		foreach ($result as $value) {
			$time = date("Y-m-d", $value->first_pay_time);
			if(isset($tmp_result[$time])){
				$tmp_result[$time]['new_pay_user']++;
			}else{
				$tmp_result[$time] = array(
					'date' => $time,
					'new_pay_user' => 1,
					);
			}
			unset($time);
		}

		return Response::json($tmp_result); 
	}

	private function BasicServerInfoQuery($params){
		$result = array();
		//获取登录数据，即活跃数据---这个稍慢
		$login_info = LoginLog::on($this->db_name)->LoginByDayNoToday($params['start_time'], $params['end_time'], $params['game_id'])->get();
		foreach ($login_info as $value) {
			$result[$value->date]['date'] = $value->date;
			$result[$value->date]['login_num'] = $value->login_num;
			$result[$value->date]['login_num_not_today'] = $value->login_num_not_today;
		}
		unset($login_info);
		unset($value);
		//获取在线人数数据---这个很快
		if(DB::connection($this->db_name)->select("show tables like 'log_online'")){	//在线表不是每个游戏都有
			$online_info = OnlineLog::on($this->db_name)->OnlineByDay($params['start_time'], $params['end_time'])->get();
			foreach ($online_info as $value) {
				$result[$value->date]['date'] = $value->date;
				$result[$value->date]['avg_online'] = $value->avg_online;
				$result[$value->date]['max_online'] = $value->max_online;
			}
		}

		//获取留存---这个很快
		$sub_sql = '';
		$days = array(2,3,4,5,6,7,14,30);
		$online_single = RetentionLog::on($this->db_name)->first();
		foreach ($days as $day) {
			$test_key = 'days_'.$day;
			if(isset($online_single->$test_key)){
				$sub_sql .= "sum($test_key) as $test_key,";
			}
		}
		unset($online_single);
		$online_info = RetentionLog::on($this->db_name)
						->whereBetween('retention_time', array($params['start_time']-3700, $params['end_time']))
						->selectRaw($sub_sql."sum(created_player_number) as created_player_number,from_unixtime(retention_time+3700, '%Y-%m-%d') as date")
						->groupBy('date')
						->get();

		foreach ($online_info as $value) {
			$result[$value->date]['date'] = $value->date;
			$result[$value->date]['created_player_number'] = $value->created_player_number;
			for ($i=2; $i <= 30; $i++) {
				$key = 'days_'.$i;
				if(isset($value->$key)){
					$result[$value->date][$key] = $value->$key;
				}
			}
		}

		return Response::json($result); 
	}
}