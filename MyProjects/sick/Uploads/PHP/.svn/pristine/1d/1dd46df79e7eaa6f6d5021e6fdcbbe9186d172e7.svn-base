<?php
namespace Admin\Controller;
use Think\Controller;
class GroupController extends CommonController
{

    public function index(){
        $data=M('auth_group')->select();
        $this->assign('data',$data);
        $this->display();
    }
    public function save(){
         $data = I('post.');
         $result = M('auth_rule')->save($data);
         if($result){
            $this->success('修改成功',U('Private/index'));exit;
         }else{
            $this->error('修改失败');exit;
         }   
    }
    
    public function add(){
        if(IS_POST){
            $title = I('post.title');
            $rs = M('auth_group')->add(array('title'=>$title));
            if($rs){
                $this->success('添加成功',U('Group/index'));exit;
            }else{
                $this->error('添加失败');exit;
            }
        }
    }
    public function auth(){
        if(IS_POST){
               $ids = I('post.ids',',');
               $ids = trim($ids, ',');
               $id = I('post.id');
               $res = M('auth_group')->where("id = $id")->setField('rules',$ids);
                if($res){
                   $this->success('授权成功',U('Group/index'));exit;
                }else{
                   $this->error('授权失败');exit;
                }   
        }else{
                $id = I('get.id');
                $group = M('auth_group')->where("id = '{$id}'")->find();
                $rules = explode(',',$group['rules']);
                $data = M('auth_rule')->where("status=1")->select();
                foreach($data as $k=>$v){
                    if(in_array($v['id'],$rules)){
                        $data[$k]['checked'] = 1;
                    }
                }
             
                $this->assign('data',$data);
                $this->assign('group',$group);
                $this->display();
        }
    }

    public function delete(){
         $id = I('get.id');
         $result = M('auth_rule')->delete($id);
         if($result){
            $this->success('删除成功',U('Private/index'));exit;
         }else{
            $this->error('删除失败');exit;
         }   
    }
}