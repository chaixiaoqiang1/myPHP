<?php
    class UserRetention extends  Eloquent
    {
    	protected $table = 'user_retention';

		protected $primaryKey = 'log_id';
	
		protected function getDateFormat()
		{
			return 'U';
		}

		public function scopeChannelRetention($query, $server_internal_id, $os_type, $source, $u1, $u2, $filter, $reg_start_time, $reg_end_time, $is_anonymous, $game_id, $platform_id)
		{
			$this->retentionBasic($query, $server_internal_id, $source, $u1, $u2, $is_anonymous, $reg_start_time, $reg_end_time, $game_id, $server_internal_id, $os_type);
			
			if ($filter == 'source') {
				$filter_sql = 'source';
				$query->groupBy('source');
			} else if ($filter == 'u1') {
				$filter_sql = 'source, u1';
				$query->groupBy('u1', 'source');
			} else if ($filter == 'u2') {
				$filter_sql = 'source, u1, u2';
				$query->groupBy('u2', 'u1', 'source');
			} else{
				$filter_sql = '';
			}

			if(in_array($os_type, array('iOS', 'android'))){
				if($filter_sql){
					$filter_sql = 'os_type, '. $filter_sql;
				}else{
					$filter_sql = 'os_type';
				}
			}else{
				if($filter_sql){
					$filter_sql = "'All' as os_type, ". $filter_sql;
				}else{
					$filter_sql = "'All' as os_type";
				}
			}

			$select_sql = "{$filter_sql},sum(created_player_number) as create_count,";
			foreach (array(2,3,4,5,6,7,14) as $value) {
				$select_sql .= "sum(days_$value) as days_$value,";
			}
			$select_sql = substr($select_sql, 0, -1);

			$query->selectRaw($select_sql);

			return $query;
		}

		private function retentionBasic($query, $db_qiqiwu, $source, $u1, $u2, $is_anonymous, $reg_start_time, $reg_end_time, $game_id, $server_internal_id, $os_type){
			if ($source || $source === '0') {
				$query->where('source', $source);
			}	
			if ($u1 || $source === '0') {
				$query->where('u1', $u1);
			}
			if ($u2 || $source === '0') {
				$query->where('u2', $u2);
			}
			if(in_array($os_type, array('android', 'iOS'))){
				$query->where('os_type', $os_type);
			}

			$query->where('game_id', $game_id);

			$query->where('server_internal_id', $server_internal_id);
			
			$query->whereBetween('retention_time', array($reg_start_time, $reg_end_time));
			
			if ($is_anonymous != null) {
				$query->where('is_anonymous', $is_anonymous);
			}
		}

    }
?>