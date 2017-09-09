<?php
class SlavePlayerController extends \SlaveServerBaseController {	

	public function __construct()
	{
		parent::__construct();
	}

	public function getNewerPointInfo(){
		$msg = array(
            'code' => '',
            'error' => ''
        );
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');

		$db = DB::connection($this->db_name);

		$create = $db->table('log_create_player')
					->whereBetween('created_time', array($start_time, $end_time))
					->count();

		$point_info = $db->table('log_point as lp')
						->join('log_create_player as lcp','lp.player_id', '=', 'lcp.player_id')
						->whereBetween('lcp.created_time', array($start_time, $end_time))
						->groupby('point')
						->selectRaw("point, count(1) as num")
						->orderby('point')
						->get();

		$result = array(
			'create_num' => $create,
			'point_info' => $point_info,
			);

		if($create){
			return Response::json($result);
		}else{
			$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
		}
	}

	public function OpenServerFrontDays(){	//统计单服开服一定天数之间的登陆和留存数据
		$game_id = (int)Input::get('game_id');
		$days_start = (int) Input::get('days_start');
		$days_end = (int) Input::get('days_end');
		$open_server_time = (int)Input::get('open_server_time');
		$start_time = strtotime(date('Y-m-d', $open_server_time));
		$time_between = array($start_time+($days_start-1)*86400, $start_time+($days_end)*86400-1);
		$result = array();
		$test = LoginLog::on($this->db_name)->first();
		if(isset($test->login_time)){
			$time_key = 'login_time';
			$is_anonymous = 0;
		}elseif(isset($test->action_time)){
			$time_key = 'action_time';
			$is_anonymous = 9;
		}else{
			$time_key = 'login_time';
			$is_anonymous = 0;
		}
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$is_anonymous = 9;
		}else{
			$is_anonymous = 0;
		}
		$dau_info = LoginLog::on($this->db_name)->whereBetween($time_key, $time_between)
							->selectRaw("from_unixtime($time_key, '%Y-%m-%d') as date, count(distinct player_id) as login_num")
							->groupby("date")
							->get();

		foreach ($dau_info as $value) {
			$tmp_key = floor((strtotime($value->date) - $start_time)/86400) + 1;
			$result[$tmp_key]['login_num'] = $value->login_num;
			unset($tmp_key);
		}
		unset($value);
		unset($dau_info);

		$create_info = CreatePlayerLog::on($this->db_name)->whereBetween('created_time', $time_between)
							->selectRaw("from_unixtime(created_time, '%Y-%m-%d') as date, count(distinct player_id) as create_num")
							->groupby("date")
							->get();
		
		foreach ($create_info as $value) {
			$tmp_key = floor((strtotime($value->date) - $start_time)/86400) + 1;
			$result[$tmp_key]['create_num'] = $value->create_num;
			unset($tmp_key);
		}
		unset($value);
		unset($create_info);
		//这里生成的结果是$result[(距离开服的天数)] = array('login_num' => (当日登陆玩家数), 'create_num' => (当日创建的玩家数));

		$retention_info = RetentionLog::on($this->db_name)
								->where('retention_time', '>=', $start_time)
								->where('is_anonymous', $is_anonymous)
								->orderby('retention_time', 'asc')
								->take(4)
								->get();

		if(count($retention_info)){
			$days_pass = floor((time() - $start_time - 7200)/86400);	//考虑到计算留存的脚本在凌晨执行，我们再略过两个小时，
																//这个值是当前超过开始时间的整天数，用来判断后面的留存是否已经有了完整的数据
			if($days_pass >= 4){	//超过整四天说明四天的完整创建数量都有
				$retention_array = array('created_player_number' => 0);
			}
			foreach (array(2,3,4,5,6,7,14) as $day) {
				if($day <= ($days_pass-3)){	//例如第五日的时候已经有了前四日的完整次留
					$retention_array['days_'.$day] = 0;
				}
			}
			foreach ($retention_info as $each_day) {
				foreach ($retention_array as $key => $value) {
					$retention_array[$key] += $each_day->$key;
				}
			}
		}else{
			$retention_array = array();
		}
		$result = array(
			'num_info' => $result,
			'retention_info' => $retention_array,
			);

		if(count($result)){
			return Response::json($result);
		}else{
			return Response::json(array(), 404);
		}
	}

	public function getMergeGemData(){
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$player_id = Input::get('player_id');

		$db = DB::connection($this->db_name);

		$result = $db->table('log_mergegem')
			->whereBetween('time',array($start_time,$end_time));
		if($player_id){
			$result->where('player_id',$player_id);
		}
		$result = $result->get();

		if($result){
			return Response::json($result);
		}else{
			$msg['error'] = Lang::get('error.slave_result_none');
            return Response::json($msg, 404);
		}
	}
}