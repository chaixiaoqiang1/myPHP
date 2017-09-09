
<?php 

class SlaveServerBaseController extends \BaseController {

	const DB_QIQIWU = 'qiqiwu';
	const DB_QIQIWU_RETENTION = 'qiqiwu_retention';
	const DB_PAYMENT = 'payment';
	const DB_AD = 'ad';

	private $set_time_zone = array(72,85);//欧美地区需要校正时区的游戏

	protected $db_qiqiwu = self::DB_QIQIWU;
	protected $db_payment = self::DB_PAYMENT;
	protected $db_qiqiwu_retention = self::DB_QIQIWU_RETENTION;
	protected $db_ad = self::DB_AD;
	protected $db_name = ''; //游戏数据服务器DB

	protected $platform_id = 0;
	protected $game_id = 0;
	protected $server_internal_id = 0;
	protected $error_msg = array(
		'code' => 0,
		'error' => '',
	);

	protected $primary_key = '';
	
	/*
	 * 按约定，如果在同一个mysql中，数据库使用qiqiwu_{$platform_id}，platform_id 7之前的数据库不做变更。
	 * 
	 */
	public function __construct()
	{
		$this->game_id = (int)Input::get('game_id');
		$this->server_internal_id = (int)Input::get('server_internal_id');
		if(in_array($this->game_id, Config::get('game_config.mobilegames'))){	//如果属于手游，手游id数组定义在BaseController以及model中
			if(in_array($this->game_id, Config::get('game_config.mnsggameids'))){	//萌娘三国可以根据player_id来计算玩家的服务器
				$player_id = (int)Input::get('player_id');
				if($player_id > 0){
					$mnsg_server_internal_id = floor((int)$player_id/100000);
					$this->db_name = "{$this->game_id}.{$mnsg_server_internal_id}";
				}else{
					$this->db_name = "{$this->game_id}.{$this->server_internal_id}";
				}
			}elseif(in_array($this->game_id, Config::get('game_config.yysggameids'))){	//夜夜三国类游戏均只有一个数据库
				if($this->server_internal_id){
					$this->db_name = "{$this->game_id}.{$this->server_internal_id}";
				}else{
					$this->db_name = Config::get('game_config.'.$this->game_id.'.database');
				}
			}else{
				if($this->server_internal_id){
					$this->db_name = "{$this->game_id}.{$this->server_internal_id}";
				}else{
					$this->db_name = $this->game_id . '.1';
				}
			}
		}else{
			if(in_array($this->game_id, array(11, 52, 57))){	//德扑的日志库全部指向11.1
				$this->db_name = "11.1";
			}else{
				$this->db_name = "{$this->game_id}.{$this->server_internal_id}";
			}
		}
		

		$this->platform_id = (int)Input::get('platform_id');
		if ($this->platform_id === 4) {
			$this->db_qiqiwu = 'qiqiwuid';
			$this->db_payment = 'paymentid';	
			$this->db_ad = 'adid';
		} else if ($this->platform_id > 0) {
			try {
				$this->db_qiqiwu = self::DB_QIQIWU . '_' . $this->platform_id;
				$this->setDB();
				DB::connection($this->db_qiqiwu);		
			} catch(\Exception $e) {
				$this->db_qiqiwu = self::DB_QIQIWU;
			}	
			try {
				$this->db_payment = self::DB_PAYMENT . '_' . $this->platform_id;
				$this->setDB();
				DB::connection($this->db_payment);
			} catch (\Exception $e) {
				$this->db_payment = self::DB_PAYMENT;
			}
		}
		$this->db_qiqiwu_retention = $this->db_qiqiwu.'_retention';
		if ($this->platform_id != 4) {
		    $this->db_ad= self::DB_AD . '_' . $this->game_id;
		}
		if ($this->game_id > 0) {
			try {
				$this->setDB();
				DB::connection($this->db_ad);
			} catch (\Exception $e) {
				$this->db_ad = self::DB_AD;
			}
		}

		$this->setDB();

		/*if(in_array($this->game_id, $this->set_time_zone)){//欧美时区校正
			$set_time_sql = "SET time_zone = '-4:00'";
			// $localtime_now = localtime(time(), true);//判断时令
			// if(isset($localtime_time['tm_isdst']) && 1 == $localtime_time['tm_isdst']){
			// 	$set_time_sql = "SET time_zone = '-4:00'";
			// }else{
			// 	$set_time_sql = "SET time_zone = '-5:00'";
			// }
			DB::connection($this->db_qiqiwu)->statement($set_time_sql);
			DB::connection($this->db_payment)->statement($set_time_sql);
			DB::connection($this->db_qiqiwu_retention)->statement($set_time_sql);
		}*/

	}

	/*
		这个方法用来从几个可能的索引中找到最靠前且存在的索引
		$database_name 所要查询的表所在的数据库
		$table_name 所要查询的表
		$possible_keys 要查询是否存在的索引，按照索引使用效率从前往后，找到第一个存在的就返回
	*/
	protected function getIndex($database_name, $table_name, $possible_keys){
		foreach ($possible_keys as $possible_key) {
			$index_try = DB::connection($database_name)->select("show index from {$table_name} where Key_name = '{$possible_key}'");
			if(count($index_try)){	//查询有结果，说明有此索引
				return $possible_key;
			}
		}
		return false;
	}

	protected function setDB()
	{
	    if($this->game_id != 38){
	        Config::set("database.connections.{$this->db_name}", array(
	                'driver'    => 'mysql',
	                'host'      => Config::get('database.connections.mysql.host'),
	                'database'  => $this->db_name,
	                'username'  => Config::get('database.connections.mysql.username'),
	                'password'  => Config::get('database.connections.mysql.password'),
	                'charset'   => 'utf8mb4',
	                'collation' => 'utf8mb4_unicode_ci',
	                'prefix'    => '',
	                'options'   => Config::get('database.connections.mysql.options'),
	        ));
	    }
		Config::set("database.connections." . $this->db_qiqiwu, array(
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
		Config::set("database.connections." . $this->db_qiqiwu_retention, array(
			'driver'    => 'mysql',
			'host'      => Config::get('database.connections.mysql.host'),
			'database'  => $this->db_qiqiwu_retention,
			'username'  => Config::get('database.connections.mysql.username'),
			'password'  => Config::get('database.connections.mysql.password'),
			'charset'   => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'prefix'    => '',
			'options'   => Config::get('database.connections.mysql.options'),
		));
		Config::set("database.connections." . $this->db_payment, array(
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
		Config::set("database.connections." . $this->db_ad, array(
			'driver'    => 'mysql',
			'host'      => Config::get('database.connections.mysql.host'),
			'database'  => $this->db_ad,
			'username'  => Config::get('database.connections.mysql.username'),
			'password'  => Config::get('database.connections.mysql.password'),
			'charset'   => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'prefix'    => '',
			'options'   => Config::get('database.connections.mysql.options'),
		));
	}

	protected function setSingleDB($db_name){
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
}