<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportItemIntoDB extends Command {

    const TABLE_NAME = 'log_item';

    const FILE_KEY = 'ItemLog';

    const PREFIX_LOG_FILE = '/home/game/trans/data/';
        
    const LOG_BACKUP = '/data/logbackup/';

    const FLAG_TYPE = 'item';

    private $log_file = '';

    private $log_file_bak = '';

    private $db_name = '';

    private $game_files = array();

    private $db = '';

    private $file_flag = array();

    private $nszj_game_id = array(8,44,45);//女神要加type字段的游戏

    private $game_id = '';

    private $log_len = '';

    private function addType($db_name){
        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'),$db_name);
        $sql="ALTER TABLE log_item 
            ADD `type` tinyint(1) NOT NULL";
        mysqli_query($con,$sql);
    }

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'item:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Item Log.';

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
        $this->game_id = $this->argument('game_id');
        $server_id = $this->argument('server_id');
        $bak = $this->argument('bak');
        $bak_fix = $bak ? '.' . $bak : '';
        $this->db_name = "{$this->game_id}.{$server_id}";
        if ($this->game_id == 1 || $this->game_id == 8) {
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
            $this->loadItemFile();
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

    private function loadItemFile()
    {

        if(in_array($this->game_id, $this->nszj_game_id)){//女神增加type字段
            try{
                $exist_type = $this->db->select("DESC log_item type");
                if(!$exist_type){
                    $this->addType($this->db_name);
                }
            }catch (\Exception $e){
                Log::error($e);
                return;
            }

            $this->log_len = 7;
        }else{
            $this->log_len = 6;
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

        Log::info('Import ' . $this->db_name . ' Item Log');
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
            Log::info($this->db_name . ' Item Log Not Found');
            return;
        }
        $this->item_files = array();
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
                if ($tmp[1] < '1412741700')
                {
                    $this->bakFile($v);
                    continue;
                }
                $this->item_files[$tmp[1]] = $v;
            }
        }
        ksort($this->item_files);
        $this->readLog();
    }

    private function readLog()
    {
        while (! empty($this->item_files))
        {
            $pos = - 1;
            $v = array_shift($this->item_files);
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
                if(count($log)>=($this->log_len)){
                    $log = array_slice($log, 0, $this->log_len);
                }else{
                    $len = count($log);
                    for($j=$len;$j<($this->log_len);$j++){
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
        if(in_array($this->game_id, $this->nszj_game_id)){
           $key_array = array(
                   'operator_id',
                   'server_id',
                   'player_id',
                   'time',
                   'item_id',
                   'num',
                   'type',
           ); 
       }else{
            $key_array = array(
                    'operator_id',
                    'server_id',
                    'player_id',
                    'time',
                    'item_id',
                    'num',
            );
       }
        

        
        try{
            $log_info = array_combine($key_array, $log);
        }
          catch(\Exception $e){     //如果两数组合并失败，返回处理下一行。
              return;
          }
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
            //Log::info(var_export($flag_params,true));
            
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