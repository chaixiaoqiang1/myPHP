<?php namespace EastBlue\GameServer\Flsg_nszj;

interface GameServerApiInterface {

	public function connect($server_ip, $server_port, $server_dir_id);
}