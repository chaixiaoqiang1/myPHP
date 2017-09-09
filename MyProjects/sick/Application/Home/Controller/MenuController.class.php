<?php
namespace Home\Controller;
use Think\Controller;
class MenuController extends Controller
{

    public function menu(){
        $appid = "wx5644101e854affee";
        $appsecret = "5c3972dd5a386dc5f8af58e662a2b891";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        $access_tokens = $jsoninfo["access_token"];


        $zi_url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_tokens;
       /* $da='{
            "button":[
                        {
                          "type":"我要咨询",
                          "name":"个人中心",
                          "url":"http://wx.yiwenzhen.net/index.php/Home/Index/index"
                        }
                    ]
        }';*/
        $da='{
        "button":[
                {
                  "type":"我要咨询",
                  "name":"个人中心",
                  "url":"http://wx.yiwenzhen.net/index.php/Home/Index/index"
                },
                {
                   "name":"我的",
                   "sub_button":[{
                        "type":"view",
                        "name":"我的医生",
                        "url":"http://wx.yiwenzhen.net/index.php/Home/Index/my_doctor"
                    },
                    {
                       "type":"view",
                       "name":"个人中心",
                       "url":"http://wx.yiwenzhen.net/index.php/Home/Index/my_content"
                    }
                  ]
                },
                {
                   "name":"医生",
                   "sub_button":[{
                        "type":"view",
                        "name":"我的信息",
                        "url":"http://wx.yiwenzhen.net/index.php/Home/Doctor/index"
                    },
                    {
                       "type":"view",
                       "name":"我的服务",
                       "url":"http://wx.yiwenzhen.net/index.php/Home/Doctor/Selected_serve"
                    },
                    {
                       "type":"view",
                       "name":"我的患者",
                       "url":"http://wx.yiwenzhen.net/index.php/Home/Doctor/my_patient"
                    }
                  ]
                },

            ]
        }';

        $res=$this->Post($da,$zi_url);
        print_r($res);die;
    }

    function Post($curlPost,$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }
}