<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportCrowdFundingLogIntoDB extends Command {

	const TABLE_NAME = 'log_crowd_funding';
	const FILE_KEY = 'CrowdFundingLog';
	const PREFIX_LOG_FILE = '/home/game/trans/data/';
	const LOG_BACKUP = '/data/logbackup/';
	const FLAG_TYPE = 'crowd_funding';

	private $log_file = '';
	private $log_file_bak = '';
	private $db_name = '';
	private $game_files = array();
	private $db = '';
	private $file_flag = array();

	private function createTable(){
	    $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
	        Config::get('database.connections.mysql.password'),$this->db_name);
	    $sql="CREATE TABLE IF NOT EXISTS `log_crowd_funding`(
	        log_id int primary key auto_increment NOT NULL,
	        `operator_id` int(10) unsigned NOT NULL,
	        `server_id` int(10) unsigned NOT NULL, 
	    	`player_id` int(10) unsigned NOT NULL,
	    	`time` int(10) unsigned NOT NULL,
	        `award_id` int(10) unsigned NOT NULL,
	        `get_yuanbao` int(10) NOT NULL,
	        `jiangchi_yuanbao` int(10) unsigned NOT NULL,
	         INDEX time_playerid (`time`,`player_id`)
	        )ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
	    mysqli_query($con,$sql);
	}

	protected $name = 'crowdFunding:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import CrowdFunding Log.';

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
        $this->setDB();
        $has_db = false;
        try
        {
            $this->db = DB::connection($this->db_name);
            $this->db->disableQueryLog();
            $has_db = true;
        }
        catch (\Exception $e)
        {
            Log::error($e);
            $has_db = false;
        }
        
        if ($has_db)
        {
            $this->loadGmaeFile();
        }
        DB::disconnect($this->db_name);
	}

	private function setDB()
	{
	    Config::set("database.connections.{$this->db_name}", 
	            array(
	                    'driver' => 'mysql',
	                    'host' => Config::get('database.connections.mysql.host'),
	                    'database' => $this->db_name,
	                    'username' => Config::get(
	                            'database.connections.mysql.username'),
	                    'password' => Config::get(
	                            'database.connections.mysql.password'),
	                    'charset' => 'utf8mb4',
	                    'collation' => 'utf8mb4_unicode_ci',
	                    'prefix' => '',
	                    'options' => Config::get(
	                            'database.connections.mysql.options')
	            ));
	}

	private function loadGmaeFile()
	{
	    
	    try
	    {
	        $result = $this->db->select("show tables like 'log_crowd_funding'");
	        if(!$result){
	            $this->createTable();
	        }  
	    }
	    catch (\Exception $e)
	    {
	        Log::error($e);
	        return;
	    }
	    
	    try{
	        if(!$this->db->select("select * from file_flag where type='".self::FLAG_TYPE."' limit 1")){
	            $tmp = (array)$this->db->table(self::TABLE_NAME)
	                        ->orderBy('log_id', 'DESC')
	                        ->first();
	            if($tmp){
	                $log = $this->db->insert("insert into file_flag (file_name, type, position, log_id) values('".self::FILE_KEY.'.'.($tmp['time'] + 1)."', '".self::FLAG_TYPE."', '-1', ".$tmp['log_id'].")");
	            }else{
	                $log = $this->db->insert("insert into file_flag (type) values('".self::FLAG_TYPE."')");
	            }
	        }
	    } catch (\Exception $e){
	        Log::error($e);
	        return;
	    }
	    
	    Log::info('Import ' . $this->db_name . ' CrowdFunding Log');
	    try
	    {
	        $this->file_flag = $this->db->table('file_flag')
	            ->where('type', self::FLAG_TYPE)
	            ->first();
	    }
	    catch (\Exception $e)
	    {
	        Log::error($e);
	        return;
	    }
	    if ($this->file_flag->position > - 1)
	    {
	        $flag = explode('.', $this->file_flag->file_name);
	    }
	    
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
	        Log::info($this->db_name . ' CrowdFunding Log Not Found');
	        return;
	    }
	    $this->game_files = array();
	    foreach ($files as $v)
	    {
	        if('.' == substr($v, 0, 1))//以"."开头的文件是临时文件，不需要作任何处理
	            continue;
	        if (strpos($v, self::FILE_KEY) !== false)
	        {
	            $tmp = explode('.', $v);
	            if (! empty($flag) && $tmp[1] < $flag[1])
	            {
	                $this->bakFile($v);
	                continue;
	            }
	            $this->game_files[$tmp[1]] = $v;
	        }
	    }
	    ksort($this->game_files);
	    $this->readLog();
	}

	private function readLog()
	{
	    while (! empty($this->game_files))
	    {
	        $pos = - 1;
	        $v = array_shift($this->game_files);
	        Log::info('Import ' . $v);
	        if ($v == $this->file_flag->file_name)
	        {
	            $pos = $this->file_flag->position;
	        }
	        
	        $path = $this->log_file . $v;
	        try {
	            $handle = fopen($path, 'r');
	        } catch (\Exception $e) {
	            Log::error(self::TABLE_NAME . ' Can not handle this file. ' . $path . "\n");
	            continue;
	        }
	        // server_id,room_id,rule_id,period,dealer,players,public_cards,seat_chips,bet_pools,time
	        $i = 0;
	        while($line = fgets($handle)) {
	            $log = explode("\t", trim($line));
	            if(count($log)>=7){
	                $log = array_slice($log, 0, 7);
	            }else{//不标准的数据
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
	            'time',
	            'award_id',
	            'get_yuanbao',
	            'jiangchi_yuanbao',
	    );

	    
	    $log_info = array_combine($key_array, $log);
	    foreach ($log_info as $k => $v) {
	        if (is_null($v)) {
	            $log_info[$k] = 0;
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
                array(
                        'example',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'An example option.',
                        null
                )
        );
    }

}
