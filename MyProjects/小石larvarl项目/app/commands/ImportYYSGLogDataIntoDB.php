<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportYYSGLogDataIntoDB extends Command
{
    const FROM_DB_NAME = 'mysqlyysg';      //来源数据库名
    const FLAG_TABLE = 'file_flag';      //file_flag表名

    private $to_db_name = '54.1';//目标数据库
    private $from_db ;      //来源数据库
    private $to_db ;        //目标数据库
    private $trans_num = 1000; //每次导入条数
    private $fields = array(    //数据表各字段和主键
        'log_create_player' =>'player_id',
        'log_arena_rank'    =>'id',
        'log_ban'           =>'id',
        'log_create_partner'=>'id',
        'log_create_rune'   =>'id',
        'log_economy'       =>'id',
        'log_giftbag'       =>'id',
        'log_giftbox'       =>'id',
        'log_item'          =>'id',
        'log_levelup'       =>'id',
        'log_login'         =>'id',
        'log_partner_powerup_and_evolve' =>'id',
        'log_player_name'   =>'id',
        'log_rune_powerup'  =>'id',
        'log_rune_sell'     =>'id',
        'log_stage'         =>'id',
        'log_summon'        =>'id',
        'log_rune_equip'    =>'id',
        'log_buff'          =>'id',
        'log_guild'         =>'guild_id',
        'log_guild_action'  =>'id',
        'log_guild_boss'    =>'id',
        'log_skill'         =>'id',
        'log_point'         =>'id',
        'log_skin'          =>'id',
        'log_rune_update'   =>'id',
        'log_yuanzheng'   =>'id',
        'log_partner_del' => 'id',

    );
    private function createTable(){
            $con = mysqli_connect(Config::get('database.connections.mysql.host'),Config::get('database.connections.mysql.username'),
                Config::get('database.connections.mysql.password'), $this->to_db_name);
            $sql1= "CREATE TABLE IF NOT EXISTS `log_point` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `player_id` int(10) NOT NULL,
                  `lev` int(4) NOT NULL,
                  `point` int(10) NOT NULL,
                  `action_time` int(10) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `player_id` (`player_id`),
                  KEY `point` (`point`),
                  KEY `action_time` (`action_time`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;";

            $sql2 = "CREATE TABLE IF NOT EXISTS `log_skin` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `player_id` int(11) NOT NULL,
                  `skin_group` int(11) NOT NULL,
                  `price` int(11) NOT NULL,
                  `created_at` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `player_id` (`player_id`),
                  KEY `created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            $sql3 = "CREATE TABLE IF NOT EXISTS `log_rune_update` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `player_id` int(10) NOT NULL,
                  `rune_id` int(10) NOT NULL,
                  `mid` int(10) NOT NULL,
                  `time` int(10) NOT NULL,
                  `old_attr` text NOT NULL,
                  `new_attr` text NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `player_id` (`player_id`,`mid`,`time`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
            $sql4 = "CREATE TABLE IF NOT EXISTS `log_yuanzheng` (
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `player_id` int(10) NOT NULL,
                  `table_id` int(10) NOT NULL,
                  `is_unlimited` tinyint(3) NOT NULL,
                  `yuanzheng_coin` int(10) NOT NULL,
                  `point` int(10) NOT NULL,
                  `battle_id` int(10) NOT NULL,
                  `yuanzheng_type` tinyint(3) NOT NULL,
                  `yuanzheng_round_id` int(10) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `player_id` (`player_id`,`table_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            $sql5 = "CREATE TABLE IF NOT EXISTS `log_partner_del`(
                  `id` int(10) NOT NULL AUTO_INCREMENT,
                  `player_id` int(10) NOT NULL,
                  `mid` int(10) NOT NULL,
                  `partner_id` int(10) NOT NULL,
                  `time` int(10) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `player_id` (`player_id`,`mid`,`time`)
            )ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            mysqli_query($con,$sql1);
            mysqli_query($con,$sql2);
            mysqli_query($con,$sql3);
            mysqli_query($con,$sql4);
            mysqli_query($con,$sql5);
    }

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'YYSGlog:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import YYSG Log Data.';

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
        $start = $this->argument('start');
        if (!$start) {
            return false;
        }
        if(!in_array($start, Config::get('game_config.yysggameids'))){
            return false;
        }
 
        $this->to_db_name = Config::get('game_config.'.$start.'.database');

        // if('test' == $start)
        // {
        //     $this->info('TEST MODE!  Only log_arena_rank Will Be Imported.');   //偶尔TEST不影响正常导入
        //     $this->fields = array('log_economy' => 'id');
        // }
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
            $this->createTable();
            $this->startLogCommand($start);
        }
        DB::disconnect($this->to_db_name);
        DB::disconnect(self::FROM_DB_NAME);
    }

    private function setDB()
    {
        Config::set("database.connections.".$this->to_db_name, array(   //数据库54.1需要使用默认配置，数据库mysqlyysg使用同名配置。
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

    private function startLogCommand($start)
    {
        if(54 == $start){   //检测游戏后台的数据库，所以只从台湾检测
            $this->check_import('log_create_player');  //检测玩家创建表是否正常更新
        }
        if (!$this->create_lockfile()) {        //Lock
            return false;
        }
        foreach($this->fields as $field => $primary_key)            //循环每个数据表
        {
//            $this->info($field . ' => '. $primary_key);
            if('log_create_player' == $field){//log-create_player表需要区分平台导入
                $this->importCreatePlayer($field, $primary_key, $start);
            }elseif('log_economy' == $field) {   //经济日志需要按行读取，并取最近读取的同player_id的行与当前行进行计算
                $this->importEconomy($field, $primary_key, $start);
            }else{                          //其他日志按1000行每次进行读取
                $this->import($field, $primary_key, $start);
            }
        }
        $this->del_lockfile();                  //Unlock
    }

    private function importCreatePlayer($field, $primary_key, $start){  //循环向后读取1000行，不满1000行时返回
        $flag_type = substr($field, 4);
        $platform_id = Config::get('game_config.'.$start.'.platform_id');
        while (true) {

            try {   //读54.1中id最大的一条，获取该id
                $flag = $this->to_db->table($field)->max($primary_key);
            } catch (\Exception $e) {
                Log::error($e);
                return;
            }
            if(NULL == $flag){  //54.1中没有数据，即第一次导入,log_create_player主键从0开始
                $flag = 0;
            }

            //读N条记录
            try {
                $list = $this->from_db->table($field)->where('platform_id', $platform_id)->where($primary_key, '>', $flag)->orderBy($primary_key)->take($this->trans_num)->get();
            } catch (\Exception $e) {
                Log::error($e);
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
                $record =(array)$record;
                $data[] = $record;
            }

            //事务：插入数组+记录时间 若SQL出错则回滚。（只看try{}内语句）
            if(!empty($data)){
                $this->to_db->beginTransaction();
                try {
                    $this->to_db->table($field)->insert($data);
                } catch (\Exception $e) {
                    Log::error('YYSGlog ' . $field . ' Insert Failed. From ' . var_export($data[0], true) . 'To ' . var_export(end($data), true));
                    $this->to_db->rollback();
                    Log::error($e);
                    return;
                }
                try {
                    $this->to_db->table(self::FLAG_TABLE)->where('type', $flag_type)->update(array('position' => (time())));
                    $this->to_db->commit();
                } catch (\Exception $e) {
                    Log::error('YYSGlog ' . $flag_type . ' Flag Update Failed. From ' . $flag . ' Count: ' . count($data));
                    $this->to_db->rollback();
                    Log::error($e);
                    return;
                }
            }

            //$this->info('field:'.$field.' type:'.$flag_type.' flag:'.$flag->position);
            Log::info('YYSG_import ' . $field . ' position: From ' . $flag . ' Count: ' .  count($data));

            //若取到的数量不足1000，返回，表示取到结尾处。
            if($this->trans_num > count($list))
                return;
        }
    }

    private function importEconomy($field, $primary_key, $start)
    {
        $flag_type = substr($field, 4);
        $platform_id = Config::get('game_config.'.$start.'.platform_id');
        while (true) {
            //读file_flag
            try {
                $flag = $this->to_db->table($field)->max($primary_key);
            } catch (\Exception $e) {
                Log::error($e);
                return;
            }
            if(NULL == $flag){  //54.1中没有数据，即第一次导入
                $flag = 0;
            }

            //读N条记录
            try {
                $list = $this->from_db->table($field.' as table')->select('table.*')
                    ->join('log_create_player as lcp', function($join) use ($platform_id){
                        $join->on('table.player_id', '=', 'lcp.player_id')
                            ->where('lcp.platform_id', '=', $platform_id);
                    })->where('table.'.$primary_key, '>', $flag)->orderBy('table.'.$primary_key)->take($this->trans_num)->get();
            } catch (\Exception $e) {
                Log::error($e);
                return;
            }

            //读到0条直接返回，表示上一次刚好读完最后一个。
            if(0 == count($list))
                return;
            //Log::info(var_export($list, true));

            //将N条记录转换为待插入数组，并逐条算出diff，插入到54.1
      
            foreach ($list as $record)
            {
                    $record = (array)$record;        
                    $last_economy = (array)$this->to_db->table($field)
                        ->where('player_id', $record['player_id'])
                        ->orderBy($primary_key, 'DESC')
                        ->first();
                    if(NULL != $last_economy){  //如果54.1中有这个玩家以前的经济数据，计算出diff，如果没有，diff不需要填写。
                        $record['diff_mana'] = $record['mana'] - $last_economy['mana'];
                        $record['diff_crystal'] = $record['crystal'] - $last_economy['crystal'];
                        $record['diff_social'] = $record['social'] - $last_economy['social'];
                        $record['diff_energy'] = $record['energy'] - $last_economy['energy'];
                        $record['diff_invitation'] = $record['invitation'] - $last_economy['invitation'];
                        $record['diff_glory'] = $record['glory'] - $last_economy['glory'];
                        $record['diff_point'] = $record['point'] - $last_economy['point'];
                        if(isset($record['guild_coin'])){
                            $record['diff_guild_coin'] = $record['guild_coin'] - $last_economy['guild_coin'];
                        }
                    }

                    //事务：插入数组+记录位置 若SQL出错则回滚。（只看try{}内语句）
                    $this->to_db->beginTransaction();
                    try {
                        $this->to_db->table($field)->insert($record);
                    } catch (\Exception $e) {
                        Log::error('YYSGlog ' . $field . ' Insert Failed '. var_export($record, true));
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
            Log::info('YYSG_import ' . $field . ' position: From ' . $flag . ' Count ' .  count($list));

            //若取到的数量不足1000，返回，表示取到结尾处。
            if($this->trans_num > count($list))
                return;
        }
    }

    private function import($field, $primary_key, $start){  //循环向后读取1000行，不满1000行时返回
        $flag_type = substr($field, 4);
        $platform_id = Config::get('game_config.'.$start.'.platform_id');
        //读54.1中id最大的一条，获取该id

        while (true){
            try {
                $flag = $this->to_db->table($field)->max($primary_key);
            } catch (\Exception $e) {
                Log::error($e);
                return;
            }
            if(NULL == $flag){  //54.1中没有数据，即第一次导入
                $flag = 0;
            }

            //读N条记录
            try {
                if(in_array($field, array('log_giftbox','log_giftbag'))){   //特殊表，没有player_id只有to_player_id
                     $list = $this->from_db->table($field.' as table')->select('table.*')
                        ->join('log_create_player as lcp', function($join) use ($platform_id){
                            $join->on('table.to_player_id', '=', 'lcp.player_id')
                                ->where('lcp.platform_id', '=', $platform_id);
                        })->where('table.'.$primary_key, '>', $flag)->orderBy('table.'.$primary_key)->take($this->trans_num)->get();
                }elseif(in_array($field, array('log_guild'))){ //特殊表，没有player_id只有created_player_id
                    $list = $this->from_db->table($field.' as table')->select('table.*')
                        ->join('log_create_player as lcp', function($join) use ($platform_id){
                            $join->on('table.created_player_id', '=', 'lcp.player_id')
                                ->where('lcp.platform_id', '=', $platform_id);
                        })->where('table.'.$primary_key, '>', $flag)->orderBy('table.'.$primary_key)->take($this->trans_num)->get();
                }else{
                    $list = $this->from_db->table($field.' as table')->select('table.*')
                        ->join('log_create_player as lcp', function($join) use ($platform_id){
                            $join->on('table.player_id', '=', 'lcp.player_id')
                                ->where('lcp.platform_id', '=', $platform_id);
                        })->where('table.'.$primary_key, '>', $flag)->orderBy('table.'.$primary_key)->take($this->trans_num)->get();
                }
            } catch (\Exception $e) {
                Log::error($e);
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
            if(!empty($data)){           
                $this->to_db->beginTransaction();
                try {
                    $this->to_db->table($field)->insert($data);
                } catch (\Exception $e) {
                    Log::error('YYSGlog ' . $field . ' Insert Failed. From ' . var_export($data[0], true) . 'To ' . var_export(end($data), true));
                    $this->to_db->rollback();
                    Log::error($e);
                    return;
                }
                try {
                    $this->to_db->table(self::FLAG_TABLE)->where('type', $flag_type)->update(array('position' => (time())));
                    $this->to_db->commit();
                } catch (\Exception $e) {
                    Log::error('YYSGlog ' . $flag_type . ' Flag Update Failed. From ' . $flag . ' Count: ' . count($data));
                    $this->to_db->rollback();
                    Log::error($e);
                    return;
                }
            }

            //$this->info('field:'.$field.' type:'.$flag_type.' flag:'.$flag->position);
            Log::info('YYSG_import ' . $field . ' position: From ' . $flag . ' Count: ' .  count($data));

            //若取到的数量不足1000，返回，表示取到结尾处。
            if($this->trans_num > count($list))
                return;
        }
    }

    private function create_lockfile()
    {
        //Lock
        $lock_mail_to = array('xguan@xinyoudi.com','xfwang@xinyoudi.com','cli2@xinyoudi.com','jpshi@xinyoudiglobal.com');
        $lock_file = storage_path() . "/import_log_YYSG_".$this->to_db_name.".lock";
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
        $lock_file = storage_path() . "/import_log_YYSG_".$this->to_db_name.".lock";
        unlink($lock_file);
    }

    private function check_import($table_name = 'log_create_player'){   //检查表是否长时间未更新
        $primary_key = 'player_id';
        $time_key = 'created_time';
        $last_time = $this->from_db->table($table_name)->orderBy($primary_key, 'desc')->first();
        if($last_time){
            $time_past = time() - $last_time->$time_key;
        }else{  //空表
            $time_past = 0;
        }
        if($time_past > 1800){  //半个小时
            $this->send_warning_mail($table_name);
        }else{
            Log::info('YYSG--Table:'.$table_name.' data in time.');
        }
    }

    private function send_warning_mail($table_name){    //夜夜三国报警邮件发送
        $mail_to = array(
            'xguan@xinyoudi.com',
            'shzhang@xinyoudi.com',
            'lyzhu@xinyoudi.com',
            'bjhan@xinyoudi.com',
            'nliu@xinyoudi.com',
            'hlcai@xinyoudi.com',
            'gwguo@xinyoudiglobal.com',
            );
        $mail_subject = 'YYSG--Table:'.$table_name;
        Mail::send('YYSGLogWarning', array(), function($message) use ($mail_subject, $mail_to) {
                $message->subject($mail_subject);
                $message->from('cs@idgameland.com', 'YYSG-DataBase-Warning');
                $message->to($mail_to);
            });
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