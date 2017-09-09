<?php

class SlaveApiGiftBoxController extends \BaseController
{
	public function giftboxYYSGIndex(){
		$game = Game::find(Session::get('game_id'));
		$servers = $this->getUnionServers();
		$giftbox = $this->OpenFile(public_path() . '/table/' . $game->game_code . '/giftbox.txt');
		$mid = $this->OpenFile(public_path() . '/table/' . $game->game_code . '/game_message.txt');	//初始化操作表
        $data = array(
            	'content' => View::make('slaveapi.economy.yysggiftbox', array(
                'servers' => $servers,
                'giftbox' => $giftbox,
                'mid' => $mid,
            ))
        );
        return View::make('main', $data);
	}

	public function giftboxYYSGDo(){
		$start_time = strtotime(Input::get('start_time'));
        $end_time = strtotime(Input::get('end_time'));
        $server_id = Input::get('server_id');
        if(!$server_id){
        	return Response::json(array('error'=> Lang::get('serverapi.select_game_server')), 403);
        }
        $mid = Input::get('action_type_num');
        if(!$mid){
        	$mid = Input::get('action_type_num_input');
        }
        $player_id = Input::get('player_id');
        $table_id = Input::get('table_id');
        if(!$table_id){
        	$table_id = Input::get('table_id_input');
        }
        $game_id = Session::get('game_id');
        $server = Server::find($server_id);
        $database_name = '`'.$game_id.'.'.$server->server_internal_id.'`';

        if($player_id){
        	$sql = "select to_player_id as player_id,mid,from_unixtime(created_at) as time,table_id from {$database_name}.log_giftbox where to_player_id = {$player_id} and created_at between {$start_time} and {$end_time} ";
        }else{
        	$sql = "select mid,table_id,count(distinct to_player_id) as p_times,count(1) as times from {$database_name}.log_giftbox where created_at between {$start_time} and {$end_time} ";
        }
        if($mid){
    		$sql .= "and mid = {$mid} ";
    	}
    	if($table_id){
    		$sql .= "and table_id = {$table_id} ";
    	}
    	if(!$player_id){	//如果没有输入玩家id，则mid和table_id中没有输入的就要按照其来groupby
	    	if(!$mid && !$table_id){
	    		$sql .= "group by mid,table_id";
	    	}else{
		        if(!$mid){
		    		$sql .= "group by mid ";
		    	}
		    	if(!$table_id){
		    		$sql .= "group by table_id ";
		    	}
	    	}
    	}

    	$sqlcontroller = new SlaveApiSqlController;

    	$result = $sqlcontroller->inputSqlDeal($sql,1);	//只有这种格式的返回才可以直接使用下面的replaceData方法

    	if(!is_array($result)){	//对这种返回值的正确返回判断，暂时用是否数组来判断，因为正确返回的时候没有做json操作
    		return $result;
    	}
    	
    	$game = Game::find(Session::get('game_id'));

    	$table = $this->OpenFile(public_path() . '/table/' . $game->game_code . '/game_message.txt');	//初始化操作表

    	$mid2name = array();
    	foreach ($table as $value) {
    		$mid2name[$value->id] = $value->desc;
    	}

    	$table = $this->OpenFile(public_path() . '/table/' . $game->game_code . '/giftbox.txt');	//初始化礼包表

		$giftboxid2name = array();
    	foreach ($table as $value) {
    		$giftboxid2name[$value->id] = $value->id.':'.$value->name;
    	}

    	unset($table);

    	$sqlcontroller->replaceData($result, 'mid', $mid2name, '操作');	//把$result中的mid替换为操作，并把mid对应的数据中的值当做键根据$mid2name中的对应关系完成替换
    	$sqlcontroller->replaceData($result, 'table_id', $giftboxid2name, '物品');
    	$replace = array(
    		'time' => '时间',
    		'player_id' => '玩家ID',
    		'p_times' => '人数',
    		'times' => '次数',
    		);
    	$sqlcontroller->replaceKeys($result, $replace);

    	unset($mid2name);
    	unset($giftboxid2name);
    	unset($sqlcontroller);

    	return Response::json($result);
	}

    public function RzzwRewardIndex(){
        $data = array(
                'content' => View::make('slaveapi.reward.rzzwrecord', array())
        );
        return View::make('main', $data);
    }

    public function RzzwRewardUpdate(){
        $type = Input::get('type');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);

        if(!$game){
            return Response::json(array('error' => 'No such Game!'), 404);
        }
        if(!$platform){
            return Response::json(array('error' => 'No such Platform!'), 404);
        }
        if('search' == $type){
            $by_time = Input::get('by_time');
            if($by_time){
                $start_time = strtotime(Input::get('start_time'));
                $end_time = strtotime(Input::get('end_time'));
            }else{
                $start_time = 0;
                $end_time = 0;
            }
            $record_type = (int)Input::get('record_type');
            $player_id = (int)Input::get('player_id');
            $reward_id = (int)Input::get('reward_id');

            $slaveapi = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

            $slave_result = $slaveapi->getRzzwRewardRecord($platform_id, $game_id, $start_time, $end_time, $record_type, $player_id, $reward_id);

            if(200 == $slave_result->http_code){
                $slave_data = $slave_result->body;
                $view_data = array();
                foreach ($slave_data as $value) {
                    $view_data[] = array(
                        'id' => $value->id,
                        'player_id' => $value->player_id,
                        'server_id' => $value->server_name,
                        'uid' => $value->uid,
                        'player_name' => $value->player_name,
                        'reward_id' => $value->reward_id,
                        'time' => date('Y-m-d H:i:s', $value->time),
                        'is_done' => $value->is_done,
                    );
                }
                $keywords = array(
                    0 => Lang::get('slave.player_id'), 
                    1 => Lang::get('slave.player_name'), 
                    2 => Lang::get('slave.server_name'),
                    3 => 'UID',
                    4 => Lang::get('slave.reward_id'),
                    5 => Lang::get('slave.is_send'),
                    );
                return Response::json(array(
                    'keywords' => $keywords,
                    'items' => $view_data,
                    ));
            }else{
                return $slaveapi->sendResponse();
            }
        }elseif('update' == $type){
            $record_id = Input::get('record_id');
            $params = array(
                    'id' => (int)$record_id,
                    'game_id' => (int)$game_id,
                    'is_done' => 1,
                    'time' => time(),
                );
            $platformapi = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);

            $platformapi_result = $platformapi->update_reward_lucky($params);

            if(200 == $platformapi_result->http_code){
                $platformapi_data = $platformapi_result->body;
                if(isset($platformapi_data->res)){
                    return Response::json(array('msg' => $platformapi_data->res));
                }else{
                    Log::info('SlaveApiGiftBoxController---'.var_export($platformapi_result, true));
                    return Response::json(array('msg' => 'Bad structure, Check Log'));
                }
            }else{
                Log::info('SlaveApiGiftBoxController---'.var_export($platformapi_result, true));
                return $platformapi->sendResponse();
            }
        }else{
            return Response::json(array('error' => 'Undefined type!'), 404);
        }
    }
}