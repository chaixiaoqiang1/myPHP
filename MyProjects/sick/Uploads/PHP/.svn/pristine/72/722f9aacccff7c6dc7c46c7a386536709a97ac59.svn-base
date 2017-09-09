<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $where[id] = array('neq',1);
        $count=M('Admin')->where($where)->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $admin_list=M('Admin')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($admin_list as $k=>$v){
            $group_id = M('auth_group_access')->where("uid = $v[id]")->getField("group_id");
            $group_id = $group_id?$group_id:0;
            $admin_list[$k]['title'] = M('auth_group')->where("id = $group_id")->getField("title");
        }

//        var_dump($show,$admin_list);
        
        $this->assign('page',$show);
        $this->assign('admin_list',$admin_list);
        $this->display();
    }
    public function editor(){
        $id=I('get.id');
        $list=M('Admin')->where(array('id'=>$id))->find();
        if(empty($list)){
            $this->error('获取信息失败');exit;
        }
        $auth = M('auth_group')->where("type = '{$list[type]}'")->field("id,title")->select();
        $group = M('auth_group_access')->where("uid = '{$id}'")->field("group_id")->find();
        $this->assign('auth',$auth);
        $this->assign('group',$group);
        $this->assign('list',$list);
        $this->display();
    }
    public function update(){
        if(IS_POST){
            $post=I('post.');
//            $validate= array(
//                array('username', '/^[0-9a-zA-Z]+$/', '用户名只限数字和字母', 1, 'regex', 3),
//                array('username','','用户名已经存在！',0,'unique',3),
//            );
            if($post['password']){
                $validate[]=  array('password', '6,18', '密码必须为6-18位', 1, 'length');
                $validate[]=  array('password', '/^[0-9a-zA-Z]+$/', '密码只限数字和字母', 1, 'regex', 3);
            }
            if(!M('Admin')->validate($validate)->create($post)){
                $this->error(M('Admin')->getError());exit;
            }

            if($post['password']){
                $post['password']=md5($post['password']);
            }else{
                unset($post['password']);
            }
            $row=M('Admin')->data($post)->save();
            $group = I('post.group');
            $gr = M('auth_group_access')->where("uid = $post[id]")->find();
            if($gr){
                $r=M('auth_group_access')->where("uid = $post[id]")->setField('group_id',$group);
            }else{
                $r=M('auth_group_access')->add(array('uid'=>$post[id],'group_id'=>$group));
            }
            if($row || $r){
                $this->success('修改成功',U('Admin/index'));die;
            }else{
                $this->error('修改失败');die;
            }
        }
    }

    public function status(){
        $id=I('id');
        $list=M('Admin')->where(array('id'=>$id))->find();
        if(empty($list)){
            $this->error('获取信息失败');
        }
        if($list['status'] == 1){
            $status=0;
        }else{
            $status=1;
        }
        $row=M('Admin')->where('id='.$id)->data(array('status'=>$status))->save();
        if($row){
            $this->success('操作成功',U('Admin/index'),1);die;
        }else{
            $this->error('操作失败');die;
        }
    }
    public function delete(){
        $id=I('id');
        $row=M('Admin')->where('id='.$id)->delete();
      
        if($row){
             M('auth_group_access')->where(array('uid'=>$id))->delete();
            $this->success('操作成功',U('Admin/index'),1);die;
        }else{
            $this->error('操作失败');die;
        }
    }

    public function add(){
        if(IS_POST){
            $post = I('post.');
            if(!$post['group']){
                  $this->error('没有选择用户组');die;
            }
            if($post[password] != $post[repassword]){
                 $this->error('两次密码不一致');die;
            }
            $row=D('admin')->update();
            if($row){
                    M('auth_group_access')->add(array('uid'=>$row,'group_id'=>$post['group']));
                    $this->success('添加管理员成功',U('Admin/index'));die;
            }else{
                $error = D('admin')->getError();
                $this->error(empty($error) ? '未知错误！' : $error);exit;
            }
        }else{
            $shop =M('shop')->where(array('status'=>1))->field('shop_name,id')->select();
            $type = I('get.type');
            $auth = M('auth_group')->where("type = $type")->field("id,title")->select();
            $this->assign('shop',$shop);
            $this->assign('auth',$auth);
            $this->display();
        }
    }
}