<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController
{

    public function __construct()
    {
        parent::__construct();
        $uid = $_SESSION['admin_id'];
        $this->model = M('ty_manager');
        $this->rolemodel = M('ty_role');
        $this->authmodel = M('ty_auth');
    }

    public function index(){
//        $where[id] = array('neq',1);
        $count= $this->model->where($where)->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $admin_list=$this->model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($admin_list as $k=>&$v){
             if($v['id'] != 1){
                 $v['role_id'] = $this->rolemodel->where(array('id'=>$v['role_id'] ))->getField('auth_all_ids');
                 $v['role_id'] = explode(',',$v['role_id']);
                 foreach($v['role_id'] as $k1=>$v1){
                     $v['role_id'][$k1] = $this->authmodel->where(array('id'=> $v1))->field('id,title')->find();
                 }
             }
        }
        $this->assign('page',$show);
        $this->assign('admin_list',$admin_list);
        $this->display();
    }

    /*
     * 添加管理员
     */
    public function addManager(){
        $info = $this->rolemodel->field('id,rolename')->select();
        if(IS_POST){
            $data = $this->model->create(I('post.'));
            $data['password'] = md5(trim($data['password']));
            $data['addtime'] = time();
            $result = $this->model->add($data);
            if($result){
                $this->redirect('Admin/index');
            }else{
                echo "<script>alert('添加失败');</script>";die;
            }
        }
        $this->assign('info',$info);
        $this->display();
    }

    /*
     * 修改管理员信息
     */
    public function editManager(){
        if(IS_POST){
            $post = $_POST;
            if($post['password']){
                $post['password']=md5($post['password']);
            }else{
                unset($post['password']);
            }

            $result =  M('ty_manager')->save($post);
            if($result){
                $this->redirect('Admin/index');
            }else{
                $this->error('修改失败');
            }
        }else{
            $id = I('id');
            $info = $this->model->where(array('id'=>$id))->find();
            $role = $this->rolemodel->field('id,rolename')->select();
            $this->assign('role',$role);
            $this->assign('info',$info);
            $this->display();
        }

    }
    /*
     * 删除管理员
     */
    public function delManager(){
        $id = I('id');
        $result = $this->model->delete($id);
        if($result){
            $this->redirect('Admin/index');
        }
    }


    /*
     *  角色列表
     */
    public function rolelist(){
        $info = $this->rolemodel->select();
        foreach($info as $k=>$v){
            $info[$k]['auth_all_ids'] = explode(',',$v['auth_all_ids']);
            foreach($info[$k]['auth_all_ids'] as $k1=>&$v1){
                $v1 = M('ty_auth')->where(array('id'=> $v1))->field('id,title')->find();
            }
        }
        $this->assign('info',$info);
        $this->display();
    }

    /*
     *  角色添加
     */
    public function roleadd(){
        $info = $this->authmodel->field('id,title')->select();
        if(IS_POST){
            $post = $_POST;
            $post['auth_all_ids'] = implode(',',$post['auth_all_ids'] );
            $result = $this->rolemodel->add($post);
            if($result){
                $this->redirect('Admin/rolelist');
            }
        }
        $this->assign('info',$info);
        $this->display();
    }

    /*
    *  角色修改
    */
    public function roleedit(){
        if(IS_POST){
            $post = $_POST;
            $post['auth_all_ids'] = implode(',',$post['auth_all_ids']);
            $result = $this->rolemodel->save($post);
            if($result) {
                $this->redirect('Admin/rolelist');
            }else{
                $this->error('修改失败');
            }
        }
        else{
            $id = I('id');
            $info = $this->rolemodel->where(array('id'=>$id))->find();
            $info['auth_all_ids'] = explode(',',$info['auth_all_ids']);
            $auth = $this->authmodel->field('id,title')->select();
            $this->assign('auth',$auth);
            $this->assign('info',$info);
            $this->display();
        }

    }
    /*
   *  角色删除
   */
    public function roledel(){
        $id = I('id');
        $result = $this->rolemodel->delete($id);
        if($result){
            $this->redirect('Admin/rolelist');
        }
    }

    /*
     *  权限列表
     */
    public function authlist(){

       /* $count = $this->authmodel->count();
        $Page= new \Think\Page($count,5);
        setPage($Page);
        $show= $Page->show();*/

        $info = $this->authmodel->order('id asc')->order("auth_path")->select();
        $this->assign('ge','---/');
        $this->assign('page',$show);
        $this->assign('info',$info);
        $this->display();
    }
    /*
   *  权限添加
   */
    public function authadd(){

    }

    /*
    *  权限修改
    */
    public function authedit(){


    }
    /*
    *  权限删除
    */
    public function authdel(){
        $id = I('id');
        $result = $this->authmodel->delete($id);
        if($result){
            $this->redirect('Admin/authlist');
        }
    }

    /*提现管理*/
    public function getprice(){
        $info = M('ty_money')->select();
        $sick = M('ty_sick');
        $doctor = M('ty_doctor');
        foreach($info as $k=>&$v){
            if($v['type'] == 1){
                $v['userid'] = $sick->where(array('id'=>$v['userid']))->field('id,username,balance')->find();
            }elseif($v['type'] == 2){
                $v['userid'] = $doctor->where(array('id'=>$v['userid']))->field('id,username,balance')->find();
            }
        }
        $this->assign('info',$info);
        $this->display();
    }

    /*提现操作*/
    public function setprice(){
        if(IS_GET){
            $get = I('get.');
            if($get['type'] == 1){
                $result =  M('ty_money')->where(array('id'=> $get['id']))->setField('is_statu',2);
            }elseif($get['type'] == 2){
                $result = M('ty_money')->where(array('id'=> $get['id']))->setField('is_statu',3);
            }elseif($get['type'] == 3){
                $result = M('ty_money')->where(array('id'=> $get['id']))->delete();
            }
            if($result){
                $this->redirect('Admin/getprice','',1,"<script>alert('操作成功')</script>");
            }else{
                $this->redirect('Admin/getprice','',1,"<script>alert('操作失败')</script>");
            }
        }
    }

    public function zhifu(){
        $id = I('id');
        $result =  M('ty_money')->where(array('id'=> $id))->setField('is_statu',2);
        if ($result) {
            $info = M('ty_money')->where(array('id' => $id))->field('userId,type,price')->find();

            $price = (int)$info['price'];
//            var_dump($info);die;
            if ($info['type'] == 1) {
                $row=M('ty_sick')->where(array('id' => $info['userid']))->setDec('balance', $price);
                $num=M('ty_sick')->where(array('id' => $info['userid']))->setInc('withdraw', $price);
            } else{
                $row=M('ty_doctor')->where(array('id' => $info['userid']))->setDec('balance', $price);
                $num= M('ty_doctor')->where(array('id' => $info['userid']))->setInc('withdraw', $price);
            }
            if($row && $num){
                $this->redirect('Admin/getprice','',1,"<script>alert('操作成功')</script>");
            }else{
                $this->redirect('Admin/getprice','',1,"<script>alert('操作失败')</script>");
            }
        }else{
            $this->redirect('Admin/getprice','',1,"<script>alert('操作失败')</script>");
        }
    }

}