<?php

define("TOKEN", "taiyou");
$wechatObj = new wechatCallbackapiTest();

require_once('mysql.php');
header("Content-type: text/html; charset=utf-8");
mysql_query("SET NAMES 'utf-8'");
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            //获取用户信息
            $sql="SELECT * FROM ty_sick WHERE openid='$fromUsername' LIMIT 1";
            $list=mysql_query($sql);
            $user_list = mysql_fetch_array($list, MYSQL_ASSOC);
            if(!$user_list){
                $sql="SELECT * FROM ty_doctor WHERE openid='$fromUsername' LIMIT 1";
                $list=mysql_query($sql);
                $doctor_list = mysql_fetch_array($list, MYSQL_ASSOC);
                if(empty($doctor_list)){
                    $content = "请您注册当前平台";
                    $result = $this->transmitText($postObj, $content);
                    echo  $result;die;
                }else{
                    $user_id = $doctor_list['id'];
                    $user_type = 2;
                }
            }else{
                 $user_id = $user_list['id'];
                 $user_type = 1;
            }
            $GLOBALS['user_id'] = $user_id;
            $GLOBALS['user_type'] = $user_type;
            if($user_type == 1){
                //获取正在进行的服务
                $sql = "SELECT id,d_id,servetime FROM ty_service WHERE s_id=$user_id AND is_status=1 AND u_reply_status=1";
                $serve_list = mysql_query($sql);
                while($rs = mysql_fetch_assoc($serve_list)){
                    $serve_data[] = $rs;
                }
                if(!empty($serve_data)){
                    foreach($serve_data as $k=>$v){
                        if(time()>($v['servetime']+86400)){
                            $sql="UPDATE ty_service SET is_status = 2 WHERE id = $v[id]";
                            mysql_query($sql);
                        }elseif(time()<$v['servetime']){

                        }
                        else{
                            $doctor[$k]['id']=$v['id'];
                            $doctor[$k]['d_id']=$v['d_id'];
                        }
                    }
                    if(count($doctor)){
                        foreach($doctor as $k=>$v){
                            $sql="SELECT id,username FROM ty_doctor WHERE id = $v[d_id] ";
                            $res=mysql_query($sql);
                            $doctor_list[$k] = mysql_fetch_array($res, MYSQL_ASSOC);
                            $doctor_list[$k]['server_id']=$v['id'];
                        }
                        if(count($doctor_list) > 1){
                            $str= '您有多个服务正在进行,请在我的服务中设置要沟通的医生';
                            echo  $result = $this->transmitText($postObj, $str);die;
                        }else{
                            $this->return_news($postObj,$doctor_list);
                        }
                    }else{
                        echo  $this->free_server($postObj);exit;
                    }
                }else{
                    echo  $this->free_server($postObj);exit;
                }
            }else{

                $sql = "SELECT id,s_id,d_id,servetime FROM ty_service WHERE d_id=$user_id AND is_status=1 AND d_reply_status=1";
                $serve_list = mysql_query($sql);
                while($rs = mysql_fetch_assoc($serve_list)){
                    $serve_data[] = $rs;
                }
                if($serve_data){
                    foreach($serve_data as $k=>$v){
                        if(time()>($v['servetime']+86400)){
                            $sql="UPDATE ty_service SET is_status = 2 WHERE id = $v[id]";
                            mysql_query($sql);
                        }elseif(time()<$v['servetime']){

                        }
                        else{
                            $sick[$k]['id']=$v['id'];
                            $sick[$k]['d_id']=$v['d_id'];
                            $sick[$k]['s_id']=$v['s_id'];
                        }
                    }
                    if(count($sick)){
                        foreach($sick as $k=>$v){
                            $sql="SELECT id,username FROM ty_sick WHERE id = $v[s_id] ";
                            $res=mysql_query($sql);
                            $sick_list[$k] = mysql_fetch_array($res, MYSQL_ASSOC);
                            $sick_list[$k]['server_id']=$v['id'];
                        }
                        if(count($sick_list) > 1){
                            $str= '您有多个服务正在进行,请在我的服务中设置要沟通的病人';
                            echo  $result = $this->transmitText($postObj, $str);die;
                        }else{
                            $this->return_news($postObj,$sick_list);
                        }
                    }else{
                        $str= '请设置您要回复的病人，或者您现在还没有进行中的服务';
                        echo  $result = $this->transmitText($postObj, $str);die;
                    }
                }else{
                    $str= '请设置您要回复的病人，或者您现在还没有进行中的服务';
                    echo  $result = $this->transmitText($postObj, $str);die;
                }
            }
        }else {
            echo "";
            exit;
        }
    }


    public function return_news($postObj,$list){
        $MsgType = $postObj->MsgType;
        switch ($MsgType)
        {
            case "event":
                $result = $this->receiveEvent($postObj);
                break;
            case "text":
                $result = $this->receiveText($postObj,$list);
                break;
            case "image":
                $result = $this->receiveImage($postObj,$list);
                break;
            default:
                $content = '请发送图片或文字信息';
                $result = $this->transmitText($postObj,$content);
                break;
        }
        echo $result;exit;
    }

    /**
     * 获取免费服务信息
     */
    public function free_server($postObj){

        $sql="SELECT * FROM ty_doctor WHERE is_check=1 AND is_online=1 AND is_status=2 AND freeconsult=1 AND office=1 LIMIT 1";
        $list=mysql_query($sql);
        $rows = mysql_fetch_array($list, MYSQL_ASSOC);
        if($rows){
            $cate_sql="SELECT * FROM ty_category WHERE id=$rows[office] LIMIT 1";
            $cate_list=mysql_query($cate_sql);
            $cate_list = mysql_fetch_array($cate_list, MYSQL_ASSOC);

            $time=time();
            $ser_sql="INSERT INTO ty_service (type,servetime,username,office,d_id,is_status,is_pay,s_id,addtime) VALUES('3','$time','$rows[username]','$cate_list[catname]',$rows[id],1,3,$GLOBALS[user_id],$time)";
            mysql_query($ser_sql);
            $ser_id=mysql_insert_id ();

            $free_sql='UPDATE ty_doctor SET receiveconsultnum=receiveconsultnum+1 WHERE id=$rows[id]';
            mysql_query($free_sql);

            $doctor_url="http://" .$_SERVER['HTTP_HOST'].'/index.php/Home/Index/doctor_detail/id/'.$rows['id'];
            $str='您好!<a href="'.$doctor_url.'">'.$rows['username'].'</a>医生为您解答';
            $evaluate_url="http://" .$_SERVER['HTTP_HOST'].'/index.php/Home/Index/evaluate/fid/'.$ser_id;
            $heart_url="http://" .$_SERVER['HTTP_HOST']."/index.php/Home/Newuser/Send_the_mind/id/".$rows['id'];
            $url = "http://" .$_SERVER['HTTP_HOST']."/index.php/Home/Index/close/id/".$ser_id;
            $content = "$str --------                     <a href='$doctor_url'>医生资料</a>，<a href='$evaluate_url'>评价</a>，<a href='$heart_url'>送心意</a>，<a href='$url'>关闭问题</a>";
            $result = $this->transmitText($postObj,$content);
            return  $result;
        }else{
            echo '';exit;
          /*  $content = '没有免费医生';
            $result = $this->transmitText($postObj, $content);
            return  $result;*/
        }
    }



    //验证
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


    /**
     * 接收文本消息
     * @param $object
     * @return string
     */
    private function receiveText($object,$list)
    {

        //用户发送消息给医生
        if($GLOBALS['user_type'] == 1){
            $reply_id= $list[0]['id'];
            $f_id= $list[0]['server_id'];
            $s_id= $GLOBALS['user_id'];
            $d_id= $reply_id;
            $sql = "SELECT * FROM ty_doctor WHERE id=$reply_id LIMIT 1";
            $list = mysql_query($sql);
            $rows = mysql_fetch_array($list, MYSQL_ASSOC);
            if($rows) {
                $close_url = "http://" .$_SERVER['HTTP_HOST']."/index.php/Home/Index/close/id/".$f_id;
                $content = "$object->Content
                --------<a href='$close_url'>关闭服务</a>";
                $this->_reply_customer($rows['openid'],$content);
                $time=time();
                $inset_sql="INSERT INTO ty_consult (content,addtime,is_status,f_id,s_id,d_id,reply_type) VALUES ('$object->Content',$time,1,$f_id,$s_id,$d_id,1)";
                mysql_query($inset_sql);
                ob_clean();
                echo '';exit;
            }else{
                echo '';exit;
            }
        }else{
            $reply_id= $list[0]['id'];
            $f_id= $list[0]['server_id'];
            $s_id= $reply_id;
            $d_id= $GLOBALS['user_id'];
            $sql = "SELECT * FROM ty_sick WHERE id=$reply_id LIMIT 1";
            $list = mysql_query($sql);
            $rows = mysql_fetch_array($list, MYSQL_ASSOC);
            if($rows) {
                $url ='http://www.baidu.com';
                $doctor_url="http://" .$_SERVER['HTTP_HOST'].'/index.php/Home/Index/doctor_detail/id/'.$d_id;
                $evaluate_url="http://" .$_SERVER['HTTP_HOST'].'/index.php/Home/Index/evaluate/fid/'.$f_id;
                $heart_url="http://" .$_SERVER['HTTP_HOST']."/index.php/Home/Newuser/Send_the_mind/id/".$d_id;
                $close_url = "http://" .$_SERVER['HTTP_HOST']."/index.php/Home/Index/close/id/".$f_id;
                $content = "$object->Content --------                     <a href='$doctor_url'>医生资料</a>，<a href='$evaluate_url'>评价</a>，<a href='$heart_url'>送心意</a>，<a href='$close_url'>关闭问题</a>";
                $this->_reply_customer($rows['openid'],$content);

                $doc_sql="SELECT reply_time,responsetime FROM ty_doctor WHERE id=$d_id";
                $doc_list=mysql_query($doc_sql);
                $responsetime = mysql_fetch_array($doc_list, MYSQL_ASSOC);
                $consult_sql="SELECT id,addtime FROM ty_consult WHERE f_id=$f_id ORDER BY id DESC LIMIT 1";
                $consult_list=mysql_query($consult_sql);
                $consul_time = mysql_fetch_array($consult_list, MYSQL_ASSOC);
                if($consul_time['addtime']){
                    $cha=time()-$consul_time['addtime'];
                    $minute=floor(($cha%3600)/60);
                    $second=floor(($cha%60));
                    $str="$minute'分'$second'秒'";
                    if($responsetime['responsetime'] == 0 || $responsetime['reply_time'] == 0){
                        $doc_sql="UPDATE ty_doctor SET responsetime='$str',reply_time='$cha' WHERE id=$d_id";
                        mysql_query($doc_sql);
                    }else if($cha<$responsetime['reply_time']){
                        $doc_sql="UPDATE ty_doctor SET responsetime='$str',reply_time='$cha' WHERE id=$d_id";
                        mysql_query($doc_sql);
                    }
                }
                $time=time();
                $inset_sql="INSERT INTO ty_consult (content,addtime,is_status,f_id,s_id,d_id,reply_type) VALUES ('$object->Content',$time,1,$f_id,$s_id,$d_id,2)";
                mysql_query($inset_sql);
                ob_clean();
                echo '';exit;
            }else{
                echo '';exit;
            }
        }
    }




    /**
     * 接收图片消息
     */
    private function receiveImage($object,$list)
    {

        //用户发送消息给医生
        if($GLOBALS['user_type'] == 1){
            $reply_id= $list[0]['id'];
            $f_id= $list[0]['server_id'];
            $s_id= $GLOBALS['user_id'];
            $d_id= $reply_id;
            $sql = "SELECT * FROM ty_doctor WHERE id=$reply_id LIMIT 1";
            $list = mysql_query($sql);
            $rows = mysql_fetch_array($list, MYSQL_ASSOC);
            if($rows) {
                $file= $this->downImg($object->MediaId);
                $this->_reply_customer_img($rows['openid'],$object->MediaId);
                $time=time();
                $inset_sql="INSERT INTO ty_consult (content,addtime,is_status,f_id,s_id,d_id,info_type,reply_type) VALUES ('$file',$time,1,$f_id,$s_id,$d_id,2,1)";
                mysql_query($inset_sql);
                ob_clean();
                echo '';exit;
            }else{
                echo '';exit;
            }
        }else{
            $reply_id= $list[0]['id'];
            $f_id= $list[0]['server_id'];
            $s_id= $reply_id;
            $d_id= $GLOBALS['user_id'];
            $sql = "SELECT * FROM ty_sick WHERE id=$reply_id LIMIT 1";
            $list = mysql_query($sql);
            $rows = mysql_fetch_array($list, MYSQL_ASSOC);
            if($rows) {
                $file= $this->downImg($object->MediaId);
                $this->_reply_customer_img($rows['openid'],$object->MediaId);
                $doc_sql="SELECT reply_time,responsetime FROM ty_doctor WHERE id=$d_id";
                $doc_list=mysql_query($doc_sql);
                $responsetime = mysql_fetch_array($doc_list, MYSQL_ASSOC);
                $consult_sql="SELECT id,addtime FROM ty_consult WHERE f_id=$f_id ORDER BY id DESC LIMIT 1";
                $consult_list=mysql_query($consult_sql);
                $consul_time = mysql_fetch_array($consult_list, MYSQL_ASSOC);
                if($consul_time['addtime']){
                    $cha=time()-$consul_time['addtime'];
                    $minute=floor(($cha%3600)/60);
                    $second=floor(($cha%60));
                    $str="$minute'分'$second'秒'";
                    if($responsetime['responsetime'] == 0 || $responsetime['reply_time'] == 0){
                        $doc_sql="UPDATE ty_doctor SET responsetime='$str',reply_time='$cha' WHERE id=$d_id";
                        mysql_query($doc_sql);
                    }else if($cha<$responsetime['reply_time']){
                        $doc_sql="UPDATE ty_doctor SET responsetime='$str',reply_time='$cha' WHERE id=$d_id";
                        mysql_query($doc_sql);
                    }
                }
                $time=time();
                $inset_sql="INSERT INTO ty_consult (content,addtime,is_status,f_id,s_id,d_id,info_type,reply_type) VALUES ('$file',$time,1,$f_id,$s_id,$d_id,2,2)";
                mysql_query($inset_sql);
                ob_clean();
                echo '';exit;
            }else{
                echo '';exit;
            }
        }

    }

    /**
     * 下载图片到本地
     * @param $pic_id
     * @return string
     */
    public function downImg($pic_id){
        $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx5644101e854affee&secret=5c3972dd5a386dc5f8af58e662a2b891';
        $json=json_decode($this->curlGet($url_get));
        $url='http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$json->access_token.'&media_id='.$pic_id.'';
        $sub_dir = date('ymd');
        $pic_wall_save_paht = './Public/Uploads/Weixin/'.'/'.$sub_dir.DIRECTORY_SEPARATOR;
        if(!file_exists($pic_wall_save_paht)){
            mkdir($pic_wall_save_paht,0777,true);
        }
        $file_name = time().substr($pic_id,0,-5).'.jpg';
        $file_web_path = './Public/Uploads/Weixin/'.'/'.$sub_dir.'/'.$file_name;
        $imgdata =  $this->curlGet($url);
        $fp = fopen($pic_wall_save_paht.$file_name, "w");
        fwrite($fp,$imgdata);
        fclose($fp);
        return   $file_web_path;
    }

    /**
     * 回复文本消息
     * @param $object
     * @param $content
     * @return string
     */
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }


    /**
     * 主动发送文本信息
     * @param $touser
     * @param $content
     * @return mixed
     */
    function _reply_customer($touser,$content){

        //更换成自己的APPID和APPSECRET
        $APPID="wx5644101e854affee";
        $APPSECRET="5c3972dd5a386dc5f8af58e662a2b891";

        $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

        $json=file_get_contents($TOKEN_URL);
        $result=json_decode($json);

        $ACC_TOKEN=$result->access_token;

        $data = '{
        "touser":"'.$touser.'",
        "msgtype":"text",
        "text":
            {
                 "content":"'.$content.'"
            }
        }';

        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$ACC_TOKEN;

        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }

    /**
     * 主动发送图文消息
     * @param $touser
     * @param $content
     * @return mixed
     */
    function _reply_customer_img($touser,$content){

        //更换成自己的APPID和APPSECRET
        $APPID="wx5644101e854affee";
        $APPSECRET="5c3972dd5a386dc5f8af58e662a2b891";

        $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

        $json=file_get_contents($TOKEN_URL);
        $result=json_decode($json);
        $ACC_TOKEN=$result->access_token;

        $data = '{
            "touser": "'.$touser.'",
            "msgtype": "image",
            "image": {
                    "media_id": "'.$content.'"
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$ACC_TOKEN;
        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }

    /**
     * 上传图文消息素材
     * @param $media_id
     * @return mixed
     */
    public function upload_img($media_id)
    {
        $APPID="wx5644101e854affee";
        $APPSECRET="5c3972dd5a386dc5f8af58e662a2b891";

        $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

        $json=file_get_contents($TOKEN_URL);
        $result=json_decode($json);

        $ACC_TOKEN=$result->access_token;

        $data = '{
                "articles": [
                         {
                             "thumb_media_id":"' . $media_id . '",
                             "author":"",
                             "title":"上传图片",
                             "content_source_url":"",
                             "content":"content",
                             "digest":"",
                             "show_cover_pic":0
                         },
                ]
        }';
        $url = " https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=".$ACC_TOKEN;
        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }



    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                     </Image>";
        $item_str = sprintf($itemTpl, $imageArray['MediaId']);
        $xmlTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    $item_str
                    </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //接收事件消息
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                $content = "欢迎关注泰友公众号";
                break;
         /*   case "unsubscribe":
                $content = "取消关注";
                break;
            case "SCAN":
                $content = "扫描场景";
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "COMPANY":
                        $content = array();
                        $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                        break;
                    default:
                        $content = "点击菜单：".$object->EventKey;
                        break;
                }
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            case "VIEW":
                $content = "跳转链接 ".$object->EventKey;
                break;
            case "MASSSENDJOBFINISH":
                $content = "消息ID：".$object->MsgID."，结果：".$object->Status."，粉丝数：".$object->TotalCount."，过滤：".$object->FilterCount."，发送成功：".$object->SentCount."，发送失败：".$object->ErrorCount;
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;*/
        }
        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }

        return $result;
    }

    function https_post($url,$data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'Errno'.curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }
    function curlGet($url,$type="GET"){

        $ch = curl_init();

        $header = "Accept-Charset: utf-8";

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $temp = curl_exec($ch);

        return $temp;

    }

}

?>