<?php

class SlavePokerDataController extends \SlaveServerBaseController{

	//从服务器上取得德州扑克数据，独立发送
	public function getPokerDataDailyFromSlave()
	{
		Log::info('pokerDataDaily---test----Position----game_id');
		$game_id = Input::get('game_id');
		$platform_id = Input::get('platform_id');
		$platform_server_id = Input::get('platform_server_id');
		$from_email = Input::get('from_email');
		$email_to = Input::get('email_to');
		$email_subject = Input::get('email_subject');

		$api_server_ip = "119.81.84.118";
		$api_server_port = "9527";
		//特殊处理
		if($this->db_name=='52.1'){
			$this->db_name = '11.1';
			$this->db_qiqiwu = 'qiqiwu_10';
			$this->db_payment = 'payment_10';
			$this->setDB();
		}

		$db_name = DB::connection($this->db_name);
		$db_qiqiwu = DB::connection($this->db_qiqiwu);
		$db_payment = DB::connection($this->db_payment);

		$table_yonghu = array();
		$table_chuzhi = array();
		$table_jingji = array();
		$table_mingxi = array();

		$today_begin_time = strtotime(date("Y-m-d 00:00:00"));//运行当天
		$from_day = $today_begin_time - 1*24*3600;//前1天，共计算3天
		for ($tmp_time=$from_day; $tmp_time >= $today_begin_time - 3*24*3600; $tmp_time-=24*3600)   //循环3次
		{
			Log::info('pokerDataDaily---test----Position----start_time');
			$start_time = $tmp_time;
			$end_time = $tmp_time + 24*3600-1;  //从00:00:00到23:59:59
			$warning_info = '';
			/////////////////////////四个表格的数据获取//////////////////////
			//***************table 用户**********************
			/////////////////////////////////////////////////
			$table_yonghu_day = array();

			//日期
			$table_yonghu_day['date'] = date("Y-m-d", $start_time);
			//qiqiwu 网站登录人数
			$web_login_sql = "select website_log as web_login
								from daily_visit
								where date_log between $start_time and $end_time";
       		$web_login = $db_name->select($web_login_sql);
       		if(empty($web_login)){
       			$table_yonghu_day['web_login'] = -1;
       		}else{
       			$web_login = reset($web_login);
       			$table_yonghu_day['web_login'] = $web_login->web_login;
       		}
       		Log::info('pokerDataDaily---test----Position----game_login_sql');
			//log 游戏登陆人数
			if('11' == $game_id){
				$game_login_sql = "select count(distinct player_id) as game_login
								from log_login 
								where operator_id = 0 
								and login_time between $start_time and $end_time";
			}else{
				$game_login_sql = "select count(distinct player_id) as game_login
								from log_login 
								where operator_id between 1 and 2 
								and login_time between $start_time and $end_time";
			}
       		$game_login = $db_name->select($game_login_sql);
       		if(empty($game_login)){
       			$table_yonghu_day['game_login'] = -1;
       		}else{
       			$game_login = reset($game_login);
       			$table_yonghu_day['game_login'] = $game_login->game_login;
       		}
			//log qiqiwu 匿名游戏登陆人数
			$sub_sql = "";
			if('11' == $game_id){
				$sub_sql = " and ll.operator_id = 0";
			}else{
				$sub_sql = " and ll.operator_id between 1 and 2";
			}
			Log::info('pokerDataDaily---test----Position----is_anoy_login_sql');
			$is_anoy_login_sql = "select count(distinct ll.player_id) as is_anoy_login
								from log_login as ll
								left join log_create_player as lcp 
								on ll.player_id = lcp.player_id
								left join `$this->db_qiqiwu`.users as u
								on lcp.user_id = u.uid
								where u.still_anonymous=1
								and ll.login_time between $start_time and $end_time ".$sub_sql;
       		$is_anoy_login = $db_name->select($is_anoy_login_sql);
       		if(empty($is_anoy_login)){
       			$table_yonghu_day['is_anoy_login'] = -1;
       		}else{
       			$is_anoy_login = reset($is_anoy_login);
       			$table_yonghu_day['is_anoy_login'] = $is_anoy_login->is_anoy_login;
       		}
			//log

			$sub_sql = "";
			if('11' == $game_id){
				$sub_sql = " and operator_id = 0";
			}else{
				$sub_sql = " and operator_id between 1 and 2";
			}
			Log::info('pokerDataDaily---test----Position----play_game_sql');
			$play_game_sql = "select count(distinct player_id) as play_game
    							from log_economy where action_type = 'endOneRound' 
    							and action_time between $start_time and $end_time ".$sub_sql;
       		$play_game = $db_name->select($play_game_sql);
       		if(empty($play_game)){
       			$table_yonghu_day['play_game'] = -1;
       		}else{
       			$play_game = reset($play_game);
       			$table_yonghu_day['play_game'] = $play_game->play_game;
       		}
    		//log

			$sub_sql = "";
			if('11' == $game_id){
				$sub_sql = " and ll.operator_id = 0";
			}else{
				$sub_sql = " and ll.operator_id between 1 and 2";
			}
			Log::info('pokerDataDaily---test----Position----old_game_login_sql');
    		$old_game_login_sql = "select count(distinct ll.player_id) as old_game_login
    							from log_login as ll
    							left join log_create_player as lcp
    							on ll.player_id = lcp.player_id
    							where ll.login_time between $start_time and $end_time
    							and lcp.created_time < $start_time ".$sub_sql;
       		$old_game_login = $db_name->select($old_game_login_sql);
       		if(empty($old_game_login)){
       			$table_yonghu_day['old_game_login'] = -1;
       		}else{
       			$old_game_login = reset($old_game_login);
       			$table_yonghu_day['old_game_login'] = $old_game_login->old_game_login;
       		}
    		//log 100*t.days_2/t.created_player_number
    		$retention_day2_sql = "select (100*days_2/created_player_number) as retention_day2
    							from log_retention
    							where is_anonymous = 0
    							and retention_time between $start_time and $end_time";
       		$retention_day2 = $db_name->select($retention_day2_sql);
       		if(empty($retention_day2)){
       			$table_yonghu_day['retention_day2'] = -1;
       		}else{
       			$retention_day2 = reset($retention_day2);
       			$table_yonghu_day['retention_day2'] = round($retention_day2->retention_day2, 2);
       		}
    		//log

			$sub_sql = "";
			if('11' == $game_id){
				$sub_sql = " and ll1.operator_id = 0 ";
			}else{
				$sub_sql = " and ll1.operator_id between 1 and 2 ";
			}

    		$reflux_sql = "select (count(a.player_id)-count(b.player_id)) as reflux 
				    		from (select distinct player_id from log_login ll1 where ll1.login_time between {$start_time} and {$end_time} ".$sub_sql.") a
				    		join log_create_player lcp 
				    			on a.player_id = lcp.player_id and lcp.created_time < {$start_time}-86400
				    		left join (select distinct player_id from log_login ll2 where ll2.login_time between {$start_time}-86400 and {$start_time}) b
				    			on a.player_id = b.player_id";
       		$reflux = $db_name->select($reflux_sql);
       		if(empty($reflux)){
       			$table_yonghu_day['reflux'] = -1;
       		}else{
       			$reflux = reset($reflux);
   				$table_yonghu_day['reflux'] = $reflux->reflux;
   			}

   			$table_yonghu[] = $table_yonghu_day;

			//***************table 储值**********************
			/////////////////////////////////////////////////
			$table_chuzhi_day = array();
			$table_chuzhi_day['date'] = date("Y-m-d", $start_time);
			$currency_id = 1; //从eastblue的platform表得到default_currency_id
			Log::info('pokerDataDaily---test----Position----Pay_Order');
			$this->setDB();
			$order = PayOrder::on($this->db_payment)
					->serverOrderStatistics($platform_server_id, $currency_id, $start_time, $end_time, $game_id)
					->first();
			//得到的是以日期分组的统计信息，这里日报只需要统计一天的信息，得到的应该就是一条数据
			if(empty($order)){
				$table_chuzhi_day['dollor'] = -1;
			}else{
				//$dollor = reset($order);
				$dollor = $order;
				$table_chuzhi_day['dollor'] = round($dollor->total_dollar_amount, 2);
				$table_chuzhi_day['user'] = $dollor->total_user_count;
				$table_chuzhi_day['ARPPU'] = $table_chuzhi_day['dollor'] / $table_chuzhi_day['user'];
			}

			$first_pay_sql = "select count(*) as first_pay
								from (
        						select pay_user_id,offer_time from pay_order
        						where offer_yuanbao=1
        						and game_id=$game_id group by pay_user_id
        						having min(offer_time) between $start_time and $end_time
        						) as tmp";
			$first_pay = $db_payment->select($first_pay_sql);
			if(empty($first_pay)){
				$table_chuzhi_day['first_pay'] = -1;
			}else{
				$first_pay = reset($first_pay);
				$table_chuzhi_day['first_pay'] = $first_pay->first_pay;
			}

			$table_chuzhi[] = $table_chuzhi_day;
			//***************table 经济**********************
			/////////////////////////////////////////////////
			$table_jingji_day = array();
			$table_mingxi_day = array();
			$table_jingji_day['date'] = date("Y-m-d", $start_time);
			$table_mingxi_day['date'] = date("Y-m-d", $start_time);
			
			$table_mingxi_day['createPlayer1000'] = 0;
			$table_mingxi_day['createPlayer5000'] = 0;

			//=籌碼總發放明细总和 - endOneRound - getChipsFromStrongBox|getChipsFromStrongBox 
			//  - standUp + 客服平台/每日筹码发放--德州/Robot
			$ff_chip_sum = 0;
			//=籌碼總小号明细总和 - A籌碼總發放明细/endOneRound 
			//- B碼總消耗明细/saveChipsToStrongBox|saveChipsToStrongBox - A籌碼總發放明细/standUp
			$xh_chip_sum = 0;
			//取出经济记录中的筹码明细  种类(action_type)--总筹码(chip)

			$sub_sql = "";
			if('11' == $game_id){
				$sub_sql = " and operator_id = 0 ";
			}else{
				$sub_sql = " and operator_id between 1 and 2 ";
			}

			$economy_sql = "select (diff_tongqian>0) as is_fafang,
							action_type, 
							SUM( diff_tongqian ) as chip
							from  log_economy use index (action_time)
							where action_time between $start_time and $end_time ".$sub_sql."
							group by is_fafang, action_type";
			$economy = $db_name->select($economy_sql);
			$datatostore = array();
			if(!empty($economy)){
				foreach ($economy as $value) {
					$datatostore[] = array(
						'created_time' => $start_time,
						'action_type' => $value->action_type,
						'is_fafang'	=> $value->is_fafang,
						'diff_chip' => $value->chip,
						'game_id' => $game_id,
						);
					$table_mingxi_day[$value->action_type.$value->is_fafang] = $value->chip;
					if($value->is_fafang){
						$ff_chip_sum += $value->chip;
					}else{
						$xh_chip_sum = $xh_chip_sum + (-$value->chip);
					}
				}
			}
			foreach ($datatostore as $daydata) {
				$check = $db_name->table('log_dataofday')
				        ->where('created_time', $daydata['created_time'])
				        ->where('action_type', $daydata['action_type'])
				        ->where('is_fafang', $daydata['is_fafang'])
				        ->where('game_id', $daydata['game_id'])
				        ->get();
				if(count($check) > 0){
				}else{
					try {
						$insertdata = $db_name->table('log_dataofday')->insert($daydata);
					} catch (Exception $e) {
					}
				}
			}
			unset($datatostore);
			$createPlayer_sql = "select tongqian, sum(tongqian) as sum
								from log_economy use index (action_time)
								where action_time between $start_time and $end_time
								and action_type = 'createPlayer' ".$sub_sql."
								group by tongqian";
			$createPlayer = $db_name->select($createPlayer_sql);
			if(!empty($createPlayer)){
				foreach ($createPlayer as $value) {
					$table_mingxi_day['createPlayer'.$value->tongqian] = $value->sum;
					$ff_chip_sum += $value->sum;
				}
			}
			if(empty($table_mingxi_day)){
				continue;
			}
			$dada_ff_chip = $ff_chip_sum;
			$dada_xh_chip = $xh_chip_sum;
			$dada_ff_chip -= isset($table_mingxi_day['endOneRound1'])?$table_mingxi_day['endOneRound1']:'0';
			$dada_ff_chip -= isset($table_mingxi_day['getChipsFromStrongBox|getChipsFromStrongBox1'])?
								$table_mingxi_day['getChipsFromStrongBox|getChipsFromStrongBox1']:'0';
			$dada_ff_chip -= isset($table_mingxi_day['standUp1'])?$table_mingxi_day['standUp1']:'0';
			$dada_xh_chip -= isset($table_mingxi_day['endOneRound1'])?$table_mingxi_day['endOneRound1']:'0';
			$dada_xh_chip -= isset($table_mingxi_day['getChipsFromStrongBox|getChipsFromStrongBox0'])?
								-$table_mingxi_day['getChipsFromStrongBox|getChipsFromStrongBox0']:'0';
			$dada_xh_chip -= isset($table_mingxi_day['standUp1'])?$table_mingxi_day['standUp1']:'0';

			$poker_gameserver_api = PokerGameServerApi::connect($api_server_ip, $api_server_port);
			$daily_chip_response = $poker_gameserver_api->dailyChips(date("Y-m-d", $start_time));
			$dailyChip = $daily_chip_response->system_send;
			if(empty($dailyChip)){
				$warning_info .= 'Cannot get robotlose from gameserverapi dailyChips. ';
			}else{
				$dada_ff_chip += $dailyChip->robotlose;
			}
			$table_jingji_day['all_chip_fafang'] = $dada_ff_chip;
			$table_jingji_day['all_chip_xiaohao'] = $dada_xh_chip;

			$all_chip_response = $poker_gameserver_api->getPokerChips(date("Y-m-d", $start_time));
			$allChip = $all_chip_response->sys_chips;
			if(empty($allChip)){
				$warning_info .= 'Cannot get all chip from gameserverapi getPokerChips. ';
			}else{
				if(isset($allChip->all)){
					$allChipNum = $allChip->all;
				}
				if(isset($allChip->active)){
					$activeChipNum = $allChip->active;
				}
			}
			$table_jingji_day['allChip'] = isset($allChipNum)?$allChipNum:0;
			$table_jingji_day['activeChip'] = isset($activeChipNum)?$activeChipNum:0;
            $table_jingji_day['averageChip'] = round($table_jingji_day['activeChip'] / $table_yonghu_day['game_login'], 2);



            //***************table 经济明细


			$table_mingxi_day['xiaohaozongji0'] = -($xh_chip_sum + $table_mingxi_day['createPlayer1000'] + $table_mingxi_day['createPlayer5000']);
            $table_mingxi_day['createPlayer10001'] = $table_mingxi_day['createPlayer1000'];
            $table_mingxi_day['createPlayer50001'] = $table_mingxi_day['createPlayer5000'];
            unset($table_mingxi_day['createPlayer1000']);
            unset($table_mingxi_day['createPlayer5000']);
			$table_mingxi_day['fafangzongji1'] = $ff_chip_sum;


            $xiaohao = array();
            $fafang = array();

            $mingxi_day_filter = array(); //将一个一维数组中的发放与消耗分开

            foreach($table_mingxi_day as $k=>$v){
                if(0 == $v) continue;
                $char = substr($k, -1);
                if('date' == $k)
                    $mingxi_day_filter['date'] = $v;
                else if('0' == $char)
                    $xiaohao[substr($k, 0, -1)] = $v;
                else if('1' == $char)
                    $fafang[substr($k, 0, -1)] = $v;
            }

            $fafang['robot'] = $dailyChip->robotlose; //这个数据通过game-server-api查询获得。
            $xiaohao['recycle'] =  $xiaohao['endOneRound'] + $xiaohao['standUp'] + $fafang['endOneRound'] + $fafang['standUp'];  //添加的一个消耗明细项目“牌局回收”

            $xiaohao_ = array();
            $fafang_ = array();
            $fafangzongji = 0;
            $xiaohaozongji = 0;
            foreach($fafang as $k=>$v){     //去掉某些不需要的项、计算总和
                if( 'endOneRound' != $k &&  //每局结算
                    'getChipsFromStrongBox|getChipsFromStrongBox' != $k &&  //存保险箱
                    'standUp' != $k &&  //站起
                    'fafangzongji' != $k &&	//总计
                    'deductChips|deductChips' != $k)   //扣除玩家筹码时由于不足自动取出
                {
                    $fafang_[$k] = $v;
                    $fafangzongji = $fafangzongji + $v;
                }
            }

            foreach($xiaohao as $k=>$v){     //去掉某些不需要的项
                if( 'endOneRound' != $k &&
                    'saveChipsToStrongBox|saveChipsToStrongBox' != $k &&
                    'standUp' != $k &&
                    'xiaohaozongji' != $k &&
                    'deductChips|deductChips' != $k)
                {
                    $xiaohao_[$k] = $v;
                    $xiaohaozongji = $xiaohaozongji + $v;
                }
            }

            $fafang_['fafangzongji'] = $fafangzongji;
            $xiaohao_['xiaohaozongji'] = $xiaohaozongji;

            asort($xiaohao_, SORT_NUMERIC);
            arsort($fafang_, SORT_NUMERIC);

            $mingxi_day_filter['xiaohao'] = $xiaohao_;
            $mingxi_day_filter['fafang'] = $fafang_;

			$table_mingxi[] = $mingxi_day_filter;


            //////////////////以下是将筹码总发放/总消耗改为重新计算后的数值
            $table_jingji_day['all_chip_fafang'] = $fafang_['fafangzongji'];
            $table_jingji_day['all_chip_xiaohao'] = $xiaohao_['xiaohaozongji'];

            $table_jingji[] = $table_jingji_day;

            //Log::info("economy per day ====>".var_export($table_mingxi_day, true));
		}

		///////////////////////////////////////////////////////////////////

		//发送邮件
		$dataView = 'pokerDataDaily';
		$data = array(
				'table_yonghu' => $table_yonghu,
				'table_chuzhi' => $table_chuzhi,
				'table_jingji' => $table_jingji,
				'table_mingxi' => $table_mingxi,
				'warning_info' => $warning_info
			);
		Mail::send($dataView, $data, function($message) use ($from_email, $email_to, $email_subject)
		{
			$message->subject($email_subject);
			$message->from($from_email, 'cs');
			$message->to($email_to);
		});
		
		Log::info(var_export('Poker Daily Mail send execute. Email to list:', true).var_export($email_to, true));
		return Response::json(array('success'));
	}

}