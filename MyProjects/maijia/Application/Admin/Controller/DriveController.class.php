<?php
namespace Admin\Controller;
use Monolog\Handler\MailHandler;
use Think\Controller;
use Think\Image;
use Think\Think;

class DriveController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = M('ask');
    }

    /**
     * 试驾列表
     */
    public function index()
    {
        if(IS_POST){
            if($_POST['type']){
                $where['type']=$_POST['type'];
            }
            if($_POST['user']){
                $where['user']=array('like',"%{$_POST['user']}%");
            }
            if($_POST['motorman']){
                $where['motorman']=array('like',"%{$_POST['motorman']}%");
            }
            if($_POST['time']){
                $where['time']=$_POST['add_time'];
            }
            $count=M('order')->where($where)->count();
            $Page=new \Think\Page($count,5);
            setPage($Page);
            $show=$Page->show();
            $order=M('order')->where($where)->limit($Page->firstRow,$Page->listRows)->select();

        }else{
            $count=M('order')->count();
            $Page=new \Think\Page($count,5);
            setPage($Page);
            $show=$Page->show();
            $order=M('order')->limit($Page->firstRow,$Page->listRows)->select();
        }
        foreach($order as $k=>$v){
            $v['user']=M('user')->where("id={$v['user_id']}")->getField('name');
            $v['motorman']=M('user')->where("id={$v['motorman_id']}")->getField('name');
            $order[$k]=$v;
        }

        $this->assign("count",$count);
        $this->assign('show',$show);
        $this->assign('orderInfo',$order);
        $this->display();
    }

    /**
     * 试驾详情查看
     */
    public function see(){
        $id=I('get.id');
        $orderone=M('order')->where("id=$id")->find();
        $user=M('user')->where("id={$orderone['user_id']}")->field('name,phone')->find();
        $motorman=M('user')->where("id={$orderone['motorman_id']}")->find();


        $this->assign('orderone',$orderone);
        $this->assign('user',$user);
        $this->assign('motorman',$motorman);
        $this->display();
    }
    /**
     * 试驾详情删除 
     */
    public function delete(){
        $id=I("post.id");
        $result=M('order')->where("id=$id")->delete();
        if($result){
            echo 'OK';
        }
    }
    /**
     * 试驾详情状态更改
     */
    public function state(){
        $id=I("post.id");
        $state=M("order")->where("id=$id")->getField('state');
        if($state==1){
            $result=M("order")->where("id=$id")->data("state=2")->save();
        }else{
            $result=M("order")->where("id=$id")->data("state=1")->save();
        }
        if($result){
            echo 1;
        }else{
            echo 0;
        }
    }
    /**
     * 试驾详情状态更改
     */
    public function delete_all(){
        $str=I('get.id');
        $where=rtrim($str,',');
        $result=M('order')->where("id in ({$where})")->delete();
        if($result){
            $this->redirect('Drive/index');
        }else{
            $this->redirect('Drive/index','',0,"<script>alert('删除失败')</script>");
        }
    }
}

