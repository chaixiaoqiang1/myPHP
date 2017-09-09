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
             /*   $Auth = new \Think\Auth();
                //需要验证的规则列表,支持逗号分隔的权限规则或索引数组
                $name = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;*/

                //当前用户id
                $uid = $_SESSION['admin_id'];
                $data = $this->category($uid);
        }else{
                $data = M('ty_auth')->where(array('is_statu'=> '1'))->select();
        }
        $data = list_to_tree($data);
        $this->assign('catgoryTop',$data);
    }
    public function category($uid){
        $group_id = M('ty_manager')->where("id=$uid")->getField('role_id');
        $rules = M('ty_role')->where("id=$group_id")->getField('auth_all_ids');
        $data = M('ty_auth')->where("id in ($rules)")->select();
        return $data;
    }


}