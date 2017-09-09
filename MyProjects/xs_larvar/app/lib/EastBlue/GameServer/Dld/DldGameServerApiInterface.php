<?php namespace EastBlue\GameServer\Dld;

interface DldGameServerApiInterface {

	public function connect($server_ip, $server_port, $server_dir_id);
}