<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CountOnlineIntoDB extends Command {

    private $servers = '';
    private $games = array(1, 2, 3, 4, 5, 8, 11, 30, 36, 41, 43, 44, 45, 59, 60, 61, 62, 63, 70);//要统计的游戏game_id,增加的时候需要在login日志脚本中去掉该游戏的计算
    protected $slave_api = '';


	protected $name = 'online:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Count online Log.';

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
		if (!$start) {
		    return false;
		}
		foreach ($this->games as $value) {
			$game = Game::find($value);
			$this->slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
			$this->servers = $this->getUnionServers($value);
			$this->startLogCommand($value);
		}
		

	}

	private function getUnionGame()
	{
	    $server = Table::init(public_path() . '/table/' . 'flsg' . '/server.txt');
	    $server = $server->getData();
	    $server = (array)$server;
	    return  $server;
	}

	private function getUnionServers($game_id)
	{
	    if(11 == $game_id){//可以省掉这一步
	    	 $server = Server::where('server_id', 13)->where('is_server_on',1)->get();
	    	 return $server;
	    }
	    $ser = $this->getUnionGame();
	    //Log::info(var_export($ser, true));
	    $response = $this->slave_api->getUnionServers($game_id, $ser);
	    //Log::info(var_export($response, true));
	    if ($response == "fail") {
	        $server = Server::where('game_id', $game_id)->where('is_server_on',1)->orderBy('server_internal_id', 'desc')->get();
	    }else{
	        $server = Server::whereNotIn('server_internal_id', $response)->where('game_id', $game_id)->where('is_server_on',1)->orderBy('server_internal_id', 'desc')->get();
	    }
	    return $server;
	}

    private function startLogCommand($game_id){
    	$all_server_num = array();
    	$this->servers = json_decode($this->servers);
        while (!empty($this->servers)) {
        	$server = array_shift($this->servers);
        	if(11 == $game_id){
        		$api = PokerGameServerApi::connect($server->api_server_ip, $server->api_server_port);
        		$now_date = date('Y-m-d', time());
        		$response = $api->getOnlineNum($now_date);
        		if(isset($response->player_nums)){
        			$player_nums = $response->player_nums;
        			foreach ($player_nums as $k => $v) {
        				$temp_player_nums[$k] = $v->time;
        			}
        			array_multisort($temp_player_nums, SORT_DESC, $player_nums);

        			$len = count($player_nums)>5 ? 5 : count($player_nums);
        			for($i=0;$i<$len;$i++){
        				$temp = array(
        					'server_internal_id' => $server->server_internal_id,
        					'num' => $player_nums[$i]->number,
        					'largest_time' => (int)(($player_nums[$i]->time)/600)*600,
        				);
        				$all_server_num[] = $temp;
        				unset($temp);
        			}
        			unset($temp_player_nums);unset($player_nums);
        			$all_server_num = array_reverse($all_server_num);
        		}
        	}else{
        		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
        		$response = $api->getOnlinePlayersNumber(); 
        		if(isset($response->num_online)){
        			$temp = array(
        				'server_internal_id' => $server->server_internal_id,
        				'num' => $response->num_online,
        			);
        			$all_server_num[] = $temp;
        			unset($temp);
        		}
        	}

    	    unset($server);
        	unset($api);
        	unset($response);
        }

        if(empty($all_server_num)){
        	return;
        }
    	$result = $this->slave_api->writeOnlineIntodb($game_id, $all_server_num[0]['server_internal_id'], $all_server_num);
		if(isset($result->body) && $result->http_code == 200){
			//Log::info('log_online of '.$server->game_id.'.'.$server->server_internal_id.' num:' . $response->num_online);
		}else{
			Log::info($game_id . ': online Insert Failed');
		}

		unset($all_server_num);
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