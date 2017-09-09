<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller
{
    public function index()
    {
        $this->display();
    }
    public function login(){
        if(IS_POST){
            $where['name'] = $_POST['username'];
            $where['password'] = md5($_POST['password']);
            $admin_list = M('admin')->where($where)->find();
            if($admin_list['status'] == 1){
                $_SESSION['mj_admin_id']   =$admin_list['id'];
                $_SESSION['mj_admin_name'] =$admin_list['name'];
                $_SESSION['mj_login_time'] =date('Y-m-d H:i:s',time());
                $this->redirect('Index/index');die;
            }else{
                $this->redirect('Public/login');die;
            }
        }else{
            $this->display();
        }
    }

    public function logout(){
        unset($_SESSION);
        session(null);
        session_destroy();
        $this->redirect('Public/login');
    }
}