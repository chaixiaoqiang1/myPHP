<?php 

class SlavePaymentController extends \SlaveServerBaseController {

	public function __construct()
	{
		parent::__construct();
	}

	public function getServerOrderStatistics()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_order'),
			'error' => ''
		);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$game_code = Input::get('game_code');
		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		
		$platform_server_id = (int)Input::get('platform_server_id');
		$game_id = (int)Input::get('game_id');
		$platfrom_server = PlatformServer::on($this->db_qiqiwu)->find($platform_server_id);
		if (!$platfrom_server) {
			$msg['error'] = Lang::get('error.server_not_found');
			return Response::json($msg, 403);
		}

		$currency_id = (int)Input::get('currency_id');
		$open_server_time = (int)Input::get('open_server_time');
		//获取总计
		$all_order = PayOrder::on($this->db_payment)
			->serverAllOrderStat($platform_server_id, $currency_id, $start_time, $end_time, $game_id)
			->first();
		$all_old_user_order = PayOrder::on($this->db_payment)
			->serverAllOrderStatOldUser($this->db_qiqiwu, $platform_server_id, $open_server_time, $currency_id, $start_time, $end_time, $game_id)
			->first();

		$order = PayOrder::on($this->db_payment)
			->serverOrderStatistics($platform_server_id, $currency_id, $start_time, $end_time, $game_id)
			->get();

		$old_user_order = PayOrder::on($this->db_payment)
			->serverOrderStatisticsOldUser($this->db_qiqiwu, $platform_server_id, $open_server_time, $currency_id, $start_time, $end_time, $game_id)
			->get();

		$created_player = 0;
		$days_2 = 0;
		$login_num = 0;
		$avg_online = 0;
		$max_online = 0;

		foreach ($order as &$v) {
			$v->total_amount = round($v->total_amount, 2);
			$v->total_dollar_amount = round($v->total_dollar_amount, 2);
			$v->old_user = array(
				'total_amount' => 0,
				'total_dollar_amount' => 0,
				'total_yuanbao_amount' => 0,
				'total_count' => 0,
				'total_user_count' => 0,
			);
			foreach ($old_user_order as $vv) {
				if ($vv->date == $v->date) {
					$vv->total_amount = round($vv->total_amount, 2);
					$vv->total_dollar_amount = round($vv->total_dollar_amount, 2);
					$v->old_user = $vv->toArray();
					continue;
				}
			}
			
		}
		unset($v);
		if ($order) {
			$all_order->total_amount = round($all_order->total_amount, 2);
			$all_order->total_dollar_amount = round($all_order->total_dollar_amount, 2);

			$all_order = (object)$all_order->toArray();
			$all_order->old_user = array(
				'total_amount' => 0,
				'total_dollar_amount' => 0,
				'total_yuanbao_amount' => 0,
				'total_count' => 0,
				'total_user_count' => 0,
			);
			if ($all_old_user_order) {
				$all_old_user_order->total_amount = round($all_old_user_order->total_amount, 2);
				$all_old_user_order->total_dollar_amount = round($all_old_user_order->total_dollar_amount, 2);

				$all_order->old_user = $all_old_user_order->toArray();
			}
			$order = $order->toArray();
			array_unshift($order, $all_order);
			$refund = SlaveRefundOrder::on($this->db_payment)
					->getRefund($start_time, $end_time, $game_id, $platform_server_id)
					->get();
			if($refund){
				$refund_total = 0;
				foreach ($refund as $value) {
					$refund_total += $value->refund_amount;
				}
				$refund = $refund->toArray();
				$refund_total_a = array(
					'refund_amount' => $refund_total,
					'refund_date' => ''
					);
				array_unshift($refund, $refund_total_a);
			}else{
				$refund = array('refund_date'=>'', 'refund_amount'=>'');
			}

			$result = array(
				'order' => $order,
				'refund' => $refund
				);
            return Response::json($result);
		} else {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);	
		}
	}

	public function getGameOrderStatistics()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_order'),
			'error' => ''
		);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$devide_servers = (int)Input::get('devide_servers');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}

		$currency_id = (int)Input::get('currency_id');

		$game_id = (int)Input::get('game_id');


		//获取总计
		$all_order = PayOrder::on($this->db_payment)
			->gameAllOrderStat($game_id, $currency_id, $start_time, $end_time)
			->first();

		$order = PayOrder::on($this->db_payment)
			->gameOrderStatistics($game_id, $currency_id, $start_time, $end_time, $devide_servers)
			->get();
		foreach ($order as &$v) {
			$v->total_amount = round($v->total_amount, 2);
			$v->total_dollar_amount = round($v->total_dollar_amount, 2);
		}
		unset($v);
		if ($order) {
			if ($all_order) {
				$all_order->total_amount = round($all_order->total_amount, 2);
				$all_order->total_dollar_amount = round($all_order->total_dollar_amount, 2);
				$order = $order->toArray();
				$all_order = (object)$all_order->toArray();
				array_unshift($order, $all_order);

				$refund = SlaveRefundOrder::on($this->db_payment)
					->getRefund($start_time, $end_time, $game_id, -1)
					->get();
				if($refund){
					$refund_total = 0;
					foreach ($refund as $value) {
						$refund_total += $value->refund_amount;
					}
					$refund = $refund->toArray();
					$refund_total_a = array(
						'refund_amount' => $refund_total,
						'refund_date' => 'Total'
						);
					array_unshift($refund, $refund_total_a);
				}else{
					$refund = array('refund_date'=>'', 'refund_amount'=>'');
				}
				
				$result = array(
					'order' => $order,
					'refund' => $refund
					);
			}
            //Log::info("getGameOrderStatistics".var_export($result, true));
			return Response::json($result);
		} else {
			return Response::json($msg, 404);
		}
	}

	public function getLuckyOrderSN()
	{
	    $lucky_number = Input::get('lucky_number');
	    $start_time = (int)Input::get('start_time');
	    $end_time = (int)Input::get('end_time');
	    $order = PayOrder::on($this->db_payment)
	    ->getLuckyOrderSN($this->db_qiqiwu, $lucky_number, $start_time, $end_time)
	    ->get();
	    if ($order) {
	        return Response::json($order);
	    } else {
	        return Response::json(array(), 404);
	    }
	}

	//以下的几个方法，由于夜夜三国没有create_player表查询的时候改为了日志库，所以都传了db_name
	
	public function getOrderByOrderSN()
	{
		$order_sn = Input::get('order_sn');
		$game_id = Input::get('game_id');
		$order = PayOrder::on($this->db_payment)
			->orderByOrderSN($this->db_qiqiwu, $order_sn, $game_id, $this->db_name)
			->first();
		if ($order) {
			return Response::json($order);
		} else {
			return Response::json(array(), 404);
		}
	}

	public function getOrderByOrderID(){
		$order_id = Input::get('order_id');
		$game_id = Input::get('game_id');
		$order = PayOrder::on($this->db_payment)
			->orderByOrderID($this->db_qiqiwu, $order_id, $game_id, $this->db_name)
			->first();
		if ($order) {
			return Response::json($order);
		} else {
			return Response::json(array(), 404);
		}		
	}
	public function getOrderByTradeseq()
	{
		$tradeseq = Input::get('tradeseq');
		$game_id = Input::get('game_id');
		$order = PayOrder::on($this->db_payment)
		->orderByTradeseq($this->db_qiqiwu, $tradeseq, $game_id, $this->db_name)
		->get();
		if ($order) {
			return Response::json($order);
		} else {
			return Response::json(array(), 404);
		}
	}
	public function getUserOrder()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_order'),
			'error' => ''
		);
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$uid = Input::get('uid');
		$player_name = Input::get('player_name');
		$player_id = (int)Input::get('player_id');
		$bank_account = Input::get('bank_account');
		$get_payment = Input::get('get_payment');
        $offer_yuanbao = Input::get('offer_yuanbao');
        $platform_server_id = Input::get('platform_server_id');
        $limit_order = Input::get('limit_order');
		$game_id = Input::get('game_id');
 		if (!$uid && !$player_name && !$bank_account && !$player_id && !$limit_order) {
			$msg['error'] = Lang::get('error.slave_order_user');
			return Response::json($msg, 403);
		}
		$order = PayOrder::on($this->db_payment)
			->orderByUser($this->db_qiqiwu, $uid, $player_name, $player_id, $start_time, $end_time, $bank_account, $game_id, $this->db_name, $get_payment, $offer_yuanbao, $platform_server_id, $limit_order)
			->get();
//        Log::info("player name:".$player_name."---game id:".$game_id."---start time:".$start_time."---end time:".$end_time."---order result".var_export($order, true));
		if ($order) {
			return Response::json($order);
		} else {
			return Response::json(array(), 404);
		}

	}

	public function getOrders()
	{
		$page = (int)Input::get('page');
		$per_page = (int)Input::get('per_page');
		$page = $page > 0 ? $page : 1;
		$per_page = $per_page > 0 ? $per_page : 30;
        $statistics_time = (int)Input::get('statistics_time');

		$platform_server_id = (int)Input::get('platform_server_id');
		$pay_type_id = (int)Input::get('pay_type_id');
		$method_id = (int)Input::get('method_id');
		$get_payment = Input::get('get_payment');

		$low_amount = (int)Input::get('low_amount');
		$high_amount = (int)Input::get('high_amount');
		$low_gold = (int)Input::get('low_gold');
		$high_gold = (int)Input::get('high_gold');
		$game_id = (int)Input::get('game_id');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$offer_yuanbao = Input::get('offer_yuanbao');
		$sdk_id = Input::get('sdk_id');

		if($sdk_id){
			$single = PayOrder::on($this->db_payment)->first();
			if(isset($single->sdk_id)){
				$count = PayOrder::on($this->db_payment)//夜夜三国没有create_player表，要用日志库
					->orders($this->db_qiqiwu, $platform_server_id, $pay_type_id, $method_id, $get_payment, $low_amount, $high_amount, $low_gold, $high_gold, $start_time, $end_time, $game_id, $offer_yuanbao, $statistics_time, $this->db_name)
					->where('o.sdk_id', $sdk_id)
					->count();

				$order = PayOrder::on($this->db_payment)
					->orders($this->db_qiqiwu, $platform_server_id, $pay_type_id, $method_id, $get_payment, $low_amount, $high_amount, $low_gold, $high_gold, $start_time, $end_time, $game_id, $offer_yuanbao, $statistics_time, $this->db_name)
					->where('o.sdk_id', $sdk_id)
					->forPage($page, $per_page)
					->get();
			}else{
				$count = PayOrder::on($this->db_payment)//夜夜三国没有create_player表，要用日志库
					->orders($this->db_qiqiwu, $platform_server_id, $pay_type_id, $method_id, $get_payment, $low_amount, $high_amount, $low_gold, $high_gold, $start_time, $end_time, $game_id, $offer_yuanbao, $statistics_time, $this->db_name)
					->count();

				$order = PayOrder::on($this->db_payment)
					->orders($this->db_qiqiwu, $platform_server_id, $pay_type_id, $method_id, $get_payment, $low_amount, $high_amount, $low_gold, $high_gold, $start_time, $end_time, $game_id, $offer_yuanbao, $statistics_time, $this->db_name)
					->forPage($page, $per_page)
					->get();
			}
		}else{
			$count = PayOrder::on($this->db_payment)//夜夜三国没有create_player表，要用日志库
				->orders($this->db_qiqiwu, $platform_server_id, $pay_type_id, $method_id, $get_payment, $low_amount, $high_amount, $low_gold, $high_gold, $start_time, $end_time, $game_id, $offer_yuanbao, $statistics_time, $this->db_name)
				->count();

			$order = PayOrder::on($this->db_payment)
				->orders($this->db_qiqiwu, $platform_server_id, $pay_type_id, $method_id, $get_payment, $low_amount, $high_amount, $low_gold, $high_gold, $start_time, $end_time, $game_id, $offer_yuanbao, $statistics_time, $this->db_name)
				->forPage($page, $per_page)
				->get();
		}
		foreach ($order as &$v) {
			$v->dollar_amount = sprintf('%.2f', $v->dollar_amount);
		}
		unset($v);
		
		$result = array(
			'count' => $count,
			'total' => ceil($count / $per_page),
			'per_page' => $per_page,
			'current_page' => $page,
			'items' => $order->toArray(),
		);
		
		return Response::json($result);
	}

	public function getAllOrders()
	{
	    $platform_server_id = (int)Input::get('platform_server_id');
	    $pay_type_id = (int)Input::get('pay_type_id');
	    $get_payment = Input::get('get_payment');
	
	    $low_amount = (int)Input::get('low_amount');
	    $high_amount = (int)Input::get('high_amount');
	    $low_gold = (int)Input::get('low_gold');
	    $high_gold = (int)Input::get('high_gold');
	    $game_id = (int)Input::get('game_id');
	    $start_time = (int)Input::get('start_time');
	    $end_time = (int)Input::get('end_time');
	    $offer_yuanbao = Input::get('offer_yuanbao');
	
	    $order = PayOrder::on($this->db_payment)
	    ->orders($this->db_qiqiwu, $platform_server_id, $pay_type_id, $get_payment, $low_amount, $high_amount, $low_gold, $high_gold, $start_time, $end_time, $game_id, $offer_yuanbao, $this->db_name)
	    ->get();
	
	    foreach ($order as &$v) {
	        $v->dollar_amount = sprintf('%.2f', $v->dollar_amount);
	    }
	    unset($v);
	    return Response::json($order);
	}
	public function getUnPayOrders()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_order'),
			'error' => ''
		);

		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		
		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		
		$game_id = (int)Input::get('game_id');
		$platform_server_id = (int)Input::get('platform_server_id');
		$failed_times = (int)Input::get('failed_times');
		$order_by = Input::get('order_by');
		$order_desc = Input::get('order_desc');

		$order = PayOrder::on($this->db_payment)
			->unPayOrder($this->db_qiqiwu, $start_time, $end_time, $failed_times, $game_id, $platform_server_id, $order_by, $order_desc)
			->get();
		if ($order) {
			return Response::json($order);
		} else {
			return Response::json(array(), 404);
		}
	}
	public function getPlayersInTrouble()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_order'),
			'error' => ''
		);

		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		
		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		
		$game_id = (int)Input::get('game_id');
		$platform_server_id = (int)Input::get('platform_server_id');
		$failed_times = (int)Input::get('failed_times');
		$failed_times = $failed_times ? $failed_times : 1;
		
		//获取储值成功的玩家的uid
		$success_order_uids = array();
		$db = DB::connection($this->db_payment);
		$success_orders = $db->select("select pay_user_id from pay_order where get_payment=1 and create_time between {$start_time} and {$end_time} group by pay_user_id");
		if($success_orders)
		{
		    foreach ( $success_orders as $v )
		    {
		        if($v->pay_user_id){
		            $success_order_uids[] = "'" . $v->pay_user_id . "'";
		        }
		    }
		}
		$uids_string = implode($success_order_uids, ",");
		//获取储值失败的订单
		$order = PayOrder::on($this->db_payment)
		->unPayOrder($this->db_qiqiwu, $start_time, $end_time, $failed_times, $game_id, $platform_server_id, $order_by='', $order_desc='');
		if($uids_string){
		    $order = $order->havingRaw("pay_user_id not in ({$uids_string})")->get();
		} else {
		    $order = $order->get();
		}
		if ($order) {
			return Response::json($order);
		} else {
			return Response::json(array(), 404);
		}
	}

	public function getYuanbaoRank()
	{
	    $msg = array(
	        'code' => Config::get('errorcode.slave_order'),
	        'error' => ''
	    );

	    $start_time = Input::get('start_time');
	    $end_time = Input::get('end_time');
	    $page = (int)Input::get('page');
	    $per_page = (int)Input::get('per_page');
	    $page = $page > 0 ? $page : 1;
	    $per_page = $per_page > 0 ? $per_page : 30;
	    
	    $game_id = (int)Input::get('game_id');
		$currency_id = (int)Input::get('currency_id');
	
	    $platform_server_id = (int)Input::get('platform_server_id');
	    $platfrom_server = PlatformServer::on($this->db_qiqiwu)->find($platform_server_id);
	    if (!$platfrom_server) {
	        $msg['error'] = Lang::get('error.server_not_found');
	        return Response::json($msg, 403);
	    }
		$server_internal_id = (int)Input::get('server_internal_id');

		$is_app_id = (DB::connection($this->db_qiqiwu)->select("show tables like 'tp_applications'") && DB::connection($this->db_qiqiwu)->select("DESC tp_applications game_id")) ? 
		DB::connection($this->db_qiqiwu)->select("SELECT app_id FROM tp_applications WHERE game_id = {$game_id}") : 0;
		$count = count(PayOrder::on($this->db_payment)
	    ->yuanbaoRank($this->db_qiqiwu, $start_time, $end_time, $currency_id, $platform_server_id, $server_internal_id, $this->db_name, $game_id, $is_app_id)
	    ->get());
	    $order = PayOrder::on($this->db_payment)
	    ->yuanbaoRank($this->db_qiqiwu, $start_time, $end_time, $currency_id, $platform_server_id, $server_internal_id, $this->db_name, $game_id, $is_app_id)
	    ->forPage($page, $per_page)
	    ->get();
 	    // $s = var_export($order,true);
 	    // Log::info($s);
	    if ($order) {
			foreach ($order as $v) {
				$first_lev = LevelUpLog::on($this->db_name)
				->getFirstOrderTime($v->first_order_time, $v->player_id)
				->take(1)
				->pluck('new_level');	
				$v->first_lev = $first_lev ? $first_lev : 1;
			}
	        $result = array(
			'count' => $count,
			'total' => ceil($count / $per_page),
			'per_page' => $per_page,
			'current_page' => $page,
			'items' => $order->toArray(),
		);
	        return $result;
	    } else {
	        return Response::json(array(), 404);
	    }
	}

	public function getYuanbaoRankForMG(){
	    $start_time = Input::get('start_time');
	    $end_time = Input::get('end_time');
	    $page = (int)Input::get('page');
	    $per_page = (int)Input::get('per_page');
	    $page = $page > 0 ? $page : 1;
	    $per_page = $per_page > 0 ? $per_page : 30;
	    
	    $game_id = (int)Input::get('game_id');
		$currency_id = (int)Input::get('currency_id');
	
	    $platform_server_ids = Input::get('platform_server_ids');
	    $platform_server_ids = is_array($platform_server_ids) ? $platform_server_ids : array();
	    if (!count($platform_server_ids)) {
	        $msg['error'] = Lang::get('error.server_not_found');
	        return Response::json($msg, 403);
	    }
	    $platfrom_servers = PlatformServer::on($this->db_qiqiwu)->find($platform_server_ids);
	    if (!count($platfrom_servers)) {
	        $msg['error'] = Lang::get('error.server_not_found');
	        return Response::json($msg, 403);
	    }

		$count = count(PayOrder::on($this->db_payment)
	    ->yuanbaoRankForMG($this->db_qiqiwu, $start_time, $end_time, $currency_id, $platform_server_ids, $this->db_name, $game_id)
	    ->get());
	    $order = PayOrder::on($this->db_payment)
	    ->yuanbaoRankForMG($this->db_qiqiwu, $start_time, $end_time, $currency_id, $platform_server_ids, $this->db_name, $game_id)
	    ->forPage($page, $per_page)
	    ->get();
 	    
	    if (count($order)) {
			foreach ($order as $v) {
				$db_name = $game_id.'.'.$v->server_internal_id;
				$this->setSingleDB($db_name);
				$first_lev = LevelUpLog::on($db_name)
				->getFirstOrderTimeForMG($v->first_order_time, $v->player_id)
				->take(1)
				->pluck('lev');	
				$last_login_line = LoginLog::on($db_name)
					->where('player_id', $v->player_id)
					->orderBy('id', 'desc')
					->first();
				$last_login = isset($last_login_line->action_time) ? $last_login_line->action_time : 0;
				$last_level = isset($last_login_line->lev) ? $last_login_line->lev : 0;
				$v->first_lev = $first_lev ? $first_lev : 1;
				$v->last_login = $last_login ? date('Y-m-d H:i:s', $last_login) : '';
				$v->level_now = $last_level ? $last_level : 1;
				DB::disconnect($db_name);
			}
	        $result = array(
				'count' => $count,
				'total' => ceil($count / $per_page),
				'per_page' => $per_page,
				'current_page' => $page,
				'items' => $order->toArray(),
				);
	        return Response::json($result);
	    } else {
		    $msg = array(
		        'code' => Config::get('errorcode.slave_order'),
		        'error' => 'No Data'
		    );
	        return Response::json($msg, 404);
	    }
	}

	public function getAllYuanbaoRank()
	{
	    $msg = array(
	        'code' => Config::get('errorcode.slave_order'),
	        'error' => ''
	    );

	    $start_time = Input::get('start_time');
	    $end_time = Input::get('end_time');
	    
	    $game_id = (int)Input::get('game_id');
		$currency_id = (int)Input::get('currency_id');
		$lower_bound = (int)Input::get('lower_bound');
		$upper_bound = (int)Input::get('upper_bound');
	
	    $platform_server_id = (int)Input::get('platform_server_id');
	    $platfrom_server = PlatformServer::on($this->db_qiqiwu)->find($platform_server_id);
	    if (!$platfrom_server) {
	        $msg['error'] = Lang::get('error.server_not_found');
	        return Response::json($msg, 403);
	    }
	
		$server_internal_id = (int)Input::get('server_internal_id');
	    $order = PayOrder::on($this->db_payment)
	    ->allYuanbaoRank($this->db_qiqiwu, $start_time, $end_time, $currency_id, $game_id, $platform_server_id, $server_internal_id, $lower_bound, $upper_bound, $this->db_name)
	    ->get();
	    //Log::info("SlavePaymentController-log----game_id:".$game->game_id."---$platform_server_id:".$server->platform_server_id."---start time:".$start_time."---end time:".$end_time."---lower_bound:".$lower_bound."---upper_bound:".$upper_bound."---db_name:".$this->db_name);
	    if ($order) {
			foreach ($order as &$v) {
				$first_lev = LevelUpLog::on($this->db_name)
				->getFirstOrderTime($v->first_order_time, $v->player_id)
				->take(1)
				->pluck('new_level');	
				$v->first_lev = $first_lev ? $first_lev : 1;
			}
	        return Response::json($order);
	    } else {
	        return Response::json(array(), 404);
	    }
	}

	public function PlayerPaymentFilter(){	//统计每个玩家在一段时间内的充值信息
	    $msg = array(
	        'code' => Config::get('errorcode.slave_order'),
	        'error' => ''
	    );

	    $start_time = Input::get('start_time');
	    $end_time = Input::get('end_time');
	    
	    $game_id = (int)Input::get('game_id');
		$currency_id = (int)Input::get('currency_id');
		$lower_bound = (int)Input::get('lower_bound');
		$upper_bound = (int)Input::get('upper_bound');
	
	    $platform_server_id = (int)Input::get('platform_server_id');
	    $platfrom_server = PlatformServer::on($this->db_qiqiwu)->find($platform_server_id);
	    if (!$platfrom_server) {
	        $msg['error'] = Lang::get('error.server_not_found');
	        return Response::json($msg, 403);
	    }
	
		$server_internal_id = (int)Input::get('server_internal_id');
	    $order = PayOrder::on($this->db_payment)
	    ->PlayerPaymentFilter($this->db_qiqiwu, $start_time, $end_time, $currency_id, $game_id, $platform_server_id, $server_internal_id, $lower_bound, $upper_bound, $this->db_name)
	    ->get();
	    if ($order) {
	        return Response::json($order);
	    } else {
	        return Response::json(array(), 404);
	    }
	}

	public function getFBDisputeOrders()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_dispute_order'),
			'error' => Lang::get('error.time_interval')
		);
		$order_sn = Input::get('order_sn');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		if ($start_time >= $end_time) {
			return Response::json($msg, 403);
		}
		$fb_name = Input::get('fb_name');
		$fb_id = Input::get('fb_id');
		$status = (int)Input::get('status');
		$order = SlaveDisputeOrder::on($this->db_payment)
			->getOrders($this->db_qiqiwu, $order_sn, $start_time, $end_time, $fb_name, $fb_id, $status)
			->get();
		if ($order) {
			return Response::json($order);
		} else {
			$msg['error'] = Lang::get('error.slave_dispute_order_not_found');
			return Response::json(array(), 404);
		}
	}

	public function getRefundOrders()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_refund_order'),
			'error' => ''
		);
		$order_sn = Input::get('order_sn');
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		$pay_type_id = (int)Input::get('pay_type_id');
		$orders = SlaveRefundOrder::on($this->db_payment)
			->refundOrders($this->db_qiqiwu, $order_sn, $start_time, $end_time, $pay_type_id)
			->get();
		if ($orders) {
			return Response::json($orders);
		} else {
			$msg['error'] = Lang::get('error.slave_refund_order_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getPayTypeStat()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_pay_type_stat'),
			'error' => ''
		);
		$currency_id = (int)Input::get('currency_id');
		$pay_type_id = (int)Input::get('pay_type_id');	
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$game_id = (int)Input::get('game_id');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}

		$pay_type = PayOrder::on($this->db_payment)
			->payTypeStat($pay_type_id, $start_time, $end_time, $currency_id, $game_id)
			->get();

		if ($pay_type) {
			return Response::json($pay_type);
		} else {
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getServerRevenueByDay()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_server_day_revenue'),
			'error' => Lang::get('error.basic_input_error')
		);

		$platform_server_ids = Input::get('platform_server_ids');
		$platform_server_ids = explode(',', $platform_server_ids);
		$days_start = (int) Input::get('days_start');
		$days_end = (int) Input::get('days_end');
		if (empty($platform_server_ids)) {
			return Response::json($msg, 403);	
		}

		$servers = SlaveServerList::on($this->db_qiqiwu)
			->whereIn('sl.server_id', $platform_server_ids)
			->orderBy('sl.server_id', 'DESC')
			->get();
		
		$result = array();
		$today = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
		foreach($servers as $server) {
			$start_time = strtotime(date('Y-m-d', $server->open_server_time));
			$result[$server->server_id]['server_name'] = $server->server_name;
			$result[$server->server_id]['open_server_time'] = $server->open_server_time;
			$result[$server->server_id]['pay_time'] = '';
			$order = PayOrder::on($this->db_payment)
				->selectRaw("
					UNIX_TIMESTAMP(FROM_UNIXTIME(pay_time, '%Y-%m-%d')) as ptime,
					SUM(pay_amount * exchange) as dollar_amount,
					count(distinct pay_user_id) as pay_user_num
				")
				->where('server_id', $server->server_id)
				->where('get_payment', 1)
				->whereBetween('pay_time', array($start_time + 86400*($days_start-1), $start_time + 86400*($days_end)))
				->whereNotExists(function($query) {
					$query->select(DB::raw(1))
						->from('refund_order')
						->whereRaw('refund_order.order_sn = o.order_sn');
				})
				->groupBy('ptime')
				->orderBy('ptime', 'ASC')
				->get();
			$arr = $order->toArray();
			unset($order);
			$result[$server->server_id]['days'] = array();
			if (isset($arr[0])) {
				$result[$server->server_id]['pay_time'] = $arr[0]['ptime'];
			}
			$tmp_arr = array();
			foreach($arr as $v) {
				$tmp_arr[$v['ptime']]['dollar'] = $v['dollar_amount'];
				$tmp_arr[$v['ptime']]['user_num'] = $v['pay_user_num'];
			}
			unset($arr);
			for ($i = $days_start; $i <= $days_end; $i++) {
				$ptime = $start_time + ($i-1) * 86400;
				if (isset($tmp_arr[$ptime])) {
					$result[$server->server_id]['days'][$i]['dollar'] = round($tmp_arr[$ptime]['dollar'], 2);
					$result[$server->server_id]['days'][$i]['user_num'] = $tmp_arr[$ptime]['user_num'];
				} elseif(isset($tmp_arr[$ptime+3600])){	//这里分别判断+3600和-3600是为了适应某些国家的时令调整
					$result[$server->server_id]['days'][$i]['dollar'] = round($tmp_arr[$ptime+3600]['dollar'], 2);
					$result[$server->server_id]['days'][$i]['user_num'] = $tmp_arr[$ptime+3600]['user_num'];
				} elseif(isset($tmp_arr[$ptime-3600])){
					$result[$server->server_id]['days'][$i]['dollar'] = round($tmp_arr[$ptime-3600]['dollar'], 2);
					$result[$server->server_id]['days'][$i]['user_num'] = $tmp_arr[$ptime-3600]['user_num'];
				} else {
					if ($ptime <= $today) {
						$result[$server->server_id]['days'][$i]['dollar'] = 0;	
						$result[$server->server_id]['days'][$i]['user_num'] = 0;
					} else {
						$result[$server->server_id]['days'][$i]['dollar'] = '';
						$result[$server->server_id]['days'][$i]['user_num'] = '';
					}
				}	
			}
		}
		if (empty($result)) {
			return Response::json(array(), 404);
		}
		return Response::json((object)$result);
	}

	public function getExchangeRate(){
		$db = DB::connection($this->db_payment);
		$rates = $db->select('SELECT * FROM ( SELECT * FROM `exchange` ORDER BY timeline DESC ) AS a GROUP BY TYPE');
		if ($rates) {
			return Response::json($rates);
		} else {
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg, 404);
		}
	}
	public function getPayType(){
		$db = DB::connection($this->db_payment);
		$pay_types = $db->select('SELECT * FROM `pay_type` ORDER BY id ASC ');
		if ($pay_types) {
			return Response::json($pay_types);
		} else {
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getMerchantData(){
	    $id = (int)Input::get('id');
		$db = DB::connection($this->db_payment);
		if($id) {
		    $merchant_data = $db->select("SELECT * FROM `merchant_data` WHERE `id` = {$id} limit 1 ");
		} else {
		    $merchant_data = $db->select('SELECT * FROM `merchant_data` ORDER BY id ASC ');
		}
		
		if ($merchant_data) {
			return Response::json($merchant_data);
		} else {
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getPayMethod(){
		$game_id = Input::get('game_id');
		$platform_id = Input::get('platform_id');
		$db = DB::connection($this->db_payment);
		if($platform_id == 50 || $platform_id == 38 || $platform_id == 1){
			$pay_methods = $db->select(
				"SELECT 
				pm.id as id,
				pm.pay_type_id as pay_type_id,
				pm.method_id as method_id,
				pm.method_name as method_name,
				pm.domain_name as domain_name,
				pm.class_name as class_name,
				pm.is_selected as is_selected,
				pm.is_recommend as is_recommend,
				pm.method_order as method_order,
				pm.method_description as method_description,
				pm.post_url as post_url,
				pm.html_name as html_name,
				pm.is_use as is_use,
				pm.zone as zone,
				pm.currency as currency,
				pm.use_for_month_card as use_for_month_card,
				pa.start_time as start_time,
				pa.end_time as end_time,
				pa.huodong_rate as huodong_rate
				FROM payment_method as pm
				left join payment_activity as pa 
				on pm.id = pa.payment_method_id
				where pa.game_id = {$game_id}
				ORDER BY id ASC ");
		}else{
			$pay_methods = $db->select('SELECT * FROM `payment_method` ORDER BY id ASC ');
		}
		if ($pay_methods) {
			return Response::json($pay_methods);
		} else {
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getPayAmount(){
		$pay_type_id = Input::get('pay_type_id');
		$method_id = Input::get('method_id');
		// $domain_name = Input::get('domain_name');
		$db = DB::connection($this->db_payment);
		// if(!empty($method_id)){
			$pay_amount = $db->select("SELECT * FROM `pay_amount` where pay_type_id= {$pay_type_id} and method_id={$method_id} ORDER BY id ASC");
		// }else{
		// 	$pay_amount = $db->select("SELECT * FROM `pay_amount` where pay_type_id={$pay_type_id} and domain_name={$domain_name} and method_id={$method_id} ORDER BY id ASC");
		// }
		if($pay_amount){
			return Response::json($pay_amount);
		}else{
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg,404);
		}
	}

	public function getPayCurrency(){
		$id = Input::get('id');
		$db = DB::connection($this->db_payment);
		if ($id) {
			$pay_currencys = $db->select("SELECT * FROM `payment_currency` WHERE `id` = {$id} limit 1");
		} else {
			$pay_currencys = $db->select('SELECT * FROM `payment_currency` ORDER BY id ASC ');
		}
		if ($pay_currencys) {
			return Response::json($pay_currencys);
			
		} else {
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg, 404);
		}
	}//德州扑克操作
	public function getPokerOrderStat()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_order'),
			'error' => ''
		);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}

		$currency_id = (int)Input::get('currency_id');
		$game_id = (int)Input::get('game_id');


		//获取总计
		$all_order = PayOrder::on($this->db_payment)
			->gameAllOrderStat($game_id, $currency_id, $start_time, $end_time)
			->first();

		$order = PayOrder::on($this->db_payment)
			->gameOrderStatistics($game_id, $currency_id, $start_time, $end_time)
			->get();
		foreach ($order as &$v) {
			$v->total_amount = round($v->total_amount, 2);
			$v->total_dollar_amount = round($v->total_dollar_amount, 2);
		}
		unset($v);
		if ($order) {
			if ($all_order) {
				$all_order->total_amount = round($all_order->total_amount, 2);
				$all_order->total_dollar_amount = round($all_order->total_dollar_amount, 2);
				$order = $order->toArray();
				$all_order = (object)$all_order->toArray();
				array_unshift($order, $all_order);
			}
			return Response::json($order);		
		} else {
			$msg['error'] = Lang::get('error.slave_result_none');
			return Response::json($msg, 404);	
		}
	}

	public function getPokerOldPays()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_order'),
			'error' => ''
		);

		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');

		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}
		$game_id = (int)Input::get('game_id');

		$db = DB::connection($this->db_payment);
		
		$old_count = $db->select("select pay_user_id  , count(order_id) as nums , FROM_UNIXTIME(pay_time, '%Y-%m-%d') as date from pay_order  where pay_time > {$start_time} and pay_time < {$end_time}  group by pay_user_id having nums > 1 ");
		if ($old_count) {
			return Response::json($old_count);
			
		} else {
			$msg['error'] = Lang::get('slave.pay_type_stat_not_found');
			return Response::json($msg, 404);
		}
		
	}

	//获取日登录用户
	/**/

	public function getPokerLogDay()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$start_time =Input::get('start_time');
		$end_time = Input::get('end_time');
		$game_id = Input::get('game_id');
		
		$sql = "select count(distinct player_id) as num , FROM_UNIXTIME(login_time, '%Y-%m-%d') as date from log_login  where login_time > '{$start_time}' and login_time < '{$end_time}'    group by date order by date desc";
		$users =  DB::connection($this->db_name)->select($sql);
		if (count($users) > 0) {
			return Response::json($users);
		} else {
			$msg['error'] = Lang::get('slave.userstat_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getPokerLogDays()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$start_time =Input::get('start_time');
		$end_time = Input::get('end_time');
		$game_id = Input::get('game_id');
		
		$sql = "select distinct cp.user_id from log_create_player cp left join log_login ll on ll.player_id = cp.player_id  where ll.login_time > {$start_time} and ll.login_time < {$end_time} and ll.is_login = 1 order by login_time desc";
		$users =  DB::connection($this->db_name)->select($sql);
		//Log::info(var_export($users, true));
		if (isset($users)) {
			return Response::json($users);
		} else {
			$msg['error'] = Lang::get('slave.userstat_not_found');
			return Response::json($msg, 404);
		}
	}



	
	public function getPokerPaypayDays()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$str = Input::get('str');
		
		$sql = "select uid from create_player where player_id in ($str)";
		$users =  DB::connection($this->db_qiqiwu)->select($sql);
		if (isset($users)) {
			return Response::json($users);
		} else {
			$msg['error'] = Lang::get('slave.userstat_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getPayNumPoker()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$str = Input::get('str');
		//$sql = "select distinct pay_user_id from pay_order where get_payment = 1";
		//$users =  DB::connection($this->db_payment)->select($sql);
		$num = PayOrder::on($this->db_payment)->select("pay_user_id")->whereIn('pay_user_id', $str)->where('get_payment', 1)->get();
		$num = array_unique((array)$num);
		$user_num = count($num);
		Log::info(var_export($user_num, true));
		if (isset($user_num)) {
			return Response::json($user_num);
		} else {
			$msg['error'] = Lang::get('slave.userstat_not_found');
			return Response::json($msg, 404);
		}
	}

	 public function getPokerLogWeek()
    {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => ''
        );
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time');
        $sql = "select count(distinct player_id) as num , FROM_UNIXTIME(login_time, '%Y-%m-%d') as date from log_login  where login_time > '{$start_time}' and login_time < '{$end_time}' and is_login = 1  order by date desc";
        $users= DB::connection($this->db_name)->select($sql);
        if (isset($users)) {
                return Response::json($users);
        } else {
                $msg['error'] = Lang::get('slave.userstat_not_found');
                return Response::json($msg, 404);
        }
    }

	public function getPokerDay()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$start_time =Input::get('start_time');
		$end_time = Input::get('end_time');
		$game_id = Input::get('game_id');
		$start_time = date("Y-m-d",$start_time);
		$end_time = date("Y-m-d",$end_time);
		$users =  SlaveCreatePlayer::on($this->db_qiqiwu)->getPokerDay($start_time, $end_time)->get();
		if (isset($users)) {
			return Response::json($users);
		} else {
			$msg['error'] = Lang::get('slave.userstat_not_found');
			return Response::json($msg, 404);
		}
	}

	 public function getPokerRegNew()
     {
        $msg = array(
                'code' => Config::get('errorcode.unknow'),
                'error' => ''
        );
        $start_time = Input::get('start_time');
        $end_time =  Input::get('end_time');
        $game_id = Input::get('game_id');
        //$reg =  SlaveCreatePlayer::on($this->db_qiqiwu)->getPokerRegNew($start_time, $end_time)->get();
        $sql = "select count(distinct player_id) as total_num from users u, create_player cp where u.uid=cp.uid and cp.created_time >= '{$start_time}' and cp.created_time <= '{$end_time}'";
        $reg = DB::connection($this->db_qiqiwu)->select($sql);
        if (isset($reg)) {
                return Response::json($reg);
        } else {
                $msg['error'] = Lang::get('slave.userstat_not_found');
                return Response::json($msg, 404);
        }
    }


        public function getPokerDayData()
        {
                $msg = array(
                        'code' => Config::get('errorcode.unknow'),
                        'error' => ''
                );
                $start_time = Input::get('start_time');
                $end_time = Input::get('end_time');
                $game_id = Input::get('game_id');
                $sql = "select u.uid  from users u left join create_player as cp on u.uid = cp.uid where cp.created_time >= {$start_time} and cp.created_time <= {$end_time}";
                $reg = DB::connection($this->db_qiqiwu)->select($sql);
                if (isset($reg)) {
                        return Response::json($reg);
                } else {
                        $msg['error'] = Lang::get('slave.userstat_not_found');
                        return Response::json($msg, 404);
                }
        }


	public function getPokerDayPay()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$game_id = Input::get('game_id');
		$sql = "select pay_user_id from pay_order where pay_time >= {$start_time} and pay_time <= {$end_time} and get_payment = 1";
		$reg = DB::connection($this->db_payment)->select($sql);
		if (isset($reg)) {
			return Response::json($reg);
		} else {
			$msg['error'] = Lang::get('slave.userstat_not_found');
			return Response::json($msg, 404);
		}
	}

	public function getPokerPlayerEconomyRank()
	{
		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$type = Input::get("type");
		$game_id = Input::get('game_id');
		$reg =  SlaveCreatePlayer::on($this->db_qiqiwu)->getPokerPlayerEconomyRank($game_id, $type)->get();
		if (isset($reg)) {
			return Response::json($reg);
		} else {
			$msg['error'] = Lang::get('slave.userstat_not_found');
			return Response::json($msg, 404);
		}
	} 


	//德州扑克查询兑奖信息
	  public function getPokerCashInfo()
        {
            $msg = array(
                            'code' => Config::get('errorcode.slave_server_economy'),
                            'error' => ''
            );

            $db_qiqiwu = $this->db_qiqiwu;
            $db_payment = $this->db_payment;
            $player_name = Input::get('player_name');
            $player_id = Input::get('player_id');
            $uid = Input::get('uid');
            $start_time = Input::get('start_time');
            $type1 = Input::get('type1');
            $type2 = Input::get('type2');

            $start_time = Input::get('start_time');
            $end_time = Input::get("end_time");

            $str = '';
            if ($type1 > 0) {
                $str .= " and ao.goods_id = {$type1}";
            }
            if ($type2 == 0) {
                $str .=" and ao.status >=0 ";
            }
            elseif ($type2 == 1) {
                $str .=" and ao.status = 0  ";
            }elseif ($type2 == 2) {
                $str .=" and ao.status = 1  ";
            }
            if ($player_name) {
            	$str .= " and player_name = '$player_name'";
            }elseif($player_id){
            	$str .= " and player_id = $player_id";
            }
            $time1=date("Y-m-d H:i:s", $start_time);
            $time2=date("Y-m-d H:i:s", $end_time);
            if($time1>='2015-01-21 23:59:59'){
            	$sql= "select ao.id ,ao.uid, ao.award_amount, ao.get_time, ao.status, ao.goods_id, ao.address_id, ao.domain_name, ao.contact_email, ao.province, ao.city, ao.county, ao.village, ao.address, ao.mobile, ao.name,ao.create_time, cp.player_name from award_order ao left join  create_player cp on cp.uid = ao.uid where ao.create_time >= {$start_time} and ao.create_time <= {$end_time} " . $str ." order by ao.create_time desc";
            	$result = DB::connection($this->db_qiqiwu)->select($sql);
            }elseif($time1<'2015-01-21 23:59:59' && $time2>'2015-01-21 23:59:59'){
                $sql1= "select ao.id ,ao.uid, ao.award_amount, ao.get_time, ao.status, ao.goods_id, ao.address_id, ao.domain_name, ao.contact_email, ao.province, ao.city, ao.county, ao.village, ao.address, ao.mobile, ao.name,ao.create_time, cp.player_name from award_order ao left join  create_player cp on cp.uid = ao.uid where ao.create_time >UNIX_TIMESTAMP('2015-01-21 23:59:59') and ao.create_time <= {$end_time}" . $str ." order by ao.create_time desc";
                $sql2= "select ao.id ,ao.uid, ao.award_amount, ao.get_time, ao.status, ao.goods_id, ao.address_id, ao.domain_name, ua.contact_email, ua.province, ua.city, ua.county, ua.village, ua.address, ua.mobile, ua.name,ao.create_time, cp.player_name from award_order ao left join user_address ua  on ao.uid = ua.uid left join  create_player cp on cp.uid = ao.uid where ao.create_time >{$start_time} and ao.create_time <= UNIX_TIMESTAMP('2015-01-21 23:59:59') " . $str ." order by ao.create_time desc";
           		$result1 = DB::connection($this->db_qiqiwu)->select($sql1);
           		$result2 = DB::connection($this->db_qiqiwu)->select($sql2);
           		$result=array_merge($result1,$result2);
            }else{
            	$sql = "select ao.id ,ao.uid, ao.award_amount, ao.get_time, ao.status, ao.goods_id, ao.address_id, ao.domain_name, ua.contact_email, ua.province, ua.city, ua.county, ua.village, ua.address, ua.mobile, ua.name,ao.create_time, cp.player_name from award_order ao left join user_address ua  on ao.uid = ua.uid left join  create_player cp on cp.uid = ao.uid where ao.create_time >= {$start_time} and ao.create_time <= {$end_time} " . $str ." order by ao.create_time desc";
            	$result = DB::connection($this->db_qiqiwu)->select($sql);
            }
            //$result = DB::connection($this->db_qiqiwu)->select($sql);
 
            if (isset($result)) {
                    return Response::json($result);
            } else {
                    $msg['error'] = Lang::get('slave.userstat_not_found');
                    return Response::json($msg, 404);
            }
        }


    public function getPokerSignData()
    {
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => ''
        );
 
    	$start_time = Input::get('start_time');
    	$end_time = Input::get('end_time');
    	$click_id = Input::get('click_id');
    	$str = "";
    	if ($click_id > 0) {
    		$type = "25" . ($click_id>=10?$click_id:'0'.$click_id);
    		$str = " where operate_id = {$type}";
    	}else{
    		$str = " where operate_id like '25%'";
    	}
    	$sql = "select count(operate_id) as click_num, FROM_UNIXTIME(create_time, '%Y-%m-%d') as date  from user_operate ".$str." and create_time >= '{$start_time}' and create_time < '{$end_time}' group by date";
    	$info = DB::connection($this->db_qiqiwu)->select($sql);
    	if (isset($info)) {
    		return Response::json($info);
    	} else{
    		$msg['error'] = Lang::get('slave.userstat_not_found');
            return Response::json($msg, 404);
    	}
    }
    public function getPlayerIdByUid()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$uid = trim(Input::get('uid'));
		$platform_id = Input::get('platform_id');
		$sql = "select cp.player_id from create_player cp left join users u on cp.uid = u.uid where cp.uid = '{$uid}'";
		$info = DB::connection($this->db_qiqiwu)->select($sql);
		if (isset($info)) {
			return Response::json($info);
		} else{
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}

	public function getPokerGameInfo()
	{
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$uid = Input::get('uid');
		$str = Input::get('str');
		$sql = "select count(log_id) as num ,FROM_UNIXTIME(create_time, '%Y-%m-%d') as date from log_game where time >= '{$start_time}' and time <= '{$end_time}' and players like '%{$uid}' or players like '{$uid}%' or players like '%{$uid}%' group by date";
	}

	/*public function getRechargeUID()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_player_created'),
			'error' => ''
		);
		$count = trim(Input::get('count'));
		$db = DB::connection($this->db_payment);
		$game_id = trim(Input::get('game_id'));
		$user = $db->select("select count(pay_amount * exchange) as total_pay, pay_user_id from pay_order where get_payment = 1 and game_id={$game_id} group by pay_user_id having total_pay > {$count}");
		if ($user) {
			return Response::json($user);
		}else{
			$msg['error'] = Lang::get('slave.slave_result_none');
			return Response::json($msg, 404);
		}
	}*/

	 public function getRechargeUID()
    {
        $money = Input::get('money');
        $game_id = Input::get('game_id');
        $db = DB::connection($this->db_payment);
        $sql="select SUM(o.pay_amount * o.exchange) as total_pay, count(o.order_id) as count, MAX(o.pay_time) as last_pay, MIN(o.pay_time) as first_pay , o.pay_user_id, cp.player_name, cp.player_id  from pay_order o left join {$this->db_qiqiwu}.create_player cp on cp.uid = o.pay_user_id where o.get_payment=1 and game_id={$game_id} group by pay_user_id having total_pay > {$money} order by total_pay desc, count desc";
        /*$user="select SUM(o.pay_amount * o.exchange) as total_pay, o.pay_user_id, cp.player_name, cp.player_id  from pay_order o left join {$this->db_qiqiwu}.create_player cp on cp.uid = o.pay_user_id where o.get_payment=1 and game_id={$game_id} group by pay_user_id having total_pay > {$money}";r =$db->select($sql);*/
        $user = $db->select($sql);
        return Response::json($user);
    }


       public function playerPayData()
        {
        $game_id = Input::get('game_id');
        $start_time = Input::get('start_time');
        $end_time =  Input::get('end_time');
//      $this->db_name="8.2";

        $db = DB::connection($this->db_payment);
        $sql1 = "select SUM(o.pay_amount * o.exchange) as total_dollar, count(o.order_id) as order_num, count(distinct o.pay_user_id) as pay_num, FROM_UNIXTIME(o.pay_time, '%Y-%m-%d') as date  from pay_order o where  o.get_payment = 1 and o.game_id = {$game_id} and o.pay_time >{$start_time} and o.pay_time < {$end_time} group by date";
        //获取总付费和总的付费人数以及付费次数
        $user1 = $db->select($sql1);
        $data = array();
        $login = array();
        foreach ($user1 as $key => $value) {
                $start = strtotime($value->date);
                $end =strtotime($value->date) + 86399;
                $sql = "select distinct o.pay_user_id, FROM_UNIXTIME(o.pay_time, '%Y-%m-%d') as pay_time, u.created_time, u.last_visit_time from pay_order o left join {$this->db_qiqiwu}.users u on u.uid = o.pay_user_id where o.pay_time >{$start} and o.pay_time < {$end} order by o.pay_time desc";
                $user = $db->select($sql);
                $data[] = $user;
                $ss = $start - 7 * 86400;
                $sqll = "select count(distinct o.pay_user_id) as lost_num , FROM_UNIXTIME(o.pay_time ,'%yy-%m-%d') from pay_order  o left join {$this->db_qiqiwu}.users u on u.uid = o.pay_user_id where o.get_payment=1 and UNIX_TIMESTAMP(last_visit_time) < {$ss} ";
                //$login[] = $db->select($sqll)[0];
                $user = $db->select($sqll);
                $login[] = $user[0];
                unset($sql);
                unset($user);
                unset($start);
                unset($end);

        }
        $str = "select distinct pay_user_id from pay_order where get_payment=1 and game_id = {$game_id}";
        $player = $db->select($str);
        $user = array(
                'user1' => $user1,
                'user2' => $data,
                'login' => $login,
                'player' => $player
        );
        return Response::json($user);
    }

    public function queryDelayOrder()
    {

    	 //Log::info('111111111111111111111111111'.$this->db_qiqiwu);
         $db = DB::connection($this->db_payment);
         if(!$db){
                Log::info('no db');
         }
         $is_check = Input::get('is_check');
         $time_now = time();
         $time_1_week_ago = $time_now - 86400*7;
         if($is_check){
         	$sql = "select * from delay_order where deal_status > 0 and deal_time > $time_1_week_ago order by ID desc";
         }else{
         	$sql = "select * from delay_order where deal_status = 0 order by ID desc";
         }
         $result = $db->select($sql);
         if(count($result)>0){
			return Response::json($result);
         }else{
         	return Response::json(array(), 404);
         }
    }
    public function yuanbaoRankSearch()
	{
	    $msg = array(
	        'code' => Config::get('errorcode.slave_order'),
	        'error' => ''
	    );

	    $start_time = Input::get('start_time');
	    $end_time = Input::get('end_time');
	    $game_id = (int)Input::get('game_id');
		$currency_id = (int)Input::get('currency_id');
	
	    $platform_server_id = (int)Input::get('platform_server_id');
	    $platfrom_server = PlatformServer::on($this->db_qiqiwu)->find($platform_server_id);
	    if (!$platfrom_server) {
	        $msg['error'] = Lang::get('error.server_not_found');
	        return Response::json($msg, 403);
	    }
	
		$server_internal_id = (int)Input::get('server_internal_id');
	    $order = PayOrder::on($this->db_payment)
	    ->yuanbaoSearch($this->db_qiqiwu, $start_time, $end_time, $currency_id, $platform_server_id, $server_internal_id, $this->db_name, $game_id)
	    ->get();
 	    // $s = var_export($order,true);
 	    // Log::info($s);
	    if ($order) {
	        $result = array(
			'items' => $order->toArray(),
		);
	        return $result;
	    } else {
	        return Response::json(array(), 404);
	    }
	}


	public function getyysggiftbagnum(){ //slave端夜夜三国查询礼包销量SQL
		$msg = array(
	        'code' => Config::get('errorcode.slave_order'),
	        'error' => ''
	    );
	    $start_time = Input::get('start_time');
	    $end_time = Input::get('end_time');
	    $game_id = (int)Input::get('game_id');
        $server_id = (int)Input::get('server_id');
        $gift_bag_id = (int)Input::get('gift_bag_id');


        if('0' == $server_id){	//代表查询所有服务器
        	if('0' == $gift_bag_id){	//代表查询所有礼包
        		$order = PayOrder::on($this->db_payment)
        						->selectRaw('*,count(1) as giftbag_num')
				        		->where('game_id',$game_id)
				        		->where('get_payment','>',0)
				        		->where('pay_time','>=',$start_time)
				        		->where('pay_time','<=',$end_time)
				        		->where('giftbag_id','>',0)
				        		->groupBy('giftbag_id','server_id')
				        		->get();
        	}else{	//代表查询单个礼包
        		$order = PayOrder::on($this->db_payment)
        						->selectRaw('*,count(1) as giftbag_num')
				        		->where('game_id',$game_id)
				        		->where('giftbag_id',$gift_bag_id)
				        		->where('get_payment','>',0)
				        		->where('pay_time','>=',$start_time)
				        		->where('pay_time','<=',$end_time)
				        		->groupBy('server_id')
				        		->get();
        	}
        }else{	//代表查询单个服务器
        	if('0' == $gift_bag_id){	//代表查询所有礼包
        		$order = PayOrder::on($this->db_payment)
        						->selectRaw('*,count(1) as giftbag_num')
				        		->where('game_id',$game_id)
				        		->where('server_id',$server_id)
				        		->where('get_payment','>',0)
				        		->where('pay_time','>=',$start_time)
				        		->where('pay_time','<=',$end_time)
				        		->where('giftbag_id','>',0)
				        		->groupBy('giftbag_id')
				        		->get();
        	}else{	//代表查询单个礼包
        		$order = PayOrder::on($this->db_payment)
        						->selectRaw('*,count(1) as giftbag_num')
				        		->where('game_id',$game_id)
				        		->where('server_id',$server_id)
				        		->where('giftbag_id',$gift_bag_id)
				        		->where('get_payment','>',0)
				        		->where('pay_time','>=',$start_time)
				        		->where('pay_time','<=',$end_time)
				        		->get();
        	}
        }

        if ($order) {
	        return $order;
	    } else {
	        return Response::json(array(), 404);
	    }
	}

	public function getyysgmonetarynum(){	//slave端夜夜三国查询货币消耗SQL
		$msg = array(
	        'code' => Config::get('errorcode.slave_order'),
	        'error' => ''
	    );
	    $start_time = Input::get('start_time');
	    $end_time = Input::get('end_time');
        $monetary_type = (int)Input::get('monetary_type');

        switch ($monetary_type) {
        	case '0': 	//元宝或称钻石
 		        $order = EconomyLog::on($this->db_name)
		        					->selectRaw('SUM(diff_crystal) as monetary_num')
		        					->where('created_at','>=',$start_time)
		        					->where('created_at','<=',$end_time)
		        					->where('diff_crystal','<',0)
		        					->get();       		
        		break;
        	case '1':	//铜钱或称金币
 		        $order = EconomyLog::on($this->db_name)
		        					->selectRaw('SUM(diff_mana) as monetary_num')
		        					->where('created_at','>=',$start_time)
		        					->where('created_at','<=',$end_time)
		        					->where('diff_mana','<',0)
		        					->get();    
        		break;
        	case '2':	//体力
 		        $order = EconomyLog::on($this->db_name)
		        					->selectRaw('SUM(diff_energy) as monetary_num')
		        					->where('created_at','>=',$start_time)
		        					->where('created_at','<=',$end_time)
		        					->where('diff_energy','<',0)
		        					->get();    
        		break;
        	default:
        		break;
        }

        if ($order) {
	        return $order;
	    } else {
	        return Response::json(array(), 404);
	    }
	}

	public function getgamepackage(){
		$msg = array(
	        'code' => Config::get('errorcode.slave_order'),
	        'error' => ''
	    );		
	    $game_id = Input::get('game_id');
	    $platform_id = Input::get('platform_id');
	    $package_id = Input::get('package_id');
	    $db = DB::connection($this->db_qiqiwu);

	    if($package_id){
	    	$result = $db->table('game_package')->find($package_id);
	    }else{
	    	$result = $db->table('game_package')->where('game_id', $game_id)->get();
	    }
	    
	    if ($result) {
	    	return Response::json($result);
	    }else{
	    	return Response::json($msg, 404);
	    }
	}

/*
 *Get Google Validate Infomation
 */
	public function ggvalidateInfo()
	{
		$msg = array(
	        			'code' => Config::get('errorcode.slave_order'),
	        			'error' => ''
	   				);	
		$game_id = Input::get('game_id');
		$platform_id = Input::get('platform_id');
		$package_id = Input::get('package_id');
		$db = DB::connection($this->db_qiqiwu);

		if($package_id)		
			$result = $db->table('google_validate')->find($package_id);
		else
			$result = $db->table('google_validate')->where('game_id',$game_id)->get();
		
		if($result)
			return Response::json($result);
		else
			return Response::json($msg, 404);
	}

//third_product modify & add
	public function thirdproductData()
	{
		$msg = array(
	        			'code' => Config::get('errorcode.slave_order'),
	        			'error' => ''
	   				);
		$id = Input::get('id');
		$game_id = Input::get('game_id');
		$db = DB::connection($this->db_payment);
		if($id)
		{
			$result = $db->table('third_product')->find($id);
		}
		else
		{	
		    $result = $db->table('third_product')->where('game_id', $game_id)->get();
	    }
		if($result)
			return Response::json($result);
		else
			return Response::json($msg,404);
	}
	public function thirdproductUpdate()
	{
		$msg = array(
	        			'code' => Config::get('errorcode.slave_order'),
	        			'error' => ''
	   				);
		$id = Input::get('id');
		$data = Input::get('data');
		$db = DB::connection($this->db_payment);
		if(!$id)
		{
			$result = $db->table('third_product')->insert($data);
		}
		else
		{	
			unset($data['id']);
		    $result = $db->table('third_product')->where('id',$id)->update($data);
	    }
		if($result)
			return Response::json($result);
		else
			return Response::json($msg,404);
	}

	public function getFirstPayInfo(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => '没有数据'
        );
        $game_id = Input::get('game_id');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time'); 

        $result = array(
        	'lessthan3'	=>	array(
        		'name'	=>	'<3',
        		'value'	=>	0,
        		'num'	=>	0,
        		),
        	'between3and15'	=>	array(
        		'name'	=>	'3-15',
        		'value'	=>	0,
        		'num'	=>	0,
        		),
        	'between15and30'	=>	array(
        		'name'	=>	'15-30',
        		'value'	=>	0,
        		'num'	=>	0,
        		),
        	'largerthan30'	=>	array(
        		'name'	=>	'>30',
        		'value'	=>	0,
        		'num'	=>	0,
        		),
        	);

        $tmp_result = PayOrder::on($this->db_payment)
        					->getFirstPayInfo($game_id, $start_time, $end_time)
        					->get();

       	foreach ($tmp_result as $value) {
       		if($value->pay_dollar > 30){
       			$result['largerthan30']['value']++;
       			if($value->pay_num > 1){
       				$result['largerthan30']['num']++;
       			}
       		}elseif($value->pay_dollar > 15){
       			$result['between15and30']['value']++;
       			if($value->pay_num > 1){
       				$result['between15and30']['num']++;
       			}
       		}elseif($value->pay_dollar > 3){
       			$result['between3and15']['value']++;
       			if($value->pay_num > 1){
       				$result['between3and15']['num']++;
       			}
       		}else{
       			$result['lessthan3']['value']++;
       			if($value->pay_num > 1){
       				$result['lessthan3']['num']++;
       			}
       		}
       	}

       	$response = array(
       			'first_pay'	=> $result,
       		);
       	unset($result);
       	unset($tmp_result);
		$tmp_result_all = PayOrder::on($this->db_payment)
        					->getSignUpTimeGroup($this->db_qiqiwu, $game_id, $start_time, $end_time, 'all')
        					->get();
        // Log::info(var_export($tmp_result_all, true));
        $tmp_result_new = PayOrder::on($this->db_payment)
        					->getSignUpTimeGroup($this->db_qiqiwu, $game_id, $start_time, $end_time, 'new')
        					->get();
        $result = array();
        if(isset($tmp_result_all[0])){
        	if(isset($tmp_result_new[0])){
        		$result = array(
        			'all_player_num' => $tmp_result_all[0]->pay_num,
        			'all_dollar' => $tmp_result_all[0]->sum_dollar,
        			'new_player_num' => $tmp_result_new[0]->pay_num,
        			'new_dollar' => $tmp_result_new[0]->sum_dollar,
        			'old_player_num' => $tmp_result_all[0]->pay_num - $tmp_result_new[0]->pay_num,
        			'old_dollar' => $tmp_result_all[0]->sum_dollar - $tmp_result_new[0]->sum_dollar,
        			);
        	}
        }
        $response['pay_newer'] = $result;
        
        unset($result);

       	if ($response) {
	    	return Response::json($response);
	    }else{
	    	return Response::json($msg, 404);
	    }
    }

    public function getAmountInfo(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => '没有数据'
        );
        $game_id = Input::get('game_id');
        $start_time = Input::get('start_time');
        $end_time = Input::get('end_time'); 

        $tmp_result = PayOrder::on($this->db_payment)
        					->getAmountInfo($game_id, $start_time, $end_time)
        					->get();

		if (count($tmp_result) == 0){
			return Response::json($msg, 404);
		}
        $tmp_array = array();
        $most_amount = array();
        $time_group = array();
        $time_group['10'] = array(
        	'name'	=>	'>=10',
        	'num'	=>	0,
        	);
        foreach ($tmp_result as $value) {
        	$tmp_array[] = $value->sum_dollar;
        	if(isset($most_amount[round($value->sum_dollar, 1).''])){
        		$most_amount[round($value->sum_dollar, 1).'']++;
        	}else{
        		$most_amount[round($value->sum_dollar, 1).''] = 1;
        	}

        	if($value->pay_num >= 10){
        		$time_group['10']['num']++;
        	}else{
	        	if (isset($time_group[$value->pay_num.''])) {
	        		$time_group[$value->pay_num]['num']++;
	        	}else{
	        		$time_group[$value->pay_num.''] = array(
	        			'name'	=>	$value->pay_num.'',
	        			'num'	=>	1,
	        			);
	        	}
        	}
        }
        unset($value);
        unset($tmp_result);
        arsort($most_amount);
        foreach ($most_amount as $key => $value) {
        	$most_dollar = $key;
        	break;
        }
        unset($most_amount);

        $all_dollar = array_sum($tmp_array);
        $player_num = count($tmp_array);
        foreach ($time_group as &$val) {
        	$val['rate'] = round($val['num']/$player_num*100, 2).'%';
        }

        $avg_dollar = round($all_dollar/$player_num, 2);
        $mid_dollar = $tmp_array[round(count($tmp_array)/2-0.1, 0)];
        $arppu = array(
        	'avg_dollar'	=>	array('name' => '平均数', 'value' => $avg_dollar),
        	'mid_dollar'	=>	array('name' => '中位数', 'value' => $mid_dollar),
        	'most_dollar'	=>	array('name' => '众数', 'value' => $most_dollar),
        	);

        $toptenpercent = round(count($tmp_array)/10 + 0.49);
        $top50percent = round(count($tmp_array)/2 + 0.49);

        $devide_parts = array(
        	'10%'	=>	array('name' => '前10%的玩家', 'num' => 0, 'value' => 0, 'rate' => ''),
        	'40%'	=>	array('name' => '10%~40%的玩家', 'num' => 0, 'value' => 0, 'rate' => ''),
        	'50%'	=>	array('name' => '后50%的玩家', 'num' => 0, 'value' => 0, 'rate' => ''),
        	);
        foreach ($tmp_array as $key => $value) {
        	if ($key < $toptenpercent) {
        		$devide_parts['10%']['value'] += $value;
        		$devide_parts['10%']['num'] ++;
        	}elseif($key < $top50percent){
        		$devide_parts['40%']['value'] += $value;
        		$devide_parts['40%']['num'] ++;
        	}else{
        		$devide_parts['50%']['value'] += $value;
        		$devide_parts['50%']['num'] ++;
        	}
        }
        unset($value);
        foreach ($devide_parts as &$devide_part) {
        	$devide_part['rate'] = round($devide_part['value']/$all_dollar*100, 2).'%';
        }
        unset($tmp_array);
        
        $result = array(
        	'arppu'	=>	$arppu,
        	'devide_parts'	=>	$devide_parts,
        	'time_group'	=>	$time_group,
        	);

	    return Response::json($result);
    }

    public function getPayTrendInfo(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => '没有数据'
        );
    	$start_time = Input::get('start_time');
    	$end_time = Input::get('end_time');
    	$game_id = Input::get('game_id');
    	$platform_id = Input::get('platform_id');
    	$interval = Input::get('interval');
    	$time_offset = $start_time;

    	$tmp_result = PayOrder::on($this->db_payment)->getPayTrendInfo($start_time, $end_time, $game_id, $interval)->get();
    	if(count($tmp_result)){
    		$result = array();
    		while ($time_offset <= $end_time) {
    			$result[($time_offset-$start_time)/$interval] = array(
					'time' => date("m-d H:i", $time_offset),
					'all_user' => 0,
					'all_times' => 0,
					'all_dollar' => 0,
					'pay_user' => 0,
					'pay_times' => 0,
					'pay_dollar' => 0,
					);
	    		$time_offset += $interval;
    		}
    		foreach ($tmp_result as $value) {
    			unset($result[($value->time-$start_time)/$interval]);
				$result[($value->time-$start_time)/$interval] = array(
					'time' => date("m-d H:i", $value->time),
					'all_user' => $value->all_user,
					'all_times' => $value->all_times,
					'all_dollar' => round($value->all_dollar, 2),
					'pay_user' => $value->pay_user,
					'pay_times' => $value->pay_times,
					'pay_dollar' => round($value->pay_dollar, 2),
					);
	    	}
	    	return Response::json($result);
    	}else{
    		return Response::json($msg, 404);
    	}
    }

    public function getFirstPayAnalysis(){
    	$msg = array(
            'code' => Config::get('errorcode.slave_server_economy'),
            'error' => '没有数据'
        );

    	$game_id = Input::get('game_id');
    	$platform_id = Input::get('platform_id');

    	$start_time_tmp = Input::get('start_time');
    	$end_time = Input::get('end_time');
    	$start_time = strtotime(date('Y-m-d', $start_time_tmp));
    	$end_time = $end_time + ($start_time - $start_time_tmp);

    	$result = array();

    	$db = DB::connection($this->db_payment);
    	$i = 0;	//直接定义下标方便排序和后续处理
    	for($start_time_tmp=$start_time; $start_time_tmp < $end_time; $start_time_tmp+=86400){	//以天为单位统计
    		$end_time_tmp = $start_time_tmp + 86400;
    		$firstpayAll = $db->select("
    									select count(tmp.pay_user_id) as allnum from 
    									(select o.pay_user_id,min(o.pay_time) as pay_time from pay_order o where get_payment = 1 and game_id = $game_id group by o.pay_user_id) as tmp
    									where tmp.pay_time between $start_time_tmp and $end_time_tmp
    									");	//得到所有首冲
    		$firstpaynewer = $db->select("
    									select count(distinct(u.uid)) as newnum from pay_order o join `$this->db_qiqiwu`.users u on o.pay_user_id = u.uid where o.get_payment = 1 and o.game_id = $game_id
    									and u.created_time between FROM_UNIXTIME($start_time_tmp) and FROM_UNIXTIME($end_time_tmp) 
    									and o.pay_time between $start_time_tmp and $end_time_tmp
    									");	//得到所有当日注册的首冲，称为新增付费玩家
    		$result[$i] = array(
    			'start_time'	=>	$start_time_tmp,
    			'end_time'	=>	$end_time_tmp,
    			'allnum'	=>	isset($firstpayAll[0]->allnum) ? $firstpayAll[0]->allnum : 0,
    			'newnum'	=>	isset($firstpaynewer[0]->newnum) ? $firstpaynewer[0]->newnum : 0,
    			);

    		$result[$i]['oldnum']	= $result[$i]['allnum'] - $result[$i]['newnum'];

    		unset($firstpayAll);
    		unset($firstpaynewer);

    		$i ++;
    	}

    	if(isset($result)){
			return Response::json($result);
    	}else{
    		return Response::json($msg, 404);
    	}
    }

   public function getConsumptionRank(){
   		$start_time = (int)Input::get('start_time');
	    $end_time = (int)Input::get('end_time');
	    $game_id = (int)Input::get('game_id');
	    $interval = (int)Input::get('interval');
	    $currency_id = (int)Input::get('currency_id');
	    $rank = (int)Input::get('rank');
	    $platform_server_id = (int)Input::get('platform_server_id');
	    $server_internal_id = (int)Input::get('server_internal_id');
	    $db_qiqiwu = $this->db_qiqiwu;
	    $result = array();
	    if(0 == $interval){//按天查询
	        $inter = ($end_time - $start_time)/86400;
	        $days = intval(ceil($inter));
	        for ($i=0; $i < $days; $i++) {
	            $start_time1 = date("Y-m-d 00:00:00", $start_time);
	            $start_time2 = strtotime($start_time1);
	            $start_time2 = $start_time2 + 86400*$i;
	            $this_start_time = strtotime(date("Y-m-d", $start_time2));
	            $this_end_time = $start_time2+86399;
	            if(0 == $i){
	            	$this_start_time = $start_time;
	            }
	            if($days-1 == $i){
	            	$this_end_time = $end_time;
	            }
	            $temp_result = PayOrder::on($this->db_payment)
	            ->getConsumptionRank($db_qiqiwu, $this_start_time, $this_end_time, $interval, $currency_id, $game_id, 
	            	$rank, $platform_server_id ,$server_internal_id)->get();
	            $result[] = json_decode($temp_result);
	            unset($temp_result);
	        }
	         //Log::info(var_export($result,true));die();
	    }elseif(1 == $interval){//周
	        $w_day=date("w",$start_time);
	    	if($w_day=='1'){
	    		$cflag = '+0';
	    	}else{
	    		$cflag = '-1';
	    	}
	       $start = strtotime(date('Y-m-d',strtotime("$cflag week Monday", $start_time)));  //取得开始时间所在自然周的开始时间
	       $inter = ($end_time - $start)/(86400*7);
	       $weeks = intval(ceil($inter));
	       for($i=0; $i<$weeks; $i++){
	           $this_start_time = $start + (86400*7)*$i;
	           $this_end_time = $this_start_time+(86400*7-1); 
	           if(0 == $i){
	            	$this_start_time = $start_time;
	           }
	           if($weeks-1 == $i){
	            	$this_end_time = $end_time;
	           }

	           $temp_result = PayOrder::on($this->db_payment)
	           ->getConsumptionRank($db_qiqiwu, $this_start_time, $this_end_time, $interval, $currency_id, $game_id, 
	           	$rank, $platform_server_id ,$server_internal_id)->get();
	           $result[] = json_decode($temp_result);
	           unset($temp_result);
	       }
	    }elseif(2 == $interval){//月
	    	$firstdate = date("Y/m/01",$start_time);
	    	$firstday = strtotime(date("Y/m/01",$start_time));//start_time所在月第一天
	    	$this_start_time = $firstday;
	    	$this_end_time = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdate +1 month -1 day")));//start_time所在月最后一天
	    	for($this_start_time; $this_start_time<$end_time; $this_start_time++){
	           $firstdate = date("Y/m/01",$this_start_time);
	           $this_end_time = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdate +1 month -1 day")));
	           if($start_time > $this_start_time){//第一个月开始查询时间
	            	$this_start_time = $start_time;
	           }
	           if($end_time < $this_end_time){//最后一个月的结束时间
	            	$this_end_time = $end_time;
	           }

	           $temp_result = PayOrder::on($this->db_payment)
	           ->getConsumptionRank($db_qiqiwu, $this_start_time, $this_end_time, $interval, $currency_id, $game_id, 
	           	$rank, $platform_server_id ,$server_internal_id)->get();
	           $result[] = json_decode($temp_result);
	           unset($temp_result);

	           $this_start_time = $this_end_time+1;//下个月1号0点
	       }

	    }elseif(3 == $interval){
	    	$temp_result = PayOrder::on($this->db_payment)
	    	->getConsumptionRank($db_qiqiwu, $start_time, $end_time, $interval, $currency_id, $game_id, 
	    		$rank, $platform_server_id ,$server_internal_id)->get();
	    	$result[] = json_decode($temp_result);
	    	unset($temp_result);
	    }

		if($result){
			return Response::json($result);
		}else{
			
		}
   }

   public function getPokerPayNum(){
   		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);
		$start_time =Input::get('start_time');
		$end_time = Input::get('end_time');
		$game_id = Input::get('game_id');
		
		$sql = "select count(distinct o.pay_user_id) as paynum from `{$this->db_name}`.log_create_player lcp 
				join (select distinct player_id from `{$this->db_name}`.log_login where login_time between $start_time and $end_time) a on lcp.player_id = a.player_id 
				join `{$this->db_payment}`.pay_order o on lcp.user_id = o.pay_user_id and o.get_payment = 1";
		$users =  DB::connection($this->db_name)->select($sql);
		if (count($users) > 0) {
			return Response::json($users);
		} else {
			$msg['error'] = Lang::get('slave.userstat_not_found');
			return Response::json($msg, 404);
		}
   }

   public function getAllPaymethods(){
   		$msg = array(
			'code' => Config::get('errorcode.unknow'),
			'error' => ''
		);

		$game_id = (int)Input::get('game_id');	
		$mobile_game = (int)Input::get('mobile_game');
		$db = DB::connection($this->db_payment);
		if($mobile_game){
			$table_name = 'mobile_payment_method';
		}else{
			$table_name = 'payment_method';
		}

		$result['pay_type'] = $db->table('pay_type as pt')
					 ->Join($table_name.' as mpm', 'pt.pay_type_id', '=', 'mpm.pay_type_id')
					 ->selectRaw("pt.pay_type_id, pt.pay_type_name, mpm.method_id, mpm.method_name")
					 ->get();

		$result['currency'] = $db->table('currency')->get();

		if (count($result['pay_type']) > 0) {
			return Response::json($result);
		} else {
			$msg['error'] = Lang::get('slave.no_payment_method');
			return Response::json($msg, 404);
		}
   }

   public function getAllGameProducts(){
   		$game_id = Input::get('game_id');
   		$pay_type_id = Input::get('pay_type_id');
   		$method_id = Input::get('method_id');
   		$product_type = Input::get('product_type');
   		$currency_id = Input::get('currency_id');
   		$mobile_game = (int)Input::get('mobile_game');

   		$db = DB::connection($this->db_payment);
   		$result = array();

   		if('giftbag' == $product_type && $mobile_game){	//仅有手游有礼包信息，在giftbag_list表中
	   		$tmp_result = $db->table('giftbag_list as gl')->join('gift_price_list as gpl', function($join) {
		   						$join->on('gl.price', '=', 'gpl.price_amount')
		   							 ->on('gl.currency', '=', 'gpl.price_currency_id');})
	   						->join('currency as cu', 'gpl.currency', '=', 'cu.currency_id')
	   						->where('gl.game_id', $game_id);

	   		if($pay_type_id){
	   			$tmp_result->where('gpl.pay_type_id', $pay_type_id);
	   			if($method_id){
	   				$tmp_result->where('gpl.method_id', $method_id);
	   			}
	   		}

	   		if($currency_id){
	   			$tmp_result->where('gpl.currency', $currency_id);
	   		}

	   		$tmp_result->selectRaw("gl.giftbag_id, gl.giftbag_name, if(gl.is_use=1, '是','否') as is_use, gpl.amount, cu.currency_name");

	   		$result = $tmp_result->get();
	   	}

	   	if('yuanbao' == $product_type){	//元宝信息
	   		if($mobile_game){	//手游元宝信息在game_product中的token
		   		$tmp_result = $db->table('game_product as gp')
		   						->join('game_product_price as gpp', 'gp.product_id', '=', 'gpp.product_id')
		   						->join('mobile_payment_method as mpm', 'gpp.payment_id', '=', 'mpm.payment_id')
		   						->join('currency as cu', 'gpp.currency_id', '=', 'cu.currency_id')
		   						->where('gp.product_type','token')
		   						->where('gp.game_id', $game_id);

		   		if($pay_type_id){
		   			$tmp_result->where('mpm.pay_type_id', $pay_type_id);
		   			if($method_id){
		   				$tmp_result->where('mpm.method_id', $method_id);
		   			}
		   		}
		   		if($currency_id){
		   			$tmp_result->where('gpp.currency_id', $currency_id);
		   		}
		   		$tmp_result->selectRaw("gp.product_id as pay_amount_id, gp.product_info as yuanbao_huodong, gpp.pay_amount, cu.currency_name");

		   		$result = $tmp_result->get();
	   		}else{
		   		$tmp_result = $db->table('pay_amount as pa')
		   						->join('currency as cu', 'pa.currency_id', '=', 'cu.currency_id')
		   						->where('pa.game_id', $game_id);

		   		if($pay_type_id){
		   			$tmp_result->where('pa.pay_type_id', $pay_type_id);
		   			if($method_id){
		   				$tmp_result->where('pa.method_id', $method_id);
		   			}
		   		}
		   		if($currency_id){
		   			$tmp_result->where('pa.currency_id', $currency_id);
		   		}

		   		$tmp_result->selectRaw("pa.pay_amount_id , pa.yuanbao_huodong, pa.pay_amount, cu.currency_name");

		   		$result = $tmp_result->get();
	   		}
	   	}

   		if(count($result)){
   			return Response::json($result);
   		}else{
			$msg['error'] = Lang::get('slave.no_giftbag_message');
			return Response::json($msg, 404);
		}
   }

   public function getfilterorders(){
        $filter_data = array(
            'filter_type' => '',
            'by_pay_time' => '',
            'by_reg_time' => '',
            'by_last_login_time' => '',
            'by_dollar_amount' => '',
            'by_yuanbao_amount' => '',
            'reg_start_time' => '',
            'reg_end_time' => '',
            'pay_start_time' => '',
            'pay_end_time' => '',
            'last_login_time' => '',
            'yuanbao_amount' => '',
            'dollar_amount' => '',
            'game_id' => '',
        );
        $special_time = array('reg_start_time', 'reg_end_time', 'last_login_time');
        foreach ($filter_data as $key => $value) {
        	if(in_array($key, $special_time)){
        		$filter_data[$key] = date('Y-m-d H:i:s', Input::get($key));
        	}else{
            	$filter_data[$key] = Input::get($key);
            }
        }
        $result = array();

        if('all' != $filter_data['filter_type']){
        	$all_keys = array(	//因为null值造成的一些判断问题，所以我们把相关的值全部获取，显示的时候把空值显示为某些特殊符号
        		'player_id', 'server_name', 'last_visit_time', 'pay_user_id', 'pay_num', 'order_sn', 'sum_dollar_amount', 'sum_yuanbao_amount', 
        	);
        	PayOrder::on($this->db_payment)
				->getFilterOrders($this->db_qiqiwu, $filter_data)
				->chunk(2000, function($tmp_result) use (&$result, $all_keys){
					foreach ($tmp_result as $value) {
						$tmp = array();
						foreach ($all_keys as $key_name) {
							$tmp[$key_name] = $value[$key_name];
						}
						if(count($tmp)){
							$result[] = $tmp;
						}
						unset($tmp);
					}
					unset($tmp_result);
				});
        }else{
        	$result = PayOrder::on($this->db_payment)
				->getFilterOrders($this->db_qiqiwu, $filter_data)->get();
        }

        if(count($result)){
   			return Response::json($result);
   		}else{
			return Response::json(array(), 404);
		}
   }

   public function getPayDollarByPlayerId(){	//获得单个玩家一段时间内的所有充值美金总数以及充值元宝数 
   		$start_time = (int)Input::get('start_time');
   		$end_time = (int)Input::get('end_time');
   		$game_id = (int)Input::get('game_id');
   		$player_id = (int)Input::get('player_id');
   		$server_internal_ids = Input::get('server_internal_ids');
   		$pay_user_id = (int)Input::get('pay_user_id');

   		$test = SlaveCreatePlayer::on($this->db_qiqiwu)->first();
   		$has_game_id = isset($test->game_id);
   		if($has_game_id){
   			$player = SlaveCreatePlayer::on($this->db_qiqiwu)->where('player_id', $player_id)->where('game_id', $game_id);
   		}else{
   			$player = SlaveCreatePlayer::on($this->db_qiqiwu)->where('player_id', $player_id);
   		}
   		if(is_array($server_internal_ids) && count($server_internal_ids) && array_sum($server_internal_ids)){
   			$player = $player->whereIn('server_id', $server_internal_ids)->first();
   		}else{
   			$player = $player->first();
   		}

   		$pay_user_id = isset($player->uid) ?  $player->uid : $pay_user_id;
   		$server_internal_id = isset($player->server_id) ? $player->server_id : (isset($server_internal_ids[0]) ? $server_internal_ids[0] : 0);
   		$player_name = isset($player->player_name) ?  $player->player_name : '-';

   		if($pay_user_id){
			$result = PayOrder::on($this->db_payment)
				->where('game_id', $game_id)
				->whereBetween('pay_time', array($start_time, $end_time))
				->where('get_payment', 1)
				->where('pay_user_id', $pay_user_id)
				->selectRaw("sum(pay_amount*exchange) as all_dollar, sum(yuanbao_amount) as all_yuanbao")
				->first();
	   		$result = array(
	   			'uid' => $pay_user_id,
	   			'all_dollar' => $result->all_dollar,
	   			'all_yuanbao' => $result->all_yuanbao,
	   			'server_internal_id' => $server_internal_id,
	   			'player_name' => $player_name,
	   			);
   		}else{
   			$result = array(
   				'pay_user_id' => $pay_user_id,
	   			'all_dollar' => 0,
	   			'all_yuanbao' => 0,
	   			'server_internal_id' => $server_internal_id,
	   			'player_name' => $player_name,
	   		);
   		}


   		return Response::json($result); 
   }

   public function getPaymentMethodActivity(){
   		$game_id = Input::get('game_id');

   		$test = DB::connection($this->db_payment)->select("show tables like 'payment_activity'");
   		if(count($test)){
	   		$db = DB::connection($this->db_payment)->table('payment_activity as pa')
	   							->join("payment_method as pm", 'pa.payment_method_id', '=', 'pm.id');
	   		if($game_id){
	   			$db = $db->where('pa.game_id', $game_id);
	   		}

	   		$result = $db->selectRaw("pa.payment_method_id, pm.method_name, FROM_UNIXTIME(pa.start_time) as start_time, FROM_UNIXTIME(pa.end_time) as end_time, pa.huodong_rate")->get();
   		}else{
   			$result = DB::connection($this->db_payment)->table('payment_method as pm')
   			                    ->selectRaw("pm.id as payment_method_id, pm.method_name, FROM_UNIXTIME(pm.start_time) as start_time, FROM_UNIXTIME(pm.end_time) as end_time, pm.huodong_rate")->get();
   		}

   		if(count($result)){
   			return Response::json($result);
   		}else{
   			return Response::json(array(), 404);
   		}
   }
}