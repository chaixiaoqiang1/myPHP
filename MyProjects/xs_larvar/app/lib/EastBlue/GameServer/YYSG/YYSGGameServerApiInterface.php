<?php namespace EastBlue\GameServer\YYSG;

interface YYSGGameServerApiInterface {

	public function connect($server_ip, $server_port, $server_dir_id);
}