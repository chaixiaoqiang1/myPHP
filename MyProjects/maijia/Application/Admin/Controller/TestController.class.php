<?php
namespace Admin\Controller;
use Monolog\Handler\MailHandler;
use Think\Controller;
use Think\Image;
use Think\Think;

class TestController extends CommonController
{

    /**
     * 试车信息列表
     */
    public function index()
    {
        $test_run = M('test_run');
        $count = $test_run->count();
        $Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        setPage($Page);
        $show = $Page->show();// 分页显示输出
        $info = $test_run->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('count',$count);
//        var_dump($show);die;
        $this->assign('page',$show);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     *  试车信息状态
     */
    public function edit_status(){
        if(IS_POST) {
            $test_run = M('test_run');
            $id = I('id');
            $status = $test_run->where(array('id' => $id))->getField('status');
            if ($status == 1) {
                $status = 2;
            } else {
                $status = 1;
            }
            $result = $test_run->where(array('id' => $id))->setField('status', $status);
            if ($result) {
                $data = 'ok';
                $this->ajaxReturn($data);
            } else {
                $this->redirect('Test/index', '', 1, "<script>alert('修改失败')</script>");
            }
        }
    }

    /**
     * 试车信息添加
     */
    public function add(){
        $test_run = M('test_run');
        if(IS_POST){
            $data = $test_run->create(I('post.'));
            $data['add_time'] = time();
            $result = $test_run->add($data);
            if($result){
                $this->redirect('Test/index', '', 1, "<script>alert('添加成功')</script>");
            }else{
                $this->redirect('Test/index', '', 1, "<script>alert('添加失败')</script>");
            }
        }
        $this->display();
    }

    /***
     * 试车信息删除
     */
    public function del_test(){
        $test_run = M('test_run');
        $id = I('id');
        $result = $test_run->where(array('id' => $id))->delete();
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Test/index','',1,"<script>alert('删除失败')</script>");
        }
    }

    /***
     * 试车信息修改
     */
    public function edit(){
        $test_run = M('test_run');
        if(IS_POST){
            $data = $test_run->create(I('post.'));
            $result = $test_run->save($data);
            if($result){
                $this->redirect('Test/index','',1,"<script>alert('修改成功')</script>");
            }else{
                $this->redirect('Test/edit',array('id'=> $_POST['id']),1,"<script>alert('修改失败')</script>");
            }
        }
        $id = I('id');
        $info = $test_run->where(array('id' => $id))->find();
        $this->assign('info',$info);
        $this->display();
    }

}

