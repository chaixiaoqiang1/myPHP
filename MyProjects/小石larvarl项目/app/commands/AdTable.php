<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AdTable extends Command {
    //脚本目前没有再用

    ////////////在此添加游戏////////////////////////////////////
    private $game_list = array(
        1 => '台湾风流三国',
        8 => '台湾女神之剑',
        59 => '台湾风流三国世界版',
    );
    private $platform_currency = array(
        1 => 1,
    );
    /////////////////////////////////////////////////////////

    //////////在此添加收件人///////////////////////////////////
    const EMAIL_HENRY = 'henry@xinyoudi.com';
    const EMAIL_XFWANG = 'xfwang@xinyoudi.com';
    const EMAIL_FROM = 'cs@game168.com.tw';

    private $db_qiqiwu = '';
    private $db_payment = '';
    private $servers = '';
    private $game_id = '';

    private $email_to_list = array(
        self::EMAIL_XFWANG,
        //self::EMAIL_HENRY,
    );
    /////////////////////////////////////////////////////////

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:AdTable';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'report the advertise\'s Effect';

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
        $this->platform_id = $this->argument('platform_id');
        $this->setDB();
        $this->getServers();
        $game = DB::connection($this->db_qiqiwu)
            ->table('game_list')
            ->where('game_id', $this->game_id);
         

        //设置邮件参数
        $from_email = self::EMAIL_FROM; //发件人
        $email_to = $this->email_to_list;   //收件人列表
        $mail_subject = 'Advertise Report Weekly  ' . date('Y-m-d H:i');    //主题
        $report_view = 'adTable';   //邮件的view

        //设置邮件
        $table_rec = array();   //每个游戏都需要向这两个数组中填入数据
        $table_reg = array();
        
        $today = date("N"); //星期一返回1，星期日返回7
        $end_time = strtotime(date("Y/m/d 23:59:59", strtotime("{$today} days ago"))); //取得上周日的时间戳
        $today = $today + 6;
        $start_time = strtotime(date("Y/m/d 00:00:00", strtotime("{$today} days ago")));   //取得上周一的时间戳

        $user = array(
            'game_id' => $this->game_id,      //单平台多游戏需要作为区分
            'start_time' => $start_time, //上周一
            'end_time' => $end_time,    //上周日
            'interval' => 60 * 60 * 24, //按天计算
            'platform_id' => $this->platform_id, 
        );

        $table_rec = $this->getAdTableTableRec($user);//统计第一个要填入数组的值

        $table_reg = $this->getAdTableTableReg($user);//统计第二个要填入数组的值

       
        $data = array(
            'reg' => $table_reg,
            'rec' => $table_rec
        );
        Log::info('AdtableTest...:' . var_export($data,true));
        $this->info(var_export($data, true));

        Mail::send($report_view, $data, function($message) use ($from_email, $email_to, $mail_subject) {
            $message->subject($mail_subject);
            $message->from($from_email, 'cs');
            $message->to($email_to);
        });
        Log::info(var_export('Ad Report Weekly Mail send execute. Email to list:', true).var_export($email_to, true));
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
    }

    private function setServerDB($db_name)
    {
        Config::set("database.connections.{$db_name}", array(
            'driver'    => 'mysql',
            'host'      => Config::get('database.connections.mysql.host'),
            'database'  => $db_name,
            'username'  => Config::get('database.connections.mysql.username'),
            'password'  => Config::get('database.connections.mysql.password'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'options'   => Config::get('database.connections.mysql.options'),
        ));

    }

    private function getServers()
    {
        $this->servers = DB::connection($this->db_qiqiwu)
            ->select("SELECT * FROM server_list WHERE game_id = {$this->game_id} ORDER BY server_id DESC");
        if(54 == $this->game_id)
        {
            $this->servers = DB::connection($this->db_qiqiwu)
                ->select("SELECT * FROM server_list WHERE game_id = {$this->game_id} AND server_internal_id = 1 ORDER BY server_id DESC");
            $string = '';
            foreach($this->servers as $server)
            {
                $string = $string . '--'.$server->server_internal_id;
            }
            $this->info('yysg server list:'.$string);
        }


    }

    private function getAdTableTableRec($user){
        $table_rec =array();
        /*$platform_id = Game::find($user['game_id'])->platform_id;
        $currency_id = Platform::find($platform_id)->default_currency_id;*/
        $currency_id = $this->platform_currency[$user['platform_id']];
        $stat = SlaveUser::on($this->db_qiqiwu)
            ->TableChannelOrder($this->db_payment, $user['start_time'], $user['end_time'],
                 $currency_id, $user['game_id'], $user['platform_id']
            )->get();
        
        $stat = json_decode($stat);

        foreach ($stat as $key => $value) {
           $table_rec[] = array(
                'game' => $this->game_list[$user['game_id']],
                'reg_date' =>date("Y-m-d",$value->created_time),
                'source' => $value->source,
                'u1' => $value->u1,
                'u2' => $value->u2,
                'rec_date' => date("Y-m-d",$value->pay_time),
                'rec_num' => $value->pay_user_count,
                'rec_amount' => $value->total_amount,
                'rec_dollar' => $value->total_dollar_amount,
            );
        }
        return $table_rec;
        //Log::info('AdtableTestRec...:' . var_export($table_rec,true));

    }

    private function getAdTableTableReg($use){
        $table_reg = array();
        foreach ($this->servers as $server) {
            $server_id[] = $server->server_internal_id; 
        }
        $len  = count($server_id);
        //$server1 = Server::find($server_id[0]);
        //$use = array();
        if ($server_id[0] > 0)
        {
            $use['server_internal_id'] = $server_id[0];
        } else
        {
            $use['server_internal_id'] = 0;
        }
        $db_name = $use['game_id'] . '.' . $use['server_internal_id'];
        $resp = SlaveUser::on($this->db_qiqiwu)
        ->adTableDateTW($use['server_internal_id'], $db_name, $use['start_time'], $use['end_time'], $use['interval'], $use['game_id'])
        ->get();
        $bod = json_decode($resp);
//Log::info(var_export($bod,true));die();
        for($k = 0; $k <count($bod); $k ++){
            $arr[$k] = array(
                'a' => 0,
                'b' =>0,
                'c' =>0,
                'd' =>0,
                'e' =>0,
                'f' =>0,
                'g' =>0,
            );
        }

        for ($i = 0; $i < $len ; $i++) {
        //Log::info(var_export(Server::find($server_id[$i]),true));die(); 
            //$server = Server::find($server_id[$i]);
            $user[$i] = array();
            $user[$i]['game_id'] = $use['game_id'];
            $user[$i]['start_time'] = $use['start_time'];
            $user[$i]['end_time'] = $use['end_time'];
            $user[$i]['interval'] = $use['interval'];
            $db_name = $user[$i]['game_id'] . '.' . $server->server_internal_id;
            $response[$i] = SlaveUser::on($this->db_qiqiwu)
            ->adTableDateTW($server_id[$i], $db_name, $user[$i]['start_time'], $user[$i]['end_time'], $user[$i]['interval'], $user[$i]['game_id'])
            ->get();
            $body[$i] = json_decode($response[$i]);
            foreach ($body[$i] as $item) {
                if ($server_id[$i] == 0) {
                    $item->count_formal = 0;
                    $item->count_anonymous = 0;
                    $item->count_anonymous_formal = 0;
                    $item->count_player_formal = 0;
                    $item->count_player_anonymous = 0;
                    $item->count_lev_formal = 0;
                    $item->count_lev_anonymous = 0;
                }
               
            }
            
            $statdata = array();
            
            $blank = new stdClass();
            $blank->ctime = null;
            $blank->source = null;
            $blank->u1 = null;
            $blank->u2 = null;
            $blank->count_formal = null;
            $blank->count_anonymous = null;
            $blank->count_anonymous_formal = null;
            $blank->count_player_formal = null;
            $blank->count_player_anonymous = null;
            $blank->count_lev_formal = null;
            $blank->count_lev_anonymous = null;
            
            for ($j = 0; $j < sizeof($body[$i]); $j ++) {
                if ($j > 0 && $body[$i][$j]->ctime != $body[$i][$j - 1]->ctime) {
                    $statdata[] = $blank;
                }
                $statdata[$j] = new stdClass();
                $statdata[$j]->ctime = 0;

                $statdata[$j]->source = 0;
                $statdata[$j]->u1= '';
                $statdata[$j]->u2= '';
                /*$statdata[$j]->count_formal = 0;
                $statdata[$j]->count_anonymous = 0;
                $statdata[$j]->count_anonymous_formal =0;*/
                
               

                $statdata[$j]->ctime = $body[0][$j]->ctime;
                $statdata[$j]->source = $body[0][$j]->source;
                $statdata[$j]->u1 = isset($body[0][$j]->u1) ? $body[0][$j]->u1 : '';
                $statdata[$j]->u2 = isset($body[0][$j]->u2) ? $body[0][$j]->u2 : '';
                
                $arr[$j]['a']+= $body[$i][$j]->count_formal;
                $arr[$j]['b']+= $body[$i][$j]->count_anonymous;
                $arr[$j]['c']+= $body[$i][$j]->count_anonymous_formal;
                
                $arr[$j]['d']+= $body[$i][$j]->count_player_formal;
                $arr[$j]['e']+= $body[$i][$j]->count_player_anonymous;
                $arr[$j]['f']+= $body[$i][$j]->count_lev_formal;
                $arr[$j]['g']+= $body[$i][$j]->count_lev_anonymous;
            }
            
        }

        for($k = 0; $k< count($statdata); $k++) {

            $statdata[$k]->count_formal = 0;
            $statdata[$k]->count_anonymous= 0;
            $statdata[$k]->count_anonymous_formal = 0;
            $statdata[$k]->count_player_formal = 0;
            $statdata[$k]->count_player_anonymous= 0;
            $statdata[$k]->count_lev_formal = 0;
            $statdata[$k]->count_lev_anonymous = 0;

            $statdata[$k]->count_formal = $arr[$k]['a'];
            $statdata[$k]->count_anonymous = $arr[$k]['b'];
            $statdata[$k]->count_anonymous_formal = $arr[$k]['c'];
            $statdata[$k]->count_player_formal = $arr[$k]['d'];
            $statdata[$k]->count_player_anonymous = $arr[$k]['e'];
            $statdata[$k]->count_lev_formal = $arr[$k]['f'];
            $statdata[$k]->count_lev_anonymous = $arr[$k]['g'];

        }

        foreach ($statdata as $key => $value) {
            $table_reg[] = array(
                'game' => $this->game_list[$use['game_id']],
                'reg_date' => date("Y-m-d",$value->ctime),
                'source' => $value->source,
                'u1' => $value->u1,
                'u2' => $value->u2,
                'reg_formal' => $value->count_formal,
                'reg_anonymous' => $value->count_anonymous,
                'reg_lvlup' => $value->count_anonymous_formal,
                'create_formal' => $value->count_player_formal,
                'create_anonymous' => $value->count_player_anonymous,
                'create_formal_10' => $value->count_lev_formal,
                'create_anonymous_10' => $value->count_lev_anonymous,
            );
        }
        return $table_reg;
        //Log::info('AdtableTestReg...:' . var_export($table_reg,true));
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
            array('game_id', InputArgument::REQUIRED, 'Game ID'),
            array('db_qiqiwu', InputArgument::REQUIRED, 'qiqiwu database'),
            array('db_payment', InputArgument::REQUIRED, 'payment database'),
            array('platform_id', InputArgument::REQUIRED, 'Platform ID'),
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
