<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AnalyzeUserRetentionIntoDB extends Command {

	private $db_name = '';
	private $db_qiqiwu = '';
	private $db_retention = '';

	private $connection_name = '';
	private $connection_qiqiwu = '';
	private $connection_retention = '';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'userretention:analyze';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'User Retention';

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
		Log::info('start to calculate retention----------------');
		$game_id = $this->argument('game_id');
		$server_id = $this->argument('server_id');
		$this->db_qiqiwu = $this->argument('db_qiqiwu');
		$this->db_retention = $this->db_qiqiwu.'_retention';
		$this->db_name = "$game_id."."$server_id";

		$this->setDB();

		try {
			$this->connection_name = DB::connection($this->db_name);
		} catch (Exception $e) {
		}
		$this->connection_qiqiwu = DB::connection($this->db_qiqiwu);
		$this->create_database();	//创建数据库
		$this->connection_retention = DB::connection($this->db_retention);

		$has_db = false;
		try {
			$this->db = DB::connection($this->db_qiqiwu);
			$this->db->disableQueryLog();
			$has_db = true;
		} catch (\Exception $e) {
			Log::error($e);
			$has_db = false;
		}
		if ($has_db) {
            $this->info('set DB :'. $this->db_qiqiwu . ' success!');
            Log::info('set DB :'. $this->db_qiqiwu . '--' . $this->db_name .' success!');
            $this->create_table();
            Log::info('start to calculate retention----------------create_table-end');
			$this->writeCreatePlayernum($game_id, $server_id, 0);	//实名
			$this->writeCreatePlayernum($game_id, $server_id, 1);	//匿名
			Log::info('start to calculate retention----------------writeCreatePlayernum-end');
			$this->RetentionDays($game_id, $server_id, 0);
			$this->RetentionDays($game_id, $server_id, 1);
			Log::info('start to calculate retention----------------RetentionDays-end');
		}
	}

	private function setDB()
	{
		try {
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
		} catch (Exception $e) {
		}

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

	private function create_table(){
		if(DB::connection($this->db_retention)->select("show tables like 'user_retention'")){	//重新调整表结构
			if(Schema::connection($this->db_retention)->hasColumn('user_retention', 'os_type')){

			}else{
				Schema::connection($this->db_retention)->table('user_retention', function($table){
					$table->string('os_type', 32)->nullable()->after('is_anonymous');
				});

				try {
					Schema::connection($this->db_retention)->table('user_retention', function($table){
						$table->dropUnique('gameserver');
					});
				} catch (Exception $e) {
				}

				Schema::connection($this->db_retention)->table('user_retention', function($table){
					$table->unique(array('retention_time','game_id','server_internal_id','is_anonymous','source','u1','u2','os_type'), 'gameserver');
				});
			}
		}else{
	        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
	            Config::get('database.connections.mysql.password'), $this->db_retention);
	        $sql= "CREATE TABLE IF NOT EXISTS `user_retention` (
				  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `game_id` int(10) unsigned NOT NULL,
				  `server_internal_id` int(10) unsigned NOT NULL,
				  `created_player_number` int(10) unsigned NOT NULL,
				  `retention_time` int(10) unsigned NOT NULL,
				  `source` varchar(32),
				  `u1` varchar(32),
				  `u2` varchar(32),
				  `is_anonymous` tinyint(1) NOT NULL,
				  `os_type` varchar(32),
				  `days_2` int(10) unsigned NOT NULL,
				  `days_3` int(10) unsigned NOT NULL,
				  `days_4` int(10) unsigned NOT NULL,
				  `days_5` int(10) unsigned NOT NULL,
				  `days_6` int(10) unsigned NOT NULL,
				  `days_7` int(10) unsigned NOT NULL,
				  `days_14` int(10) unsigned NOT NULL,
				  `days_30` int(10) unsigned NOT NULL,
				  PRIMARY KEY (`log_id`),
				  UNIQUE KEY `gameserver` (`retention_time`,`game_id`,`server_internal_id`,`is_anonymous`,`source`,`u1`,`u2`,`os_type`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";
	        mysqli_query($con,$sql);
		}
	}

	private function create_database(){
        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'), $this->db_qiqiwu);
        $sql= "CREATE Database IF NOT EXISTS `$this->db_retention`;";
        mysqli_query($con,$sql);
	}

	private function writeCreatePlayernum($game_id, $server_id, $is_anonymous)	//将当天各渠道创建的角色数量信息存入数据库中
	{
		$end_time = strtotime(date('Y-m-d'));
		$start_time = $end_time-86400;
		if(in_array($game_id, Config::get('game_config.yysggameids'))){	//夜夜三国使用日志库
			try {
				$create_player = CreatePlayerLog::on($this->db_name)
						->join("$this->db_qiqiwu.".'users as u', function($join){
							$join->on('u.uid', '=', 'p.uid');
						})
						->leftJoin("$this->db_qiqiwu.".'device_list as dl', function($join) use ($game_id){
							$join->on('u.device_id', '=', 'dl.device_id')
								 ->where('dl.game_id', '=', $game_id);
						})
						->where('u.is_anonymous', $is_anonymous)
						->whereBetween('p.created_time', array($start_time, $end_time))
						->groupby('u.source')
						->groupby('u.u')
						->groupby('u.u2')
						->groupby('dl.os_type')
						->selectRaw("u.source, u.u, u.u2, dl.os_type, count(distinct p.player_id) as player_num")
						->get();
			} catch (Exception $e) {
				$create_player = array();
			}
			if($create_player){
				foreach ($create_player as $value) {
					$data = array(
						'game_id' => $game_id,
						'server_internal_id' => $server_id,
						'created_player_number' => $value->player_num,
						'retention_time' =>  $start_time,
						'source' =>$value->source,
						'u1' => $value->u,
						'u2' => $value->u2,
						'is_anonymous' => $is_anonymous,
						'os_type' => $value->os_type,
						);
					$try = $this->filterRetention($data)->first();
					if($try){
						$this->filterRetention($data)->update($data);
					}else{
						try{
							$this->connection_retention->table('user_retention')->insert($data);
						}catch (Exception $e) {
							//Log::info('Catchable_Exception--'.var_export($e, true));
						}
					}
					unset($value);
					unset($try);
					unset($data);
				}
			}
		}else{	//其他游戏使用create_player
			$has_game_id = 0;
			if($this->connection_qiqiwu->select("desc create_player game_id")){	//测试create_player表是否含有game_id字段
				$has_game_id = 1;
			}
			$create_player = SlaveCreatePlayer::on($this->db_qiqiwu)
							->join('users as u', function($join) use ($game_id, $server_id, $has_game_id){
								$join->on('u.uid', '=', 'p.uid');
								if($has_game_id){
									$join->where('p.game_id', '=', $game_id)
										->where('p.server_id', '=', $server_id);
								}else{
									$join->where('p.server_id', '=', $server_id);
								}
							});
			if(in_array($game_id, Config::get('game_config.all_mobilegameids'))){	
				$create_player = $create_player->leftJoin('device_list as dl', function($join) use ($game_id){
					$join->on('u.device_id', '=', 'dl.device_id')
						 ->where('dl.game_id', '=', $game_id);
				})
				->groupby('dl.os_type');
			}
			$create_player = $create_player->where('u.is_anonymous', $is_anonymous)
							->whereBetween('p.created_time', array($start_time, $end_time))
							->groupby('u.source')
							->groupby('u.u')
							->groupby('u.u2')
							->selectRaw("u.source, u.u, u.u2, ".(in_array($game_id, Config::get('game_config.all_mobilegameids')) ? "dl.os_type," : "")." count(distinct p.player_id) as player_num")
							->get();
			if($create_player){
				foreach ($create_player as $value) {
					$data = array(
						'game_id' => $game_id,
						'server_internal_id' => $server_id,
						'created_player_number' => $value->player_num,
						'retention_time' =>  $start_time,
						'source' =>$value->source,
						'u1' => $value->u,
						'u2' => $value->u2,
						'is_anonymous' => $is_anonymous,
						);
					if(in_array($game_id, Config::get('game_config.all_mobilegameids'))){
						$data['os_type'] = $value->os_type;
					}
					$try = $this->filterRetention($data)->first();
					if($try){	//测试是否已经包含有此条数据，若有则更新
						$this->filterRetention($data)->update($data);
					}else{	//没有则插入
						try{
							$this->connection_retention->table('user_retention')->insert($data);
						}catch (Exception $e) {
							//Log::info('Catchable_Exception--'.var_export($e, true));
						}
					}
					unset($value);
					unset($try);
					unset($data);
				}
			}
		}
	}

	private function filterRetention($data){
		$sql = $this->connection_retention->table('user_retention')
					->whereBetween('retention_time', array($data['retention_time']-3700, $data['retention_time']+3700))
					->where('game_id', $data['game_id'])
					->where('server_internal_id', $data['server_internal_id'])
					->where('is_anonymous', $data['is_anonymous']);
		//以下几个字段groupby的结果可能是null，所以需要这样判断
		if(isset($data['os_type'])){	//如果null == $data['os_type']的话会导致这个判断是false
			$sql = $sql->where('os_type', $data['os_type']);
		}else{
			$sql = $sql->whereNUll('os_type');
		}

		if(isset($data['source'])){	//如果null == $data['source']的话会导致这个判断是false
			$sql = $sql->where('source', $data['source']);
		}else{
			$sql = $sql->whereNUll('source');
		}

		if(isset($data['u1'])){	//如果null == $data['u1']的话会导致这个判断是false
			$sql = $sql->where('u1', $data['u1']);
		}else{
			$sql = $sql->whereNUll('u1');
		}

		if(isset($data['u2'])){	//如果null == $data['u2']的话会导致这个判断是false
			$sql = $sql->where('u2', $data['u2']);
		}else{
			$sql = $sql->whereNUll('u2');
		}

		return $sql;
	}

	private function RetentionDays($game_id, $server_id, $is_anonymous)
	{
		$days = array(2,3,4,5,6,7,14,30);
		foreach ($days as $day) {
			$this->SingleDayRetention($day, $game_id, $server_id, $is_anonymous);
		}
	}

	private function SingleDayRetention($day, $game_id, $server_id, $is_anonymous){	//计算前一天属于的所有需要计算某日留存的天
		$end_time = strtotime(date('Y-m-d'));
		$start_time = $end_time - 86400;
		$retention_day_start_time = $start_time - ($day-1)*86400;
		$retention_day_end_time = $end_time - ($day-1)*86400;
		if(in_array($game_id, Config::get('game_config.yysggameids'))){	//夜夜三国创建用日志库,登陆数据也用日志库
			$login_num = CreatePlayerLog::on($this->db_name)
					->join('log_login as ll', function($join) use ($start_time, $end_time, $retention_day_start_time, $retention_day_end_time){
						$join->on('p.player_id', '=', 'll.player_id')
							 ->where('p.created_time', '>=', $retention_day_start_time)
							 ->where('p.created_time', '<=', $retention_day_end_time)
							 ->where('ll.action_time', '>=', $start_time)
							 ->where('ll.action_time', '<=', $end_time);
					})
					->join("$this->db_qiqiwu.".'users as u', function($join){
						$join->on('u.uid', '=', 'p.uid');
					})
					->leftJoin("$this->db_qiqiwu.".'device_list as dl', function($join) use ($game_id){
						$join->on('u.device_id', '=', 'dl.device_id')
							 ->where('dl.game_id', '=', $game_id);
					})
					->where('u.is_anonymous', $is_anonymous)
					->groupby('u.source')
					->groupby('u.u')
					->groupby('u.u2')
					->groupby('dl.os_type')
					->selectRaw("u.source, u.u, u.u2, dl.os_type, count(distinct p.player_id) as player_num")
					->get();
			if(count($login_num)){
				foreach ($login_num as $value) {
					$data = array(
						'game_id' => $game_id,
						'server_internal_id' => $server_id,
						'retention_time' =>  $retention_day_start_time,
						'source' =>$value->source,
						'u1' => $value->u,
						'u2' => $value->u2,
						'is_anonymous' => $is_anonymous,
						'os_type' => $value->os_type,
						'days_'.$day => $value->player_num,
						);
					$this->filterRetention($data)->update($data);
					unset($value);
					unset($try);
					unset($data);
				}
			}
		}elseif(11 == $game_id){
			try {
				$login_num = CreatePlayerLog::on($this->db_name)
						->join('log_login as ll', function($join) use ($start_time, $end_time, $retention_day_start_time, $retention_day_end_time){
							$join->on('p.player_id', '=', 'll.player_id')
								 ->where('p.created_time', '>=', $retention_day_start_time)
								 ->where('p.created_time', '<=', $retention_day_end_time)
								 ->where('ll.login_time', '>=', $start_time)
								 ->where('ll.login_time', '<=', $end_time);
						})
						->join("$this->db_qiqiwu.".'users as u', function($join){
							$join->on('u.uid', '=', 'p.user_id');
						})
						->where('u.is_anonymous', $is_anonymous)
						->groupby('u.source')
						->groupby('u.u')
						->groupby('u.u2')
						->selectRaw("u.source, u.u, u.u2, count(distinct p.player_id) as player_num")
						->get();
			} catch (Exception $e) {
				$login_num = array();
			}

			if($login_num){
				foreach ($login_num as $value) {
					$data = array(
						'game_id' => $game_id,
						'server_internal_id' => $server_id,
						'retention_time' =>  $retention_day_start_time,
						'source' =>$value->source,
						'u1' => $value->u,
						'u2' => $value->u2,
						'is_anonymous' => $is_anonymous,
						'days_'.$day => $value->player_num,
						);
					$this->filterRetention($data)->update($data);
					unset($value);
					unset($try);
					unset($data);
				}
			}
		}else{
			$has_game_id = 0;
			if($this->connection_qiqiwu->select("desc create_player game_id")){
				$has_game_id = 1;
			}
			$login_num = SlaveCreatePlayer::on($this->db_qiqiwu)
						->join('users as u', 'p.uid', '=', 'u.uid')
						->join('server_list as sl', function($join) use ($game_id, $server_id, $has_game_id, $retention_day_start_time, $retention_day_end_time){
							$join->on('p.server_id', '=', 'sl.server_internal_id')
								->where('sl.game_id', '=', $game_id)
								->where('p.server_id', '=', $server_id);
							if($has_game_id){
								$join->where('p.game_id', '=', $game_id);
							}
							$join->where('p.created_time', '>=', $retention_day_start_time)
								 ->where('p.created_time', '<=', $retention_day_end_time);
						})
						->join('played_server_list as psl', function($join) use ($start_time, $end_time){
							$join->on('psl.game_id', '=', 'sl.game_id')
								->on('psl.server_id', '=', 'sl.server_id')
								->on('psl.uid', '=', 'p.uid')
								->where('psl.last_login_time', '>=', $start_time);
						});

			if(in_array($game_id, Config::get('game_config.all_mobilegameids'))){
				$login_num = $login_num->leftJoin('device_list as dl', function($join) use ($game_id){
					$join->on('u.device_id', '=', 'dl.device_id')
						 ->where('dl.game_id', '=', $game_id);
				})
				->groupby('dl.os_type');
			}

			$login_num = $login_num->where('u.is_anonymous', $is_anonymous)
						->groupby('u.source')
						->groupby('u.u')
						->groupby('u.u2')
						->selectRaw("u.source, u.u, u.u2, ".(in_array($game_id, Config::get('game_config.all_mobilegameids')) ? "dl.os_type," : "")." count(distinct p.player_id) as player_num")
						->get();

			if($login_num){
				foreach ($login_num as $value) {
					$data = array(
						'game_id' => $game_id,
						'server_internal_id' => $server_id,
						'retention_time' =>  $retention_day_start_time,
						'source' =>$value->source,
						'u1' => $value->u,
						'u2' => $value->u2,
						'is_anonymous' => $is_anonymous,
						'days_'.$day => $value->player_num,
						);
					if(in_array($game_id, Config::get('game_config.all_mobilegameids'))){
						$data['os_type'] = $value->os_type;
					}
					$this->filterRetention($data)->update($data);
					unset($value);
					unset($try);
					unset($data);
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
			array('game_id', InputArgument::REQUIRED, 'Game ID'),
			array('server_id', InputArgument::REQUIRED, 'Server ID'),
			array('db_qiqiwu', InputArgument::REQUIRED, 'Qiqiwu Database'),
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