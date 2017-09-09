<?php 

class SlaveOpenServerController extends \BaseController {

	const SCRIPT_PATH = '/var/job/setup_new_server.sh';
	protected $game_id = 0;
	protected $server_internal_id = 0;
	protected $server_ip = '';
	protected $api_dir_id = '';
	protected $open_server_time = 0;

	public function __construct()
	{
		$this->game_id = (int)Input::get('game_id');
		$this->server_internal_id = (int)Input::get('server_internal_id');
		$this->server_ip = Input::get('server_ip');
		$this->api_dir_id = (int)Input::get('api_dir_id');
		$this->open_server_time = (int)Input::get('open_server_time');
	}

	public function execScript()
	{
		exec(self::SCRIPT_PATH . " {$this->server_internal_id} {$this->game_id} {$this->server_ip} {$this->api_dir_id} {$this->open_server_time}", $output, $retun_var);
	}

}