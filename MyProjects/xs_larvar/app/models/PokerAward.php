<?php
    class PokerAward extends Eloquent {

	protected $table = 'award_order as ao';

	protected $primaryKey = 'id';

	protected function getDateFormat()
	{
		return 'U';
	}
	public function scopeGetUserAward($query, $type1, $type2, $type3, $start_time, $end_time, $player_name, $uid)
	{
		$query->leftJoin('user_address as ua', 'ao.uid', '=', 'ua.uid');
		if ($type1) {
			$query->where('award_name', '=', $type1);
		}
		if ($type2) {
			$query->where('status', '=', $type2);
		}
		if (!$type3) {
			$query->where('get_time', '>=', $start_time)
				->where('end_time', '<=', $end_time);
		}
		if ($player_name) {
			$query->where('ua.name', '=', $player_name);
		}
		if ($uid) {
			$query->where('ao.uid', '=', $uid);
		}
		$query->selectRaw('ao.uid,  ao.award_name,  ao.award_amount, ao.get_time, ao.status, ao.goods_id, ao.address_id, ao.domain_name, ua.contact_email, ua.address, ua.mobile, ua.name');
		return $query;
	}
}