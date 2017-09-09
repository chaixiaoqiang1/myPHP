<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportEconomyLogIntoDB extends Command {
	const TABLE_NAME = 'log_economy';
	const FILE_KEY = 'EconomyLog';
	const PREFIX_LOG_FILE = '/home/game/trans/data/';
	const LOG_BACKUP = '/data/logbackup/';
	const FLAG_TYPE = 'economy';

	private $log_file = '';
	private $log_file_bak = '';
	private $db_name = '';
	private $economy_files = array();
	private $db = '';
	private $file_flag = array();
	private $game_id = '';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'economy:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import Economy Log.';

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
		$this->game_id = $game_id;
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
			$this->loadEconomyFile();
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

	private function loadEconomyFile()
	{

		try{
			if(!$this->db->select("select * from file_flag where type='".self::FLAG_TYPE."' limit 1")){
				$tmp = (array)$this->db->table(self::TABLE_NAME)
							->orderBy('log_id', 'DESC')
							->first();
				if($tmp){
			        $log = $this->db->insert("insert into file_flag (file_name, type, position, log_id) values('".self::FILE_KEY.'.'.($tmp['action_time'] + 1)."', '".self::FLAG_TYPE."', '-1', ".$tmp['log_id'].")");
			    }else{
		            $log = $this->db->insert("insert into file_flag (type) values('".self::FLAG_TYPE."')");
		        }
	        }
	    } catch (\Exception $e){
	        Log::error($e);
	        return;
	    }
	    
		Log::info('Import ' . $this->db_name . ' Economy Log');
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
			Log::info($this->db_name . ' Economy Log Not Found');
			return;
		}
		$this->economy_files = array();
		foreach ($files as $v) {
            if('.' == substr($v, 0, 1))//以"."开头的文件是临时文件，不需要作任何处理
                continue;
			if (strpos($v, self::FILE_KEY) !== false) {
				$tmp = explode('.', $v);
				if (!empty($flag) && $tmp[1] < $flag[1]) {
					$this->bakFile($v);
					continue;
				}
				$this->economy_files[$tmp[1]] = $v;
			}
		}

		ksort($this->economy_files);

		$this->readLog();
	}

	private function readLog()
	{
		while(!empty($this->economy_files)) {
			$pos = -1;
			$v = array_shift($this->economy_files);
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
			//operator_id,server_id,player_id,yuanbao,tongqian,yueli,tili,shengwang,action_type,time,extra_tili,lingshi,jingjiedian,xianling	
			$i = 0;
			if($this->game_id == 11) { //如果是德州扑克 ，action_type类型为字符串
				$action_type_type = "%s";
			} else {
			    $action_type_type = "%d";
			}
			while($log = fscanf($handle, "%d\t%d\t%d\t%d\t%d\t%d\t%d\t%d\t" .$action_type_type ."\t%d\t%d\t%d\t%d\t%d\t%d\n")) {
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
			}
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
			'yuanbao',
			'tongqian',
			'yueli',
			'tili',
			'shengwang',
			'action_type',
			'action_time',
			'extra_tili',
			'lingshi',
			'jingjiedian',
			'xianling',
			'boat_book',
		);
		
		$log_info = array_combine($key_array, $log);
		foreach ($log_info as $k => $v) {
			if (is_null($v)) {
				$log_info[$k] = 0;
			}
		}


		$last_economy = (array)$this->db->table(self::TABLE_NAME)
			->where('player_id', $log_info['player_id'])
			->where('server_id', $log_info['server_id'])
			->orderBy('log_id', 'DESC')
			->first();

		$diff_array = array();

		if (!empty($last_economy)) {
			$diff_array = array(
				'diff_yuanbao'     => $log_info['yuanbao'] - $last_economy['yuanbao'],
				'diff_tongqian'    => $log_info['tongqian'] - $last_economy['tongqian'],
				'diff_yueli'       => $log_info['yueli'] - $last_economy['yueli'],
				'diff_tili'        => $log_info['tili'] - $last_economy['tili'],
				'diff_shengwang'   => $log_info['shengwang'] - $last_economy['shengwang'],
				'diff_extra_tili'  => $log_info['extra_tili'] - $last_economy['extra_tili'],
				'diff_lingshi'     => $log_info['lingshi'] - $last_economy['lingshi'],
				'diff_jingjiedian' => $log_info['jingjiedian'] - $last_economy['jingjiedian'],
				'diff_xianling'    => $log_info['xianling'] - $last_economy['xianling'],
				'diff_boat_book'   => $log_info['boat_book'] - $last_economy['boat_book'],
			);
		}

		if (!empty($diff_array)) {
			$log_info = array_merge($log_info, $diff_array);
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
		unset($diff_array);
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