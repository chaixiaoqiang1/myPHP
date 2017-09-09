<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GamePlayCommand extends Command {
	const TABLE_NAME = 'log_gameplay';
	const FILE_KEY = 'EndgameLog';
	const PREFIX_LOG_FILE = '/home/game/trans/data/';
	const LOG_BACKUP = '/data/logbackup/';
	const FLAG_TYPE = 'gameplay';

	private $log_file = '';
	private $log_file_bak = '';
	private $db_name = '';
	private $data_files = array();
	private $db = '';
	private $file_flag = array();

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'gameplay:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import GamePlay Data';

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
		$bak = $this->argument('bak');
		$bak_fix = $bak ? '.' . $bak : '';
		$this->db_name = "{$game_id}.{$server_id}";
//		if ($game_id == 1 || $game_id == 8) {
//			$this->log_file = '/home/zwzheng/trans/data/' . $this->db_name . $bak_fix . '/';
//		} else {
//			$this->log_file = self::PREFIX_LOG_FILE . $this->db_name . $bak_fix . '/';
//		}
		//$this->log_file = '/root/log/';
		$this->log_file = self::PREFIX_LOG_FILE . $this->db_name . $bak_fix . '/';
		$this->log_file_bak = self::LOG_BACKUP . $this->db_name . '.bak/';
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
        Log::info('looooooooooooooooadGamePlayFile zhiqian');
		if ($has_db) {
			$this->loadGamePlayFile();
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
			'options'   => Config::get('database.connections.mysql.options')
		));

	}


	private function loadGamePlayFile()
	{
		Log::info('Import ' . $this->db_name . ' Create GamePlay Log');
		try {
			$this->file_flag = $this->db->table('file_flag')->where('type', self::FLAG_TYPE)->first();
		} catch (\Exception $e) {
			Log::error($e);
			return;
		}
		if ($this->file_flag->position > -1) {
			$flag = explode('.', $this->file_flag->file_name);
		}
		$files = '';
		try {
			$files = scandir($this->log_file);
		} catch (\Exception $e) {
			Log::error($e);
		}
		if (!$files) {
			Log::info($this->db_name . ' Create GamePlay Log Not Found');
			return;
		}
		$this->data_files= array();
		$i = 0;
		foreach ($files as $v) {
			if (strpos($v, self::FILE_KEY) !== false) {
				$tmp = explode('.', $v);
				if (!empty($flag) && $tmp[1] < $flag[1]) {
					$this->bakFile($v);
					continue;
				}
				$this->data_files[$tmp[1]] = $v;
			}
		}

		ksort($this->data_files);
		$this->readLog();
	}

	private function readLog()
	{
		while(!empty($this->data_files)) {
			$pos = -1;
			$v = array_shift($this->data_files);
			Log::info('Data ' . $v);
			if ($v == $this->file_flag->file_name) {
				$pos = $this->file_flag->position;	
			}

			$path = $this->log_file . $v;

			try {
			    $handle = fopen($path, 'r');
			} catch (\Exception $e) {
			    Log::error(self::TABLE_NAME . ' Can not handle this file. ' . $path . "\n");
			    continue;
			}
// 			$handle = fopen($path, 'r');
// 			if ($handle === false) {
// 				Log::error(self::TABLE_NAME . ' Can not handle this file. ' . $path . "\n");
// 				continue;
// 			}
			//user_id,operator_id,server_id,player_id,table_id,remote_host,time,name
			$i = 0;
			//while($log = fscanf($handle, "%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\n")) {
			while($line = fgets($handle)) {
				$log = explode("\t", trim($line));
				if ($i <= $pos) {
					$i++;
					continue;
				}
				$flag_params = array(
					'file_name' => $v,
					'position' => $i,
				);
				$this->insertData($log, $flag_params);
				$i++;
				unset($log);
				unset($line);
			}
			fclose($handle);
			unset($i);
			unset($flag_params);
			unset($handle);
			unset($v);
			unset($path);
			unset($pos);
		}
	}


	private function insertData($log, $flag_params)
	{
	/*	$key_array = array(
			'user_id',
			'operator_id',
			'server_id',
			'player_id',
			'table_id',
			'remote_host',
			'created_time',
			'player_name'
		);
	*/
		$key_array = array(
			'GAME_ID',
			'TIME',
			'PLAYER_NUM',
            'PLAYER_LOSENUM',
			'PLAYER_ID',
            'CHIPS',
			'WIN_CHIP',
			'LAST_ACTION',
            'V_CHIPS',
            'BLIND',
            'TABLE_FEE',
            'GAME_TYPE',
		);
		
		$log_info = array_combine($key_array, $log);

		//类型，打出的日志可能有问题，所以用%s
/*		$log_info['operator_id'] = (int)$log_info['operator_id'];
		$log_info['server_id'] = (int)$log_info['server_id'];
		$log_info['player_id'] = (int)$log_info['player_id'];
		$log_info['table_id'] = (int)$log_info['table_id'];
		$log_info['created_time'] = (int)number_format((float)$log_info['created_time'], 0, '.', '');	
*/
		foreach ($log_info as $k => $v) {
			if (is_null($v)) {
				$log_info[$k] = '';
			}
		}

		$this->db->beginTransaction();
		try {
			$log_id = $this->db->table(self::TABLE_NAME)->insertGetId($log_info);
		} catch (\Exception $e) {
			Log::error(self::TABLE_NAME . ' Insert Failed' . json_encode($log_info) . "\n");
			$this->db->rollback();
		}
		try {
			$flag_params['log_id'] = $log_id;
			$this->db->table('file_flag')->where('type', self::FLAG_TYPE)->take(1)->update($flag_params);
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();	
		}
		unset($flag_params);
		unset($log);
		unset($key_array);
		unset($log_info);
		unset($log_id);
	}

	private function bakFile($file)
	{
		$from = $this->log_file . $file;
		if (!file_exists($this->log_file_bak)) {
			mkdir($this->log_file_bak);
		}
		$to = $this->log_file_bak. $file;
		try {
			rename($from, $to);
		} catch (\Exception $e) {
			Log::error($e);
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
			array('game_id', InputArgument::REQUIRED, 'Require game id'),
			array('server_id', InputArgument::REQUIRED, 'Require server id'),
			array('bak', InputArgument::OPTIONAL, 'bak dir'),
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
