<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MobileGameOnline extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'online:mobilegame';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'MobileGame Online';

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
		if('start' == $start){

		}else{
			$this->info('bad argument');
			return;
		}
		$game_ids = Config::get('game_config.mnsggameids');
		foreach ($game_ids as $game_id) {
			$game = Game::find($game_id);
			if(!$game){
				continue;
			}
			$platform_id = $game->platform_id;
			$servers = Server::where('game_id', $game_id)
							->where('is_server_on', 1)
							->where('server_internal_id', '<', 9999)
							->get();
			foreach ($servers as $server) {
				if($server){
					$api = YYSGGameServerApi::connect($server->api_server_ip, $server->api_server_port);
					$response = $api->getonlinenum($server->server_internal_id, $platform_id, $game->game_code);
					if(isset($response->num)){
						$slave_api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
						$result = $slave_api->writeonlinenumtodb($game_id, $server->server_internal_id, $response->num);
						if($result){
						}else{
							Log::info('log_online of '.$game_id.'.'.$server->server_internal_id.' fail!');
						}
					}else{
						Log::info('log_online of '.$game_id.'.'.$server->server_internal_id.'--bad return from game:'.var_export($response, true));
					}
				}
			}
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
			array('start', InputArgument::REQUIRED, 'start'),
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