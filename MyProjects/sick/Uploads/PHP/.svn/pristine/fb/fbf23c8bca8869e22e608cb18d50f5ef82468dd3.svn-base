<?php
namespace Admin\Controller;
use Think\Controller;
/*
 * 
 * 医生信息
 */
class UserController extends CommonController
{
/*
*医生用户信息列表
*/
 public function doctorlist(){

    $where=array();
    $count=M('ty_doctor')->where($where)->count();
    $Page= new \Think\Page($count,15);
    setPage($Page);
    $show= $Page->show();

    $list=M('ty_doctor')->limit($Page->firstRow.','.$Page->listRows)->select();
    $cat = M('ty_category')->select();
     foreach($list as $k1=> $v1){
            foreach($cat as $k2=>$v2){
                if( $list[$k1]['office'] == $cat[$k2]['id']){
                    $list[$k1]['office'] = $cat[$k2]['catname'];
                }
            }
     }
     $this->assign('page',$show);
     $this->assign('list',$list);
     $this->display();
 }
 
 
 //  添别医生 
 public function add(){
     $doctor = M('ty_doctor');
     $ke = M('ty_category');
     $keshi = $ke->order("id asc")->select();
     if (IS_POST) {
         //  头像上传部分
         $upload = new \Think\Upload();// 实例化上传类
         $upload->maxSize   =     3145728 ;// 设置附件上传大小
         $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
         $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
         $upload->savePath  =     ''; // 设置附件上传（子）目录
         // 上传文件
         $info   =   $upload->upload();
         if(!$info) {// 上传错误提示错误信息
             $this->error($upload->getError());
         }else{// 上传成功
             $this->success('上传成功！');
         }
         $data = $doctor->create(I('post.'));
         $data['image'] = $info['image']['savepath'].$info['image']['savename'];
          $data['addtime'] = time();
          $doctor->add($data);
     }
     $this->assign('keshi',$keshi);
     $this->display();
 }
 
 // 删除医生 
public function del(){
    if(IS_GET){
        $id= $_GET['id'];
        $del = M('ty_doctor')->where("id = $id")->delete();
        if($del){
            $this->success('删除成功', U("User/doctorlist"));
        }
    }
}

 // 修改医生数据 
public function edit(){
        $id = $_GET['id'];
        $doctor = M('ty_doctor');
        $info = $doctor->where("id = $id")->find();
//        var_dump($info);die;

        $catinfo = M('ty_category')->select();
        $this->assign('catinfo',$catinfo);
        $this->assign("info",$info);
        if(IS_POST){
            $data = $doctor->create(I('post.',2));
            $addinfo = $doctor->add($data);
            if($addinfo){
                $this->success('数据修改成功', U("User/doctorlist"));
            }
        }
        $this->display();
}

 /*
 *医生科室分类列表
 */
     public function specialtylist(){
        $list=M("ty_category")->order("id asc")->select();
        $this->assign('category',$list);
        $this->display();
     }

    // 添加分类
    public function specialtyadd(){
        if(IS_POST){
            $spec =  M("ty_category");
            $data = $spec->create(I("post."),1);
            $addinfo = $spec->add($data);
            if($addinfo){
                $this->success('数据添加成功', U("User/specialtylist"));
            }
        }
        $this->display();
    }

    // 修改分类
    public function specialtyedit(){
        $id = $_GET['id'];
        $spec =  M("ty_category");
        $info = $spec-> where("id = $id")->order("id asc")->find();
        $this->assign('info',$info);
        if(IS_POST){
           $data = $spec->create(I('post.'),2);
           $save =  $spec->where("id = $id")->save($data);
            if($save){
                $this->success('数据修改成功', U("User/specialtylist"));
            }
        }
        $this->display();
    }

    // 删除分类
    public function specialtydel(){
        if(IS_GET){
            $id = I("get.id");
            $spec =  M("ty_category");
            $delcat = $spec->where("id = $id")->delete();
            if($delcat){
                $this->success("数据删除成功", U("User/specialtylist"));
            }
        }
    }


    // 更多信息列表
    public function infolist($id){
        $doctor = M('ty_doctor');
        $info = $doctor->where("id = $id ")->field("speciality,intro")->find();
        $this->assign("info",$info);
        $this->display();
    }

}

