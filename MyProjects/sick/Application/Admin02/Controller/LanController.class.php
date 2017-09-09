<?php
namespace Admin\Controller;
use Think\Controller;
class LanController extends Controller
{
    public function index(){

    }
    public function add(){
        if(IS_POST){
            $data = I('post.');
            $res = M('auth_rule')->add($data);
            if($res){
                $this->success('ok');
            }else{
                $this->error('err');
            }
            exit;
        }
        $data = M('auth_rule')->where("pid = 0")->select();
        $this->assign('data',$data);
        $this->display();
    }
}