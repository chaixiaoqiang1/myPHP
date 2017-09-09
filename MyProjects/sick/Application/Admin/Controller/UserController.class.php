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
//     var_dump($list);die;
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
         //$img = uploadfile();
         $data = $doctor->create(I('post.'),1);
         $data['speciality'] = trim($data['speciality']);
         $data['intro'] = trim($data['intro']);

//         $data['image'] = $img['image']['savepath'].$img['image']['savename'];
          $data['addtime'] = time();
       //var_dump($_data);die;
          $doctor->add($data);
     }
     $this->assign('keshi',$keshi);
     $this->display();
 }


    //  文件上传
    public function uploadfile(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
//            $this->error($upload->getError());
        }else{// 上传成功
            return $info;
        }
    }

 // 删除医生 
public function del(){
    if(IS_GET){
        $id= $_GET['id'];
        $del = M('ty_doctor')->where(array('id'=> $id))->delete();
        if($del){
//            $this->success('删除成功', U("User/doctorlist"));
            $this->redirect('User/doctorlist','',1,"<script>alert('删除成功')</script>");
        }
    }
}

 // 修改医生数据 
public function edit(){
        $id = $_GET['id'];
        $doctor = M('ty_doctor');
        $info = $doctor->where("id = $id")->find();

        $catinfo = M('ty_category')->select();
        $this->assign('catinfo',$catinfo);
        $this->assign("info",$info);
        if(IS_POST){
            $data = $doctor->create(I('post.',2));
            $data['intro'] = trim($data['intro']);
            $data['speciality'] = trim($data['speciality']);

            $addinfo = $doctor->where(array('id'=> $id))->save($data);
            if($addinfo){
//                $this->success('数据修改成功', U("User/doctorlist"),0);
                    $this->redirect('User/doctorlist','',1,"<script>alert('修改成功')</script>");
            }else{
                    $this->redirect('User/edit',array('id'=>$id),'',1,"<script>alert('修改失败')</script>");
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
//                $this->success('数据添加成功', U("User/specialtylist"),'0');
                $this->redirect('User/specialtylist','',1,"<script>alert('添加成功')</script>");
            }else{
                $this->redirect('User/specialtyadd','',1,"<script>alert('添加失败')</script>");
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
//                $this->success('数据修改成功', U("User/specialtylist"));
                $this->redirect('User/specialtylist','',1,"<script>alert('修改成功')</script>");
            }else{
                $this->redirect('User/specialtylist','',array('id'=> $id),1,"<script>alert('修改失败')</script>");
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
//                $this->success("数据删除成功", U("User/specialtylist"));
                $this->redirect('User/specialtylist','',1,"<script>alert('删除成功')</script>");
            }else{
                $this->redirect('User/specialtylist','',1,"<script>alert('删除失败')</script>");
            }

        }
    }


    // 更多信息列表
    public function infolist($id){
        $doctor = M('ty_doctor');
//        $service = M('ty_service');
//        $data = $service->where(array('d_id'=>$id))->select();
        $info = $doctor->where("id = $id ")->find();
        $this->assign("info",$info);
        $this->display();
    }


    // 修改审核状态
    public function shenghe($id){
        $doctor = M('ty_doctor');
        $info = $doctor->where(array("id"=> $id))->field("id,is_status")->find();

            if($info['is_status']  == 1){
                $data['is_status'] = '2';
                $st = $doctor->where(array('id'=>$id))->save($data);
                if($st){
                    $this->success('用户被冻结',U('User/doctorlist'));
                }
            }else if($info['is_status']  == 2){
                $data['is_status'] = '1';
                $st = $doctor->where(array('id'=>$id))->save($data);
                if($st){
//                    $this->success('用户恢复正常',U('User/doctorlist'));
                    $this->redirect('User/doctorlist');
                }else{
                    $this->redirect('User/doctorlist','',1,"<script>alert('状态更改失败')</script>");
                }
            }
    }


    // 个人服务信息
    public function serviceinfo(){
        $service =  M("ty_service");
        if(IS_GET){
            $did = $_GET['id'];
            $data = $service->where(array('d_id'=>$did))->select();
        }
    }

    // 医生搜索
    public function searchdoctor(){
        $username = I('username');
        $doctor =  M("ty_doctor");
        $map['username'] = array('like',"{$username}%");
        $info = $doctor->where($map)->select();
        $this->assign('list',$info);
        $this->display('User/doctorlist');
    }

}

