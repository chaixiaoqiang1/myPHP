<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MailPayCount extends Command {

    private $db_payment = '';
    private $db_qiqiwu = '';
    private $db_retention = '';

    private $games = array(1, 2, 3, 4, 8, 30, 36, 41, 43, 44, 45, 50, 51, 54 , 55, 58, 66, 69, 71, 77, 79, 81, 82, 83, 84, 85);//要统计的游戏game_id
    private $platform_games = '';

    private $w_start_time = '';
    private $w_end_time = '';
    private $w_time = false;

    private $from_email_list = array(
        1  => 'cs@game168.com.tw',
        2  => 'cs@vnwebgame.com',
        3  => 'cs@thwebgame.com',
        4  => 'cs@idwebgame.com',
        8  => 'cs@twwebgame.com',
        30 => 'cs@carolgames.com',
        36 => 'cs@nuthankiem.com',
        41 => 'cs@soathai.com',
        43 => 'cs@walgames.com',
        44 => 'cs@trwebgame.com',
        45 => 'cs@soaindo.com',
        50 => 'cs@idgameland.com',
        51 => 'cs@idgameland.com',
        54 => 'cs@game168.com.tw',
        55 => 'cs@idgameland.com',
        58 => 'cs@idgameland.com',
        64 => 'cs@idgameland.com',
        65 => 'cs@idgameland.com',
        66 => 'cs@game168.com.tw',
        69 => 'cs@carolgames.com',
        71 => 'cs@idgameland.com',
        72 => 'cs@pocketsummoners.com',
        73 => 'cs@idgameland.com',
        74 => 'cs@vnwebgame.com',
        75 => 'cs@thwebgame.com',
        76 => 'game@game.game168.com.tw',
        77 => 'game@game.game168.com.tw',
        79 => 'cs@carolgames.com',
        81 => 'game@game.game168.com.tw',
        82 => 'cs@idgameland.com',
        83 => 'cs@vnwebgame.com',
        84 => 'cs@thwebgame.com',
        85 => 'cs@pocketsummoners.com',
    );

    private $to_email_list = array(
        'xfwang@xinyoudi.com',
    );

    private $subject_list = array(
        1 => '台湾风流三国', 
        2 => '越南风流三国',
        3 => '泰国风流三国',
        4 => '印尼风流三国',
        8 => '台湾女神之剑',
        11 => '德州扑克',
        30 => '英文风流三国',
        36 => '越南女神之剑',
        38 => '印尼神仙道',
        41 => '泰国女神之剑',
        43 => '巴西女神之剑',
        44 => '土耳其女神之剑',
        45 => '印尼女神之剑',
        46 => '台湾黑暗之光',
        47 => '台湾大乱斗',
        48 => '越南大乱斗',
        50 => '印尼大乱斗',
        51 => '印尼君王2',
        52 => '印尼德州扑克',
        53 => '土耳其火影',
        54 => '台湾夜夜三国',
        55 => '印尼忍者之王',
        59 => '世界版台湾风流三国',
        60 => '世界版越南风流三国',
        61 => '世界版泰国风流三国',
        62 => '世界版印尼风流三国',
        63 => '世界版英文风流三国',
        64 => '印尼大闹天宫',
        58 => '印尼宝贝联盟',
        65 => '印尼英雄战魂',
        66 => '台湾萌娘三国',
        67 => '越南宝贝联盟',
        69 => '英文夜夜三国',
        70 => '俄罗斯女神',
        71 => '印尼世界Online',
        72 => '欧美夜夜三国',
        73 => '印尼夜夜三国',
        74 => '越南夜夜三国',
        75 => '泰国夜夜三国',
        76 => '大陆夜夜三国IOS',
        77 => '台湾天上天下',
        79 => '英文萌娘三国',
        80 => '韩国夜夜三国',
        81 => '大陆夜夜三国安卓',
        82 => '印尼萌娘三国',
        83 => '越南萌娘三国',
        84 => '泰国萌娘三国',
        85 => '欧美萌娘三国',
    );

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'payIp:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay Ip Count.';

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
        $this->db_qiqiwu = $this->argument('db_qiqiwu');
        $this->db_payment = $this->argument('db_payment');

        $this->setDB();

        $this->getTime();

        if(false == $this->w_time){
            return;
        }

        $this->platform_games = DB::connection($this->db_qiqiwu)->table('server_list')
            ->groupBy('game_id')
            ->lists('game_id');

        $this->AllAbnormalSendMail();//以上三个邮件合并

    }

    private function AllAbnormalSendMail(){
        foreach ($this->platform_games as $game_id) {
            if(!in_array($game_id, $this->games)){
                continue;
            }
        
            $view = 'emails/all_abnormal_pay';

            $ip_data = $this->AbnormalPayIpData($game_id);

            $user_data = $this->AbnormalPayUserData($game_id);

            $mail_data = array(
                'ip_data' => $ip_data,
                'user_data' => $user_data,
            );
            if(empty($ip_data) && empty($user_data)){
            	Log::info('['.$game_id.'] not data');
            	continue;
            }
            $mail_subject = $this->subject_list[$game_id] . '代充预警';
            if(isset($this->from_email_list[$game_id])){
            	$from_email = $this->from_email_list[$game_id];
            }else{
            	Log::info(var_export('['.$game_id.'] not exist in from_email_list.', true));
            	continue ;
            }
            
            $to_email = $this->to_email_list;
            Mail::send($view, $mail_data, function($message) use ($from_email, $to_email, $mail_subject) {
                $message->subject($mail_subject);
                $message->from($from_email, 'cs');
                $message->to($to_email);
            });
            unset($ip_data);
            unset($user_data);
            unset($mail_data);

        }
    }

    private function AbnormalPayIpData($game_id){
        $order_ip = DB::connection($this->db_payment)->table('pay_order')
            ->where('get_payment',1)
            ->where('game_id',$game_id)
            ->where('pay_time','<=',$this->w_end_time)
            ->groupBy('pay_ip')
            ->havingRaw("num>10 and max(pay_time) between {$this->w_start_time} and {$this->w_end_time}")
            ->orderBy('num','DESC')
            ->selectRaw('pay_ip,COUNT(pay_user_id) as num')
            ->get();

        return $order_ip;
    }

    private function AbnormalPayUserData($game_id){
        $order_user = DB::connection($this->db_payment)->table('pay_order as o')
            ->where('get_payment',1)
            ->where('game_id',$game_id)
            ->where('pay_time','<=',$this->w_end_time)
            ->groupBy('pay_user_id')
            ->havingRaw("num>10 and max(pay_time) between {$this->w_start_time} and {$this->w_end_time}")
            ->orderBy('num','DESC')
            ->selectRaw('pay_user_id,COUNT(pay_ip) as num')
            ->get();

        return $order_user;
    }

    private function getTime(){
        $w_day=date("w",time());
        if($w_day=='1'){
            $cflag = '+0';
        }else{
            $cflag = '-1';
        }
        $mon_time = strtotime(date('Y-m-d',strtotime("$cflag week Monday", time())));  //取得开始时间所在自然周的开始时间
        if(3 == $w_day){
            $this->w_start_time = $mon_time-7*86400;//上周星期一0点
            $this->w_end_time = $mon_time-1;
            $this->w_time = true;
        }
    }

    private function setDB()
    {
        Config::set("database.connections.{$this->db_qiqiwu}", array(
            'driver'    => 'mysql',
            'host'      => Config::get('database.connections.mysql.host'),
            'database'  => $this->db_qiqiwu,
            'username'  => Config::get('database.connections.mysql.username'),
            'password'  => Config::get('database.connections.mysql.password'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'options'   => Config::get('database.connections.mysql.options'),
        ));
        Config::set("database.connections.{$this->db_payment}", array(
            'driver'    => 'mysql',
            'host'      => Config::get('database.connections.mysql.host'),
            'database'  => $this->db_payment,
            'username'  => Config::get('database.connections.mysql.username'),
            'password'  => Config::get('database.connections.mysql.password'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'options'   => Config::get('database.connections.mysql.options'),
        ));
        Config::set("database.connections.{$this->db_retention}", array(
            'driver'    => 'mysql',
            'host'      => Config::get('database.connections.mysql.host'),
            'database'  => $this->db_retention,
            'username'  => Config::get('database.connections.mysql.username'),
            'password'  => Config::get('database.connections.mysql.password'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'options'   => Config::get('database.connections.mysql.options'),
        ));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('db_qiqiwu', InputArgument::REQUIRED, 'Qiqiwu Database'),
            array('db_payment', InputArgument::REQUIRED, 'Payment Database'),
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
