<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DailyVisitIntoDB extends Command {
	private $game_id =11;
	//private $server_id ='';
	private $db_name = '';
	private $db_qiqiwu = '';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'daily:visit';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Daily visit login info into DB-log.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()//构造函数
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *相当于主函数，里边可以写一些处理的方法
	 * @return mixed
	 */
	public function fire()
	{
		//$server_id = $this->argument('server_id');
		$this->db_qiqiwu = $this->argument('db_qiqiwu');
	    $this->db_name =  $this->argument('db_name');
			
		$this->setDB();

		$has_db = false;
		try {
			$db_qiqiwu = DB::connection($this->db_qiqiwu);

			$db_name=DB::connection($this->db_name);
			//$this->db = DB::connection($this->db_name);
			//$this->db->disableQueryLog();//禁用日志
			$has_db = true;
		} catch (\Exception $e) {
			//Log::error($e);
			$has_db = false;
		}
		if ($has_db) {
			//$this->getRetention();
			$this->insertVisit($this->game_id,$db_qiqiwu,$db_name);
		}
		
	}

	private function insertVisit($game_id,$db_qiqiwu,$db_name)
	{
		$end_time = time();
		$sql1 = "select count(1) as num from users where last_visit_time between FROM_UNIXTIME($end_time, '%Y-%m-%d 00:00:00') and FROM_UNIXTIME($end_time, '%Y-%m-%d 23:59:59')";
    	$sql2="select count(1) as num from users where is_anonymous = 1 and last_visit_time between FROM_UNIXTIME($end_time, '%Y-%m-%d 00:00:00') and FROM_UNIXTIME($end_time, '%Y-%m-%d 23:59:59')";
    	$sql3="select count(1) as num from users where u = '100005' and last_visit_time between FROM_UNIXTIME($end_time, '%Y-%m-%d 00:00:00') and FROM_UNIXTIME($end_time, '%Y-%m-%d 23:59:59')";
    	$log1 = $db_qiqiwu->select($sql1);
    	$log2=$db_qiqiwu->select($sql2);
    	$log3=$db_qiqiwu->select($sql3);
    	$numb2=$log1[0]->num;$numb3=$log2[0]->num;$numb4=$log3[0]->num;
    	$sql4="insert into daily_visit (date_log, website_log, Anonymouse_log, share_log) values($end_time,$numb2,$numb3,$numb4)";

    	$log4 = $db_name->insert($sql4);
	}

	///
	private function setDB()
	{
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
		Config::set("database.connections.".$this->db_name, array(
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

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		//array('server_id', InputArgument::REQUIRED, 'Server ID'),
		array('db_qiqiwu', InputArgument::REQUIRED, 'Qiqiwu Database'),
		array('db_name', InputArgument::REQUIRED, 'Name Database'),
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
