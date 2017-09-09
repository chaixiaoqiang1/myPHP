<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class OpenServerCommand extends Command {

	/**
	 * The console command name.
	 * 游戏有时区的差异
	 * 定时开服脚本，所开的服务器开服时间需要大于当前时间
	 * 提前5分钟开启网站游戏服务器列表与支付服务器列表，同时重置游戏服务器
	 * 发送邮件告知结果，请客服及时测试
	 * @var string
	 */
	protected $name = 'server:open';

    protected $game_list = array(38, 46, 59, 60, 61, 62);//不需要进行重置服务器的名单

    protected $games_not_auto_open = array(67);	//不需要执行开服操作的游戏

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '定时开服脚本.';

	const SPEC_FIX = 'XYD9061';

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
		set_time_limit(0);
		$start = $this->argument('start');
		if ($start != 'start') {
			return;
		}
		$this->getServers();
		$this->getMonthCardServers();
	}

	private function getServers()
	{
		$servers = Server::where('is_server_on', 0)->get();
		foreach ($servers as $v) {      //循环每个还没有开启的服务器
			if(in_array($v->game_id, $this->games_not_auto_open)){
				continue;
			}
			$game = Game::find($v->game_id);
		    if(!$game){
		    	continue;
		    }
			$platform = Platform::find($game->platform_id);
			$region = Region::find($platform->region_id);
			$timezone = $region->timezone;
			date_default_timezone_set($timezone);
			$time_now = time();
			$open_server_time = $v->open_server_time;

			$diff_time = $open_server_time - $time_now;

			if ($diff_time >= 0 && $diff_time <= 3600) {    //设定的开服时间在未来一个小时以内的
				Log::info('Going to open server: game_id='.$v->game_id.' and server_name='.$v->server_name);
				$this->initGameServer($v);
			}
		}
	}

	private function getMonthCardServers()
	{
	   $month_card_servers = Server::where('use_for_month_card', 1)->get();
		foreach ($month_card_servers as $v) {
		    $game = Game::find($v->game_id);
		    if($v->game_id == 38) { //神仙道use_for_month_card固定位0，加上这句防止出错
		    	continue;
		    }
		    $platform = Platform::find($game->platform_id);
		    $region = Region::find($platform->region_id);
		    $timezone = $region->timezone;
		    date_default_timezone_set($timezone);
		    $time_now = time();
		    $open_server_time = $v->open_server_time;
		      //月卡开启7天之后关闭
			$diff_time_month_card = $open_server_time + 7*24*3600 - $time_now;
			if ($v->use_for_month_card == 1 && $diff_time_month_card >= 0 && $diff_time_month_card <= 3600) {
				$this->closeMonthCard($v);
			}
		}
	}
	private function initGameServer($server)
	{
	    if(!$this->init_games($server->game_id)) { //代理的游戏不需要重置服务器  世界版风流三国只需要充值一次，这里仅重置英文版
	    	Log::info('Do not have to init server: '.$server->server_track_name);
	        $this->openGameServer($server);
	    } else {	//要修改一下，大量的游戏并不需要重置服务器
	    	Log::info('Going to init server: '.$server->server_track_name);
	        $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
            $response = $api->initGameServer();     //调用服务器API重置服务器
	        //$s = var_export($response,true);
	        //Log::info($s);
	        if (isset($response->result) && $response->result == 'OK') {
	            sleep(60);      //重置成功后等待60秒
	            $this->openGameServer($server);
	        } else {
	            $this->sendMail(Lang::get('server.script_init_server_error'), $server);
	        }
	    }
	}

	private function init_games($game_id){
		$game = Game::find($game_id);
		if($game){
			if(in_array($game->game_code, array('flsg', 'nszj', 'dld'))){	//目前只有这三类游戏需要重置
				if(in_array($game->game_id, $this->game_list)){
					return false;
				}else{
					return true;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	private function openGameServer($server)
	{
		$server->server_track_name = str_replace(self::SPEC_FIX, '', $server->server_track_name);
		$server->is_server_on = 1;
		$server->on_recharge = 1;   //去掉服务器名的前缀，服务器状态开启，重置服务开启
		if ($server->save()) {      //在运营平台开启服务器成功的话
			$platform = Game::find($server->game_id)->platform;
			$api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
			$params = array(
				'server_id' => $server->platform_server_id,
				'is_server_on' => 1,
				'server_track_name' => $server->server_track_name
			);
			$response = $api->updatePlatformServer($params);    //开启官网的游戏服务器
			if ($response->http_code != 200) {
				$this->sendMail(Lang::get('server.script_open_server_qiqiwu_error'), $server);
				return;
			}
			$api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
			$params = array(
				'server_id' => $server->platform_server_id,
				'on_recharge' => 1,
			);

			$response = $api->updatePaymentServer($params);     //开启官网的支付服务器
			if ($response->http_code != 200) {
				$this->sendMail(Lang::get('server.script_open_server_qiqiwu_error'), $server);
			} else {
				$this->sendMail(Lang::get('server.script_open_server_success'), $server);
			}
		} else {
				$this->sendMail(Lang::get('server.script_open_server_platform_error'), $server);
		}
	}

	private function closeMonthCard($server)
	{
	      $api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		  $response = $api->initGameServer();
	      if (isset($response->result) && $response->result == 'OK') {
			 sleep(60);
			 $server->use_for_month_card = 0;
			 if ($server->save()) {
			     $platform = Game::find($server->game_id)->platform;
			     $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
			     $params = array(
			             'use_for_month_card' => 0,
			     );
			     
			     $response = $api->updatePaymentServer($params);
			     if ($response->http_code != 200) {
			         $this->sendMail(Lang::get('server.script_close_month_card_error'), $server);
			     } else {
			         $this->sendMail(Lang::get('server.script_close_month_card_success'), $server);
			     }
			 }
		  } else {
			 $this->sendMail(Lang::get('server.script_init_server_error'), $server);
		  }
		 
	}
	private function sendMail($msg, $server)
	{
		$game = Game::find($server->game_id);
		$mail_msg = $game->game_name . '-' . $server->server_name . ' ' . $msg;
		// Log::info($mail_msg);
		$mail_data = array(
			'msg' => $mail_msg
		);
		$mail_subject = $mail_msg;
		$from_email = 'cs@game168.com.tw';
		$email_to = 'yy@xinyoudi.com';
		Mail::send('openserveralert', $mail_data, function($message) use ($from_email, $email_to, $mail_subject) {
			$message->subject($mail_subject);
			$message->from($from_email, 'cs');
			$message->to($email_to);
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
			array('start', InputArgument::REQUIRED, 'Start...'),
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