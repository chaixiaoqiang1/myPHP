<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
class TimingOpenActivities extends Command {
	const TABLE_NAME = 'timing_activities';
	const EMAIL_TO_ZSL = 'xfwang@xinyoudi.com';
	const EMAIL_TO_PANDA = 'xguan@xinyoudi.com';
	private $db = 'eastblue';
	private $now_time = 0;
	private $type_name = array(
		1 => '转盘类活动',
		2 => '假日活动',
		3 => '假日活动设置活动奖励',
	);
	//假日活动type=>设置奖励type
	private $type_to_id = array(
		50 => 1,
		51 => 2,
		52 => 3,
		70 => 21,
		72 => 23,
		73 => 24,
		107 => 58,
	);
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'activities:open';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Timing Open Activities.';

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
		if(!$start){
			return;
		}

		$this->now_time = time();

		$activity_list = DB::table('timing_activities')->where('status',0)
			->whereBetween('start_time',array($this->now_time-60,$this->now_time+600))
			->where('type','<',3)//实际是要排除设置活动奖励的类型
			->first();
		if(NULL != $activity_list){
			//set_time_limit(600);
			$this->openActivities();
		}else{
			return;
		}
		
	}

	private function openActivities()
	{
		
		$activity_list = DB::table('timing_activities')->where('status',0)
			->whereBetween('start_time',array(time()-60,$this->now_time+600))
			->where('type','<',3)
			->orderBy('start_time','ASC')
			->get();

		foreach ($activity_list as $v) {
			if(1 == $v->type){//转盘活动
				$this->turnplate($v);
			}elseif(2 == $v->type){//需要设置奖励的假日活动
				$this->holiday($v);
			}
		}

		//通过上次的开启后判断是否还有未开启的活动
		$activity_list = DB::table('timing_activities')->where('status',0)
			->whereBetween('start_time',array(time()-60,$this->now_time+600))
			->where('type','<',3)
			->first();

		if(NULL != $activity_list){
			if(time()+100 < $this->now_time+600){
				sleep(60);
				$this->openActivities();
			}elseif (time()+60 <= $this->now_time+600) {
				sleep(20);
				$this->openActivities();
			}elseif (time()+40 <= $this->now_time+600) {
				$this->openActivities();
			}else{
				return;
			}
			
		}

	}

	private function sendMail($game_name, $email_to, $mail_data)
	{
		$mail_subject = $game_name . '定时活动';    //主题
		$from_email = 'cs@game168.com.tw';
		Mail::send('timingActivity', $mail_data, function($message) use ($from_email, $email_to, $mail_subject) {
			$message->subject($mail_subject);
			$message->from($from_email, 'cs');
			$message->to($email_to);
		});
	}

	private function turnplate($v)
	{
		$server_ids = explode(",", $v->main_server);
		$params = json_decode($v->params,true);
		/*if(time()>$v->start_time+10){
			$params['payload'] = json_decode($params['payload'],true);
			$params['payload']['open_time'] = time()+10;
			$params['payload'] = json_encode((object)$params['payload']);
		}*/
		$yy_email = User::where('user_id',$v->user_id)->pluck('email');
		$email_to = array($yy_email, self::EMAIL_TO_ZSL,self::EMAIL_TO_PANDA);
		$game = Game::find($v->game_id);
		$mail_data = array();
		$mail_ok_msg = array();
		$mail_error_msg = array();
		$error_servers = array();
		foreach ($server_ids as $server_id) {
			$mail_temp = array();
			$server = Server::find($server_id);
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->timingOpen($v->game_id, $params);
			if(isset($response->result) && $response->result == 'OK'){
				$mail_ok_msg[] = $server->server_name;
			}else{
				Log::info('timing turnplate game_id:'.$v->game_id.' server_id:'.$server_id.var_export($response,true));
				$error_servers[] = $server_id;
				$mail_error_msg[] = $server->server_name;
			}
		}
		if(!empty($mail_ok_msg)){
			DB::table(self::TABLE_NAME)->where('id',$v->id)->update(array('status' => 1));
			$mail_data['mail_ok_msg'] = $this->type_name[$v->type] . ' (' . implode(",", $mail_ok_msg) . ') OK';
		}
		if(!empty($mail_error_msg) && ($v->start_time <= time())){
			$mail_data['mail_error_msg'] = $this->type_name[$v->type] . ' (' .  implode(",", $mail_error_msg) . ') error';
		}
		if(!empty($mail_ok_msg) && !empty($error_servers)){
			$this->insertActivity($error_servers,$v);
		}
		if(!empty($mail_data)){
			Log::info(var_export($mail_data,true));
			$this->sendMail($game->game_name, $email_to, $mail_data);
		}
	}

	private function holiday($v)
	{
		$server_ids = explode(",", $v->main_server);
		$params = json_decode($v->params,true);

		$award_type = isset($this->type_to_id[$v->from_server]) ? $this->type_to_id[$v->from_server] : 0;

		$yy_email = User::where('user_id',$v->user_id)->pluck('email');
		$email_to = array($yy_email, self::EMAIL_TO_ZSL,self::EMAIL_TO_PANDA);
		$game = Game::find($v->game_id);
		$mail_data = array();
		$mail_ok_msg = array();
		$mail_error_msg = array();
		$error_servers = array();
		foreach ($server_ids as $server_id) {
			$mail_temp = array();
			$server = Server::find($server_id);
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->timingOpen($v->game_id, $params);
			if(isset($response->result) && $response->result == 'OK'){
				$mail_ok_msg[] = $server->server_name;
			}else{
				Log::info('timing turnplate holiday:'.$v->game_id.' server_id:'.$server_id.var_export($response,true));
				$error_servers[] = $server_id;
				$mail_error_msg[] = $server->server_name;
			}
		}
		if(!empty($mail_ok_msg)){
			DB::table(self::TABLE_NAME)->where('id',$v->id)->update(array('status' => 1));
			$mail_data['mail_ok_msg'] = $this->type_name[$v->type] . ' (' . implode(",", $mail_ok_msg) . ') OK';
		}
		if(!empty($mail_error_msg) && ($v->start_time <= time())){
			$mail_data['mail_error_msg'] = $this->type_name[$v->type] . ' (' .  implode(",", $mail_error_msg) . ') error';
		}
		if(!empty($mail_ok_msg) && !empty($error_servers)){
			$this->insertActivity($error_servers,$v);
		}
		if(!empty($mail_data)){
			Log::info(var_export($mail_data,true));
			$this->sendMail($game->game_name, $email_to, $mail_data);
			if(!empty($mail_ok_msg) && 0 != $award_type){
				$award_list = DB::table('timing_activities')->where('status',0)
					->whereBetween('start_time',array($v->start_time-1200,$v->start_time+1200))
					->where('game_id',$v->game_id)
					->where('type',3)
					->where('from_server',$award_type)
					->orderBy('start_time','DESC')
					->get();
				foreach ($award_list as $value) {
					$this->holidaySetAward($value);
				}
				
			}
		}	
	} 

	private function holidaySetAward($v)
	{
		$server_ids = explode(",", $v->main_server);
		$params = json_decode($v->params,true);
		$yy_email = User::where('user_id',$v->user_id)->pluck('email');
		$email_to = array($yy_email, self::EMAIL_TO_ZSL,self::EMAIL_TO_PANDA);
		$game = Game::find($v->game_id);
		$mail_data = array();
		$mail_ok_msg = array();
		$mail_error_msg = array();
		$error_servers = array();
		foreach ($server_ids as $server_id) {
			$mail_temp = array();
			$server = Server::find($server_id);
			$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
			$response = $api->timingOpen($v->game_id, $params);
			if(isset($response->type) && isset($response->yunyin_award)){
				$mail_ok_msg[] = $server->server_name;
			}else{
				Log::info('timing holidaySetAward game_id:'.$v->game_id.' server_id:'.$server_id.var_export($response,true));
				$error_servers[] = $server_id;
				$mail_error_msg[] = $server->server_name; 
			}
		}
		
		if(!empty($mail_ok_msg)){
			DB::table(self::TABLE_NAME)->where('id',$v->id)->update(array('status' => 1));
			$mail_data['mail_ok_msg'] = $this->type_name[$v->type] . ' (' . implode(",", $mail_ok_msg) . ') OK';
		}
		if(!empty($mail_error_msg)){
			$mail_data['mail_error_msg'] = $this->type_name[$v->type] . ' (' .  implode(",", $mail_error_msg) . ') error';
		}
		if(!empty($mail_ok_msg) && !empty($error_servers)){
			$this->insertActivity($error_servers,$v);
		}
		if(!empty($mail_data)){
			Log::info(var_export($mail_data,true));
			$this->sendMail($game->game_name, $email_to, $mail_data);
		}

	}

	private function insertActivity($error_servers, $v){
		$activity['game_id'] = $v->game_id;
		$activity['type'] = $v->type;
		$activity['start_time'] = $v->start_time;
		$activity['end_time'] = $v->end_time;
		$activity['created_time'] = $v->created_time;
		$activity['user_id'] = $v->user_id;
		$activity['main_server'] = implode(",", $error_servers);
		$activity['from_server'] = $v->from_server;
		$activity['params'] = $v->params;
		try{
			$res = DB::table(self::TABLE_NAME)->insertGetId($activity);
		}catch(\Exception $e){
			Log::error($e);
		}
		unset($activity);
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
