<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RemoveLogFileIntoBak extends Command {
	const PREFIX_LOG_FILE = '/home/game/trans/data/';
	const LOG_BACKUP = '/data/logbackup/';

	private $log_file = '';
	private $log_file_bak = '';
	private $db_name = '';
	private $game_files = array();
	private $db = '';
	private $file_flag = array();
	private $import_files = array();
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'remove:file';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove file Log.';

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

        if (1 == $game_id || 8 == $game_id) {
            $this->log_file = '/home/zwzheng/trans/data/' . $this->db_name . $bak_fix . '/';
        } else {
            $this->log_file = self::PREFIX_LOG_FILE . $this->db_name . $bak_fix . '/';
        }
        $this->log_file_bak = self::LOG_BACKUP . $this->db_name . '.bak/';

        $this->import_files = $this->getImportFiles($game_id);
        if(empty($this->import_files)){
        	return;
        }

        $this->loadGmaeFile();
	}

	private function loadGmaeFile()
	{   
	    Log::info($this->db_name.' remove File Log');

	    $files = '';
	    try
	    {
	        $files = scandir($this->log_file);
	    }
	    catch (\Exception $e)
	    {
	        Log::error($e);
	    }
	    if (! $files)
	    {
	        Log::info($this->db_name . ' remove file Log Not Found');
	        return;
	    }
	    $this->game_files = array();
	    foreach ($files as $v)
	    {
	        if('.' == substr($v, 0, 1))//以"."开头的文件是临时文件，不需要作任何处理
	            continue;
	        $tmp = explode('.', $v);
	        if (!in_array($tmp[0], $this->import_files) && 2 == count($tmp)) {
				$this->bakFile($v);
			}
	    }
	}

	private function getImportFiles($game_id){
		if(in_array($game_id, array(1, 2, 3, 4, 5, 30, 59, 60, 61, 62, 63))){//风流三国
			return array(
					'LevelUpLog','EconomyLog','CreatePlayerLog','ItemLog','LoginLog',
					'RanksLog','CrowdFundingLog','DragonLog','ExpLog','GoldRollingLog',
					'MergeGemLog','MingGeLog','OperationLog','RingsLog','ShakeDiceLog',
				);
		}elseif(in_array($game_id, array(8, 36, 41, 45, 70))){//女神之剑
			return array(
					'LevelUpLog','EconomyLog','CreatePlayerLog','ItemLog','LoginLog',
					'ExpLog','OperationLog','XLonelyLog',
				);
		}elseif(in_array($game_id, array(11))){//德扑
			return array(
					'LevelUpLog','EconomyLog','CreatePlayerLog','ItemLog','LoginLog',
					'PlayerActionLog','EndgameLog','Activity','GameLog','MatchRankLog',
					'PlayCount','SettleLog',
				);
		}else{	//不需要移走文件操作的游戏
			return;
		}
	}

	private function bakFile($file)
    {
        $from = $this->log_file . $file;
        if (! file_exists($this->log_file_bak))
        {
            mkdir($this->log_file_bak);
        }
        $to = $this->log_file_bak . $file;
        try
        {
            rename($from, $to);
        }
        catch (\Exception $e)
        {
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
                array(
                        'game_id',
                        InputArgument::REQUIRED,
                        'Require game id'
                ),
                array(
                        'server_id',
                        InputArgument::REQUIRED,
                        'Require server id'
                ),
                array(
                        'bak',
                        InputArgument::OPTIONAL,
                        'bak dir'
                )
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
