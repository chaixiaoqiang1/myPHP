<?php
namespace EastBlue\GameServer\Dld;
use \Curl;
use \Response;
use \App;
use \Lang;

class DldGameServerApi {

	const DIR_KEY = '.server';

    protected $serverIP = '';

    protected $serverPort = '';

    protected $serverDirID = '';

    protected $response = '';

    public $url = '';

    public function connect($server_ip, $server_port, $server_dir_id)
    {
        $this->serverIP = $server_ip;
        $this->serverPort = $server_port;
        $this->serverDirID = $server_dir_id;
        $this->buildUrl();
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

    protected function getParams($mid, $payload = array())
    {
        return array(
                'mid' => $mid,
                'payload' => json_encode((object) $payload)
        );
    }

    protected function getResponse($params)
    {
        $response = Curl::url($this->url)->postFields($params)->post();
        $this->response = $response;
        $body = $this->response->body;
        if (isset($body->error_code))
        {
            $body->code = $body->error_code;
            $body->error = Lang::get('error.game_server_error');
        }
        /* $post_data = $this->url."".var_export($params, true);
        return $post_data;*/
        /*foreach ($response as $k => $v)
        {
            $data_res[$k] = $v;
        }
        return $data_res;*/
        return $body;
    }

    public function resetXJYWC()
    {
    	$payload = array();
    	$mid = 0xbc4c;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function setBudokai($isOpen)
    {
        $payload = array(
            'isOpen' => $isOpen
        );
        $mid = 0xbc4d;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function setLvTalk($active, $level)
    {
        $payload = array(
            'active' => (int) $active,
            'level' => (int) $level
        );
        $mid = 0Xbc4e;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

    public function lvTalkLookup()
    {
        $payload = array(
        );
        $mid = 0Xbc4f;
        $params = $this->getParams($mid, $payload);
        return $this->getResponse($params);
    }

}