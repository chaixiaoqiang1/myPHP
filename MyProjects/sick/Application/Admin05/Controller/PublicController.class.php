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
           $username=I('post.username','trim');
           $password=md5(I('post.password','trim'));
           $User=M("ty_manager")->where("username='{$username}' and password='{$password}'")->find();
           if(!empty($User)){
                $_SESSION['admin_id']=$User['id'];
                $this->redirect('Index/index');
           }
         }
        $this->display();
    }

    public function logout(){
        unset($_SESSION);
        session_destroy();
        $this->redirect('Public/login');
    }
}



























