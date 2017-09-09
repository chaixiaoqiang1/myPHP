<?php
namespace Admin\Controller;
use Think\Controller;

/*
 * 评论信息管理
 */
class CommentController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = M('ty_comment');
    }
    //  评论列表页
    public function commentlist(){
        $where[id] = array('neq',1);
        $count = $this->model->where($where)->count();
        $Page= new \Think\Page($count,5);
        setPage($Page);
        $show = $Page->show();
        $comment = M('ty_comment');
        $data = $comment->field('id,grade,content,s_id,addtime,is_status,d_id')->limit($Page->firstRow.','.$Page->listRows)->select();
        $sick = M('ty_sick');
        $doctor = M('ty_doctor');
        foreach($data as $k=>&$v){
            $v['s_id'] =  $sick->where(array('id'=> $v['s_id']))->getField('username');
            $v['d_id'] = $doctor->where(array('id'=> $v['d_id']))->getField('username');
        }
        $this->assign('page',$show);
        $this->assign('info',$data);
        $this->display();
    }

    // 删除评论
    public function del(){
        if(IS_GET){
            $id = I('id');
            $result = $this->model->where(array('id'=> $id))->delete();
            if($result){
                $this->redirect('commentlist');
            }
        }
    }

    /*
     * 修改评论
     */
    public function edit(){
        $id = I('id');
        if(IS_POST){
//            var_dump($_POST);die;
            $data = $this->model->create(I('post.'));
            $result = $this->model->save($data);
            if($result){
                $this->redirect('Comment/commentlist');die;
            }else{
                echo "<script>alert('修改失败')</script>";die;
            }
        }
        $info = $this->model->where(array('id'=> $id))->find();
        $info['s_id'] = M('ty_sick')->where(array('id'=> $info['s_id'] ))->getField('username');
        $info['d_id'] = M('ty_doctor')->where(array('id'=> $info['d_id'] ))->getField('username');
//        var_dump($info);die;
        $this->assign('info',$info);
        $this->display();
    }

    /*
     * 状态修改
     */
    public function states(){

        if(IS_GET){
            $id = I('id');
            $sta = $this->model->where(array('id'=> $id))->getField('is_status');
            if($sta == 1){
                $data = $this->model->where(array('id'=> $id))->setField('is_status',2);
                if($data) {
                    $this->redirect('commentlist');
                }
            }else{
                $data['is_status'] = 1;
                $data = $this->model->where(array('id'=> $id))->setField('is_status',1);
                if($data) {
                    $this->redirect('commentlist');
                }
            }
        }
    }
}











