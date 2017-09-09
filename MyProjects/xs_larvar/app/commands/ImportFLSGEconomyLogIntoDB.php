<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportFLSGEconomyLogIntoDB extends Command {
	const TABLE_NAME = 'log_economy';
	const TABLE_EXTRA_NAME = 'log_economy_extra';
	const TABLE_THIRD_NAME = 'log_economy_third';
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

	private function createTable($db_name){
        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'),$db_name);
        $sql="create table IF NOT EXISTS log_economy_extra(
            `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
            `operator_id` int(10) unsigned NOT NULL,
  			`server_id` int(10) unsigned NOT NULL,
            `player_id` int(10) unsigned NOT NULL,
            `action_type` int(10) NOT NULL,
            `action_time` int(10) unsigned NOT NULL,
            `star_fragment` int(11) NOT NULL, 
            `talent_point` int(11) NOT NULL,
            `heaven_token` int(11) NOT NULL,
            `skill_fragment` int(11) NOT NULL,
            `fight_spirit` int(11) NOT NULL,
            `diff_star_fragment` int(11) NOT NULL, 
            `diff_talent_point` int(11) NOT NULL,
            `diff_heaven_token` int(11) NOT NULL,
            `diff_skill_fragment` int(11) NOT NULL,
            `diff_fight_spirit` int(11) NOT NULL,
             PRIMARY KEY (`log_id`),
 			 KEY `action_time` (`action_time`),
  			 KEY `player_id` (`player_id`)
            )ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1";
        mysqli_query($con,$sql);
    }

	private function createTableThird($db_name){
        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'),$db_name);
        $sql="create table IF NOT EXISTS log_economy_third(
            `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
            `operator_id` int(10) unsigned NOT NULL,
  			`server_id` int(10) unsigned NOT NULL,
            `player_id` int(10) unsigned NOT NULL,
            `action_type` int(10) NOT NULL,
            `action_time` int(10) unsigned NOT NULL,
            `power` int(11) NOT NULL,
            `mount_fragment` int(11) NOT NULL, 
            `jing_po` int(11) NOT NULL, 
            `diff_power` int(11) NOT NULL,
            `diff_mount_fragment` int(11) NOT NULL,
            `diff_jing_po` int(11) NOT NULL,
             PRIMARY KEY (`log_id`),
 			 KEY `action_time` (`action_time`),
  			 KEY `player_id` (`player_id`)
            )ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1";
        mysqli_query($con,$sql);
    }

	private function alterTableThird($db_name){
        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'),$db_name);
        $sql="ALTER TABLE log_economy_third 
        	ADD `jing_po` int(11) NOT NULL AFTER mount_fragment,
        	ADD `diff_jing_po` int(11) NOT NULL";
        mysqli_query($con,$sql);
    }

	private function addFollowCard($db_name){
        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'),$db_name);
        $sql="ALTER TABLE log_economy_third 
       		ADD `fruit_currency` int(11) NOT NULL AFTER jing_po,
       		ADD `fruit_bet` varchar(255) NOT NULL AFTER jing_po,
        	ADD `follow_card` int(11) NOT NULL AFTER jing_po,
        	
        	ADD `diff_follow_card` int(11) NOT NULL,
        	ADD `diff_fruit_currency` int(11) NOT NULL";
        mysqli_query($con,$sql);
    }

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'flsgEconomy:import';

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
		
		if ($game_id == 1) {
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
		try
        {
            $table1 = $this->db->select("show tables like 'log_economy'");
            $table2 = $this->db->select("show tables like 'log_economy_extra'");
            $table3 = $this->db->select("show tables like 'log_economy_third'");
            if($table1 && !$table2){
                $this->createTable($this->db_name);
            }

            if($table1 && !$table3){
            	$this->createTableThird($this->db_name);
            }

            $third_structure = $this->db->select("DESC log_economy_third jing_po");//增加jing_po字段
        	if(!$third_structure){
        		$this->alterTableThird($this->db_name);
        	}

    	    $is_follow_card = $this->db->select("DESC log_economy_third follow_card");//增加跟随卡碎片数量等3个字段
    		if(!$is_follow_card){
    			$this->addFollowCard($this->db_name);
    		}
        }catch (\Exception $e){
            Log::error($e);
            return;
        }

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
			while($line = fgets($handle)) {
				$log = explode("\t", trim($line));
				if(count($log)>=26){//根据数据库中字段标准化数组
					$log = array_slice($log, 0, 26);
				}else{
					$len = count($log);
					for($j=$len;$j<26;$j++){
						$log[$j] = '';
					}
					unset($len);
					unset($j);
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
			'star_fragment',
			'talent_point',
			'heaven_token',
			'skill_fragment',
			'fight_spirit',
			'power',
			'mount_fragment',
			'jing_po',
			'follow_card',
			'fruit_bet',
			'fruit_currency',
		);
		$log_info = array_combine($key_array, $log);
		foreach ($log_info as $k => $v) {
			if (is_null($v)) {
				$log_info[$k] = 0;
			}
		}

		$key_array1 = array(
			'operator_id' =>'',
			'server_id'   =>'',
			'player_id'   =>'',
			'yuanbao'     =>'',
			'tongqian'    =>'',
			'yueli'       =>'',
			'tili'        =>'',
			'shengwang'   =>'',
			'action_type' =>'',
			'action_time' =>'',
			'extra_tili'  =>'',
			'lingshi'     =>'',
			'jingjiedian' =>'',
			'xianling'    =>'',
			'boat_book'   =>'',
		);
		$key_array2 = array(
			'operator_id' =>'',
			'server_id'   =>'',
			'player_id'   =>'',
			'action_type' =>'',
			'action_time' =>'',
			'star_fragment' =>'',
			'talent_point'  =>'',
			'heaven_token'  =>'',
			'skill_fragment' =>'',
			'fight_spirit'  =>'',
		);
		$key_array3 = array(
			'operator_id' =>'',
			'server_id'   =>'',
			'player_id'   =>'',
			'action_type' =>'',
			'action_time' =>'',
			'power' =>'',
			'mount_fragment'  =>'',
			'jing_po'  =>'',
			'follow_card' => '',
			'fruit_bet' => '',
			'fruit_currency' => '',
		);
		$log_info1 = array_intersect_key($log_info,$key_array1);
		$log_info2 = array_intersect_key($log_info,$key_array2);
		$log_info3 = array_intersect_key($log_info,$key_array3);

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
			$log_info1 = array_merge($log_info1, $diff_array);
		}

		$last_economy2 = (array)$this->db->table(self::TABLE_EXTRA_NAME)
			->where('player_id', $log_info['player_id'])
			->where('server_id', $log_info['server_id'])
			->orderBy('log_id', 'DESC')
			->first();

		$diff_array2 = array();
		$extra_diff = 1;//是否导入该条数据的标志
		if(!empty($last_economy2)){
			$extra_diff = 0;
			$diff_array2 = array(
				'diff_star_fragment' => $log_info['star_fragment'] - $last_economy2['star_fragment'],
				'diff_talent_point'  => $log_info['talent_point'] - $last_economy2['talent_point'],
				'diff_heaven_token'  => $log_info['heaven_token'] - $last_economy2['heaven_token'],
				'diff_skill_fragment'=> $log_info['skill_fragment'] - $last_economy2['skill_fragment'],
				'diff_fight_spirit'  => $log_info['fight_spirit'] - $last_economy2['fight_spirit'],
			);
			foreach ($diff_array2 as $value) {
				if(0 != $value){
					$extra_diff = 1;break;
				}
			}
		}
		
		if (!empty($diff_array2)) {
			$log_info2 = array_merge($log_info2, $diff_array2);
		}

		$last_economy3 = (array)$this->db->table(self::TABLE_THIRD_NAME)
			->where('player_id', $log_info['player_id'])
			->where('server_id', $log_info['server_id'])
			->orderBy('log_id', 'DESC')
			->first();

		$diff_array3 = array();
		$third_diff = 1;
		if(!empty($last_economy3)){
			$third_diff = 0;
			$diff_array3 = array(
				'diff_power' => $log_info['power'] - $last_economy3['power'],
				'diff_mount_fragment'  => $log_info['mount_fragment'] - $last_economy3['mount_fragment'],
				'diff_jing_po'  => $log_info['jing_po'] - $last_economy3['jing_po'],
				'diff_follow_card'  => $log_info['follow_card'] - $last_economy3['follow_card'],
				'diff_fruit_currency'  => $log_info['fruit_currency'] - $last_economy3['fruit_currency'],
			);
			foreach ($diff_array3 as $value) {
				if(0 != $value){
					$third_diff = 1;break;
				}
			}
		}

		if (!empty($diff_array3)) {
			$log_info3 = array_merge($log_info3, $diff_array3);
		}

		$this->db->beginTransaction();
		try {
			$log_id = $this->db->table(self::TABLE_NAME)->insertGetId($log_info1);
			if(1 == $extra_diff){
				$log_info2['log_id'] = $this->db->table(self::TABLE_NAME)->max('log_id');
				$log_id2 = $this->db->table(self::TABLE_EXTRA_NAME)->insertGetId($log_info2);
			}
			if(1 == $third_diff){
				$log_info3['log_id'] = $this->db->table(self::TABLE_NAME)->max('log_id');
				$log_id3 = $this->db->table(self::TABLE_THIRD_NAME)->insertGetId($log_info3);
			}
			
		} catch (\Exception $e) {
			Log::error(self::TABLE_NAME . ' Insert Failed' . json_encode($log_info1) . json_encode($log_info2) . "\n");
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
		unset($key_array1);
		unset($key_array2);
		unset($log_info1);
		unset($log_info2);
		unset($diff_array2);
		unset($log_id2);

		unset($key_array3);
		unset($log_info3);
		unset($diff_array3);
		unset($log_id3);
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