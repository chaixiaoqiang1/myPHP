<?php
namespace Admin\Controller;
use Monolog\Handler\MailHandler;
use Think\Controller;
use Think\Image;
use Think\Think;

class AskController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = M('ask');  // 因为这个类所有操作都需要用到这张表 所以我定的了一个
    }

    /**
     * 问题列表
     */
    public function index()
    {
        $user = M('user');
        if(IS_POST){
            $where = I('post.');
            $this->search($where);
        }
        $count = $this->model->where($where)->count();
        $Page = new \Think\Page($count,15);
        setPage($Page);
        $show = $Page->show();
        $info = $this->model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($info as $k=>&$v) {
            $v['user_id'] = $user->where(array('id'=>$v['user_id'] ))->getField('name');
        }
        $this->assign('page',$show);
        $this->assign('count',$count);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 问题状态修改
     */
    public function edit_status(){
        $id = I('id');
        $status = $this->model->where(array('id'=>$id))->getField('status');
        if($status == 1){
            $status = 0;
        }else{
            $status = 1;
        }
        $result = $this->model->where(array('id'=>$id))->setField('status',$status);
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Ask/index','',1,"<script>alert('修改失败')</script>");
        }
    }

    /**
     * 问题信息删除
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
     * 问题信息指量删除
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
     * 问题信息搜索
     */
    public function search(&$where){
        $data = $where;
        $data = array_filter($data);
        if($data['title']){
            $data['title'] = array('like',"%{$data['title']}%");
        }
        if($data['content']){
            $data['content'] = array('like',"%{$data['content']}%");
        }
        if($data['add_time']){
            $data['add_time'] = array('like',"{$data['add_time']}%");
        }
        $where = $data;
    }

    /**
     * 问题详情查看
     */
    public function see(){
        $user = M('user');
        $id = I('id');
        $info = $this->model->where(array('id'=>$id))->find();
        $info['imgs'] = explode('*',$info['img']);
        $info['img'] = $info['imgs'][0];
        $info['user_id'] = $user->where(array('id'=> $info['user_id']))->field('icon,name')->find();
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 查看回答信息
     */
    public function see_ask_info(){
       if(IS_POST){
            foreach($_POST as $k=>$v){
                if($v){
                    if($k == 'ask_id'){
                        $where['ask_id']=$v;
                    }else{
                        $where[$k]=array('like','%'.$v.'%');
                    }
                }
            }
           $id = $_POST['ask_id'];
       }else{
           $id = I('id');
           $where['ask_id']=$id;
       }
            $ask_com = M('ask_com');
            $user = M('user');

            $count =  $ask_com->where($where)->count();
            $Page = new \Think\Page($count,1);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            setPage($Page);
            $show = $Page->show();// 分页显示输出
            $info = $ask_com->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($info as $k=>&$v){
                $v['user_id'] = $user->where(array('id'=>$v['user_id']))->getField('name');
            }
            $this->assign('id',$id);
            $this->assign('count',$count);
            $this->assign('page',$show);
            $this->assign('info',$info);
            $this->display();

    }

    /**
     * 回答详情状态更改
     */
    public function edit_status_com(){
        $ask_com = M('ask_com');
        $id = I('id');
        $status = $ask_com->where(array('id'=>$id))->getField('status');
        if($status == 1){
            $status = 0;
        }else{
            $status = 1;
        }
        $result = $ask_com->where(array('id'=>$id))->setField('status',$status);
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Ask/see_ask_info','',1,"<script>alert('修改失败')</script>");
        }
    }

    /**
     * 回答详情删除
     */
    public function del_ask_info(){
        $ask_com = M('ask_com');
        $id = I('id');
        $result = $ask_com->delete($id);
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Ask/see_ask_info','',1,"<script>alert('删除失败')</script>");
        }
    }

    /**
     * 回答详情批量删除
     */
    public function del_all_info(){
        $ask_com = M('ask_com');
        $id = I('id');
        $ids = I('ids');
        $ids_str = implode(',',$ids);
        $result = $ask_com->delete($ids_str);
        if($result){
            $this->redirect('Ask/see_ask_info',array('id'=>$id),1,"<script>alert('删除成功')</script>");
        }else{
            $this->redirect('Ask/see_ask_info',array('id'=>$id),1,"<script>alert('删除失败')</script>");
        }

    }

    /**
     * 查看回答详情
     */
    public function see_com_info(){
        $ask_com = M('ask_com');
        $user = M('user');
        $id = I('id');
        $info = $ask_com->where(array('id'=>$id))->find();
        $info['user_id'] = $user->where(array('id'=> $info['user_id']))->getField('name');
        $this->assign('info',$info);
        $this->display();
    }
}

