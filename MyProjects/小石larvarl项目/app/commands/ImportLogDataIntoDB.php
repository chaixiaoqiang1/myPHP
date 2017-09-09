<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportLogDataIntoDB extends Command
{
    private $db_name = 'qiqiwu';
    private $servers = '';
    private $db = '';
    private $bak = '';
    ////////////////需要导入数据的游戏的  game_id 填入数组  数组命名反了，请忽略
    private $game_ban_player = array(1, 2, 3, 4, 5, 8, 11, 30, 36 , 41, 43, 44, 45, 47, 48, 49, 50, 59, 60, 61, 62, 63, 70);       //女神+三国+德扑+大乱斗
    private $game_ban_lvlup = array(1, 2, 3, 4, 5, 8, 11, 30, 36 , 41, 43, 44, 45, 47, 48, 49, 50, 59, 60, 61, 62, 63, 70);  //女神+三国+德扑+大乱斗
    private $game_ban_login = array(1, 2, 3, 4, 5, 8, 11, 30, 36, 41, 43, 44, 45, 47, 48, 49, 50, 59, 60, 61, 62, 63, 70);     //女神+三国+德扑+大乱斗
    private $game_ban_economy = array(8, 11, 36, 41, 43, 44, 45, 47, 48, 49, 50, 70);        //女神+德扑+大乱斗
    private $game_ban_item = array(1, 2, 3, 4, 5, 8, 11, 30, 36, 41, 43, 44, 45, 47, 48, 49, 50, 59, 60, 61, 62, 63, 70);  //女神+三国+德扑+大乱斗
    private $game_ban_dragon = array(1, 2, 3, 4, 5, 59, 60, 61, 62, 63);   //导入风流三国
    private $game_ban_exp = array(1, 2, 3, 4, 5, 8, 30, 36, 40, 41, 43, 44, 45, 59, 60, 61, 62, 63, 70); //女神+三国
    private $game_ban_mingge = array(1, 2, 3, 4, 5, 30, 59, 60, 61, 62, 63);   //导入三国
    private $game_ban_lonely = array(8, 36, 41, 45, 70);   //导入女神
    private $game_ban_flsg_economy = array(1, 2, 3, 4, 5, 30, 59, 60, 61, 62, 63);   //导入三国经济日志
    private $game_ban_rings = array(1, 2, 3, 4, 5, 30, 59, 60, 61, 62, 63);   //导入三国戒指经验日志
    private $game_ban_flsg_ranks = array(1, 2, 3, 4, 5, 30, 59, 60, 61, 62, 63);//三国导入神树大乱斗排行日志的游戏
    private $game_ban_mergegem = array(1, 2, 3, 4, 5, 30, 59, 60, 61, 62, 63);//风流三国将魂日志
    private $game_ban_operation = array(1, 2, 3, 4, 5, 8, 30, 36, 40, 41, 43, 44, 45, 59, 60, 61, 62, 63, 70);//风流三国+女神
//    private $game_ban_game = array(38, 46, 51, 54, 55, 56, 11);  //徳扑
    //38神仙道 46黑暗之光 51君王 54夜夜三国 55忍者之王 56地下城堡
    private $game_ban_remove = array(1, 2, 3, 4, 5, 8, 30, 36, 40, 41, 43, 44, 45, 59, 60, 61, 62, 63, 70);//风流三国+女神+德扑
    private $game_ban_crowd_funding = array(1, 2, 3, 4, 5, 30);  //风流三国众筹转盘日志
    private $game_ban_gold_rolling = array(1, 2, 3, 4, 5, 30);  //风流三国
    private $game_ban_shake_dice = array(1,2);  //风流三国
    /////////////////////////////////////////////////////////

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'log:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import All Log Data.';

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
        $start = $this->argument('start');
        $db_name = $this->argument('db_name');
        $this->bak = $this->argument('bak');
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
            $this->startLogCommand();
        }
        DB::disconnect($this->db_name);
    }

    private function setDB()
    {
        Config::set("database.connections.{$this->db_name}", array(
            'driver' => 'mysql',
            'host' => Config::get('database.connections.mysql.host'),
            'database' => $this->db_name,
            'username' => Config::get('database.connections.mysql.username'),
            'password' => Config::get('database.connections.mysql.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'options' => Config::get('database.connections.mysql.options'),
        ));
    }

    private function getServers()
    {
        $this->servers = $this->db->table('server_list')->select('server_id', 'game_id', 'server_internal_id')->where('is_server_on',1)->orWhereRaw("(server_id=170 and game_id=1)")->get();
    }

    private function startLogCommand()
    {
        $backup_file = "/data/logbackup/backup.lock";   //奇修正在整理备份文件，暂时不导入。 backup.lock文件由奇修管理。
        if (file_exists($backup_file)) {
            return;
        }

        $this->servers = array_reverse($this->servers);//倒序导数据
        while (!empty($this->servers)) {
            //Log::info(var_export($this->servers,true));
            $server = array_shift($this->servers);
           // Log::info(var_export($server,true));die();
            
            if ($this->create_lockfile('player', $server->server_id)) {

                if (in_array($server->game_id, $this->game_ban_player)) {
                    Artisan::call('player:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('player', $server->server_id);
            }


            if ($this->create_lockfile('lvlup', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_lvlup)) {
                    Artisan::call('levelup:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('lvlup', $server->server_id);
            }


            if ($this->create_lockfile('login', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_login)) {
                    Artisan::call('login:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('login', $server->server_id);
            }


            if ($this->create_lockfile('economy', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_economy)) {
                    Artisan::call('economy:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('economy', $server->server_id);
            }


            if ($this->create_lockfile('item', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_item)) {
                    Artisan::call('item:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('item', $server->server_id);
            }


            if ($this->create_lockfile('exp', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_exp)) {
                    Artisan::call('exp:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('exp', $server->server_id);
            }


            if ($this->create_lockfile('dragon', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_dragon)) {
                    Artisan::call('dragon:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('dragon', $server->server_id);
            }


            if ($this->create_lockfile('game', $server->server_id)) {
                if ($server->game_id == 11) {       //只有徳扑才需要导入 game_log。
                    Artisan::call('game:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('game', $server->server_id);
            }

            if ($this->create_lockfile('playcount', $server->server_id)) {
                if ($server->game_id == 11) {       //只有徳扑才需要导入 playcount
                    Artisan::call('playcount:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('playcount', $server->server_id);
            }


            if ($this->create_lockfile('mingge', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_mingge)) {
                    Artisan::call('mingge:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('mingge', $server->server_id);
            }


            if ($this->create_lockfile('lonely', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_lonely)) {
                    Artisan::call('lonely:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('lonely', $server->server_id);
            }


            if ($this->create_lockfile('flsg_economy', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_flsg_economy)) {
                    Artisan::call('flsgEconomy:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('flsg_economy', $server->server_id);
            }

            if ($this->create_lockfile('rings', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_rings)) {
                    Artisan::call('rings:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('rings', $server->server_id);
            }

            if ($this->create_lockfile('flsg_ranks', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_flsg_ranks)) {
                    Artisan::call('flsgRanks:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('flsg_ranks', $server->server_id);
            }

            if ($this->create_lockfile('mergegem', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_mergegem)) {
                    Artisan::call('mergegem:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('mergegem', $server->server_id);
            }

            if ($this->create_lockfile('operation', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_operation)) {
                    Artisan::call('operations:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('operation', $server->server_id);
            }

            if ($this->create_lockfile('remove', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_remove)) {
                    Artisan::call('remove:file', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('remove', $server->server_id);
            }

            if ($this->create_lockfile('crowd_funding', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_crowd_funding)) {
                    Artisan::call('crowdFunding:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('crowd_funding', $server->server_id);
            }

            if ($this->create_lockfile('gold_rolling', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_gold_rolling)) {
                    Artisan::call('goldRolling:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('gold_rolling', $server->server_id);
            }

            if ($this->create_lockfile('shake_dice', $server->server_id)) {
                if (in_array($server->game_id, $this->game_ban_shake_dice)) {
                    Artisan::call('shakeDice:import', array(
                        'game_id' => $server->game_id,
                        'server_id' => $server->server_internal_id,
                        'bak' => $this->bak
                    ));
                }
                $this->del_lockfile('shake_dice', $server->server_id);
            }


            unset($server);
        }
    }

    private function create_lockfile($import = 'import', $server_id)
    {
        //Log::info(var_export($import . $server_id,true));die();
        //Lock
        $lock_mail_to = array('xguan@xinyoudi.com','xfwang@xinyoudi.com','cli2@xinyoudi.com','jpshi@xinyoudiglobal.com');
        $lock_file = storage_path() . "/import_log_{$this->db_name}_{$import}_{$server_id}.lock";
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

        $lock_file2 = "/data/logbackup/import_log_{$this->db_name}_{$import}_{$server_id}.lock";
        file_put_contents($lock_file, date('Y-m-d H:i:s'));
        file_put_contents($lock_file2, date('Y-m-d H:i:s'));

        if(file_exists($lock_file)){
            return true;
        }else{
            return false;
        }
        
    }

    private function del_lockfile($import = 'import', $server_id)
    {
        //Lock
        $lock_file = storage_path() . "/import_log_{$this->db_name}_{$import}_{$server_id}.lock";
        $lock_file2 = "/data/logbackup/import_log_{$this->db_name}_{$import}_{$server_id}.lock";

        unlink($lock_file);
        unlink($lock_file2);
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
            array('db_name', InputArgument::OPTIONAL, 'Database Name'),
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