<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        header("Content-Type:text/html; charset=utf-8");
        $this->assign('CONTROLLER_NAME',CONTROLLER_NAME);
        if($_SESSION['mj_admin_id'] == ''){
            $this->redirect('Public/login');die;
        }

      /*  if($_SESSION['mj_admin_id'] != 1){
                $Auth = new \Think\Auth();
                //需要验证的规则列表,支持逗号分隔的权限规则或索引数组
                $name = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
                //当前用户id
                $uid = $_SESSION['admin_id'];
                if (!$Auth->check($name, $uid,'')) {
                    $path=__ROOT__;
                               $html='<body style="width: 100%; height: 100%; margin: 0; background-color: #eeeeee;"><p style="padding-top: 15%; text-align: center;"><img src="'.$path.'/./Public/images/no.jpg" alt="" style=""></p></body>';
                               $str="<script>document.write('$html')</script>";
                    echo $str;exit;
                }else{
                    $data = $this->category($uid);
                }
        }else{
                $data = M('auth_rule')->where("status = 1 and id != 10 and style=1 and hide = 0")->order("sort")->select();

        }
        $data = list_to_tree($data);
        $this->assign('catgoryTop',$data);*/
    }
    public function category($uid){
        $group_id = M('auth_group_access')->where("uid=$uid")->getField('group_id');
        $rules = M('auth_group')->where("id=$group_id")->getField('rules');
        $data = M('auth_rule')->where("id in ($rules) and id != 10 and hide = 0")->order("sort")->select();
        return $data;
    }

    public function upload(){
        $config = array(
            //规定上传附件大小,约3M
            'maxSize'    =>    3145728,
            //规定上传的目录
            'savePath'   =>    'upload/',
            //规定上传根目录
            'rootPath'	 =>		'./Public/',
            //规定保存名字必须唯一
            'saveName'   =>    array('uniqid',''),
            //规定附件上传的格式
            'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'    =>    true,
            'subName'    =>    array('date','Ymd'),
        );
        // 实例化上传类
        return new \Think\Upload($config);
    }

    public function gain_file($data,$type){
        foreach($data as $key=>$val){
            $arr[$key]['name']=$_FILES[$type]['name'][$key];
            $arr[$key]['type']=$_FILES[$type]['type'][$key];
            $arr[$key]['tmp_name']=$_FILES[$type]['tmp_name'][$key];
            $arr[$key]['error']=$_FILES[$type]['error'][$key];
            $arr[$key]['size']=$_FILES[$type]['size'][$key];
        }
        return $arr;
    }

    public function public_upload($data){
        $up=$this->upload();
        foreach($data as $key=>$val){
            $infos[]=$up->uploadOne($data[$key]);
        }
        return $infos;
    }

    public function thumb($infos){
        foreach($infos as $key=>$val){
            $img = './Public/' . $infos[$key]['savepath'] . $infos[$key]['savename'];
            $image = new \Think\Image();
            $image->open($img);
            $image->thumb(500, 400,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/' . $infos[$key]['savepath'] . 'thumb_' . $infos[$key]['savename']);
            $thumb_url = './Public/' . $infos[$key]['savepath'] . 'thumb_' . $infos[$key]['savename'];
            $img_list[] = $thumb_url;
            unlink($img);
        }
        return $img_list;
    }

    public function bbsThumb($infos){
        foreach($infos as $key=>$val){
            $img = './Public/' . $infos[$key]['savepath'] . $infos[$key]['savename'];
            $image = new \Think\Image();
            $image->open($img);
            $image->thumb(100, 100,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/' . $infos[$key]['savepath'] . 'thumb_' . $infos[$key]['savename']);
            $thumb_url = './Public/' . $infos[$key]['savepath'] . 'thumb_' . $infos[$key]['savename'];
            $img_list['thumb'][] = $thumb_url;
            $img_list['img'][] = $img;

        }
        return $img_list;
    }

    public function addImg($date,$row){
        $data['product_id']=$row;
        foreach($date as $key=>$val){
            $data['imgurl']=$val;
            $row=M('Product_img')->data($data)->add();
        }
        return $row;
    }
}