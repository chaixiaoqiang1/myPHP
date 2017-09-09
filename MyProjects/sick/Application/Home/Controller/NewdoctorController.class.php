<?php
namespace Home\Controller;
use Think\Controller;
class NewdoctorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        session('doctorId', '3');
        if(!$_SESSION['doctorId']){
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

        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5644101e854affee&redirect_uri=http://wx.yiwenzhen.net/index.php/Home/Doctor/getUserInfo&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';

        $code=I('code');

        if(!$code){

            // $this->curlGet($url);
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



            $molde = M('ty_doctor');

            $res = $molde->where('openid="'.$user->openid.'"')->find();

            if(!$res) {

                $user_res = M('ty_sick')->where('openid="'.$user->openid.'"')->find();

                if($user_res){
                    $this->redirect('Index/index','',1,"<script>alert('您已经注册为用户，不能再次注册为医生');</script>");
                }

                $_SESSION['openid'] = $user->openid;

                $date['openid'] = $user->openid;

                $date['sex'] = $user->sex == 0 ? 1 : $user->sex;

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

                    M('ty_doctor')->where('id='. $row)->data(array('twocode'=>$path.''.$qrcodeName.'.png'))->save();

                    $_SESSION['doctorId'] = $row;

                    $this->redirect('Doctor/index');

                }else{

                    $this->redirect('Doctor/getUserInfo');

                }



            }else{

                $_SESSION['doctorId'] = $res['id'];

                $this->redirect('Doctor/index');

            }

        }

    }

    /*我的零钱*/
    public function doctor_change(){
        $id = session('doctorId');
        $list = M('ty_doctor')->where(array('id'=>$id))->find();
        $this->assign('list',$list);
        $this->display();
    }
    /*
     * 余额提现
     */
    public function doctor_withdraw_deposit(){
        $id = session('doctorId');
        if(IS_POST){
            $post = $_POST;
            $balance = M('ty_doctor')->where(array('id'=> $id))->getField('balance');
            if($balance < $post['price']){
                $this->redirect('Newdoctor/doctor_withdraw_deposit','',1,"<script>alert('余额不足！')</script>");
            }
            $doctor = M('ty_money');
            $data = $doctor->create(I('post.'));
            $data['type'] = 2;
            $data['userId'] = session('doctorId');
            $data['is_statu'] = 1;
            $data['addtime'] = time();
            $result = $doctor->add($data);
            if($result){
                $this->redirect('Doctor/index','',1,"<script>alert('提交成功请耐心等待！')</script>");
            }else{
                $this->redirect('Newdoctor/doctor_withdraw_deposit','',1,"<script>alert('提交失败！')</script>");
            }
        }
        $this->display();
    }


    /*账目明细*/
    public function reckoning(){
        $this->display();
    }

    /*提现现金*/
    public function doctor_withdraw_deposit_two(){

    }

    /*心意墙*/
    public function Mind_wall(){
        $id = session('doctorId');
        $info = M('ty_mind')->where(array('d_id'=> $id))->select();
        $price =  M('ty_mind')->where(array('d_id'=> $id))->sum('price');
        $sick = M('ty_sick');
        foreach($info as $k=>&$v){
            $v['s_id'] = $sick->where(array('id'=> $v['s_id']))->field('username,icon')->find();
        }
        $this->assign('price',$price);
        $this->assign('info',$info);
        $this->display();
    }

    /*我的收入*/
    public function earning(){
        $id = session('doctorId');
        $map['d_id']  = array('eq', $id);
        $map['type']  = array('neq', 3);
        $info = M('ty_service')->where($map)->select();
        $price = M('ty_service')->where(array('d_id'=> $id))->sum('price');
        $this->assign('info',$info);
        $this->assign('price',$price);
        $this->display();
    }
}