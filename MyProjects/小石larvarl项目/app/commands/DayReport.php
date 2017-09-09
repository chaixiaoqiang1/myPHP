<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DayReport extends Command {

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
	const EMAIL_YYSG_ID = 'cs@idgameland.com';
	const EMAIL_MNSG_ID = 'cs@idgameland.com';
    const EMAIL_RZZW_ID = 'cs@idgameland.com';
    const EMAIL_DNTG_ID = 'cs@idgameland.com';
    const EMAIL_BBLM_ID = 'cs@idgameland.com';
    const EMAIL_SJOL_ID = 'cs@idgameland.com';
    const EMAIL_YXZH_ID = 'cs@idgameland.com';
    const EMAIL_YYSG_VN = 'cs@vnwebgame.com';
    const EMAIL_YYSG_TH = 'cs@thwebgame.com';
    const EMAIL_BBLM_VN = 'cs@mangaloandau.com';
    const EMAIL_TSTX_TW = 'cs@game168.com.tw';
    const EMAIL_MNSG_EN = 'cs@carolgames.com';
    const EMAIL_CN = 'game@game.game168.com.tw';
    const EMAIL_MNSG_VN = 'cs@vnwebgame.com';
    const EMAIL_TXGZH_TW = 'cs@game168.com.tw';


	const EMAIL_TO = 'ribao@xinyoudi.com';

	private $db_qiqiwu = '';
	private $db_payment = '';
	private $servers = '';
	private $game_id = '';
	private $server_retention = array();

	//game_id => email
	private $from_email_list = array(
		1 => self::EMAIL_FLSG_TW,
		2 => self::EMAIL_FLSG_VN,
		3 => self::EMAIL_FLSG_TH,
		4 => self::EMAIL_FLSG_ID,
		8 => self::EMAIL_NSZJ_TW,
		11 => self::EMAIL_POKER,
		30 => self::EMAIL_FLSG_EN,
		36 => self::EMAIL_NSZJ_VN,
		38 => self::EMAIL_SXD_ID,
		41 => self::EMAIL_NSZJ_TH,
		43 => self::EMAIL_NSZJ_BR,
		44 => self::EMAIL_NSZJ_TR,
		45 => self::EMAIL_NSZJ_ID,
		46 => self::EMAIL_HAZG_TW,
		47 => self::EMAIL_DLD_TW,
		48 => self::EMAIL_DLD_VN,
        50 => self::EMAIL_DLD_ID,
		51 => self::EMAIL_JW_ID,
		52 => self::EMAIL_POKER_ID,
		53 => self::EMAIL_HY_TR,
		54 => self::EMAIL_YYSG_TW,
        55 => self::EMAIL_RZZW_ID,
        59 => self::EMAIL_FLSG_TW,
        60 => self::EMAIL_FLSG_VN,
        61 => self::EMAIL_FLSG_TH,
        62 => self::EMAIL_FLSG_ID,
        63 => self::EMAIL_FLSG_EN,
        64 => self::EMAIL_DNTG_ID,
        58 => self::EMAIL_BBLM_ID,
        65 => self::EMAIL_YXZH_ID,
        66 => self::EMAIL_MNSG_TW,
        67 => self::EMAIL_BBLM_VN,
        69 => self::EMAIL_YYSG_EN,
        70 => self::EMAIL_YYSG_TW,
        71 => self::EMAIL_SJOL_ID,
        72 => self::EMAIL_YYSG_US,
        73 => self::EMAIL_YYSG_ID,
        74 => self::EMAIL_YYSG_VN,
        75 => self::EMAIL_YYSG_TH,
        76 => self::EMAIL_CN,
        77 => self::EMAIL_TSTX_TW,
        79 => self::EMAIL_MNSG_EN,
        80 => self::EMAIL_YYSG_TW,
        81 => self::EMAIL_CN,
        82 => self::EMAIL_MNSG_ID,
        83 => self::EMAIL_MNSG_VN,
        84 => self::EMAIL_YYSG_TH,
        85 => self::EMAIL_YYSG_US,
        86 => self::EMAIL_TXGZH_TW,
	);

	private $subject_list = array(
		1 => '台湾风流三国日报', 
		2 => '越南风流三国日报',
		3 => '泰国风流三国日报',
		4 => '印尼风流三国日报',
		8 => '台湾女神之剑日报',
		11 => '德州扑克日报',
		30 => '英文风流三国日报',
		36 => '越南女神之剑日报',
		38 => '印尼神仙道日报',
		41 => '泰国女神之剑日报',
		43 => '巴西女神之剑日报',
		44 => '土耳其女神之剑日报',
		45 => '印尼女神之剑日报',
		46 => '台湾黑暗之光日报',
		47 => '台湾大乱斗日报',
		48 => '越南大乱斗日报',
		50 => '印尼大乱斗日报',
		51 => '印尼君王2日报',
		52 => '印尼德州扑克日报',
		53 => '土耳其火影日报',
		54 => '台湾夜夜三国日报',
        55 => '印尼忍者之王日报',
        59 => '世界版台湾风流三国日报',
        60 => '世界版越南风流三国日报',
        61 => '世界版泰国风流三国日报',
        62 => '世界版印尼风流三国日报',
        63 => '世界版英文风流三国日报',
        64 => '印尼大闹天宫日报',
        58 => '印尼宝贝联盟日报',
        65 => '印尼英雄战魂日报',
        66 => '台湾萌娘三国日报',
        67 => '越南宝贝联盟日报',
        69 => '英文夜夜三国日报',
        70 => '俄罗斯女神日报',
        71 => '印尼世界Online日报',
        72 => '欧美夜夜三国日报',
        73 => '印尼夜夜三国日报',
        74 => '越南夜夜三国日报',
        75 => '泰国夜夜三国日报',
        76 => '大陆夜夜三国IOS日报',
        77 => '台湾天上天下日报',
        79 => '英文萌娘三国日报',
        80 => '韩国夜夜三国日报',
        81 => '大陆夜夜三国安卓日报',
        82 => '印尼萌娘三国日报',
        83 => '越南萌娘三国日报',
        84 => '泰国萌娘三国日报',
        85 => '欧美萌娘三国日报',
        86 => '台湾铁血刚之魂日报',
	);

	private $days = array(
		2, 3, 4, 5, 6, 7
	);

	private $levels = array(
		1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 60, 70, 80, 90
	);

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'day:report';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Day Report.';

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
		$this->game_id = $this->argument('game_id');
		if(in_array($this->game_id, array(41,50,55,64,47,48,59,60,61,62,63,71,77))){
			return;
		}
		$this->db_qiqiwu = $this->argument('db_qiqiwu');
		$this->db_payment = $this->argument('db_payment');
		$this->setDB();
		$this->getServers();
		$game = DB::connection($this->db_qiqiwu)
			->table('game_list')
			->where('game_id', $this->game_id);
		//印尼神仙道 黑暗之光 土耳其火影/代理 印尼宝贝联盟 印尼英雄战魂 越南宝贝联盟
			
		if(in_array($this->game_id, array('38', '58', '46', '53', '67', '51', '52', '55', '64', '65', '71', '77', '86'))){
		    $revenue = $this->getRevenueStat();
		    $this->info('revenue count success！');
		    $channels = $this->getPayChannels();
		    $this->info('get paychannels success！');
		    $mail_data = array(
		            'revenue' => $revenue,
		            'channels' => $channels,
		    );
		} else {
		    $retention = $this->getRetention();
            $this->info('retention count success！');
		    $revenue = $this->getRevenueStat();
            $this->info('revenue count success！');
		    $levels = $this->getLevelsRate();
            $this->info('levels_rate count success！');
            $login = $this->getLoginStat();
            $this->info('login_stat count success！');
		    if ($this->game_id == 5) {
		    	$mail_data = array(
		            'retention' => $retention,
		            'revenue' => $revenue,
		            'login' => $login,
		            'levels' => $levels,   
		    	);
		    }else{
		    	$channels = $this->getPayChannels();
                $this->info('pay_channels count success！');
		    	$mail_data = array(
		    		'is_yysg' => in_array($this->game_id, Config::get('game_config.yysggameids')) ? 1 : 0,
		            'retention' => $retention,
		            'revenue' => $revenue,
		            'login' => $login,
		            'levels' => $levels,
		            'channels' => $channels,
		    	);
		    }
		}
		if ($this->game_id == '5') {  //腾讯日报
			$view = View::make('dayreport', $mail_data);
			$title_date = date('Y-m-d', strtotime('-1 day'));
			file_put_contents("/tmp/ribao/qq_{$title_date}.html", $view);	
			return;	
		} 
		if (isset($this->from_email_list[$this->game_id])) {
			$from_email = $this->from_email_list[$this->game_id];
		} else {
			$from_email = self::EMAIL_FLSG_OTHER;
		}
		if(in_array($this->game_id, Config::get('game_config.yysggameids'))){	//夜夜三国添加奇修的邮箱
			$email_to = array(self::EMAIL_TO, 'hlcai@xinyoudi.com');
		}else{
			$email_to = self::EMAIL_TO;
		}
		if ($game && !$this->subject_list[$this->game_id]) {
			$mail_subject = $game->game_name . '风流三国日报';
		} else if (isset($this->subject_list[$this->game_id])) {
			$mail_subject = $this->subject_list[$this->game_id];
		}
		if(in_array($this->game_id, array('38', '58', '46', '53', '67', '51', '52', '55', '64', '65', '71', '77', '86'))){
		//神仙道，印尼宝贝联盟，黑暗之光，火影,君王2,印尼德州手游 印尼英雄战魂
		    $dayreport_str = "sxd_dayreport";
		} else { //
		    $dayreport_str = "dayreport";
		}
		Mail::send($dayreport_str, $mail_data, function($message) use ($from_email, $email_to, $mail_subject) {
			$message->subject($mail_subject . ' ' . date('Y-m-d', strtotime('-1 day')));
			$message->from($from_email, 'cs');
			$message->to($email_to);
		});
        $this->info('send mail success！');

    }

	private function setDB()
	{
		Config::set("database.connections.{$this->db_qiqiwu}", array(
			'driver'    => 'mysql',
			'host'      => Config::get('database.connections.mysql.host'),
			'database'  => $this->db_qiqiwu,
			'username'  => Config::get('database.connections.mysql.username'),
			'password'  => Config::get('database.connections.mysql.password'),
			'charset'   => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'prefix'    => '',
			'options'   => Config::get('database.connections.mysql.options'),
		));
		Config::set("database.connections.{$this->db_payment}", array(
			'driver'    => 'mysql',
			'host'      => Config::get('database.connections.mysql.host'),
			'database'  => $this->db_payment,
			'username'  => Config::get('database.connections.mysql.username'),
			'password'  => Config::get('database.connections.mysql.password'),
			'charset'   => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'prefix'    => '',
			'options'   => Config::get('database.connections.mysql.options'),
		));
	}

	private function setServerDB($db_name)
	{
		Config::set("database.connections.{$db_name}", array(
			'driver'    => 'mysql',
			'host'      => Config::get('database.connections.mysql.host'),
			'database'  => $db_name,
			'username'  => Config::get('database.connections.mysql.username'),
			'password'  => Config::get('database.connections.mysql.password'),
			'charset'   => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'prefix'    => '',
			'options'   => Config::get('database.connections.mysql.options'),
		));

	}

	private function getServers()
	{
		$this->servers = DB::connection($this->db_qiqiwu)
			->select("SELECT * FROM server_list WHERE game_id = {$this->game_id} ORDER BY server_id DESC");
		if(in_array($this->game_id, Config::get('game_config.mnsggameids'))){
			$this->servers = DB::connection($this->db_qiqiwu)
			->select("SELECT * FROM server_list WHERE game_id = {$this->game_id} AND server_internal_id < 999 ORDER BY server_id DESC");
		}
        if(in_array($this->game_id, Config::get('game_config.yysggameids')))
        {
        	$server_internal_id = Config::get('game_config.'.$this->game_id.'.main_server');
            $this->servers = DB::connection($this->db_qiqiwu)
                ->select("SELECT * FROM server_list WHERE game_id = {$this->game_id} AND server_internal_id = {$server_internal_id} ORDER BY server_id DESC");
            $string = '';
            foreach($this->servers as $server)
            {
                $string = $string . '--'.$server->server_internal_id;
            }
            $this->info('yysg server list:'.$string);
        }


	}

	private function getRetention()
	{
		$result = array();
		foreach ($this->servers as $server) {
			$result[$server->server_id] = $this->getServerRetention($server);
		}
		return $result;
	}	
	
	/*
	 * 从开服时间开始有4天倒流时间，不算匿名用户
	 * 2日留存率为前4天每天的2日留存累加 / 前4天所有创建
	 * 3日留存率为前4天每天的3日留存累加 / 前4天所有创建
	 */

	private function getServerRetention($server)
	{
		$db_name = $this->game_id . '.' . $server->server_internal_id;
		$this->setServerDB($db_name);
		$open_server_day_time = strtotime(date('Y-m-d', $server->open_server_time));
		$end_time = strtotime(date('Y-m-d'));
		if(in_array($this->game_id, Config::get('game_config.mobilegames'))){	//手游不区分匿名或者非匿名，在字段is_anonymous用9表示不区分
			if(in_array($this->game_id, Config::get('game_config.yysggameids'))){
				$start_time = $end_time - 15*86400;
				$retention_log = RetentionLog::on($db_name)
					->where('retention_time', '>=', $start_time)
					->where('retention_time', '<', $end_time)
					->where('is_anonymous', 9)
					->orderBy('retention_time', 'ASC')
					->get();			
				$result = array();
				foreach ($retention_log as $day_retention) {
					$result[] = array(
						'retention_time' => date('Y-m-d', $day_retention->retention_time),
						'created_player_number' => $day_retention->created_player_number,
						'days_2' => $day_retention->days_2,
						'days_3' => $day_retention->days_3,
						'days_4' => $day_retention->days_4,
						'days_5' => $day_retention->days_5,
						'days_6' => $day_retention->days_6,
						'days_7' => $day_retention->days_7,
						'days_14' => $day_retention->days_14,
					);
				}
				return $result;	
			}else{
				$retention_log = RetentionLog::on($db_name)
					->where('retention_time', '>=', $open_server_day_time)
					->where('retention_time', '<', $end_time)
					->where('is_anonymous', 9)
					->orderBy('retention_time', 'ASC')
					->take(4)
					->get();
			}
		}else{
			$retention_log = RetentionLog::on($db_name)
				->where('retention_time', '>=', $open_server_day_time)
				->where('retention_time', '<', $end_time)
				->where('is_anonymous', 0)
				->orderBy('retention_time', 'ASC')
				->take(4)
				->get();
		}
        DB::disconnect($db_name);
        $result = array(
			'start_time' => '',
			'end_time' => '',
			'server_name' => '',
			'all' => 0,
			'days_2' => 0,
			'days_3' => 0,
			'days_4' => 0,
			'days_5' => 0,
			'days_6' => 0,
			'days_7' => 0,
			'days_14' => 0,
			'rate_2' => 0,
			'rate_3' => 0,
			'rate_4' => 0,
			'rate_5' => 0,
			'rate_6' => 0,
			'rate_7' => 0,
			'rate_14' => 0,
		);
		$result['server_name'] = $server->server_name;
		$v_start_time = 0;
		$v_end_time = 0;
		foreach($retention_log as $v) {
			if (!$result['start_time']) {
				$v_start_time = $v->retention_time;
				$v_end_time = $v->retention_time + 4 * 86400;
				$result['start_time'] = date('Y-m-d H:i:s', $v_start_time);
				$result['end_time'] = date('Y-m-d H:i:s', $v_end_time - 1);
			}
			$result['all'] += $v->created_player_number;	
			if (($end_time - $v_end_time) >= 86400) {	
				$result['days_2'] += $v->days_2;
			}

			if (($end_time - $v_end_time) >= 86400 * 2) {	
				$result['days_3'] += $v->days_3;
			}
			if (($end_time - $v_end_time) >= 86400 * 3) {	
				$result['days_4'] += $v->days_4;
			}
			
			if (($end_time - $v_end_time) >= 86400 * 4) {	
				$result['days_5'] += $v->days_5;
			}
			if (($end_time - $v_end_time) >= 86400 * 5) {	
				$result['days_6'] += $v->days_6;
			}
			if (($end_time - $v_end_time) >= 86400 * 6) {	
				$result['days_7'] += $v->days_7;
			}
			if (($end_time - $v_end_time) >= 86400 * 14) {
				$result['days_14'] += $v->days_14;
			}
		}
		$result['rate_2'] = $result['all'] > 0 ? round($result['days_2'] / $result['all'] * 100, 2) : 0;
		$result['rate_3'] = $result['all'] > 0 ? round($result['days_3'] / $result['all'] * 100, 2) : 0;
		$result['rate_4'] = $result['all'] > 0 ? round($result['days_4'] / $result['all'] * 100, 2) : 0;
		$result['rate_5'] = $result['all'] > 0 ? round($result['days_5'] / $result['all'] * 100, 2) : 0;
		$result['rate_6'] = $result['all'] > 0 ? round($result['days_6'] / $result['all'] * 100, 2) : 0;
		$result['rate_7'] = $result['all'] > 0 ? round($result['days_7'] / $result['all'] * 100, 2) : 0;
		$result['rate_14'] = $result['all'] > 0 ? round($result['days_14'] / $result['all'] * 100, 2) : 0;
		return (object)$result;
	}


	/*
	 * 取得当年、当月、当天、上月 上上月  上上上月的收入，使用美金计算，按服分组。
	 *
	 */
	private function getRevenueStat()
	{
		$result = array();
		$result[0] = $this->getRevenue(null);
		
		foreach ($this->servers as $server) {
			$result[$server->server_id] = $this->getRevenue($server);
		}
		return $result;
	}

	private function getRevenue($server)
	{
		$server_id = $server ? $server->server_id : 0;
		$server_name = $server ? $server->server_name : 'Total';
		$result = array(
			'server_name' => $server_name,
		);
		//当天的收入
		$now_time = strtotime('-1 day');    //运行时的时间号数减1
		$date_time = date('Y-m-d', $now_time);      //now_time格式化
		$start_time = strtotime($date_time);
		$end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
		if(!$server){
			try {
				Log::info('------------------------------------'.$now_time);
				Log::info('------------------------------------'.$date_time);
				Log::info('------------------------------------'.$start_time);
				Log::info('------------------------------------'.$end_time);
			} catch (Exception $e) {
			}
		}
		$result['total_dollar_amount_day'] = $this->getDetailRevenue($start_time, $end_time, $server_id);
		//当月的收入
		$start_time = strtotime(date('Y-m-1', $end_time));
		$result['total_dollar_amount_month'] = $this->getDetailRevenue($start_time, $end_time, $server_id);
		//当年的收入
		$start_time = strtotime(date('Y-1-1', $end_time));
		$result['total_dollar_amount_year'] = $this->getDetailRevenue($start_time, $end_time, $server_id);
		//历史统计
		$result['total_dollar_amount_all'] = $this->getDetailRevenue(0, 0, $server_id);


		//这个用法会造成月份不对:$start_time = strtotime(date('Y-m-1', strtotime('-1 month', $now_time)));
        //   上月月底用本月1号减1秒，  上月月初用上月1号（通过上月月底时间得出）
        //     上上月月底用上月1号减1秒，上上月月初用上上月1号
        //   上上上月月底用上上月1号-1秒，上上上月月初用上上上月1号
        //上月
        $month_end_time =  strtotime(date('Y-m-1'))-1;  //月底
        $start_time = strtotime(date('Y-m-1', $month_end_time));    //月初
		$result['total_dollar_amount_month_last'] = $this->getDetailRevenue($start_time, $month_end_time, $server_id);
		//上上月
        $month_end_time =  $start_time - 1;
        $start_time = strtotime(date('Y-m-1', $month_end_time));
		$result['total_dollar_amount_month_ll'] = $this->getDetailRevenue($start_time, $month_end_time, $server_id);
		//上上上月
        $month_end_time =  $start_time - 1;
        $start_time = strtotime(date('Y-m-1', $month_end_time));
		$result['total_dollar_amount_month_lll'] = $this->getDetailRevenue($start_time, $month_end_time, $server_id);

		return (object)$result;
	}

	private function getDetailRevenue($start_time, $end_time, $server_id)
	{
		$query = PayOrder::on($this->db_payment)
			->where('get_payment', 1)
			->whereNotExists(function($query) {
				$query->select(DB::raw(1))
					->from('refund_order')
					->whereRaw('refund_order.order_sn = o.order_sn');
			})
			->selectRaw('SUM(pay_amount * exchange) as dollar_amount');
		if ($start_time > 0 && $end_time > 0) {
			$query->where('pay_time', '>=', $start_time)
				->where('pay_time', '<=', $end_time);
		}
		if ($server_id > 0) {
			$query->where('server_id', $server_id);
		}
		if ($this->game_id) {
			$query->where('game_id', $this->game_id);
		}
		$result = $query->first();
		if ($result) {
			return number_format($result->dollar_amount, 2);
		} else {
			return 0;
		}
	}

	private function getLevelsRate()
	{
		$result = array();
		foreach ($this->servers as $server) {
			$result[$server->server_id] = $this->getServerLevelsRate($server);
		}	
		return $result;
	}	

	private function getServerLevelsRate($server)
	{
		$db_name = $this->game_id . '.' . $server->server_internal_id;
		$this->setServerDB($db_name);

		$total = CreatePlayerLog::on($db_name)->count();

		if(in_array($this->game_id, Config::get('game_config.mobilegames'))){
			$sql = "SELECT COUNT(*) as count, level
					FROM (SELECT MAX(lev) as level, player_id FROM log_levelup where 1 group by player_id) as x 
					GROUP BY level
					ORDER BY level ASC
					";           
        }else{
			$sql = "SELECT COUNT(*) as count, level
					FROM (SELECT MAX(new_level) as level, player_id FROM log_levelup where 1 group by player_id) as x 
					GROUP BY level
					ORDER BY level ASC
					";
        }

		$log = DB::connection($db_name)
			->select($sql);
        DB::disconnect($db_name);

		$levelup = 0;
		foreach ($log as $k => $v) {
			$levelup += $v->count;
		}
		$no_levelup = (object)array(
			'count' => $total - $levelup,
			'level' => 1
		);
		array_unshift($log, $no_levelup);	
		$arr = array();
		foreach ($this->levels as $v) {
			$arr['rate_' . $v] = 0;
		}
		foreach ($log as $k => $v) {
			if (in_array($v->level, $this->levels)) {
				$v->rate = $total ? round($v->count / $total * 100, 2) : 0;
				$arr['rate_' . $v->level] = $v->rate;
			}
		}
		$arr['server_name'] = $server->server_name;
		return (object)$arr;
	}

	/*
	 * 取得今天往后推一周的登录数据
	 *
	 */
	private function getLoginStat()
	{
		$result = array();
		foreach($this->servers as $server) {
			$result[$server->server_id] = $this->getServerLoginData($server);
		}
		return $result;
	}
	private function getServerLoginData($server)
	{
		$db_name = $this->game_id . '.' . $server->server_internal_id;
		$end_time = strtotime(date('Y-m-d')) - 1;
		$this->setServerDB($db_name);
		if(in_array($this->game_id, Config::get('game_config.mobilegames'))){
			$login = LoginLog::on($db_name)
				->selectRaw("
					COUNT(DISTINCT(player_id)) as count,
					FROM_UNIXTIME(action_time, '%Y-%m-%d') as ldate
				")
				->where('action_time', '>=', $server->open_server_time)
				->where('action_time', '<=', $end_time)
				->groupBy('ldate')
				->take(7)
				->orderBy('ldate', 'DESC')
				->get();
        }else{
			$login = LoginLog::on($db_name)
				->selectRaw("
					COUNT(DISTINCT(player_id)) as count,
					FROM_UNIXTIME(login_time, '%Y-%m-%d') as ldate
				")
				->where('login_time', '>=', $server->open_server_time)
				->where('login_time', '<=', $end_time)
				->groupBy('ldate')
				->take(7)
				->orderBy('ldate', 'DESC')
				->get();
		}
        DB::disconnect($db_name);
        $arr = array(
			't_0' => 0,
			't_1' => 0,
			't_2' => 0,
			't_3' => 0,
			't_4' => 0,
			't_5' => 0,
			't_6' => 0
		);
		$day = array(
			date('Y-m-d', $end_time) => 't_0',
			date('Y-m-d', $end_time - 1 * 86400) => 't_1',
			date('Y-m-d', $end_time - 2 * 86400) => 't_2',
			date('Y-m-d', $end_time - 3 * 86400) => 't_3',
			date('Y-m-d', $end_time - 4 * 86400) => 't_4',
			date('Y-m-d', $end_time - 5 * 86400) => 't_5',
			date('Y-m-d', $end_time - 6 * 86400) => 't_6',
		);
		$i = 6;
		foreach ($login as $v) {
			if (isset($day[$v->ldate])) {
				$arr[$day[$v->ldate]] = $v->count;
			}
		}
		$arr['server_name'] = $server->server_name;
		return (object)$arr;
	}

	//支付渠道统计

	private function getPayChannels(){
		$game_id = $this->game_id;
		$now_time = strtotime('-1 day');
		$date_time = date('Y-m-d', $now_time);
		$start_time = strtotime($date_time);
		$end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));

		$currency = DB::connection($this->db_payment)->select("select * from currency where currency_name = 'USD'");
		$currency_id = $currency[0]->currency_id;
		$sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
		$pay_type = PayOrder::on($this->db_payment)->selectRaw("
			o.pay_type_id,
		   	o.method_id,
			o.money_flow_name,
			o.zone,
			MAX(o.pay_time) as pay_time_last,
			MIN(o.pay_time) as pay_time_first,
			SUM(po.pay_amount * po.exchange /({$sql_currency})) as total_amount,
			SUM(po.pay_amount * po.exchange) as total_dollar_amount,
			COUNT(po.order_id) as get_payment_count,
			COUNT(o.order_id) as count 
		")->leftJoin("pay_order as po", function($join){
			$join->on('po.order_id', '=', 'o.order_id')	
				->where('po.get_payment', '=', 1);
		})->whereBetween('o.create_time', array($start_time, $end_time))
		->where('o.game_id', $game_id)
		->whereNotExists(function($query) {
				$query->select(DB::raw(1))
					->from('refund_order')
					->whereRaw('refund_order.order_sn = o.order_sn');
			})
		->groupBy('o.pay_type_id', 'o.method_id', 'o.money_flow_name', 'o.zone')
		->get();
		
		$all_amount = 0;
		foreach ($pay_type as $v)
        {
            $all_amount += $v->total_amount;
        }
        
        foreach ($pay_type as $k => &$v)
        {
        	$v->total_amount = isset($v->total_amount) ? round($v->total_amount, 2) : 0;
        	$v->total_dollar_amount = isset($v->total_dollar_amount) ? round($v->total_dollar_amount, 2) : 0;
            $v->amount_rate = $all_amount ? round($v->total_amount / $all_amount * 100, 2) : 0;
            $v->pay_time_first = date('Y-m-d H:i:s', $v->pay_time_first);
            $v->pay_time_last = date('Y-m-d H:i:s', $v->pay_time_last);
            $v->get_payment_rate = round(($v->get_payment_count / $v->count) * 100, 2);
            $pay_types = DB::connection($this->db_payment)->select("select * from pay_type where pay_type_id = {$v->pay_type_id}");

            if ($pay_types)
            {
                $v->pay_type_name = $pay_types[0]->pay_type_name;
            }
            $payment = DB::connection($this->db_payment)->select("select * from payment_method where pay_type_id = $v->pay_type_id and method_id = $v->method_id and zone = $v->zone");

            if ($payment)
            {
                $v->pay_method_name = isset($payment[0]->method_name) ? $payment[0]->method_name: 'none';
            }
        }
        return $pay_type;      
	}
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('game_id', InputArgument::REQUIRED, 'Game ID'),
			array('db_qiqiwu', InputArgument::REQUIRED, 'qiqiwu database'),
			array('db_payment', InputArgument::REQUIRED, 'payment database'),
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
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}