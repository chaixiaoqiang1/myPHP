<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportMNSGLogDataIntoDB extends Command
{
    const FROM_DB_NAME = 'mysqlmnsg';      //来源数据库名
    const FLAG_TABLE = 'file_flag';      //file_flag表名
   // const TO_DB_NAME = '66.1';        //目标数据库名
    
    private $to_db_name;
    private $from_db ;      //来源数据库
    private $to_db ;        //目标数据库
    private $trans_num = 1000; //每次导入条数
    private $fields = array(    //数据表各字段和主键
        'log_create_partner'=>'id',
        'log_create_player' =>'player_id',
        'log_economy'       =>'id',
        'log_item'          =>'id',
        'log_levelup'       =>'id',
        'log_login'         =>'id',
        'log_player_name'   =>'id',
        'log_stage'         =>'id',
        'log_summon'        =>'id',
        'log_economy_new'   =>'id',
        'log_point'         =>'id',
        'log_formation'     =>'id',
    );
    private $othertables = array(   //默认存在但并不是通过导入得到数据的表
            'file_flag',
            'log_online',
            'log_retention',
        );
    private $createTableSqls = array(   //数据表各表的建表语句
        'file_flag'         =>"CREATE TABLE IF NOT EXISTS `file_flag` (
                              `flag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `position` int(10) NOT NULL DEFAULT '0',
                              `TYPE` varchar(125) DEFAULT NULL,
                              `log_id` int(10) unsigned DEFAULT '0',
                              PRIMARY KEY (`flag_id`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",
        'log_online'        =>"CREATE TABLE IF NOT EXISTS `log_online` (
                              `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `online_time` int(10) unsigned NOT NULL,
                              `online_value` int(11) NOT NULL,
                              `server_internal_id` int(10) NOT NULL,
                              PRIMARY KEY (`log_id`),
                              KEY `online_time` (`online_time`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
        'log_retention'    =>"CREATE TABLE IF NOT EXISTS `log_retention` (
                              `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `created_player_number` int(10) unsigned NOT NULL,
                              `retention_time` int(10) unsigned NOT NULL,
                              `is_anonymous` tinyint(1) NOT NULL,
                              `days_2` int(10) unsigned NOT NULL,
                              `days_3` int(10) unsigned NOT NULL,
                              `days_4` int(10) unsigned NOT NULL,
                              `days_5` int(10) unsigned NOT NULL,
                              `days_6` int(10) unsigned NOT NULL,
                              `days_7` int(10) unsigned NOT NULL,
                              `days_14` int(10) unsigned NOT NULL,
                              `days_30` int(10) unsigned NOT NULL,
                              PRIMARY KEY (`log_id`),
                              KEY `retention_time` (`retention_time`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
        'log_create_partner'=>"CREATE TABLE IF NOT EXISTS `log_create_partner` (
                              `id` BIGINT(15) unsigned NOT NULL AUTO_INCREMENT,
                              `mid` int(10) unsigned NOT NULL,
                              `player_id` int(10) unsigned NOT NULL,
                              `partner_id` int(10) unsigned NOT NULL,
                              `table_id` int(10) unsigned NOT NULL,
                              `lev` smallint(5) unsigned NOT NULL,
                              `star` tinyint(3) unsigned NOT NULL,
                              `created_at` int(10) unsigned NOT NULL,
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `partner_id` (`player_id`,`partner_id`),
                              KEY `table_id` (`created_at`,`table_id`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
        'log_create_player' =>"CREATE TABLE IF NOT EXISTS `log_create_player` (
                              `player_id` int(10) unsigned NOT NULL,
                              `player_name` varchar(255) NOT NULL,
                              `uid` varchar(255) NOT NULL,
                              `created_time` int(10) unsigned NOT NULL,
                              `created_ip` varchar(255) NOT NULL,
                              PRIMARY KEY (`player_id`),
                              KEY `player_name` (`player_name`),
                              KEY `uid` (`uid`),
                              KEY `created_time` (`created_time`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'log_economy'       =>"CREATE TABLE IF NOT EXISTS `log_economy` (
                              `id` BIGINT(15) unsigned NOT NULL AUTO_INCREMENT,
                              `mid` int(10) unsigned NOT NULL,
                              `player_id` int(10) unsigned NOT NULL,
                              `mana` bigint(32) unsigned NOT NULL,
                              `crystal` int(10) unsigned NOT NULL,
                              `energy` int(10) unsigned NOT NULL,
                              `arena_coin` int(10) unsigned NOT NULL,
                              `march_coin` int(10) unsigned NOT NULL,
                              `created_at` int(10) unsigned NOT NULL,
                              `diff_mana` int(10) NOT NULL DEFAULT '0',
                              `diff_crystal` int(10) NOT NULL DEFAULT '0',
                              `diff_energy` int(10) NOT NULL DEFAULT '0',
                              `diff_arena_coin` int(10) NOT NULL DEFAULT '0',
                              `diff_march_coin` int(10) NOT NULL DEFAULT '0',
                              PRIMARY KEY (`id`),
                              KEY `player_id` (`player_id`),
                              KEY `mid` (`mid`),
                              KEY `created_at` (`created_at`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
        'log_item'          =>"CREATE TABLE IF NOT EXISTS `log_item` (
                              `id` BIGINT(15) unsigned NOT NULL AUTO_INCREMENT,
                              `mid` int(10) unsigned NOT NULL,
                              `player_id` int(10) unsigned NOT NULL,
                              `table_id` int(10) unsigned NOT NULL,
                              `num` int(10) NOT NULL,
                              `created_at` int(10) unsigned NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `player_id` (`player_id`),
                              KEY `table_id` (`table_id`),
                              KEY `created_at` (`created_at`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
        'log_levelup'       =>"CREATE TABLE IF NOT EXISTS `log_levelup` (
                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `player_id` int(10) unsigned NOT NULL,
                              `old_lev` smallint(5) unsigned NOT NULL,
                              `lev` smallint(5) unsigned NOT NULL,
                              `created_at` int(10) unsigned NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `player_id` (`player_id`),
                              KEY `created_at` (`created_at`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
        'log_login'         =>"CREATE TABLE IF NOT EXISTS `log_login` (
                              `id` BIGINT(15) unsigned NOT NULL AUTO_INCREMENT,
                              `player_id` int(10) unsigned NOT NULL,
                              `lev` smallint(5) unsigned NOT NULL,
                              `action` tinyint(3) NOT NULL,
                              `action_time` int(10) unsigned NOT NULL,
                              `last_ip` varchar(255) NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `player_id` (`player_id`),
                              KEY `action_time` (`action_time`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
        'log_player_name'   =>"CREATE TABLE IF NOT EXISTS `log_player_name` (
                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `player_id` int(10) unsigned NOT NULL,
                              `player_name` varchar(255) NOT NULL,
                              `created_at` int(10) unsigned NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `player_id` (`player_id`),
                              KEY `created_at` (`created_at`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;",
        'log_stage'         =>"CREATE TABLE IF NOT EXISTS `log_stage` (
                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `player_id` int(10) unsigned NOT NULL,
                              `stage_id` int(10) unsigned NOT NULL,
                              `passed_time` int(10) unsigned NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `player_id` (`player_id`),
                              KEY `passed_time` (`passed_time`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
        'log_summon'        =>"CREATE TABLE IF NOT EXISTS `log_summon` (
                              `id` BIGINT(15) unsigned NOT NULL AUTO_INCREMENT,
                              `player_id` int(10) unsigned NOT NULL,
                              `mid` int(10) unsigned NOT NULL,
                              `summon_type` int(10) unsigned NOT NULL,
                              `item_ids` varchar(255) NOT NULL,
                              `item_nums` varchar(255) NOT NULL,
                              `to_stones` varchar(255) NOT NULL,
                              `created_at` int(10) unsigned NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `summon_type` (`summon_type`),
                              KEY `created_at` (`created_at`),
                              KEY `player_id` (`player_id`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;", 
      'log_economy_new'     =>"CREATE TABLE IF NOT EXISTS `log_economy_new` (
                              `id` BIGINT(15) unsigned NOT NULL AUTO_INCREMENT,
                              `mid` int(10) unsigned NOT NULL,
                              `player_id` int(10) unsigned NOT NULL,
                              `top_coin` int(10) unsigned NOT NULL,
                              `guild_coin` int(10) unsigned NOT NULL,
                              `keys1` int(10) unsigned NOT NULL,
                              `keys2` int(10) unsigned NOT NULL,
                              `keys3` int(10) unsigned NOT NULL,
                              `keys4` int(10) unsigned NOT NULL,
                              `keys5` int(10) unsigned NOT NULL,
                              `keys6` int(10) unsigned NOT NULL,
                              `keys7` int(10) unsigned NOT NULL,
                              `keys8` int(10) unsigned NOT NULL,
                              `keys9` int(10) unsigned NOT NULL,
                              `keys10` int(10) unsigned NOT NULL,
                              `created_at` int(10) unsigned NOT NULL,
                              `diff_top_coin` int(10) NOT NULL DEFAULT '0',
                              `diff_guild_coin` int(10) NOT NULL DEFAULT '0',
                              `diff_keys1` int(10) NOT NULL DEFAULT '0',
                              `diff_keys2` int(10) NOT NULL DEFAULT '0',
                              `diff_keys3` int(10) NOT NULL DEFAULT '0',
                              `diff_keys4` int(10) NOT NULL DEFAULT '0',
                              `diff_keys5` int(10) NOT NULL DEFAULT '0',
                              `diff_keys6` int(10) NOT NULL DEFAULT '0',
                              `diff_keys7` int(10) NOT NULL DEFAULT '0',
                              `diff_keys8` int(10) NOT NULL DEFAULT '0',
                              `diff_keys9` int(10) NOT NULL DEFAULT '0',
                              `diff_keys10` int(10) NOT NULL DEFAULT '0',
                              PRIMARY KEY (`id`),
                              KEY `playerid` (`player_id`),
                              KEY `player_id` (`created_at`,`player_id`),
                              KEY `mid` (`mid`)
                          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;",
        'log_point'         =>"CREATE TABLE IF NOT EXISTS `log_point` (
                              `id` BIGINT(15) NOT NULL AUTO_INCREMENT,
                              `player_id` int(10) NOT NULL,
                              `lev` int(4) NOT NULL,
                              `point` int(10) NOT NULL,
                              `action_time` int(10) NOT NULL,
                              PRIMARY KEY (`id`),
                              KEY `player_id` (`player_id`),
                              KEY `point` (`point`),
                              KEY `action_time` (`action_time`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;",
        'log_formation'     =>"CREATE TABLE IF NOT EXISTS `log_formation` (
                            `id` bigint(32) NOT NULL AUTO_INCREMENT,
                            `formation` varchar(255) NOT NULL,
                            `formation_type` tinyint(3) NOT NULL,
                            `hero_id` int(10) NOT NULL,
                            `player_id` int(10) NOT NULL,
                            `player_lev` tinyint(3) unsigned NOT NULL,
                            `vip` tinyint(3) unsigned NOT NULL,
                            `hero_type` tinyint(3) unsigned NOT NULL,
                            `pet_id` int(10) NOT NULL,
                            `is_win` tinyint(1) NOT NULL,
                            `action_time` int(10) NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `hero_id` (`hero_id`),
                            KEY `player_id` (`player_id`),
                            KEY `player_lev` (`player_lev`),
                            KEY `action_time` (`action_time`)
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    );


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'MNSGlogServer:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import MNSG Log Data.';

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
        $start = $this->argument('start');  //game_id
        $server_internal_id = $this->argument('server_internal_id');
        $this->to_db_name = $start.'.'.$server_internal_id;
        if('9999' == $server_internal_id){
            Log::info('MNSGlog test_server');
            return;
        }
        if (!$start) {
            return false;
        }
        if('test' == $start)
        {
            $this->info('TEST MODE!  Only log_arena_rank Will Be Imported.');   //偶尔TEST不影响正常导入
            $this->fields = array('log_economy' => 'id');
        }
        $this->setDB();
        $has_db = false;
        try {
            $this->from_db = DB::connection(self::FROM_DB_NAME);
            $this->to_db = DB::connection($this->to_db_name);
            $this->from_db->disableQueryLog();
            $this->to_db->disableQueryLog();
            $has_db = true;
        } catch (\Exception $e) {
            Log::error($e);
            $has_db = false;
        }
        if ($has_db) {
            $this->startLogCommand($server_internal_id);
        }
        DB::disconnect($this->to_db_name);
        DB::disconnect(self::FROM_DB_NAME);
    }

    private function setDB()
    {
        Config::set("database.connections.".$this->to_db_name, array(   //数据库66.1需要使用默认配置，数据库mysqlmnsg使用同名配置。
            'driver' => 'mysql',
            'host' => Config::get('database.connections.mysql.host'),
            'database' => $this->to_db_name,
            'username' => Config::get('database.connections.mysql.username'),
            'password' => Config::get('database.connections.mysql.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'options' => Config::get('database.connections.mysql.options'),
        ));
    }

    private function startLogCommand($server_internal_id)
    {
        if (!$this->create_lockfile()) {        //Lock
            return false;
        }
        foreach ($this->othertables as $single_table) {
            $this->createTable($single_table);
        }
        foreach($this->fields as $field => $primary_key)            //循环每个数据表
        {
//            $this->info($field . ' => '. $primary_key);
            $this->createTable($field);
            if('log_economy' == $field || 'log_economy_new' == $field) {   //经济日志需要按行读取，并取最近读取的同player_id的行与当前行进行计算
                $this->importEconomy($field, $primary_key, $server_internal_id);
            }else{                          //其他日志按1000行每次进行读取
                $this->import($field, $primary_key, $server_internal_id);
            }
        }
        $this->del_lockfile();                  //Unlock
    }

    //萌娘三国建表语句
    private function createTable($field){
        $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
            Config::get('database.connections.mysql.password'), $this->to_db_name);
        $sql= $this->createTableSqls[$field];
        mysqli_query($con,$sql);
    }

    private function importEconomy($field, $primary_key, $server_internal_id)
    {
        $flag_type = substr($field, 4);

        $player_id_start = $server_internal_id*100000;
        $player_id_end = ($server_internal_id + 1)*100000;

        while (true) {
            //读file_flag
            try {
                $flag = $this->to_db->table($field)->max($primary_key);
            } catch (\Exception $e) {
                Log::error($e);
                return;
            }

            if(NULL == $flag){  //66.1中没有数据，即第一次导入
                $flag = 0;
            }

            //读N条记录
            try {
                $list = $this->from_db->table($field)->where($primary_key, '>', $flag)->whereBetween('player_id', array($player_id_start, $player_id_end))->orderBy($primary_key)->take($this->trans_num)->get();
            } catch (\Exception $e) {
                Log::error($e);
                return;
            }
            //读到0条直接返回，表示上一次刚好读完最后一个。
            if(0 == count($list))
                return;
            //Log::info(var_export($list, true));

            //将N条记录转换为待插入数组，并逐条算出diff，插入到66.1
            foreach ($list as $record)
            {
                $record = (array)$record;
                $last_economy = (array)$this->to_db->table($field)
                    ->where('player_id', $record['player_id'])
                    ->orderBy($primary_key, 'DESC')
                    ->first();
                if('log_economy' == $field){
                  if(NULL != $last_economy){  //如果66.1中有这个玩家以前的经济数据，计算出diff，如果没有，diff不需要填写。
                      $record['diff_mana'] = $record['mana'] - $last_economy['mana'];
                      $record['diff_crystal'] = $record['crystal'] - $last_economy['crystal'];
                      $record['diff_energy'] = $record['energy'] - $last_economy['energy'];
                      $record['diff_arena_coin'] = $record['arena_coin'] - $last_economy['arena_coin'];
                      $record['diff_march_coin'] = $record['march_coin'] - $last_economy['march_coin'];
                  }
                }elseif('log_economy_new' == $field){
                  if(NULL != $last_economy){  //如果66.1中有这个玩家以前的经济数据，计算出diff，如果没有，diff不需要填写。
                      $record['diff_top_coin'] = $record['top_coin'] - $last_economy['top_coin'];
                      $record['diff_guild_coin'] = $record['guild_coin'] - $last_economy['guild_coin'];
                      $record['diff_keys1'] = $record['keys1'] - $last_economy['keys1'];
                  }
                }

                //事务：插入数组+记录位置 若SQL出错则回滚。（只看try{}内语句）
                $this->to_db->beginTransaction();
                try {
                    $this->to_db->table($field)->insert($record);
                } catch (\Exception $e) {
                    Log::error('MNSGlog ' . $field . ' Insert Failed '. var_export($record, true));
                    $this->to_db->rollback();
                    Log::error($e);
                    return;
                }
                try {
                    $this->to_db->table(self::FLAG_TABLE)->where('type', $flag_type)->update(array('position' => time()));
                    $this->to_db->commit();
                } catch (\Exception $e) {
                    Log::error('YYSGlog ' . $flag_type . ' Flag Update Failed ' . var_export($record, true));
                    $this->to_db->rollback();
                    Log::error($e);
                    return;
                }
            }

            //$this->info('field:'.$field.' type:'.$flag_type.' flag:'.$flag->position);
            Log::info('MNSG_import ' . $server_internal_id . $field . ' position: From ' . $flag . ' Count ' .  count($list));

            //若取到的数量不足1000，返回，表示取到结尾处。
            if($this->trans_num > count($list))
                return;
        }
    }

    private function import($field, $primary_key, $server_internal_id){  //循环向后读取1000行，不满1000行时返回
        $flag_type = substr($field, 4);
        $player_id_start = $server_internal_id*100000;
        $player_id_end = ($server_internal_id + 1)*100000;
        while (true) {
            //读54.1中id最大的一条，获取该id
            try {
                $flag = $this->to_db->table($field)->max($primary_key);
            } catch (\Exception $e) {
                Log::error($e);
                return;
            }

            if(NULL == $flag){  //66.1中没有数据，即第一次导入
                $flag = 0;
            }

            //读N条记录
            try {
                $list = $this->from_db->table($field)->where($primary_key, '>', $flag)->whereBetween('player_id', array($player_id_start, $player_id_end))->orderBy($primary_key)->take($this->trans_num)->get(); 
            } catch (\Exception $e) {
                //Log::error($e);
                Log::info($field.':MNSG read Data error!');//不易察觉问题，所以等越南加完log_point和log_formation表后继续使用Log::error($e)
                return;
            }

            //读到0条直接返回，表示上一次刚好读完最后一个。
            if(0 == count($list))
                return;
            //Log::info(var_export($list, true));

            //将N条记录转换为待插入数组
            
            $data = array();
            foreach ($list as $record)
            {
                $record = (array)$record;
                $data[] = $record;
            }

            //事务：插入数组+记录时间 若SQL出错则回滚。（只看try{}内语句）
            if(count($data) > 0){
                $this->to_db->beginTransaction();
                try {
                    $this->to_db->table($field)->insert($data);
                } catch (\Exception $e) {
                    Log::error('MNSGlog ' . $field . ' Insert Failed. From ' . var_export($data[0], true) . 'To ' . var_export(end($data), true));
                    $this->to_db->rollback();
                    Log::error($e);
                    return;
                }
                try {
                    $this->to_db->table(self::FLAG_TABLE)->where('type', $flag_type)->update(array('position' => (time())));
                    $this->to_db->commit();
                } catch (\Exception $e) {
                    Log::error('MNSGlog ' . $flag_type . ' Flag Update Failed. From ' . $flag . ' Count: ' . count($data));
                    $this->to_db->rollback();
                    Log::error($e);
                    return;
                }
            }


            //$this->info('field:'.$field.' type:'.$flag_type.' flag:'.$flag->position);
            Log::info('MNSG_import ' . $server_internal_id . $field . ' position: From ' . $flag . ' Count: ' .  count($data));

            //若取到的数量不足1000，返回，表示取到结尾处。
            if($this->trans_num > count($list))
                return;
        }
    }

    private function create_lockfile()
    {
        //Lock
        $lock_mail_to = array('xguan@xinyoudi.com','xfwang@xinyoudi.com','cli2@xinyoudi.com','jpshi@xinyoudiglobal.com');
        $lock_file = storage_path() . "/import_log_MNSG".$this->to_db_name.".lock";
        if (file_exists($lock_file)) {
            try {
                $data = file_get_contents($lock_file);
                if(time() - strtotime($data) > 8*60*60){
                    Mail::send('lockwarning', array(), function($message) use ($lock_file,$lock_mail_to)
                    {
                        $message->subject($lock_file);
                        $message->from('cs@idgameland.com', 'FileLock');
                        $message->to($lock_mail_to);
                    });
                }
            } catch (Exception $e) {
                Log::info('lock last for at least 4 hours (mail send failed): '.var_export($lock_file, true));
            }
            return false;
        }
        file_put_contents($lock_file, date('Y-m-d H:i:s'));
        return true;
    }

    private function del_lockfile()
    {
        //UnLock
        $lock_file = storage_path() . "/import_log_MNSG".$this->to_db_name.".lock";
        unlink($lock_file);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('start', InputArgument::REQUIRED, 'Any argument'),
            array('server_internal_id', InputArgument::REQUIRED, 'server_internal Id'),
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