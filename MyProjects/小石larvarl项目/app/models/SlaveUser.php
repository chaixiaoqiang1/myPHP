<?php

class SlaveUser extends Eloquent {

	protected $table = 'users as u';

	protected $primaryKey = 'user_id';

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeWeeklyStat($query, $server_start_time, $server_end_time, $reg_start_time, $reg_end_time, $game_id, $users_type, $player_type, $game_type=1, $filter_u1=1) {

		if('2' != $game_type){
			$query->leftJoin('users as f', function($join) {
				$join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=', 0);
			});
		}

		if('1' == $player_type){	//表明create_player表中存在game_id字段
			$query->leftJoin('create_player as cp', function($join) use ($reg_start_time, $reg_end_time, $game_id) {
					$join->on('cp.uid', '=', 'u.uid')
							->where('cp.game_id', '=', $game_id)
							->where('cp.created_time', '>=', $reg_start_time)
							->where('cp.created_time', '<=', $reg_end_time);
			});
		}else{	//不存在game_id
			$query->leftJoin('create_player as cp', function($join) use ($reg_start_time, $reg_end_time) {
					$join->on('cp.uid', '=', 'u.uid')
						->where('cp.created_time', '>=', $reg_start_time)
						->where('cp.created_time', '<=', $reg_end_time);
			});
		}

		if('2' == $game_type){
			$query->leftJoin('device_list as dl', function($join) use ($game_id){
				$join->on('u.device_id', '=', 'dl.device_id')
					->where('dl.game_id', '=', $game_id);
			});
		}

		if('1' == $users_type){	//users表中有game_source字段
			$query->where('u.game_source', $game_id);
		}elseif('2' == $users_type){	//users表中有game_id字段
			$query->where('u.game_id', $game_id);
		}else{	//users表中不存在game_source或者game_id字段的情况,不限制
		}

		if('2' == $game_type){
			$uid_type = 'COUNT(DISTINCT(u.uid)) as count_formal';
			if($filter_u1){
				$filter_sql = 'dl.os_type, u.u as u1, u.source';
				$query->groupBy('dl.os_type', 'u.source', 'u.u');
			}else{
				$filter_sql = 'dl.os_type, u.source';
				$query->groupBy('dl.os_type', 'u.source');
			}
		}else{
			$uid_type = 'COUNT(DISTINCT(f.uid)) as count_formal';
			if($filter_u1){
				$filter_sql = 'u.u as u1, u.source';
				$query->groupBy('u.source', 'u.u');
			}else{
				$filter_sql = 'u.source';
				$query->groupBy('u.source');
			}
		}

		$query->select(DB::raw($filter_sql),
				DB::raw($uid_type),
				DB::raw('COUNT(cp.uid) as count_player'))
			->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $reg_start_time) , date("Y-m-d H:i:s", $reg_end_time)));
		return $query;
	}

	

    //单平台多游戏：三国普通服世界服
	 public function scopeUserStat($query, $server_internal_id, $db_server, $start_time, $end_time, $interval, $filter, $source, $u1, $u2 ,$game_id, $classify) {
        if(0 == $classify){
	        $query->leftJoin('users as f', function($join) use ($game_id) {
	            $join->on('f.uid', '=', 'u.uid')
	                ->where('f.is_anonymous', '=' , 0)
	                ->where('f.game_source', '=', $game_id);//三国世界版区分用户来源
	        })->leftJoin('users as afu', function($join) use ($game_id){
	            $join->on('afu.uid', '=', 'u.uid')
	                ->where('afu.is_anonymous', '=', 1)
	                ->where('afu.still_anonymous', '=', 0)
	                ->where('afu.game_source', '=', $game_id);
	                // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
	        });

	        if(in_array($game_id, Config::get('game_config.yysggameids'))){
				$query->leftJoin(DB::raw("`{$db_server}`.log_create_player as cpf"), function($join) {
					$join->on('cpf.uid', '=', 'f.uid');
				});
	        }else{
				$query->leftJoin('create_player as cpf', function($join) use ($server_internal_id, $game_id) {
					$join->on('cpf.uid', '=', 'f.uid')
						->where('cpf.server_id', '=', $server_internal_id)
						->where('cpf.game_id', '=', $game_id);
				});
			}
			if(in_array($game_id, Config::get('game_config.mobilegames'))){
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
					$join->on('lf.player_id', '=', 'cpf.player_id')
						->where('lf.lev', '=', 10);
				});
			}else{
				if ($server_internal_id > 0) {				
						$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
							$join->on('lf.player_id', '=', 'cpf.player_id')
								->where('lf.new_level', '=', 10);
						});
				}
			}

	    		$interval_sql = '';
				$filter_sql = '';
				$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

			if ($server_internal_id > 0) {
				$server_sql = 
					'COUNT(cpf.player_id) as count_player_formal,
					COUNT(lf.player_id) as count_lev_formal';
			}	
			$afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
			$query->select(
				($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(f.uid ) as count_formal'), 
				DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
			)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
	    }elseif(1 == $classify){
	        $query->leftJoin('users as a', function($join) use ($game_id) {
	            $join->on('a.uid', '=', 'u.uid')
	                ->where('a.is_anonymous', '=', 1)
	                ->where('a.game_source', '=', $game_id);
	        });

	        if(in_array($game_id, Config::get('game_config.yysggameids'))){
				$query->leftJoin(DB::raw("`{$db_server}`.log_create_player as cpa"), function($join) {
					$join->on('cpa.uid', '=', 'a.uid');
				});
	        }else{
				$query->leftJoin('create_player as cpa', function($join) use ($server_internal_id, $game_id) {
					$join->on('cpa.uid', '=', 'a.uid')
						->where('cpa.server_id', '=', $server_internal_id)
		                ->where('cpa.game_id', '=', $game_id);
				});
			}
			if(in_array($game_id, Config::get('game_config.mobilegames'))){
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
					$join->on('la.player_id', '=', 'cpa.player_id')
						->where('la.lev', '=', 10);
				});
			}else{	
				if ($server_internal_id > 0) {				
						$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
							$join->on('la.player_id', '=', 'cpa.player_id')
								->where('la.new_level', '=', 10);		
						});
				}
			}

	    		$interval_sql = '';
				$filter_sql = '';
				$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

			if ($server_internal_id > 0) {
				$server_sql = 
					'COUNT(cpa.player_id) as count_player_anonymous, 
					COUNT(la.player_id) as count_lev_anonymous';
			}	
			$query->select(
				($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(a.uid) as count_anonymous'), 
				DB::raw($server_sql)
			)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));    	
	    }elseif(3 == $classify){
	        $query->leftJoin('users as f', function($join) use ($game_id) {
	            $join->on('f.uid', '=', 'u.uid')
	                ->where('f.is_anonymous', '=' , 0)
	                ->where('f.game_source', '=', $game_id);//三国世界版区分用户来源
	        })->leftJoin('users as a', function($join) use ($game_id) {
	            $join->on('a.uid', '=', 'u.uid')
	                ->where('a.is_anonymous', '=', 1)
	                ->where('a.game_source', '=', $game_id);
	        })->leftJoin('users as afu', function($join) use ($game_id){
	            $join->on('afu.uid', '=', 'u.uid')
	                ->where('afu.is_anonymous', '=', 1)
	                ->where('afu.still_anonymous', '=', 0)
	                ->where('afu.game_source', '=', $game_id);
	                // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
	        });

	        if(in_array($game_id, Config::get('game_config.yysggameids'))){
	        	$query->leftJoin(DB::raw("`{$db_server}`.log_create_player as cpf"), function($join) {
					$join->on('cpf.uid', '=', 'f.uid');
				});
				$query->leftJoin(DB::raw("`{$db_server}`.log_create_player as cpa"), function($join) {
					$join->on('cpa.uid', '=', 'a.uid');
				});
	        }else{
				$query->leftJoin('create_player as cpf', function($join) use ($server_internal_id, $game_id) {
					$join->on('cpf.uid', '=', 'f.uid')
						->where('cpf.server_id', '=', $server_internal_id)
						->where('cpf.game_id', '=', $game_id);
				});
				$query->leftJoin('create_player as cpa', function($join) use ($server_internal_id, $game_id) {
					$join->on('cpa.uid', '=', 'a.uid')
						->where('cpa.server_id', '=', $server_internal_id)
		                ->where('cpa.game_id', '=', $game_id);
				});
			}

			if(in_array($game_id, Config::get('game_config.mobilegames'))){			
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
					$join->on('lf.player_id', '=', 'cpf.player_id')
						->where('lf.lev', '=', 10);
				});
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
					$join->on('la.player_id', '=', 'cpa.player_id')
						->where('la.lev', '=', 10);		
				});
			}else{
				if ($server_internal_id > 0) {				
						$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
							$join->on('lf.player_id', '=', 'cpf.player_id')
								->where('lf.new_level', '=', 10);
						});
						$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
							$join->on('la.player_id', '=', 'cpa.player_id')
								->where('la.new_level', '=', 10);		
						});
				}
			}

    		$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

			if ($server_internal_id > 0) {
				$server_sql = 
					'COUNT(cpf.player_id) as count_player_formal,
					COUNT(cpa.player_id) as count_player_anonymous, 
					COUNT(lf.player_id) as count_lev_formal, 
					COUNT(la.player_id) as count_lev_anonymous';
			}	
			$afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
			$query->select(
				($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(a.uid) as count_anonymous'), 
				DB::raw('COUNT(f.uid ) as count_formal'), 
				DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
			)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));    	
	    }
		$this->filtersource($query, $source, $u1, $u2, '', $filter, $des = 'game_source');
		return $query;	
	}

        //单平台单游戏
    public function scopeUserStatSingleGame($query, $server_internal_id, $db_server, $start_time, $end_time, $interval, $filter, $source, $u1, $u2 ,$game_id, $classify) {
        if(0 == $classify){
	        $query->leftJoin('users as f', function($join) {
	            $join->on('f.uid', '=', 'u.uid')
	                ->where('f.is_anonymous', '=' , 0);
	        })->leftJoin('users as afu', function($join) {
	            $join->on('afu.uid', '=', 'u.uid')
	                ->where('afu.is_anonymous', '=', 1)
	                ->where('afu.still_anonymous', '=', 0);
	            // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
	        });


	        $query->leftJoin('create_player as cpf', function($join) use ($server_internal_id) {
	            $join->on('cpf.uid', '=', 'f.uid')
	                ->where('cpf.server_id', '=', $server_internal_id);
	        });

	        if ($server_internal_id > 0) {
	            $query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
	                $join->on('lf.player_id', '=', 'cpf.player_id')
	                    ->where('lf.new_level', '=', 10);
	            });
	        }

    		$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

	        if ($server_internal_id > 0) {
	            $server_sql =
	                'COUNT(cpf.player_id) as count_player_formal,
					COUNT(lf.player_id) as count_lev_formal';
	        }
	        $afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
	        $query->select(
	            ($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)),
	            DB::raw('COUNT(f.uid ) as count_formal'),
	            DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
	        )->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
    	}elseif(1 == $classify){
	        $query->leftJoin('users as a', function($join) {
	            $join->on('a.uid', '=', 'u.uid')
	                ->where('a.is_anonymous', '=', 1);
	        });

	        $query->leftJoin('create_player as cpa', function($join) use ($server_internal_id) {
	            $join->on('cpa.uid', '=', 'a.uid')
	                ->where('cpa.server_id', '=', $server_internal_id);
	        });

	        if ($server_internal_id > 0) {
	            $query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
	                $join->on('la.player_id', '=', 'cpa.player_id')
	                    ->where('la.new_level', '=', 10);
	            });
	        }

    		$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

	        if ($server_internal_id > 0) {
	            $server_sql =
	                'COUNT(cpa.player_id) as count_player_anonymous,
					COUNT(la.player_id) as count_lev_anonymous';
	        }
	        $query->select(
	            ($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)),
	            DB::raw('COUNT(a.uid) as count_anonymous'),
	            DB::raw('COUNT(f.uid ) as count_formal'),
	            DB::raw($server_sql)
	        )->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
    	}elseif(3 == $classify){
	        $query->leftJoin('users as f', function($join) {
	            $join->on('f.uid', '=', 'u.uid')
	                ->where('f.is_anonymous', '=' , 0);
	        })->leftJoin('users as a', function($join) {
	            $join->on('a.uid', '=', 'u.uid')
	                ->where('a.is_anonymous', '=', 1);
	        })->leftJoin('users as afu', function($join) {
	            $join->on('afu.uid', '=', 'u.uid')
	                ->where('afu.is_anonymous', '=', 1)
	                ->where('afu.still_anonymous', '=', 0);
	            // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
	        });


	        $query->leftJoin('create_player as cpf', function($join) use ($server_internal_id) {
	            $join->on('cpf.uid', '=', 'f.uid')
	                ->where('cpf.server_id', '=', $server_internal_id);
	        });
	        $query->leftJoin('create_player as cpa', function($join) use ($server_internal_id) {
	            $join->on('cpa.uid', '=', 'a.uid')
	                ->where('cpa.server_id', '=', $server_internal_id);
	        });

	        if ($server_internal_id > 0) {
	            $query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
	                $join->on('lf.player_id', '=', 'cpf.player_id')
	                    ->where('lf.new_level', '=', 10);
	            });
	            $query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
	                $join->on('la.player_id', '=', 'cpa.player_id')
	                    ->where('la.new_level', '=', 10);
	            });
	        }

	    		$interval_sql = '';
				$filter_sql = '';
				$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

	        if ($server_internal_id > 0) {
	            $server_sql =
	                'COUNT(cpf.player_id) as count_player_formal,
					COUNT(cpa.player_id) as count_player_anonymous,
					COUNT(lf.player_id) as count_lev_formal,
					COUNT(la.player_id) as count_lev_anonymous';
	        }
	        $afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
	        $query->select(
	            ($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)),
	            DB::raw('COUNT(a.uid) as count_anonymous'),
	            DB::raw('COUNT(f.uid ) as count_formal'),
	            DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
	        )->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
    	}
        $this->filtersource($query, $source, $u1, $u2, '', $filter, $des = '');
        return $query;
    }


	/*
	* 德州扑克
	*/

	public function scopeUserPokerStat($query, $server_internal_id, $db_server, $start_time, $end_time, $interval, $filter, $source, $u1, $u2) {
		$query->leftJoin('users as f', function($join) {
		   		$join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=' , 0);
			})->leftJoin('users as a', function($join) {
				$join->on('a.uid', '=', 'u.uid')->where('a.is_anonymous', '=', 1);
			})->leftJoin('users as afu', function($join) {
				$join->on('afu.uid', '=', 'u.uid')
					->where('afu.is_anonymous', '=', 1)
					->where('afu.still_anonymous', '=', 0);
					// ->where('afu.nickname', 'NOT LIKE', '%@anonymous');	
			});

		$query->leftJoin('create_player as cpf', function($join) use ($server_internal_id) {
			$join->on('cpf.uid', '=', 'f.uid')
				->where('cpf.server_id', '=', $server_internal_id)->where('cpf.activate_time', '>', 0);	
		});
		$query->leftJoin('create_player as cpa', function($join) use ($server_internal_id) {
			$join->on('cpa.uid', '=', 'a.uid')
				->where('cpa.server_id', '=', $server_internal_id)->where('cpa.activate_time', '>', 0);
		});

		if ($server_internal_id > 0) {				
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
					$join->on('lf.player_id', '=', 'cpf.player_id')
						->where('lf.new_level', '=', 10);
				});
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
					$join->on('la.player_id', '=', 'cpa.player_id')
						->where('la.new_level', '=', 10);		
				});
		}

		$interval_sql = '';
		$filter_sql = '';
		$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

		if ($server_internal_id > 0) {
			$server_sql = 
				'COUNT(cpf.player_id) as count_player_formal,
				COUNT(cpa.player_id) as count_player_anonymous, 
				COUNT(lf.player_id) as count_lev_formal, 
				COUNT(la.player_id) as count_lev_anonymous';
		}	
		$afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
		$query->select(
			($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
			DB::raw('COUNT(a.uid) as count_anonymous'), 
			DB::raw('COUNT(f.uid) as count_formal'), 
			DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
		)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));

		$this->filtersource($query, $source, $u1, $u2, '', $filter, $des = '');
		return $query;	
	}




	private function selectTenMinute()
	{
		$interval = 600;
		return "FLOOR(UNIX_TIMESTAMP(u.created_time)/{$interval}) * {$interval} as ctime";
	}
	
	private function selectHour()
	{
		return 'UNIX_TIMESTAMP(DATE_FORMAT(u.created_time, \'%Y-%m-%d %H:00:00\')) as ctime';
	}

	private function selectDay()
	{
		return 'UNIX_TIMESTAMP(DATE_FORMAT(u.created_time, \'%Y-%m-%d\')) as ctime';
	}

	public function scopeSXDUserStat($query, $platform_server_id, $start_time, $end_time, $interval, $filter, $source, $u1, $u2) {
	    $query->leftJoin('users as f', function($join) {
	        $join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=' , 0);
	    })->leftJoin('users as a', function($join) {
	        $join->on('a.uid', '=', 'u.uid')->where('a.is_anonymous', '=', 1);
	    })->leftJoin('users as afu', function($join) {
	        $join->on('afu.uid', '=', 'u.uid')
	        ->where('afu.is_anonymous', '=', 1)
	        ->where('afu.still_anonymous', '=', 0);
	        // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
	    });
	
	        $query->leftJoin('create_player as cpf', function($join) use ($platform_server_id) {
	            $join->on('cpf.uid', '=', 'f.uid')
	            ->where('cpf.server_id', '=', $platform_server_id);
	        });
	        $query->leftJoin('create_player as cpa', function($join) use ($platform_server_id) {
	            $join->on('cpa.uid', '=', 'a.uid')
	            ->where('cpa.server_id', '=', $platform_server_id);
	        });
	        
	            if ($platform_server_id > 0) {
	            $query->leftJoin('created_10 as cp10f', function($join) use ($platform_server_id) {
	                $join->on('cp10f.uid', '=', 'f.uid')
	                ->where('cp10f.server_id', '=', $platform_server_id)
	                ->where('cp10f.is_ten', '=', '1');
	            });
	            $query->leftJoin('created_10 as cp10a', function($join) use ($platform_server_id) {
	                $join->on('cp10a.uid', '=', 'a.uid')
	                ->where('cp10a.server_id', '=', $platform_server_id)
	                ->where('cp10a.is_ten', '=', '1');;
	            });
	
	            }
	
	    		$interval_sql = '';
				$filter_sql = '';
				$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

	            if ($platform_server_id > 0) {
	                $server_sql =
	                'COUNT(cpf.player_id) as count_player_formal,
				COUNT(cpa.player_id) as count_player_anonymous,
				COUNT(cp10f.player_id) as count_lev_formal,
				COUNT(cp10a.player_id) as count_lev_anonymous';
	            }
	            $afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
	            $query->select(
	                    ($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)),
	                    DB::raw('COUNT(a.uid) as count_anonymous'),
	                    DB::raw('COUNT(f.uid) as count_formal'),
	                    DB::raw(($platform_server_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
	            )->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
	
	            $this->filtersource($query, $source, $u1, $u2, '', $filter, $des = '');
	            return $query;
	}
	public function scopeChannelOrder($query, $db_payment, $source, $u1, $u2,
	 								$reg_start_time, $reg_end_time,
	  								$order_start_time, $order_end_time,
	  								$filter, $currency_id, $is_anonymous, $game_id, $platform_id, $game_type=0) 
	{
		if(2 == $game_type){
			$query->leftJoin('device_list as dl', function($join) use ($game_id){
				$join->on('u.device_id', '=', 'dl.device_id')
					 ->where('dl.game_id', '=', $game_id);
			});
		}
		$this->channelOrderBasic($query, $db_payment, $source, $u1, $u2, $reg_start_time, $reg_end_time, $order_start_time, $order_end_time, $filter, $currency_id, $is_anonymous, $game_id, $platform_id);
		if ($filter == 'source') {
			$filter_sql = ', u.source';
			$query->groupBy('u.source');
		} else if ($filter == 'u1') {
			if(2 == $game_type){
				$filter_sql = ', dl.os_type, u.u as u1, u.source';
				$query->groupBy('dl.os_type', 'u.u', 'u.source');
			}else{
				$filter_sql = ', u.u as u1, u.source';
				$query->groupBy('u.u', 'u.source');
			}
		} else if ($filter == 'u2') {
			$filter_sql = ', u.u2, u.u as u1, u.source';
			$query->groupBy('u.u2', 'u.u', 'u.source');
		} else {
			$filter_sql = '';
		}
		$sql_currency = "select exchange from {$db_payment}.exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
		$query->selectRaw("
			SUM(o.pay_amount * o.exchange /({$sql_currency})) as total_amount,
			SUM(o.pay_amount * o.exchange) as total_dollar_amount,
			COUNT(DISTINCT(o.pay_user_id)) as pay_user_count
		" . $filter_sql);
		return $query;
	}

	public function scopeChannelOrderTotal($query, $db_payment, $source, $u1, $u2, $reg_start_time, $reg_end_time, $order_start_time, $order_end_time, $filter, $currency_id, $is_anonymous, $game_id, $platform_id)
	{
		$this->channelOrderBasic($query, $db_payment, $source, $u1, $u2, $reg_start_time, $reg_end_time, $order_start_time, $order_end_time, $filter, $currency_id, $is_anonymous, $game_id, $platform_id);
		$sql_currency = "select exchange from {$db_payment}.exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
		$query->selectRaw("
			SUM(o.pay_amount * o.exchange /({$sql_currency})) as total_amount,
			SUM(o.pay_amount * o.exchange) as total_dollar_amount,
			COUNT(DISTINCT(o.pay_user_id)) as pay_user_count
		");
		return $query;
	}

	private function channelOrderBasic($query, $db_payment, $source, $u1, $u2, $reg_start_time, $reg_end_time, $order_start_time, $order_end_time, $filter, $currency_id, $is_anonymous, $game_id, $platform_id)
	{
		$query->leftJoin("{$db_payment}.pay_order as o", function($join) use ($game_id) {
			$join->on('o.pay_user_id', '=', 'u.uid')	
				->where('o.get_payment', '=', 1)
				->where('o.game_id', '=', $game_id);
		});	
		if ($reg_start_time && $reg_end_time) {
			$query->whereBetween('u.created_time', array(
				date('Y-m-d H:i:s', $reg_start_time), date('Y-m-d H:i:s', $reg_end_time)
			));
		}	
		if ($order_start_time && $order_end_time) {
			$query->whereBetween('o.pay_time', array(
				$order_start_time, $order_end_time
			));
		}

		if ($source || $source === '0') {
			if('-1' == $source){
				$source = -1;
			}
			$query->where('u.source', $source);	
		}
		if ($u1) {
			$query->where('u.u', $u1);
		}
		if ($u2) {
			$query->where('u.u2', $u2);
		}
		if ($is_anonymous != null) {
			$query->where('u.is_anonymous', $is_anonymous);
		}
	}

	 public function scopeTHUserStat($query, $platform_server_id, $start_time, $end_time, $interval, $filter, $source, $u1, $u2, $game_id) {
            $query->leftJoin('users as f', function($join) use ($game_id) {
                $join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=' , 0)->where('f.game_id','=',$game_id);
            })->leftJoin('users as a', function($join) use ($game_id) {
                $join->on('a.uid', '=', 'u.uid')->where('a.is_anonymous', '=', 1)->where('a.game_id', '=', $game_id);
            })->leftJoin('users as afu', function($join) use ($game_id) {
                $join->on('afu.uid', '=', 'u.uid')
                ->where('afu.is_anonymous', '=', 1)
                ->where('afu.still_anonymous', '=', 0)->where('afu.game_id', '=', $game_id);
                // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
            });

        	$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

            $afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
            $query->select(
                    ($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)),
                    DB::raw('COUNT(a.uid) as count_anonymous'),
                    DB::raw('COUNT(f.uid) as count_formal'),
                    //DB::raw(($platform_server_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
                    DB::raw($afu_sql)
                    )->whereBetween(DB::raw('UNIX_TIMESTAMP(u.created_time)'), array($start_time, $end_time));

            $this->filtersource($query, $source, $u1, $u2, $game_id, $filter, $des = 'game_id');
            return $query;

	}
	
	private function filtersource($query, $source, $u1, $u2, $game_id, $filter, $des){
		if ($source || $source === '0') {
			if(strlen($source) > 16){
				$source = substr($source, 0, 16);
			}
			if($source == '-1'){
				$source = -1;
			}
            $query->where('u.source', $source);
        }
        if ($u1) {
        	if($u1 == '-1'){
				$u1 = -1;
			}
            $query->where('u.u', $u1);
        }
        if ($u2) {
        	if($u2 == '-1'){
				$u2 = -1;
			}
            $query->where('u.u2', $u2);
        }
        if($game_id && $des) {
            $query->where('u.'.$des,'=', $game_id);
        }

        if ($filter == 'source') {
            $query->orderBy('u.source', 'ASC');
        } else if ($filter == 'u1') {
            $query->orderBy('u.u', 'ASC')->orderBy('u.source', 'ASC');
        } else if ($filter == 'u2') {
            $query->orderBy('u.u', 'ASC')->orderBy('u.u2', 'ASC')->orderBy('u.source', 'ASC');
        }
	}

	private function filterInterval($query, $interval, $filter, &$interval_sql, &$filter_sql){
		if ($interval == 600) {
			$interval_sql = $this->selectTenMinute();
		} else if ($interval == 3600) {
			$interval_sql = $this->selectHour();
		} else if ($interval == 86400) {
			$interval_sql = $this->selectDay();
		}
		if ($interval > 0) {
			$query->groupBy('ctime')->orderBy('ctime', 'DESC');	
		}
		
		if ($filter == 'source') {
			$filter_sql = 'u.source';
			$query->groupBy('u.source');
		} else if ($filter == 'u1') {
			$filter_sql = 'u.u as u1, u.source';
			$query->groupBy('u.u', 'u.source');
		} else if ($filter == 'u2') {
			$filter_sql = 'u.u2, u.u as u1, u.source';
			$query->groupBy('u.u2', 'u.u', 'u.source');
		}
	}

	/*
	台湾女神和黑暗之光
	*/

	public function scopeUserStatTW($query, $server_internal_id, $db_server, $start_time, $end_time, $interval, $filter, $source, $u1, $u2 , $game_id, $classify) {
        if(0 == $classify){
	        $query->leftJoin('users as f', function($join)  use ($game_id) {
	            $join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=' , 0)->where('f.game_id', '=', $game_id);
	        })->leftJoin('users as afu', function($join) use ($game_id) {
            	$join->on('afu.uid', '=', 'u.uid')
                ->where('afu.is_anonymous', '=', 1)
                ->where('afu.still_anonymous', '=', 0)->where('afu.game_id', '=',$game_id);
        	});

			$query->leftJoin('create_player as cpf', function($join) use ($server_internal_id) {
				$join->on('cpf.uid', '=', 'f.uid')
					->where('cpf.server_id', '=', $server_internal_id);	
			});

			if ($server_internal_id > 0) {				
					$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
						$join->on('lf.player_id', '=', 'cpf.player_id')
							->where('lf.new_level', '=', 10);
					});
			}

			$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

			if ($server_internal_id > 0) {
				$server_sql = 
					'COUNT(cpf.player_id) as count_player_formal, 
					COUNT(lf.player_id) as count_lev_formal';
			}	
			$afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
			$query->select(
			($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(f.uid ) as count_formal'), 
				DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
			)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
        }elseif(1 == $classify){
            $query->leftJoin('users as a', function($join)  use ($game_id) {
                $join->on('a.uid', '=', 'u.uid')->where('a.is_anonymous', '=', 1)->where('a.game_id', '=', $game_id);
            });

    		$query->leftJoin('create_player as cpa', function($join) use ($server_internal_id) {
    			$join->on('cpa.uid', '=', 'a.uid')
    				->where('cpa.server_id', '=', $server_internal_id);
    		});

    		if ($server_internal_id > 0) {				
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
					$join->on('la.player_id', '=', 'cpa.player_id')
						->where('la.new_level', '=', 10);		
				});
    		}

    	    $interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

    		if ($server_internal_id > 0) {
    			$server_sql = 
    				'COUNT(cpa.player_id) as count_player_anonymous, 
    				COUNT(la.player_id) as count_lev_anonymous';
    		}	
    		$query->select(
    			($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
    			DB::raw('COUNT(a.uid) as count_anonymous'), 
    			DB::raw($server_sql)
    		)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));	
        }elseif(3 == $classify){
        	 $query->leftJoin('users as f', function($join)  use ($game_id) {
        	            $join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=' , 0)->where('f.game_id', '=', $game_id);
	        })->leftJoin('users as a', function($join)  use ($game_id) {
	            $join->on('a.uid', '=', 'u.uid')->where('a.is_anonymous', '=', 1)->where('a.game_id', '=', $game_id);
	        })->leftJoin('users as afu', function($join) use ($game_id) {
	            $join->on('afu.uid', '=', 'u.uid')
	                ->where('afu.is_anonymous', '=', 1)
	                ->where('afu.still_anonymous', '=', 0)->where('afu.game_id', '=',$game_id);
	                // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
	        });


			$query->leftJoin('create_player as cpf', function($join) use ($server_internal_id) {
				$join->on('cpf.uid', '=', 'f.uid')
					->where('cpf.server_id', '=', $server_internal_id);	
			});
			$query->leftJoin('create_player as cpa', function($join) use ($server_internal_id) {
				$join->on('cpa.uid', '=', 'a.uid')
					->where('cpa.server_id', '=', $server_internal_id);
			});

			if ($server_internal_id > 0) {				
					$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
						$join->on('lf.player_id', '=', 'cpf.player_id')
							->where('lf.new_level', '=', 10);
					});
					$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
						$join->on('la.player_id', '=', 'cpa.player_id')
							->where('la.new_level', '=', 10);		
					});
			}

			$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

			if ($server_internal_id > 0) {
				$server_sql = 
					'COUNT(cpf.player_id) as count_player_formal,
					COUNT(cpa.player_id) as count_player_anonymous, 
					COUNT(lf.player_id) as count_lev_formal, 
					COUNT(la.player_id) as count_lev_anonymous';
			}	
			$afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
			$query->select(
				($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(a.uid) as count_anonymous'), 
				DB::raw('COUNT(f.uid ) as count_formal'), 
				DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
			)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));

        }

		//
		$this->filtersource($query, $source, $u1, $u2, $game_id, $filter, $des = 'game_id');
		return $query;	
	}

	public function scopeUserStatTR($query, $server_internal_id, $db_server, $start_time, $end_time, $interval, $filter, $source, $u1, $u2 ,$game_id, $classify) 
	{
        if(0 == $classify){
	        $query->leftJoin('users as f', function($join)  use ($game_id) {
	            $join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=' , 0)->where('f.game_source', '=', $game_id);
	        })->leftJoin('users as afu', function($join) use ($game_id) {
	            $join->on('afu.uid', '=', 'u.uid')->where('afu.is_anonymous', '=', 1)->where('afu.still_anonymous', '=', 0)
	                ->where('afu.game_source', '=',$game_id);
	                // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
	        });
			
			$query->leftJoin('create_player as cpf', function($join) use ($server_internal_id) {
				$join->on('cpf.uid', '=', 'f.uid')
					->where('cpf.server_id', '=', $server_internal_id);	
			});
			
			//10级自己的游戏查log,代理的查created_10
			if ($server_internal_id > 0) {
				if($game_id == 44 || $game_id == 50){
					$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
						$join->on('lf.player_id', '=', 'cpf.player_id')
							->where('lf.new_level', '=', 10);
					});
				}
				if($game_id == 53 || $game_id == 38 || $game_id == 51 || $game_id == 64){
					$query->leftJoin('created_10 as ctf', function($join) {
						$join->on('cpf.uid', '=', 'ctf.uid')
							->where('ctf.is_ten', '=', 1);
					});
				}
			}

	    	$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

			if ($server_internal_id > 0) {
				if($game_id == 44 || $game_id == 50){
					$server_sql = 
						'COUNT(cpf.player_id) as count_player_formal,
						COUNT(lf.player_id) as count_lev_formal';
				}
				if($game_id == 53 || $game_id == 38 || $game_id == 51 || $game_id == 64){
					$server_sql = 
						'COUNT(cpf.player_id) as count_player_formal,
						COUNT(ctf.uid) as count_lev_formal';
				}
			}
			$afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
			$query->select(
				($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(f.uid ) as count_formal'), 
				DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
			)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
		}elseif(1 == $classify){
	        $query->leftJoin('users as a', function($join)  use ($game_id) {
	            $join->on('a.uid', '=', 'u.uid')->where('a.is_anonymous', '=', 1)->where('a.game_source', '=', $game_id);
	        });
			
			$query->leftJoin('create_player as cpa', function($join) use ($server_internal_id) {
				$join->on('cpa.uid', '=', 'a.uid')
					->where('cpa.server_id', '=', $server_internal_id);
			});

			//10级自己的游戏查log,代理的查created_10
			if ($server_internal_id > 0) {
				if($game_id == 44 || $game_id == 50){
					$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
						$join->on('la.player_id', '=', 'cpa.player_id')
							->where('la.new_level', '=', 10);		
					});
				}
				if($game_id == 53 || $game_id == 38 || $game_id == 51 || $game_id == 64){
					$query->leftJoin('created_10 as cta', function($join) {
						$join->on('cpa.uid', '=', 'cta.uid')
							->where('cta.is_ten', '=', 1);
					});
				}
			}

	    	$interval_sql = '';
			$filter_sql = '';
			$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

			if ($server_internal_id > 0) {
				if($game_id == 44 || $game_id == 50){
					$server_sql = 
						'COUNT(cpf.player_id) as count_player_formal,
						COUNT(cpa.player_id) as count_player_anonymous, 
						COUNT(lf.player_id) as count_lev_formal, 
						COUNT(la.player_id) as count_lev_anonymous';
				}
				if($game_id == 53 || $game_id == 38 || $game_id == 51 || $game_id == 64){
					$server_sql = 
						'COUNT(cpa.player_id) as count_player_anonymous, 
						COUNT(cta.uid) as count_lev_anonymous';
				}
			}
			$query->select(
				($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(a.uid) as count_anonymous'), 
				DB::raw($server_sql)
			)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
		}elseif(3 == $classify){
	        $query->leftJoin('users as f', function($join)  use ($game_id) {
	            $join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=' , 0)->where('f.game_source', '=', $game_id);
	        })->leftJoin('users as a', function($join)  use ($game_id) {
	            $join->on('a.uid', '=', 'u.uid')->where('a.is_anonymous', '=', 1)->where('a.game_source', '=', $game_id);
	        })->leftJoin('users as afu', function($join) use ($game_id) {
	            $join->on('afu.uid', '=', 'u.uid')->where('afu.is_anonymous', '=', 1)->where('afu.still_anonymous', '=', 0)
	                ->where('afu.game_source', '=',$game_id);
	                // ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
	        });
			
			$query->leftJoin('create_player as cpf', function($join) use ($server_internal_id) {
				$join->on('cpf.uid', '=', 'f.uid')
					->where('cpf.server_id', '=', $server_internal_id);	
			});
			$query->leftJoin('create_player as cpa', function($join) use ($server_internal_id) {
				$join->on('cpa.uid', '=', 'a.uid')
					->where('cpa.server_id', '=', $server_internal_id);
			});

			//10级自己的游戏查log,代理的查created_10
			if ($server_internal_id > 0) {
				if($game_id == 44 || $game_id == 50){
					$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
						$join->on('lf.player_id', '=', 'cpf.player_id')
							->where('lf.new_level', '=', 10);
					});
					$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
						$join->on('la.player_id', '=', 'cpa.player_id')
							->where('la.new_level', '=', 10);		
					});
				}
				if($game_id == 53 || $game_id == 38 || $game_id == 51 || $game_id == 64){
					$query->leftJoin('created_10 as ctf', function($join) {
						$join->on('cpf.uid', '=', 'ctf.uid')
							->where('ctf.is_ten', '=', 1);
					});
					$query->leftJoin('created_10 as cta', function($join) {
						$join->on('cpa.uid', '=', 'cta.uid')
							->where('cta.is_ten', '=', 1);
					});
				}
			}

		    	$interval_sql = '';
				$filter_sql = '';
				$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

			if ($server_internal_id > 0) {
				if($game_id == 44 || $game_id == 50){
					$server_sql = 
						'COUNT(cpf.player_id) as count_player_formal,
						COUNT(cpa.player_id) as count_player_anonymous, 
						COUNT(lf.player_id) as count_lev_formal, 
						COUNT(la.player_id) as count_lev_anonymous';
				}
				if($game_id == 53 || $game_id == 38 || $game_id == 51 || $game_id == 64){
					$server_sql = 
						'COUNT(cpf.player_id) as count_player_formal,
						COUNT(cpa.player_id) as count_player_anonymous, 
						COUNT(ctf.uid) as count_lev_formal, 
						COUNT(cta.uid) as count_lev_anonymous';
				}
			}
			$afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
			$query->select(
				($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
				DB::raw('COUNT(a.uid) as count_anonymous'), 
				DB::raw('COUNT(f.uid ) as count_formal'), 
				DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
			)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
		}
		$this->filtersource($query, $source, $u1, $u2, $game_id, $filter, $des = 'game_source');

		return $query;	
	}

	public function scopeTableChannelOrder($query, $db_payment,
	  								$order_start_time, $order_end_time,
	  								$currency_id, $game_id, $platform_id) 
	{
		$this->channelTableOrderBasic($query, $db_payment, $order_start_time, $order_end_time, $currency_id, $game_id, $platform_id);
	
		$query->groupBy('u.u2', 'u.u', 'u.source');
		$sql_currency = "select exchange from {$db_payment}.exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
		$query->selectRaw("u.source,u.u as u1,u.u2,UNIX_TIMESTAMP(u.created_time) as created_time,o.pay_time,
			SUM(o.pay_amount * o.exchange /({$sql_currency})) as total_amount,
			SUM(o.pay_amount * o.exchange) as total_dollar_amount,
			COUNT(DISTINCT(o.pay_user_id)) as pay_user_count");
		return $query;
	}
	private function channelTableOrderBasic($query, $db_payment, $order_start_time, $order_end_time, $currency_id, $game_id, $platform_id)
	{
		$query->leftJoin("{$db_payment}.pay_order as o", function($join) {
			$join->on('o.pay_user_id', '=', 'u.uid')	
				->where('o.get_payment', '=', 1);
		});	

		if ($order_start_time && $order_end_time) {
			$query->whereBetween('o.pay_time', array(
				$order_start_time, $order_end_time
			));
		}
		
		//土耳其 和 印尼IdGameLand 和 game168 VNWebGame THWebGame IDWebGame 英文风流三国
		if ($platform_id == 50 || $platform_id == 38 || $platform_id == 1 || $platform_id == 2 || $platform_id == 3 || $platform_id == 4 || $platform_id == 29){
			$query->where('u.game_source', $game_id);
		}

	}
	public function scopeAdTableDateTW($query, $server_internal_id, $db_server, $start_time, $end_time, $interval, $game_id) {
        $query->leftJoin('users as f', function($join)  use ($game_id) {
            $join->on('f.uid', '=', 'u.uid')->where('f.is_anonymous', '=' , 0)->where('f.game_source', '=', $game_id);
        })->leftJoin('users as a', function($join)  use ($game_id) {
            $join->on('a.uid', '=', 'u.uid')->where('a.is_anonymous', '=', 1)->where('a.game_source', '=', $game_id);
        })->leftJoin('users as afu', function($join) use ($game_id) {
            $join->on('afu.uid', '=', 'u.uid')
                ->where('afu.is_anonymous', '=', 1)
                ->where('afu.still_anonymous', '=', 0)->where('afu.game_source', '=',$game_id)
                 ->where('afu.nickname', 'NOT LIKE', '%@anonymous');
        });


		$query->leftJoin('create_player as cpf', function($join) use ($server_internal_id) {
			$join->on('cpf.uid', '=', 'f.uid')
				->where('cpf.server_id', '=', $server_internal_id);	
		});
		$query->leftJoin('create_player as cpa', function($join) use ($server_internal_id) {
			$join->on('cpa.uid', '=', 'a.uid')
				->where('cpa.server_id', '=', $server_internal_id);
		});

		if ($server_internal_id > 0) {				
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as lf"), function($join) {
				$join->on('lf.player_id', '=', 'cpf.player_id')
					->where('lf.new_level', '=', 10);
			});
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as la"), function($join) {
				$join->on('la.player_id', '=', 'cpa.player_id')
					->where('la.new_level', '=', 10);		
			});
		}

		$interval_sql = $this->selectDay();
		
		$query->groupBy('ctime')->orderBy('ctime', 'DESC');	
		
		
		$filter_sql = 'u.u2, u.u as u1, u.source';
		$query->groupBy('u.u2', 'u.u', 'u.source');

		if ($server_internal_id > 0) {
			$server_sql = 
				'COUNT(cpf.player_id) as count_player_formal,
				COUNT(cpa.player_id) as count_player_anonymous, 
				COUNT(lf.player_id) as count_lev_formal, 
				COUNT(la.player_id) as count_lev_anonymous';
		}	
		$afu_sql = 'COUNT(afu.uid) as count_anonymous_formal';
		$query->select(
			(DB::raw($interval_sql . ', ' . $filter_sql)), 
			DB::raw('COUNT(a.uid) as count_anonymous'), 
			DB::raw('COUNT(f.uid ) as count_formal'), 
			DB::raw(($server_internal_id > 0) ? $afu_sql . ', ' . $server_sql : $afu_sql)
		)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
		
		$query->orderBy('u.u', 'ASC')->orderBy('u.u2', 'ASC')->orderBy('u.source', 'ASC');
		return $query;	
	}

	public function scopeGetSignupNum($query, $start_time, $end_time, $judge_game, $judge_create_player_game, $game_id, $anonymous, $server_internal_id = 0){	//简约版注册用户统计--Panda
		if(in_array($game_id, Config::get('game_config.yysggameids'))){
			if(in_array($game_id, array(76,81))){	//大陆使用的是.5
				$db_name = $game_id.'.5';
			}else{
				$db_name = $game_id.'.1';
			}
			$query->leftJoin(DB::raw('`'.$db_name.'`.log_create_player as cp'), function($join){
				$join->on('u.uid', '=', 'cp.uid');	
			});
		}else{
			if($server_internal_id){
				$query->leftJoin('create_player as cp', function($join) use ($judge_create_player_game, $game_id, $server_internal_id){
					$join->on('u.uid', '=', 'cp.uid')
						->where('cp.server_id', '=', $server_internal_id);
					if($judge_create_player_game){
						$join->where($judge_create_player_game, '=', $game_id);
					}
				});
			}else{
				$query->leftJoin('create_player as cp', function($join) use ($judge_create_player_game, $game_id){
					$join->on('u.uid', '=', 'cp.uid');
					if($judge_create_player_game){
						$join->where($judge_create_player_game, '=', $game_id);
					}
				});
			}
		}
		$query->whereBetween('u.created_time', array($start_time, $end_time))->where('is_anonymous', $anonymous);
		if($judge_game){
			$query->where($judge_game, $game_id);
		}
		if(in_array($game_id, Config::get('game_config.yysggameids'))){
			$query->selectRaw('count(DISTINCT u.uid) as unum, count(distinct cp.player_id) as cpnum');
		}else{
			$query->selectRaw('count(DISTINCT u.uid) as unum, count(distinct cp.create_player_id) as cpnum');
		}
		return $query;
	}

	public function scopeGetcreateNum($query, $start_time, $end_time, $judge_game, $judge_create_player_game, $game_id, $anonymous, $server_internal_id = 0){	//简约版注册用户统计--Panda,这个功能与上面的功能不同在于表连接用的是join
		if(in_array($game_id, Config::get('game_config.yysggameids'))){
			if(in_array($game_id, array(76,81))){	//大陆使用的是.5
				$db_name = $game_id.'.5';
			}else{
				$db_name = $game_id.'.1';
			}
			$query->Join(DB::raw('`'.$db_name.'`.log_create_player as cp'), function($join){
				$join->on('u.uid', '=', 'cp.uid');	
			});
		}else{
			if($server_internal_id){
				$query->Join('create_player as cp', function($join) use ($judge_create_player_game, $game_id, $server_internal_id){
					$join->on('u.uid', '=', 'cp.uid')
						->where('cp.server_id', '=', $server_internal_id);
					if($judge_create_player_game){
						$join->where($judge_create_player_game, '=', $game_id);
					}
				});
			}else{
				$query->Join('create_player as cp', function($join) use ($judge_create_player_game, $game_id){
					$join->on('u.uid', '=', 'cp.uid');
					if($judge_create_player_game){
						$join->where($judge_create_player_game, '=', $game_id);
					}
				});
			}
		}
		$query->whereBetween('u.created_time', array($start_time, $end_time))->where('still_anonymous', $anonymous);
		if($judge_game){
			$query->where($judge_game, $game_id);
		}
		$query->selectRaw('count(1) as cpnum');
		return $query;
	}

	public function scopeGetUserStatSignup($query, $start_time, $end_time, $interval, $filter, $source, $u1, $u2){
		$interval_sql = '';
		$filter_sql = '';
		$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);
		$this->filtersource($query, $source, $u1, $u2, '', $filter, '');

		$query->select(
			($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
			DB::raw('COUNT(1) as all_signup'), 
			DB::raw('sum(u.is_anonymous) as anonymous_signup'), 
			DB::raw('sum(u.still_anonymous) as still_anonymous_signup')
		)->whereBetween(DB::raw('u.created_time'), array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time))); 
		return $query;   	
	}

	public function scopeGetUserStatCreateplayer($query, $game_id, $server_internal_id, $start_time, $end_time, $interval, $filter, $source, $u1, $u2){
		if(in_array($game_id, Config::get('game_config.yysggameids'))){
			$query->join(DB::raw('`'.$game_id.'.1`.log_create_player as cp'), function($join) use ($source, $u1, $u2, $start_time, $end_time){
				$join->on('u.uid', '=', 'cp.uid')
				     ->where('cp.created_time', '>', $start_time)
					 ->where('u.created_time', '>', date("Y-m-d H:i:s", $start_time))
					 ->where('u.created_time', '<', date("Y-m-d H:i:s", $end_time));
				if ($source || $source === '0') {
					if(strlen($source) > 16){
						$source = substr($source, 0, 16);
					}
					if('-1' == $source){
						$source = -1;
					}
		            $join->where('u.source', '=', $source);
		        }
		        if ($u1) {
		        	if('-1' == $u1){
						$u1 = -1;
					}
		            $join->where('u.u', '=', $u1);
		        }
		        if ($u2) {
		        	if('-1' == $u2){
						$u2 = -1;
					}
		            $join->where('u.u2', '=', $u2);
		        }
			});
		}else{
			$query->join('create_player as cp', function($join) use ($source, $u1, $u2, $start_time, $end_time, $server_internal_id){
				$join->on('u.uid', '=', 'cp.uid')
					->where('cp.server_id', '=', $server_internal_id)
					->where('cp.created_time', '>', $start_time)
					->where('u.created_time', '>', date("Y-m-d H:i:s", $start_time))
					->where('u.created_time', '<', date("Y-m-d H:i:s", $end_time));
				if ($source || $source === '0') {
					if(strlen($source) > 16){
						$source = substr($source, 0, 16);
					}
					if('-1' == $source){
						$source = -1;
					}
		            $join->where('u.source', '=', $source);
		        }
		        if ($u1) {
		        	if('-1' == $u1){
						$u1 = -1;
					}
		            $join->where('u.u', '=', $u1);
		        }
		        if ($u2) {
		        	if('-1' == $u2){
						$u2 = -1;
					}
		            $join->where('u.u2', '=', $u2);
		        }
			});
		}

		$interval_sql = '';
		$filter_sql = '';
		$this->filterInterval($query, $interval, $filter, $interval_sql, $filter_sql);

		$query->select(
			($interval_sql ? DB::raw($interval_sql . ', ' . $filter_sql) : DB::raw($filter_sql)), 
			DB::raw('COUNT(1) as all_create'), 
			DB::raw('sum(u.is_anonymous) as anonymous_create'), 
			DB::raw('sum(u.still_anonymous) as still_anonymous_create')
		); 
		return $query;  		
	}

	public function scopeGetBasicCount($query, $start_time, $end_time, $game_id, $db_name, $is_game_source, $create_player_single){
	    if(in_array($game_id, Config::get('game_config.yysggameids'))){
	    	$query->leftJoin(DB::raw("`{$db_name}`.log_create_player as cp"),function($join){
	    		$join->on('cp.uid', '=', 'u.uid');
	    	});
	    }else{
	    	$query->leftJoin('create_player as cp',function($join) use ($create_player_single, $game_id){
	    		$join->on('cp.uid', '=', 'u.uid');
	    		if($create_player_single){
	    			$join->where('cp.game_id', '=' , $game_id);
	    		}
	    	});
	    }//注册、注册未登录、注册未创建
	    $query->selectRaw("FROM_UNIXTIME(UNIX_TIMESTAMP(u.created_time), '%Y-%m-%d') as time,count(distinct u.uid) as count_user,count(if(u.created_time=u.last_visit_time, 1 , null)) as reg_no_login, count(distinct cp.uid) as reg_create");
	    $query->whereBetween('u.created_time',array(date("Y-m-d H:i:s", $start_time) , date("Y-m-d H:i:s", $end_time)));
	    if(1 == $is_game_source){
	    	$query->where('u.game_source', $game_id);
	    }
	    return $query;

	}

}