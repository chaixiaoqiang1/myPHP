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

        if($_SESSION['admin_id'] == ''){
            $this->redirect('Public/login');die;
        }
        if($_SESSION['admin_id'] != 1){
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

        $this->assign('catgoryTop',$data);
    }
    public function category($uid){
        $group_id = M('auth_group_access')->where("uid=$uid")->getField('group_id');
        $rules = M('auth_group')->where("id=$group_id")->getField('rules');
        $data = M('auth_rule')->where("id in ($rules) and id != 10 and hide = 0")->order("sort")->select();
        return $data;
    }


}