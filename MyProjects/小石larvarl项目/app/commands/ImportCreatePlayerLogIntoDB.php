<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportCreatePlayerLogIntoDB extends Command {
	const TABLE_NAME = 'log_create_player';
	const FILE_KEY = 'CreatePlayerLog';
	const PREFIX_LOG_FILE = '/home/game/trans/data/';
	const LOG_BACKUP = '/data/logbackup/';
	const FLAG_TYPE = 'create_player';

	private $log_file = '';
	private $log_file_bak = '';
	private $db_name = '';
	private $player_files = array();
	private $db = '';
	private $file_flag = array();

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'player:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import Create Player Log.';

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
		} else if($game_id!==46 && $game_id!==53) {
			$this->log_file = self::PREFIX_LOG_FILE . $this->db_name . $bak_fix . '/';
		}
		$this->log_file_bak = self::LOG_BACKUP . $this->db_name . '.bak/';
		$this->setDB($this->db_name);
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
			$this->loadPlayerFile();
		}
        DB::disconnect($this->db_name);
	}


	private function setDB($db_name)
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


	private function loadPlayerFile()
	{
		try{
			if(!$this->db->select("select * from file_flag where type='".self::FLAG_TYPE."' limit 1")){
				$tmp = (array)$this->db->table(self::TABLE_NAME)
							->orderBy('log_id', 'DESC')
							->first();
				if($tmp){
			        $log = $this->db->insert("insert into file_flag (file_name, type, position, log_id) values('".self::FILE_KEY.'.'.($tmp['created_time'] + 1)."', '".self::FLAG_TYPE."', '-1', ".$tmp['log_id'].")");
			    }else{
		            $log = $this->db->insert("insert into file_flag (type) values('".self::FLAG_TYPE."')");
		        }
	        }
	    } catch (\Exception $e){
	        Log::error($e);
	        return;
	    }

		Log::info('Import ' . $this->db_name . ' Create Player Log');
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
			Log::info($this->db_name . ' Create Player Log Not Found');
			return;
		}
		$this->player_files= array();
		foreach ($files as $v) {
            if('.' == substr($v, 0, 1))//以"."开头的文件是临时文件，不需要作任何处理
                continue;
			if (strpos($v, self::FILE_KEY) !== false) {
				$tmp = explode('.', $v);
				if (!empty($flag) && $tmp[1] < $flag[1]) {
					$this->bakFile($v);
					continue;
				}
				$this->player_files[$tmp[1]] = $v;
			}
		}

		ksort($this->player_files);
		$this->readLog();
	}

	private function readLog()
	{
		while(!empty($this->player_files)) {
			$pos = -1;
			$v = array_shift($this->player_files);
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
		$key_array = array(
			'user_id',
			'operator_id',
			'server_id',
			'player_id',
			'table_id',
			'remote_host',
			'created_time',
			'player_name'
		);
		
		$log_info = array_combine($key_array, $log);

		//类型，打出的日志可能有问题，所以用%s
		$log_info['operator_id'] = (int)$log_info['operator_id'];
		$log_info['server_id'] = (int)$log_info['server_id'];
		$log_info['player_id'] = (int)$log_info['player_id'];
		$log_info['table_id'] = (int)$log_info['table_id'];
		$log_info['created_time'] = (int)number_format((float)$log_info['created_time'], 0, '.', '');	

		foreach ($log_info as $k => $v) {
			if (is_null($v)) {
				$log_info[$k] = '';
			}
		}

		$this->db->beginTransaction();
		try {
			$log_id = $this->db->table(self::TABLE_NAME)->insertGetId($log_info);
		} catch (\Exception $e) {
			Log::error(self::TABLE_NAME . $this->argument('game_id') .' Insert server Failed' . json_encode($log_info) . "\n");
			if(4 == $this->argument('game_id')){
				$this->insertOtherData($this->argument('game_id'),$log_info);
			}
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
	private function insertOtherData($game_id,$log_info){
		$other_db_name = "{$game_id}.{$log_info['server_id']}";
		$this->setDB($other_db_name);
		$has_db = false;
		try {
			$other_db = DB::connection($other_db_name);
			$other_db->disableQueryLog();
			$has_db = true;
		} catch (\Exception $e) {
			Log::error($e);
			$has_db = false;
		}
		if ($has_db) {
			$other_db->beginTransaction();
			try {
				$log_id = $other_db->table(self::TABLE_NAME)->insertGetId($log_info);
				$other_db->commit();
			} catch (\Exception $e) {
				Log::error(self::TABLE_NAME . ' Insert Failed' . json_encode($log_info) . "\n");
				$other_db->rollback();
			}
		}
		unset($log_info);
		unset($log_id);
		DB::disconnect($other_db_name);
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