<?php
namespace EastBlue\GameServer\SXD;
use \Curl;
use \Response;
use \App;
use \Lang;
use \Log;

class SXDGameServerApi implements SXDGameServerApiInterface {


    const SXD_KEY = "{be062dfe-7da6-3f7e-b2e7-674927cdad0d}";

    const SXD_SEND_GIFT_URL = "http://sxd-api.idgameland.com/api/sendgift.php";

    protected $response = '';

    public $url = '';

    public function __construct()
    {
    }

    protected function getResponse($params)
    {
        //Log::info('post url:'.$this->url.'post data:'.var_export($params, true));
        $response = Curl::url($this->url)->postFields($params)->post();
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

    private function makeSig($params)
    {
        $key = self::SXD_KEY;
        $sig = implode('_', $params) . '_' . $key;
        return md5($sig);
    }

    public function sxd_sendGift($user_id, $gift_id, $domain)
    {
        $this->url = self::SXD_SEND_GIFT_URL;
        $params = array(
                'user' => $user_id,
                'giftid' => $gift_id,
                'time' => time(),
                'domain' => $domain
        );
        $sign = $this->makeSig($params);
        $params['sign'] = $sign;
        return $this->getResponse($params);
    }
}