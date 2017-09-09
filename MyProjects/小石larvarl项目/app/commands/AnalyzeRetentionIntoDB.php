<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AnalyzeRetentionIntoDB extends Command {

	private $days = array(
		2, 3, 4, 5, 6, 7, 14
	);
	private $db_name = '';
	private $db_qiqiwu = '';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'retention:analyze';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Player Retention';

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
		$game_id = $this->argument('game_id');
		$server_id = $this->argument('server_id');
		$this->db_qiqiwu = $this->argument('db_qiqiwu');
		$this->db_name = "{$game_id}.{$server_id}";
			
		$this->setDB();

		$has_db = false;
		try {
			$this->db = DB::connection($this->db_name);
			$this->db->disableQueryLog();
			$has_db = true;
		} catch (\Exception $e) {
			Log::error($e);
			$has_db = false;
		}
		if ($has_db) {
            $this->info('set DB :'. $this->db_name . ' success!');
			$this->getRetention($game_id);
		}
	}

	private function setDB()
	{
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

	private function getRetention($game_id)
	{
		$time_now = time();
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$time_start = $time_now - 86400*32;
			$player_date = CreatePlayerLog::on($this->db_name)
				->select(DB::raw("UNIX_TIMESTAMP(FROM_UNIXTIME(created_time, '%Y-%m-%d')) as time"))
				->where('created_time', '>', $time_start)
				->groupBy('time')
				->get();
		}else{
			$time_start = $time_now - 86400*17;
			$player_date = CreatePlayerLog::on($this->db_name)
				->select(DB::raw("UNIX_TIMESTAMP(FROM_UNIXTIME(created_time, '%Y-%m-%d')) as time"))
				->where('created_time', '>', $time_start)
				->groupBy('time')
				->get();
		}
		$player_date = $player_date->toArray();
		arsort($player_date);
        $this->info('set date :'. var_export($player_date, true) . ' success!');
        foreach ($player_date as $k => $v) {
            $this->info('going to count retention of date:'. $v['time']);
            if(in_array($game_id, Config::get('game_config.mobilegames'))){		//手游不区分是否匿名
            	$this->retentionByDay($v['time'], 9, $game_id);//9代表不区分匿名和非匿名
            }else{
            	$this->retentionByDay($v['time'], 1, $game_id);
				$this->retentionByDay($v['time'], 0, $game_id);
            }
        }
	}

	private function retentionByDay($retention_time, $is_anonymous, $game_id)
	{
		$create_player = CreatePlayerLog::on($this->db_name)
			->retentionCreatePlayer($this->db_qiqiwu, $retention_time, $is_anonymous, $game_id)
			->get();
		//从log_create_player表中查询我们传过去的表名中玩家或角色id可以与log_create_player表中的uid对应且创建时间介于$retention_time和$retention_time+86399之间的数据
		$create_player_ids = array();
		foreach ($create_player as $v) {
			$create_player_ids[] = $v->player_id;
		}	
		$retention = RetentionLog::on($this->db_name)
			->whereBetween('retention_time', array($retention_time-3700, $retention_time+3700))	
			->where('is_anonymous', $is_anonymous)
			->first();
			//从log_retention表中查询注册时间并且is_anonymous对应的第一条数据(应当只有一条)
			//whereBetween('retention_time', array($retention_time-3700, $retention_time+3700))-------这样做的原因是防止因为某些国家夏令时等变化导致时区变化一个小时---潘达
		if ($retention) {
			$retention = $retention->toArray();
		}
		
		$result = array();

		
		foreach ($this->days as $v) { //$this->days是几个需要统计的时间点
			$result['days_' . $v] = $this->dayDetail($v, $retention_time, $create_player_ids, $game_id);	 //对几个需要统计的时间点分别统计
		}
		if(in_array($game_id, Config::get('game_config.mobilegames'))){ //手游需要算30天的留存率
			$result['days_30'] = $this->dayDetail(30, $retention_time, $create_player_ids, $game_id);
		}
		
		$result['created_player_number'] = count($create_player_ids);

		if (!$retention) {
			$result['retention_time'] = $retention_time;
			$result['is_anonymous'] = $is_anonymous;
			RetentionLog::on($this->db_name)->insert($result);
		} else {
			if (!empty($result)) {
				RetentionLog::on($this->db_name)
					->whereBetween('retention_time', array($retention_time-3700, $retention_time+3700))	
					->where('is_anonymous', $is_anonymous)
					->update($result);
			}
		}
		unset($create_player_ids);
		unset($result);
	}

	private function dayDetail($day, $retention_time, $create_player_ids, $game_id)
	{
		if (empty($create_player_ids)) {
			return 0;
		}
		/*$online = LoginLog::on($this->db_name)
			->loginOnline($retention_time, $day, $create_player_ids)->get();
		$player_ids = array();

		foreach ($online as $v) {
			$player_ids[] = $v->player_id;
		}

		$online_number = count($player_ids);*/
		$in_str='';
        foreach ($create_player_ids as $player_id) { //取出所有的从log_create_player查询的符合要求的player_id
           $in_str .= ($player_id.',');
        }
        $in_str = rtrim($in_str, ',');
        if(in_array($game_id, Config::get('game_config.mobilegames'))){	//手游
        	$online = $this->db->select("SELECT COUNT( l.player_id ) as num FROM  `log_login` l JOIN (SELECT player_id, MAX( id ) AS last_log_id FROM  `log_login`  WHERE  `action_time` BETWEEN $retention_time+($day-2)*86400  AND $retention_time+$day*86400 AND player_id IN ($in_str) GROUP BY player_id HAVING MAX(  `action_time` ) < $retention_time+($day-1)*86400) AS t ON l.id = t.last_log_id WHERE l.action =1");
        }else{
        	$online = $this->db->select("SELECT COUNT( l.player_id ) as num FROM  `log_login` l JOIN (SELECT player_id, MAX( log_id ) AS last_log_id FROM  `log_login`  WHERE  `login_time` BETWEEN $retention_time+($day-2)*86400  AND $retention_time+$day*86400 AND player_id IN ($in_str) GROUP BY player_id HAVING MAX(  `login_time` ) < $retention_time+($day-1)*86400) AS t ON l.log_id = t.last_log_id WHERE l.is_login =1");
        }
        

		if (isset($online)) {
			$online = $online[0];
			$online_number = $online->num;
		}
		/*$create_player_ids = array_diff($create_player_ids, $player_ids);
		if (empty($create_player_ids)) {
			return $online_number;
		}*/

		$login_count = LoginLog::on($this->db_name)
			->loginCount($retention_time, $day, $create_player_ids, $game_id)
			->first();
			//log_login
		$total = 0;
		if ($login_count->count > 0) {
			$total = $login_count->count + $online_number;
		}
		unset($player_ids);
		return $total;
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