<?php
class SlaveRefundOrder extends Eloquent {

    protected $table = 'refund_order as ro';

    protected $primaryKey = 'id';

    protected function getDateFormat()
    {
        return 'U';
    }

    public function scopeGetRefund($query, $start_time, $end_time, $game_id, $server_id)
    {
    	$query->leftJoin("pay_order as o", "o.order_sn", "=", "ro.order_sn");
   		/*$query->selectRaw('
   			sum(ro.refund_amount*o.exchange) as refund_amount,
   			FROM_UNIXTIME(ro.time_updated, "%Y-%m-%d") as refund_date
   			');*///这是正确的统计方式。但由于facebook退款订单不带汇率，因此使用支付订单的美金作为退款订单的美金。
        $query->selectRaw('
   			sum(o.pay_amount*o.exchange) as refund_amount,
   			FROM_UNIXTIME(ro.time_updated, "%Y-%m-%d") as refund_date
   			');
    	$query->where("o.game_id", "=", $game_id);
    	if($server_id!=-1){
    		$query->where("o.server_id", "=", $server_id);
    	}
    	$query->whereBetween('time_updated', array($start_time, $end_time));
    	$query->groupBy('refund_date')
    			->orderBy('refund_date', 'DESC');	
    	return $query;
    }

	public function scopeRefundOrders($query, $db_qiqiwu, $order_sn, $start_time, $end_time, $pay_type_id)
	{
		$query->leftJoin("pay_order as o", 'o.order_sn', '=', 'ro.order_sn');
		$query->leftJoin("{$db_qiqiwu}.users as u ", 'u.uid', '=', 'o.pay_user_id');
		$query->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id');
		$query->leftJoin("{$db_qiqiwu}.create_player as cp", function($join) {
			$join->on('cp.uid', '=', 'u.uid')
				->on('cp.server_id', '=', 'sl.server_internal_id');
		});
		$query->selectRaw('
			ro.pay_type_id,
			ro.order_sn,
			o.tradeseq,
			o.pay_amount,
			(o.pay_amount * o.exchange) as pay_amount_dollar,
			ro.refund_amount,
			(ro.refund_amount * o.exchange) as refund_amount_dollar,
			ro.currency as currency_code,
			o.pay_time,
			ro.time_updated,
			o.get_payment,
			o.order_status,
			sl.server_name,
			cp.player_name,
			u.nickname,
			cp.player_id
		');
		if ($order_sn) {
			$query->where('ro.order_sn', $order_sn);
		}
		if ($start_time && $end_time) {
			$query->whereBetween('ro.time_updated', array(
				$start_time, $end_time
			));
		}
		if ($pay_type_id) {
			$query->where('ro.pay_type_id', $pay_type_id);
		}
		return $query;	
	}

}