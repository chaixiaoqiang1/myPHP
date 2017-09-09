<?php
namespace Admin\Controller;
use Monolog\Handler\MailHandler;
use Think\Controller;
use Think\Image;
use Think\Think;

class AdminController extends CommonController
{
    /**
     * 管理员列表
     */
    public function index(){

            $count=M('admin')->count();
            $Page=new \Think\Page($count,10);
            setPage($Page);
            $listshow=$Page->show();
            $admin=M('admin')->limit($Page->firstRow,$Page->listRows)->select();

            $this->assign('count',$count);
            $this->assign('listshow',$listshow);
            $this->assign('admin',$admin);
            $this->display();
        }
    /**
     * 管理员删除
     */
    public function delete(){
        $id=I('post.id');
        $result=M('admin')->where("id=$id")->delete();
        if($result){
            $data='ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Admin/index','',1,"<script>alert('删除失败')</script>");
        }
    }
    /**
     * 管理员添加
     */
    public function add(){
        if(IS_POST){
            $data['name']=I('post.name');
            $data['password']=md5(I('post.password'));
            $data['status']=I('post.status');
            $data['add_time']=date('Y-m-d H:i:s',time());

            $result=M('admin')->add($data);
            if($result){
                $this->redirect('Admin/index');
            }else{
                $this->redirect('Admin/add','',1,"<script>alert('添加失败')</script>");
            }
        }

        $this->display();
    }
    /**
     * 管理员编辑
     */
    public function editor(){
        $id=I('get.id');
        $info=M('admin')->where("id=$id")->find();
        if(IS_POST){
            $data['name']=I('post.name');
            $data['password']=md5(I('post.password'));
            $data['status']=I('post.status');
            $data['add_time']=date('Y-m-d H:i:s',time());
            $result=M('admin')->where("id=$id")->save($data);
            if($result){
                $this->redirect('Admin/index');
            }else{
                $this->redirect('Admin/editor','',1,"<script>alert('修改失败')</script>");
            }
        }
        $this->assign('info',$info);
        $this->display();
    }
    /**
     * 管理员状态修改
     */
    public function status(){

        $id=I("post.id");
        $status=M("admin")->where("id=$id")->field("status")->find();
        if($status['status']==1){
            $result=M("admin")->where("id=$id")->data("status=0")->save();
        }else{
            $result=M("admin")->where("id=$id")->data("status=1")->save();
        }
        if($result){
            echo 1;
        }else{
            echo 0;
        }
    }
}