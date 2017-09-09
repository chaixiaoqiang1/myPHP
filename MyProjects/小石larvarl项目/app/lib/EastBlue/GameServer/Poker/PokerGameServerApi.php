<?php
namespace EastBlue\GameServer\Poker;
use \Curl;
use \Response;
use \App;
use \Lang;
use \Log;
use \Auth;
use \Session;

class PokerGameServerApi implements PokerGameServerApiInterface {

    const POKER_KEY = "eb/api/v1";

    protected $serverIP = '';

    protected $serverPort = '';

    protected $response = '';

    public $url = '';

    public function __construct()
    {
    }
    /* 建立连接 */
    public function connect($server_ip, $server_port)
    {  
        $this->serverIP = $server_ip;
        $this->serverPort = $server_port;
        $this->buildPokerUrl();
        return $this;
    }
    /* 获取URL */
    protected function buildPokerUrl()
    {
        $parts = array(
                'scheme' => 'http://',
                'host' => $this->serverIP . ':',
                'port' => $this->serverPort . '/',
                'path' => self::POKER_KEY
        );
        $this->url = implode('', $parts);
    }

    protected function getParams($mid, $payload = array())
    {
        return array(
                'mid' => $mid,
                'payload' => json_encode((object) $payload)
        );
    }

    protected function getResponse($params)
    {
        //var_dump($this->url);
        //这里打印一条日志，记录所有调用接口的信息
        if(Auth::check()){
            Log::info('PokerGameServerApi--Url:'.$this->url.', Username:'.Auth::user()->username.',game_id:'.Session::get('game_id').',params:'.var_export($params, true));
        }else{
            Log::info('PokerGameServerApi--Url:'.$this->url.', params:'.var_export($params, true));
        }
        $response = Curl::url($this->url)->postFields($params)->post();
        //var_dump($response);die();
        $this->response = $response;
        $body = $this->response->body;
        if (isset($body->error_code))
        {
            $body->code = $body->error_code;
            $body->error = Lang::get('error.game_server_error');
        }
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
    /*
     * 德州扑克发筹码，player_id & uid 二选一
     */
    public function giveChips($chips, $player_id = '', $uid = '')
    {
        $payload = array(
                'chips' =>  $chips,
                'player_id' => (int) $player_id,
                'uid' => (string) $uid
        );
        $params = $this->getParams(0xfcff, $payload);
        return $this->getResponse($params);
    }
    /*
     * 德州扑克发元宝，player_id & uid 二选一
     */
    public function giveGold($gold, $player_id = '', $uid = '')
    {
        $payload = array(
                'gold' => (int) $gold,
                'player_id' => (int) $player_id,
                'uid' => (string) $uid
        );
        $params = $this->getParams(0xfcfe, $payload);
        return $this->getResponse($params);
    }
    /*
     * 发物品
     */
    public function giveItem($player_id, $item_id, $num)
    {
        $payload = array(
                'item_id' => (int) $item_id,
                'num' => (int) $num,
                'player_id' => (int) $player_id
        );
        $params = $this->getParams(0xfcfd, $payload);
        return $this->getResponse($params);
    }
    /*
     * 获取玩家信息
     */
    public function getPlayer($player_id)
    {
        $payload = array(
                'player_id' => (int) $player_id
        );
        $params = $this->getParams(0xfcfc, $payload);
        return $this->getResponse($params);
    }
    
    /*
     * 获取问题
     */
    public function getSupportQuestion()
    {
        $params = $this->getParams(0xfcfb);
        return $this->getResponse($params);
    }
    
    /*
     * 回答问题
     */
    public function replySupportQuestion($id, $replied_msg)
    {
        $payload = array(
                'id' => (int) $id,
                'replied_msg' => (string) $replied_msg
        );
        $params = $this->getParams(0xfcfa, $payload);
        return $this->getResponse($params);
    }
    public function announceSend($content, $type)
    {
        $payload = array(
            'content' => $content,
            'lang_code' => $type,
        );
        $params = $this->getParams(0xfcf9, $payload);
        return $this->getResponse($params);
    }
	public function getRoomPlayer($start_time)
	{
		$payload = array(
			'date' => $start_time
		);
		$params = $this->getParams(0xfcf8, $payload);
		return $this->getResponse($params);
	} 

    public function getPlayerChips($chips)
    {
        $payload = array(
            'chips' => intval($chips)
        );
        $params = $this->getParams(0xfcf7, $payload);
        return $this->getResponse($params);
    }
    public function getOnlineNum($start)
    {
        $payload = array(
            'date' => strval($start)
        );
        $params = $this->getParams(0xfcf6, $payload);
        return $this->getResponse($params);
    }

    public function userPieceData()
    {
        $payload = array();
        $params = $this->getParams(0xfcf5, $payload);
        return $this->getResponse($params);
    }

    public function getPokerChips($start)
    {
        $payload = array(
            'date' => strval($start)
        );
        $params = $this->getParams(0xfcf4, $payload);
        return $this->getResponse($params);
    }

    public function dailyChips($time)
    {
        $payload = array(
            'date' => strval($time)
        );
        $params = $this->getParams(0xfcf3, $payload);
        return $this->getResponse($params);
    }

    public function dailyTfRecover($time)
    {
        $payload = array(
            'date' => strval($time)
            );
        $params = $this->getParams(0xfcf2, $payload);
        return $this->getResponse($params);
    }

    public function sendMessage($player_id, $message)
    {
        $payload = array(
            'player_id' => intval($player_id),
            'message' => strval($message)
        );
        $params = $this->getParams(0xfcf1, $payload);
        return $this->getResponse($params);
    }

    public function deleteChips($player_id, $chips, $type)
    {
        $payload = array(
            'player_id' => intval($player_id),
            'chips' => intval($chips),
            'type' => intval($type)
        );
        $params = $this->getParams(0xfcf0, $payload);
        return $this->getResponse($params);
    }
    public function freezePlayers($player_id, $is_ban)
    {
        $payload = array(
            'player_id' => intval($player_id),
            'is_ban' => intval($is_ban)
        );
        $params = $this->getParams(0xfcdf, $payload);
        return $this->getResponse($params);
    }

    public function getCheaterPlayerId($type, $start_time, $end_time)
    {
        $msg = (object)array('body'=>'type error.');
        switch ($type) {
            case 1: $mid = 0xfcde; break;  //双人牌局检测
            case 2: $mid = 0xfcdd; break;  //连胜
            default: return $msg;
        }
        $payload = array(
            'start_time' => $start_time,
            'end_time' => $end_time
        );
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }
    

    public function steadWinPlayer($start_time,$end_time)
    {

	   $payload = array(
	        'start_time' => intval($start_time),
 	        'end_time' => intval($end_time)
    	);
	   $params = $this->getParams(0xfcdd, $payload);
       return $this->getResponse($params);	
    }

    public function tradeChips($start_time,$end_time)
    {
        $payload = array(
            'start_time' => intval($start_time),
            'end_time' => intval($end_time)
            );
        $params = $this->getParams(0xfcde,$payload);
        return $this->getResponse($params);
    }

    public function setSpeakAuthority($player_id,$is_ban_speak)
    {
        $payload = array(
            'player_id' => $player_id,
            'is_ban_speak' => $is_ban_speak
            );
        $params = $this->getParams(0xfcdc,$payload);
        return $this->getResponse($params);
    }

    public function setActivityStatus($activity_id,$start_time,$end_time,$status)
    {
        $payload = array(
            'activity_id' => $activity_id,
            'start_time'  => $start_time,
            'end_time'    => $end_time,
            'status'      => $status,
            );
        $params = $this->getParams(0xfcdb,$payload);
        return $this->getResponse($params);
    }

    public function getRecentAction($player_id)
    {
        $payload = array(
            'player_id' => $player_id,
            );
        $params = $this->getParams(0xfcda,$payload);
        return $this->getResponse($params);
    }

    public function getSameStrongboxPasswd()
    {
        $payload = array();
        $params = $this->getParams(0xfcd9,$payload);
        return $this->getResponse($params);
    }

    public function getSameStrongboxPasswdPlayer($player_id)
    {
        $payload = array(
            'player_id' => $player_id,
            );
        $params = $this->getParams(0xfcd7,$payload);
        return $this->getResponse($params);
    }

    public function getSameStrongboxPasswdPassword($password)
    {
        $payload = array(
            'password' => $password,
            );
        $params = $this->getParams(0xfcd6,$payload);
        return $this->getResponse($params);   
    }

    public function setBusinessman($player_id,$level)
    {
        $payload = array(
            'player_id' => $player_id,
            'level' => $level,
            );
        $params = $this->getParams(0xfcd5,$payload);
        return $this->getResponse($params);
    }

    public function getBusinessman()
    {
        $payload = array();
        $params = $this->getParams(0xfcd4,$payload);
        return $this->getResponse($params);
    }

    public function sendLibao($player_array,$items_string)
    {
        $payload = array(
            'player_array' => $player_array,
            'items_string' => $items_string
            );
        $params = $this->getParams(0xfcd1,$payload);
        return $this->getResponse($params);
    }

    public function sendallservergiftbag($start_time, $end_time,$items_string)
    {
        $payload = array(
            'start_time' => $start_time,
            'end_time' => $end_time,
            'items_string' => $items_string
            );
        $params = $this->getParams(0xfcd0,$payload);
        return $this->getResponse($params);
    }   

}

