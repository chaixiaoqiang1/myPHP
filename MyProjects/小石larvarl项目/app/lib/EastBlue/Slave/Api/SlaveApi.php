<?php namespace EastBlue\Slave\Api;


use \Curl;
use \Response;
use \Log;

class SlaveApi implements SlaveApiInterface
{

    const ECONOMY_TYPE_TONGQIAN = 'tongqian';
    const ECONOMY_TYPE_YUANBAO = 'yuanbao';
    const ECONOMY_TYPE_GONGXUN = 'gongxun';
    const ORDER_STATUS_UNPAY = 1;
    const ORDER_STATUS_PAYING = 2;
    const ORDER_STATUS_COMPLETE_3 = 3;
    const ORDER_STATUS_COMPLETE_4 = 4;

    protected $api_key = '';
    protected $api_secret_key = '';
    protected $url = '';

    public function connect($api_url, $api_key, $api_secret_key)
    {
        $this->api_url = $api_url;
        $this->api_key = $api_key;
        $this->api_secret_key = $api_secret_key;
        return $this;
    }

    protected function getResponse($method, $params, $is_post = false)
    {
        $methodURL = $method;
        $method = $this->api_url . $method;
        if ($is_post) {
            $response = Curl::url($method)->postFields($params)->post();
        } else {
            $params = http_build_query($params);
            $method .= '?' . $params; 
            //var_dump($method);
            $response = Curl::url($method)->get();
        }
        ////////////////////////////////取消注释可打印  post 和 response 的详细信息。
        // if('/player/economy/analysis' == $methodURL) {
        //     foreach ($response as $k => $v) {
        //         $data_res[$k] = $v;
        //     }
        //     Log::info("PANDA--TEST--after--curl--get method:" . $method . "-----get params:" . var_export($params, true) . "-----get response:" . var_export($data_res, true));
        // }
        /////////////////////////////////////////////////////////////////////////

        $this->response = $response;
        return $response;       
    }

    public function sendResponse()
    {
        $http_code = $this->response->http_code;
        $body = $this->response->body;
        if('504' == $http_code){
            return Response::json(array('error'=>'超时未得到返回值'), $http_code);
        }elseif('500' == $http_code){
            return Response::json(array('error'=>'slave端发生错误或无法连接服务器'), $http_code);
        }elseif('502' == $http_code){
            return Response::json(array('error'=>'502 bad gateway'), $http_code);
        }elseif('404' == $http_code || '403' == $http_code){
            return Response::json(array('error'=>'没有满足条件的数据'), $http_code);
        }else{
            return Response::json($body, $http_code);
        }
    }

    private function makeSignMD5($params)
    {
        $params['api_secret_key'] = $this->api_secret_key;
        uksort($params, 'strcmp');
        return md5(http_build_query($params));
    }

    public function getUnionServers($game_id, $ser)
    {
        $len = count($ser);
        for ($i = 0; $i < $len; $i++) {
            $game_arr[$i] = $ser[$i]->gameid;
        }
        $game_arr = array_unique($game_arr);
        if (in_array($game_id, $game_arr)) {
            $se = "";
            for ($i = 0; $i < $len; $i++) {
                if ($ser[$i]->gameid == $game_id) {
                    $se .= $ser[$i]->serverid2 . ",";
                }
            }
            $se_arr = explode(",", $se);
            unset($se_arr[count($se_arr) - 1]);
            return $se_arr;
        } else {
            $error = "fail";
            return $error;
        }

    }

    public function getCreatePlayerLog($game_id, $server_internal_id, $start_time, $end_time, $page, $per_page)
    {
        $method = '/player/log';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getplayeridbyname($game_id, $player_name, $server_internal_id, $platform_id){ //使用玩家名称得到id
        $method = '/player/getidbyname';
        $params = array(
            'game_id' => $game_id,
            'player_name' => $player_name,
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getplayernamebyid($game_id, $player_id, $server_internal_id, $platform_id){ //使用玩家名称得到id
        $method = '/player/getnamebyid';
        $params = array(
            'game_id' => $game_id,
            'player_id' => $player_id,
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerOnlineTrend($game_id, $server_internal_id, $start_time, $end_time, $interval)
    {
        $method = '/login/trend';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'interval' => $interval,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getLoginTotalByTime($game_id, $server_internal_id, $start_time, $end_time, $interval, $level)
    {
        $method = '/login/total';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'interval' => $interval,
            'level' => $level,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerRank($platform_id, $game_id, $server_internal_id,$is_created_time, $start_time, $end_time, $levelup_time, $page, $per_page = 30, $level_lower_bound, $level_upper_bound)
    {
        $method = '/player/rank';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'is_created_time' => $is_created_time,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'levelup_time' => $levelup_time,
            'page' => $page,
            'per_page' => $per_page,
            'level_lower_bound' => $level_lower_bound,
            'level_upper_bound' => $level_upper_bound,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerLevelTrend($platform_id, $game_id, $server_internal_id, $is_anonymous, $start_time, $end_time)
    {
        $method = '/player/trend';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'is_anonymous' => $is_anonymous,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerRetention($game_id, $server_internal_id, $start_time, $end_time, $is_anonymous)
    {
        $method = '/player/retention';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_anonymous' => $is_anonymous,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerChannelRetention($game_id, $server_internal_id, $start_time, $end_time, $is_anonymous, $channel_name){
        $method = '/player/channel/retention';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_anonymous' => $is_anonymous,
            'channel_name' => $channel_name,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getCreatedPlayerNumberByTime($game_id, $server_internal_id, $start_time, $end_time, $interval)
    {
        $method = '/player/created';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'interval' => $interval,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function runOpenServerScript($game_id, $server_internal_id, $server_ip, $api_dir_id, $open_server_time)
    {
        $method = '/server/opened';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'server_ip' => $server_ip,
            'api_dir_id' => $api_dir_id,
            'open_server_time' => $open_server_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function releaseProjectScript($project_id, $svn_name, $version, $exclude_files)
    {
        $method = '/project/release';
        $params = array(
            'project_id' => $project_id,
            'svn_name' => $svn_name,
            'version' => $version,
            'exclude_files' => $exclude_files,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function checkProjectScript($project_id)
    {
        $method = '/project/check';
        $params = array(
            'project_id' => $project_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function syncServer($platform_id)
    {
        $method = '/server/sync';
        $params = array(
            'platform_id' => $platform_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /**
     * 获取当前网站使用的汇率
     * */
    public function getExchangeRate($platform_id)
    {
        $method = '/platform/exchange-rate';
        $params = array(
            'platform_id' => $platform_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /**
     * 获取当前网站使用的支付方式类型
     * */
    public function getPlatformPayType($platform_id)
    {
        $method = '/platform/pay-type';
        $params = array(
            'platform_id' => $platform_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
   



    /**
     * 获取当前网站使用的商家信息
     * */
    public function getPlatformMerchantData($platform_id, $id = '')
    {
        $method = '/platform/merchant-data';
        $params = array(
            'platform_id' => $platform_id,
            'id' => $id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*
     * 获取玩家个人-消费统计
     * type为 yuanbao \ tongqian \ gongxun 见最前面的const
     */
    public function getPlayerEconomy($game_id, $server_internal_id, $player_id, $type, $start_time, $end_time, $look_type, $action_type_num, $page, $per_page = 30)
    {
        $method = '/player/economy';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'type' => $type,
            'page' => $page,
            'per_page' => $per_page,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'look_type' => $look_type,
            'action_type_num' => $action_type_num,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getSimplePlayerEconomyTotal($game_id, $server_internal_id, $player_id, $type, $start_time, $end_time, $page, $per_page = 30)
    {
        $method = '/player/simple-total';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'type' => $type,
            'page' => $page,
            'per_page' => $per_page,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getSimplePlayerEconomy($game_id, $server_internal_id, $player_id, $type, $start_time, $end_time, $page, $per_page = 30)
    {
        $method = '/player/simple-economy';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'type' => $type,
            'page' => $page,
            'per_page' => $per_page,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getAllPlayerEconomy($game_id, $server_internal_id, $player_id, $type, $start_time, $end_time)
    {
        $method = '/player/all-economy';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*获取玩家-消费排行*/
    public function getPlayerEconomyRank($game_id, $server_internal_id, $type)
    {
        $method = '/player/economy/rank';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'type' => $type,
            'api_key' => $this->api_key
        );

        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerEconomyRankWithTime($game_id, $server_internal_id, $type, $start_time, $end_time)
    {
        $method = '/player/economy/rank';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );

        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*查找世界boss杀手*/
    public function findBossKiller($game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = '/player/economy/find-boss-killer';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );

        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*获取玩家-消费分析*/
    public function getPlayerEconomyAnalysis($platform_id, $game_id, $server_internal_id, $type, $action_type, $start_time, $end_time, $lower_bound, $upper_bound = '', $no_name = 0)
    {
        $method = '/player/economy/analysis';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'type' => $type,
            'action_type' => $action_type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'lower_bound' => $lower_bound,
            'upper_bound' => $upper_bound,
            'no_name' => $no_name,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*获取玩家个人-消费统计*/
    public function getPlayerEconomyStatistics($game_id, $server_internal_id, $player_id, $type, $start_time, $end_time, $look_type, $action_type_num)
    {
        $method = '/player/economy/stat';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'look_type' => $look_type,
            'action_type_num' => $action_type_num,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*获取单服-消费统计*/
    public function getServerEconomyStatistics($game_id, $platform_id, $server_internal_id, $type, $start_time, $end_time, $player_level, $is_filter_neiwan, $vip_selector)
    {
        $method = '/server/economy';
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_level' => $player_level,
            'is_filter_neiwan' => $is_filter_neiwan,
            'vip_selector' => $vip_selector,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getAllServersConsume($game_id, $platform_id, $server_internal_id, $type, $start_time, $end_time)
    {
        $method = '/server/consume';
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'type' => $type,
            'end_time' => $end_time,
            'start_time' => $start_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*获取全服的充值统计数据*/
    public function getGameOrderStatistics($platform_id, $game_id, $currency_id, $start_time, $end_time, $devide_servers='0')
    {
        $method = '/payment/order/stat/game';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'devide_servers' => $devide_servers,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*获取单服的充值统计数据*/
    public function getServerOrderStatistics($platform_id, $platform_server_id, $currency_id, $open_server_time, $start_time, $end_time, $game_id, $server_internal_id, $game_code)
    {
        $method = '/payment/order/stat/server';
        $params = array(
            'platform_id' => $platform_id,
            'platform_server_id' => $platform_server_id,
            'currency_id' => $currency_id,
            'open_server_time' => $open_server_time,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'game_code' => $game_code,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getOrders($order, $page, $per_page = 30, $statistics_time)
    {
        if (!is_array($order)) {
            return;
        }
        $method = '/payment/order';
        $params = array(
            'platform_id' => $order['platform_id'],
            'pay_type_id' => $order['pay_type_id'],
            'low_amount' => $order['low_amount'],
            'high_amount' => $order['high_amount'],
            'low_gold' => $order['low_gold'],
            'high_gold' => $order['high_gold'],
            'start_time' => $order['start_time'],
            'end_time' => $order['end_time'],
            'page' => $page,
            'per_page' => $per_page,
            'statistics_time' => $statistics_time,
            'api_key' => $this->api_key,
        );
        if (isset($order['method_id'])) {
            $params['method_id'] = $order['method_id'];
        }
        if (isset($order['platform_server_id']) && $order['platform_server_id']) {
            $params['platform_server_id'] = $order['platform_server_id'];
        }
        if (isset($order['game_id']) && $order['game_id']) {
            $params['game_id'] = $order['game_id'];
        }
        if (isset($order['offer_yuanbao']) && $order['offer_yuanbao'] !== null) {
            $params['offer_yuanbao'] = $order['offer_yuanbao'];
        }
        if (isset($order['get_payment'])) {
            $params['get_payment'] = $order['get_payment'];
        }
        if (isset($order['sdk_id'])) {
            $params['sdk_id'] = $order['sdk_id'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getAllOrders($order)
    {
        if (!is_array($order)) {
            return;
        }
        $method = '/payment/all-order';
        $params = array(
            'platform_id' => $order['platform_id'],
            'pay_type_id' => $order['pay_type_id'],
            'low_amount' => $order['low_amount'],
            'high_amount' => $order['high_amount'],
            'low_gold' => $order['low_gold'],
            'high_gold' => $order['high_gold'],
            'start_time' => $order['start_time'],
            'end_time' => $order['end_time'],
            'api_key' => $this->api_key,
        );
        if (isset($order['platform_server_id']) && $order['platform_server_id']) {
            $params['platform_server_id'] = $order['platform_server_id'];
        }
        if (isset($order['game_id']) && $order['game_id']) {
            $params['game_id'] = $order['game_id'];
        }
        if (isset($order['offer_yuanbao']) && $order['offer_yuanbao'] !== null) {
            $params['offer_yuanbao'] = $order['offer_yuanbao'];
        }
        if (isset($order['get_payment'])) {
            $params['get_payment'] = $order['get_payment'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getOrdersByUser($platform_id, $uid, $player_name, $player_id, $start_time, $end_time, $bank_account = '', $game_id, $get_payment, $offer_yuanbao, $platform_server_id, $limit_order)
    {
        $method = '/payment/order/user';
        $params = array(
            'platform_id' => $platform_id,
            'uid' => $uid,
            'player_name' => $player_name,
            'player_id' => $player_id,
            'bank_account' => $bank_account,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'game_id' => $game_id,
            'get_payment' => $get_payment,
            'offer_yuanbao' => $offer_yuanbao,
            'platform_server_id' => $platform_server_id,
            'limit_order' => $limit_order,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getLuckyOrderSN($platform_id, $lucky_number, $start_time, $end_time)
    {
        $method = '/payment/order/lucky-order_sn';
        $params = array(
            'start_time' => $start_time,
            'end_time' => $end_time,
            'platform_id' => $platform_id,
            'lucky_number' => $lucky_number,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getOrderByOrderID($platform_id, $order_id, $game_id){
        $method = '/payment/order/order_id';
        $params = array(
            'platform_id' => $platform_id,
            'order_id' => $order_id,
            'game_id' => $game_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getOrderByOrderSN($platform_id, $order_sn, $game_id)
    {
        $method = '/payment/order/order_sn';
        $params = array(
            'platform_id' => $platform_id,
            'order_sn' => $order_sn,
            'game_id' => $game_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getOrderByTradeseq($platform_id, $tradeseq, $game_id)
    {
        $method = '/payment/order/tradeseq';
        $params = array(
            'platform_id' => $platform_id,
            'tradeseq' => $tradeseq,
            'game_id' => $game_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getUserDevice($user)
    {
        $method = '/user/device';
        $params = array(
            //'game_id' => $user['game_id'],
            //'server_internal_id' => $user['server_internal_id'],
            //'platform_server_id' => $user['platform_server_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'check_type' => $user['check_type'],
            'game_id' => $user['game_id'],
            'serach_type' => $user['serach_type'],
            'channel_type' => $user['channel_type'],
            'source' => $user['source'],
            'api_key' => $this->api_key,
        );
        
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getUserStat($user,$classify)
    {
        $method = '/user/stat';
        $params = array(
            'game_id' => $user['game_id'],
            'server_internal_id' => $user['server_internal_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'filter' => $user['filter'],
            'classify' => $classify,
            'api_key' => $this->api_key
        );
        if (isset($user['platform_server_id'])) {
            $params['platform_server_id'] = $user['platform_server_id'];
        }
        if (isset($user['source']) && $user['source']) {
            $params['source'] = $user['source'];
        }
        if (isset($user['u1']) && $user['u1']) {
            $params['u1'] = $user['u1'];
        }
        if (isset($user['u2']) && $user['u2']) {
            $params['u2'] = $user['u2'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getUserStatSignupinfo($user){
        $method = '/user/stat/signup';
        $params = array(
            'game_id' => $user['game_id'],
            'server_internal_id' => $user['server_internal_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'filter' => $user['filter'],
            'source' => $user['source'],
            'u1' => $user['u1'],
            'u2' => $user['u2'],
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);        
    }

    public function getSetupStat($user){
        $method = '/setup/stat';
        $params = array(
            'game_id' => $user['game_id'],
            'server_internal_id' => $user['server_internal_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'filter' => $user['filter'],
            'source' => $user['source'],
            'u1' => $user['u1'],
            'u2' => $user['u2'],
            'os_type' => $user['os_type'],
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);          
    }

    public function getUserStatCreateplayerinfo($user){
        $method = '/user/stat/createplayer';
        $params = array(
            'game_id' => $user['game_id'],
            'server_internal_id' => $user['server_internal_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'filter' => $user['filter'],
            'source' => $user['source'],
            'u1' => $user['u1'],
            'u2' => $user['u2'],
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);        
    }

    public function getUserStatLevelteninfo($user){
        $method = '/user/stat/levelten';
        $params = array(
            'game_id' => $user['game_id'],
            'server_internal_id' => $user['server_internal_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'filter' => $user['filter'],
            'source' => $user['source'],
            'u1' => $user['u1'],
            'u2' => $user['u2'],
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);        
    }

    public function getUserStatyy($user,$classify){
        $method = '/user/statyy';
        $params = array(
            'game_id' => $user['game_id'],
            'server_internal_id' => $user['server_internal_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'filter' => $user['filter'],
            'classify' => $classify,
            'api_key' => $this->api_key
        );
        if (isset($user['platform_server_id'])) {
            $params['platform_server_id'] = $user['platform_server_id'];
        }
        if (isset($user['source']) && $user['source']) {
            $params['source'] = $user['source'];
        }
        if (isset($user['u1']) && $user['u1']) {
            $params['u1'] = $user['u1'];
        }
        if (isset($user['u2']) && $user['u2']) {
            $params['u2'] = $user['u2'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //广告周报计算用户注册接口
    public function getAdUserStat($user)
    {
        $method = '/user/stat/ad';
        $params = array(
            'game_id' => $user['game_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function SXDGetUserStat($user)
    {
        $method = '/user/sxd/stat';
        $params = array(
            'game_id' => $user['game_id'],
            'platform_server_id' => $user['platform_server_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'filter' => $user['filter'],
            'api_key' => $this->api_key
        );
        if (isset($user['source']) && $user['source']) {
            $params['source'] = $user['source'];
        }
        if (isset($user['u1']) && $user['u1']) {
            $params['u1'] = $user['u1'];
        }
        if (isset($user['u2']) && $user['u2']) {
            $params['u2'] = $user['u2'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getUserByUID($platform_id, $uid, $server_internal_id = 0, $game_id = 0)
    {
        $method = '/user';
        $params = array(
            'platform_id' => $platform_id,
            'uid' => $uid,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getUserByEmail($platform_id, $email, $server_internal_id = 0, $game_id = 0)
    {
        $method = '/user';
        $params = array(
            'platform_id' => $platform_id,
            'email' => $email,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }


    public function neiwan($params, $is_delete)
    {
        $method = '/user/neiwan';
        $params['is_delete'] = $is_delete;
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getFailedOrderUser($platform_id, $game_id, $start_time, $end_time, $failed_times, $platform_server_id, $order_by, $order_desc)
    {
        $method = '/payment/order/unpay';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'failed_times' => $failed_times,
            'platform_server_id' => $platform_server_id,
            'order_by' => $order_by,
            'order_desc' => $order_desc,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayersInTrouble($platform_id, $game_id, $start_time, $end_time, $failed_times, $platform_server_id)
    {
        $method = '/payment/order/players-in-trouble';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'failed_times' => $failed_times,
            'platform_server_id' => $platform_server_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getYuanbaoRank($platform_id, $game_id, $platform_server_id, $start_time = '', $end_time = '', $currency_id, $server_internal_id, $page, $per_page = 30)
    {
        $method = '/payment/order/rank';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'platform_server_id' => $platform_server_id,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'server_internal_id' => $server_internal_id,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getYuanbaoRankforMG($platform_id, $game_id, $platform_server_ids, $start_time = '', $end_time = '', $currency_id, $page, $per_page = 30)
    {
        $method = '/payment/order/mgrank';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'platform_server_ids' => $platform_server_ids,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getAllYuanbaoRank($platform_id, $game_id, $platform_server_id, $start_time = '', $end_time = '', $currency_id, $server_internal_id, $lower_bound = '', $upper_bound = '')
    {
        $method = '/payment/order/all-rank';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'platform_server_id' => $platform_server_id,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'server_internal_id' => $server_internal_id,
            'lower_bound' => $lower_bound,
            'upper_bound' => $upper_bound,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerPaymentFilter($platform_id, $game_id, $platform_server_id, $start_time = '', $end_time = '', $currency_id, $server_internal_id, $lower_bound = '', $upper_bound = ''){
        $method = '/player/payment/filter';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'platform_server_id' => $platform_server_id,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'server_internal_id' => $server_internal_id,
            'lower_bound' => $lower_bound,
            'upper_bound' => $upper_bound,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);        
    }

    public function getUserByPlayerID($platform_id, $player_id, $server_internal_id, $game_id, $tp_code = 'fb')
    {
        $method = '/user/player';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'tp_code' => $tp_code,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getUserByPlayerName($platform_id, $player_name, $server_internal_id, $game_id, $tp_code = 'fb')
    {
        $method = '/user/player';
        $params = array(
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'game_id' => $game_id,
            'player_name' => $player_name,
            'tp_code' => $tp_code,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function queryDelayOrder($server_internal_id, $game_id, $platform_id, $is_check)
    {
        $method = '/poker/payment/queryDelayOrder';
        $params = array(
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'is_check' => $is_check,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
    // //连胜玩家
    // public function steadPlayer($server_internal_id, $game_id, $platform_id, $start_time, $end_time)
    // {
    //  $method = '/poker/user/querySteadWinPlayer';
    //  $params = array(
    //      'server_internal_id' => $server_internal_id,
    //      'platform_id' => $platform_id,
    //      'game_id' => $game_id,
    //      'start_time'=>$start_time,
    //      'end_time'=>$end_time,
    //      'api_key' => $this->api_key,
    //      );
    //  $params['sign'] = $this->makeSignMD5($params);
    //  return $this->getResponse($method, $params);
    // }
    //筹码流向查询by mumu
    public function queryChips($server_internal_id, $game_id, $platform_id, $start_time, $end_time, $player_id)
    {
        $method = '/poker/user/queryChips';
        $params = array(
            'server_internal_id'=> $server_internal_id,
            'game_id'           => $game_id,
            'platform_id'       => $platform_id,
            'start_time'        => intval($start_time),
            'end_time'          => intval($end_time),
            'player_id'         => $player_id,
            'api_key'           => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //牌局统计 by mumu
    public function queryPoker($server_internal_id, $game_id, $platform_id, $start_time, $end_time)
    {
        $method = '/poker/user/queryPoker';
        $params = array(
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );

        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //经济日志查询-奇修 by mumu
    public function queryLogEconomy($server_internal_id, $game_id, $platform_id, $start_time, $end_time)
    {
        $method = '/poker/user/queryLogEconomy';
        $params = array(
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //不同忙注场玩家的玩牌统计
    public function queryPlayCount($server_internal_id, $game_id, $platform_id, $start_time, $end_time)
    {
        $method = '/poker/user/queryPlayCount';
        $params = array(
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }   
    public function getFBStat($fb)
    {
        $method = '/ad/fb';
        $params = array(
            'platform_id' => $fb['platform_id'],
            'game_id' => $fb['game_id'],
            'server_internal_id' => $fb['server_internal_id'],
            'start_time' => $fb['start_time'],
            'end_time' => $fb['end_time'],
            'diff_hours' => $fb['diff_hours'],
            'api_key' => $this->api_key
        );
        if (isset($fb['u1'])) {
            $params['u1'] = $fb['u1'];
        }
        if (isset($fb['u2'])) {
            $params['u2'] = $fb['u2'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function SXDGetFBStat($fb)
    {
        $method = '/ad/sxd/fb';
        $params = array(
            'platform_id' => $fb['platform_id'],
            'game_id' => $fb['game_id'],
            'server_internal_id' => $fb['server_internal_id'],
            'start_time' => $fb['start_time'],
            'end_time' => $fb['end_time'],
            'api_key' => $this->api_key
        );
        if (isset($fb['u1'])) {
            $params['u1'] = $fb['u1'];
        }
        if (isset($fb['u2'])) {
            $params['u2'] = $fb['u2'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getCreatePlayerStat($platform_id, $game_id, $server_internal_id, $start_time, $end_time, $interval)
    {
        $method = '/user/player/stat';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'interval' => $interval,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getFBDisputeOrders($platform_id, $order)
    {
        $method = '/payment/order/dispute';
        $params = array(
            'platform_id' => $platform_id,
            'api_key' => $this->api_key,
        );
        if (isset($order['order_sn'])) {
            $params['order_sn'] = $order['order_sn'];
        }
        if (isset($order['start_time'])) {
            $params['start_time'] = $order['start_time'];
        }
        if (isset($order['end_time'])) {
            $params['end_time'] = $order['end_time'];
        }
        if (isset($order['fb_name'])) {
            $params['fb_name'] = $order['fb_name'];
        }
        if (isset($order['status'])) {
            $params['status'] = $order['status'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getRefundOrders($platform_id, $order_sn, $start_time, $end_time, $pay_type_id)
    {
        $method = '/payment/order/refund';
        $params = array(
            'platform_id' => $platform_id,
            'order_sn' => $order_sn,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'pay_type_id' => $pay_type_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPayTypeStat($platform_id, $pay_type_id, $start_time, $end_time, $game_id, $currency_id)
    {
        $method = '/payment/pay-type';
        $params = array(
            'platform_id' => $platform_id,
            'pay_type_id' => $pay_type_id,
            'game_id' => $game_id,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getChannelOrderStat($platform_id, $order)
    {
        $method = '/user/channel/order';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $order['game_id'],
            'order_start_time' => $order['order_start_time'],
            'order_end_time' => $order['order_end_time'],
            'reg_start_time' => $order['reg_start_time'],
            'reg_end_time' => $order['reg_end_time'],
            'filter' => $order['filter'],
            'currency_id' => $order['currency_id'],
            'api_key' => $this->api_key,
        );
        if (isset($order['is_anonymous'])) {
            $params['is_anonymous'] = $order['is_anonymous'];
        }
        if (isset($order['source'])) {
            $params['source'] = $order['source'];
        }
        if (isset($order['u1'])) {
            $params['u1'] = $order['u1'];
        }
        if (isset($order['u2'])) {
            $params['u2'] = $order['u2'];
        }
        if (isset($order['game_type'])) {
            $params['game_type'] = $order['game_type'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getChannelRetentionStat($platform_id, $user)
    {
        $method = '/user/channel/retention';
        $params = array(
            'platform_id' => $platform_id,
            'server_internal_id' => $user['server_internal_id'],
            'game_id' => $user['game_id'],
            'reg_start_time' => $user['reg_start_time'],
            'reg_end_time' => $user['reg_end_time'],
            'filter' => $user['filter'],
            'api_key' => $this->api_key,
        );
        if (isset($user['is_anonymous'])) {
            $params['is_anonymous'] = $user['is_anonymous'];
        }
        if (isset($user['source'])) {
            $params['source'] = $user['source'];
        }
        if (isset($user['u1'])) {
            $params['u1'] = $user['u1'];
        }
        if (isset($user['u2'])) {
            $params['u2'] = $user['u2'];
        }
        if(isset($user['os_type'])){
            $params['os_type'] = $user['os_type'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getWeeklyStat($platform_id, $game_id, $server_start_time, $server_end_time, $reg_start_time, $reg_end_time, $game_type=1, $filter_u1=1)
    {
        $method = '/user/weekly';
        $param = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_start_time' => $server_start_time,
            'server_end_time' => $server_end_time,
            'reg_start_time' => $reg_start_time,
            'reg_end_time' => $reg_end_time,
            'game_type' => $game_type,
            'filter_u1' => $filter_u1,
            'api_key' => $this->api_key,
        );
        $param['sign'] = $this->makeSignMD5($param);
        return $this->getResponse($method, $param);
    }

    public function getServerRevenueByDay($platform_id, $platform_server_ids, $days_start, $days_end)
    {
        $method = '/payment/server/revenue';
        $params = array(
            'platform_id' => $platform_id,
            'platform_server_ids' => implode(',', $platform_server_ids),
            'days_start' => $days_start,
            'days_end' => $days_end,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerLevelUp($player_id, $player_name, $game_id, $server_internal_id)
    {
        $method = '/player/levelup';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'player_name' => $player_name,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getCreatePlayerInfo($uid = '', $player_id = '', $player_name = '', $game_id, $server_internal_id)
    {
        $method = '/player/playerinfo';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'uid' => $uid,
            'player_id' => $player_id,
            'player_name' => $player_name,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getLogFile($date, $num, $shownum, $find_str)
    {
        $method = '/eb/log';
        $params = array(
            'date' => $date,
            'num' => (int)$num,
            'shownum' => (int)$shownum,
            'find_str' => $find_str,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*获取当前网站用户的支付渠道*/
    public function getPlatformPayMethod($platform_id, $game_id)
    {
        $method = '/platform/pay-method';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlatformCurrency($platform_id, $id = '')
    {
        $method = '/platform/pay-currency';
        $params = array(
            'platform_id' => $platform_id,
            'id' => $id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    /*
     * 德州扑克活跃用户信息
    */
    public function pokerActivateData($platform_id, $game_id, $server_interval_id, $start_time, $end_time, $type)
    {
        $method = '/poker/user/activate';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_interval_id' => $server_interval_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);

    }

    /*
     * 德州扑克 充值统计
    */
    public function getPokerOrderStat($platform_id, $game_id, $currency_id, $start_time, $end_time)
    {
        $method = '/poker/payment/order';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getOldPayUser($platform_id, $game_id, $start_time, $end_time)
    {
        $method = '/poker/payment/oldpays';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerPlayerRetention($platform_id, $game_id, $server_internal_id, $start_time, $end_time, $is_anonymous)
    {
        $method = '/poker/user/activate';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //获取注册用户

    public function getLogDay($platform_id, $game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/user/logday";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getLogDays($platform_id, $game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/user/logdays";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPaypayDays($platform_id, $str)
    {
        $method = "/poker/user/paydays";
        $params = array(
            'platform_id' => $platform_id,
            'str' => $str,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }


    public function getPokerDayActivity($start_time, $end_time)
    {
        $method = "/poker/user/day";
        $params = array(
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerRegNew($platform_id, $game_id, $start_time, $end_time)
    {
        $method = "/poker/user/regnew";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerPlayerEconomyRank($game_id, $type)
    {
        $method = '/poker/economy/rank';
        $params = array(
            'game_id' => $game_id,
            'type' => $type,
            'api_key' => $this->api_key
        );

        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerEconomyStatistics($game_id, $player_id, $type, $start_time, $end_time)
    {
        $method = '/poker/user/economy';
        $params = array(
            'game_id' => $game_id,
            'player_id' => $player_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerPlayerEconomy($game_id, $player_id, $type, $start_time, $end_time, $page, $per_page = 30)
    {
        $method = '/poker/user/detail';
        $params = array(
            'game_id' => $game_id,
            'player_id' => $player_id,
            'type' => $type,
            'page' => $page,
            'per_page' => $per_page,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerCreatePlayerInfo($uid = '', $player_id = '', $player_name = '', $game_id)
    {
        $method = '/poker/user/playerinfo';
        $params = array(
            'game_id' => $game_id,
            'uid' => $uid,
            'player_id' => $player_id,
            'player_name' => $player_name,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);

        return $this->getResponse($method, $params);
    }

    //德州扑克获取全服消费统计
    public function getPokerServerEconomy($game_id, $platform_id, $type, $start_time, $end_time, $player_level, $is_filter_neiwan, $vip_selector)
    {
        $method = '/poker/user/allserver';
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_level' => $player_level,
            'is_filter_neiwan' => $is_filter_neiwan,
            'vip_selector' => $vip_selector,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }


    public function getPokerCashInfo($uid, $platform_id, $player_name, $type1, $start_time, $end_time, $type2)
    {
        $method = "/poker/cash/info";
        $params = array(
            'uid' => $uid,
            'player_name' => $player_name,
            'platform_id' => $platform_id,
            'type1' => $type1,
            'type2' => $type2,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //查看玩家Item
    public function userItemData($game_id, $server_internal_id, $player_id, $start_time, $end_time)
    {
        $method = "/user/item";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getIdByName($platform_id, $game_id, $server_internal_id, $player_name, $player_id)
    {
        $method = "/user/user-info";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_name' => $player_name,
            'player_id' => $player_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerInfo($platform_id, $game_id, $server_internal_id, $player_id)
    {
        $method = "/user/userinfo";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getExpInfo($platform_id, $game_id, $server_internal_id, $player_id, $start_time, $end_time, $type, $item_id, $page, $per_page)
    {
        $method = "/user/user-exp";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'item_id' => $item_id,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getIPInfo($platform_id, $game_id, $server_internal_id, $player_id, $start_time, $end_time)
    {
        $method = "/user/ip-info";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //public function getServerRemain($platform_id,$game_id,$server_internal_id,$start_time,$end_time,$sql){
    public function getServerRemain($platform_id, $game_id, $server_internal_id)
    {
        $method = "/economy/server-remain";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getServerRemainPlayer($platform_id, $game_id, $server_internal_id)
    {
        $method = "/economy/server-remain-player";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function findBossKillerNum($game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = '/player/economy/find-boss-killer-num';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );

        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerWeek($platform_id, $game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/user/week";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerDayData($platform_id, $game_id, $start_time, $end_time)
    {
        $method = "/poker/user/day-data";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerDayPay($platform_id, $game_id, $start_time, $end_time)
    {
        $method = "/poker/user/day-pay";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function serverConsumeData($platform_id, $game_id, $server_internal_id, $start_time, $end_time, $type)
    {
        $method = "/economy/server-consume";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function pokerRoundsData($platform_id, $game_id, $start_time, $end_time, $uid, $rounds_type, $blind_type)
    {
        $method = "/poker/rounds/data";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'uid' => $uid,
            'rounds_type' => $rounds_type,
            'blind_type' => $blind_type,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function pokerSignData($platform_id, $game_id, $start_time, $end_time, $click_id)
    {
        $method = "/poker/round/sign";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'click_id' => $click_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerIdByUID($platform_id, $uid, $game_id ,$server_internal_id = 1)
    {
        $method = "/uid/playerid";
        $params = array(
            'platform_id' => $platform_id,
            'uid' => $uid,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getGamesByUID($platform_id, $game_id, $server_internal_id, $player_id, $start_time, $end_time)
    {
        $method = "/poker/uid/games";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerGameInfo($platform_id, $server_internal_id, $game_id, $start_time, $end_time, $str)
    {
        $method = "/poker/game/info";
        $params = array(
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'str' => $str,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerUser($platform_id, $game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/user/unlog";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getAllUsers($platform_id, $game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/user/log";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerPlayerByUID($platform_id, $game_id, $uid)
    {
        $method = "/poker/user/player_name";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'uid' => $uid,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerPayNums($platform_id, $game_id, $uid)
    {
        $method = "/poker/user/paynums";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'uid' => $uid,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPayNum($platform_id, $str)
    {
        $method = "/poker/user/pay-num";
        $params = array(
            'platform_id' => $platform_id,
            'str' => $str,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getSuccessOrders($platform_id, $game_id, $start_time, $end_time)
    {
        $method = "/economy/recharge/success";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerInfoFromLog($platform_id, $game_id, $server_internal_id, $uid)
    {
        $method = "/user/info/log";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'uid' => $uid,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getAmounts($params)
    {
        $method = "/platform/pay-amount";
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getRankThree($params)
    {
        $method = "/economy/yanwu/three";
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //黑暗之光--第三方--单平台多游戏

    public function getUserByEmailTH($platform_id, $email, $server_internal_id = 0, $game_id)
    {
        $method = '/user/th';
        $params = array(
            'platform_id' => $platform_id,
            'email' => $email,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getUserByUIDTH($platform_id, $uid, $server_internal_id = 0, $game_id)
    {
        $method = '/user/th';
        $params = array(
            'platform_id' => $platform_id,
            'uid' => $uid,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }


    public function getUserByPlayerIDTH($platform_id, $player_id, $server_internal_id, $game_id)
    {
        $method = '/user/player/th';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getUserByPlayerNameTH($platform_id, $player_name, $server_internal_id, $game_id)
    {
        $method = '/user/player/th';
        $params = array(
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'game_id' => $game_id,
            'player_name' => $player_name,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function THGetUserStat($user)
    {
        $method = '/user/th/stat';
        $params = array(
            'game_id' => $user['game_id'],
            'platform_server_id' => $user['platform_server_id'],
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'filter' => $user['filter'],
            'api_key' => $this->api_key
        );
        if (isset($user['source']) && $user['source']) {
            $params['source'] = $user['source'];
        }
        if (isset($user['u1']) && $user['u1']) {
            $params['u1'] = $user['u1'];
        }
        if (isset($user['u2']) && $user['u2']) {
            $params['u2'] = $user['u2'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function THGetFBStat($fb)
    {
        $method = '/ad/th/fb';
        $params = array(
            'platform_id' => $fb['platform_id'],
            'game_id' => $fb['game_id'],
            'server_internal_id' => $fb['server_internal_id'],
            'start_time' => $fb['start_time'],
            'end_time' => $fb['end_time'],
            'api_key' => $this->api_key
        );
        if (isset($fb['u1'])) {
            $params['u1'] = $fb['u1'];
        }
        if (isset($fb['u2'])) {
            $params['u2'] = $fb['u2'];
        }
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getCreatePlayer($uid = '', $player_id = '', $player_name = '', $game_id, $server_internal_id)
    {
        $method = '/player/player-info';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'uid' => $uid,
            'player_id' => $player_id,
            'player_name' => $player_name,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getDragonLog($game_id, $server_internal_id, $player_id, $start_time, $end_time, $type)
    {
        $method = "/dragon/log";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getRechargeUID($platform_id, $game_id, $count, $page, $per_page)
    {
        $method = '/poker/recharge-info';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'money' => $count,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerUserInfo($platform_id, $game_id, $uid, $player_name = '', $player_id = '')
    {
        $method = "/poker/user-info";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'uid' => $uid,
            'player_name' => $player_name,
            'player_id' => $player_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function playerPayData($platform_id, $game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/player/paydata";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerUid($platform_id, $game_id, $sql)
    {
        $method = "/player/info";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'sql' => $sql,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getLoginPlayerId($platform_id, $game_id, $server_internal_id, $ss)
    {
        $method = "/poker/login";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'ss' => $ss,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getCreatePlayer_xs($platform_id, $game_id, $player_name, $player_id = '', $uid = '', $server_internal_id)
    {
        $method = "/user/xs";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'player_name' => $player_name,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'uid' => $uid,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //ip
    public function getIdByName2($platform_id, $game_id, $server_internal_id, $player_name, $player_id)
    {
        $method = "/user/user-ip";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_name' => $player_name,
            'player_id' => $player_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function loginPlayersData($game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/login-times";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function chipsRangeData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $sort, $mid, $page, $per_page, $group_by=0)
    {
        $method = "/poker/chips-range";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_id' => $player_id,
            'sort' => $sort,
            'mid' => $mid,
            'page' => $page,
            'per_page' => $per_page,
            'group_by' => $group_by,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function playerLoginData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $page, $per_page)
    {
        $method = "/poker/player-login";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_id' => $player_id,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function roundsRangeData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $page, $per_page)
    {
        $method = "/poker/rounds-range";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_id' => $player_id,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getGamesData($platform_id, $game_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/games";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getLogLogin($game_id, $server_internal_id, $start, $end)
    {
        $method = "/poker/log-data";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start' => $start,
            'end' => $end,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //德扑退款查询
    public function getPokerRefund($platform_id, $start_time, $end_time, $page, $per_page)
    {
        $method = "/poker/pokeruserinfo";
        $params = array(
            'platform_id' => $platform_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerBackStat($platform_id, $game_id,
                                     $server_internal_id, $start_time, $end_time, $p_time, $pd_time)
    {
        $method = "/poker/back-stat";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'p_time' => $p_time,
            'pd_time' => $pd_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerBackStatOld($platform_id, $game_id,
                                        $server_internal_id, $start_time, $end_time, $p_time, $pd_time)
    {
        $method = "/poker/back-stat-old";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'p_time' => $p_time,
            'pd_time' => $pd_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getFirstPayPlayer($platform_id, $game_id,
                                      $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/first-pay";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getAnonyPlayer($platform_id, $game_id,
                                   $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/anony-player";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getRechargeFailInfoFromSlave($game_id, $platform_id,
                                                 $start_time, $end_time, $from_email, $email_to, $email_subject)
    {
        $method = "/mail/fromSlave";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'from_email' => $from_email,
            'email_to' => $email_to,
            'email_subject' => $email_subject,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
    // public function getPartyMember($game_id, $platform_id, $player_name_str)
    // {
    //  $method = "/party/get-member";
    //  $params = array(
    //      'game_id' => $game_id,
    //      'platform_id' => $platform_id,
    //      'player_name_str' => $player_name_str,
    //      'api_key' => $this->api_key
    //      );
    //  $params['sign'] = $this->makeSignMD5($params);
    //  return $this->getResponse($method, $params);
    // }

    public function getSoldStatics($game_id, $platform_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/shop/soldStatics";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //德扑手动扣筹码历史记录
    public function chipsRecordData($game_id, $server_internal_id, $player_id, $start_time, $end_time, $page, $per_page)
    {
        $method = "/poker/chips_record_poker";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_id' => $player_id,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function chipsRecordData2($game_id, $server_internal_id, $player_name, $start_time, $end_time, $page, $per_page)
    {
        $method = "/user/user_record";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_name' => $player_name,
            'page' => $page,
            'per_page' => $per_page,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //同IP查询
    public function sameIpData($platform_id, $game_id, $server_internal_id, $ip)
    {
        $method = "/user/same_ip";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'ip' => $ip,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPokerDataDailyFromSlave($game_id, $platform_id, $server_internal_id,
                                               $platform_server_id, $from_email, $email_to, $email_subject)
    {
        $method = "/poker/dataDaily";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'platform_server_id' => $platform_server_id,
            'from_email' => $from_email,
            'email_to' => $email_to,
            'email_subject' => $email_subject,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function matchRankData($game_id, $platform_id, $server_internal_id, $player_id, $start_time, $end_time)
    {
        $method = "/poker/matchRank";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        //var_dump($params);die();
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getGMQuestions($server_id, $server_name, $api_server_ip, $api_server_port, $api_dir_id)
    {
        $method = "/gm/questions";
        $params = array(
            'server_id' => $server_id,
            'server_name' => $server_name,
            'api_server_ip' => $api_server_ip,
            'api_server_port' => $api_server_port,
            'api_dir_id' => $api_dir_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    //德扑经济查询
    public function queryEconomy($server_internal_id, $game_id, $platform_id, $start)
    {
        $method = '/poker/user/queryEconomy';
        $params = array(
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start' => $start,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function matchAreaData($game_id, $platform_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/matchArea";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function gameAreaData($game_id, $platform_id, $server_internal_id, $start_time, $end_time)
    {
        $method = "/poker/gameArea";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function importMingGeLog($db_name, $log_file, $log_file_bak)
    {
        $method = "/import/mingge";
        $params = array(
            'db_name' => $db_name,
            'log_file' => $log_file,
            'log_file_bak' => $log_file_bak,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
    public function getPlayerServerInfo($platform_id, $game_id, $server_internal_id,$item_id,$start_time,$end_time)
   {
       $method = "/server/serverinfo";
       $params = array(
           'platform_id' => $platform_id,
           'game_id' => $game_id,
           'server_internal_id' => $server_internal_id,
           'item_id' => $item_id,
           'start_time' => $start_time,
           'end_time' => $end_time,
           'api_key' => $this->api_key
       );
       $params['sign'] = $this->makeSignMD5($params);
       return $this->getResponse($method, $params);
   }
   public function getPlayerLoginName($platform_id, $player_name, $server_internal_id, $game_id,$start_time,$end_time)
    {
        $method = '/player/login/time';
        $params = array(
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'game_id' => $game_id,
            'player_name' => $player_name,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
    public function getPlayerLoginID($platform_id, $player_id, $server_internal_id, $game_id,$start_time,$end_time)
    {
        $method = '/player/login/time';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
    public function yuanbaoRankSearch($platform_id, $game_id, $platform_server_id, $start_time = '', $end_time = '', $currency_id, $server_internal_id)
    {
        $method = '/payment/order/search';
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'platform_server_id' => $platform_server_id,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerName($game_id, $server_internal_id, $player_id)
    {
        $method = '/player/name';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
     public function getMingGeLog($game_id, $server_internal_id, $player_id, $start_time, $end_time, $type)
    {
        $method = "/mingge/log";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
    public function getUserPhone($platform_id,$created_ip,$game_id = 0)
    {
        $method = '/user/phone';
        $params = array(
            'platform_id' => $platform_id,
            'created_ip' => $created_ip,
            'game_id' => $game_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
    /*yysg获取玩家个人-消费统计*/
    public function getYysgPlayerEconomyStatistics($game_id, $server_internal_id, $player_id, $type, $start_time, $end_time, $look_type, $action_type_num)
    {
        $method = '/player/economy/yysg/stat';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'look_type' => $look_type,
            'action_type_num' => $action_type_num,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
     /*
     * yysg获取玩家个人-消费统计
     */
    public function getYysgPlayerEconomy($game_id, $server_internal_id, $player_id, $type, $start_time, $end_time, $look_type, $action_type_num)
    {
        $method = '/player/economy/yysg';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'look_type' => $look_type,
            'action_type_num' => $action_type_num,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function count_giftbag_check($game_id, $server_id, $gift_bag_id, $start_time, $end_time, $platform_id)  //夜夜三国查询礼包销量
    {
        $method = '/yysg/giftbag_num';
        $params = array(
            'game_id' => $game_id,
            'server_id' => $server_id,
            'gift_bag_id' => $gift_bag_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'platform_id' => $platform_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function count_monetary_check($game_id, $server_internal_id, $monetary_type, $start_time, $end_time)  //夜夜三国查询货币消耗
    {
        $method = '/yysg/monetary_num';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'monetary_type' => $monetary_type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getLonelyExpInfo($platform_id, $game_id, $server_internal_id, $player_id, $start_time, $end_time)
    {
        $method = "/user/lonely/exp";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }
    public function playerLogDate($game_id, $player_id, $start_time, $end_time){
        $method = "/yysg/log/search";
        $params = array(
            'game_id'=>$game_id,
            'player_id'=>$player_id,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'api_key'=>$this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function playerLogItemData($game_id, $player_id, $table_id, $start_time, $end_time){
        $method = "/mnsg/log/item";
        $params = array(
            'game_id'=>$game_id,
            'player_id'=>$player_id,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'table_id' => $table_id,
            'api_key'=>$this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function playerlifetime($game_id, $platform_id, $server_id, $check_type, $time_stamp, $server_internal_id){
        $method = "/yysg/lifetime";
        $params = array(
            'game_id'=>$game_id,
            'platform_id'=>$platform_id,
            'server_id'=>$server_id,
            'check_type'=>$check_type,
            'time_stamp'=>$time_stamp,
            'server_internal_id' => $server_internal_id,
            'api_key'=>$this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getlogindeviceinfo($game_id, $platform_id, $uid, $device_id, $baned){
        $method = "/yysg/logindevice";
        $params = array(
            'game_id'=>$game_id,
            'platform_id'=>$platform_id,
            'uid' => $uid,
            'device_id' => $device_id,
            'baned' => $baned,
            'api_key'=>$this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function writeonlinenumtodb($game_id, $server_internal_id, $num){
        $method = "/mobilegame/writeonlinenum";
        $params = array(
            'game_id'=>$game_id,
            'server_internal_id'=>$server_internal_id,
            'num' => $num,
            'api_key'=>$this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);       
    }
    public function playerWjData($game_id, $player_id, $wj_id, $start_time, $end_time)
    {
        $method = "/log/wj/is_eat";
        $params = array(
            'game_id' => $game_id,
            'player_id' => $player_id,
            'wj_id' => $wj_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getMGavgonlinetime($server_internal_id, $game_id, $platform_id, $start_time, $end_time, $lev_low, $lev_up, $limit_pay_user){
        $method = "/mg/avgonlinetime";
        $params = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'lev_low' => $lev_low,
            'lev_up' => $lev_up,
            'limit_pay_user' => $limit_pay_user,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getsignupnum($start_time, $end_time, $game_id, $platform_id, $server_internal_ids = array()){
        $method = "/users/signnum";
        $params = array(
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'platform_id' => $platform_id,
            'server_internal_ids' => $server_internal_ids,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);        
    }

    public function get_game_package($game_id, $platform_id, $package_id){
        $method = "/payment/game_package";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'package_id' => $package_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params); 
    }
/*
 *Google validate Slave...Connection
 */
    public function ggvalidateData($game_id, $platform_id, $package_id)
    { 
        $method = "/ggvalidate/modify";
        $params = array(
                         'game_id' => $game_id,
                         'platform_id' => $platform_id,
                         'package_id' => $package_id,
                         'api_key' => $this->api_key
                       );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getsqlresult($game_id, $platform_id, $sql, $database, $ifdownload=0){ //执行手动输入的sql语句
        $method = "/execute/sql";
        $params = array(
                 'game_id' => $game_id,
                 'platform_id' => $platform_id,
                 'sql' => $sql,
                 'database' => $database,
                 'ifdownload' => $ifdownload,
                 'api_key' => $this->api_key
                );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params,1);
    }

//third_product modify & add
    public function thirdproductData($game_id, $platform_id , $key)
    {
        $method = "/third_product/getdata";
        $params = array(
                        'id' => $key,
                        'game_id' => $game_id,
                        'platform_id' => $platform_id,
                        'api_key' => $this->api_key
                        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }
    public function thirdproductUpdate($game_id, $platform_id , $key , $data)
    {
        $method = "/third_product/update";
        $params = array(
                        'data' => $data,
                        'id' => $key,
                        'game_id' => $game_id,
                        'platform_id' => $platform_id,
                        'api_key' => $this->api_key
                        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function checkYYSGplayer($game_id, $player_ids, $player_names){
        $method = "/check/yysgplayer";
        $params = array(
                'game_id' => $game_id,
                'player_ids' => $player_ids,
                'player_names' => $player_names,
                'api_key' => $this->api_key
                );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params,1);        
    }

//玩家流失
    public function getRegistByTime($game_id, $server_internal_id,$is_pay,$start_time, $end_time, $login_start_time, $login_end_time, $miss_days, $platform_id)
    {
        $method = '/player/outflow';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'is_pay' =>$is_pay,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'login_start_time' => $login_start_time,
            'login_end_time' => $login_end_time,
            'miss_days' => $miss_days,
            'platform_id' => $platform_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getAbnormalDada($game_id, $server_internal_id, $type, $start_time, $end_time, $min_limit)
    {
        $method = '/economy/player/abnormal';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'min_limit' => $min_limit,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getplayerinfolike($type, $id_or_name, $platform_id, $game_id){
        $method = '/player/like';
        $params = array(
            'game_id' => $game_id,
            'type' => $type,
            'id_or_name' => $id_or_name,
            'platform_id' => $platform_id,
            'api_key' => $this->api_key
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getplayerinfolikeserver($type, $id_or_name, $platform_id, $game_id, $server_internal_id){
        $method = '/player/likeserver';
        $params = array(
            'game_id' => $game_id,
            'type' => $type,
            'id_or_name' => $id_or_name,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getPlayerImportance($game_id, $platform_id, $all_playerids){  //根据玩家id获取玩家重要性，包括等级以及充值数
        $method = '/player/importance';
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'all_playerids' => $all_playerids,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params, 1);        
    }

    public function playerEquipmentData($game_id, $player_id, $start_time, $end_time)
    {
        $method = "/log/equipment";
        $params = array(
            'game_id' => $game_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getPokerDailyData($server_internal_id, $game_id, $platform_id, $start_time, $end_time){
        $method = '/joyspade/daily/data';
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);   
    }

    public function playerGetWjData($game_id, $player_id, $start_time, $end_time, $table_id)
    {
        $method = "/log/player/wj";
        $params = array(
            'game_id' => $game_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'table_id' => $table_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getSpendonParts($game_id, $server_internal_id, $start_time, $end_time, $type, $symbol){
        $method = "/economy/parts";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'symbol' => $symbol,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getEachPlayerEconomyChange($game_id, $server_internal_id, $start_time, $end_time, $type, $symbol,  $limit_symbol, $limit_value){
        $method = "/economy/each/player";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'symbol' => $symbol,
            'limit_symbol' => $limit_symbol,
            'limit_value' => $limit_value,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getSpendonShops($game_id, $server_internal_id, $start_time, $end_time, $type, $mid, $symbol){
        $method = "/economy/parts";
        $params = array(
            'mid'   =>  $mid,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'symbol' => $symbol,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getfirstpayinfo($game_id, $start_time, $end_time, $platform_id){
        $method = "/payment/info/firstpay";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);        
    }

    public function getamountinfo($game_id, $start_time, $end_time, $platform_id){
        $method = "/payment/info/amount";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);     
    }

    public function getRemainYuanbao($params){
        $method = "/economy/remainyuanbao";
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);   
    }

    public function pokercreatelibao($game_id, $operator, $created_time, $player_array, $items_string){
        $method = "/poker/writegiftbag";
        $params = array(
            'game_id' => $game_id,
            'operator' => $operator,
            'created_time'  =>  $created_time,
            'player_array'    =>  $player_array,
            'items_string'  =>  $items_string,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);           
    }

    public function getunsendgiftbags($game_id, $giftbag_id=0, $check_start_time=0, $check_end_time=0, $creater=''){
        $method = "/poker/unsendgiftbag";
        $params = array(
            'game_id' => $game_id,
            'giftbag_id' => $giftbag_id,
            'check_start_time' => $check_start_time,
            'check_end_time' => $check_end_time,
            'creater' => $creater,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);             
    }

    public function changePokerGiftbagStatu($game_id, $giftbag_id, $statu){
        $method = "/poker/changegiftbagstatu";
        $params = array(
            'game_id' => $game_id,
            'statu' =>  $statu,
            'giftbag_id' => $giftbag_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);           
    }

    public function getExpenseSum($server_internal_id, $game_id, $platform_id, $start_time, $end_time, $interval){
        $method = "/economy/expensesum";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
            'interval' => $interval, 
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);

    }

    public function getUserNum($game_id, $platform_id, $start_time, $end_time, $interval, $u1='', $u2='',$source='',$is_anonymous=''){
        $method = "/count/usernum";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
            'interval' => $interval, 
            'u1'=> $u1,
            'u2'=> $u2,
            'source'=> $source,
            'is_anonymous'=> $is_anonymous,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getfirstpayanalysis($game_id, $platform_id, $start_time, $end_time){
        $method = "/firstpay/analysis";
        $params = array(
            'game_id' => $game_id,
            'platform_id' =>  $platform_id,
            'start_time'    =>  $start_time,
            'end_time'  =>  $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);            
    }

    public function getUserInfoDevice($user)
    {
        $method = '/user/device-search';
        $params = array(
            'platform_id' => $user['platform_id'],
            'start_time' => $user['start_time'],
            'end_time' => $user['end_time'],
            'interval' => $user['interval'],
            'check_type' => $user['check_type'],
            'game_id' => $user['game_id'],
            'serach_type' => $user['serach_type'],
            'channel_type' => $user['channel_type'],
            'api_key' => $this->api_key,
            'server_internal_id' => $user['server_internal_id'],
        );
        
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getDevicePlayerInfo($start_time,$end_time, $platform_id, $interval, $check_type, $game_id, $server_internal_id){
        $method = "/user/device-player-search";
        $params = array(
            'start_time' => $start_time,
            'end_time' =>  $end_time,
            'platform_id'    =>  $platform_id,
            'interval'  =>  $interval,
            'check_type'  =>  $check_type,
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);  
    }

    public function getActivityAnalysis($game_id, $server_internal_id, $start_time, $end_time, $mids, $type){
        $method = "/activity/analysis";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' =>  $server_internal_id,
            'start_time'    =>  $start_time,
            'end_time'  =>  $end_time,
            'mids'  =>  $mids,
            'type' => $type,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);           
    }

    public function getConsumptionRank($game_id, $platform_id, $interval, $currency_id, $start_time, $end_time, $rank, $platform_server_id ,$server_internal_id){
        $method = "/user/consumption/rank";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'interval' => $interval,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'rank' => $rank,
            'platform_server_id' => $platform_server_id,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params); 
    }

    public function getcountpartnerlog($game_id, $server_internal_id, $wjids, $start_time, $end_time){
        $method = "/partner/log";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'wjids' => $wjids,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params); 
    }

    //统计德扑日活跃付费用户
    public function getPokerPayNum($platform_id, $game_id, $server_internal_id,$start_time, $end_time){
        $method = "/poker/user/paynum";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'platform_id' => $platform_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params); 
    }

    public function getBasicCount($game_id, $platform_id, $interval, $currency_id, $start_time, $end_time){
        $method = "/user/basic/count";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'interval' => $interval,
            'currency_id' => $currency_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params); 
    }

    public function getUidByPlayerInfo($game_id, $platform_id, $server_internal_id, $player_id, $player_name){
        $method = "/getuid/byplayerinfo";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'player_name' => $player_name,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);         
    }

    public function getAllMobliePaymethods($platform_id, $game_id, $mobile_game){ //获取当前游戏的所有支付方法
        $method = "/mobile/getallpaymethods";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'mobile_game' => $mobile_game,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);             
    }

    public function getGameProduct($platform_id, $game_id, $pay_type_id, $method_id, $product_type, $currency_id, $mobile_game){
        $method = "/mobile/getgameproducts";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'pay_type_id' => $pay_type_id,
            'method_id' => $method_id,
            'product_type' => $product_type,
            'currency_id' => $currency_id,
            'mobile_game' => $mobile_game,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);               
    }

    public function getActivityData($platform_id, $game_id, $player_id, $device, $activity_id, $reward_id, $start_time, $end_time){
        $method = "/poker/activity/data";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'player_id' => $player_id,
            'device' => $device,
            'activity_id' => $activity_id,
            'reward_id' => $reward_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);  
    }

    public function getNewerPointInfo($game_id, $start_time, $end_time){
        $method = "/yysg/newer/point";
        $params = array(
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);          
    }

    public function getScoreRankData($type, $start_time, $end_time, $game_id, $server_internal_id){
        $method = "/score/rank/log";
        $params = array(
            'game_id' => $game_id,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'server_internal_id' => $server_internal_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);               
    }

    public function getFormData($game_id, $platform_id, $id, $created_at){
        $method = "/mobilegame/getformdata";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'id' => $id,
            'created_at' => $created_at,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);               
    }

    public function getInformaitionData($game_id, $platform_id){
        $method = "/mobilegame/getinformaitiondata";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getWeeklyChannelStat($platform_id, $game_id, $cre_start_time,
                    $cre_end_time, $channle_order_start_time, $channle_order_end_time){
        $method = "/weekly/channel";
        $params = array(
            'game_id' => $game_id,
            'platform_id' => $platform_id,
            'cre_start_time' => $cre_start_time,
            'cre_end_time' => $cre_end_time,
            'channle_order_start_time' => $channle_order_start_time,
            'channle_order_end_time' => $channle_order_end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);    
    }

    public function writeOnlineIntodb($game_id, $server_internal_id, $all_server_num){
        $method = "/webgame/writeonlinenum";
        $params = array(
            'game_id'=>$game_id,
            'server_internal_id'=>$server_internal_id,
            'all_server_num' => $all_server_num,
            'api_key'=>$this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params, 1);       
    }

    public function create_player_get($game_id, $platform_id, $player_id){
        $method = "/createplayer/getplayer";
        $params = array(
            'game_id'=> $game_id,
            'platform_id' => $platform_id,
            'player_id'=> $player_id,
            'api_key'=> $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);       
    }

    public function getRzzwRewardRecord($platform_id, $game_id, $start_time, $end_time, $record_type, $player_id, $reward_id){
        $method = "/rzzw/rewardrecord";
        $params = array(
            'game_id'=> $game_id,
            'platform_id' => $platform_id,
            'start_time'=> $start_time,
            'end_time' => $end_time,
            'player_id'=> $player_id,
            'record_type' => $record_type,
            'reward_id' => $reward_id,
            'api_key'=> $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params); 
    }

    public function getmnsgsummondata($game_id, $server_internal_id, $single_or_count, $player_id, $summon_type, $start_time, $end_time){
        $method = "/mnsg/logsummon";
        $params = array(
            'game_id'=> $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time'=> $start_time,
            'end_time' => $end_time,
            'player_id'=> $player_id,
            'single_or_count' => $single_or_count,
            'summon_type' => $summon_type,
            'api_key'=> $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);        
    }

    public function filterOrders($filter_data){
        $method = "/filter/orders";
        $filter_data['api_key'] = $this->api_key;
        $filter_data['sign'] = $this->makeSignMD5($filter_data);
        return $this->getResponse($method, $filter_data, 1);           
    }

    public function getDataByDeviceids($game_id, $platform_id, $server_internal_id, $data_type, $device_ids){
        $method = "/getdata/device";
        $params = array(
            'game_id'=> $game_id,
            'platform_id' => $platform_id,
            'server_internal_id' => $server_internal_id,
            'data_type'=> $data_type,
            'device_ids' => $device_ids,
            'api_key'=> $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params, 1);  
    }

    public function getWeeklySetup($platform_id, $game_id, $start_time, $end_time, $filter_u1=1)
    {
        $method = '/setup/weekly';
        $param = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'filter_u1' => $filter_u1,
            'api_key' => $this->api_key,
        );
        $param['sign'] = $this->makeSignMD5($param);
        return $this->getResponse($method, $param);
    }

    public function SignupCreateInfo($game_id, $platform_id, $start_time, $end_time){
        $method = '/signupcreate/info';
        $param = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $param['sign'] = $this->makeSignMD5($param);
        return $this->getResponse($method, $param);
    }

    public function CalculateRetention($data){
        $method = '/calculate/retention';
        $data['api_key'] = $this->api_key;
        $data['sign'] = $this->makeSignMD5($data);
        return $this->getResponse($method, $data);
    }

    public function getPayTrendinfo($game_id, $start_time, $end_time, $platform_id, $interval){
        $method = '/paytrend/info';
        $param = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'interval' => $interval,
            'api_key' => $this->api_key,
        );
        $param['sign'] = $this->makeSignMD5($param);
        return $this->getResponse($method, $param);        
    }

    public function getPokerBankruptcy($game_id, $platform_id, $by_create_time, $start_time, $end_time, $create_start_time, $create_end_time){
        $method = '/poker/bankruptcy';
        $param = array(
            'platform_id' => $platform_id,
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'by_create_time' => $by_create_time,
            'create_start_time' => $create_start_time,
            'create_end_time' => $create_end_time,
            'api_key' => $this->api_key,
        );
        $param['sign'] = $this->makeSignMD5($param);
        return $this->getResponse($method, $param);              
    }

    public function getSinglePlayerPayDollar($pay_start_time, $pay_end_time, $game_id, $player_id, $server_internal_ids, $platform_id, $player_uid=0){
        $method = '/payment/dollar/player';
        $param = array(
            'platform_id' => $platform_id,
            'player_id' => $player_id,
            'server_internal_ids' => $server_internal_ids,
            'game_id' => $game_id,
            'pay_user_id' => $player_uid,
            'start_time' => $pay_start_time,
            'end_time' => $pay_end_time,
            'api_key' => $this->api_key,
        );
        $param['sign'] = $this->makeSignMD5($param);
        return $this->getResponse($method, $param, 1);  //用post方式
    }

    public function getBasicGameInfoQuery($params){
        $method = '/basic/game/info/query';
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params, 1);
    }

    public function getPaymentMethodActivity($data){
        $method = '/payment/method/activity';
        $data['api_key'] = $this->api_key;
        $data['sign'] = $this->makeSignMD5($data);
        return $this->getResponse($method, $data);
    }

    public function getPlayerYuanbaoEconomy($pay_start_time, $pay_end_time, $game_id, $player_id, $server_internal_id){
        $method = '/economy/yuanbao/player';
        $param = array(
            'server_internal_id' => $server_internal_id,
            'player_id' => $player_id,
            'game_id' => $game_id,
            'start_time' => $pay_start_time,
            'end_time' => $pay_end_time,
            'api_key' => $this->api_key,
        );
        $param['sign'] = $this->makeSignMD5($param);
        return $this->getResponse($method, $param, 1);
    }

    public function getWholeServerEconomyChange($game_id, $server_internal_id, $start_time, $end_time, $type, $symbol){
        $method = "/economy/whole/server";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'type' => $type,
            'symbol' => $symbol,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getOpenServerFrontDays($open_server_time, $game_id, $server_internal_id, $days_start, $days_end){
        $method = "/openserver/days/info";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'days_start' => $days_start,
            'days_end' => $days_end,
            'open_server_time' => $open_server_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getMergeGemData($game_id, $start_time, $end_time, $player_id, $server_internal_id){
        $method = "/flsg/mergegem/log";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_id' => $player_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getOperationData($game_id,$operation_id, $server_internal_id){
        $method = "/operation/log";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'operation_id' => $operation_id,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function ItemLogCount($params){
        $method = '/mg/item/count';
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getCreatePlayers($params){
        $method = '/server/create/players';
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function commandTest($params){//测试脚本
        $method = '/command/test';
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getYYSGAward($params){
        $method = '/yysg/award';
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getYYSGAwardUser($params){
        $method = '/yysg/award/user';
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getFormationData($params){
        $method = '/mnsg/formation';
        $params['api_key'] = $this->api_key;
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method, $params);
    }

    public function getguessData($game_id,$server_internal_id,$server_id,$player_id,$start_time,$end_time){
        $method = "/flsg/guess/log";
        $params = array(
            'game_id' => $game_id,
            'server_internal_id' => $server_internal_id,
            'server_id' => $server_id,
            'player_id' => $player_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

    public function getPartnerDel($game_id, $start_time, $end_time, $player_id, $mid){
        $method = '/yysg/player/partnerdel';
        $params = array(
            'game_id' => $game_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'player_id' => $player_id,
            'mid' => $mid,
            'api_key' => $this->api_key,
        );
        $params['sign'] = $this->makeSignMD5($params);
        return $this->getResponse($method,$params);
    }

}