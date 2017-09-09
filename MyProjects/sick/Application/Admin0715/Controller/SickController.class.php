<?php
namespace Admin\Controller;
use Think\Controller;

/*
 * 病人信息管理
 * 
 */
class SickController extends CommonController
{

    public function __construct()
    {
        parent::__construct();
    }
    
/*
*用户信息列表
*/
    public function index(){
          $sick = M("ty_sick");
        $where = array();
        $count = $sick->where($where)->count();
        $Page= new \Think\Page($count,10);
        setPage($Page);
        $show= $Page->show();
//        var_dump($data);
        $list=M('ty_sick')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
          $this->assign('list',$list);
          $this->display();
    }
   
   // 删除病人
     public function delUser(){
         if (IS_GET) {
             $id = $_GET['id'];
             $user = M('ty_sick');
             if($user->where("id = $id")->delete()){
                 echo  "<script>history.go(-1);</script>";
             }
         }
     }
     
     //添加用户
     public function insertUser()
     {
         $user = M("ty_sick");
         if (IS_POST) {
             $data = $user->create(I('post.'));
             $data['addtime'] = time();
             $result = $user->add($data);
             if ($result) {
//              $this->success ('Sick/index');
                 $this->redirect('Sick/index', '', 1, '<script>alert("添加成功")</script>');
             } else {
                 $this->redirect('Sick/index', '', 1, '<script>alert("添加失败")</script>');
             }
         }
         $this->display();
     }
     /*
      * 修改页面
      */
     public function saveUser(){
        $id = $_GET['id'];
        $user = M("ty_sick");
        $info = $user->find($id);
        if (IS_POST) {
            $data = $user->create(I('post.',2));
            $result = $user->where("id = $id")->save($data);
            if ($result) {
//                $this->success("数据添加成功","Sick/index");
                $this->redirect('Sick/index','',1,"<script>alert('修改成功')</script>");
            }else{
                $this->redirect('Sick/index','',1,"<script>alert('修改失败')</script>");
            }
        }
         $this->assign('data',$info);
         $this->display();
     }


    // 更多信息
    public function sickinfo($id){
        $user = M("ty_sick");
        $info = $user->where("id = $id")->find();
//        var_dump($info);die;
        $this->assign("info",$info);
        $this->display();
    }

    // 咨询历史
    public function historylist(){
        $id = I('id');
        $hinfo = M('ty_sick')->where(array('id'=>$id))->find();
        $strinfo =  $history['collect'];
        // 医生id 数组
        $doctorarr = explode('|',$strinfo);
        $doctor = M("ty_doctor");
        $lastinfo = array();
        foreach($doctorarr as $k=>$v){
            $lastinfo[] = $doctor->where(array('id'=> $v))->find();
        }
        $this->assign("data",$lastinfo);
        $this->display();
    }

    // 用户状态更改
    public function sickstatus(){
        $id = I('get.id');
        $sick = M('ty_sick');
        $info = $sick->where(array('id'=> $id))->find();
        if($info['is_status'] == 1){
            $info['is_status'] = 2;
        }else{
            $info['is_status'] = 1;
        }
//        var_dump($info);die;
        $data['is_status'] = $info['is_status'];
        $row = $sick->where(array('id'=> $id))->save($data);
        if($row){
//            $this->success('操作成功',U('Sick/index',array('id'=> $info['id'])),1);die;
            $this->redirect('Sick/index',array('id'=> $info['id']),1,"<script>alert('修改失败')</script>");die;
        }else{
            $this->redirect('Sick/index','',1,"<script>alert('修改失败')</script>");
        }
    }

    //  病人搜索
    public function searchsick(){
        $username = I('username');
        $sick = M('ty_sick');
        $map['username'] = array('like',"{$username}%");
        $list = $sick->where($map)->select();
        $this->assign('list',$list);
        $this->display('Sick/index');
    }

}











