<?php

class GM extends Eloquent {

	protected $table = 'gm';

	protected $primaryKey = 'gm_id';

	public $timestamps = false;

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeFindServerGMID($query, $server_gm_id, $server_id)
	{
		return $query->where('server_gm_id', $server_gm_id)->where('server_id', $server_id);
	}

	public function scopeRepliedGM($query, $player_name, $start_time, $end_time, $server_id, $type)
	{
		$query->leftJoin('users as u', 'u.user_id', '=', 'gm.user_id');
		$query->where('server_id', $server_id)
			->where('is_done', 1);
		if ($player_name) {
			$query->where('player_name', $player_name);
		}
		if ($start_time && $end_time) {
            $query->whereBetween("replied_time", array(
                $start_time,
                $end_time
            ));
		}
		if ($type) {
            $query->where("gm_type", $type);
		}
		return $query;
	}

	public function scopeRepliedYYSGGM($query, $player_name, $start_time, $end_time, $server_id, $type)
	{
		$query->leftJoin('users as u', 'u.user_id', '=', 'gm.user_id');
		$query->where('server_id', $server_id);
		if ($player_name) {
			$query->where('player_name', $player_name);
		}
		if ($start_time && $end_time) {
            $query->whereBetween("send_time", array(
                $start_time,
                $end_time
            ));
		}
		if ($type) {
            $query->where("gm_type", $type);
		}
		return $query;
	}

	public function scopeGetGmMessageLikeWG($query, $game_id, $partofmessage){
		$query->leftJoin('users as u', 'u.user_id', '=', 'gm.user_id');
		$query->leftJoin('servers as s', 's.server_id', '=', 'gm.server_id');
		$query->orderby('gm.replied_time', 'desc');
		$query->where('s.game_id', $game_id)
			  ->selectRaw("u.username, gm.*");
		if($partofmessage){
			$query->where('reply_message', 'like', "%$partofmessage%");
		}
		return $query;
	}

	public function scopeGetGmQuestionLikeWG($query, $game_id, $partofmessage){
		$query->leftJoin('users as u', 'u.user_id', '=', 'gm.user_id');
		$query->leftJoin('servers as s', 's.server_id', '=', 'gm.server_id');
		$query->orderby('gm.send_time', 'desc');
		$query->where('s.game_id', $game_id)
			  ->selectRaw("u.username, gm.*");
		if($partofmessage){
			$query->where('message', 'like', "%$partofmessage%");
		}
		return $query;		
	}

	public function scopeGetGmMessageLikeMG($query, $game_id, $partofmessage){
		$query->leftJoin('users as u', 'u.user_id', '=', 'gm.user_id');
		$query->leftJoin('servers as s', 's.server_id', '=', 'gm.server_id');	
		$query->orderby('gm.send_time', 'desc');
		$query->where('s.game_id', $game_id);
		$query->where('gm.reply_message', '')
			  ->selectRaw("u.username, gm.*");
		if($partofmessage){
			$query->where('message', 'like', "%$partofmessage%");
		}
		return $query;
	}

	public function scopeGetGmQuestionLikeMG($query, $game_id, $partofmessage){
		$query->leftJoin('servers as s', 's.server_id', '=', 'gm.server_id');	
		$query->orderby('gm.send_time', 'desc');
		$query->where('s.game_id', $game_id);
		$query->where('gm.reply_message', '')
			  ->selectRaw("gm.*");
		if($partofmessage){
			$query->where('message', 'like', "%$partofmessage%");
		}
		return $query;
	}

	public function scopegetPossibleQuestion($query, $send_time, $player_id){
		$query->where('send_time', '<', $send_time)
			  ->where('user_id', 0)
			  ->where('player_id', $player_id)
			  ->orderby('gm_id', 'desc');
		return $query;
	}

	public function scopegetPossibleAnswer($query, $send_time, $player_id){
		$query->leftJoin('users as u', 'u.user_id', '=', 'gm.user_id')
			  ->where('send_time', '>', $send_time)
			  ->where('gm.player_id', $player_id)
			  ->orderby('gm_id', 'asc')
			  ->selectRaw("u.username, gm.*");
		return $query;
	}

/*	public function scopeGetGmMessageReplyGMDone($query,$game_id,$server_id,$start_time,$end_time,$gm_name){
		$query->leftJoin('users as u', 'u.user_id', '=', 'gm.user_id');
		$query->leftJoin('servers as s', 's.server_id', '=', 'gm.server_id');
		if (in_array(0,$server_id)) {
			$query->where("s.game_id",$game_id);
		}else{
			$query->whereIn("gm.server_id",$server_id);
		}
		if ($gm_name) {
			$query->where('username', $gm_name);
		}
		if ($start_time && $end_time) {
			$query->whereBetween("send_time", array(
				$start_time,
				$end_time
				));
		}
		$query->groupBy('username','player_id','date')
		->selectRaw("username,AVG(replied_time-send_time) as avg_time,sum(`is_done`) as gm_answer,FROM_UNIXTIME(send_time, '%Y-%m-%d') as date");
		return $query;
	}*/


}