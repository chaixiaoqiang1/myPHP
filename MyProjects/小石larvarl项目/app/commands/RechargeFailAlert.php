<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RechargeFailAlert extends Command {
	//脚本目前没有再用
	
	const EMAIL_FLSG_TW = 'cs@game168.com.tw';
	const EMAIL_FLSG_VN = 'cs@vnwebgame.com';
	const EMAIL_FLSG_ID = 'cs@idwebgame.com';
	const EMAIL_FLSG_TH = 'cs@thwebgame.com';
	const EMAIL_FLSG_EN = 'cs@carolgames.com';
	const EMAIL_FLSG_OTHER = 'cs@game168.com.tw';

	const EMAIL_NSZJ_TW = 'cs@twwebgame.com';
	const EMAIL_NSZJ_VN = 'cs@nuthankiem.com';
	const EMAIL_NSZJ_TH = 'cs@soathai.com';
	const EMAIL_NSZJ_BR = 'cs@walgames.com';
	const EMAIL_SXD_ID = 'cs@idgameland.com';
	const EMAIL_POKER = 'cs@joyspade.com';
	const EMAIL_NSZJ_TR = 'cs@trwebgame.com';
	const EMAIL_NSZJ_ID = 'cs@soaindo.com';
	const EMAIL_HAZG_TW = 'cs@twwebgame.com';
	const EMAIL_DLD_TW = 'cs@abu168.com';
	const EMAIL_DLD_VN = 'cs@mangaloandau.com';
    const EMAIL_DLD_ID = 'cs@idgameland.com';

	const EMAIL_HY_TR = 'cs@trwebgame.com';
	const EMAIL_JW_ID = 'cs@idgameland.com';
	const EMAIL_POKER_ID = 'cs@joyspade.com';

	const EMAIL_YYSG_TW = 'cs@game168.com.tw';
	const EMAIL_MNSG_TW = 'cs@game168.com.tw';
	const EMAIL_YYSG_EN = 'cs@carolgames.com';
	const EMAIL_YYSG_US = 'cs@pocketsummoners.com';
    const EMAIL_RZZW_ID = 'cs@idgameland.com';
    const EMAIL_DNTG_ID = 'cs@idgameland.com';
    const EMAIL_BBLM_ID = 'cs@idgameland.com';
    const EMAIL_YXZH_ID = 'cs@idgameland.com';
    const EMAIL_YYSG_ID = 'cs@idgameland.com';
    const EMAIL_YYSG_VN = 'cs@vnwebgame.com';
    const EMAIL_YYSG_TH = 'cs@thwebgame.com';
    const EMAIL_SJOL_ID = 'cs@idgameland.com';
    const EMAIL_TSTX_TW = 'cs@game168.com.tw';

	const EMAIL_TO_YY = 'yy@xinyoudi.com';
	const EMAIL_TO_ZC = 'zczhang@xinyoudi.com';
	const EMAIL_TO_CW = 'caiwu@xinyoudi.com';
	const EMAIL_TO_PT = 'pingtai@xinyoudi.com';
	const EMAIL_TO_PANDA = 'xguan@xinyoudi.com';
	const EMAIL_TO_SX = 'cli2@xinyoudi.com';

	// private $game_id_list = array(
	// 	 1,  2,  3,  4,  8,
	// 	11, 30, 36, 38 ,41,
	// 	43, 44, 45, 46, 47,
	// 	48, 50, 51, 52, 53, 
	// 	54, 55, 57, 58, 59,
	// 	60, 61, 62, 63, 64,
	// 	65, 66, 69, 71, 72,
	// 	73, 74, 75, 76, 77
	// );

	private $game_id_not_in_list = array(
		5,6,7,9,10,12,13,14,
		15,16,17,18,19,20,21,
		22,23,24,25,26,27,28,
		29,31,32,33,34,35,37,
		39,40,42,49,67,68,70
		);

	//private $game_id_list = array(8,8, 8);
	//game_id => email
	// private $from_email_list = array(
	// 	1 => self::EMAIL_FLSG_TW,
	// 	2 => self::EMAIL_FLSG_VN,
	// 	3 => self::EMAIL_FLSG_TH,
	// 	4 => self::EMAIL_FLSG_ID,
	// 	8 => self::EMAIL_NSZJ_TW,
	// 	11 => self::EMAIL_POKER,
	// 	30 => self::EMAIL_FLSG_EN,
	// 	36 => self::EMAIL_NSZJ_VN,
	// 	38 => self::EMAIL_SXD_ID,
	// 	41 => self::EMAIL_NSZJ_TH,
	// 	43 => self::EMAIL_NSZJ_BR,
	// 	44 => self::EMAIL_NSZJ_TR,
	// 	45 => self::EMAIL_NSZJ_ID,
	// 	46 => self::EMAIL_HAZG_TW,
	// 	47 => self::EMAIL_DLD_TW,
	// 	48 => self::EMAIL_DLD_VN,
 //        50 => self::EMAIL_DLD_ID,
	// 	51 => self::EMAIL_JW_ID,
	// 	52 => self::EMAIL_POKER_ID,
	// 	53 => self::EMAIL_HY_TR,
	// 	54 => self::EMAIL_YYSG_TW,
 //        55 => self::EMAIL_RZZW_ID,
 //        57 => self::EMAIL_POKER,
 //        58 => self::EMAIL_BBLM_ID,
 //        59 => self::EMAIL_FLSG_TW,
 //        60 => self::EMAIL_FLSG_VN,
 //        61 => self::EMAIL_FLSG_TH,
 //        62 => self::EMAIL_FLSG_ID,
 //        63 => self::EMAIL_FLSG_EN,
 //        64 => self::EMAIL_DNTG_ID,
 //        65 => self::EMAIL_YXZH_ID,
 //        66 => self::EMAIL_MNSG_TW,
 //        69 => self::EMAIL_YYSG_EN,
 //        71 => self::EMAIL_SJOL_ID,
 //        72 => self::EMAIL_YYSG_US,
 //        73 => self::EMAIL_YYSG_ID,
 //        74 => self::EMAIL_YYSG_VN,
 //        75 => self::EMAIL_YYSG_TH,
 //        77 => self::EMAIL_TSTX_TW,
	// );

	// private $subject_list = array(
	// 	1 => '台湾风流三国储值失败订单', 
	// 	2 => '越南风流三国储值失败订单',
	// 	3 => '泰国风流三国储值失败订单',
	// 	4 => '印尼风流三国储值失败订单',
	// 	8 => '台湾女神之剑储值失败订单',
	// 	11 => '德州扑克储值失败订单',
	// 	30 => '英文风流三国储值失败订单',
	// 	36 => '越南女神之剑储值失败订单',
	// 	38 => '印尼神仙道储值失败订单',
	// 	41 => '泰国女神之剑储值失败订单',
	// 	43 => '巴西女神之剑储值失败订单',
	// 	44 => '土耳其女神之剑储值失败订单',
	// 	45 => '印尼女神之剑储值失败订单',
	// 	46 => '台湾黑暗之光储值失败订单',
	// 	47 => '台湾大乱斗储值失败订单',
	// 	48 => '越南大乱斗储值失败订单',
	// 	50 => '印尼大乱斗储值失败订单',
	// 	51 => '印尼君王储值失败订单',
	// 	52 => '德州扑克手游储值失败订单',
	// 	53 => '土耳其火影储值失败订单',
	// 	54 => '台湾夜夜三国储值失败订单',
	// 	55 => '印尼忍者之王储值失败订单',
	// 	57 => '英文徳扑手游储值失败订单',
	// 	58 => '印尼宝贝联盟储值失败订单',
	// 	59 => '台湾风流三国世界版储值失败订单',
	// 	60 => '越南风流三国世界版储值失败订单',
	// 	61 => '泰国风流三国世界版储值失败订单',
	// 	62 => '印尼风流三国世界版储值失败订单',
	// 	63 => '英文风流三国世界版储值失败订单',
	// 	64 => '印尼大闹天宫储值失败订单',
	// 	65 => '印尼英雄战魂储值失败订单',
	// 	66 => '台湾萌娘三国储值失败订单',
	// 	69 => '英文夜夜三国储值失败订单',
	// 	71 => '印尼世界online储值失败订单',
	// 	72 => '欧美夜夜三国储值失败订单',
	// 	73 => '印尼夜夜三国储值失败订单',
	// 	74 => '越南夜夜三国储值失败订单',
	// 	75 => '泰国夜夜三国储值失败订单',
	// 	77 => '台湾天上天下储值失败订单',
	// );



	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'recharge:fail';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Recharge fail.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{	
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->interval = $this->argument('interval');
		$this->option = $this->option('option');
		$game = DB::table("games")->get();
		foreach ($game as $key =>$value){
			if(in_array($value->game_id, $this->game_id_not_in_list)){
				continue;
			}
			$this->game_id_list[] = $value->game_id;
			$this->from_email_list[$value->game_id] = self::EMAIL_RZZW_ID;
			$this->subject_list[$value->game_id] = $value->game_name.'储值失败订单';
		}
		$this->getRechargeFail();
	}

	private function getRechargeFail()
	{
		$end_time = time();
		$start_time = $end_time - $this->interval - 60; //防止边界值出现问题，多查60秒

		foreach ($this->game_id_list as $game_id) 
		{
			$from_email = '';
			$email_to = array();
			$email_subject = '';

			if (isset($this->from_email_list[$game_id])) {
				$from_email = $this->from_email_list[$game_id];
			} else {
				Log::info(var_export('game_id_list['.$game_id.'] not exist in from_email_list.', true));
				return ;
			}

			if($this->option=='test'){
				$email_to = array(self::EMAIL_TO_ZC);
			}elseif ($this->option=='release') {
				$email_to = array(self::EMAIL_TO_PANDA, self::EMAIL_TO_SX, 'xfwang@xinyoudi.com');
			}else{
				Log::info(var_export('option option error.', true));
				return ;
			}

			if(isset($this->subject_list[$game_id]))
			{
				$email_subject = $this->subject_list[$game_id];
			} else {
				Log::info(var_export('game_id_list['.$game_id.'] not exist in email_subject_list.', true));
				return ;
			}
			$game = Game::find($game_id);
			if(!isset($game))
			{
				Log::info(var_export('can not find game_id '.$game_id, true));
				continue;
			}
			$platform_id =$game->platform_id;
			$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
			$response = $api->getRechargeFailInfoFromSlave($game_id, $platform_id,
					$start_time, $end_time, $from_email, $email_to, $email_subject);

			if(isset($response->http_code)){
				if('200' != $response->http_code){
					Log::info($game_id.'--RechargeFailAlert--'.var_export($response, true));
				}else{
					if(is_array($response->body)){
						$orders = $response->body;
					}else{
						Log::info($game_id.' return not an array:--RechargeFailAlert--'.var_export($response, true));
						$orders = array();
					}
					foreach ($orders as $order) {
						$data2store = array(
							'order_id' => $order->order_id,
				            'order_sn' => $order->order_sn,
				            'pay_user_id' => $order->pay_user_id,
				            'tradeseq' => $order->tradeseq,
				            'pay_amount' => round($order->pay_amount, 2),
				            'currency_code' => $order->currency_id,
				            'created_time' => time(),
				            'order_created_time' => $order->create_time,
				            'reason' => Lang::get('slave.normal_bad_order'),
				            'created_operator' => 'system',
				            'game_id' => $game_id,
				            'last_operator' => '-',
				            'deal_time' => 0,
				            'result' => '-',
				            'updated_at' => time(),
				            'type' => 'fail',
				            'is_done' => 0,
							);
						$currency = Currency::find($order->currency_id);
						if($currency){
							$data2store['currency_code'] = $currency->currency_code;
						}else{
							$data2store['currency_code'] = '';
						}
						$pay_type = PayType::where('pay_type_id', $order->pay_type_id)->where('platform_id', $game->platform_id)->first();
						if($pay_type)
		                {
		                    $data2store['pay_type_name'] = $pay_type->pay_type_name;
		                }else{
							$data2store['pay_type_name'] = '';
						}

						$server = Server::where('game_id', $game_id)->where('server_internal_id', $order->server_internal_id)->first();
	                    if ($server) {
	                    	$data2store['server_name'] = $server->server_name;
	                        $response = $api->getPlayerInfoFromLog($game->platform_id, $game_id, $server->server_internal_id, $order->pay_user_id);
	                        if ($response->http_code == 200) {
	                            $data2store['player_id'] = isset($response->body[0]->player_id) ? $response->body[0]->player_id :'';
	                            $data2store['player_name'] = isset($response->body[0]->player_name) ? $response->body[0]->player_name : '';
	                        }else{
		                    	$data2store['player_id'] = '';
								$data2store['player_name'] = '';
	                        }
	                    }else{
	                    	$data2store['server_name'] = '';
	                    	$data2store['player_id'] = '';
							$data2store['player_name'] = '';
	                    }

						$data2store['method_name'] = '';

						$already =  RecordOrders::where('game_id', $data2store['game_id'])->where('order_id', $data2store['order_id'])->where('type', 'fail')->first();
				        if($already){
				        }else{
				            DB::table('record_orders')->insert($data2store);
				        }
				        unset($data2store);
					}
				}
			}
			
		}
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('interval', InputArgument::REQUIRED, 'Data within a period time (Unit-s).'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('option', 'test/release', InputOption::VALUE_OPTIONAL, 'Debug or Release option.', null),
		);
	}

}
