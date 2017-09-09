<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MailToRechargeFail extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'mail:fail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mail To Fail.';

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
        $this->db_qiqiwu = $this->argument('db_qiqiwu');
        $this->db_payment = $this->argument('db_payment');
        $this->setDB();
        $this->getPlayersInTrouble();
    }

    private function setDB()
    {
        Config::set("database.connections.{$this->db_qiqiwu}", 
                array(
                        'driver' => 'mysql',
                        'host' => Config::get('database.connections.mysql.host'),
                        'database' => $this->db_qiqiwu,
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

        Config::set("database.connections.{$this->db_payment}", 
                array(
                        'driver' => 'mysql',
                        'host' => Config::get('database.connections.mysql.host'),
                        'database' => $this->db_payment,
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
 
    private function getPlayersInTrouble()
    {
        
        $platform_server_id = 0; //检查当前游戏所有服务器
        $failed_times = 1; // 储值失败次数>=1
        $game_id = $this->game_id; 
        $end_time = time();
        $start_time = $end_time - 3600 *3 ;
        //Cache::forget('mail_fail_player_ids');
        
        //获取储值成功的玩家的uid
        /*$success_order_uids = array();
        $db = DB::connection($this->db_payment);
        $success_orders = $db->select("select pay_user_id from pay_order where get_payment=1 and pay_time between {$start_time} and {$end_time} group by pay_user_id");
        if($success_orders)
        {
            foreach ( $success_orders as $v )
            {
                if($v->pay_user_id){
                    $success_order_uids[] = "'" . $v->pay_user_id . "'";
                }
            }
        }
        $uids_string = implode($success_order_uids, ",");*/
        //获取储值失败的订单
        $order = PayOrder::on($this->db_payment)
        ->unPayOrder($this->db_qiqiwu, $start_time, $end_time, $failed_times, $game_id, $platform_server_id, '', '')->get();
        /*if($uids_string){
            $order = $order->havingRaw("pay_user_id not in ({$uids_string})")->get();
        } else {
            $order = $order->get();
        }*/
        $num = 0;
        //Log::info(var_export($order, true));
        if($order){
            foreach($order as $item){
                 //发送邮件
                $platform_server_id = $item->server_id;
                //$platform_server_id = 22;
                $player_id = intval($item->player_id);
                //Log::info(var_export($player_id ."===".$platform_server_id, true));
                $player_ids = Cache::get('mail_fail_player_ids');
                $player_array = explode(",", $player_ids);
                $player_array = array_unique($player_array);
                //Log::info(var_export($player_array, true));
                if (in_array($player_id, $player_array)) {
                    continue;
                }
                if ($game_id == 1) { // 台湾三国
                	$title = Lang::get('slave.unpay_title_tw_sanguo');
                	$body = Lang::get('slave.unpay_body_tw_sanguo1') . "<br/><br/>" . Lang::get('slave.unpay_body_tw_sanguo2') . "<br/><br/>" . Lang::get('slave.unpay_body_tw_sanguo3');
                } elseif ($game_id == 8) { //台湾女神
                	$title = Lang::get('slave.unpay_title_tw_nvshen');
                	$body = Lang::get('slave.unpay_body_tw_nvshen1') . "<br/><br/>" . Lang::get('slave.unpay_body_tw_nvshen2') . "<br/><br/>" . Lang::get('slave.unpay_body_tw_nvshen3');
                } elseif ($game_id == 2) { //越南三国
                	$title = Lang::get('slave.unpay_title_vn_sanguo');
                	$body = Lang::get('slave.unpay_body_vn_sanguo1') . "<br/>" . Lang::get('slave.unpay_body_vn_sanguo2') . "<br/>".  Lang::get('slave.unpay_body_vn_sanguo3');
                } elseif ($game_id == 36) { //越南女神
                	$title = Lang::get('slave.unpay_title_vn_nvshen');
                	$body = Lang::get('slave.unpay_body_vn_nvshen1') . "<br/>" . Lang::get('slave.unpay_body_vn_nvshen2') . "<br/>".  Lang::get('slave.unpay_body_vn_nvshen3');
                } elseif ($game_id == 3) { //泰国三国
                	$title = Lang::get('slave.unpay_title_th_sanguo');
                	$body = Lang::get('slave.unpay_body_th_sanguo1') . "<br/>" . Lang::get('slave.unpay_body_th_sanguo2') . "<br/>" . Lang::get('slave.unpay_body_th_sanguo3') .  Lang::get('slave.unpay_body_th_sanguo4') . "<br/>" . Lang::get('slave.unpay_body_th_sanguo5');
                } elseif ($game_id == 41) { //泰国女神
                	$title = Lang::get('slave.unpay_title_th_nvshen');
                	$body = Lang::get('slave.unpay_body_th_nvshen1') . "<br/>" . Lang::get('slave.unpay_body_th_nvshen2') . "<br/>" . Lang::get('slave.unpay_body_th_nvshen3') .  Lang::get('slave.unpay_body_th_nvshen4') . "<br/>" . Lang::get('slave.unpay_body_th_nvshen5');
                } elseif ($game_id == 4) { //印尼三国
                	$title = Lang::get('slave.unpay_title_id_sanguo');
                	$body = Lang::get('slave.unpay_body_id_sanguo1') . "<br/>" . Lang::get('slave.unpay_body_id_sanguo2') . "<br/>" . Lang::get('slave.unpay_body_id_sanguo3');
                } elseif ($game_id == 30 ) { //英文三国
                	$title = Lang::get('slave.unpay_title_en_sanguo');
                	$body = Lang::get('slave.unpay_body_en_sanguo1') . "<br/>" . Lang::get('slave.unpay_body_en_sanguo2') . "<br/>" . Lang::get('slave.unpay_body_en_sanguo3') . "<br/>" . Lang::get('slave.unpay_body_en_sanguo4');
                } elseif ($game_id == 43) { //巴西女神
                	$title = Lang::get('slave.unpay_title_br_nvshen');
                	$body = Lang::get('slave.unpay_body_br_nvshen1') . "<br/>" . Lang::get('slave.unpay_body_br_nvshen2');
                } elseif ($game_id == 44 ) { //土耳其女神
                	$title = Lang::get('slave.unpay_title_tr_nvshen');
                	$body = Lang::get('slave.unpay_body_tr_nvshen1') . "<br/>" . Lang::get('slave.unpay_body_tr_nvshen2') ."<br/>" . Lang::get('slave.unpay_body_tr_nvshen3');
                } elseif ($game_id == 45 ) { //土耳其女神
                    $title = Lang::get('slave.unpay_title_id_nvshen');
                    $body = Lang::get('slave.unpay_body_id_nvshen1') . "<br/>" . Lang::get('slave.unpay_body_id_nvshen2') ."<br/>" . Lang::get('slave.unpay_body_id_nvshen3');
                }  

                //$server = Server::on($this->db_eastblue)->where('game_id', '=', $this->game_id)->where('platform_server_id', '=', $platform_server_id)->first();
                $db_server = DB::connection($this->db_qiqiwu);
                $server = $db_server->select("select * from server_list where game_id = {$game_id} and server_id = {$platform_server_id}");
                if($server){
                    $server = $server[0];
                    $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->dir_id);
                    $response = $api->sendMail($player_id, $title, $body);
                    //Log::info(var_export($response, true));
                    $ids = "";
                    if ($response->result == "OK") {
                        //Log::info(var_export($server->server_name . '---' . $player_id .'---' .$game_id,true));
                        if (Cache::has('mail_fail_player_ids')) {
                            $ids = Cache::get('mail_fail_player_ids');
                            $ids .= ",". $player_id; 
                        }
                        else{
                            $ids .= $player_id.",";
                        }
                        Cache::forget('mail_fail_player_ids');
                        Cache::add('mail_fail_player_ids', $ids, 100000);
                        $num ++;
                    }
                	
                } else {
                }
                 if ($num >= 200) {
                 	break;
                 }
            }
            //Log::info(var_export($num, true));
        }
        $time_arr = getdate($end_time);
        if ($time_arr['hours'] >= 23 || $time_arr['hours'] <= 1) {
            Cache::forget('mail_fail_player_ids');
        }
    }

    protected function getArguments()
    {
        return array(
            array('game_id', InputArgument::REQUIRED, 'Game ID'),
            array('db_qiqiwu', InputArgument::REQUIRED, 'qiqiwu database'),
            array('db_payment', InputArgument::REQUIRED, 'payment database'),
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