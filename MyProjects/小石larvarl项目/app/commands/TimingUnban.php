<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TimingUnban extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'timing:unban';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Timing Unban.';

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

		$start_time = time()-4*86400;
		//检查最近4天是否有封禁的数据
		$unban_list = Operation::whereIn('operation_type',array('ban_game','ban_room'))
			->where('operate_time','>',$start_time)
			->first();
		if(NULL != $unban_list){
			$this->startUnban($start_time);
		}else{
			return;
		}
	}

	private function startUnban($start_time){
		//查询出最近4天被封禁玩家
		$unban_list = Operation::whereIn('operation_type',array('ban_game','ban_room'))
			->where('operate_time','>',$start_time)
			->get();

		if(NULL == $unban_list){
			return;
		}
		$unban_list = json_decode($unban_list);
		foreach ($unban_list as $v) {
			$extra_msg = explode('|', $v->extra_msg);
			//不是永久封禁且是10分钟以内就应该解封的玩家(包括之前没有解封成功的)
			if(isset($extra_msg[1]) && $extra_msg[1] && ($extra_msg[1]+$v->operate_time-time()<=600)){
				//该玩家后面有没有在进行过操作
				$unban_status = Operation::whereIn('operation_type',array($v->operation_type,'un'.$v->operation_type))
			//	->where('operate_time','>',$start_time)
				->where('game_id',$v->game_id)
				->where('operation_id','>',$v->operation_id)
				->where('player_id',$v->player_id)
				->first();
				if($unban_status){
					continue;
				}elseif('ban_game' == $v->operation_type){
					$this->unbanGame($v);
				}elseif('ban_room' == $v->operation_type){
					$this->unbanRoom($v);
				}
			}
		}
	}

	private function unbanGame($v){
		$game_id = $v->game_id;
		$game = Game::find($game_id);
		if('yysg' == $game->game_code){
		    $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
		    $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
		}elseif('mnsg' == $game->game_code){
		    $server = Server::where('game_id', $game_id)->first();
		}else{
			return;
		}
		$api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
		if($v->player_id){
			$player_ids = array((int)$v->player_id);
		    $res = $api->closeAccountId($player_ids, false, $game->platform_id, $game->game_code);
		}elseif($v->player_name){
			$player_names = array($v->player_name);
			$res = $api->closeAccountName($player_names, false, $game->platform_id);
		}
		if(isset($res->errors)){
		    Log::info('game_id:'.$game_id.' timing unban error1');
		}elseif(is_array($res) && (false == $res[0]->is_banned)){
			$extra_msg = explode('|', $v->extra_msg);
		    $server_name = $server->server_name;
		    try{
		    	$operation = Operation::insert(array('operate_time' => time(),
		    	                                     'game_id' => $game_id, 
		    	                                     'player_id' => $v->player_id,
		    	                                     'player_name' => $v->player_name,
		    	                                     'operator' => 'eastblue',
		    	                                     'server_name' => $server_name,
		    	                                     'operation_type' => 'un'.$v->operation_type,
		    	                                     'extra_msg' => '自动解封|'.$extra_msg[1],

		    				));
		    }catch(\Exception $e){
		    	Log::error($e);
		    }
		}else{
		    Log::info('game_id:'.$game_id.' timing unban error2');
		}
	}

	private function unbanRoom($v){
		$game_id = $v->game_id;
		$game = Game::find($game_id);
		if('yysg' == $game->game_code){
		    $server_internal_id = Config::get('game_config.'.$game_id.'.main_server');
		    $server = Server::where('game_id', $game_id)->where('server_internal_id', $server_internal_id)->first();
		}elseif('mnsg' == $game->game_code){
		    $server = Server::where('game_id', $game_id)->first();
		}else{
			return;
		}
		$api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
		$res = $api->bannedTalk(array($v->player_name), array($v->player_id), false, $game->platform_id, $game->game_code);
		if(isset($res->errors)){
		    Log::info('game_id:'.$game_id.' timing unban error3');
		}elseif(is_array($res) && (false == $res[0]->is_banned)){
			$extra_msg = explode('|', $v->extra_msg);
		    $server_name = $server->server_name;
    		try{
    			$operation = Operation::insert(array('operate_time' => time(),
                                             'game_id' => $game_id, 
                                             'player_id' => $v->player_id,
                                             'player_name' => $v->player_name,
                                             'operator' => 'eastblue',
                                             'server_name' => $server_name,
                                             'operation_type' => 'un'.$v->operation_type,
                                             'extra_msg' => '自动解封|'.$extra_msg[1],
                             ));
    		}catch(\Exception $e){
    			Log::error($e);
    		}
		}else{
		    Log::info('game_id:'.$game_id.' timing unban error4');
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
