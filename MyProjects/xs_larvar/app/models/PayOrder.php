<?php

/*
 * 支付订单数据，查看payment系列数据。
 *
 */

use \Log;

class PayOrder extends Eloquent
{

    protected $table = 'pay_order as o';

    protected $primaryKey = 'order_id';

    protected function getDateFormat()
    {
        return 'U';
    }

    public function scopeServerAllOrderStat($query, $platform_server_id, $currency_id, $start_time, $end_time, $game_id)
    {
        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        return $query->selectRaw("
            ".((70 == $game_id) ? "SUM(pay_amount)" : "SUM(pay_amount * exchange /({$sql_currency}))")." as total_amount,
                SUM(pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "exchange").") as total_dollar_amount,
            SUM(yuanbao_amount) as total_yuanbao_amount,
            COUNT(order_id) as total_count,
            COUNT(DISTINCT(pay_user_id)) as total_user_count")
            ->where('game_id', $game_id)
            ->where('server_id', $platform_server_id)
            ->where('get_payment', 1)
            ->whereBetween('pay_time', array($start_time, $end_time));
        if ($game_id !== 11) {
            $query->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('refund_order')
                    ->whereRaw('refund_order.order_sn = o.order_sn');
            });
        }
    }

    public function scopeServerAllOrderStatOldUser($query, $db_qiqiwu, $platform_server_id, $open_server_time, $currency_id, $start_time, $end_time, $game_id)
    {
        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        return $query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
            ->selectRaw("
            ".((70 == $game_id) ? "SUM(pay_amount)" : "SUM(pay_amount * exchange /({$sql_currency}))")." as total_amount,
                SUM(pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "exchange").") as total_dollar_amount,
            SUM(yuanbao_amount) as total_yuanbao_amount,
            COUNT(order_id) as total_count,
            COUNT(DISTINCT(pay_user_id)) as total_user_count")
            ->where('o.game_id', $game_id)
            ->where('server_id', $platform_server_id)
            ->where('get_payment', 1)
            ->whereBetween('pay_time', array($start_time, $end_time))
            ->whereRaw("u.created_time <= FROM_UNIXTIME($open_server_time)");
        if ($game_id !== 11) {
            $query->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('refund_order')
                    ->whereRaw('refund_order.order_sn = o.order_sn');
            });
        }
    }


    public function scopeServerOrderStatistics($query, $platform_server_id, $currency_id, $start_time, $end_time, $game_id)
    {
        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        return $query->selectRaw("
            ".((70 == $game_id) ? "SUM(pay_amount)" : "SUM(pay_amount * exchange /({$sql_currency}))")." as total_amount,
                SUM(pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "exchange").") as total_dollar_amount,
            SUM(yuanbao_amount) as total_yuanbao_amount,
            COUNT(order_id) as total_count,
            COUNT(DISTINCT(pay_user_id)) as total_user_count,
            FROM_UNIXTIME(pay_time, '%Y-%m-%d') as date")
            ->where('game_id', $game_id)
            ->where('server_id', $platform_server_id)
            ->where('get_payment', 1)
            ->whereBetween('pay_time', array($start_time, $end_time))
            ->groupBy('date')
            ->orderBy('date', 'DESC');
        if ($game_id !== 11) {
            $query->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('refund_order')
                    ->whereRaw('refund_order.order_sn = o.order_sn');
            });
        }
    }

    public function scopeServerOrderStatisticsOldUser($query, $db_qiqiwu, $platform_server_id, $open_server_time, $currency_id, $start_time, $end_time, $game_id)
    {
        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        return $query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
            ->selectRaw("
            ".((70 == $game_id) ? "SUM(pay_amount)" : "SUM(pay_amount * exchange /({$sql_currency}))")." as total_amount,
                SUM(pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "exchange").") as total_dollar_amount,
            SUM(yuanbao_amount) as total_yuanbao_amount,
            COUNT(order_id) as total_count,
            COUNT(DISTINCT(pay_user_id)) as total_user_count,
            FROM_UNIXTIME(pay_time, '%Y-%m-%d') as date")
            ->where('o.game_id', $game_id)
            ->where('server_id', $platform_server_id)
            ->where('get_payment', 1)
            ->whereBetween('pay_time', array($start_time, $end_time))
            ->whereRaw("UNIX_TIMESTAMP(u.created_time) <= $open_server_time")
            ->groupBy('date')
            ->orderBy('date', 'DESC');
        if ($game_id !== 11) {
            $query->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('refund_order')
                    ->whereRaw('refund_order.order_sn = o.order_sn');
            });
        }
    }

    public function scopeGameOrderStatistics($query, $game_id, $currency_id, $start_time, $end_time, $devide_servers='0')
    {
        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        if('1' == $devide_servers){
            return $query->selectRaw("
            server_id,
            ".((70 == $game_id) ? "SUM(pay_amount)" : "SUM(pay_amount * exchange /({$sql_currency}))")." as total_amount,
            SUM(pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "exchange").") as total_dollar_amount,
            SUM(yuanbao_amount) as total_yuanbao_amount,
            COUNT(order_id) as total_count,
            COUNT(DISTINCT(pay_user_id)) as total_user_count,
            FROM_UNIXTIME(pay_time, '%Y-%m-%d') as date")
            ->where('game_id', $game_id)
            ->where('get_payment', 1)
            ->whereBetween('pay_time', array($start_time, $end_time))
            ->groupBy('date', 'server_id')
            ->orderBy('date', 'DESC')
            ->orderBy('server_id');
            if ($game_id !== 11) {
                $query->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('refund_order')
                        ->whereRaw('refund_order.order_sn = o.order_sn');
                });
            }
        }elseif('2' == $devide_servers){
            return $query->selectRaw("
            server_id,
            ".((70 == $game_id) ? "SUM(pay_amount)" : "SUM(pay_amount * exchange /({$sql_currency}))")." as total_amount,
            SUM(pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "exchange").") as total_dollar_amount,
            SUM(yuanbao_amount) as total_yuanbao_amount,
            COUNT(order_id) as total_count,
            COUNT(DISTINCT(pay_user_id)) as total_user_count,
            server_id as date")
            ->where('game_id', $game_id)
            ->where('get_payment', 1)
            ->whereBetween('pay_time', array($start_time, $end_time))
            ->groupBy('date')
            ->orderBy('server_id');
            if ($game_id !== 11) {
                $query->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('refund_order')
                        ->whereRaw('refund_order.order_sn = o.order_sn');
                });
            }
        }else{
            if(70 == $game_id){
                Log::info('Panda-----test------time--------arrive-----payorder---'.$start_time.'--'.$end_time);
            }
            return $query->selectRaw("
                ".((70 == $game_id) ? "SUM(pay_amount)" : "SUM(pay_amount * exchange /({$sql_currency}))")." as total_amount,
                SUM(pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "exchange").") as total_dollar_amount,
                SUM(yuanbao_amount) as total_yuanbao_amount,
                COUNT(order_id) as total_count,
                COUNT(DISTINCT(pay_user_id)) as total_user_count,
                FROM_UNIXTIME(pay_time, '%Y-%m-%d') as date")
                ->where('game_id', $game_id)
                ->where('get_payment', 1)
                ->whereBetween('pay_time', array($start_time, $end_time))
                ->groupBy('date')
                ->orderBy('date', 'DESC');
            if ($game_id !== 11) {
                $query->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('refund_order')
                        ->whereRaw('refund_order.order_sn = o.order_sn');
                });
            }
        }
    }

    public function scopeGameAllOrderStat($query, $game_id, $currency_id, $start_time, $end_time)
    {
        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        return $query->selectRaw("
            ".((70 == $game_id) ? "SUM(pay_amount)" : "SUM(pay_amount * exchange /({$sql_currency}))")." as total_amount,
            SUM(pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "exchange").") as total_dollar_amount,
            SUM(yuanbao_amount) as total_yuanbao_amount,
            COUNT(order_id) as total_count,
            COUNT(DISTINCT(pay_user_id)) as total_user_count")
            ->where('game_id', $game_id)
            ->where('get_payment', 1)
            ->whereBetween('pay_time', array($start_time, $end_time));
        if ($game_id !== 11) {
            $query->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('refund_order')
                    ->whereRaw('refund_order.order_sn = o.order_sn');
            });
        }
    }

    public function scopeGetLuckyOrderSN($query, $db_qiqiwu, $lucky_number, $start_time, $end_time)
    {
        $query->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
            ->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
            ->leftJoin("{$db_qiqiwu}.create_player as p", function ($join) {
                $join->on('p.uid', '=', 'u.uid')
                    ->on('p.server_id', '=', 'sl.server_internal_id');
            })
            ->leftJoin("bank_order as bo", 'bo.order_id', '=', 'o.order_id')
            ->selectRaw("order_sn, pay_type_id, method_id, pay_amount, o.currency as currency_id, o.exchange, (pay_amount * exchange) as dollar_amount, yuanbao_amount, create_time, o.pay_time, sl.server_name, o.pay_user_id, p.player_name, p.player_id, o.money_flow_name");
        $query->where('order_sn', 'like', "%{$lucky_number}");
        $query->where('get_payment', '=', '1');
        $query->whereBetween('o.create_time', array(
            $start_time, $end_time
        ));
        $query->orderBy('o.create_time');
        return $query;
    }

    public function scopeOrderByOrderSN($query, $db_qiqiwu, $order_sn, $game_id ,$db_name = '')
    {
        $this->basicQuery($query, $db_qiqiwu, $game_id, $db_name);
        return $query->where('order_sn', $order_sn)->where('o.game_id', $game_id);
    }

    public function scopeOrderByOrderID($query, $db_qiqiwu, $order_id, $game_id ,$db_name = ''){
        $this->basicQuery($query, $db_qiqiwu, $game_id, $db_name);
        return $query->where('o.order_id', $order_id)->where('o.game_id', $game_id);
    }

    public function scopeOrderByTradeseq($query, $db_qiqiwu, $tradeseq, $game_id, $db_name = '')
    {
        $this->basicQuery($query, $db_qiqiwu, $game_id, $db_name);
        return $query->where('tradeseq', $tradeseq)->where('o.game_id', $game_id);
    }

    public function scopeOrders($query, $db_qiqiwu, $platform_server_id, $pay_type_id, $method_id, $get_payment, $low_amount, $high_amount, $low_gold, $high_gold, $start_time, $end_time, $game_id, $offer_yuanbao, $statistics_time , $db_name = '')
    {
        $this->basicQuery($query, $db_qiqiwu, $game_id, $db_name);
        if ($start_time && $end_time) {
            if (0 == $statistics_time) {
                $query->whereBetween('o.create_time', array($start_time, $end_time));
                Log::info("create_time !!  statistics_time:".var_export($statistics_time, true));
            } elseif (1 == $statistics_time) {
                $query->whereBetween('o.pay_time', array($start_time, $end_time));
                Log::info("pay_time  !! statistics_time:" . var_export($statistics_time, true));
            }
        }

        if ($platform_server_id) {
            $query->where('o.server_id', $platform_server_id);
        }

        if ($game_id) {
            $query->where('o.game_id', $game_id);
        }
        if ($pay_type_id) {
            $query->where('o.pay_type_id', $pay_type_id);
        }
        if ($method_id != 999) {
            $query->where('o.method_id', $method_id);
        }
        if ($get_payment != null) {
            $query->where('get_payment', $get_payment);
        }
        if ('0' != $low_amount) {
            $query->where('pay_amount', '>=', $low_amount);
        }
        if ('0' != $high_amount) {
            $query->where('pay_amount', '<=', $high_amount);
        }
        if ('0' != $low_gold) {
            $query->where('yuanbao_amount', '>=', $low_gold);
        }
        if ('0' != $high_gold) {
            $query->where('yuanbao_amount', '<=', $high_gold);
        }
        if ($offer_yuanbao != null) {
            $query->where('offer_yuanbao', $offer_yuanbao);
        }
        $query->orderBy('o.order_id', 'DESC');
        return $query;
    }

    /*
     *由于夜夜三国没有create_player表，所以改为了查询log_create_player表，
     *所以调用它的方法都传了db_name，夜夜三国没有的功能可以不传
     */
    private
    function basicQuery($query, $db_qiqiwu, $game_id, $db_name = '', $limit_order = 0)
    {
        // 此处根据game_id确定对应的元宝单价（美元计）
        switch ($game_id) {
            case 54:    //台湾夜夜三国
            case 47:    //台湾大乱斗
            case 1:     //台湾风流三国
            case 8:     //台湾女神之剑
            case 59:    //59-63为风流三国世界服
            case 60:
            case 61:
            case 62:
            case 63:
                $yuanbao_price = 0.019207683;
                break;
            case 44:    //土耳其女神
            case 53:    //土耳其火影
                $yuanbao_price = 0.015357143;
                break;
            case 5:     //腾讯风流三国
                $yuanbao_price = 0.013217391;
                break;
            case 43:     //巴西女神
                $yuanbao_price = 0.012684;
                break;
            case 51:     //印尼君王2
                $yuanbao_price = 0.008835294;
                break;
            case 30:     //英文风流三国
                $yuanbao_price = 0.008333333;
                break;
            case 55:     //印尼忍者之王
                $yuanbao_price = 0.006944444;
                break;
            case 3:     //泰国风流三国
                $yuanbao_price = 0.006687215;
                break;
            case 41:     //泰国女神之剑
                $yuanbao_price = 0.005919192;
                break;
            case 2:     //越南风流三国
                $yuanbao_price = 0.005;
                break;
            case 4:     //印尼风流三国
                $yuanbao_price = 0.004714286;
                break;
            case 64:     //印尼大闹天宫
                $yuanbao_price = 0.005976096;
                break;
            case 48:     //越南大乱斗
                $yuanbao_price = 0.0046;
                break;
            case 38:    //印尼神仙道
            case 50:     //印尼大乱斗
                $yuanbao_price = 0.004550303;
                break;
            case 36:     //越南女神
            case 45:     //印尼女神
                $yuanbao_price = 0.004166667;
                break;
            case 58:     //印尼宝贝联盟
                $yuanbao_price = 0.00625;
                break;
            case 65:     //英雄战魂
                $yuanbao_price = 0.00088888;
                break;
            case 67:    //越南宝贝联盟
                $yuanbao_price = 0.009342;
                break;
            case 11:
            case 52:
            case 57:
            case 68:
                $yuanbao_price = 0.083333333;
                $chouma_price = 0.00001;
                break;
            case 70:    //俄罗斯女神
                $yuanbao_price = 0.00598;
                break;
            default:
                $yuanbao_price = 0.019207683;
                break;
        }

        if (in_array($game_id, Config::get('game_config.yysggameids'))) { //夜夜三国礼包 & create_player改为日志库
            return $query->leftJoin("giftbag_list as gl", function ($join) use ($game_id) {
                $join->on('gl.game_id', '=', 'o.game_id')
                    ->on('o.giftbag_id', '=', 'gl.giftbag_id');
            })
                ->leftJoin("gift_price_list as gpl", function ($join) {
                    $join->on('gl.price', '=', 'gpl.price_amount')
                        ->on('gl.currency', '=', 'gpl.price_currency_id')
                        ->on('o.pay_type_id', '=', 'gpl.pay_type_id')
                        ->on('o.method_id', '=', 'gpl.method_id')
                        ->on('o.currency', '=', 'gpl.currency');
                })
                ->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
                ->leftJoin("{$db_qiqiwu}.create_player as p", function ($join) {
                    $join->on('p.uid', '=', 'u.uid')
                        ->on('p.server_id', '=', 'sl.server_internal_id')
                        ->on('p.game_id', '=', 'sl.game_id');
                })
                ->leftJoin("bank_order as bo", 'bo.order_id', '=', 'o.order_id')
                ->selectRaw(((in_array($game_id, array(74,75))) ? "o.combined_order, " : "")."order_sn, o.sdk_id, tradeseq, o.pay_type_id as pay_type_id, o.method_id as method_id, zone,".($limit_order ? "max(yuanbao_amount) as yuanbao_amount" : "yuanbao_amount").",o.currency as currency_id, o.mycard_activity_code,
            o.exchange, (pay_amount * exchange) as dollar_amount, o.pay_amount, offer_yuanbao, create_time, o.pay_time, o.get_payment, o.giftbag_id, gl.giftbag_name as giftbag_name,
            o.order_status, o.goods_type, sl.server_name, sl.server_id, sl.server_internal_id, o.pay_user_id, u.nickname, u.login_email, p.player_name,
            p.player_id, o.basic_yuanbao_amount, o.extra_yuanbao_amount, o.huodong_yuanbao_amount, o.order_id, o.money_flow_name,
            bo.name as bank_user_name, bo.bank_name, bo.bank_account, bo.pay_time as bank_pay_time,
            (o.exchange*ifnull(gpl.amount, 0)+ifnull(o.yuanbao_amount, 0)*{$yuanbao_price}) as goods_value");
        }elseif('58' == $game_id || '67' == $game_id || '86' == $game_id || in_array($game_id, Config::get('game_config.mnsggameids'))){//宝贝联盟,萌娘三国
            return $query->leftJoin("giftbag_list as gl", function ($join) use ($game_id) {
                $join->on('gl.game_id', '=', 'o.game_id')
                    ->on('o.giftbag_id', '=', 'gl.giftbag_id');
            })
                ->leftJoin("gift_price_list as gpl", function ($join) {
                    $join->on('gl.price', '=', 'gpl.price_amount')
                        ->on('gl.currency', '=', 'gpl.price_currency_id')
                        ->on('o.pay_type_id', '=', 'gpl.pay_type_id')
                        ->on('o.method_id', '=', 'gpl.method_id')
                        ->on('o.currency', '=', 'gpl.currency');
                })
                ->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
                ->leftJoin("{$db_qiqiwu}.create_player as p", function ($join) {
                    $join->on('p.uid', '=', 'u.uid')
                        ->on('p.server_id', '=', 'sl.server_internal_id')
                        ->on('p.game_id', '=', 'sl.game_id');
                })
                ->leftJoin("bank_order as bo", 'bo.order_id', '=', 'o.order_id')
                ->selectRaw("order_sn, o.sdk_id, tradeseq, o.pay_type_id as pay_type_id, o.method_id as method_id, zone,".($limit_order ? "max(yuanbao_amount) as yuanbao_amount" : "yuanbao_amount").",o.currency as currency_id, o.mycard_activity_code,
            o.exchange, (pay_amount * exchange) as dollar_amount, o.pay_amount, offer_yuanbao, create_time, o.pay_time, o.get_payment, o.giftbag_id, gl.giftbag_name as giftbag_name,
            o.order_status, o.goods_type, sl.server_name, sl.server_id, sl.server_internal_id, o.pay_user_id, u.nickname, u.login_email, p.player_name,
            p.player_id, o.basic_yuanbao_amount, o.extra_yuanbao_amount, o.huodong_yuanbao_amount, o.order_id, o.money_flow_name,
            bo.name as bank_user_name, bo.bank_name, bo.bank_account, bo.pay_time as bank_pay_time,
            (o.exchange*ifnull(gpl.amount, 0)+ifnull(o.yuanbao_amount, 0)*{$yuanbao_price}) as goods_value");
        }elseif('11' == $game_id || '52' == $game_id || '57' == $game_id || '68' == $game_id) { //各国德扑
            return $query->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
                ->leftJoin("{$db_qiqiwu}.create_player as p", function ($join) {
                    $join->on('p.uid', '=', 'u.uid')
                        ->on('p.server_id', '=', 'sl.server_internal_id');
                })
                ->leftJoin("bank_order as bo", 'bo.order_id', '=', 'o.order_id')
                ->selectRaw("order_sn, o.sdk_id, tradeseq, pay_type_id, method_id, zone,".($limit_order ? "max(yuanbao_amount) as yuanbao_amount" : "yuanbao_amount").",o.currency as currency_id, o.mycard_activity_code,
            o.exchange, (pay_amount * exchange) as dollar_amount, o.pay_amount, offer_yuanbao, create_time, o.pay_time, o.get_payment,
            o.order_status, o.goods_type, sl.server_name, sl.server_id, sl.server_internal_id, o.pay_user_id, u.nickname, u.login_email, p.player_name,
            p.player_id, o.basic_yuanbao_amount, o.extra_yuanbao_amount, o.huodong_yuanbao_amount, o.order_id, o.money_flow_name,
            bo.name as bank_user_name, bo.bank_name, bo.bank_account, bo.pay_time as bank_pay_time,
            case
            when goods_type='2' then (ifnull(o.yuanbao_amount, 0)*{$yuanbao_price})
            when goods_type='1' then (ifnull(o.yuanbao_amount, 0)*{$chouma_price}) 
            when goods_type='4' then (0.99) 
            when goods_type='5' then (4.99) 
            when goods_type='6' then (14.99) 
            end as goods_value");
                //德扑用goods_type区分了几种货币以及礼包(4,5,6)，并且没有维护一个存有礼包信息的表，因此暂时把德扑的价格信息写进代码里
        }elseif('65' == $game_id){ //战魂的player_id从pay_order直接取
            return $query->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
                ->leftJoin("{$db_qiqiwu}.create_player as p", function ($join) {
                    $join->on('p.uid', '=', 'u.uid')
                        ->on('p.player_id','=','o.player_id')
                        ->on('p.server_id', '=', 'sl.server_internal_id');
                })
                ->leftJoin("bank_order as bo", 'bo.order_id', '=', 'o.order_id')
                ->selectRaw("order_sn, o.sdk_id, tradeseq, pay_type_id, method_id, zone, ".($limit_order ? "max(yuanbao_amount) as yuanbao_amount" : "yuanbao_amount").",o.currency as currency_id, o.mycard_activity_code,
            o.exchange, (pay_amount * exchange) as dollar_amount, o.pay_amount, offer_yuanbao, create_time, o.pay_time, o.get_payment,
            o.order_status, o.goods_type, sl.server_name, sl.server_id, sl.server_internal_id, o.pay_user_id, u.nickname, u.login_email, p.player_name,
            o.player_id, o.basic_yuanbao_amount, o.extra_yuanbao_amount, o.huodong_yuanbao_amount, o.order_id, o.money_flow_name,
            bo.name as bank_user_name, bo.bank_name, bo.bank_account, bo.pay_time as bank_pay_time,
            (ifnull(o.yuanbao_amount, 0)*{$yuanbao_price}) as goods_value");
        }elseif('71' == $game_id){  //世界online的订单实际价值计算比较特殊
            return $query->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
                ->leftJoin("{$db_qiqiwu}.create_player as p", function ($join) {
                    $join->on('p.uid', '=', 'u.uid')
                        ->on('p.player_id','=','o.player_id')
                        ->on('p.server_id', '=', 'sl.server_internal_id');
                })
                ->leftJoin(DB::raw("(select payment_id,pay_type_id,method_id from mobile_payment_method group by pay_type_id,method_id) as mpm"), function ($join) {
                    $join->on('mpm.pay_type_id', '=', 'o.pay_type_id')
                        ->on('mpm.method_id', '=', 'o.method_id');
                })
                ->leftJoin("game_product_price as gpp", function ($join) use ($game_id) {
                    $join->on('o.product_id', '=', 'gpp.product_id')
                        ->on('mpm.payment_id', '=', 'gpp.payment_id')
                        ->where('gpp.game_id', '=', $game_id);
                })
                ->leftJoin("game_product as gp", function ($join) {
                    $join->on('o.product_id', '=', 'gp.product_id');
                })
                ->leftJoin(DB::raw("(select exchange,type from (select exchange,type from exchange as ex1 order by timeline desc) ex2 group by type) as excu"), 'excu.type', '=', 'gpp.currency_id')
                ->leftJoin("bank_order as bo", 'bo.order_id', '=', 'o.order_id')
                ->selectRaw("order_sn, o.sdk_id, tradeseq, o.pay_type_id, o.method_id, zone, ".($limit_order ? "max(o.yuanbao_amount) as yuanbao_amount" : "o.yuanbao_amount").",o.yuanbao_amount, o.currency as currency_id, o.mycard_activity_code,
            o.exchange, (o.pay_amount * o.exchange) as dollar_amount, gp.product_name, o.pay_amount, offer_yuanbao, create_time, o.pay_time, o.get_payment,
            o.order_status, o.goods_type, sl.server_name, sl.server_id, sl.server_internal_id, o.pay_user_id, u.nickname, u.login_email, p.player_name,
            o.player_id, o.basic_yuanbao_amount, o.extra_yuanbao_amount, o.huodong_yuanbao_amount, o.order_id, o.money_flow_name,
            bo.name as bank_user_name, bo.bank_name, bo.bank_account, bo.pay_time as bank_pay_time,
            (ifnull(gpp.pay_amount, 0)*excu.exchange) as goods_value");
        }elseif('87' == $game_id){
            return $query->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
                ->leftJoin("{$db_qiqiwu}.create_player as p", function ($join) {
                    $join->on('p.uid', '=', 'u.uid')
                        ->on('p.server_id', '=', 'sl.server_internal_id')
                        ->on('p.game_id', '=', 'sl.game_id');
                })
                ->leftJoin("bank_order as bo", 'bo.order_id', '=', 'o.order_id')
                ->selectRaw("order_sn, tradeseq, pay_type_id, method_id, zone,".($limit_order ? "max(o.yuanbao_amount) as yuanbao_amount" : "o.yuanbao_amount").",o.currency as currency_id, o.mycard_activity_code,
            o.exchange, (pay_amount * exchange) as dollar_amount, o.pay_amount, offer_yuanbao, create_time, o.pay_time, o.get_payment,
            o.order_status, o.goods_type, sl.server_name, sl.server_id, sl.server_internal_id, o.pay_user_id, u.nickname, u.login_email, p.player_name,
            p.player_id, o.basic_yuanbao_amount, o.extra_yuanbao_amount, o.huodong_yuanbao_amount, o.order_id, o.money_flow_name,
            bo.name as bank_user_name, bo.bank_name, bo.bank_account, bo.pay_time as bank_pay_time,
            (ifnull(o.yuanbao_amount, 0)*{$yuanbao_price}) as goods_value");
        }else{
            return $query->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
                ->leftJoin("{$db_qiqiwu}.create_player as p", function ($join) {
                    $join->on('p.uid', '=', 'u.uid')
                        ->on('p.server_id', '=', 'sl.server_internal_id');
                })
                ->leftJoin("bank_order as bo", 'bo.order_id', '=', 'o.order_id')
                ->selectRaw("order_sn, tradeseq, pay_type_id, method_id, zone,".($limit_order ? "max(o.yuanbao_amount) as yuanbao_amount" : "o.yuanbao_amount").",o.currency as currency_id, o.mycard_activity_code,
            o.exchange, (pay_amount * exchange) as dollar_amount, o.pay_amount, offer_yuanbao, create_time, o.pay_time, o.get_payment,
            o.order_status, o.goods_type, sl.server_name, sl.server_id, sl.server_internal_id, o.pay_user_id, u.nickname, u.login_email, p.player_name,
            p.player_id, o.basic_yuanbao_amount, o.extra_yuanbao_amount, o.huodong_yuanbao_amount, o.order_id, o.money_flow_name,
            bo.name as bank_user_name, bo.bank_name, bo.bank_account, bo.pay_time as bank_pay_time,
            (ifnull(o.yuanbao_amount, 0)*{$yuanbao_price}) as goods_value");
        }
    }

    public
    function scopeOrderByUser($query, $db_qiqiwu, $uid, $player_name, $player_id, $start_time, $end_time, $bank_account, $game_id ,$db_name = '', $get_payment, $offer_yuanbao, $platform_server_id, $limit_order)
    {
        $this->basicQuery($query, $db_qiqiwu, $game_id, $db_name, $limit_order);
        if ($uid) {
            $query->where('u.uid', $uid);
        }
        if($player_id){
            $query->where('p.player_id', $player_id);
        }
        if ($player_name) {
            $query->where('p.player_name', $player_name);
        }
        if ($bank_account) {
            $query->where('bo.bank_account', $bank_account);
        }
        if ($start_time && $end_time) {
            $query->whereBetween('o.create_time', array(
                $start_time, $end_time
            ));
        }
        if ($game_id) {
            $query->where('o.game_id', $game_id);
        }
        if ($get_payment > -1){ //-1代表不限制此条件，0代表未支付，1代表支付
            $query->where('o.get_payment', $get_payment);
        }
        if ($offer_yuanbao > -1){ //-1代表不限制此条件，0代表未发放，1代表发放
            $query->where('o.offer_yuanbao', $offer_yuanbao);
        }
        if($platform_server_id){
            $query->where('o.server_id', $platform_server_id);
        }
        if($limit_order){
            $query->groupBy('p.player_id','sl.server_id')
            ->orderby('yuanbao_amount','DESC')->get();
        }else{
            $query->orderBy('o.order_id','DESC')->get();
        }
        return $query;
    }

    public
    function scopeUnPayOrder($query, $db_qiqiwu, $start_time, $end_time, $failed_times, $game_id, $platform_server_id, $order_by='', $order_desc='')
    {
        $query->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id');
        $query->leftJoin("{$db_qiqiwu}.create_player as cp", function ($join) {
            $join->on('cp.uid', '=', 'o.pay_user_id')
                ->on('cp.server_id', '=', 'sl.server_internal_id');
        });
        $query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id');
        $query->selectRaw('
            COUNT(o.order_id) as count,
            sum(o.pay_amount * o.exchange) as dollar_amount,
            cp.player_id,
            cp.player_name,
            u.uid,
            u.login_email,
            sl.server_id,
            sl.server_name,
            o.game_id,
            max(o.create_time) as create_time
            '
        );
        $query->where('o.offer_yuanbao', '=', 0)
            ->whereBetween('o.create_time', array($start_time, $end_time));
        if ($platform_server_id) {
            $query->where('o.server_id', $platform_server_id);
        } else if ($game_id) {
            $query->where('o.game_id', $game_id);
        }
        $query->having('count', '>=', $failed_times);
        $query->groupBy('o.pay_user_id', 'o.server_id');
        if($order_by && $order_desc){
            $query->orderBy($order_by, $order_desc);
        }
    }

    public
    function scopeYuanbaoRank($query, $db_qiqiwu, $start_time = '', $end_time = '', $currency_id, $platform_server_id, $server_internal_id, $db_server, $game_id, $app_id)
    {
        if ($server_internal_id > 0) {
            $query->leftJoin("{$db_qiqiwu}.create_player as cp", function ($join) use ($server_internal_id) {
                $join->on('cp.uid', '=', 'o.pay_user_id')
                    ->where('cp.server_id', '=', $server_internal_id);
            });
            $sub_sql = "(SELECT player_id, max(login_time) as login_time FROM `{$db_server}`.log_login GROUP BY player_id) as lo";
            $query->leftJoin(DB::raw($sub_sql), 'lo.player_id', '=', 'cp.player_id');
        }

        $query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id');

        if(0 == $app_id){//无tp_applications表
            $query->leftJoin("{$db_qiqiwu}.third_party as tp", function ($join) {
                $join->on('tp.uid', '=', 'u.uid')
                    ->where('tp_code', '=', 'fb');
            });
        }else{//同一个UID可能有多个第三方id，必须通过app_id区分
            $query->leftJoin("{$db_qiqiwu}.third_party as tp", function ($join) use ($app_id) {
                $join->on('tp.uid', '=', 'u.uid')
                    ->where('tp_code', '=', 'fb')
                    ->where('app_id','=', '{$app_id}');
            });
        }
        
        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        $sql = "
            SUM(o.yuanbao_amount) as total_yuanbao_amount,
            SUM(pay_amount * exchange /({$sql_currency})) as total_amount,
            SUM(pay_amount * exchange) as total_dollar_amount,
            COUNT(o.order_id) as count,
            AVG(pay_amount * exchange /({$sql_currency})) as avg_amount,
            AVG(pay_amount * exchange) as avg_dollar_amount,
            cp.player_id,
            cp.player_name,
            u.uid,
            IF(lo.login_time, lo.login_time, 0) as last_visit_time,
            u.login_email,
            u.nickname,
            u.u,
            u.u2,
            u.source,
            u.is_anonymous,
            u.still_anonymous,
            u.created_ip,
            tp.tp_user_id,
            o.pay_user_id,
            o.server_id,
            MAX(o.pay_time) as last_order_time,
            MIN(o.pay_time) as first_order_time
            ";

        $query->selectRaw($sql);

        if ($platform_server_id) {
            $query->where('o.server_id', $platform_server_id);
        } else if ($game_id) {
            $query->where('o.game_id', $game_id);
        }
        if ($start_time && $end_time) {
            $query->whereBetween('o.create_time', array(
                $start_time, $end_time
            ));
        }
        $query->where('get_payment', '=', 1);
        $query->groupBy('o.pay_user_id');
        $query->orderBy('total_dollar_amount', 'DESC');
    }


    public function scopeYuanbaoRankForMG($query, $db_qiqiwu, $start_time = '', $end_time = '', $currency_id, $platform_server_ids, $db_server, $game_id){
        $query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id')
              ->leftJoin("{$db_qiqiwu}.server_list as sl", 'sl.server_id', '=', 'o.server_id')
              ->leftJoin("{$db_qiqiwu}.create_player as cp", function($join) use ($game_id){
                    $join->on('cp.uid', '=', 'o.pay_user_id')
                         ->on('cp.server_id', '=', 'sl.server_internal_id')
                         ->where('cp.game_id', '=', $game_id);
              });
        
        $sql = "
            SUM(o.yuanbao_amount) as total_yuanbao_amount,
            SUM(pay_amount * exchange) as total_dollar_amount,
            COUNT(o.order_id) as count,
            AVG(pay_amount * exchange) as avg_dollar_amount,
            cp.player_id,
            cp.player_name,
            u.uid,
            u.login_email,
            u.nickname,
            u.created_ip,
            o.pay_user_id,
            o.server_id,
            sl.server_internal_id,
            sl.server_name,
            cp.remote_host_ip as created_ip,
            from_unixtime(cp.created_time) as created_time,
            MAX(o.pay_time) as last_order_time,
            MIN(o.pay_time) as first_order_time
            ";            

        $query->selectRaw($sql);

        if (count($platform_server_ids)) {
            $query->whereIn('o.server_id', $platform_server_ids)
                  ->groupBy('o.server_id');
        } else if ($game_id) {
            $query->where('o.game_id', $game_id);
        }
        if ($start_time && $end_time) {
            $query->whereBetween('o.create_time', array(
                $start_time, $end_time
            ));
        }
        $query->where('get_payment', '=', 1);
        $query->groupBy('o.pay_user_id');
        $query->orderBy('total_dollar_amount', 'DESC');
    }

    public
    function scopeAllYuanbaoRank($query, $db_qiqiwu, $start_time = '', $end_time = '', $currency_id, $game_id, $platform_server_id, $server_internal_id, $lower_bound = '', $upper_bound = '', $db_server)
    {
        //Log::info("PayOrder-log---$platform_server_id:".$platform_server_id."---start time:".$start_time."---end time:".$end_time."---lower_bound:".$lower_bound."---upper_bound:".$upper_bound."---db_name:".$db_server);
        if ($server_internal_id > 0) {
            $query->leftJoin(DB::raw("`{$db_server}`.log_create_player as cp"), function ($join) use ($server_internal_id) {
                $join->on('cp.user_id', '=', 'o.pay_user_id')
                    ->where('cp.server_id', '=', $server_internal_id);
            });
            $sub_sql = "(SELECT player_id, login_time, is_login FROM (SELECT * FROM `{$db_server}`.log_login ORDER BY login_time DESC) as temp GROUP BY player_id) as lo";
            $query->leftJoin(DB::raw($sub_sql), 'lo.player_id', '=', 'cp.player_id');
        }

        $query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id');

        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        $sql = "
            SUM(o.yuanbao_amount) as total_yuanbao_amount,
            SUM(pay_amount * exchange /({$sql_currency})) as total_amount,
            SUM(pay_amount * exchange) as total_dollar_amount,
            COUNT(o.order_id) as count,
            AVG(pay_amount * exchange /({$sql_currency})) as avg_amount,
            AVG(pay_amount * exchange) as avg_dollar_amount,
            cp.player_id,
            cp.player_name,
            o.pay_user_id,
            o.server_id,
            u.uid,
            u.login_email,
            u.nickname,
            u.u,
            u.u2,
            u.source,
            u.is_anonymous,
            u.still_anonymous,
            u.created_ip,
            MAX(o.pay_time) as last_order_time,
            MIN(o.pay_time) as first_order_time
            ";
        $query->selectRaw($sql);

        if ($platform_server_id) {
            $query->where('o.server_id', $platform_server_id);
        } else if ($game_id) {
            $query->where('o.game_id', $game_id);
        }
        if ($start_time && $end_time) {
            $query->whereBetween('o.pay_time', array(
                $start_time, $end_time
            ));
        }
        $query->where('get_payment', '=', 1);
        $query->groupBy('o.pay_user_id');
        if ($lower_bound) {
            $query->having('total_yuanbao_amount', ">=", $lower_bound);
        }
        if ($upper_bound) {
            $query->having('total_yuanbao_amount', "<=", $upper_bound);
        }
        $query->orderBy('total_yuanbao_amount', 'DESC');
    }

    //此方法获取某个具体游戏服务器下的所有玩家充值信息
    public function scopePlayerPaymentFilter($query, $db_qiqiwu, $start_time = '', $end_time = '', $currency_id, $game_id, $platform_server_id, $server_internal_id, $lower_bound = '', $upper_bound = '', $db_server){
        if ($server_internal_id > 0) {
            $query->leftJoin(DB::raw("`{$db_server}`.log_create_player as cp"), function ($join) use ($server_internal_id) {    //用日志库不需要再使用server_internal_id
                $join->on('cp.user_id', '=', 'o.pay_user_id');
            });
        }

        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        $sql = "
            SUM(o.yuanbao_amount) as total_yuanbao_amount,
            SUM(pay_amount * exchange)/({$sql_currency}) as total_amount,
            SUM(pay_amount * exchange) as total_dollar_amount,
            COUNT(o.order_id) as count,
            cp.player_id,
            cp.player_name,
            o.pay_user_id,
            o.server_id
            ";
        $query->selectRaw($sql);

        if ($platform_server_id) {
            $query->where('o.server_id', $platform_server_id);
        }
        if ($game_id) {
            $query->where('o.game_id', $game_id);
        }
        if ($start_time && $end_time) {
            $query->whereBetween('o.pay_time', array(
                $start_time, $end_time
            ));
        }
        $query->where('get_payment', '=', 1);
        $query->groupBy('o.pay_user_id');
        if ($lower_bound) {
            $query->having('total_yuanbao_amount', ">=", $lower_bound);
        }
        if ($upper_bound) {
            $query->having('total_yuanbao_amount', "<=", $upper_bound);
        }
        $query->orderBy('total_yuanbao_amount', 'DESC');        
    }

    public
    function scopePayTypeStat($query, $pay_type_id, $start_time, $end_time, $currency_id, $game_id)
    {
        $query->leftJoin("pay_order as po", function ($join) {
            $join->on('po.order_id', '=', 'o.order_id')
                ->where('po.get_payment', '=', 1);
        });
        $sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        $query->selectRaw("
            o.pay_type_id,
            o.method_id,    
            o.money_flow_name,
            o.zone,
            MAX(o.create_time) as pay_time_last,
            MIN(o.create_time) as pay_time_first,
            ".((70 == $game_id) ? "SUM(po.pay_amount)" : "SUM(po.pay_amount * po.exchange)/({$sql_currency})")." as total_amount,
            SUM(po.pay_amount * ".((70 == $game_id) ? "({$sql_currency})" : "po.exchange").") as total_dollar_amount,
            COUNT(po.order_id) as get_payment_count,
            COUNT(o.order_id) as count 
        ");
        if ($pay_type_id) {
            $query->where('o.pay_type_id', $pay_type_id);
        }
        if ($start_time && $end_time) {
            $query->whereBetween('o.create_time', array(
                $start_time, $end_time
            ));
        }
        $query->where('o.game_id', $game_id);
        $query->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('refund_order')
                ->whereRaw('refund_order.order_sn = o.order_sn');
        });
        $query->groupBy('o.pay_type_id', 'o.method_id', 'o.money_flow_name', 'o.zone');
        return $query;

    }
      public
    function scopeYuanbaoSearch($query, $db_qiqiwu, $start_time = '', $end_time = '', $currency_id, $platform_server_id, $server_internal_id, $db_server, $game_id)
    {
        if ($server_internal_id > 0) {
            if(in_array($game_id, Config::get('game_config.yysggameids'))){
                $query->leftJoin(DB::raw("`{$db_server}`.log_create_player as cp"), function ($join) {
                    $join->on('cp.uid', '=', 'o.pay_user_id');
                });
            }else{
                $query->leftJoin("{$db_qiqiwu}.create_player as cp", function ($join) use ($server_internal_id) {
                    $join->on('cp.uid', '=', 'o.pay_user_id')
                        ->where('cp.server_id', '=', $server_internal_id);
                });
            }
            if(in_array($game_id, Config::get('game_config.mobilegames'))){
                $sub_sql = "(SELECT player_id, action_time as login_time, action as is_login FROM (SELECT * FROM `{$db_server}`.log_login ORDER BY action_time DESC) as temp GROUP BY player_id) as lo";
            }else{
                $sub_sql = "(SELECT player_id, login_time, is_login FROM (SELECT * FROM `{$db_server}`.log_login ORDER BY login_time DESC) as temp GROUP BY player_id) as lo";
            }
            $query->leftJoin(DB::raw($sub_sql), 'lo.player_id', '=', 'cp.player_id');
        }

        $query->leftJoin("{$db_qiqiwu}.users as u", 'u.uid', '=', 'o.pay_user_id');

        $is_app_id = (DB::connection($db_qiqiwu)->select("show tables like 'tp_applications'") && DB::connection($db_qiqiwu)->select("DESC tp_applications game_id")) ? 
        DB::connection($db_qiqiwu)->select("SELECT app_id FROM tp_applications WHERE game_id = {$game_id} limit 1") : 0;
        //同一个UID可能有多个第三方id，必须通过app_id区分
        if($is_app_id){
            $app_id = $is_app_id[0]->app_id;
            $query->leftJoin("{$db_qiqiwu}.third_party as tp", function ($join) use ($app_id) {
                $join->on('tp.uid', '=', 'u.uid')
                    ->where('tp_code', '=', 'fb')
                    ->where('app_id','=', "{$app_id}"); 
            });
        }else{
            $query->leftJoin("{$db_qiqiwu}.third_party as tp", function ($join){
                $join->on('tp.uid', '=', 'u.uid')
                    ->where('tp_code', '=', 'fb');
            });
        }
        
        $sql = "
            SUM(o.yuanbao_amount) as total_yuanbao_amount,
            SUM(pay_amount * exchange) as total_dollar_amount,
            cp.player_id,
            cp.player_name,
            u.uid,
            IF(lo.is_login = '-1', lo.login_time, 0) as last_visit_time,
            o.pay_user_id,
            MAX(o.pay_time) as last_order_time,
            MIN(o.pay_time) as first_order_time
            ";

        $query->selectRaw($sql);

        if ($platform_server_id) {
            $query->where('o.server_id', $platform_server_id);
        } else if ($game_id) {
            $query->where('o.game_id', $game_id);
        }
        if ($start_time && $end_time) {
            $query->whereBetween('o.pay_time', array(
                $start_time, $end_time
            ));
        }
        $query->where('get_payment', '=', 1);
        $query->groupBy('o.pay_user_id');
        $query->orderBy('total_dollar_amount', 'DESC');
    }

    public function scopeGetFirstPayInfo($query, $game_id, $start_time, $end_time){
        $query->where('get_payment', 1)
              ->where('game_id', $game_id)
              ->whereBetween('pay_time', array($start_time, $end_time))
              ->groupBy('pay_user_id')
              ->selectRaw("pay_amount*exchange as pay_dollar, count(1) as pay_num");
        return $query;
    }

    public function scopeGetAmountInfo($query, $game_id, $start_time, $end_time){
        $query->where('get_payment', 1)
              ->where('game_id', $game_id)
              ->whereBetween('pay_time', array($start_time, $end_time))
              ->groupBy('pay_user_id')
              ->selectRaw("sum(pay_amount*exchange) as sum_dollar, count(1) as pay_num")
              ->orderBy('sum_dollar', 'desc');
        return $query;
    }

    public function scopeGetConsumptionRank($query, $db_qiqiwu, $start_time, $end_time, $interval, $currency_id, $game_id, $rank, $platform_server_id, $server_internal_id){
        //$sql_currency = "select exchange from exchange where type = {$currency_id} ORDER BY  timeline DESC LIMIT 1";
        if($platform_server_id){
            $query->leftJoin("{$db_qiqiwu}.create_player as cp", function($join) use ($server_internal_id,$game_id){
                $join->on('cp.uid','=','o.pay_user_id')
                ->where('cp.server_id','=',$server_internal_id)
                ->where('cp.game_id','=',$game_id);
            });
        }else{
            $query->leftJoin("{$db_qiqiwu}.create_player as cp", function($join) use ($game_id){
                $join->on('cp.uid','=','o.pay_user_id')
                ->where('cp.game_id','=',$game_id);
            });
        }
        $query->leftJoin("{$db_qiqiwu}.server_list as sl",function($join) use($game_id){
            $join->on('sl.server_id','=','o.server_id')
                ->where('sl.game_id','=',$game_id);
        });
        $query->selectRaw("cp.player_name,cp.player_id,sl.server_name,FROM_UNIXTIME(o.pay_time,'%Y-%m-%d') as time,count(o.order_id) as times,o.pay_user_id as uid,SUM(o.pay_amount * o.exchange) as total_dollar_amount")
        ->where('o.game_id',$game_id)
        ->where('o.get_payment',1)
        ->whereBetween('o.pay_time',array($start_time,$end_time));
        if($platform_server_id){
            $query->where('o.server_id',$platform_server_id)
            ->groupBy('o.pay_user_id')
            ->orderBy('total_dollar_amount','DESC')->take($rank);
        }else{
            $query->groupBy('o.pay_user_id','o.server_id')
            ->orderBy('o.server_id','DESC')->orderBy('total_dollar_amount','DESC')->take($rank);
        }
        return $query;

    }

    public function scopeGetSignUpTimeGroup($query, $qiqiwu, $game_id, $start_time, $end_time, $part){
        if('new' == $part){
            $query->Join("$qiqiwu.users as u", function ($join) use ($start_time) {
                $join->on('o.pay_user_id', '=', 'u.uid')
                     ->where('u.created_time', '>', date('Y-m-d H:i:s', ($start_time-7*86400)));    //七天内注册的玩家视为新玩家
            });
        }
        $query->where('o.get_payment', 1)
              ->where('o.game_id', $game_id)
              ->whereBetween('o.pay_time', array($start_time, $end_time))
              ->selectRaw("sum(pay_amount*exchange) as sum_dollar, count(distinct o.pay_user_id) as pay_num");
        return $query;        
    }

    public function scopeChannelPayOrder($query, $qiqiwu, $cre_start_time, $cre_end_time, $channle_order_start_time, $channle_order_end_time, $game_id, $channel=''){ //周报channel支付相关
        $query->Join("$qiqiwu.users as u", function ($join) use ($cre_start_time, $cre_end_time) {
                $join->on('o.pay_user_id', '=', 'u.uid')
                     ->where('u.created_time','>=', date('Y-m-d H:i:s', $cre_start_time))
                     ->where('u.created_time','<=', date('Y-m-d H:i:s', $cre_end_time));
            })->where('o.game_id', $game_id)
              ->where('get_payment', 1)
              ->whereBetween('pay_time', array($channle_order_start_time, $channle_order_end_time));

        if($channel){
            $query->where('channel', $channel);
        }else{
            $query->groupBy('channel');
        }

        $query->selectRaw('channel, count(distinct pay_user_id) as pay_num, sum(pay_amount*exchange) as pay_dollar');

        return $query;
    }

    public function scopegetFilterOrders($query, $db_qiqiwu, $filter_data){
        $query->Join("$db_qiqiwu.users as u", function($join) {
            $join->on('u.uid', '=', 'o.pay_user_id');
        });

        if('all' == $filter_data['filter_type']){
            $query->selectRaw('count(distinct pay_user_id) as pay_player_num, count(1) as pay_num, sum(pay_amount*exchange) as sum_dollar_amount, sum(yuanbao_amount) as sum_yuanbao_amount');
        }
        if('order' == $filter_data['filter_type']){
            $query->Join("$db_qiqiwu.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("$db_qiqiwu.create_player as cp", function($join) use ($filter_data){
                    $join->on('cp.uid', '=', 'o.pay_user_id')
                         ->on('cp.server_id', '=', 'sl.server_internal_id');
                    if(!in_array($filter_data['game_id'], array(8, 36, 41, 43, 44, 45, 70))){
                        $join->where('cp.game_id', '=', $filter_data['game_id']);
                    }
                });
            $query->selectRaw('cp.player_id, sl.server_name, u.last_visit_time, o.pay_user_id, order_sn, pay_amount*exchange as sum_dollar_amount, yuanbao_amount as sum_yuanbao_amount');       
        }
        if('user' == $filter_data['filter_type']){
            $query->selectRaw('pay_user_id, u.last_visit_time, count(1) as pay_num, sum(pay_amount*exchange) as sum_dollar_amount, sum(yuanbao_amount) as sum_yuanbao_amount')
                  ->groupBy('o.pay_user_id');
        }
        if('player' == $filter_data['filter_type']){
            $query->Join("$db_qiqiwu.server_list as sl", 'sl.server_id', '=', 'o.server_id')
                ->leftJoin("$db_qiqiwu.create_player as cp", function($join) use ($filter_data){
                    $join->on('cp.uid', '=', 'o.pay_user_id')
                         ->on('cp.server_id', '=', 'sl.server_internal_id')
                         ->where('cp.game_id', '=', $filter_data['game_id']);
                });
            $query->selectRaw('cp.player_id, sl.server_name, u.last_visit_time, o.pay_user_id, count(1) as pay_num, sum(pay_amount*exchange) as sum_dollar_amount, sum(yuanbao_amount) as sum_yuanbao_amount')
                  ->groupBy('o.pay_user_id')->groupBy('o.server_id');
        }


        $query->where('o.game_id', $filter_data['game_id'])
            ->where('o.get_payment', 1);
        if($filter_data['by_pay_time']){
             $query->whereBetween('o.pay_time', array($filter_data['pay_start_time'], $filter_data['pay_end_time']));
        }
        if($filter_data['by_reg_time']){
             $query->whereBetween('u.created_time', array($filter_data['reg_start_time'], $filter_data['reg_end_time']));
        }
        if($filter_data['by_last_login_time']){
            $query->where('u.last_visit_time', $filter_data['by_last_login_time'], $filter_data['last_login_time']);
        }
        if($filter_data['by_dollar_amount']){
            $query->having('sum_dollar_amount', $filter_data['by_dollar_amount'], $filter_data['dollar_amount']);
        }
        if($filter_data['by_yuanbao_amount']){
            $query->having('sum_yuanbao_amount', $filter_data['by_yuanbao_amount'], $filter_data['yuanbao_amount']);
        }
        if('all' != $filter_data['filter_type']){
            $query->orderBy('pay_user_id');
        }

        return $query;
    }

    public function scopegetPayTrendInfo($query, $start_time, $end_time, $game_id, $interval){
        $time_sql = "({$start_time}+floor((create_time-{$start_time})/{$interval})*{$interval}) as time,";
        $query->where('game_id', $game_id)
              ->whereBetween('create_time', array($start_time, $end_time))
              ->selectRaw($time_sql." count(distinct pay_user_id) as all_user, count(1) as all_times, sum(pay_amount*exchange) as all_dollar,
               count(distinct if(get_payment=1, pay_user_id, null)) as pay_user, count(if(get_payment=1,1,null)) as pay_times, sum(if(get_payment=1, pay_amount*exchange, 0)) as pay_dollar")
              ->groupBy('time')
              ->orderby('time');
    }

}