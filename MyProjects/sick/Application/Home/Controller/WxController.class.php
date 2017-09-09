<?php

namespace Home\Controller;

use Think\Controller;

class WxController extends Controller {

    public function __construct()
    {
        parent::__construct();
        define("TOKEN", "taiyou");
        define('AppId', "wx5644101e854affee");//定义AppId，需要在微信公众平台申请自定义菜单后会得到
        define('AppSecret', "5c3972dd5a386dc5f8af58e662a2b891");//定义AppSecret，需要在微信公众平台申请自定义菜单后会得
    }

    public function index(){
        $echoStr = $_GET["echostr"];
        if($echoStr){
            if($this->checkSignature()){
                echo $echoStr;
                exit;
            }
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}