<?php
    class Server extends Eloquent {

	protected $table = 'servers';

	protected $primaryKey = 'server_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeGetServer($query, $game_id){
		$skip_servers = Config::get('skip_servers.'.$game_id);
		return $query->where('game_id', $game_id)
			->whereNotIn('server_internal_id', $skip_servers)
			->orderBy('open_server_time', 'DESC')->orderBy('server_id', 'DESC');
	}

	public function scopeCurrentGameServers($query, $no_skip=0)
	{
		$game_id = Session::get('game_id');
		$skip_servers = Config::get('skip_servers.'.$game_id);
		if($no_skip){
			return $query->where('game_id', $game_id)
				->orderBy('server_id', 'DESC');
		}else{
			return $query->where('game_id', $game_id)
				->whereNotIn('server_internal_id', $skip_servers)
				->orderBy('server_id', 'DESC');
		}
	}

	public function scopeGetServerByGameId($query, $game_id)
	{
		return $query->where('game_id', $game_id)
					->select('api_server_ip', 'api_server_port');
	}

	public function scopeGetPlatformServerId($query, $game_id, $server_internal_id)
	{
		return $query->select('platform_server_id')
					->where('game_id', $game_id)
					->where('server_internal_id', $server_internal_id);
	}

    public function scopeInternalServer($query, $game_id, $server_internal_id)
    {
        return $query->select('server_name')
                    ->where('game_id', $game_id)
                    ->where('server_internal_id', $server_internal_id);
    }
}