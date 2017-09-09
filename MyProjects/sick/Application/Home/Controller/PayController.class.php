<?php
namespace Home\Controller;

use Think\Controller;

class PayController extends Controller
{
    public $appUrl = "";

    public function _initialize()
    {
        header("Content-Type: text/html; charset=utf-8");
        $this->appid='wx5644101e854affee';
        $this->appsecret='5c3972dd5a386dc5f8af58e662a2b891';
        $this->mchid='1354661902';
        $this->key='7PRGsZXGy7ldykCnpnUP3mrNRZRB9Gau';
        $this->appUrl = "http://" . I("server.HTTP_HOST");
    }


    public function index()
    {
        if($_SESSION['server_id']){
            $orderid =$_SESSION['server_id'];
            $totalPrice = $_SESSION['server_price'];
            $type = $_SESSION['type'];
        }else{
            $this->redirect('Index/index');
        }
        vendor('WxPayPubHelper.WxPayPubHelper');

        $jsApi = new \JsApi_pub($this->appid, $this->appsecret, $this->mchid, $this->key);
        if (!isset($_GET['code'])) {
            $url = $jsApi->createOauthUrlForCode($this->appUrl . U("Pay/index"));
            Header("Location: $url");
        } else {
            $code = $_GET['code'];
            $jsApi->setCode($code);
            $openid = $jsApi->getOpenId();
        }
        $unifiedOrder = new \UnifiedOrder_pub($this->appid, $this->appsecret, $this->mchid, $this->key);
        $total_fee = floatval($totalPrice*100);
        $body = "易问诊";
        $unifiedOrder->setParameter("openid", "$openid");//用户标识
        $unifiedOrder->setParameter("body", "$body");//商品描述
        $unifiedOrder->setParameter("out_trade_no", "$orderid");//商户订单号
        $unifiedOrder->setParameter("total_fee", "$total_fee");//总金额
        $unifiedOrder->setParameter("notify_url",$this->appUrl . U("Pay/notify"));//通知地址

        $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型

        $unifiedOrder->setParameter("attach","$type");//附加数据

        $prepay_id = $unifiedOrder->getPrepayId();
        $jsApi->setPrepayId($prepay_id);
        $jsApiParameters = $jsApi->getParameters();



        $this->assign("jsApiParameters", $jsApiParameters);
        $this->assign("url", $this->appUrl."/Home/Index/payment_success");
        $this->display();
    }

    public function notify()
    {
        Vendor("WxPayPubHelper.WxPayPubHelper");
        Vendor("WxPayPubHelper.log_");
        //使用通用通知接口
        $notify = new \Notify_pub($this->appid, $this->appsecret, $this->mchid, $this->key);
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL");//返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        // echo $returnXml;
        //以log文件形式记录回调信息
        $log_ = new \Log_();
        $log_name = "./Public/notify_url.log";//log文件路径
        $log_->log_result($log_name, "【接收到的notify通知】:\n" . $xml . "\n");
        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
        if ($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【通信出错】:\n" . $xml . "\n");
            } elseif ($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【业务出错】:\n" . $xml . "\n");
            } else {
                //此处应该更新一下订单状态，商户自行增删操作
                $log_->log_result($log_name, "【支付成功】:\n" . $xml . "\n");
            }
            $xml = $notify->xmlToArray($xml);
            // 商户订单号
            $out_trade_no = $xml ['out_trade_no'];
            $total_fee = $xml ['total_fee'];
            $openid = $xml ['openid'];
            $type = $xml['attach'];
            if($type == 1){
                $list["is_pay"] = '2';
                $list["is_status"] = '1';
                M('ty_service')->where('id='.$out_trade_no)->data($list)->save();
            }else{
                $list["is_pay"] = '1';
                $mind_info=M('ty_mind')->where('id='.$out_trade_no)->find();
                M('doctor')->where('id='.$mind_info['d_id'])->setInc('balance',$total_fee);
                M('ty_mind')->where('id='.$out_trade_no)->data($list)->save();
            }
        }
    }

}