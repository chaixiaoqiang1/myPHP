<?php

class RecordOrders extends Eloquent {

    protected $table = 'record_orders as ro';

    protected $primaryKey = 'id';

    protected function getDateFormat()
    {
        return 'U';
    }

    public function scopegetRecordOrders($query, $by_time, $already_deal, $start_time, $end_time, $is_gm, $order_msg, $game_id, $type){
    	$query->leftJoin('games as g', function($join){
    		$join->on('ro.game_id', '=', 'g.game_id');
    	});

    	if($by_time){
    		$query->whereBetween('ro.created_time', array($start_time, $end_time));
    	}
    	if($already_deal){
    		if(1 == $already_deal){
    			$query->where('deal_time', '>', '0')->where('is_done', '0');
    		}
            if(2 == $already_deal){
                $query->where('is_done', '1');
            }
    	}else{
    		$query->where('deal_time', 0);
    	}

    	if($order_msg['order_id']){
    		$query->where('ro.order_id', $order_msg['order_id']);
    	}elseif($order_msg['order_sn']){
    		$query->where('ro.order_sn', $order_msg['order_sn']);
    	}elseif($order_msg['tradeseq']){
    		$query->where('ro.tradeseq', $order_msg['tradeseq']);
    	}elseif($order_msg['pay_user_id']){
    		$query->where('ro.pay_user_id', $order_msg['pay_user_id']);
    	}elseif($order_msg['player_id']){
            $query->where('ro.player_id', $order_msg['player_id']);
        }elseif($order_msg['player_name']){
            $query->where('ro.player_name', $order_msg['player_name']);
        }

    	if($is_gm || ('fail' != $type)){
    		$query->where('ro.game_id', $game_id);
    	}

        $query->where('type', $type);

    	$query->selectRaw("ro.id, ro.order_id, ro.order_sn, ro.pay_user_id, g.game_name as game_name, ro.game_id, ro.tradeseq, ro.pay_amount, from_unixtime(ro.created_time) as created_time, ro.created_operator,
    		ro.reason, ro.last_operator, ro.deal_time, ro.result, ro.pay_type_name, ro.method_name, ro.player_id, ro.player_name, ro.server_name, ro.currency_code, ro.is_done, ro.order_created_time");
        $query->orderby('ro.created_time', 'desc');
        
    	return $query;
    }

}