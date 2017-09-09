<?php
namespace Admin\Controller;
use Monolog\Handler\MailHandler;
use Think\Controller;
use Think\Image;
use Think\Think;

class TrainController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = M('train');
    }
    /**
     * 车主培训课
     */
    public function index()
    {
        $count = $this->model->count();
        $Page = new \Think\Page($count,15);
        setPage($Page);
        $show = $Page->show();
        $info = $this->model->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('count',$count);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 培训信息添加
     */
    public function add(){
        if(IS_POST){
            $upload = upload();
            $info = $upload->upload();
//            $img_list=$this->public_upload($_FILES);
            $data = $this->model->create(I('post.'));
            $data['addtime'] = date("Y-m-d H:i:s",time());
            if($_FILES['path']['error'] == 0) {
                $data['path'] = './Public/' . $info['path']['savepath'] . $info['path']['savename'];
            }
            $result = $this->model->add($data);
            if($result){
                $this->redirect('Train/index','',1,"<script>alert('添加成功')</script>");
            }else{
                $this->redirect('Train/add','',1,"<script>alert('添加失败')</script>");
            }

        }
        $this->display();
    }

    /**
     * 培训信息修改
     */
    public function edit(){
        if(IS_POST){
            $upload = upload();
            $info = $upload->upload();
//            $img_list=$this->public_upload($_FILES);
            $data = $this->model->create(I('post.'));
            if($_FILES['path']['error'] == 0){
                $data['path'] = './Public/'.$info['path']['savepath'].$info['path']['savename'];
            }
            $result = $this->model->save($data);
            if($result){
                $this->redirect('Train/index','',1,"<script>alert('修改成功')</script>");
            }else{
                $this->redirect('Train/edit','',1,"<script>alert('修改失败')</script>");
            }
        }
        $id = I('id');
        $info = $this->model->where(array('id'=>$id))->find();
        $this->assign('info',$info);
        $this->display();
    }

    /**
     * 培训状态设置
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
            $this->redirect('Train/index','',1,"<script>alert('修改失败')</script>");
        }
    }

    /**
     * 培训信息删除
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
     * 培训指量删除
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

}

