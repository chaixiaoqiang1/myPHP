<?php namespace EastBlue\GameServer\Poker;

interface PokerGameServerApiInterface {

	public function connect($server_ip, $server_port);
}