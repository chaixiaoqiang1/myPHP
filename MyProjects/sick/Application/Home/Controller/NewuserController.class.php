<?php
namespace Home\Controller;
use Think\Controller;
class NewuserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if(!$_SESSION['sid']){
            $this->getUserInfo();
        }
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

    public function getUserInfo(){
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5644101e854affee&redirect_uri=http://wx.yiwenzhen.net/index.php/Home/Index/getUserInfo&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        $code=I('code');

        if(!$code){
            header("Location:$url");
        }
        $api_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx5644101e854affee&secret=5c3972dd5a386dc5f8af58e662a2b891&code='.$code.'&grant_type=authorization_code';
        $token= $this->curlGet($api_url);

        $access_token=json_decode($token)->access_token;

        $openid = json_decode($token)->openid;

        $pull_url ='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_xin=$this->curlGet($pull_url,'POST');

        $user = json_decode($user_xin);

        if(!empty($user->openid)){
            //判断 用户表是否存在 此openid
            $_SESSION['openid'] = $user->openid;
            $molde = M('ty_sick');
            $res = $molde->where('openid="'.$user->openid.'"')->find();

            if(!$res) {
                $user_res = M('ty_doctor')->where('openid="'.$user->openid.'"')->find();
                if($user_res){
                    $this->redirect('Doctor/index','',1,"<script>alert('您已经注册为医生，不能再次注册为用户');</script>");
                }

                $date['username'] = $user->nickname;
                $date['openid'] = $user->openid;
                $date['sex'] = $user->sex == 0 ? 1 : $user->sex;
                $date['address'] = $user->city;
                $date['icon'] = $user->headimgurl;
                $row=$molde->add($date);
                if($row){

                    $url=$this->appUrl . '/Home/Index/index/userId/'.$row.'.html';
                    Vendor('phpqrcode.phpqrcode');
                    $errorCorrectionLevel =intval(3) ;//容错级别
                    $matrixPointSize = intval(20);//生成图片大小
                    //生成二维码图片
                    //echo $_SERVER['REQUEST_URI'];
                    $path ='./Public/QRCode/'; // 图片输出路径
                    $qrcodeName=time().$row;
                    mkdir ( $path );
                    $object = new \QRcode();
                    $object->png($url, $path.''.$qrcodeName.'.png', $errorCorrectionLevel, $matrixPointSize, 2,$saveandprint=true);
                    M('ty_sick')->where('id='. $row)->data(array('qrcode'=>$path.''.$qrcodeName.'.png'))->save();
                    $_SESSION['sid'] = $row;
                    $this->redirect('Index/index');
                }else{
                    $this->redirect('Index/getUserInfo');
                }

            }else{
                $_SESSION['sid'] = $res['id'];
                $this->redirect('Index/index');
            }
        }
    }

     /*送心意*/
     public function Send_the_mind(){
         $id = I('id');
         $dname = M('ty_doctor')->where(array('id'=> $id))->field('username,image')->find();
         if(IS_POST){
                 $data['price'] = $_POST['price'];
                 $data['content'] = $_POST['content'];
                 $data['d_id'] = $_POST['did'];
                 $data['s_id'] = session('sid');
                 $result = M('ty_mind')->add($data);
                 if($result){
                     $this->redirect('Newuser/pay',array('id'=>$result));
                 }else{
                     $this->redirect('Index/my_doctor','',1,"<script>alert('发送失败')</script>");
                 }
         }
//         var_dump($dname);die;
         $this->assign('did',$id);
         $this->assign('dname',$dname);
         $this->display();
     }

     function pay(){
        $id=I('id');
        $price=M('ty_mind')->where(array('id'=>$id))->getField('price');
        $price=0.01;
        if($price && $id){
            $_SESSION['server_id']=$id;
            $_SESSION['server_price']=$price;
            $_SESSION['type']='2';
            $this->redirect('Pay/index');
        }else{
            $this->error('参数错误');
        }
     }
    public function payment_success(){
        $id=$_SESSION['mind_id'];
        $_SESSION['mind_id']='';
        $_SESSION['mind_price']='';
        $list= M('ty_mind')->where(array('id'=>$id))->find();
        if($list['is_pay'] != 1){
            $this->redirect('Index/my_doctor','',1,"<script>alert('支付失败，请重新确认')</script>");
        }else{
            $this->redirect('Newuser/Send_the_mind_money');
        }
    }


     /*支付成功*/
     public function Send_the_mind_money(){
         $this->display();
     }

    /*支付宝绑定*/
    public function user_Alipay(){
        $this->display();
    }

    /*支付宝绑定成功*/
    public function user_Bind_uccessfully(){}

    /*我的零钱*/
    public function change(){
        $map['id']=session('sid');
        $list=M('ty_sick')->where($map)->find();
        $this->assign('list',$list);
        $this->display();
    }

    /*余额提现*/
    public function withdraw_deposit(){
        $id = session('sid');
        if(IS_POST){
            $balance = M('ty_sick')->where(array('id'=> $id))->getField('balance');
            if($balance < $_POST['price']){
                $this->redirect('Newuser/withdraw_deposit','',1,"<script>alert('余额不足！')</script>");
            }
            $doctor = M('ty_money');
            $data = $doctor->create(I('post.'));
            $data['type'] = 1;
            $data['userId'] = $id;
            $data['is_statu'] = 1;
            $data['addtime'] = time();
            $result = $doctor->add($data);
            if($result){
                $this->redirect('Index/my_content','',1,"<script>alert('提交成功请耐心等待！')</script>");
            }else{
                $this->redirect('Newuser/withdraw_deposit','',1,"<script>alert('提交失败！')</script>");
            }
        }
        $this->display();
    }

    /*账目明细*/
    public function accounts(){
        $this->display();
    }

    /*充值*/
    public function recharge(){
        if(IS_POST){
            if($_POST['price']){
                $price=$_POST['price'];
                $price=0.01;
                $rand=rand(11111,99999);
                $_SESSION['server_id']=$_SESSION['sid'].$rand;
                $_SESSION['server_price']=$price;
                $_SESSION['type']='3';
              //  print_r($_SESSION);die;
                $this->redirect('Pay/index');
            }else{
                $this->redirect('Newuser/recharge','',1,"<script>alert('非法参数')</script>");
            }
        }
        $this->display();
    }

}