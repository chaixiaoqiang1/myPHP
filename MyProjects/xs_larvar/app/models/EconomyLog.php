<?php 

class EconomyLog extends Eloquent {

	protected $table = 'log_economy as e';

	protected $primaryKey = 'log_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopePlayerSpend($query, $player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)
	{

		$sum_sql = $field_2 ? "SUM({$field} + {$field_2}) as spend" : "SUM({$field}) as spend";
		if($start_time && $end_time){
		    $query = $query->where('action_time', '>=', $start_time)
			->where('action_time', '<=', $end_time);
		}
		if($look_type == 1){//获得
			$sign_str = ">";
		}elseif($look_type == 2){
			$sign_str = "<";
		}elseif($look_type == 3){
			$sign_str = "<>";
		}
		$query->selectRaw($sum_sql)
			->where(function($query) use ($field, $field_2,$sign_str) {
				$query->where($field, $sign_str, 0);
				if ($field_2) {
					$query->orWhere($field_2, $sign_str, 0);
				}
			})
			->where('player_id', $player_id);
		if($action_type_num){
			$query->where('action_type',$action_type_num);
		}
		return $query;
	}

	public function scopeServerEconomy($query,$field,$symbol,$start_time,$end_time){
		$query->selectRaw("sum({$field}) as spend")
		->whereBetween('action_time', array($start_time, $end_time))
		->where($field,$symbol,0);
		return $query;
	}

	public function scopePlayerEconomyCount($query, $player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)
	{
	    if($start_time && $end_time){
	    	$query = $query->where('action_time', '>=', $start_time)
			->where('action_time', '<=', $end_time);
	    }
	    if($look_type == 1){//获得
	    	$sign_str = ">";
	    }elseif($look_type == 2){
	    	$sign_str = "<";
	    }else{
	    	$sign_str = "<>";
	    }
		$query->where('player_id', $player_id)
			->where(function($query) use ($field, $field_2, $sign_str) {
				$query->where($field, $sign_str, 0);
				if ($field_2) {
					$query->orWhere($field_2, $sign_str, 0);
				}
			});
		if($action_type_num){
			$query->where('action_type',$action_type_num);
		}
		return $query;
	}

	public function scopePlayerEconomy($query, $player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)
	{
		$sum_sql = $field_2 ? "({$field} + {$field_2}) as spend" : "{$field} as spend";
		if($start_time && $end_time){
		    $query = $query->where('action_time', '>=', $start_time)
			->where('action_time', '<=', $end_time);
		}
		if($look_type == 1){//获得
			$sign_str = ">";
		}elseif($look_type == 2){
			$sign_str = "<";
		}elseif($look_type == 3){
			$sign_str = "<>";
		}
		if($action_type_num){
			$query->where('action_type',$action_type_num);
		}
		$query->selectRaw("{$sum_sql}, action_type, action_time, yuanbao, tongqian, yueli, tili, shengwang,extra_tili, lingshi, jingjiedian, xianling, boat_book")
			->where(function($query) use ($field, $field_2, $sign_str) {
				$query->where($field, $sign_str, 0);
				if ($field_2) {
					$query->orWhere($field_2, $sign_str, 0);
				}
			})
			->where('player_id', $player_id)
			->orderBy('action_time', 'DESC');
		return $query;
	}

	public function scopeSimplePlayerEconomy($query, $player_id, $field, $start_time, $end_time, $field_2)
	{
	    $sum_sql = $field_2 ? "({$field} + {$field_2}) as spend" : "{$field} as spend";
	    if($start_time && $end_time){
	        $query = $query->where('action_time', '>=', $start_time)
	        ->where('action_time', '<=', $end_time);
	    }
	    $query->selectRaw("{$sum_sql}, action_type, action_time, yuanbao")
	    ->where(function($query) use ($field, $field_2) {
	        $query->where($field, '<>', 0);
	        if ($field_2) {
	            $query->orWhere($field_2, '<>', 0);
	        }
	    })
	    ->where('player_id', $player_id)
	    ->orderBy('action_time', 'DESC');
	    return $query;
	}
	public function scopePlayerEconomyStatistics($query, $player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)
	{
		$sum_sql = $field_2 ? "SUM({$field} + {$field_2}) as spend, count(1) as times" : "SUM({$field}) as spend, count(1) as times";
		if($start_time && $end_time){
		    $query = $query->where('action_time', '>=', $start_time)
			->where('action_time', '<=', $end_time);
		}
		if($look_type == 1){//获得
			$sign_str = ">";
		}elseif($look_type == 2){
			$sign_str = "<";
		}elseif($look_type == 3){
			$sign_str = "<>";
		}
		$query->selectRaw("{$sum_sql}, action_type, action_time, player_id")
			->where(function($query) use ($field, $field_2, $sign_str) {
				$query->where($field, $sign_str, 0);
				if ($field_2) {
					$query->orWhere($field_2, $sign_str, 0);
				}
			});
			if($action_type_num){
				$query->where('action_type',$action_type_num)
				->groupBy('player_id');
			}
			if(0 != $player_id){
				$query->where('player_id', $player_id)
				->groupBy('action_type');
			}
		return $query;
	}

	public function scopePlayerEconomyRank($query, $field, $symbole, $order)
	{
		return $query->selectRaw("SUM({$field}) as spend, p.player_id, p.player_name")
			->leftJoin('log_create_player as p', 'p.player_id', '=', 'e.player_id')
			->where($field, $symbole, 0)
			->groupBy('p.player_id')
			->orderBy('spend', $order);
	}

	public function scopePlayerEconomyRankWithTime($query, $field, $symbole, $order, $start_time, $end_time){
		return $query->selectRaw("SUM({$field}) as spend, e.player_id, lpn.player_name")
			->leftJoin(DB::raw("(select player_id,player_name from (select player_id,player_name from log_player_name order by id desc) as temp group by player_id) as lpn"),
			 'lpn.player_id', '=', 'e.player_id')
			->where($field, $symbole, 0)
			->where('e.created_at', '>=', $start_time)
			->where('e.created_at', '<=', $end_time)
			->groupBy('lpn.player_id')
			->orderBy('spend', $order);
	}

	public function scopePlayerEconomyAnalysis($query, $db_qiqiwu, $field, $symbole, $order, $start_time, $end_time, $lower_bound, $upper_bound='',$action_type,$game_id, $no_name)
	{
	    $query->selectRaw("SUM({$field}) as spend, e.player_id, p.player_name");
	    if(in_array($game_id, Config::get('game_config.mobilegames')) && 0 == $no_name){
            $query->leftJoin(DB::raw("(select idn.player_id,idn.player_name from (select lpn.player_id,lpn.player_name from log_player_name as lpn ORDER BY lpn.id desc) as idn GROUP BY idn.player_id) as p"), 'p.player_id', '=', 'e.player_id')
                  ->where($field, $symbole, 0);
        }else{
        	$query->leftJoin('log_create_player as p', 'p.player_id', '=', 'e.player_id')
                  ->where($field, $symbole, 0);
        }
	    if(123456789 == $action_type) {/*获取玩家-庄园消费数据 是以下其中消费数据的集合:清除礦脈CD 恢復精力 升級礦脈 直接購買高級礦石 刷新礦石 開闢礦脈 升級礦脈*/
            $query->whereIn('action_type', array(6413, 6404, 6402, 6405, 6403, 6401));
        }elseif(123456790 == $action_type){//女神金币转转乐
        	$query->whereIn('action_type', array(49929, 49963));
        }elseif($action_type != 0){
			if(in_array($game_id, Config::get('game_config.mobilegames'))){
				$query->where('mid', '=', $action_type);
			}else{
				$query->where('action_type', '=', $action_type);
			}
			
		}
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$query->whereBetween('created_at', array($start_time, $end_time))
			->having("spend", "<=", -$lower_bound);
		}else{
			$query->whereBetween('action_time', array($start_time, $end_time))
			->having("spend", "<=", -$lower_bound);
		}
		
	    if($upper_bound){
	    	$query->having("spend", ">=", -$upper_bound);
	    }
	    return $query->groupBy('e.player_id')
	   		->orderBy('spend', $order);
			
	}

	public function scopeFindBossKiller($query, $start_time, $end_time)
	{	
		 return $query->selectRaw("diff_tongqian as max_diff_tongqian, e.player_id as player_id, p.player_name as player_name, action_time")
			->leftJoin('log_create_player as p', 'p.player_id', '=', 'e.player_id')
			->where('action_time', '>=', $start_time)
			->where('action_time', '<=', $end_time)
		    ->where('action_type', 5915)
		    ->orderBy('diff_tongqian', 'DESC');

// 	    return $query->selectRaw("max(diff_tongqian) as max_diff_tongqian, e.player_id as player_id")
// 	    ->where('action_time', '>=', $start_time)
// 	    ->where('action_time', '<=', $end_time)
// 	    ->where('action_type', '=', 5915)
// 	    ->take(1);
	}

	public function scopeFindBossNum($query, $start_time, $end_time)
	{
		return $query->selectRaw("count(distinct(e.player_id)) as num , action_time ")
			->leftJoin('log_create_player as p ', 'p.player_id', '=', 'e.player_id')
			->where('action_time', '>=', $start_time)
			->where('action_time', '<=', $end_time)
			->where('action_type', 5915);
	}

	//演武场前三名
	 public function scopeEconomys($query, $player_id, $start_time, $end_time, $sort, $mid)
    {
        
         $query->selectRaw("e.yuanbao, e.diff_yuanbao, e.tongqian, e.diff_tongqian, e.player_id, e.action_type, FROM_UNIXTIME(e.action_time) as action_time, p.player_name")
            ->leftJoin('log_create_player as p','p.player_id', '=', 'e.player_id')
            ->whereBetween('action_time', array($start_time, $end_time));
         if($player_id){
         	$query->where('e.player_id', '=', $player_id);
         }
         if('0' != $mid){
         	$query->where('action_type', '=', $mid);
         }
         if('sortaction' == $sort){
          	$query->orderBy('action_type', 'DESC');
         }elseif('asc' == $sort || 'desc' == $sort){
         	$query->orderBy('diff_tongqian', $sort);
         }
         $query->orderBy('action_time', 'DESC');
         return $query;
    }

    public function scopePokerEconomyChange($query, $start_time, $end_time, $sort, $mid, $group_by){	//$group_by为1代表按照每个玩家来统计，为2代表统计所有的玩家
         $query->selectRaw("'-' as yuanbao, sum(diff_yuanbao) as diff_yuanbao, '-' as tongqian, sum(diff_tongqian) as diff_tongqian, 
         	".(($group_by == 1) ? "e.player_id, p.player_name, '-' as action_type," : "'-' as player_id, '-' as player_name, count(distinct e.player_id) as action_type,")." count(1) as action_time")
            ->leftJoin('log_create_player as p','p.player_id', '=', 'e.player_id')
            ->whereBetween('action_time', array($start_time, $end_time));
         $query->where('action_type', '=', $mid);
         if($group_by == 1){
         	$query->groupBy('e.player_id')->orderBy('diff_yuanbao')->orderBy('diff_tongqian');
         }
         return $query;
    }


	public function scopeGames($query, $player_id, $start_time, $end_time)
	{
		return $query->selectRaw("e.server_id, g.players, g.room_id, g.rule_id, FROM_UNIXTIME(g.time) as time")
			->leftJoin('log_game as g','g.server_id', '=', 'e.server_id')
            ->whereBetween('time', array($start_time, $end_time))
            ->where('e.player_id', 'like', '%'.$player_id.'%')->orderBy('time', 'DESC');
	}
	public function scopeFindEconomys($query, $player_id, $start_time, $end_time)
    {
        return $query->selectRaw("e.tongqian, e.diff_tongqian, p.player_id, e.action_type, FROM_UNIXTIME(e.action_time) as action_time, p.player_name")
            ->leftJoin('log_create_player as p','p.player_id', '=', 'e.player_id')
            ->whereBetween('action_time', array($start_time, $end_time))
            ->where('e.action_type','=','64752')
            ->where('p.player_id', '=', $player_id)->orderBy('action_time', 'DESC');
    }
    public function scopeFindEconomys2($query, $player_name, $start_time, $end_time)
    {
        return $query->selectRaw("e.tongqian, e.diff_tongqian, p.player_id, e.action_type, FROM_UNIXTIME(e.action_time) as action_time, p.player_name")
            ->leftJoin('log_create_player as p','p.player_id', '=', 'e.player_id')
            ->whereBetween('action_time', array($start_time, $end_time))
            ->where('e.action_type','=','64752')
            ->where('p.player_name', '=', $player_name)->orderBy('action_time', 'DESC');
    }
    public function scopeGetPlayerEconomyCount($query, $player_id, $field, $start_time, $end_time, $field_2, $look_type, $action_type_num)
	{
		$sum_sql = $field_2 ? "SUM({$field} + {$field_2}) as spend" : "SUM({$field}) as spend";
		if($start_time && $end_time){
		    $query = $query->where('action_time', '>=', $start_time)
			->where('action_time', '<=', $end_time);
		}
		$query->selectRaw("{$sum_sql}, player_id as action_name, action_time")
			->where(function($query) use ($field, $field_2, $look_type,$player_id) {
				if ($look_type == 1 ) {
					$query->where($field, '>', 0);
				}elseif ($look_type == 2) {
					$query->where($field, '<', 0);
				} elseif ($look_type == 3) {
					$query->where($field, '<>', 0);
				}
				if ($field_2) {
					$query->orWhere($field_2, '<>', 0);
				}
				if($player_id){
					$query->where('player_id', '=', $player_id);
				}
			})
			->where('action_type', $action_type_num)
			->groupBy('player_id');
		return $query;
	}

	public function scopeGetSpendonParts($query, $type, $start_time, $end_time, $game_id, $symbol){
		$type2type = array(
			'tongqian' => 'mana',
			'yuanbao' => 'crystal'
			);
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$key_time = 'created_at';
			$key_action = 'mid';
			$type = $type2type[$type];
		}else{
			$key_time = 'action_time';
			$key_action = 'action_type';
		}
		$diff_type = 'diff_'.$type;
		if($start_time && $end_time){
		    $query->whereBetween($key_time, array($start_time, $end_time));
		}
		if($type){
			$query->where($diff_type, $symbol, 0);
		}
		$query->selectRaw("count(distinct player_id) as player_num, count(1) as times, sum($diff_type) as sumvalue, $key_action as actionvalue");
		$query->orderBy('sumvalue', 'asc');
		$query->groupBy($key_action);

		return $query;
	}

	public function scopegetEconomyEachPlayer($query, $type, $start_time, $end_time, $game_id, $symbol, $limit_symbol, $limit_value){
		$type2type = array(
			'tongqian' => 'mana',
			'yuanbao' => 'crystal',
			'tili' => 'energy',
			);
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$key_time = 'e.created_at';
			$type = $type2type[$type];
		}else{
			$key_time = 'e.action_time';
		}

		$query->leftjoin("log_create_player as lcp", 'lcp.player_id', '=', 'e.player_id');

		$diff_type = 'diff_'.$type;
		if($start_time && $end_time){
		    $query->whereBetween($key_time, array($start_time, $end_time));
		}
		if($type){
			$query->where($diff_type, $symbol, 0);
		}
		$query->selectRaw("e.player_id, lcp.player_name, sum($diff_type) as sumvalue");
		$query->groupBy('e.player_id');
		if($limit_symbol){
			$query->having('sumvalue', $limit_symbol, $limit_value);
		}
		if('>' == $symbol){
			$query->orderBy('sumvalue', 'desc');
		}else{
			$query->orderBy('sumvalue', 'asc');
		}

		return $query;
	}

	public function scopegetEconomyWholeServer($query, $type, $start_time, $end_time, $game_id, $symbol){
		$type2type = array(
			'tongqian' => 'mana',
			'yuanbao' => 'crystal'
			);
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$key_time = 'e.created_at';
			$type = $type2type[$type];
		}else{
			$key_time = 'e.action_time';
		}

		$diff_type = 'diff_'.$type;
		if($start_time && $end_time){
		    $query->whereBetween($key_time, array($start_time, $end_time));
		}
		if($type){
			$query->where($diff_type, $symbol, 0);
		}
		$query->selectRaw("sum($diff_type) as sumvalue");
		return $query;
	}

	public function scopeGetSpendonShopYYSG($query, $type, $start_time, $end_time, $game_id, $mid, $symbol){
		$type2type = array(
			'tongqian' => 'mana',
			'yuanbao' => 'crystal'
			);
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$key_time = 'created_at';
			$key_action = 'mid';
			$type = $type2type[$type];
		}else{
			$key_time = 'action_time';
			$key_action = 'action_type';
		}
		$query->where($key_action, $mid);
		$diff_type = 'diff_'.$type;
		if($start_time && $end_time){
		    $query->whereBetween($key_time, array($start_time, $end_time));
		}
		if($type){
			$query->where($diff_type, $symbol, 0);
		}
		$query->selectRaw("count(distinct player_id) as player_num, count(1) as times, sum($diff_type) as sumvalue, $key_action as actionvalue, $diff_type as singlepirce");
		$query->orderBy('sumvalue', 'asc');
		$query->groupBy($diff_type);

		return $query;
	}

	public function scopeGetActivityAnalysis($query, $game_id, $start_time, $end_time, $mids, $type){
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
		 	$query->whereBetween('created_at', array($start_time, $end_time))
		 		  ->whereIn('mid', $mids);
		 	if('all' == $type){

		 	}
		 	if('parts' == $type){
		 		$query->groupBy('mid');
		 	}	  
		 	$query->selectRaw("mid as action_type,count(distinct player_id) as player_num, count(1) as times, sum(diff_crystal) as diff_yuanbao, sum(diff_mana) as diff_tongqian");
		}else{
		 	$query->whereBetween('action_time', array($start_time, $end_time))
		 		  ->whereIn('action_type', $mids);
		 	if('all' == $type){

		 	}
		 	if('parts' == $type){
		 		$query->groupBy('action_type');
		 	}	 
		 	$query->selectRaw("action_type,count(distinct player_id) as player_num, count(1) as times, sum(diff_yuanbao) as diff_yuanbao, sum(diff_tongqian) as diff_tongqian"); 	
		}
		return $query;
	}

	public function scopeCalculateRetention($query, $params, $db_qiqiwu, $db_payment){	//德扑计算各天玩牌人数
		if($params['interval']){
			$interval_second = $params['interval'] * 86400;	//天转时间
			$query->selectRaw("({$params['login_start_time']} + floor((action_time-{$params['login_start_time']})/{$interval_second})*{$interval_second}) as count_start_time, count(distinct player_id) as result_num");
			$query->groupBy('count_start_time');
		}else{
			$query->selectRaw("{$params['login_start_time']} as count_start_time, count(distinct player_id) as result_num");
		}

		$query->whereBetween('action_time', array($params['login_start_time'], $params['login_end_time']));
		$query->where('action_type', 'endOneRound');	//牌局结束的经济记录
		return $query;
	}
	//破产人数和次数
	public function scopegetPokerBankruptcy($query, $by_create_time, $start_time, $end_time, $create_start_time, $create_end_time){
		if($by_create_time){
			$query->join('log_create_player as lcp', function($join) use($create_start_time, $create_end_time){
				$join->on('lcp.player_id', '=', 'e.player_id')
					->where('lcp.created_time', '>', $create_start_time)
					->where('lcp.created_time', '<', $create_end_time);
			});
		}

		$query->where('e.action_type', '!=', 'saveChipsToStrongBox|saveChipsToStrongBox')	//200以下且这次操作是结束一局且这次操作为扣款操作且扣款前身上筹码超过200为破产
			  ->where('e.diff_tongqian', '<', 0)
			  ->where('e.tongqian', '<', 200)
			  ->whereBetween('e.action_time', array($start_time, $end_time))
			  ->whereRaw("e.tongqian-(e.diff_tongqian) >= 200")
			  ->selectRaw("count(distinct e.player_id) as bankruptcy_user_num, count(1) as bankruptcy_times");
		return $query;
	}
	//等级信息
	public function scopegetPokerBankruptcyLevel($query, $by_create_time, $start_time, $end_time, $create_start_time, $create_end_time){
		if($by_create_time){
			$query->join('log_create_player as lcp', function($join) use($create_start_time, $create_end_time){
				$join->on('lcp.player_id', '=', 'e.player_id')
					->where('lcp.created_time', '>', $create_start_time)
					->where('lcp.created_time', '<', $create_end_time);
			});
		}
		$query->leftjoin('log_levelup as ll', 'e.player_id', '=', 'll.player_id');	//获取等级

		$query->where('e.action_type', '!=', 'saveChipsToStrongBox|saveChipsToStrongBox')	//200以下且这次操作是结束一局且这次操作为扣款操作且扣款前身上筹码超过200为破产
			  ->where('e.diff_tongqian', '<', 0)
			  ->where('e.tongqian', '<', 200)
			  ->whereBetween('e.action_time', array($start_time, $end_time))
			  ->whereRaw("e.tongqian-(e.diff_tongqian) >= 200");

		$query->selectRaw('ifnull(max(ll.new_level), 1) as level')->groupBy('e.player_id');
		return $query;		
	}
	//救济人数
	public function scopegetPokerEconomy($query, $by_create_time, $start_time, $end_time, $create_start_time, $create_end_time, $action_type){
		if($by_create_time){
			$query->join('log_create_player as lcp', function($join) use($create_start_time, $create_end_time){
				$join->on('lcp.player_id', '=', 'e.player_id')
					->where('lcp.created_time', '>', $create_start_time)
					->where('lcp.created_time', '<', $create_end_time);
			});
		}

		$query->where('e.action_type', $action_type)	//200以下且这次操作是结束一局且这次操作为扣款操作且扣款前身上筹码超过200为破产
			  ->whereBetween('e.action_time', array($start_time, $end_time));
		return $query;
	}

	public function scopeGetPlayerYuanbaoIncrease($query, $start_time, $end_time, $game_id, $player_id){
		if(in_array($game_id, Config::get('game_config.mobilegames'))){
			$key_time = 'created_at';
			$key_yuanbao = 'crystal';
		}else{
			$key_time = 'action_time';
			$key_yuanbao = 'yuanbao';
		}
		$query->where('diff_'.$key_yuanbao, '>', 0)
			  ->whereBetween($key_time, array($start_time, $end_time))
			  ->where('player_id', $player_id)
			  ->selectRaw("sum(diff_".$key_yuanbao.") as yuanbao_increase");
		return $query;
	}
}