<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportMNSGDataIntoDB extends Command
{
    private $db_name = '';
    /*private $gameid2dbname = array(     //已弃用
        66 => 'qiqiwu',
        78 => 'qiqiwu_57',
        79 => 'qiqiwu_29',
        82 => 'qiqiwu_38',
        83 => 'qiqiwu',
        );*/
    private $servers = '';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'MNSGlog:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import AllMNSG Log Data.';

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
        $this->game_id = $this->argument('start');
        if (!$this->game_id) {
            return false;
        }
        if(!in_array($this->game_id, Config::get('game_config.mnsggameids'))){
            return false;
        }
        $this->db_name = Config::get('game_config.'.$this->game_id.'.qiqiwu');

        if(!$this->db_name){
            return false;
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
        $this->servers = $this->db->table('server_list')->select('game_id', 'server_internal_id')->where('game_id', $this->game_id)->get();
    }

    private function startLogCommand()
    {

      //  $this->servers = array_reverse($this->servers);//倒序导数据
        while (!empty($this->servers)) {
            $server = array_shift($this->servers);
        
            Artisan::call('MNSGlogServer:import', array(
                'start' => $server->game_id,
                'server_internal_id' => $server->server_internal_id,
            ));
                
            unset($server);
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