<?php
namespace EastBlue\GameServer\Flsg_nszj;

use \Curl;
use \Response;
use \App;
use \Lang;
use \Log;
use \Auth;
use \Session;

class GameServerApi implements GameServerApiInterface
{

    const DIR_KEY = '.server';

    protected $serverIP = '';

    protected $serverPort = '';

    protected $serverDirID = '';

    protected $response = '';

    public $url = '';

    protected $world_edition_list = array(59, 60, 61, 62, 63);

    public function __construct()
    {
    }

    /* 建立连接 */
    public function connect($server_ip, $server_port, $server_dir_id)
    {
        $this->serverIP = $server_ip;
        $this->serverPort = $server_port;
        $this->serverDirID = $server_dir_id;
        $this->buildUrl();
        return $this;
    }

    public function mnsgconnect($server_ip, $server_port)
    {
        $this->serverIP = $server_ip;
        $this->serverPort = $server_port;
        $this->serverDirID = 'eb/v1';
        $this->mnsgbuildUrl();
        return $this;
    }

    protected function buildUrl()
    {
        $parts = array(
            'scheme' => 'http://',
            'host' => $this->serverIP . ':',
            'port' => $this->serverPort . '/',
            'path' => $this->serverDirID . self::DIR_KEY
        );
        $this->url = implode('', $parts);
    }

    protected function mnsgbuildUrl()
    {
        $parts = array(
                'scheme' => 'http://',
                'host' => $this->serverIP . ':',
                'port' => $this->serverPort . '/',
                'path' => $this->serverDirID
        );
        $this->url = implode('', $parts);
    }

    protected function getParams($mid, $payload = array())
    {
        return array(
            'mid' => $mid,
            'payload' => json_encode((object)$payload)
        );
    }

    protected function getResponse($params)
    {
        //Log::info(var_export($this->url,true));
        //这里打印一条日志，记录所有调用接口的信息
        if(Auth::check()){
            Log::info('GameServerApi--Url:'.$this->url.', Username:'.Auth::user()->username.', game_id:'.Session::get('game_id').', params:'.var_export($params, true));
        }else{
            Log::info('GameServerApi--Url:'.$this->url.', params:'.var_export($params, true));
        }
        $response = Curl::url($this->url)->postFields($params)->post();
        $this->response = $response;
        $body = $this->response->body;


        /////////////////////////改mid后取消注释可打印  post 和 response 的详细信息。
        
        /*if (0xbc6c == $params['mid']) {
             $data_res = array();
             foreach ($response as $k => $v) {
                 $data_res[$k] = $v;
             }
             Log::info("post url:" . $this->url . "-----post params:" . var_export($params, true) . "-----response:" . var_export($data_res, true));
         }*/
         
        /*if (0xbc28 == $params['mid']) {
            $data_res = array();
            foreach ($response as $k => $v) {
                $data_res[$k] = $v;
            }
            Log::info("post url:" . $this->url . "-----post params:" . var_export($params, true) . "-----response:" . var_export($data_res, true));
        }

         if (0xbc18 == $params['mid']) {
             $data_res = array();
             foreach ($response as $k => $v) {
                 $data_res[$k] = $v;
             }
             Log::info("post url:" . $this->url . "-----post params:" . var_export($params, true) . "-----response:" . var_export($data_res, true));
         }*/
        /////////////////////////////////////////////////////////////////////////

        if (isset($body->error_code)) {
            $body->code = $body->error_code;
            $body->error = Lang::get('error.game_server_error');
        }

        return $body;
    }

    public function sendResponse()
    {
        $http_code = $this->response->http_code;
        $body = $this->response->body;

        if (isset($body->error_code)) {
            $body->code = $body->error_code;
            $body->error = Lang::get('error.game_server_error');
            $http_code = 500;
        }
        return Response::json($body, $http_code);
    }

    protected function isJson($string)
    {
    }
    //定时开活动
    public function timingOpen($game_id, $params)
    {
        Log::info(var_export('timing open game_id:'.$game_id,true));
        return $this->getResponse($params);
    }
    /*
     * 用户是否创建游戏人物
     */
    public function isCreatedPlayer($user_id)
    {
        $payload = array(
            'user_id' => (string)$user_id
        );
        $params = $this->getParams(0xbb02, $payload);
        return $this->getResponse($params);
    }

    /*
     * 充值
     */
    public function recharge($order_sn, $uid, $amount, $month_card_type = '')
    {
        if ($month_card_type) {
            $payload = array(
                'recharge_id' => $order_sn,
                'user_id' => (string)$uid,
                'amount' => (int)$amount,
                'month_vip_type' => (int)$month_card_type
            );
        } else {
            $payload = array(
                'recharge_id' => $order_sn,
                'user_id' => (string)$uid,
                'amount' => (int)$amount
            );
        }
        $params = $this->getParams(0xbb03, $payload);
        return $this->getResponse($params);
    }

    /*
     * 获得游戏在线人数
     */
    public function getOnlinePlayersNumber()
    {
        $params = $this->getParams(0xbb05);
        return $this->getResponse($params);
    }

    /*
     * 获得在线游戏玩家列表
     */
    public function getOnlinePlayersList()
    {
        $params = $this->getParams(0xbb06);
        return $this->getResponse($params);
    }

    /*
     * 冻结游戏玩家帐号
     */
    public function freezeAccount($player_id, $days)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'freezed_days' => $days
        );
        $params = $this->getParams(0xbb07, $payload);
        return $this->getResponse($params);
    }

    /*
     * 禁止玩家发言
     */
    public function banChat($player_id, $days)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'banned_to_post_days' => $days
        );
        $params = $this->getParams(0xbb08, $payload);
        return $this->getResponse($params);
    }

    /*
     * 发布公告
     */

    public function announce($days, $interval_type, $position, $interval,
                             $content, $area_id = 0)
    {
        $payload = array(
            'days' => $days,
            'interval_type' => $interval_type,
            'type' => $position,
            'interval' => $interval,
            'content' => $content
        );
        if($area_id != 0)
        $payload['area_id'] = $area_id;
        $params = $this->getParams(0xbb09, $payload);
        return $this->getResponse($params);
    }

    /*
     * 强行设置每日更新
     */
    public function setDailyUpdate($game_id)
    {
        if(1 == $game_id){
            $mid = 0xbc6e;
        }else{
            $mid = 0xbc5e;
        }
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }

    /*
     * 查询最近一次服务器进行每日更新的时间
     */
    public function checkUpdateTime($game_id)
    {
        if(1 == $game_id){
            $mid = 0xbc6f;
        }else{
            $mid = 0xbc5f;
        }
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }

    /*
     * 查询当前版本后端的更服时间
     */
    public function checkVersionUpdateTime($game_id)
    {
        $params = $this->getParams(0xbc5c);
        return $this->getResponse($params);
    }

    public function notice($days, $interval_type, $position, $interval, $start_time, $content, $area_id = 0)
    {
        $payload = array(
            'days' => $days,
            'interval_type' => $interval_type,
            'type' => $position,
            'interval' => $interval,
            'start_time' => $start_time,
            'content' => $content
        );
        if($area_id != 0)
            $payload['area_id'] = $area_id;
        $params = $this->getParams(0xbb09, $payload);
        return $this->getResponse($params);
    }

    /*
     *load公告
     */
    public function loadNotice()
    {
        $payload = array();
        $params = $this->getParams(0x19, $payload);
        return $this->getResponse($params);
    }

    /*
     *删除公告
     */
    public function stopNotice($game_code, $bulletin_id)
    {
        $payload = array(
            'bulletin_id' => $bulletin_id
        );
        //$params = $this->getParams(0xbb22,$payload);
        if ($game_code == 'flsg' || $game_code == 'dld') {
            $params = $this->getParams(0xbb22, $payload);
        } elseif ($game_code == 'nszj') {
            $params = $this->getParams(0xbb20, $payload);
        }
        return $this->getResponse($params);
    }

    public function stopNotice_nv($bulletin_id)
    {
        $payload = array('bulletin_id' => $bulletin_id);
        $params = $this->getParams(0xbb20, $payload);
        return $this->getResponse($params);
    }


    // 激活码类型
    public function createGiftCode($code_type, $num)
    {
        $payload = array(
            'code_type' => $code_type,
            'num' => $num
        );
        $params = $this->getParams(0xbb0a, $payload);
        return $this->getResponse($params);
    }

    // 增加过滤词
    public function addWordFilter($words, $is_delete = false)
    {
        if (!is_array($words)) {
            App::abort(403,
                'Method:addWordFilter. The param $words [' . $words .
                '] must be an array.');
        }
        $payload = array(
            'words' => $words,
            'delete' => $is_delete
        );
        $params = $this->getParams(0xbb0b, $payload);
        return $this->getResponse($params);
    }

    /*
     * 砸金蛋活动
     */
    public function HitGoldenEgg($num)
    {
        $payload = array(
            'num' => $num
        );

        $params = $this->getParams(0xbb0d, $payload);
        return $this->getResponse($params);
    }

    // 获得 GM 问题
    public function getGMQuestions()
    {
        $params = $this->getParams(0xbb0e);
        return $this->getResponse($params);
    }

    /*
     * type: 1 Bug \ 2 投诉 \3 建议 \4 其他
     */
    public function replyGMQuestion($gm_id, $player_id, $type, $msg)
    {
        $payload = array(
            'gm_id' => $gm_id,
            'player_id' => (int)$player_id,
            'type' => $type,
            'message' => $msg
        );

        $params = $this->getParams(0xbb0f, $payload);
        return $this->getResponse($params);
    }

    /*
     * 通过名字查询玩家信息
     */
    public function getPlayerInfoByName($player_name)
    {
        $payload = array(
            'player_name' => $player_name
        );
        $params = $this->getParams(0xbb11, $payload);
        return $this->getResponse($params);
    }

    /*
     * 通过玩家 ID 获得玩家信息
     */
    public function getPlayerInfoByPlayerID($player_id)
    {
        $payload = array(
            'player_id' => (int)$player_id
        );
        $params = $this->getParams(0xbb13, $payload);
        return $this->getResponse($params);
    }

    /*
     * 通过激活码查询激活码使用情况
     */
    public function getGiftCodeStatusByCode($code)
    {
        $payload = array(
            'code' => $code
        );

        $params = $this->getParams(0xbb12, $payload);
        return $this->getResponse($params);
    }

    /*
     * 通过类型查询激活码使用情况
     */
    public function getGiftCodeStatusByCodeType($code_type)
    {
        $payload = array(
            'code_type' => $code_type
        );

        $params = $this->getParams(0xbb12, $payload);
        return $this->getResponse($params);
    }

    /*
     * 根据玩家 id 查询激活码使用情况
     */
    public function getGiftCodeStatusByPlayerID($player_id)
    {
        $payload = array(
            'player_id' => (int)$player_id
        );

        $params = $this->getParams(0xbb12, $payload);
        return $this->getResponse($params);
    }

    /*
     * 发全服礼包
     */
    public function createGiftBagForAllServer($gift_bag_id, $days, $remark)
    {
        $payload = array(
            'gift_bag_id' => $gift_bag_id,
            'days' => $days,
            'remark' => $remark,
        );

        $params = $this->getParams(0xbb14, $payload);
        return $this->getResponse($params);
    }

    public function createGiftBagForAllServer1($gift_bag_id, $days, $remark, $server_id)
    {
        $payload = array(
            'gift_bag_id' => $gift_bag_id,
            'days' => $days,
            'remark' => $remark,
            'server_id' => intval($server_id)
        );

        $params = $this->getParams(0xbb14, $payload);
        return $this->getResponse($params);
    }

    /*
     * 给玩家发礼包 | 单服发送礼包 所有服发送礼包
     */
    public function sendGiftBagToPlayers($gift_bag_id, $players = null)
    {
        if ($players == null) {
            $payload = array(
                'gift_bag_id' => $gift_bag_id
            );
        } else {
            if (!is_array($players)) {
                App::abort(403,
                    'Method:sendGiftBag. The param $players [' . $players .
                    '] must be an array.');
            }
            $payload = array(
                'gift_bag_id' => $gift_bag_id,
                'players' => $players
            );
        }

        $params = $this->getParams(0xbb0c, $payload);
        return $this->getResponse($params);
    }

    public function sendGiftBagToUser($gift_bag_id, $user_id)
    {
        $payload = array(
            'gift_bag_id' => $gift_bag_id,
            'user_id' => (string)$user_id
        );

        $params = $this->getParams(0xbb1e, $payload);
        return $this->getResponse($params);
    }

    /*
     * 增加称号
     */
    public function addTitle($player_id, $title)
    {
        $payload = array(
            'player_id' => $player_id,
            'title' => $title,
            'delete' => false
        );

        $params = $this->getParams(0xbb16, $payload);
        return $this->getResponse($params);
    }

    /*
     * 删除称号
     */
    public function deleteTitle($player_id, $title)
    {
        $payload = array(
            'player_id' => $player_id,
            'title' => $title,
            'delete' => true
        );

        $params = $this->getParams(0xbb16, $payload);
        return $this->getResponse($params);
    }

    /*
     * 修改元宝
     */
    public function updateLevel($level, $player_id)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'lev' => (int)$level
        );
        $params = $this->getParams(0xbc0a, $payload);
        return $this->getResponse($params);
    }

    /*
     * 修改元宝
     */
    public function changeYuanbao($player_id, $delta , $game_code, $consumeDelta = 1)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        if('flsg' == $game_code && $consumeDelta <= 0){
            $payload['consumeDelta'] = $consumeDelta;
        }

        $params = $this->getParams(0xbc04, $payload);

        return $this->getResponse($params);
    }

    /*
     * 修改铜钱
     */
    public function changeTongqian($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc05, $payload);
        return $this->getResponse($params);
    }

    /*
     * 修改阅历
     */
    public function changeYueli($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc07, $payload);
        return $this->getResponse($params);
    }

    /*
     * 修改功勋
     */
    public function changeGongxuan($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc06, $payload);
        return $this->getResponse($params);
    }

    /*
     * 修改体力
     */
    public function changeTili($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc08, $payload);
        return $this->getResponse($params);
    }

    /*
     * 修改 VIP 元宝
     */
    public function changeVipYuanbao($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc12, $payload);
        return $this->getResponse($params);
    }

    /*
     * 修改 经验
     */
    public function changeJingyan($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc13, $payload);
        return $this->getResponse($params);
    }

    /*
     * 修改 天赋点
     */
    public function changeTianfudian($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc52, $payload);
        return $this->getResponse($params);
    }/*
     * 修改 祭天令
     */
    public function changeJitianling($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc51, $payload);
        return $this->getResponse($params);
    }

    /*修改宠物试炼石 宠物原石 宠物技能精华*/
    public function changeChongwu($change_type, $player_id, $delta)
    {
        switch ($change_type) {
            case 9:
                $mid = 0xbc4c;
                break;
            case 10:
                $mid = 0xbc4d;
                break;
            case 11:
                $mid = 0xbc4e;
                break;
        }
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /*
     * 给背包发物品
     */
    public function addItemToBackpack($item_id, $player_id, $delta)
    {
        $payload = array(
            'item_id' => $item_id,
            'player_id' => (int)$player_id,
            'delta' => (int)$delta
        );
        $params = $this->getParams(0xbc02, $payload);
        return $this->getResponse($params);
    }

    /*
     * 兑换活动获得
     */
    public function getExchangePromotion()
    {
        $params = $this->getParams(0x3800);
        return $this->getResponse($params);
    }

    public function addExchangePromotion($open_time, $close_time)
    {
        $payload = array(
            'open_time' => $open_time,
            'close_time' => $close_time
        );

        $params = $this->getParams(0xbb17, $payload);
        return $this->getResponse($params);
    }

    public function closeExchangePromotion()
    {
        $params = $this->getParams(0xbb18);
        return $this->getResponse($params);
    }

    /* 载入活动 */
    public function getPromotion()
    {
        $params = $this->getParams(0x380f);
        return $this->getResponse($params);
    }

    /* 添加活动 */
    public function addPromotion($type, $open_time, $close_time)
    {
        $payload = array(
            'type' => (int)$type,
            'open_time' => $open_time,
            'close_time' => $close_time
        );
        $params = $this->getParams(0xbb19, $payload);
        return $this->getResponse($params);
    }

    /* 关闭活动 */
    public function closePromotion($type)
    {
        $payload = array(
            'type' => (int)$type
        );
        $params = $this->getParams(0xbb1a, $payload);
        return $this->getResponse($params);
    }

    /* 查看假日活动 */
    public function getNSUnifiedPromotion($game_code)
    {
        if ($game_code == 'nszj') {
            $mid = 0xbc3c;
        } else {
            $mid = 0xbc37;
        }
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }

    /* 开启假日互动*/
    public function addNSUnifiedPromotion($is_timing, $game_code, $type, $open_time, $close_time, $ratio)
    {
        $payload = array(
            'type' => (int)$type,
            'open_time' => $open_time,
            'close_time' => $close_time
        );
        if ($game_code == 'nszj') {
            $mid = 0xbc3d;
            if(95 == $type){
                $payload['ratio'] = (int)$ratio;
            }
        } elseif ($game_code == 'flsg' || $game_code == 'dld') {
            $mid = 0xbc38;
        } else {
            return Response::json(array('error' => 'MID'));
        }
        $params = $this->getParams($mid, $payload);
        if(1 == $is_timing){
            return $params;
        }
        return $this->getResponse($params);
    }

    public function addNSUnifiedPromotion2($is_timing, $game_code, $type, $open_time, $close_time, $ratio)
    {
        if ($game_code == 'nszj') {
            $mid = 0xbc3d;
        } else {
            $mid = 0xbc38;
        }
        $payload = array(
            'type' => (int)$type,
            'open_time' => $open_time,
            'close_time' => $close_time,
            'ratio' => (int)$ratio
        );
        $params = $this->getParams($mid, $payload);
        if(1 == $is_timing){
            return $params;
        }
        return $this->getResponse($params);
    }

    /* 关闭假日活动 */
    public function closeNSUnifiedPromotion($game_code, $type)
    {
        if ($game_code == 'nszj') {
            $mid = 0xbc3e;
        } else {
            $mid = 0xbc39;
        }
        $payload = array(
            'type' => $type
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 载入转盘活动信息 */
    public function lookupTurnplate($game_code)
    {
        if ($game_code == 'nszj') {
            $mid = 0xbc37;
        } elseif ($game_code == 'flsg') {
            $mid = 0xbc34;
        } elseif ($game_code == 'dld') {
            $mid = 0xbc34;
        } else {
            return Response::json(array('error' => 'MID'));
        }
        $payload = array();
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 开启转盘活动 */
    public function openTurnplate($is_timing, $game_code, $open_time, $close_time, $label)
    {
        $payload = array(
            'open_time' => $open_time,
            'close_time' => $close_time,
            'label' => $label
        );

        if ($game_code == 'nszj') {
            $mid = 0xbc38;//
        } elseif ($game_code == 'flsg') {
            $mid = 0xbc35;
        } elseif ($game_code == 'dld') {
            $mid = 0xbc35;
        } else {
            return Response::json(array('error' => 'MID'));
        }
        $params = $this->getParams($mid, $payload);
        if(1 == $is_timing){
            return $params;
        }
        return $this->getResponse($params);
    }

    /* 关闭转盘活动 */
    public function closeTurnplate($game_code, $game_id)
    {
        if ($game_code == 'nszj') {
            $mid = 0xbc39;
        } elseif ($game_code == 'flsg') {
            $mid = 0xbc36;
        } elseif ($game_code == 'dld') {
            $mid = 0xbc36;
        } else {
            return Response::json(array('error' => 'MID'));
        }
        $payload = array();
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 载入转盘活动信息 */

    public function lookupActivity($type)
    {
        $payload = array();
        //$mid = 0xbc37;
        if ($type < 7) {
            $mid = 0x380f;
        } else {
            $mid = 0xbc37;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 开启转盘活动 */
    public function openActivity($open_time, $close_time, $type)
    {
        $payload = array(
            'open_time' => $open_time,
            'close_time' => $close_time,
            'type' => $type
        );
        //$mid = 0xbc38;
        if ($type < 7) {
            $mid = 0xbb19;
        } else {
            $mid = 0xbc38;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 关闭转盘活动 */
    public function closeActivity($type)
    {
        $payload = array(
            'type' => $type
        );
        if ($type < 7) {
            $mid = 0xbb1a;
        } else {
            $mid = 0xbc39;
        }

        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 载入开服活动信息 */
    public function lookupOpenServerActivity($activity_id)
    {
        $payload = array(
            'activity_id' => (int)$activity_id
        );
        $mid = 0xbc3b;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 开启开服活动 三国和大乱斗 一样的mid*/
    public function openOpenServerActivity($activity_id)
    {
        $payload = array(
            'activity_id' => (int)$activity_id
        );

        $mid = 0xbc3c;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 关闭开服活动 */
    public function closeOpenServerActivity($activity_id)
    {
        $payload = array(
            'activity_id' => (int)$activity_id
        );
        $mid = 0xbc3d;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /*
     *
     * {"recipient":-1,"head":"test34","body":"hello,world34","attachments":[{"attach_id":1,"award_id":1,"award_itemid":0,"award_value":1},{"attach_id":2,"award_id":2,"award_itemid":0,"award_value":2}]}
     * 发送邮件
     */
    public function sendMail($to, $title, $body, $attachments = array(), $area_id = 0, $need_level = 0)
    {
        $payload = array(
            'recipient' => $to,
            'head' => $title,
            'body' => $body,
            'attachments' => $attachments
        );
        if($area_id != 0){
            $payload['area_id'] = (int)$area_id;
        }
        if($need_level != 0){
            $payload['need_level'] = (int)$need_level;
        }
        $params = $this->getParams(0xbb1b, $payload);
        return $this->getResponse($params);
    }

    public function addZuoQi($player_id, $mount)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'mount' => $mount,
            'delete' => false
        );

        $params = $this->getParams(0xbb1c, $payload);
        return $this->getResponse($params);
    }

    public function deleteZuoQi($player_id, $mount)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'mount' => $mount,
            'delete' => true
        );

        $params = $this->getParams(0xbb1c, $payload);
        return $this->getResponse($params);
    }

    /*
     * 设置天气
     */
    public function setWeather($weather)
    {
        $payload = array(
            'weather' => $weather
        );
        $params = $this->getParams(0xbb1d, $payload);
        return $this->getResponse($params);
    }

    /*
     * 获取战报
     */
    public function getBattleReport($battle_id)
    {
        $payload = array(
            'battle_id' => $battle_id
        );

        $params = $this->getParams(0x13, $payload);
        return $this->getResponse($params);
    }

    /*
     * 连接比赛服
     */
    public function updateGameMatch($type, $host, $port, $is_active)
    {
        $payload = array(
            'tournament_type' => (int)$type,
            'host' => (string)$host,
            'port' => (string)$port,
            'active' => (boolean)$is_active
        );

        $params = $this->getParams(0xbc1a, $payload);
        return $this->getResponse($params);
    }

    /*
     * 开启争霸天下比赛
     */
    public function openGameMatch($type, $start_time)
    {
        $payload = array(
            'tournament_type' => $type,
            'start_time' => $start_time
        );

        $params = $this->getParams(0xbc17, $payload);
        return $this->getResponse($params);
    }

    /*
     * 关闭争霸天下比赛
     */
    public function closeGameMatch($type)
    {
        $payload = array(
            'tournament_type' => $type
        );
//        Log::info('build-param before');
        $params = $this->getParams(0xbc18, $payload);
//        Log::info('build-param after');
        return $this->getResponse($params);
    }

    public function getGameMatch()
    {
        $params = $this->getParams(0xbc19);
        return $this->getResponse($params);
    }

    public function resetAllGameMatch()
    {
        $params = $this->getParams(0xbc1b, $payload = array());
        return $this->getResponse($params);
    }

    /*
     * 查询报名
     */
    public function searchGameMatchOtherServer($type, $game_id, $game_code)
    {
        $payload = array(
            'tournament_type' => $type
        );
        if($game_code == 'flsg'){
            $mid = 0xbc5a;
            if ($game_id == 4) {
                $mid = 0xbc5b;
            }
        }elseif($game_code == 'nszj'){
            $mid = 0xbc64;
        }
       
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    public function searchCrossMatchOtherServer($type, $game_id, $game_code)
    {
        $payload = array(
            'tournament_type' => $type
        );
        $mid = 0xbc1d;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function searchGameMatchOtherServerWL($type, $game_id)
    {
        $payload = array(
            'tournament_type' => $type
        );
        $mid = 0xbc1d;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /*
     * 向比赛服报名
     */
    public function requestGameMatch($type)
    {
        $payload = array(
            'tournament_type' => $type
        );
        if(8 == $type){//女神全服战
            $payload['auto_add'] = true;
        }
        $params = $this->getParams(0xbc1e, $payload);
        return $this->getResponse($params);
    }

    /*
     * 载入比赛状态
     */
    public function loadGameMatchStatus($type)
    {
        $payload = array(
            'tournament_type' => $type
        );
        $params = $this->getParams(0xbc28, $payload);
        return $this->getResponse($params);
    }

    /*
     * ---三国大乱斗--- 查詢當前服跨服信息，返回當前服務器連接哪個跨服服務器
     */
    public function searchMelee($is_allserverconnect = 0, $game_id)//默认为大乱斗，1为全服连接
    {
        if(1 == $is_allserverconnect){
            if(4 == $game_id){
                $mid = 0xbca4;
            }else{
                $mid = 0xbca6;
            }
        }else {
            $mid = 0xbc2b;
        }
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }

    /*
     * ---三国大乱斗--- 更新當前服跨服信息，設置連接哪個服務器
     */
    public function updateMelee($host, $port, $active, $is_allserverconnect = 0, $game_id)
    {
        $payload = array(
            'host' => (string)$host,
            'port' => (string)$port,
            'active' => (boolean)$active
        );
        if(1 == $is_allserverconnect){
            $mid = 0xbca5;
        }else {
            $mid = 0xbc2c;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function closeMelee($host, $port, $active, $is_allserverconnect = 0, $game_id)
    {
        $payload = array(
            'host' => (string)$host,
            'port' => (string)$port,
            'active' => (boolean)$active
        );
        if(1 == $is_allserverconnect){
            $mid = 0xbca5;
        }else {
            $mid = 0xbc2c;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    //女神圣域争霸
    public function updateShengyu($host, $port, $is_all, $active)
    {
        $payload = array(
            'host' => (string)$host,
            'port' => (string)$port,
            'active' => (boolean)$active
        );
        if('0' == $is_all){
            $mid = 0xbc51;
        }elseif ('1' == $is_all) {
            $mid = 0xbc7a;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function shengyuClose($host, $port, $is_all, $active)
    {
        $payload = array(
            'host' => (string)$host,
            'port' => (string)$port,
            'active' => (boolean)$active
        );
        if('0' == $is_all){
            $mid = 0xbc51;
        }elseif ('1' == $is_all) {
            $mid = 0xbc7a;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function searchShengyu($is_all)
    {
        if('0' == $is_all){
            $mid = 0xbc50;
        }elseif ('1' == $is_all) {
            $mid = 0xbc79;
        }
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }

    public function setConsumerAlert($v)
    {
        $payload = array(
            'value' => $v
        );
        $params = $this->getParams(0xbc20, $payload);
        return $this->getResponse($params);
    }

    public function openShop()
    {
        $params = $this->getParams(0xbc26);
        return $this->getResponse($params);
    }

    public function closeShop()
    {
        $params = $this->getParams(0xbc27);
        return $this->getResponse($params);
    }

    public function openShopTimeLimit($open_time, $duration)
    {
        $payload = array(
            'open_time' => $open_time,
            'duration' => $duration
        );
        $params = $this->getParams(0xbc21, $payload);
        return $this->getResponse($params);
    }

    public function closeShopTimeLimit()
    {
        $params = $this->getParams(0xbc22);
        return $this->getResponse($params);
    }

    public function onShopItem($shop_id)
    {
        $payload = array(
            'shop_item_id' => $shop_id
        );
        $params = $this->getParams(0xbc23, $payload);
        return $this->getResponse($params);
    }

    public function offShopItem($shop_id)
    {
        $payload = array(
            'shop_item_id' => $shop_id
        );
        $params = $this->getParams(0xbc24, $payload);
        return $this->getResponse($params);
    }

    public function loadShopStatus()
    {
        $params = $this->getParams(0xbc25);
        return $this->getResponse($params);
    }

    public function resetShangXiangBug($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => $delta
        );
        $params = $this->getParams(0xbc1f, $payload);
        return $this->getResponse($params);
    }

    /*
     * 重置游戏服务器
     */
    public function initGameServer()
    {
        $params = $this->getParams(0xbb15);
        return $this->getResponse($params);
    }

    public function setGameMaster($player_id, $is_game_manager)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'is_game_manager' => $is_game_manager
        );
        $params = $this->getParams(0xbc16, $payload);
        return $this->getResponse($params);
    }

    /*
     * 解散过关斩将 $scene_id指的是队长的player_id
     */
    public function dissolve($scene_id)
    {
        $payload = array(
            'scene_id' => (int)$scene_id
        );
        $params = $this->getParams(0xbc33, $payload);
        return $this->getResponse($params);
    }

    public function setSuperGM($is_super_gm, $player_id)
    {
        $payload = array(
            'is_super_gm' => (boolean)$is_super_gm,
            'player_id' => (int)$player_id
        );
        $params = $this->getParams(0xbc33, $payload);
        return $this->getResponse($params);
    }

    public function setSuperCustomer($is_super_customer, $player_id)
    {
        $payload = array(
            'is_super_customer' => (boolean)$is_super_customer,
            'player_id' => (int)$player_id
        );
        $params = $this->getParams(0xbc34, $payload);
        return $this->getResponse($params);
    }

    public function addGSContact($super_customer_id, $player_id)
    {
        $payload = array(
            'super_customer_id' => (int)$super_customer_id,
            'player_id' => (int)$player_id
        );
        $params = $this->getParams(0xbc35, $payload);
        return $this->getResponse($params);
    }

    public function removeGSContact($super_customer_id, $player_id)
    {
        $payload = array(
            'super_customer_id' => (int)$super_customer_id,
            'player_id' => (int)$player_id
        );
        $params = $this->getParams(0xbc36, $payload);
        return $this->getResponse($params);
    }

    /**
     * 获取聊天好友
     * */
    public function getChattingFriends($player_id, $start_time, $end_time)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'start_time' => (int)$start_time,
            'stop_time' => (int)$end_time,
        );
        $params = $this->getParams(0xbc3f, $payload);
        return $this->getResponse($params);
    }

    /**
     * 获取聊天记录
     * */
    public function getChattingRecords($player_id, $start_time, $end_time, $to_player_id)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'start_time' => (int)$start_time,
            'stop_time' => (int)$end_time,
            'to_player_id' => (int)$to_player_id,
        );
        $params = $this->getParams(0xbc40, $payload);
        return $this->getResponse($params);
    }

    public function changeLingshi($player_id, $num)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => (int)$num
        );
        $params = $this->getParams(0xbc0f, $payload);
        return $this->getResponse($params);
    }

    public function changeQiyundian($player_id, $num)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => (int)$num
        );
        $params = $this->getParams(0xbc11, $payload);
        return $this->getResponse($params);
    }

    public function changeZaoChuanLing($player_id, $num)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => (int)$num
        );
        $params = $this->getParams(0xbc29, $payload);
        return $this->getResponse($params);
    }

    public function changeXinfa($player_id, $num)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => (int)$num
        );
        $params = $this->getParams(0xbc1c, $payload);
        return $this->getResponse($params);
    }

    // 以下好像是QQ平台版本专有的
    public function inviteFriends($user_id)
    {
        $payload = array(
            'user_id' => $user_id
        );
        $params = $this->getParams(0xbb1f, $payload);
        return $this->getResponse($params);
    }

    public function leagueToQQGroup($league_id, $qq_group_id)
    {
        $payload = array(
            'league_id' => $qq_group_id,
            'qq_group_id' => $qq_group_id
        );

        $params = $this->getParams(0xbb20, $payload);
        return $this->getResponse($params);
    }

    public function getQQJiShiTasks($user_id, $cmd, $task_id, $step)
    {
        $payload = array(
            'user_id' => $user_id,
            'cmd' => $cmd,
            'task_id' => $task_id,
            'step' => $step
        );
        $params = $this->getParams(0xbb21, $payload);
        return $this->getResponse($params);
    }

    /* 添加时装 */
    public function addDress($player_id, $dress_id)
    {
        $payload = array(
            'player_id' => $player_id,
            'dress_id' => $dress_id
        );
        $params = $this->getParams(0xbc2c, $payload);
        return $this->getResponse($params);
    }

    /* 移除时装 */
    public function removeDress($player_id, $dress_id)
    {
        $payload = array(
            'player_id' => $player_id,
            'dress_id' => $dress_id
        );
        $params = $this->getParams(0xbc2d, $payload);
        return $this->getResponse($params);
    }

    /* 德州扑克 */
    /*
     * 1 签到 2 七天登录 9发筹码
     */
    public function sendTexasPokerReward($rewards, $reward_type, $uid)
    {
        $payload = array(
            'rewards' => $rewards,
            'reward_type' => $reward_type,
            'user_id' => $uid
        );
        $params = $this->getParams(0xba00, $payload);
        return $this->getResponse($params);
    }

    /* 修改佳人等级 */
    public function changeBeautyLevel($level)
    {
        $payload = array(
            'level' => (int)$level
        );
        $params = $this->getParams(0xbc2e, $payload);
        return $this->getResponse($params);
    }


    /*
     * 关闭仙界活动-三国
    */
    public function closeHeaven($open_time, $end_time, $type)
    {
        $payload = array(
            'open_time' => (int)$open_time,
            'close_time' => (int)$end_time,
            'type' => (int)$type,
        );
        $params = $this->getParams(0xbc38, $payload);
        return $this->getResponse($params);
    }

    public function openHeaven($type)
    {
        $payload = array(
            'type' => intval($type),
        );
        $params = $this->getParams(0xbc39, $payload);
        return $this->getResponse($params);
    }

    public function lookupHeaven()
    {
        $payload = array();
        $params = $this->getParams(0xbc37, $payload);
        return $this->getResponse($params);
    }

    /*
     * 大乱斗--界王
    */
    public function jiewangLookup()
    {
        $params = $this->getParams(0xbc2b);
        return $this->getResponse($params);
    }

    public function jiewangOpen($host, $port, $active)
    {
        $payload = array(
            'host' => (string)$host,
            'port' => (string)$port,
            'active' => (boolean)$active
        );
        $params = $this->getParams(0xbc2c, $payload);
        return $this->getResponse($params);
    }

    public function jiewangClose($host, $port, $active)
    {
        $payload = array(
            'host' => (string)$host,
            'port' => (string)$port,
            'active' => (boolean)$active
        );
        $params = $this->getParams(0xbc2c, $payload);
        return $this->getResponse($params);
    }

    public function getQqFriendData()
    {
        $payload = array();
        $params = $this->getParams(0xfa1e, $payload);
        return $this->getResponse($params);
    }

    public function stopAnnounce($type, $level, $game_code)
    {

        if('nszj' == $game_code){
            if ($type == "true") {
                $payload = array(
                    'active' => true,
                    'level' => intval($level)
                );
            } else {
                $payload = array(
                    'active' => false,
                    'level' => intval($level)
                );
            }
            $mid = 0xbc41;
        }else{
            if ($type == "true") {
                $payload = array(
                    'active' => (int)1,
                    'level' => intval($level)
                );
            } else {
                $payload = array(
                    'active' => (int)0,
                    'level' => intval($level)
                );
            }
            $mid = 0xbc42;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function lookupAnnounce($game_code)
    {
        $payload = array();
        if('nszj' == $game_code){
            $mid = 0xbc42;
        }else{
            $mid = 0xbc43;
        }
        
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function openGmOrder()
    {
        $payload = array();
        $params = $this->getParams(0xbc00, $payload);
        return $this->getResponse($params);
    }

    public function heavenStudio($type)
    {
        $type = $type;
        $payload = array();
        $params = $this->getParams($type, $payload);
        return $this->getResponse($params);
    }

    public function updateNotice($params)
    {
        $payload = array(
            'activity_notice' => strval($params['notice']),
            'activity_notice_link' => strval($params['notice_link']),
            'activity_notice_head' => strval($params['notice_head']),
        );
        $params = $this->getParams(0xbc3e, $payload);
        return $this->getResponse($params);
    }

    public function heavenBattleOperate($type, $game_code)
    {
        if ($type == "open") {
            $mid = 0xbc44;
            if($game_code == 'nszj')
                $mid = 0xbc77;
            $payload = array(
                'active' => true
            );
        } elseif ($type == "close") {
            $mid = 0xbc44;
            if($game_code == 'nszj')
                $mid = 0xbc77;
            $payload = array(
                'active' => false
            );
        } elseif ($type == "look") {
            $mid = 0xbc46;
            if($game_code == 'nszj')
                $mid = 0xbc78;
            $payload = array();
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    //跨服争霸查看
    public function lookUpCrossServer()
    {
        $payload = array();
        $params = $this->getParams(0xbc19, $payload);
        return $this->getResponse($params);
    }

    public function getBattleChampion()
    {
        $payload = array();
        $params = $this->getParams(0xbc48, $payload);
        return $this->getResponse($params);
    }

    public function getBattleChampionMember($league_id)
    {
        $payload = array(
            'league_id' => intval($league_id)
        );
        $params = $this->getParams(0xbc49, $payload);
        return $this->getResponse($params);
    }

    public function playerEscort($player_id, $status, $activity_type, $game_code)
    {
        $payload = array(
            'player_id' => intval($player_id),
            'status' => intval($status),
        );
        if($game_code == 'nszj'){
            if($activity_type == 0){
                $params = $this->getParams(0xbc66, $payload);
            }elseif(1 == $activity_type){
                $params = $this->getParams(0xbc83, $payload);
            }
        }else{
            if($activity_type == 0){
                $params = $this->getParams(0xbc4a, $payload);
            }elseif($activity_type == 1){
                $params = $this->getParams(0xbc53, $payload);
            }
        }
        
        
        return $this->getResponse($params);
    }

    public function firstRechargeOperate($type)
    {
        switch ($type) {
            case 'open':
                $payload = array(
                    'active' => true
                );
                $params = $this->getParams(0xbc48, $payload);
                break;

            case 'close':
                $payload = array(
                    'active' => false
                );
                $params = $this->getParams(0xbc48, $payload);
                break;
            case 'look':
                $payload = array();
                $params = $this->getParams(0xbc47, $payload);
                break;
        }
        return $this->getResponse($params);

    }

    public function partnerOperate($partner, $type)
    {
        if ($type == "open") {
            $payload = array(
                'active' => true,
                'partners' => $partner
            );
            $params = $this->getParams(0xbc43, $payload);
        } elseif ($type == "close") {
            $payload = array(
                'active' => false,
                'partners' => $partner
            );
            $params = $this->getParams(0xbc43, $payload);
        } elseif ($type == "look") {
            $payload = array();
            $params = $this->getParams(0xbc44, $payload);
        }
        return $this->getResponse($params);
    }

    public function oneKeyOperate($player_id)
    {
        $payload = array(
            'player_id' => intval($player_id)
        );
        $params = $this->getParams(0xbc49, $payload);
        return $this->getResponse($params);
    }

    public function itemActivity($open_time, $close_time, $type, $item)
    {
        $payload = array(
            'open_time' => intval($open_time),
            'close_time' => intval($close_time),
            'type' => $type,
            'items' => $item
        );
        $params = $this->getParams(0xbc38, $payload);
        return $this->getResponse($params);
    }

    public function warsLordsSet($game_code,$tournament_type, $counter)
    {
        switch ($game_code) {
            case 'nszj':
                $mid = 0xbc62;
                break;
            case 'flsg':
                $mid = 0xbc4c;
                break;
            default:
                return Response::json(array('result' => 'error game_code'));
        }
        $payload = array(
            'tournament_type' => $tournament_type,
            'counter' => (int)$counter
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function requestWarLords($tournament_type, $auto_add)
    {
        $payload = array(
            'tournament_type' => $tournament_type,
            'auto_add' => (int)$auto_add
        );
        $params = $this->getParams(0xbc1e, $payload);
        return $this->getResponse($params);
    }

    public function beautyGiftNSZJ($open_time, $close_time, $init_times, $dayly_times)
    {
        $type_mid = 0xbb17;
        $payload = array(
            'open_time' => $open_time,
            'close_time' => $close_time,
            'init_times' => (int)$init_times,
            'dayly_times' => (int)$dayly_times
        );
        $params = $this->getParams($type_mid, $payload);
        return $this->getResponse($params);
    }

    public function beautyGiftNSZJClose()
    {
        $type_mid = 0xbb18;
        $params = $this->getParams($type_mid);
        return $this->getResponse($params);
    }

    public function beautyGiftNSZJLook()
    {
        $type_mid = 0x3837;
        $payload = array();
        $params = $this->getParams($type_mid, $payload);
        return $this->getResponse($params);
    }

    public function getPartyMember($party_id, $game_code)
    {
        switch ($game_code) {
            case 'nszj':
                $mid = 0xbc4b;
                break;
            case 'flsg':
                $mid = 0xbc55;
                break;
            case 'dld':
                $mid = 0xbc4b;
                break;
            default:
                return Response::json(array('result' => 'error game_code'));
        }
        $payload = array(
            'league_id' => $party_id
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function updateBosslives($boss_type, $times)
    {
        $mid = 0xbc56;
        switch ($boss_type) {
            case 1:
                $payload = array("boss_id" => 268435440, "times" => $times, "is_xserver" => 0);
                break;
            case 2:
                $payload = array("boss_id" => 268435441, "times" => $times, "is_xserver" => 0);
                break;
            case 3:
                $payload = array("boss_id" => 268435442, "times" => $times, "is_xserver" => 1);
                break;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function checkBosslives($boss_type)
    {
        $mid = 0xbc57;
        switch ($boss_type) {
            case 1:
                $payload = array("boss_id" => 268435440, "is_xserver" => 0);
                break;
            case 2:
                $payload = array("boss_id" => 268435441, "is_xserver" => 0);
                break;
            case 3:
                $payload = array("boss_id" => 268435442, "is_xserver" => 1);
                break;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function requestWorldLords($tournament_type, $player_id, $game_id ,$game_code)
    {
        $payload = array(
            'tournament_type' => $tournament_type,
            'player_id' => (int)$player_id
        );
        if($game_code == 'flsg'){
            $mid = 0xbc59;
            if ($game_id == 4) {
                $mid = 0xbc5a;
            }
        }elseif($game_code == 'nszj'){
            $mid = 0xbc63;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function nsLonely($type)
    {
        $type = $type;
        $payload = array();
        $params = $this->getParams($type, $payload);
        return $this->getResponse($params);
    }

    /* 载入转转活动信息 */
    public function lookupAround($game_code)
    {
        if ('nszj' == $game_code) {
            $mid = 0xbc59;
        }elseif('flsg' == $game_code){
            $mid = 0xbc77;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        $payload = array();
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 开启转转活动 */
    public function openAround($game_code, $open_time, $close_time, $label)
    {
        $payload = array(
            'open_time' => $open_time,
            'close_time' => $close_time,
            'label' => $label
        );

        if ('nszj' == $game_code) {
            $mid = 0xbc5a;
        } elseif('flsg' == $game_code){
            $mid = 0xbc78;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    /* 关闭转转活动 */
    public function closeAround($game_code, $label)
    {
        if ('nszj' == $game_code) {
            $mid = 0xbc5b;
            $payload = array();
        } elseif('flsg' == $game_code){
            $mid = 0xbc79;
            $payload = array('label' => $label);
        }else {
            return Response::json(array('error' => 'MID'));
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /*
     * 修改 戒指经验
     */
    public function changeJiezhi($player_id, $exp, $game_code)
    {
        if('nszj' == $game_code){
            $mid = 0xbc53;
        }else{
            $mid = 0xbc54;
        }
        $payload = array(
            'player_id' => (int)$player_id,
            'exp' => $exp
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    public function setActivityAward($is_timing, $game_code, $type, $award, $game_id, $spring_recharge_goal, $spring_recharge_rebate)
    {
        if ($game_code == 'flsg' && $game_id == 4 ) {
            $mid = 0xbc62;
        }elseif($game_code == 'flsg'){
            $mid = 0xbc5c;
        }elseif ($game_code == 'nszj') {
            $mid = 0xbc5f;
        }
        else {
            return Response::json(array('error' => 'MID'));
        } 
        $payload = array(
            'type' => (int)$type,
            'award' => $award,
            'area_id' => (int)$game_id
        );
        if(34 == $type){
            $payload['spring_recharge_goal'] = (int)$spring_recharge_goal;
            $payload['spring_recharge_rebate'] = (int)$spring_recharge_rebate;
        }
        $params = $this->getParams($mid, $payload);
        if(1 == $is_timing){
            return $params;
        }
        return $this->getResponse($params);
    }
    public function getActivityAward($game_code, $type, $game_id)
    {
        if ($game_code == 'flsg' && $game_id == 4) {
            $mid = 0xbc63;
        }elseif($game_code == 'flsg'){
            $mid = 0xbc5d;
        }elseif ($game_code == 'nszj') {
            $mid = 0xbc60;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        $payload = array(
            'type' => (int)$type,
            'area_id' => (int)$game_id
        );
        //Log::info(var_export($payload,true));
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 限时抢购紧急开启*/
    public function addNSUrgentPromotion($game_code, $label, $game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc61;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if($game_id==4){
            $mid = 0xbc64;
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc61;
        }
        $payload = array(
            'active' => true,
            'label' => $label
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 团购紧急开启*/
    public function addNSUrgentPromotion2($game_code, $label, $game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc66;
            if($game_id==4){
                $mid = 0xbc69;
            }
        }elseif($game_code == 'nszj'){
            $mid = 0xbc6f;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc66;
        }
        
        $payload = array(
            'active' => true,
            'label' => $label
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 限时抢购紧急关闭*/
    public function closeNSUrgentPromotion($game_code, $label, $game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc61;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if($game_id==4){
            $mid = 0xbc64;
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc61;
        }
        $payload = array(
            'active' => false,
            'label' => $label
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 团购紧急关闭*/
    public function closeNSUrgentPromotion2($game_code, $label, $game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc66;
            if($game_id == 4){
                $mid = 0xbc69;
            }
        }elseif($game_code == 'nszj'){
            $mid = 0xbc6f;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc66;
        }
        $payload = array(
            'active' => false,
            'label' => $label
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 限时抢购奖励设置*/
    public function limitBuySetPromotion($game_code, $goods, $game_id, $is_clean)
    { 
        if ($game_code == 'flsg') {
            $mid = 0xbc63;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if($game_id==4){
            $mid = 0xbc66;
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc62;
        }
        $payload = array(
            'goods' => $goods
        );
        if(1 == $is_clean){
            $payload['is_clean'] = true;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 限时抢购奖励移除*/
    public function limitBuyRemovePromotion($game_code, $goods, $game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc64;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if($game_id==4){
            $mid = 0xbc67;
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc63;
        }
        $payload = array(
            'goods' => $goods
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 限时抢购奖励查看*/
    public function getlimitBuyPromotion($game_code, $game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc62;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if($game_id==4){
            $mid = 0xbc65;
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc64;
        }
        $payload = array();
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }
     /* 团购奖励设置*/
    public function groupBuySetPromotion($game_code, $goods, $game_id, $is_clean)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc68;
            if($game_id==4){
                $mid = 0xbc6b;
            }
        }elseif ($game_code == 'nszj') {
            $mid = 0xbc71;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc67;
        }
        
        $payload = array(
            'goods' => $goods
        );
        if(1 == $is_clean){
            $payload['is_clean'] = true;
        }
        $params = $this->getParams($mid, $payload);
        //Log::info(var_export($params,true));
        return $this->getResponse($params);
    }
    /* 团购奖励移除*/
    public function groupBuyRemovePromotion($game_code, $goods,$game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc69;
            if($game_id==4){
                $mid = 0xbc6c;
            }
        }elseif ($game_code == 'nszj') {
            $mid = 0xbc72;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc68;
        }
        $payload = array(
            'goods' => $goods
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
     /* 团购奖励查看*/
    public function getgroupBuyPromotion($game_code,$game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc67;
            if($game_id==4){
                $mid = 0xbc6a;
            }
        }elseif ($game_code == 'nszj') {
            $mid =  0xbc70;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc69;
        }
        
        $payload = array();
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }
     /* 团购设置虚拟人数*/
    public function groupBuyChangePromotion($game_code, $item_id, $delta,$game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc6a;
            if($game_id==4){
                $mid = 0xbc6d;
            }
        }elseif ($game_code == 'nszj') {
            $mid = 0xbc73;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if(in_array($game_id, $this->world_edition_list)){
            $mid = 0xbc6a;
        }
        $payload = array(
            'item_id' => $item_id,
            'delta' => $delta
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
     /* 设置在线奖励*/
    public function onlineAwardSetPromotion($game_code, $awards, $game_id, $is_clean)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc6c;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if($game_id==4){
            $mid = 0xbc6f;
        }
        $payload = array(
            'awards' => $awards
        );
        if(1 == $is_clean){
            $payload['is_clean'] = true;
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 移除特殊在线奖励*/
    public function onlineAwardRemovePromotion($game_code, $awards, $game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc6d;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if($game_id==4){
            $mid = 0xbc70;
        }
        $payload = array(
            'awards' => $awards
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    /* 查看在线奖励*/
    public function getOnlineAwardPromotion($game_code, $game_id)
    {
        if ($game_code == 'flsg') {
            $mid = 0xbc6b;
        }else {
            return Response::json(array('error' => 'MID'));
        }
        if($game_id==4){
            $mid = 0xbc6e;
        }
        $payload = array();
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }

    //热更策划excel
    public function updateexcel($game_code){
        if('flsg' == $game_code){
            $mid = 0xbc71;
        }elseif('nszj' == $game_code){
            $mid = 0xbc7b;
        }
        $payload = array();
        $params = $this->getParams($mid);
        return $this->getResponse($params);
    }

    public function getremainyuanbao($min_yuanbao){
        $mid = 0xbc7c;
        $payload = array(
            'limit' => $min_yuanbao
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

     public function allServerFightSet($list)
    {
        if ($list) {
            $payload = array(
                'list' => $list
            );
        }
        $params = $this->getParams(0xbc87, $payload);
        return $this->getResponse($params);
    }

    public function allServerFightLook(){
        $params = $this->getParams(0xbc86, $payload = array());
        return $this->getResponse($params);
    }

    public function oreFightOpenOrClose($is_open){
        $mid = 0xbc84;
        $payload = array(
            'active' => $is_open
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function oreFightLook(){
        $params = $this->getParams(0xbc85, $payload = array());
        return $this->getResponse($params);
    }

    public function getvipplayer($min_vip_level,$game_code){
        if('flsg' == $game_code){
            $mid = 0xbca0;
            $payload = array(
                'vip' => $min_vip_level
            );
        }else{
            $mid = 0xbc88;
            $payload = array(
                'min_vip_level' => $min_vip_level
            );
        }
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function allServerAlertIntegral($player_id, $server_id, $operator_id, $score)
    {
        
        $payload = array(
            'player_id' => $player_id,
            'server_id' => $server_id,
            'operator_id' => $operator_id,
            'score' => $score
        );
       
        $params = $this->getParams(0xbc89, $payload);
        return $this->getResponse($params);
    }

    public function setMount($player_id, $mount, $is_mount)
    {
        
        $payload = array(
            'player_id' => (int)$player_id,
            'mount' => (int)$mount
        );
        if(1 == $is_mount){
            $payload['delete'] = true;
        }

        $params = $this->getParams(0xbb1c, $payload);
        return $this->getResponse($params);
    }

    public function sesetLeagueBoss($league_id)
    {
        
        $payload = array(
            'league_id' => $league_id,
        );

        $params = $this->getParams(0xbca1, $payload);
        return $this->getResponse($params);
    }
    //修改内力
    public function changePower($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => (int)$delta
        );
        $params = $this->getParams(0xcc0d, $payload);
        return $this->getResponse($params);
    }
    //修改累积登录次数
    public function sesetAccumulateLoginTime($player_id)
    {
        
        $payload = array(
            'player_id' => $player_id,
            'type' => 38,
            'times' => 1,
        );
        $params = $this->getParams(0xbca2, $payload);
        return $this->getResponse($params);
    }
    //设置三国储值返利
    public function setProportion($proportion)
    {
        
        $payload = array(
            'value' => $proportion,
        );
        $params = $this->getParams(0xbca3, $payload);
        return $this->getResponse($params);
    }
    //风流三国关闭GM命令
    public function closeGmOrder()
    {
        
        $payload = array();
        $params = $this->getParams(0xbc01, $payload);
        return $this->getResponse($params);
    }
    //加载三国比赛信息
    public function getMatchInfo($tournament_type)
    {
        
        $payload = array(
            'tournament_type' => $tournament_type,
        );
        $params = $this->getParams(0xbca4, $payload);
        return $this->getResponse($params);
    }
    //风流三国修改战魂
    public function changeBattleSpirits($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => (int)$delta
        );
        $params = $this->getParams(0xbc5f, $payload);
        return $this->getResponse($params);
    }
    //女神修改星宿碎片
    public function changeStartFragment($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => (int)$delta
        );
        $params = $this->getParams(0xbc15, $payload);
        return $this->getResponse($params);
    }
    //风流三国设置运营排行榜奖励
    public function setRankAward($type, $award, $is_clean)
    {
        $payload = array(
            'type'      => (int)$type,
            'awards'     => $award,
            'is_clean'  => $is_clean,
        );
        $params = $this->getParams(0xbcb1, $payload);
        return $this->getResponse($params);
    }

    public function setRankAwardLook($type)
    {
        $payload = array(
            'type'      => (int)$type,
        );
        $params = $this->getParams(0xbcb2, $payload);
        return $this->getResponse($params);
    }

    public function testmid($mid, $data)
    {
        $params = $this->getParams($mid, $data);
        return $this->getResponse($params);
    }

    //风流三国元神精魄
    public function changeJingPo($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'delta' => (int)$delta
        );
        $params = $this->getParams(0xcf4f, $payload);
        return $this->getResponse($params);
    }
    //风流三国修改红包次数
    public function replacementRedPacket($player_id)
    {
        $payload = array(
            'player_id' => (int)$player_id,
        );
        $params = $this->getParams(0xbc74, $payload);
        return $this->getResponse($params);
    }
    //页游游戏通告新手福利设置
    public function welfareAnnounce($info, $release, $game_code)
    {
        switch ($game_code) {
            case 'flsg':
                $mid = 0xbb25;
                break;
            case 'nszj':
                $mid = 0xbc96;
                break;
            default:
                $mid = 0xbb25;
                break;
        }
        $payload = array(
            'info' => $info,
            'release' => $release,
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    //页游游戏通告新手福利查看
    public function welfareAnnounceLook($game_code)
    {
        switch ($game_code) {
            case 'flsg':
                $mid = 0xbb24;
                break;
            case 'nszj':
                $mid = 0xbc95;
                break;
            default:
                $mid = 0xbb24;
                break;
        }
        $payload = array();
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    //风流三国查看玩家布阵信息
    public function getPlayerEmbattle($player_id)
    {
        $payload = array(
            'player_id' => (int)$player_id,
        );
        $params = $this->getParams(0xbca7, $payload);
        return $this->getResponse($params);
    }
    //女神设置最强公会竞技王活动
    public function setGuildAward($is_timing, $game_code, $type, $award)
    {
        $payload = array(
            'type' => (int)$type,
            'award' => $award,
        );
        $params = $this->getParams(0xbc93, $payload);
        return $this->getResponse($params);
    }
    //女神设置最强公会竞技王活动标题
    public function setGuildAwardTitle($is_timing, $game_code, $type, $misc)
    {
        $payload = array(
            'type' => (int)$type,
            'misc' => $misc,
        );
        $params = $this->getParams(0xbc94, $payload);
        return $this->getResponse($params);
    }
    //风流三国全名pk开启
    public function openPeoplePK()
    {
        $payload = array();
        $params = $this->getParams(0xdf10, $payload);
        return $this->getResponse($params);
    }
    //风流三国全名pk关闭
    public function closePeoplePK()
    {
        $payload = array();
        $params = $this->getParams(0xdf11, $payload);
        return $this->getResponse($params);
    }
    //风流三国全名pk初始化玩家布阵
    public function initPeoplePK($player_id)
    {
        $payload = array(
            'player_id' => (int)$player_id,
        );
        $params = $this->getParams(0xdf26, $payload);
        return $this->getResponse($params);
    }
    //风流三国设置/修改全服等级
    public function aserverLevel($aserver_level)
    {
        $payload = array(
            'aserver_level' => (int)$aserver_level,
        );
        $params = $this->getParams(0xbb28, $payload);
        return $this->getResponse($params);
    }
    //风流三国查看全服等级
    public function aserverLevelLook()
    {
        $payload = array();
        $params = $this->getParams(0xbb27, $payload);
        return $this->getResponse($params);
    }
    //风流三国查询演武场前100玩家
    public function getYWRank()
    {
        $payload = array();
        $params = $this->getParams(0xbc72, $payload);
        return $this->getResponse($params);
    }
    //修改风流三国技能书残章
    public function changeJiNengShu($player_id, $delta)
    {
        $payload = array(
            'player_id' => (int)$player_id,
            'num' => (int)$delta
        );
        $params = $this->getParams(0xbc5e, $payload);
        return $this->getResponse($params);
    }
       
}