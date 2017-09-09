<?phpnamespace Home\Controller;use Think\Controller;class DoctorController extends Controller{    public function __construct()    {        parent::__construct();        //unset($_SESSION['doctorId']);die;//        $_SESSION['doctorId'] = 3;        if(!$_SESSION['doctorId']){            $this->getUserInfo();        }    }    function curlGet($url,$type="GET"){        $ch = curl_init();        $header = "Accept-Charset: utf-8";        curl_setopt($ch, CURLOPT_URL, $url);        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        $temp = curl_exec($ch);        return $temp;    }    public function getUserInfo(){        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx5644101e854affee&redirect_uri=http://wx.yiwenzhen.net/index.php/Home/Doctor/getUserInfo&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';        $code=I('code');        if(!$code){           // $this->curlGet($url);            header("Location:$url");        }        $api_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx5644101e854affee&secret=5c3972dd5a386dc5f8af58e662a2b891&code='.$code.'&grant_type=authorization_code';        $token= $this->curlGet($api_url);        $access_token=json_decode($token)->access_token;        $openid = json_decode($token)->openid;        $pull_url ='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';        $user_xin=$this->curlGet($pull_url,'POST');        $user = json_decode($user_xin);        if(!empty($user->openid)){            //判断 用户表是否存在 此openid            $molde = M('ty_doctor');            $res = $molde->where('openid="'.$user->openid.'"')->find();            if(!$res) {                $user_res = M('ty_sick')->where('openid="'.$user->openid.'"')->find();                if($user_res){                    $this->redirect('Index/index','',1,"<script>alert('您已经注册为用户，不能再次注册为医生');</script>");                }                $_SESSION['openid'] = $user->openid;                $date['openid'] = $user->openid;                $date['sex'] = $user->sex == 0 ? 1 : $user->sex;                $row=$molde->add($date);                if($row){                    $url=$this->appUrl . '/Home/Index/index/userId/'.$row.'.html';                    Vendor('phpqrcode.phpqrcode');                    $errorCorrectionLevel =intval(3) ;//容错级别                    $matrixPointSize = intval(20);//生成图片大小                    //生成二维码图片                    //echo $_SERVER['REQUEST_URI'];                    $path ='./Public/QRCode/'; // 图片输出路径                    $qrcodeName=time().$row;                    mkdir ( $path );                    $object = new \QRcode();                    $object->png($url, $path.''.$qrcodeName.'.png', $errorCorrectionLevel, $matrixPointSize, 2,$saveandprint=true);                    M('ty_doctor')->where('id='. $row)->data(array('twocode'=>$path.''.$qrcodeName.'.png'))->save();                    $_SESSION['doctorId'] = $row;                    $this->redirect('Doctor/index');                }else{                    $this->redirect('Doctor/getUserInfo');                }            }else{                $_SESSION['doctorId'] = $res['id'];                $this->redirect('Doctor/index');            }        }    }    // 首页    public function index(){        $doctorId = session('doctorId');        $doctor_info = M('ty_doctor')->where(array('id'=>$doctorId))->field('username,receiveconsultnum,buyservernum,zscore,responsetime,image')->find();        $this->assign('info',$doctor_info);        $this->display();    }    //个人资料    public function personal_info(){        $doctorId = session('doctorId');        $doctorobj = M('ty_doctor');        $doctor_info = $doctorobj->where(array('id'=>$doctorId))->field('is_attestation,username,iphone,mailbox,hospital,office,job,school,major,education')->find();        $category_info = M('ty_category')->order('id asc')->select();        if(IS_POST){            $data = $doctorobj->create(I('post.'));            $result = $doctorobj->where(array('id'=>$doctorId))->save($data);            if($result){//              $this->success('提交成功',U('Doctor/personal_info'),1);                $this->redirect('Doctor/personal_info','',1,"<script>alert('提交成功')</script>");            }else{                $this->redirect('Doctor/personal_info','',1,"<script>alert('提交失败')</script>");            }        }        $this->assign('cateinfo',$category_info);        $this->assign('info',$doctor_info);        $this->display();    }    //我的二维码    public function code(){        $doctorId = session('doctorId');        $info = M('ty_doctor')->where(array('id'=>$doctorId))->field('id,username,image,twocode')->find();        $this->assign('info',$info);        $this->display();    }    //我的粉丝    public function doctor_follower(){        $doctorId = session('doctorId');        $info = M('ty_doctor')->where(array('id'=>$doctorId))->field('sick_id')->find();        $info['sick_id'] = json_decode($info['sick_id'],true);        $info['sick_id'] = implode(',',$info['sick_id']);        $whe['id']=array('in',$info['sick_id']);        $list = M('ty_sick')->where($whe)->field('id,username,icon')->select();        $this->assign('list',$list);        $this->display();    }    //医生执照    public function authentication(){        $doctorId = session('doctorId');        $doctorobj = M('ty_doctor');        $doctor_info = $doctorobj->where(array('id'=>$doctorId))->field('practice,qualification,identity')->find();        if(IS_POST){            if($_FILES['zhizhao']['error'] == 0){                $info = $this->uploadimg();                if($info){                    $zhizhao = $info['zhizhao']['savepath'].$info['zhizhao']['savename'];                    $result01 = M('ty_doctor')->where(array('id'=> $doctorId))->setField('licenseimg',$zhizhao);                    if($result01){                        $this->redirect('Doctor/authentication','',1,"<script>alert('图片上传成功')</script>");                    }else{                        $this->redirect('Doctor/authentication','',1,"<script>alert('图片上传失败')</script>");                    }                }            }            $data = $doctorobj->create(I('post.'));            $result = $doctorobj->where(array('id'=>$doctorId))->save($data);            if($result){                $this->redirect('Doctor/authentication','',1,"<script>alert('提交成功')</script>");            }        }        $this->assign('info',$doctor_info);        $this->display();    }    //详细信息    public function doctor_info(){        $doctorId = session('doctorId');        $doctorobj = M('ty_doctor');        $doctor_info = $doctorobj->where(array('id'=>$doctorId))->find();        $doctor_info['office'] = M('ty_category')->where(array('id'=>$doctor_info['office']))->getField('catname');        if(IS_POST){            $data = $doctorobj->create(I('post.'),2);            $result = $doctorobj->where(array('id'=> $doctorId))->save($data);            if($result){                $this->redirect('Doctor/doctor_info','',1,"<script>alert('提交成功')</script>");            }else{                $this->redirect('Doctor/doctor_info','',1,"<script>alert('提交失败')</script>");            }        }        $this->assign('info',$doctor_info);        $this->display();    }    /*医生图像上传*/    public function uploadimg(){        $upload = new \Think\Upload();// 实例化上传类        $upload->maxSize   =     3145728 ;// 设置附件上传大小        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录        $upload->savePath  =     ''; // 设置附件上传（子）目录        // 上传文件        $info   =   $upload->upload();        return $info;    }    // 一般信息    public function my_info(){        $doctorId = session('doctorId');        $doctorobj = M('ty_doctor');        $doctor_info = $doctorobj->where(array('id'=>$doctorId))->field('image,iphone,mailbox,age,sex')->find();        if(IS_POST){                $abc =  $this->uploadimg();                if($abc){                    $img =  $abc['image']['savepath'].$abc['image']['savename'];                    $doctorobj->where(array('id'=> $doctorId ))->setField('image',$img);                }                $data = $doctorobj->create(I('post.'));                $data = array_filter($data);                $result = $doctorobj->where(array('id'=> $doctorId))->save($data);                if($result){                    $this->redirect('Doctor/my_info','',1,"<script>alert('更新成功')</script>");                }else{                    $this->redirect('Doctor/my_info');                }        }        $this->assign('info',$doctor_info);        $this->display();    }    //立即开通页面    public function Free_consultation(){        $doctorId = session('doctorId');        $type = I('type');        if(IS_POST){     /*       var_dump($_POST);die; 'type' => string '3' (length=1)  'kg' => string '2' (length=1)*/            if($type == '1' ){                M('ty_doctor')->where(array('id'=>$doctorId))->setField('is_picture','1');            }else if($type == '2'){                M('ty_doctor')->where(array('id'=>$doctorId))->setField('is_orderstatus','1');            }else if($type == '3'){                $result = M('ty_doctor')->where(array('id'=>$doctorId))->setField('freeconsult',$_POST['kg']);                if($result){                       $aaa = 'aaa';                       $this->ajaxReturn($aaa);die;                }else{                     $this->redirect('Doctor/Free_consultation','',1,"<script>alert('更改失败')</script>");die;                }            }        }        $this->assign('type',$type);        $this->display();    }    // 开通后的页面    public function Selected_serve(){        $doctorId = session('doctorId');        $doctorobj = M('ty_doctor');        $doctor_info = $doctorobj->where(array('id'=>$doctorId))->field('freeconsult,is_orderstatus,is_picture')->find();//        var_dump($doctor_info);die;        $this->assign('info',$doctor_info);        $this->display();    }    //图文咨询内容页    public function images_text_condult(){        $doctorId = session('doctorId');        $service = M('ty_service');        $service_info = $service->where(array('d_id'=>$doctorId,'type'=> 1))->order('id asc')->select();        foreach($service_info as $k=>&$v){            $service_info[$k]['user_name'] = M('ty_sick')->where(array('id'=> $v['s_id']))->getField('username');        }//        print_r($service_info);die;        $this->assign('info',$service_info);        $this->display();//        print_r($service_info);die;        /*foreach($service_info as $k=>$v){            $aaa[] = M('ty_consult')->where(array('f_id'=> $v))->find();        }        foreach($aaa as $k=>$v){            if($v){                $bbb[$k] = $v;            }        }*/       /* foreach($bbb as &$v){            $v['s_id'] = M('ty_sick')->where(array('id'=>$v['s_id']))->field('username,icon')->find();        }*/        $this->assign("info",$service_info);        $this->display();    }    //图文设置    public function img_set_up(){        $doctorId = session('doctorId');        $imagetextobj = M('ty_imagetext');        $imagetext_info = $imagetextobj->where(array('d_id'=> $doctorId))->find();        if(IS_POST){            $data = $imagetextobj->create(I('post.'));            $data['d_id'] = $doctorId;            if(!$data['is_ticket']){                $data["is_ticket"] = 2;            }            if($imagetext_info){                $result = $imagetextobj->where(array('d_id'=>$doctorId))->save($data);            }else{                $result = $imagetextobj->where(array('d_id'=>$doctorId))->add($data);            }            if($result){                $this->redirect('Doctor/images_text_condult');die;            }else{                $this->error('更新失败','',1);die;            }        }        $this->assign('info',$imagetext_info);        $this->display();    }    //预约咨询设置页    public function Free_consultation_fit(){        $doctorId = session('doctorId');        $orderconsultobj = M('ty_orderconsult');        $orderconsult_info = $orderconsultobj->where(array('d_id'=> $doctorId))->find();        if(IS_POST){            $data = $orderconsultobj->create(I('post.'));            if(!$data['is_open']){                $data['is_open'] = 2;            }            $result = $orderconsultobj->where(array('d_id'=> $doctorId))->save($data);            if($result){                $this->redirect('Doctor/Free_consultation_fit','',1,'<script>alert("更新成功")</script>');            }else{                $this->redirect('Doctor/Free_consultation_fit','',1,'<script>alert("更新失败")</script>');            }        }        $this->assign('info',$orderconsult_info);        $this->display();    }    //预约列表页    public function appointment_consultation(){        $doctorId = session('doctorId');        $order_info = M('ty_service')->where(array('d_id'=> $doctorId,'type'=> '2'))->order('id desc')->select();        foreach($order_info as $k=>&$v){            $order_info[$k]['user_name'] = M('ty_sick')->where(array('id'=> $v['s_id']))->getField('username');         /*   $arr['content'] = M('ty_consult')->where(array('f_id'=> $v['id']))->field('content')->find();            $arr['addtime'] = M('ty_consult')->where(array('f_id'=> $v['id']))->field('addtime')->find();            $v['s_id'] =  $arr;*/        }//        print_r($order_info);die;        $this->assign('info',$order_info);        $this->display();    }    //预约时间设置    public function time_set_up(){        $doctorId = session('doctorId');//        $order_obj = M('ty_orderset');//        $order_info = $order_obj->where(array('d_id'=> $doctorId))->distinct(true)->field('week,ordertime')->select();//        $arr1 = array();//        foreach($order_info as $k=>$v){//            $arr1[$k] = $v['ordertime'].'-'.$v['week'];//        }//        var_dump($arr1);die;//        var_dump($order_info);die;//        $this->assign('info',$order_info);//        $Model->distinct(true)->field('name')->select();//        var_dump($order_info);die;        if(IS_POST){            $week = $_POST['weekday'];            if($week){                M('ty_orderset')->where(array('d_id'=> $doctorId ))->delete();            }            $renshu = $_POST['renshu'];            $price = $_POST['price'];            if($price){                M('ty_doctor')->where(array('id'=> $doctorId))->setField('orderprice',$price);            }            $aaa = explode(',', $week);            foreach($aaa as $k=>$v){                if($v){                    $bbb[$k] = explode('-',$v);                }            }            $order_obj = M('ty_orderset');            $data['d_id'] = $doctorId;            $data['numpeople'] = $renshu;            foreach($bbb as $k=>$v){                $data['week']  =  $v[1];                $data['ordertime']  =  $v[0];                $order_obj->data($data)->add();            }            $this->redirect('Doctor/appointment_consultation','',1,"<script>alert('添加成功')</script>");        }        $this->display();    }    //  我的患者    public function my_patient(){        $doctorId = session('doctorId');        $doctor_data = M('ty_doctor')->where(array('id'=>$doctorId))->field('sick_id,userconsultid,acceptsick_id')->find();        if(IS_POST){            $listname = I('listname');            $data['d_id'] = $doctorId;            $data['listname'] = $listname;            $doctorsick = M('ty_doctorsick');            $result = $doctorsick->add($data);            if($result){                $this->redirect('Doctor/my_patient','',1,'<script>alert("分组添加成功")</script>');            }else{                $this->redirect('Doctor/my_patient','',1,'<script>alert("分组添加失败")</script>');            }        }        // 分组        $group_2 = M('ty_doctorsick')->where(array('d_id'=> $doctorId))->select();        foreach($group_2 as $k=>$v){              $group_2[$k]['s_id'] = array_unique(json_decode($v['s_id'],true));              $group_2[$k]['count'] = count($group_2[$k]['s_id']);              foreach($group_2[$k]['s_id'] as $k1=>$v1){                  $group_2[$k]['s_id'][$k1] = M('ty_sick')->where(array('id'=> $v1))->field('id,username')->find();              }        }        $this->assign('group',$group_2);        extract($doctor_data);        $sick_id = json_decode($sick_id,true);        $userconsultid = json_decode($userconsultid,true);        $acceptsick_id = json_decode($acceptsick_id,true);        $sick = M('ty_sick');        $arr1 = array();  // 关注 咨询 接受治疗        $arr2  = array(); // 咨询        $arr3 = array(); // 接受治疗        foreach($sick_id as $k=>$v){            $arr1[] =  $sick->where(array('id'=> $v))->field('id,username')->find();        }        foreach($userconsultid as $k=>$v){            $arr2[] = $sick->where(array('id'=> $v))->field('id,username')->find();        }        foreach($acceptsick_id as $k=>$v){            $arr3[] = $sick->where(array('id'=> $v))->field('id,username')->find();        }        $count_arr[] = count($arr1);        $count_arr[] = count($arr2);        $count_arr[] = count($arr3);        $this->assign('count_arr',$count_arr);        $this->assign('sick_id',$arr1);        $this->assign('userconsultid',$arr2);        $this->assign('acceptsick_id',$arr3);        $this->display();    }    // 移动好友页面    public function Patient_name_card(){        $doctorId  = session('doctorId');        $type = I('t'); //类型 共3个 关注 咨询 接受治疗        $sId = I('sid');        if(IS_POST){           /* $post = I('post.');            var_dump($post);die;*/                $sickId = I('sickid');                $gname = I('groupname');                $info = M('ty_doctorsick')->where(array('id'=> $gname ))->getField('s_id');                if($info){                    $info1 = json_decode($info,true);                    $info2 = array_unique($info1);                    foreach($info2 as $k=>$v){                        $info2[] = (int)$sickId;                    }                    $info3 = json_encode($info2);                    $result = M('ty_doctorsick')->where(array('id'=> $gname ))->setField('s_id',$info3);                    if($result){                       $this->redirect('Doctor/my_patient','',1,'<script>alert("提交成功")</script>');                   }else{                       $this->redirect('Doctor/my_patient','',1,'<script>alert("提交失败")</script>');                   }            }else{                $aaa[] = (int)$sickId;                $bbb  = json_encode($aaa);                $ccc = M('ty_doctorsick')->where(array('id'=> $gname ))->setField('s_id', $bbb);            }        }        $sick_info = M('ty_sick')->where(array('id'=> $sId))->field('username,phonenum,sex,userage,height,weight')->find();        $groupdoctor = M('ty_doctorsick')->where(array('d_id'=> $doctorId))->select();        $this->assign('sickId',$sId);        $this->assign('type',$type);        $this->assign('group',$groupdoctor);        $this->assign('info',$sick_info);        $this->display();    }    /*图文聊天设置*/    public function setchat(){//        $sid = session('sid');        $get = I('get.');        $service = M('ty_service');        if($get['guan'] == 1){            $result =  $service->where(array('id'=>$get['fid']))->setField('d_reply_status',2);        }elseif($get['guan'] == 2){            $result = $service->where(array('id'=>$get['fid']))->setField('d_reply_status',1);        }elseif($get['guan'] == 3){            $result =  $service->where(array('id'=>$get['fid']))->setField('is_status',2);        }        if($result){            $this->redirect("Doctor/images_text_condult",'',1,"<script>alert('操作成功')</script>");        }else{            $this->redirect("Doctor/images_text_condult",'',1,"<script>alert('操作失败')</script>");        }    }    /*预约聊天设置*/    public function ordersetchat(){//        $sid = session('sid');        $get = I('get.');        $service = M('ty_service');        if($get['guan'] == 1){            $result =  $service->where(array('id'=>$get['fid']))->setField('d_reply_status',2);        }elseif($get['guan'] == 2){            $result = $service->where(array('id'=>$get['fid']))->setField('d_reply_status',1);        }elseif($get['guan'] == 3){            $result =  $service->where(array('id'=>$get['fid']))->setField('is_status',2);        }        if($result){            $this->redirect("Doctor/appointment_consultation",'',1,"<script>alert('操作成功')</script>");        }else{            $this->redirect("Doctor/appointment_consultation",'',1,"<script>alert('操作失败')</script>");        }    }}