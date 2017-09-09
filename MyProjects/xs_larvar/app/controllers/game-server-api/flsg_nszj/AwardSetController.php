<?php 
class AwardSetController extends \BaseController {
	private function initTable($file_name, $area_id = array()){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        if (!empty($area_id) && in_array($game_id, $area_id)) {
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.$game_id.'.txt');
        }else {
            $table = Table::init(public_path() . '/table/' . $game->game_code . '/'.$file_name.'.txt');
        }
        $file_table = $table->getData();
        return $file_table;
    }

	//风流三国设置运营排行榜奖励
	public function setRankIndex(){
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$servers = $this->getUnionServers($no_skip=1);
		if (empty($servers)) {
		    App::abort(404);
		    exit();
		}
		$items = $this->initTable('item', $this->area_item_id);
		$item = array(); 
		foreach ($items as $k => $v) {
		    $item[] = $v->name . ':' . $v->id;
		}
		$data = array(
			'content' => View::make('serverapi.flsg_nszj.activity.set_rank',
				array(
					'item' => $item,
					'servers' => $servers,
					'game_id' => $game_id,
					'game_code' => $game->game_code
				))
		);
		return View::make('main', $data);

	}

	public function setRankAward(){
		$msg = array(
		    'code' => Config::get('errorcode.unknow'),
		    'error' => Lang::get('error.basic_input_error')
		);
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$enter_type = (int)Input::get('enter_type');
		$server_id = Input::get('server_id');
		$is_look = Input::get('is_look');
		$activity_type = (int)Input::get('activity_type');
		$is_clean = (1 == Input::get('is_clean')) ? true : false;
		$award = array();
		if(!$server_id){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    if(!$activity_type){
	    	return Response::json(array('error'=>'Did you select a activity?'), 403);
	    }
	    if(1 == $is_look){//查看
	    	$result = $this->setRankAwardLook($server_id, $activity_type);
	    	return Response::json($result);
	    }

	    if(1 == $enter_type){
	    	for($i = 1; $i <= 30; $i++){
	    		$item = Input::get('item_id'.$i);
	    		if($item){
	    			$item = explode(":", $item);
	    			try{
	    				$item_id = (int)$item[1];
	    			}catch(\Exception $e){
	    				return Response::json($msg, 403);
	    			}
	    		}else{
	    			continue;
	    		}
	    		$item_num = (int)Input::get('amount'.$i);
	    		$temp_award = array(
	    			'item_id' => $item_id,
	    			'item_num' => $item_num,
	    			'rank' => (int)($i-1),//0是第一名
	    		);
	    		$award[] = $temp_award;
	    		unset($temp_award); 
	    	}
	    }elseif (2 == $enter_type) {
	    	$text_datas = Input::get('text_data');
	    	$text_datas = explode("\n", $text_datas);
	    	if(!$text_datas){
	    		return Response::json($msg, 403);
	    	}
	    	foreach ($text_datas as &$v) {
	    	    $v = trim($v);
	    	}
	    	unset($v);
	    	$text_datas = array_unique($text_datas);
	    	foreach ($text_datas as $text_data) {
	    		$text_data = explode("\t", $text_data,3);
	    		if(count($text_data) != 3){
	    			return Response::json($msg, 403);
	    		}
	    		$temp_award = array(
	    			'item_id' => (int)$text_data[0],
	    			'item_num' => (int)$text_data[1],
	    			'rank' => (int)$text_data[2]-1,//0是第一名
	    		);
	    		$award[] = $temp_award;
	    		unset($temp_award);
	    	}
	    	
	    }else{
	    	return Response::json($msg, 403);
	    }
	    
    	$server = Server::find($server_id);
    	if(!$server){
    		return Response::json(array('error'=>'Not Found Server'), 403);
    	}
    	$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
    	$response = $api->setRankAward($activity_type, $award, $is_clean);
    	if(isset($response->awards)){
    		$result[] = array(
    			'msg' => ' ( ' . $server->server_name . ' ) : OK'  . "\n",
    			'status' => 'ok'
    		);
    	}else{
    		$result[] = array(
    		        'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
    		        'status' => 'error'
    		);
    	}

	    return Response::json($result);

	}

	public function setRankAwardLook($server_id, $activity_type){
		$game_id = Session::get('game_id');
		$items = $this->initTable('item', $this->area_item_id);
		$item = array(); 
		foreach ($items as $k => $v) {
		    $item[$v->id] = $v->name. ':'. $v->id;
		}

		$result = array();
		
		$server = Server::find($server_id);
		if(!$server){
			$result = array(
    		        'msg' => 'Not Found Server' . "\n",
    		        'status' => 'error'
    		);
			return $result;
		}
		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);
		$response = $api->setRankAwardLook($activity_type);
		if(isset($response->awards)){
			foreach ($response->awards as $v) {
				$temp_award = array(
					'item_name' => isset($item[$v->item_id]) ? $item[$v->item_id] : $v->item_id,
					'item_num' => $v->item_num,
					'rank' => $v->rank+1,
				);
				$result[] = $temp_award;
				unset($temp_award);
			}
		}else{
			$result = array(
    		        'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
    		        'status' => 'error'
    		);
		}

		return $result;
	}
	//女神最强工会 竞技王设置奖励
	public function guildAwardSetIndex()
	{
		$servers = $this->getUnionServers();
	    if(empty($servers))
	    {
	        App::abort(404);
	        exit();
	    }
		$award = $this->initTable('award');
		$items = $this->initTable('item',$this->area_item_id);
		$item = '';
		foreach ($items as $k => $v) {
			$item = $item ."'". $v->name . ':' . $v->id."',";
		}
		$game_id = Session::get('game_id');
		$game = Game::find($game_id);
		$award_types = array(
			108 => Lang::get('serverapi.super_guild'),
            109 => Lang::get('serverapi.promotion_jingjiwang'),
		);
		$data = array(
				'content' => View::make('serverapi.flsg_nszj.activity.set_guild',array(
						'servers' => $servers,
						'award' => $award,
                    	'item' => $item,
                    	'game_id' => $game_id,
                    	'game_code' => $game->game_code,
                    	'award_types' => $award_types,
					))
		);
		return View::make('main', $data);
	}
	
	public function guildAwardSet()
	{
		
	    $msg = array(
	           'code' => Config::get('errorcode.unknow'),
	           'error' => Lang::get('error.basic_input_error')
	    );
	    $server_ids = Input::get('server_id');
	    if(empty($server_ids)){
	    	return Response::json(array('error'=>'Did you select a server?'), 403);
	    }
	    $type = Input::get('award_type');
	    if('0' == $type){
	    	return Response::json(array('error'=>'未选择要设置的活动'), 403);
	    }
	    $title = Input::get('title');
	    $title_area = Input::get('title_area');
	    if(1 == $title){//设置标题
	    	if(108 == $type){
	    		$title_index = 'champion_league_title_index|'.$title_area;
	    	}elseif(109 == $type) {
	    		$title_index = 'arena_king_title_index|'.$title_area;
	    	}
	    }

	    $result = array();
	    $award2="y";
	    $game_id = Session::get('game_id');
	    $game = Game::find(Session::get('game_id'));

	    for($i = 1;$i <= 30; $i++){
	    	$award1="x";
	    	$day = (int)Input::get('day' . $i);
	    	$left_rank = (int)Input::get('left_rank'.$i); 
	    	$right_rank = (int)Input::get('right_rank'.$i);
	    	if(0 == $left_rank){
	    		continue;
	    	}                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
	    	if($day < 0 || $left_rank<0 || $right_rank<0){
	    		return Response::json(array('error'=>'天数排名不能小于0'), 403);
	    	}
	    	for($j=65;$j<70;$j++){
	    		$temp=strtolower(chr($j));
	    		$award_id = (int)Input::get('award_id_' . $temp . $i);
	    		$award_value= Input::get('award_value_' . $temp . $i);
	    		if($award_id == 0){continue;}
	    		if($award_value <= 0){
	    			$k = $j-64;
	    			return Response::json(array('error'=>"第$i 个档位第$k 个物品的奖励数必须大于0"), 403);
	    		}
	    		if($award_id != 9){//如果不是物品
	    			//$award1=$award1 . $award_id . ',' . $award_value . '&';
	    			$award1=$award1 . $award_id . ',' . 0 . ',' . $award_value . '&';
	    		}else{
	    			$item_id_name = Input::get('item_id_' . $temp . $i);
	    			$gift_id_name = explode(":", $item_id_name);
	    			try{
	    			    $item_id = (int)$gift_id_name[1];
	    			}catch(\Exception $e){
	    			    return Response::json($msg, 403);
	    			}
	    			$award1=$award1 . $award_id . ',' . $item_id. ',' . $award_value . '&';
	    		}
	    	}
	    	//截掉每个award1最后的&和前面的x
	    	$award1=substr($award1, 1,strlen($award1)-2) . ';';
	    	$award2=$award2 . $day . '|'.$left_rank.'|'.$right_rank. ':' . $award1;	
	    }
	   	$award=substr($award2, 1,strlen($award2)-2);
	   	$is_timing = (int)Input::get('is_timing');
	   	$start_time = strtotime(trim(Input::get('start_time')));
	   	foreach ( $server_ids as $server_id ){
	   		$server = Server::find($server_id);
	        if(! $server)
	        {
	            $msg['error'] = Lang::get('error.basic_not_found');
	            return Response::json($msg, 404);
	        }
	        if(Session::get('game_id') != $server->game_id){
	            return Response::json(array('error'=>'please check the current platform and servers!'), 403);
	        }
	   		$api = GameServerApi::connect($server->api_server_ip, $server->api_server_port, $server->api_dir_id);

	   		if(1 == $title){
	   			$response = $api->setGuildAwardTitle($is_timing, $game->game_code, $type, $title_index);
	   		}else{
	            $response = $api->setGuildAward($is_timing, $game->game_code, $type, $award);
	            if(1 == $is_timing){
	            	if($start_time<time()+1200){
	            		return Response::json($result = array('msg' => '保证活动开启时间大于当前时间20分钟以上', 'status' => 'error'));
	            	}
	            	if(empty($response)){
	            		return Response::json($result = array('msg'=>'设置error','status' => 'error'));
	            	}
	            	$activity['game_id'] = $game_id;
	            	$activity['type'] = 3;//设置活动奖励
	            	$activity['start_time'] = $start_time; 
	            	$activity['created_time'] = time();
	            	$activity['user_id'] = Auth::user()->user_id;
	            	$activity['main_server'] = implode(",", $server_ids);
	            	$activity['from_server'] = $type;
	            	$activity['params'] = json_encode($response);
	            	try{
	            		$res = DB::table('timing_activities')->insertGetId($activity);
	            	}catch(\Exception $e){
	            		Log::error($e);
	            		return Response::json($result = array('msg'=>'设置error','status' => 'error'));
	            	}
	            	unset($activity);
	            	return Response::json($result = array('msg'=>'本次设置预计在' . date("Y-m-d H:i:s", $start_time-600) .  '—' . date("Y-m-d H:i:s", $start_time+120) . '内开启,请确定好对应的假日活动是否有设置','status' => 'ok'));
	            }			
	   		}
		   	if(isset($response->result) && $response->result == 'OK')
   			{
   			    $result[] = array(
   			            'msg' => ' ( ' . $server->server_name . ' ) : ' . $response->result . "\n",
   			            'status' => 'ok'
   			    );
   			} else
   			{
   			    $result[] = array(
   			            'msg' => ' ( ' . $server->server_name . ' ) : ' . 'error' . "\n",
   			            'status' => 'error'
   			    );
   			}  
	   	}
	   	$msg = array(
				'result' => $result
		);
		//Log::info(var_export($msg,true));
		return Response::json($msg);
	   	
	}

}