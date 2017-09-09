<?php

class SlaveAdReportController extends SlaveServerBaseController {
	public function getFBStat()
	{
		$msg = array(
			'code' => Config::get('errorcode.slave_ad_fb'),
			'error' => '',
		);
		$start_time = (int)Input::get('start_time');
		$end_time = (int)Input::get('end_time');
		$diff_hours = (int)Input::get('diff_hours');
		$game_id = Input::get('game_id');
		if ($start_time >= $end_time) {
			$msg['error'] = Lang::get('error.time_interval');
			return Response::json($msg, 403);
		}

		$u1 = Input::get('u1');
		$u2 = Input::get('u2');
		$game_id = $this->game_id;
		$server_internal_id = (int)Input::get('server_internal_id');
		/*$fb = SlaveAdReport::on($this->db_ad)
			->getFBStat($this->db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $this->db_name)
		->get();*/
        Log::info('params--game_id:'.$game_id.'start_time:'.$start_time.'end time:'.$end_time.'diff_hours:'.$diff_hours.'u1:'.$u1.'u2:'.$u2.'internal_id:'.$server_internal_id.'db_name:'.$this->db_name);
		if ($game_id == 11) {
            $fb = SlaveAdReport::on($this->db_ad)
			->getPokerFBStat($this->db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $this->db_name)
			->get();
		} elseif($game_id == 44 || $game_id == 53){
            $fb = SlaveAdReport::on($this->db_ad)
			->getFBStatTR($this->db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $this->db_name)
			->get();
		}elseif($game_id == 1){
			$fb = SlaveAdReport::on($this->db_ad)
			->getTSFBStat($this->db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $this->db_name)
			->get();
		}else {
			$fb = SlaveAdReport::on($this->db_ad)
			->getFBStat($this->db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $this->db_name)
			->get();
		}

		if ($fb) {
			return Response::json($fb);
		} else {
			return Response::json(array(), 404);
		}
	}
	public function SXDGetFBStat()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.slave_ad_fb'),
	            'error' => '',
	    );
	    $start_time = (int)Input::get('start_time');
	    $end_time = (int)Input::get('end_time');
	    if ($start_time >= $end_time) {
	        $msg['error'] = Lang::get('error.time_interval');
	        return Response::json($msg, 403);
	    }
	
	    $u1 = Input::get('u1');
	    $u2 = Input::get('u2');
	    $game_id = (int)Input::get('game_id');
	
	    $server_internal_id = (int)Input::get('server_internal_id');
	    $fb = SlaveAdReport::on($this->db_ad)
	    ->sXDGetFBStat($this->db_qiqiwu, $game_id, $start_time, $end_time, $u1, $u2, $server_internal_id)
	    ->get();
	
	    if ($fb) {
	        return Response::json($fb);
	    } else {
	        return Response::json(array(), 404);
	    }
	}

	public function THGetFBStat()
	{
	    $msg = array(
	            'code' => Config::get('errorcode.slave_ad_fb'),
	            'error' => '',
	    );
	    $start_time = (int)Input::get('start_time');
	    $end_time = (int)Input::get('end_time');
	    if ($start_time >= $end_time) {
	        $msg['error'] = Lang::get('error.time_interval');
	        return Response::json($msg, 403);
	    }
	
	    $u1 = Input::get('u1');
	    $u2 = Input::get('u2');
	    $game_id = (int)Input::get('game_id');
	
	    $server_internal_id = (int)Input::get('server_internal_id');
	    $fb = SlaveAdReport::on($this->db_ad)
	    ->thGetFBStat($this->db_qiqiwu, $game_id, $start_time, $end_time, $u1, $u2, $server_internal_id)
	    ->get();
	
	    if ($fb) {
	        return Response::json($fb);
	    } else {
	        return Response::json(array(), 404);
	    }
	}
}