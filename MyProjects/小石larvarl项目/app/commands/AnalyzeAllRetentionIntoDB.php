<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AnalyzeAllRetentionIntoDB extends Command {
	private $db_name = 'qiqiwu';
	private $servers = '';
	private $db = '';

	private $ignore_games = array(38, 46, 55);//不需要计算留存的游戏
	//private $game_flag_ranks = array(1, 2, 3, 4, 5, 30, 59, 60, 61, 62, 63);//需要导入神树大乱斗排行日志的游戏

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'retention:all';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'All Server Retention.';

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
		//
		$start = $this->argument('start');
		$db_name = $this->argument('db_name');
		if (!$start) {
			return false;
		}
		if ($db_name) {
			$this->db_name = $db_name;
		}
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
			$this->getServers();	
			$this->startAnalyzeCommand();
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
		));
	}


	private function getServers()
	{
		$this->servers = $this->db->table('server_list')->select('game_id', 'server_internal_id')->orderBy('game_id', 'DESC')->get();
	}

	private function startAnalyzeCommand()
	{
		while(!empty($this->servers)) {
			$server = array_shift($this->servers);
			if(in_array($server->game_id, Config::get('game_config.mnsggameids'))){	//萌娘的一些测试服，因为数据库没有表仅有库，会出现一些异常，需要跳过
				if(9999 == $server->server_internal_id){
					continue;
				}
			}
			if(!in_array($server->game_id, $this->ignore_games)){//留存相关
	            if(!in_array($server->game_id, Config::get('game_config.yysggameids'))) {
	                Artisan::call('retention:analyze', array(
	                    'game_id' => $server->game_id,
	                    'server_id' => $server->server_internal_id,
	                    'db_qiqiwu' => $this->db_name,
	                ));
	            }elseif(in_array($server->game_id, Config::get('game_config.yysggameids')) && 
	            	$server->server_internal_id == Config::get('game_config.'.$server->game_id.'.main_server')){
	            	Artisan::call('retention:analyze', array(
	                    'game_id' => $server->game_id,
	                    'server_id' => $server->server_internal_id,
	                    'db_qiqiwu' => $this->db_name,
	                ));
	            }
	            
	            if(in_array($server->game_id, Config::get('game_config.mobilegames'))){
	            	Artisan::call('channelretention:analyze', array(
	                    'game_id' => $server->game_id,
	                    'server_id' => $server->server_internal_id,
	                    'db_qiqiwu' => $this->db_name,
	                ));	            	
	            }

	            try {
					Artisan::call('userretention:analyze', array(
	                    'game_id' => $server->game_id,
	                    'server_id' => $server->server_internal_id,
	                    'db_qiqiwu' => $this->db_name,
		            ));	
	            } catch (Exception $e) {
	            } 
          		            
            }
            ///////////其他每天需要运行一次的脚本

            /*try{
            	if(in_array($server->game_id, $this->game_flag_ranks)){//神树大乱斗排行脚本
            		Artisan::call('flsgRanks:import', array(
            	        'game_id' => $server->game_id,
            	        'server_id' => $server->server_internal_id,
            	    ));	            	
            	} 
            }catch (Exception $e) {
            	Log::info('Import flsgRanks error:' . $server->game_id. '.' .$server->server_internal_id);
	        } */
            

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
			array('start', InputArgument::REQUIRED, 'Start Script'),
			array('db_name', InputArgument::OPTIONAL, 'DB NAME')
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