<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PayWarningCount extends Command {
	const TABLE_NAME = 'pay_abnormal_ip';

	private $db_payment = '';
	private $db_qiqiwu = '';
	private $db_retention = '';
	private $db = '';

	private $servers = '';
    private $games = array(1, 8, 54 ,66, 77, 86);//要统计的游戏game_id
    private $platform_games = '';

    private $w_start_time = '';
    private $w_end_time = '';
    private $w_time = false;

    private $from_email_list = array(
    	1  => 'cs@game168.com.tw',
    	2  => 'cs@vnwebgame.com',
    	3  => 'cs@thwebgame.com',
    	4  => 'cs@idwebgame.com',
    	30 => 'cs@carolgames.com',

    	8  => 'cs@twwebgame.com',
    	36 => 'cs@nuthankiem.com',
    	41 => 'cs@soathai.com',
    	43 => 'cs@walgames.com',
    	44 => 'cs@trwebgame.com',
    	45 => 'cs@soaindo.com',

    	54 => 'cs@game168.com.tw',
    	69 => 'cs@carolgames.com',
    	72 => 'cs@pocketsummoners.com',
    	73 => 'cs@idgameland.com',
    	74 => 'cs@vnwebgame.com',
    	75 => 'cs@thwebgame.com',
    	76 => 'game@game.game168.com.tw',
    	81 => 'game@game.game168.com.tw',

    	66 => 'cs@game168.com.tw',
    	79 => 'cs@carolgames.com',
    	82 => 'cs@idgameland.com',
    	83 => 'cs@vnwebgame.com',
    	84 => 'cs@thwebgame.com',
    );

    private $to_email_list = array(
    	'xfwang@xinyoudi.com',
    );

    private $subject_list = array(
		1 => '台湾风流三国', 
		2 => '越南风流三国',
		3 => '泰国风流三国',
		4 => '印尼风流三国',
		8 => '台湾女神之剑',
		11 => '德州扑克',
		30 => '英文风流三国',
		36 => '越南女神之剑',
		38 => '印尼神仙道',
		41 => '泰国女神之剑',
		43 => '巴西女神之剑',
		44 => '土耳其女神之剑',
		45 => '印尼女神之剑',
		46 => '台湾黑暗之光',
		47 => '台湾大乱斗',
		48 => '越南大乱斗',
		50 => '印尼大乱斗',
		51 => '印尼君王2',
		52 => '印尼德州扑克',
		53 => '土耳其火影',
		54 => '台湾夜夜三国',
        55 => '印尼忍者之王',
        59 => '世界版台湾风流三国',
        60 => '世界版越南风流三国',
        61 => '世界版泰国风流三国',
        62 => '世界版印尼风流三国',
        63 => '世界版英文风流三国',
        64 => '印尼大闹天宫',
        58 => '印尼宝贝联盟',
        65 => '印尼英雄战魂',
        66 => '台湾萌娘三国',
        67 => '越南宝贝联盟',
        69 => '英文夜夜三国',
        70 => '俄罗斯女神',
        71 => '印尼世界Online',
        72 => '欧美夜夜三国',
        73 => '印尼夜夜三国',
        74 => '越南夜夜三国',
        75 => '泰国夜夜三国',
        76 => '大陆夜夜三国IOS',
        77 => '台湾天上天下',
        79 => '英文萌娘三国',
        80 => '韩国夜夜三国',
        81 => '大陆夜夜三国安卓',
        82 => '印尼萌娘三国',
        83 => '越南萌娘三国',
        84 => '泰国萌娘三国',
	);

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'payIp:count';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Pay Ip Count.';

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
		$this->db_qiqiwu = $this->argument('db_qiqiwu');
		$this->db_payment = $this->argument('db_payment');
		$this->db_retention = $this->db_qiqiwu.'_retention';

		$this->setDB();
		try {
			$this->db = DB::connection($this->db_retention);
			$this->db->disableQueryLog();
		} catch (\Exception $e) {
			Log::error($e);
		}

		$this->getTime();
		if(false == $this->w_time){
			return;
		}

        $this->create_table();

    	$this->platform_games = DB::connection($this->db_qiqiwu)->table('server_list')
    		->groupBy('game_id')
    		->lists('game_id');

        $this->countPayIp();//统计支付多个用户的ip

        $this->countPayUser();//统计使用多个ip的用户

        // $this->AbnormalPayIpSendMail();//使用异常ip充值的玩家邮件

        // $this->AbnormalPayUserSendMail();//使用多个ip充值的玩家邮件

        // $this->AbnormalUserSendMail();//运营添加的异常用户再充值玩家邮件

        $this->AllAbnormalSendMail();//以上三个邮件合并

	}

	private function countPayIp(){
		$payIp = DB::connection($this->db_payment)->table('pay_order')
				->whereBetween('pay_time', array($this->w_start_time,$this->w_end_time))
				->where('get_payment',1)
				->groupBy('pay_ip')
				->having('num','>',1)
				->selectRaw('pay_ip,COUNT(DISTINCT pay_user_id) as num');
		$payIp = $payIp->get();

		foreach ($payIp as $v) {
			if(!$v->pay_ip){
				continue;
			}
			$try = $this->db->table(self::TABLE_NAME)
						->where('pay_ip', $v->pay_ip)
						->where('type',1)
						->first();
			if($try){
				$data = array(
					'times' => $try->times+1,
				);
				$this->db->table(self::TABLE_NAME)->where('pay_ip', $v->pay_ip)
					->update($data);
			}else{
				$data = array(
					'pay_ip' => $v->pay_ip,
				);
				$this->db->table(self::TABLE_NAME)->insertGetId($data);
			}
			unset($data);
		}
	}

	private function countPayUser(){
		$payUser = DB::connection($this->db_payment)->table('pay_order')
				->whereBetween('pay_time', array($this->w_start_time,$this->w_end_time))
				->where('get_payment',1)
				->groupBy('pay_user_id')
				->having('num','>',1)
				->selectRaw('pay_user_id,COUNT(DISTINCT pay_ip) as num');
		$payUser = $payUser->get();

		foreach ($payUser as $v) {
			if(!$v->pay_user_id){
				continue;
			}
			$try = $this->db->table('pay_abnormal_user')
						->where('pay_user_id', $v->pay_user_id)
						->where('type',1)
						->first();
			if($try){
				$data = array(
					'times' => $try->times+1,
				);
				$this->db->table('pay_abnormal_user')->where('pay_user_id', $v->pay_user_id)
					->update($data);
			}else{
				$data = array(
					'pay_user_id' => $v->pay_user_id,
				);
				$this->db->table('pay_abnormal_user')->insertGetId($data);
			}
			unset($data);
		}
	}

	private function AllAbnormalSendMail(){
		foreach ($this->platform_games as $game_id) {
			if(!in_array($game_id, $this->games)){
				continue;
			}
		
			$view = 'emails/all_abnormal_pay';

			$ip_data = $this->AbnormalPayIpData($game_id, 1);

			$user_data = $this->AbnormalPayUserData($game_id,1);

			$user_data2 = $this->AbnormalPayUserData($game_id, 2);

			$mail_data = array(
				'ip_data' => $ip_data,
				'user_data' => $user_data,
				'user_data2' => $user_data2,
			);
			$mail_subject = $this->subject_list[$game_id] . '充值预警';
			$from_email = $this->from_email_list[$game_id];
			$to_email = $this->to_email_list;

			Mail::send($view, $mail_data, function($message) use ($from_email, $to_email, $mail_subject) {
			    $message->subject($mail_subject);
			    $message->from($from_email, 'cs');
			    $message->to($to_email);
			});

			unset($ip_data);
			unset($user_data);
			unset($user_data2);
			unset($mail_data);

		}
	}

	private function AbnormalPayIpSendMail(){
		foreach ($this->platform_games as $game_id) {
			if(!in_array($game_id, $this->games)){
				continue;
			}

			$view = 'emails/abnormal_payip';

			$ip_data = $this->AbnormalPayIpData($game_id, 1);


			$mail_data = array(
				'data' => $ip_data,
			);
			$mail_subject = $this->subject_list[$game_id] . '使用代充IP充值预警';
			$from_email = $this->from_email_list[$game_id];
			$to_email = $this->to_email_list;

			Mail::send($view, $mail_data, function($message) use ($from_email, $to_email, $mail_subject) {
			    $message->subject($mail_subject);
			    $message->from($from_email, 'cs');
			    $message->to($to_email);
			});
			unset($ip_data);
			unset($mail_data);
		}
	}

	private function AbnormalPayIpData($game_id, $type){
		$order_ip = DB::connection($this->db_payment)->table('pay_order as o')
			->join($this->db_retention.'.pay_abnormal_ip as p','p.pay_ip','=','o.pay_ip')
			->whereBetween('o.pay_time',array($this->w_start_time,$this->w_end_time))
			->where('o.get_payment',1)
			->where('o.game_id',$game_id)
			->where('p.type',$type)
			->groupBy('o.pay_ip')
			->selectRaw('p.pay_ip,p.times,COUNT(o.pay_user_id) as num')
			->get();

		return $order_ip;
	}

	private function AbnormalPayUserSendMail(){
		foreach ($this->platform_games as $game_id) {
			if(!in_array($game_id, $this->games)){
				continue;
			}

			$view = 'emails/abnormal_payuser';

			$user_data = $this->AbnormalPayUserData($game_id,1);	

			$mail_data = array(
				'data' => $user_data
			);
			$mail_subject = $this->subject_list[$game_id]. '使用多个IP充值预警';
			$from_email = $this->from_email_list[$game_id];
			$to_email = $this->to_email_list;

			Mail::send($view, $mail_data, function($message) use ($from_email, $to_email, $mail_subject) {
			    $message->subject($mail_subject);
			    $message->from($from_email, 'cs');
			    $message->to($to_email);
			});
			unset($user_data);
			unset($mail_data);
		}
	}

	private function AbnormalUserSendMail(){
		foreach ($this->platform_games as $game_id) {
			if(!in_array($game_id, $this->games)){
				continue;
			}

			$view = 'emails/abnormal_user';

			$user_data = $this->AbnormalPayUserData($game_id, 2);	

			$mail_data = array(
				'data' => $user_data
			);
			$mail_subject = $this->subject_list[$game_id]. '异常用户充值预警';
			$from_email = $this->from_email_list[$game_id];
			$to_email = $this->to_email_list;

			Mail::send($view, $mail_data, function($message) use ($from_email, $to_email, $mail_subject) {
			    $message->subject($mail_subject);
			    $message->from($from_email, 'cs');
			    $message->to($to_email);
			});
			unset($user_data);
			unset($mail_data);
		}
	}

	private function AbnormalPayUserData($game_id, $type){
		$order_user = DB::connection($this->db_payment)->table('pay_order as o')
			->join($this->db_retention.'.pay_abnormal_user as p','p.pay_user_id','=','o.pay_user_id')
			->whereBetween('o.pay_time',array($this->w_start_time,$this->w_end_time))
			->where('o.get_payment',1)
			->where('o.game_id',$game_id)
			->where('p.type',$type)
			->groupBy('o.pay_user_id')
			->selectRaw('p.pay_user_id,p.times,COUNT(o.pay_ip) as num')
			->get();

		return $order_user;
	}


	private function create_table(){
        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'), $this->db_retention);
        $sql_pay_ip= "CREATE TABLE IF NOT EXISTS `pay_abnormal_ip` (
			  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `pay_ip` varchar(15) NOT NULL,
			  `type` tinyint(1) NOT NULL DEFAULT '1',
			  `times` int(10) unsigned NOT NULL DEFAULT '1',
			  PRIMARY KEY (`log_id`),
			  KEY `pay_ip` (`pay_ip`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

        $sql_pay_uid= "CREATE TABLE IF NOT EXISTS `pay_abnormal_user` (
			  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `pay_user_id` varchar(10) NOT NULL,
			  `type` tinyint(1) NOT NULL DEFAULT '1',
			  `times` int(10) unsigned NOT NULL DEFAULT '1',
			  PRIMARY KEY (`log_id`),
			  KEY `pay_user_id` (`pay_user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

		mysqli_query($con,$sql_pay_ip);
		mysqli_query($con,$sql_pay_uid);
        		
                
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
		Config::set("database.connections.{$this->db_retention}", array(
			'driver'    => 'mysql',
			'host'      => Config::get('database.connections.mysql.host'),
			'database'  => $this->db_retention,
			'username'  => Config::get('database.connections.mysql.username'),
			'password'  => Config::get('database.connections.mysql.password'),
			'charset'   => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'prefix'    => '',
			'options'   => Config::get('database.connections.mysql.options'),
		));
	}

	private function getTime(){
	    $w_day=date("w",time());
	    if($w_day=='1'){
			$cflag = '+0';
		}else{
			$cflag = '-1';
		}
	    $mon_time = strtotime(date('Y-m-d',strtotime("$cflag week Monday", time())));  //取得开始时间所在自然周的开始时间
	    if(1 == $w_day){
	    	$this->w_start_time = $mon_time-7*86400;//上周星期一0点
	    	$this->w_end_time = $mon_time-1;
	    	$this->w_time = true;
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
			array('db_qiqiwu', InputArgument::REQUIRED, 'Qiqiwu Database'),
			array('db_payment', InputArgument::REQUIRED, 'Payment Database'),
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
