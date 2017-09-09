<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportLoginLogIntoDB extends Command {
	const TABLE_NAME = 'log_login';
	const FILE_KEY = 'LoginLog';
	const PREFIX_LOG_FILE = '/home/game/trans/data/';
	const LOG_BACKUP = '/data/logbackup/';
	//const PREFIX_LOG_FILE = '/tmp/';
	const FLAG_TYPE = 'login';

	private $log_file = '';
	private $log_file_bak = '';
	private $db_name = '';
	private $login_files = array();
	private $db = '';
	private $file_flag = array();
	private $game_ban_online = array(1, 2, 3, 4, 5, 8, 11, 30, 36, 41, 43, 44, 45, 59, 60, 61, 62, 63, 70);//不使用该脚本计算在线人数的游戏

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'login:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import Login Log.';

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

		if ($game_id == 1 || $game_id == 8) {
			$this->log_file = '/home/zwzheng/trans/data/' . $this->db_name . $bak_fix . '/';
		} else {
			$this->log_file = self::PREFIX_LOG_FILE . $this->db_name . $bak_fix . '/';
		}
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
		if ($has_db) {
			$this->loadLoginFile();
		}
        DB::disconnect($this->db_name);
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


	private function loadLoginFile()
	{
        try{
            if(!$this->db->select("select * from file_flag where type='".self::FLAG_TYPE."' limit 1")){
                $tmp = (array)$this->db->table(self::TABLE_NAME)
                            ->orderBy('log_id', 'DESC')
                            ->first();
                if($tmp){
                    $log = $this->db->insert("insert into file_flag (file_name, type, position, log_id) values('".self::FILE_KEY.'.'.($tmp['login_time'] + 1)."', '".self::FLAG_TYPE."', '-1', ".$tmp['log_id'].")");
                }else{
                    $log = $this->db->insert("insert into file_flag (type) values('".self::FLAG_TYPE."')");
                }
            }
        } catch (\Exception $e){
            Log::error($e);
            return;
        }

		Log::info('Import ' . $this->db_name . ' Login Log');
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
			Log::info($this->db_name . ' Login Log Not Found');
			return;
		}
		$this->login_files= array();
		foreach ($files as $v) {
            if('.' == substr($v, 0, 1))//以"."开头的文件是临时文件，不需要作任何处理
                continue;
			if (strpos($v, self::FILE_KEY) !== false) {
				$tmp = explode('.', $v);
				if (!empty($flag) && $tmp[1] < $flag[1]) {
					$this->bakFile($v);
					continue;
				}
				$this->login_files[$tmp[1]] = $v;
			}
		}

		ksort($this->login_files);

		$this->readLog();
	}

	private function readLog()
	{
		while(!empty($this->login_files)) {
			$pos = -1;
			$v = array_shift($this->login_files);
			Log::info('Import ' . $v);
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
			//operator_id,server_id,player_id,is_login,remote_host,level,block_copy_id,block_jingying_copy_id,time
			$i = 0;
			//while($log = fscanf($handle, "%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s\n")) {
			while($line = fgets($handle)) {
				$log = explode("\t", trim($line));
				if(9 != count($log)){
				     $i++;
	                continue;
				}
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
		$key_array = array(
			'operator_id',
			'server_id',
			'player_id',
			'is_login',
			'remote_host',
			'level',
			'block_copy_id',
			'block_jingying_copy_id',
			'login_time'
		);
		
		$log_info = array_combine($key_array, $log);
		//类型转换
		$log_info['operator_id'] = (int)$log_info['operator_id'];
		$log_info['server_id'] = (int)$log_info['server_id'];
		$log_info['player_id'] = (int)$log_info['player_id'];
		$log_info['is_login'] = (int)$log_info['is_login'];
		$log_info['level'] = (int)$log_info['level'];
		$log_info['block_copy_id'] = (int)$log_info['block_copy_id'];
		$log_info['block_jingying_copy_id'] = (int)$log_info['block_jingying_copy_id'];
		$log_info['login_time'] = (int)number_format((float)$log_info['login_time'], 0, '.', '');

		foreach ($log_info as $k => $v) {
			if (is_null($v)) {
				$log_info[$k] = '';
			}
		}

		if ($log_info['is_login'] === 0) {
			$log_info['is_login'] = -1; //登出为-1
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
		} catch (\Exception $e) {
			$this->db->rollback();	
		}

		try {
			if(!in_array($this->argument('game_id'), $this->game_ban_online)){
				$online_time = floor($log_info['login_time'] / 600) * 600;
				$online_data = $this->db->table('log_online')->where('online_time', $online_time)->first();
				if (!$online_data) {
					$data = array(
						'online_time' => $online_time
					);
					$last = $this->db->table('log_online')->orderBy('online_time', 'DESC')->first();
					if (!$last) {
						$online_value = $log_info['is_login'];
					} else {
						$online_value = $log_info['is_login'] + (int)$last->online_value;
					}
					$data['online_value'] = $online_value;
					$this->db->table('log_online')->insert($data);	
				} else {
					$this->db->table('log_online')->where('online_time', $online_time)->update(array(
						'online_value' => (int)$online_data->online_value + $log_info['is_login']
					));
				}
			}
			
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