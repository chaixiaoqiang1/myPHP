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
          $data = $sick -> select();
//          var_dump($data);
          $this->assign('list',$data);
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
     public function insertUser(){
                  
        $user = M("ty_sick");        
        
         if (IS_POST) {
         $data = $user->create(I('post.'));
            $data['addtime'] = time();  

            if($user->add($data))
                $this->success ('Sick/index');
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
        
//        var_dump($data);die; 
      
        if (IS_POST) {
             // var_dump($_POST);die;
            $data = $user->create(I('post.',2));
            $sc = $user->where("id = $id")->save($data);
            if ($sc) {
                $this->success("数据添加成功","Sick/index");
            }
        }
          $this->assign('data',$info);
          $this->display();
         
     }


    // 更多信息
    public function sickinfo($id){
        $user = M("ty_sick");
        $info = $user->where("id = $id")->find();
        $this->assign("info",$info);
        $this->display();
    }


    // 咨询历史
    public function historylist($id){
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

}











