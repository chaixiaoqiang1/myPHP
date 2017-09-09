<?php
class SlaveDisputeOrder extends Eloquent {

    protected $table = 'dispute_order as d';

    protected $primaryKey = 'id';

    protected function getDateFormat()
    {
        return 'U';
    }

	public function scopeGetOrders($query, $db_qiqiwu, $order_sn, $start_time, $end_time, $fb_name, $fb_id, $status)
	{
		$query->leftJoin('pay_order as o', 'o.order_sn', '=', 'd.order_sn')
			->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
			->leftJoin("{$db_qiqiwu}.create_player as cp", function($join){
				$join->on('cp.uid', '=', 'o.pay_user_id')
					->on('cp.server_id', '=', 'sl.server_internal_id');
			});

		if ($order_sn) {
			$query->where('d.order_sn', $order_sn);
		}
		if ($start_time && $end_time) {
			$query->whereBetween('d.pay_time', array($start_time, $end_time));
		}
		if ($fb_name) {
			$query->where('d.user_name', $fb_name);
		}
		if ($fb_id) {
			$query->where('d.user_fb_id', $fb_id);
		}
		if ($status != null) {
			$query->where('d.status', $status);
		}
		
		$query->selectRaw('sl.server_name, cp.player_name, d.user_name, d.execute_refund,
			d.user_fb_id, d.user_email, d.order_sn, d.refund_amount, 
			(d.refund_amount * o.exchange) as refund_amount_dollar,
			d.currency as currency_code, d.user_comment, d.create_time, d.status,
			d.execute_time, o.tradeseq, o.pay_amount, 
			(o.pay_amount * o.exchange) as pay_amount_dollar, d.id as dispute_id
		');

		return $query;
	}
}