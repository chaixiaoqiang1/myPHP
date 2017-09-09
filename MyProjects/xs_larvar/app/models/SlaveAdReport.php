<?php

class SlaveAdReport extends Eloquent {

	protected $table = 'ad_report as a';

	protected $primaryKey = 'report_id';

	public $timestamps = false;

	protected function getDateFormat()
	{
		return 'U';
	}

	public function scopeGetFBStat($query, $db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $db_server)
	{
		//输入的日期当成美国时间
		$us_start_time = date('Y-m-d H:i:s', $start_time);
		$us_end_time = date('Y-m-d H:i:s' , $end_time);
// 		$s = "#####输入的facebook开始时间是".$us_start_time."  输入的facebook结束时间是".$us_end_time."####\n";
// 		Log::info($s);
		
		$start_time = date('Y-m-d H:i:s', $start_time + (int)$diff_hours*3600);
		$end_time = date('Y-m-d H:i:s', $end_time + (int)$diff_hours*3600);
		
// 		$s = "#####转化后的开始时间是".$start_time."  转化后的结束时间是".$end_time."####\n";
// 		Log::info($s);
		$sub_sql = "(SELECT SUM(spent) as spent, u1, ad_name FROM ad_report WHERE date BETWEEN '{$us_start_time}' AND '{$us_end_time}' GROUP BY u1, ad_name) as x";
// 		Log::info($sub_sql);
		$query->leftJoin(DB::raw($sub_sql), function($join){
			$join->on('x.u1', '=', 'a.u1')
				->on('x.ad_name', '=', 'a.ad_name');
		});
		$query->leftJoin("{$db_qiqiwu}.users as u", function($join) use ($start_time, $end_time) {
			$join->on('u.u', '=', 'a.u1')
				->on('u.u2', '=', 'a.ad_name')
				->where('u.source', '=', 'fb')
				->where('u.created_time', '>=', $start_time)
				->where('u.created_time', '<=', $end_time);
		});
		$query->leftJoin("{$db_qiqiwu}.users as fu", function($join) {
			$join->on('fu.uid', '=', 'u.uid')
				->where('fu.is_anonymous', '=', 0);
		});
		$query->leftJoin("{$db_qiqiwu}.users as au", function($join) {
			$join->on('au.uid', '=', 'u.uid')
				->where('au.is_anonymous', '=', 1)
				->where('au.still_anonymous', '=', 0);
		});
		
		if($server_internal_id){
			$query->leftJoin("{$db_qiqiwu}.create_player as p", function($join) use ($server_internal_id){
				$join->on('p.uid', '=', 'u.uid')
				    ->where('p.server_id', '=', $server_internal_id);
			});
			$query->leftJoin("{$db_qiqiwu}.create_player as fp", function($join) use ($server_internal_id){
				$join->on('fp.uid', '=', 'fu.uid')
				 	->where('fp.server_id', '=', $server_internal_id);
			});
			$query->leftJoin("{$db_qiqiwu}.create_player as ap", function($join) use ($server_internal_id){
				$join->on('ap.uid', '=', 'au.uid')
				 	->where('ap.server_id', '=', $server_internal_id);
			});			

			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as l"), function($join) {
				$join->on('l.player_id', '=', 'p.player_id')
					->where('l.new_level', '=', 10);
			});
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as fl"), function($join) {
				$join->on('fl.player_id', '=', 'fp.player_id')
					->where('fl.new_level', '=', 10);
			});
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as al"), function($join) {
				$join->on('al.player_id', '=', 'ap.player_id')
					->where('al.new_level', '=', 10);
			});
		}else{
			$query->leftJoin("{$db_qiqiwu}.create_player as p", function($join) {
				$join->on('p.uid', '=', 'u.uid');
			});
			$query->leftJoin("{$db_qiqiwu}.create_player as fp", function($join) {
				$join->on('fp.uid', '=', 'fu.uid');
			});
			$query->leftJoin("{$db_qiqiwu}.create_player as ap", function($join) {
				$join->on('ap.uid', '=', 'au.uid');
			});
		}

		$query->whereBetween('a.date', array($us_start_time, $us_end_time));

		if ($u1) {
			$query->where('a.u1', '=', $u1);
		}
		if ($u2) {
			$query->where('a.ad_name', '=', $u2);
		}

		$query->selectRaw(
			'
			a.campaign,
			a.u1 as fb_u1,
			a.ad_name as fb_u2,
			MAX(click_through_rate) as click_through_rate,
			x.spent,
			COUNT(DISTINCT(fu.uid)) as count_formal_user,
			COUNT(DISTINCT(au.uid))as count_anonymous_user,
			COUNT(DISTINCT(u.uid)) as total_user,
			COUNT(DISTINCT(fp.player_id)) as count_formal_player,
			COUNT(DISTINCT(ap.player_id)) as count_anonymous_player,
			COUNT(DISTINCT(p.player_id)) as total_player
			'.
			($server_internal_id > 0 ? ', 
			COUNT(DISTINCT(fl.player_id)) as count_formal_lev,
			COUNT(DISTINCT(al.player_id)) as count_anonymous_lev,
			COUNT(DISTINCT(l.player_id)) as total_lev
			' : '')
		);
		
		$query->groupBy('a.u1', 'a.ad_name');
		$query->orderBy('a.u1', 'a.ad_name');
		return $query;
	}


	/*
	 *德州扑克
	*/

	public function scopeGetPokerFBStat($query, $db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $db_server)
	{
		//输入的日期当成美国时间
		$us_start_time = date('Y-m-d H:i:s', $start_time);
		$us_end_time = date('Y-m-d H:i:s' , $end_time);
// 		$s = "#####输入的facebook开始时间是".$us_start_time."  输入的facebook结束时间是".$us_end_time."####\n";
// 		Log::info($s);
		
		$start_time = date('Y-m-d H:i:s', $start_time + (int)$diff_hours*3600);
		$end_time = date('Y-m-d H:i:s', $end_time + (int)$diff_hours*3600);
		
// 		$s = "#####转化后的开始时间是".$start_time."  转化后的结束时间是".$end_time."####\n";
// 		Log::info($s);
		$sub_sql = "(SELECT SUM(spent) as spent, u1, ad_name FROM ad_report WHERE date BETWEEN '{$us_start_time}' AND '{$us_end_time}' GROUP BY u1, ad_name) as x";
// 		Log::info($sub_sql);
		$query->leftJoin(DB::raw($sub_sql), function($join){
			$join->on('x.u1', '=', 'a.u1')
				->on('x.ad_name', '=', 'a.ad_name');
		});
		$query->leftJoin("{$db_qiqiwu}.users as u", function($join) use ($start_time, $end_time) {
			$join->on('u.u', '=', 'a.u1')
				->on('u.u2', '=', 'a.ad_name')
				->where('u.created_time', '>=', $start_time)
				->where('u.created_time', '<=', $end_time)
				->where('u.source', '=', 'fb');
		});
		$query->leftJoin("{$db_qiqiwu}.users as fu", function($join) {
			$join->on('fu.uid', '=', 'u.uid')
				->where('fu.is_anonymous', '=', 0);
		});
		$query->leftJoin("{$db_qiqiwu}.users as au", function($join) {
			$join->on('au.uid', '=', 'u.uid')
				->where('au.is_anonymous', '=', 1)
				->where('au.still_anonymous', '=', 0);
				// ->where('au.nickname', 'NOT LIKE', '%@anonymous');	
		});
		
		$query->leftJoin("{$db_qiqiwu}.create_player as p", function($join) use ($server_internal_id) {
			$join->on('p.uid', '=', 'u.uid');//->where('p.activate_time','>',0);
			if ($server_internal_id) {
				$join->where('p.server_id', '=', $server_internal_id);
			}
		});
		$query->leftJoin("{$db_qiqiwu}.create_player as fp", function($join) use ($server_internal_id) {
			$join->on('fp.uid', '=', 'fu.uid');//->where('fp.activate_time','>',0);
			if ($server_internal_id) {
				$join->where('fp.server_id', '=', $server_internal_id);
			}
		});
		$query->leftJoin("{$db_qiqiwu}.create_player as ap", function($join) use ($server_internal_id) {
			$join->on('ap.uid', '=', 'au.uid');//->where('ap.activate_time','>',0);
			if ($server_internal_id) {
				$join->where('ap.server_id', '=', $server_internal_id);
			}
		});

		if ($server_internal_id > 0) {
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as l"), function($join) {
				$join->on('l.player_id', '=', 'p.player_id')
					->where('l.new_level', '=', 10);
			});
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as fl"), function($join) {
				$join->on('fl.player_id', '=', 'fp.player_id')
					->where('fl.new_level', '=', 10);
			});
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as al"), function($join) {
				$join->on('al.player_id', '=', 'ap.player_id')
					->where('al.new_level', '=', 10);
			});
		}

		$query->whereBetween('a.date', array($us_start_time, $us_end_time));

		if ($u1) {
			$query->where('a.u1', '=', $u1);
		}
		if ($u2) {
			$query->where('a.ad_name', '=', $u2);
		}

		$query->selectRaw(
			'
			a.campaign,
			a.u1 as fb_u1,
			a.ad_name as fb_u2,
			MAX(click_through_rate) as click_through_rate,
			x.spent,
			COUNT(DISTINCT(fu.uid)) as count_formal_user,
			COUNT(DISTINCT(au.uid))as count_anonymous_user,
			COUNT(DISTINCT(u.uid)) as total_user,
			COUNT(DISTINCT(fp.player_id)) as count_formal_player,
			COUNT(DISTINCT(ap.player_id)) as count_anonymous_player,
			COUNT(DISTINCT(p.player_id)) as total_player
			'.
			($server_internal_id > 0 ? ', 
			COUNT(DISTINCT(fl.player_id)) as count_formal_lev,
			COUNT(DISTINCT(al.player_id)) as count_anonymous_lev,
			COUNT(DISTINCT(l.player_id)) as total_lev
			' : '')
		);
		
		$query->groupBy('a.u1', 'a.ad_name');
		return $query;
	}

	public function scopeSXDGetFBStat($query, $db_qiqiwu, $game_id, $start_time, $end_time, $u1, $u2, $server_internal_id)
	{
		//输入的日期当成美国时间
		$us_start_time = date('Y-m-d H:i:s', $start_time);
		$us_end_time = date('Y-m-d H:i:s' , $end_time);
		
		if($game_id == 3 || $game_id == 4 || $game_id == 38){//印尼泰国 slave
		    $start_time = date('Y-m-d H:i:s', $start_time + 14*3600);
		    $end_time = date('Y-m-d H:i:s', $end_time + 14*3600);
		}else {
		    $start_time = date('Y-m-d H:i:s', $start_time + 15*3600);
		    $end_time = date('Y-m-d H:i:s', $end_time + 15*3600);
		}
		

		$sub_sql = "(SELECT SUM(spent) as spent, u1, ad_name FROM ad_report WHERE date BETWEEN '{$us_start_time}' AND '{$us_end_time}' GROUP BY u1, ad_name) as x";
		$query->leftJoin(DB::raw($sub_sql), function($join){
			$join->on('x.u1', '=', 'a.u1')
				->on('x.ad_name', '=', 'a.ad_name');
		});
		$query->leftJoin("{$db_qiqiwu}.users as u", function($join) use ($start_time, $end_time) {
			$join->on('u.u', '=', 'a.u1')
				->on('u.u2', '=', 'a.ad_name')
				->where('u.created_time', '>=', $start_time)
				->where('u.created_time', '<=', $end_time)
				->where('u.source', '=', 'fb');
		});
		$query->leftJoin("{$db_qiqiwu}.users as fu", function($join) {
			$join->on('fu.uid', '=', 'u.uid')
				->where('fu.is_anonymous', '=', 0);
		});
		$query->leftJoin("{$db_qiqiwu}.users as au", function($join) {
			$join->on('au.uid', '=', 'u.uid')
				->where('au.is_anonymous', '=', 1)
				->where('au.still_anonymous', '=', 0);
				// ->where('au.nickname', 'NOT LIKE', '%@anonymous');	
		});
		
		$query->leftJoin("{$db_qiqiwu}.create_player as p", function($join) use ($server_internal_id) {
			$join->on('p.uid', '=', 'u.uid');
			if ($server_internal_id) {
				$join->where('p.server_id', '=', $server_internal_id);
			}
		});
		$query->leftJoin("{$db_qiqiwu}.create_player as fp", function($join) use ($server_internal_id) {
			$join->on('fp.uid', '=', 'fu.uid');
			if ($server_internal_id) {
				$join->where('fp.server_id', '=', $server_internal_id);
			}
		});
		$query->leftJoin("{$db_qiqiwu}.create_player as ap", function($join) use ($server_internal_id) {
			$join->on('ap.uid', '=', 'au.uid');
			if ($server_internal_id) {
				$join->where('ap.server_id', '=', $server_internal_id);
			}
		});

		if ($server_internal_id > 0) {
			$query->leftJoin("{$db_qiqiwu}.created_10 as l", function($join) use ($server_internal_id) {
				$join->on('l.uid', '=', 'u.uid')
				->where('l.server_id', '=', $server_internal_id)
					->where('l.is_ten', '=', 1);
			});
			$query->leftJoin("{$db_qiqiwu}.created_10 as fl", function($join) use ($server_internal_id) {
				$join->on('fl.uid', '=', 'fu.uid')
				->where('fl.server_id', '=', $server_internal_id)
					->where('l.is_ten', '=', 1);
			});
			$query->leftJoin("{$db_qiqiwu}.created_10 as al", function($join) use ($server_internal_id) {
				$join->on('al.uid', '=', 'au.uid')
				->where('al.server_id', '=', $server_internal_id)
					->where('l.is_ten', '=', 1);
			});
		}

		$query->whereBetween('a.date', array($us_start_time, $us_end_time));

		if ($u1) {
			$query->where('a.u1', '=', $u1);
		}
		if ($u2) {
			$query->where('a.ad_name', '=', $u2);
		}

		$query->selectRaw(
			'
			a.campaign,
			a.u1 as fb_u1,
			a.ad_name as fb_u2,
			MAX(click_through_rate) as click_through_rate,
			x.spent,
			COUNT(DISTINCT(fu.uid)) as count_formal_user,
			COUNT(DISTINCT(au.uid))as count_anonymous_user,
			COUNT(DISTINCT(u.uid)) as total_user,
			COUNT(DISTINCT(fp.player_id)) as count_formal_player,
			COUNT(DISTINCT(ap.player_id)) as count_anonymous_player,
			COUNT(DISTINCT(p.player_id)) as total_player
			'.
			($server_internal_id > 0 ? ', 
			COUNT(DISTINCT(fl.player_id)) as count_formal_lev,
			COUNT(DISTINCT(al.player_id)) as count_anonymous_lev,
			COUNT(DISTINCT(l.player_id)) as total_lev
			' : '')
		);
		
		$query->groupBy('a.u1', 'a.ad_name');
		$query->orderBy('a.u1', 'a.ad_name');
		return $query;
	}

	public function scopeGetFBStatTR($query, $db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $db_server)
	{
		//输入的日期当成美国时间
		$us_start_time = date('Y-m-d H:i:s', $start_time);
		$us_end_time = date('Y-m-d H:i:s' , $end_time);
// 		$s = "#####输入的facebook开始时间是".$us_start_time."  输入的facebook结束时间是".$us_end_time."####\n";
// 		Log::info($s);
		
		$start_time = date('Y-m-d H:i:s', $start_time + (int)$diff_hours*3600);
		$end_time = date('Y-m-d H:i:s', $end_time + (int)$diff_hours*3600);
		
// 		$s = "#####转化后的开始时间是".$start_time."  转化后的结束时间是".$end_time."####\n";
// 		Log::info($s);
		$sub_sql = "(SELECT SUM(spent) as spent, u1, ad_name FROM ad_report WHERE date BETWEEN '{$us_start_time}' AND '{$us_end_time}' GROUP BY u1, ad_name) as x";
// 		Log::info($sub_sql);
		$query->leftJoin(DB::raw($sub_sql), function($join){
			$join->on('x.u1', '=', 'a.u1')
				->on('x.ad_name', '=', 'a.ad_name');
		});
		$query->leftJoin("{$db_qiqiwu}.users as u", function($join) use ($start_time, $end_time, $game_id) {
			$join->on('u.u', '=', 'a.u1')
				->on('u.u2', '=', 'a.ad_name')
				->where('u.created_time', '>=', $start_time)
				->where('u.created_time', '<=', $end_time)
				->where('u.source', '=', 'fb')
				->where('u.game_source', '=', $game_id);
		});
		$query->leftJoin("{$db_qiqiwu}.users as fu", function($join) use ($game_id) {
			$join->on('fu.uid', '=', 'u.uid')
				->where('fu.is_anonymous', '=', 0)
				->where('fu.game_source', '=', $game_id);
		});
		$query->leftJoin("{$db_qiqiwu}.users as au", function($join) use ($game_id) {
			$join->on('au.uid', '=', 'u.uid')
				->where('au.is_anonymous', '=', 1)
				->where('au.still_anonymous', '=', 0)
				->where('au.game_source', '=', $game_id);
				// ->where('au.nickname', 'NOT LIKE', '%@anonymous');	
		});
		
		$query->leftJoin("{$db_qiqiwu}.create_player as p", function($join) use ($server_internal_id, $game_id) {
			$join->on('p.uid', '=', 'u.uid');
			if ($server_internal_id) {
				$join->where('p.server_id', '=', $server_internal_id);
			}
		});
		$query->leftJoin("{$db_qiqiwu}.create_player as fp", function($join) use ($server_internal_id) {
			$join->on('fp.uid', '=', 'fu.uid');
			if ($server_internal_id) {
				$join->where('fp.server_id', '=', $server_internal_id);
			}
		});
		$query->leftJoin("{$db_qiqiwu}.create_player as ap", function($join) use ($server_internal_id) {
			$join->on('ap.uid', '=', 'au.uid');
			if ($server_internal_id) {
				$join->where('ap.server_id', '=', $server_internal_id);
			}
		});

		if ($server_internal_id > 0) {
			if($game_id == 44){
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as l"), function($join) {
					$join->on('l.player_id', '=', 'p.player_id')
						->where('l.new_level', '=', 10);
				});
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as fl"), function($join) {
					$join->on('fl.player_id', '=', 'fp.player_id')
						->where('fl.new_level', '=', 10);
				});
				$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as al"), function($join) {
					$join->on('al.player_id', '=', 'ap.player_id')
						->where('al.new_level', '=', 10);
				});
			}
			if($game_id == 53){
				$query->leftJoin("{$db_qiqiwu}.created_10 as ct", function($join) {
					$join->on('p.uid', '=', 'ct.uid')
						->where('ct.is_ten', '=', 1);
				});
				$query->leftJoin("{$db_qiqiwu}.created_10 as ctf", function($join) {
					$join->on('fp.uid', '=', 'ctf.uid')
						->where('ctf.is_ten', '=', 1);
				});
				$query->leftJoin("{$db_qiqiwu}.created_10 as ctl", function($join) {
					$join->on('ap.uid', '=', 'ctl.uid')
						->where('ctl.is_ten', '=', 1);
				});
			}
		}

		$query->whereBetween('a.date', array($us_start_time, $us_end_time));

		if ($u1) {
			$query->where('a.u1', '=', $u1);
		}
		if ($u2) {
			$query->where('a.ad_name', '=', $u2);
		}
		if($game_id == 44){
			$sql_game_id = 'COUNT(DISTINCT(fl.player_id)) as count_formal_lev,
							COUNT(DISTINCT(al.player_id)) as count_anonymous_lev,
							COUNT(DISTINCT(l.player_id)) as total_lev';
		}
		if($game_id == 53){
			$sql_game_id = 'COUNT(DISTINCT(ctf.uid)) as count_formal_lev,
							COUNT(DISTINCT(ctl.uid)) as count_anonymous_lev,
							COUNT(DISTINCT(ct.uid)) as total_lev';
		}
		$query->selectRaw(
			'
			a.campaign,
			a.u1 as fb_u1,
			a.ad_name as fb_u2,
			MAX(click_through_rate) as click_through_rate,
			x.spent,
			COUNT(DISTINCT(fu.uid)) as count_formal_user,
			COUNT(DISTINCT(au.uid))as count_anonymous_user,
			COUNT(DISTINCT(u.uid)) as total_user,
			'.($game_id == 44 ? 
				'COUNT(DISTINCT(fp.player_id)) as count_formal_player,
				 COUNT(DISTINCT(ap.player_id)) as count_anonymous_player,
				 COUNT(DISTINCT(p.player_id)) as total_player'
				:
				'COUNT(fp.player_id) as count_formal_player,
				 COUNT(ap.player_id) as count_anonymous_player,
				 COUNT(p.player_id) as total_player'
				)
			.
			($server_internal_id > 0 ? ','.$sql_game_id : '')
		);
		
		$query->groupBy('a.u1', 'a.ad_name');
		return $query;
	}

	//台湾三国
	public function scopeGetTSFBStat($query, $db_qiqiwu, $game_id, $start_time, $end_time, $diff_hours, $u1, $u2, $server_internal_id, $db_server)
	{
		//输入的日期当成美国时间
		$us_start_time = date('Y-m-d H:i:s', $start_time);
		$us_end_time = date('Y-m-d H:i:s' , $end_time);
// 		$s = "#####输入的facebook开始时间是".$us_start_time."  输入的facebook结束时间是".$us_end_time."####\n";
// 		Log::info($s);
		
		$start_time = date('Y-m-d H:i:s', $start_time + (int)$diff_hours*3600);
		$end_time = date('Y-m-d H:i:s', $end_time + (int)$diff_hours*3600);
		
// 		$s = "#####转化后的开始时间是".$start_time."  转化后的结束时间是".$end_time."####\n";
// 		Log::info($s);
		$sub_sql = "(SELECT SUM(spent) as spent, u1, ad_name FROM ad_report WHERE date BETWEEN '{$us_start_time}' AND '{$us_end_time}' AND spent>0 GROUP BY u1, ad_name) as x";
// 		Log::info($sub_sql);
		$query->leftJoin(DB::raw($sub_sql), function($join){
			$join->on('x.u1', '=', 'a.u1')
				->on('x.ad_name', '=', 'a.ad_name');
		});
		$query->leftJoin("{$db_qiqiwu}.users as u", function($join) use ($start_time, $end_time) {
			$join->on('u.u', '=', 'a.u1')
				->on('u.u2', '=', 'a.ad_name')
				->where('u.created_time', '>=', $start_time)
				->where('u.created_time', '<=', $end_time)
				->where('u.source', '=', 'fb');
		});
		$query->leftJoin("{$db_qiqiwu}.users as fu", function($join) {
			$join->on('fu.uid', '=', 'u.uid')
				->where('fu.is_anonymous', '=', 0);
		});
		$query->leftJoin("{$db_qiqiwu}.users as au", function($join) {
			$join->on('au.uid', '=', 'u.uid')
				->where('au.is_anonymous', '=', 1)
				->where('au.still_anonymous', '=', 0);
				// ->where('au.nickname', 'NOT LIKE', '%@anonymous');	
		});
		
		$query->leftJoin("{$db_qiqiwu}.create_player as p", function($join) use ($server_internal_id) {
			$join->on('p.uid', '=', 'u.uid');
			if ($server_internal_id) {
				$join->where('p.server_id', '=', $server_internal_id);
			}
		});
		$query->leftJoin("{$db_qiqiwu}.create_player as fp", function($join) use ($server_internal_id) {
			$join->on('fp.uid', '=', 'fu.uid');
			if ($server_internal_id) {
				$join->where('fp.server_id', '=', $server_internal_id);
			}
		});
		$query->leftJoin("{$db_qiqiwu}.create_player as ap", function($join) use ($server_internal_id) {
			$join->on('ap.uid', '=', 'au.uid');
			if ($server_internal_id) {
				$join->where('ap.server_id', '=', $server_internal_id);
			}
		});

		if ($server_internal_id > 0) {
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as l"), function($join) {
				$join->on('l.player_id', '=', 'p.player_id')
					->where('l.new_level', '=', 10);
			});
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as fl"), function($join) {
				$join->on('fl.player_id', '=', 'fp.player_id')
					->where('fl.new_level', '=', 10);
			});
			$query->leftJoin(DB::raw("`{$db_server}`.log_levelup as al"), function($join) {
				$join->on('al.player_id', '=', 'ap.player_id')
					->where('al.new_level', '=', 10);
			});
		}

		$query->whereBetween('a.date', array($us_start_time, $us_end_time));

		if ($u1) {
			$query->where('a.u1', '=', $u1);
		}
		if ($u2) {
			$query->where('a.ad_name', '=', $u2);
		}

		$query->selectRaw(
			'
			a.campaign,
			a.u1 as fb_u1,
			a.ad_name as fb_u2,
			MAX(click_through_rate) as click_through_rate,
			x.spent,
			COUNT(DISTINCT(fu.uid)) as count_formal_user,
			COUNT(DISTINCT(au.uid))as count_anonymous_user,
			COUNT(DISTINCT(u.uid)) as total_user,
			COUNT(DISTINCT(fp.player_id)) as count_formal_player,
			COUNT(DISTINCT(ap.player_id)) as count_anonymous_player,
			COUNT(DISTINCT(p.player_id)) as total_player
			'.
			($server_internal_id > 0 ? ', 
			COUNT(DISTINCT(fl.player_id)) as count_formal_lev,
			COUNT(DISTINCT(al.player_id)) as count_anonymous_lev,
			COUNT(DISTINCT(l.player_id)) as total_lev
			' : '')
		);
		
		$query->groupBy('a.u1', 'a.ad_name');
		$query->orderBy('a.u1', 'a.ad_name');
		return $query;
	}

}