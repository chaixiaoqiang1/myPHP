<?php
namespace Admin\Controller;
use Monolog\Handler\MailHandler;
use Think\Controller;
use Think\Image;
use Think\Think;

class UserController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = M('user');
    }
    /**
     * 用户信息列表
     */
    public function index()
    {
        if(IS_POST){
            $where = I('post.');
            $this->search($where);
        }
        $count = $this->model->where($where)->count();
        $Page = new \Think\Page($count,15);
        setPage($Page);
        $show = $Page->show();
        $info = $this->model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('count',$count);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 用户信息查看
     */
    public function see(){
        $id = I('id');
        $info = $this->model->where(array('id'=>$id))->find();
        $info['course_pay'] = explode(',',$info['course_pay']);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 用户信息状态设置
     */
    public function edit_status(){
        $id = I('id');
        $status = $this->model->where(array('id'=>$id))->getField('status');
        if($status == 1){
            $status = 2;
        }else{
            $status = 1;
        }
        $result = $this->model->where(array('id'=>$id))->setField('status',$status);
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('User/index','',1,"<script>alert('修改失败')</script>");
        }
    }

    /**
     * 用户信息删除
     */
    public function del(){
        $id = I('id');
        $result = $this->model->delete($id);
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('User/index','',1,"<script>alert('删除失败')</script>");
        }
    }

    /**
     * 用户信息指量删除
     */
    public function delete_all(){
        $ids = I('checks');
        $ids_str = implode(',',$ids);
        $result = $this->model->delete($ids_str);
        if($result){
            $this->redirect('User/index','',1,"<script>alert('删除成功')</script>");
        }else{
            $this->redirect('User/index','',1,"<script>alert('删除失败')</script>");
        }
    }

    /**
     * 用户信息搜索
     */
    public function search(&$where){
        $data = $where;
        $data = array_filter($data);
        if($data['name']){
            $data['name'] = array('like',"%{$data['name']}%");
        }
        if($data['phone']){
            $data['phone'] = array('like',"{$data['phone']}%");
        }
        if($data['add_time']){
            $data['add_time'] = array('like',"{$data['add_time']}%");
        }
        $where = $data;
    }
}

