<?php
namespace Admin\Controller;
use Think\Controller;
class WithdrawController extends CommonController
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index(){

        $with_list=D('Withdraw')->relation(true)->select();
        $this->assign('with_list',$with_list);
        $this->display();
    }
    public function see(){
        $id=I('id');
        $with_list=D('Withdraw')->relation(true)->where(array('id'=>$id))->find();
        $this->assign('with_list',$with_list);
        $this->display();
    }
    public function editor(){
        $id=I('id');
        $with_list=D('Withdraw')->where(array('id'=>$id))->find();
        if(empty($with_list)){
            $this->error('获取信息失败');
        }
        $mer_list=M('Shopadmin')->where(array('id'=>$with_list['shopadmin_id']))->find();
      //  print_r($mer_list);die;
        $new_balance=$mer_list['balance']-$with_list['money'];
        $new_with=$mer_list['withdraw']+$with_list['money'];
        $data['balance']=$new_balance;
        $data['withdraw']=$new_with;
        $row=M('Shopadmin')->where(array('id'=>$with_list['shopadmin_id']))->data($data)->save();
        $num=D('Withdraw')->where(array('id'=>$id))->data('status=1')->save();
        if($row && $num){
            $this->success('操作成功',U('Withdraw/index'),1);
        }else{
            $this->error('操作失败',U('Withdraw/index'),1);
        }
    }
    public function reject(){
        $id=I('id');
        $with_list=M('Withdraw')->where(array('id'=>$id))->find();
        if(empty($with_list)){
            $this->error('获取信息失败');
        }
        M('Withdraw')->where(array('id'=>$id))->data('status=2')->save();
        $this->redirect('Withdraw/index');die;
    }
}