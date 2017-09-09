<?php
namespace EastBlue\GameServer\YYSG;
use \Curl;
use \Response;
use \App;
use \Lang;
use \Log;
use \Auth;
use \Session;
use \Config;

class YYSGGameServerApi implements YYSGGameServerApiInterface {

    protected $serverIP = '';

    protected $serverPort = '';

    protected $serverDirID = '';

    protected $response = '';

    protected $game2lang = array();

    public $url = '';

    public function __construct()
    {
        $all_yysg_games = Config::get('game_config.yysggameids');
        foreach ($all_yysg_games as $yysg_game_id) {
            $this->game2lang[$yysg_game_id] = Config::get('game_config.'.$yysg_game_id.'.language');
        }
    }
    /* 建立连接 */
    public function connect($server_ip, $server_port, $server_dir_id = 'eb/v1') //夜夜三国实现这个接口时不需要第三个参数
    {
        $this->serverIP = $server_ip;
        $this->serverPort = $server_port;
        $this->serverDirID = 'eb/v1';
        $this->buildUrl();
        return $this;
    }

    protected function buildUrl()
    {
        $parts = array(
                'scheme' => 'http://',
                'host' => $this->serverIP . ':',
                'port' => $this->serverPort . '/',
                'path' => $this->serverDirID
        );
        $this->url = implode('', $parts);
    }

    protected function getResponse($params)
    {
       /* Log::info(var_export($this->url,true));
        Log::info(var_export($params,true));*/
        //这里打印一条日志，记录所有调用接口的信息
        if(Auth::check()){
            Log::info('YYSGGameServerApi--Url:'.$this->url.', Username:'.Auth::user()->username.',game_id:'.Session::get('game_id').',params:'.var_export($params, true));
        }else{
            Log::info('YYSGGameServerApi--Url:'.$this->url.', params:'.var_export($params, true));
        }
        $response = Curl::url($this->url)->postFields($params)->post(); //curl post
        //Log::info(var_export($response,true));
        $this->response = $response;
        $body = $this->response->body;
        if (isset($body->error_code))
        {
            $body->code = $body->error_code;
            $body->error = Lang::get('error.game_server_error');
        }
        
        ////////////////////////////////////////////////////////////
        /*$params = json_decode($params['payload'], true);
        if (0x7003 == $params['mid']) {
               $data_res = array();
               foreach ($response as $k => $v) {
                   $data_res[$k] = $v;
               }
               Log::info("post url:" . $this->url . "-----post params:" . var_export($params, true) . "-----response:" . var_export($data_res, true));
        }*/
       /*if (0x7002 == $params['mid']) {
           $data_res = array();
           foreach ($response as $k => $v) {
               $data_res[$k] = $v;
           }
           Log::info("post url:" . $this->url . "-----post params:" . var_export($params, true) . "-----response:" . var_export($data_res, true));
       }*/
        /////////////////////////////////////////////////////////////////////////

        return $body;
    }

    public function sendResponse()
    {
        $http_code = $this->response->http_code;
        $body = $this->response->body;

        if (isset($body->error_code))
        {
            $body->code = $body->error_code;
            $body->error = Lang::get('error.game_server_error');
            $http_code = 500;
        }
        return Response::json($body, $http_code);
    }

    protected function isJson($string)
    {
    }



    // 获得 GM 问题
    public function getGMQuestions($platform_id)
    {
        $payload = array(
            'mid' => 0x700a,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        //$params = $this->getParams(0x7007, $payload);
        return $this->getResponse($params);
    }

    /*
     * type: 1 Bug \ 2 投诉 \3 建议 \4 其他
     */
    public function replyGMQuestion($gm_id, $msg, $platform_id)
    {
        $payload = array(
            'mid' => 0x7009,
            'id' => $gm_id,
            'content' => $msg,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        //$params = $this->getParams(0x7007, $payload);
        return $this->getResponse($params);
    }

    public function getPlayerInfo($player_name, $platform_id)
    {
        $payload = array(
            'mid' => 0x700e,
            'player_name' => $player_name,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        //$params = $this->getParams(0x7007, $payload);
        return $this->getResponse($params);
    }


    /*
     * 给玩家发礼包 | 单服发送礼包 所有服发送礼包
     */
    public function sendGiftBagToPlayers($gift_bag_id, $players = null, $platform_id, $giftbag_num)
    {
        $payload = array(
            'mid' => 0x7006,
            'player_names' => $players,
            'giftbag_id' => $gift_bag_id,
            'platform_id' => $platform_id,
            'times' => $giftbag_num,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        //$params = $this->getParams(0x7007, $payload);
        return $this->getResponse($params);
    }
     public function sendGiftBagToPlayersId($gift_bag_id, $players = null, $platform_id, $giftbag_num)
    {
        $payload = array(
            'mid' => 0x7006,
            'player_ids' => $players,
            'giftbag_id' => $gift_bag_id,
            'platform_id' => $platform_id,
            'times' => $giftbag_num,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        //$params = $this->getParams(0x7007, $payload);
        return $this->getResponse($params);
    }

    //設置GM
    public function setGameMaster($player_name, $player_id, $is_gm, $platform_id, $game_code)
    {
        $payload = array(
                'mid' => 0x7007,
                'is_gm' => $is_gm,
                'platform_id' => $platform_id,
        );
        if($game_code == 'yysg'){
            $payload['player_name'] = $player_name;
        }elseif($game_code == 'mnsg'){
            $payload['player_id'] = $player_id;
        }
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        //$params = $this->getParams(0x7007, $payload);
        return $this->getResponse($params);
    }

    public function closeAccountName($player_names, $is_banned, $platform_id)
    {
         $payload = array(
            'mid' => 0x7011,
            'is_banned' => $is_banned,
            'player_names' => $player_names,
            'platform_id' => $platform_id,
         );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
     public function closeAccountId($player_ids, $is_banned, $platform_id, $game_code)
    {
        if('mnsg' == $game_code){
             $payload = array(
                'mid' => 0x7014,
                'is_banned' => $is_banned,
                'player_ids' => $player_ids,
                'platform_id' => $platform_id,
            );
        }elseif('yysg' == $game_code){
              $payload = array(
                'mid' => 0x7011,
                'is_banned' => $is_banned,
                'player_ids' => $player_ids,
                'platform_id' => $platform_id,
            );
         }
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function getGMTalkPlayers($platform_id)
    {

        $payload = array(
            'mid' => 0x700a,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function getGMMessages($player_id, $page_num, $platform_id)
    {

        $payload = array(
            'mid' => 0x700f,
            'player_id' => $player_id,
            'page_num' => $page_num,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function replyGMTalk($player_id, $msg, $platform_id)
    {

        $payload = array(
            'mid' => 0x7009,
            'player_id' => $player_id,
            'msg' => $msg,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
    public function getYYSGPlayerInfo($player_name, $platform_id)
    {

        $payload = array(
            'mid' => 0x700e,
            'player_name' => $player_name,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function getMNSGPlayerInfo($player_id, $platform_id){
        $payload = array(
            'mid' => 0x700e,
            'player_id' => $player_id,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function bannedTalk($player_names, $player_ids, $is_banned, $platform_id, $game_code)
    {
        if('mnsg' == $game_code){
            $payload = array(
                'mid' => 0x7013,
                'player_ids' => $player_ids,
                'is_banned' => $is_banned,
                'platform_id' => $platform_id,
            );            
        }elseif('yysg' == $game_code){
            if(($player_ids[0])){
                $payload = array(
                    'mid' => 0x7010,
                    'player_ids' => $player_ids,
                    'is_banned' => $is_banned,
                    'platform_id' => $platform_id,
                );
            }else{
                $payload = array(
                    'mid' => 0x7010,
                    'player_names' => $player_names,
                    'is_banned' => $is_banned,
                    'platform_id' => $platform_id,
                );
            }
        }
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function getyysgcomments($current_page, $num_per_page, $show_delete, $is_like, $game_id, $player_id='', $table_id='', $platform_id){  //获取夜夜三国武将评论
        $payload = array(
            'mid' => 0x7014,
            'page' => $current_page,
            'num' => $num_per_page,
            'is_delete' => $show_delete,
            'is_like' => $is_like,
            'lang' => $this->game2lang[$game_id],
            'player_id' => $player_id,
            'table_id' => $table_id,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function changecommentlikesnum($comment_id, $likes_num, $platform_id){     //修改夜夜三国武将评论赞数
        $payload = array(
            'mid' => 0x7015,
            'id' => $comment_id,
            'like' => $likes_num,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function deletecomment($comment_id, $is_delete, $platform_id){ //删除或撤销删除夜夜三国武将评论
        $payload = array(
            'mid' => 0x7016,
            'id' => $comment_id,
            'is_delete' => $is_delete,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
    //萌娘和夜夜三国发公告
    public function announceSend($content, $platform_id, $game_id, $game_code)
    {
        if('mnsg' == $game_code){
            $language = 'zh_TW';
        }else{
            $language = $this->game2lang[$game_id];
        }
        $payload = array(
                'mid' => 0x7008,
                'msg' => $content,
                'platform_id' => $platform_id,
                'lang' => $language,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
    //夜夜开启活动
    public function openActivity($table_ids, $start_time, $end_time, $platform_id, $game_id, $is_lang)
    {
        $payload = array(
                'mid' => 0x7000,
                'table_ids' => $table_ids,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'platform_id' => $platform_id,
        );
        if(1 == $is_lang){
            $language = $this->game2lang[$game_id];
            $payload['lang'] = $language;
        }
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
    //夜夜关闭活动
    public function closeActivity($table_ids, $platform_id, $game_id, $is_lang)
    {
        $payload = array(
                'mid' => 0x7001,
                'table_ids' => $table_ids,
                'platform_id' => $platform_id,
        );
        if(1 == $is_lang){
            $language = $this->game2lang[$game_id];
            $payload['lang'] = $language;
        }
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }


     /*
     * 修改元宝
     */
    public function changeYuanbao($player_id, $delta, $platform_id)
    {
        $payload = array(
            'mid' => 0x7013,
            'player_id' => (int)$player_id,
            'delta' => $delta,
            'platform_id' => $platform_id,
        );

        $params = array(
            'payload' => json_encode((object) $payload)
        );

        return $this->getResponse($params);
    }

    /*
     * 修改铜钱--只能扣除
     */
    public function changeTongqian($player_id, $delta, $platform_id)
    {
        $payload = array(
            'mid' => 0x7019,
            'player_id' => (int)$player_id,
            'delta' => (int)$delta,
            'platform_id' => $platform_id,
        );
        
        $params = array(
            'payload' => json_encode((object) $payload)
        );

        return $this->getResponse($params);
    }

    /*
     * 修改体力--只能扣除
     */    
    public function changeTili($player_id, $delta, $platform_id)
    {
        $payload = array(
            'mid' => 0x7020,
            'player_id' => (int)$player_id,
            'delta' => (int)$delta,
            'platform_id' => $platform_id,
        );

        $params = array(
            'payload' => json_encode((object) $payload)
        );

        return $this->getResponse($params);
    }
     /*
     * 发布活动公告
     */
    public function releaseAnnounce($title, $type, $url, $banner, $start_time, $end_time, $is_open, $game_id, $platform_id, $is_show, $activity_id)
    {
        $payload = array(
            'mid' => 0x7002,
            'title' => $title,
            'type' => $type,
            'url' => $url,
            'banner' => $banner,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_open' => (int)$is_open,
            'lang' => $this->game2lang[$game_id],
            'platform_id' => $platform_id,
            'is_begin' => (int)$is_show,
            'activity_id' => (int)$activity_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
     public function ActivityAnnounceLook($type, $limit, $game_id, $platform_id)
    {
        $payload = array(
            'mid' => 0x700c,
            'type' => $type,
            'limit' => $limit,
            'lang' => $this->game2lang[$game_id],
            'platform_id' => $platform_id,
        );

        $params = array(
            'payload' => json_encode((object) $payload)
        );

        return $this->getResponse($params);
    }
    public function updateAnnounce($id, $title, $type, $url, $banner, $start_time, $end_time, $is_open, $platform_id, $is_show)
    {
        $payload = array(
            'mid' => 0x7003,
            'id' => $id,
            'title' => $title,
            'type' => $type,
            'url' => $url,
            'banner' => $banner,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_open' => (int)$is_open,
            'platform_id' => $platform_id,
            'is_begin' => (int)$is_show,
        );

        $params = array(
            'payload' => json_encode((object) $payload)
        );

        return $this->getResponse($params);
    }
     public function mnsgsendMail($to, $title, $body, $attachments = array())
    {
        $payload = array(
            'mid' => 0x7010,
            'from' => 'GM',
            'to_player_id' => $to,
            'title' => $title,
            'content' => $body,
            'attach' => $attachments
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function mnsgsendMailServer($server_internal_id, $title, $body, $attachments = array())
    {
        $payload = array(
            'mid' => 0x7011,
            'from' => 'GM',
            'regions' => $server_internal_id,
            'title' => $title,
            'content' => $body,
            'attach' => $attachments
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
    //設置新手指导
    public function setBeginnerMaster($player_info, $is_true, $platform_id, $game_code)
    {
        if('yysg' == $game_code){   //相同功能的参数以及接口不同，为了控制器中的方法统一，在这里作区分
            $mid = 0x7018;
            $player_key = 'player_name';
            $sign_key = 'is_newer_guide';
        }elseif('mnsg' == $game_code){
            $mid = 0x7007;
            $player_key = 'player_id';
            $sign_key = 'is_gm';
            if($is_true){
                $is_true = 2;
            }
        }
        $payload = array(
                'mid' => $mid,
                $player_key => $player_info,
                $sign_key => $is_true,
                'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        //$params = $this->getParams(0x7007, $payload);
        return $this->getResponse($params);
    }
    //查询现有新手指导
    public function getBeginnerMaster($platform_id)
    {
        $payload = array(
            'mid' => 0x7027,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function getonlinenum($server_internal_id, $platform_id, $game_code){
        if('mnsg' == $game_code){
            $payload = array(
                'mid' => 0x7015,
                'region' => (int)$server_internal_id,
                'platform_id' => $platform_id,
            );
        }elseif('yysg' == $game_code){
            return;
        }
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);       
    }
    //萌娘三国增加VIP经验或者补储元宝，与普通方式相比补储元宝会增加VIP经验
    public function mnsgrestore($platform_id, $player_id, $delta, $increase_type){
        if('1' == $increase_type){
            $mid = 0x7018;
        }elseif('2' == $increase_type){
            $mid = 0x7016;
        }
        $payload = array(
                'mid' => $mid,
                'player_id' => (int)$player_id,
                'delta' => (int)$delta,
                'platform_id' => (int)$platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
    //萌娘三国开启或关闭五星好评活动
    public function switch_fivestars($server_internal_id, $switch_type, $platform_id){
        $payload = array(
                'mid' => 0x7017,
                'region' => $server_internal_id,
                'is_open' => $switch_type,
                'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);        
    }

    public function mnsgopenActivity($table_ids, $start_time, $end_time, $platform_id, $server_ids){
        $payload = array(
                'mid' => 0x7000,
                'regions' => $server_ids,
                'ids' => $table_ids,
                'start_time' => $start_time,
                'end_time' => $end_time,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);           
    }

    public function mnsgcloseActivity($table_ids, $platform_id, $server_ids){
        $payload = array(
                'mid' => 0x7001,
                'regions' => $server_ids,
                'ids' => $table_ids
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);           
    }

    public function mnsgcheckActivity($server_id){
        $payload = array(
                'mid' => 0x7024,
                'region' => $server_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);           
    }

    public function mnsgOpenServer($server_name, $server_internal_id){
        $payload = array(
                'mid' => 0x700b,
                'name' =>   $server_name,
                'id' => $server_internal_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);           
    }

    public function switchShowServer($is_hide, $server_internal_id){
        $payload = array(
                'mid' => 0x7023,
                'id' => $server_internal_id,
                'is_hide' => $is_hide,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);           
    }

    public function mnsgeditplayereconomy($player_id, $mana, $energy, $top_coin, $arena_coin, $march_coin, $crystal, $guild_coin, $region_coin){
        $payload = array(
                'mid' => 0x701b,
                'player_id' => $player_id,
                'mana' => $mana,
                'energy' => $energy,
                'top_coin' => $top_coin,
                'arena_coin' => $arena_coin,
                'march_coin' => $march_coin,
                'crystal' => $crystal,
                'guild_coin' => $guild_coin,
                'region_coin' => $region_coin,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);         
    }

    public function mnsgrepairplayershop($player_ids){
        $payload = array(
                'mid' => 0x7021,
                'player_ids' => $player_ids,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);          
    }

    public function MnsgUpdateNew($title, $content, $time, $version){
        $payload = array(
                'mid' => 0x7025,
                'title' => $title,
                'time' => $time,
                'contents' => $content,
                'version' => $version,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);            
    }

    public function MnsgSetSoulCasket($main_str, $second_str, $time){
        $payload = array(
                'mid' => 0x7028,
                'time' => $time,
                'main_str' => $main_str,
                //'second_str' => $second_str,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);   
    }

    public function getYYSGPlayerInfoByUID($uid, $platform_id)
    {
        $payload = array(
            'mid' => 0x7021,
            'uid' => $uid,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    //群发邮件，可附物品
    public function sendMailGiftToPlayers($mail_title, $mail_body, $writer, $gift_bag_id, $player_names, $player_ids, $available_time, $platform_id){
        $payload = array(
            'mid' => 0x7028,
            'player_ids' => $player_ids,      
            'player_names' => $player_names,                                                
            'title' => $mail_title,                                                    
            'content' => $mail_body,                                                      
            'writer' => $writer,                                                        
            'giftbag_id' => $gift_bag_id,                                                       
            'duration' => $available_time,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function testmid($mid, $data)
    {
        $data['mid'] = $mid;
        $params = array(
            'payload' => json_encode((object) $data)
        );
        return $this->getResponse($params);
    }

    public function sendGiftBagToAllServer($giftbag_id, $platform_id){    //夜夜三国用的全服礼包发送接口
        $payload = array(
            'mid' => 0x700b,
            'giftbag_id' => $giftbag_id,
            'platform_id' => $platform_id,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function setFlashSaleContent($payload){    //夜夜三国设置抢购内容
        $payload['mid'] = 0x7029;
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function getFlashSaleContent(){    //夜夜三国获取抢购内容
        $payload = array(
            'mid' => 0x7030,
        );
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function sendPartner($payload){    //夜夜三国发送武将
        $payload['mid'] = 0x7024;
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }

    public function MnsgSetManaCrystalCasket($payload){ //萌娘三国设置铜钱钻石饮料机
        $payload['mid'] = 0x7031;
        $params = array(
            'payload' => json_encode((object) $payload)
        );
        return $this->getResponse($params);
    }
}