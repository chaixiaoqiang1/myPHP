<?php
namespace Admin\Controller;
use Think\Controller;

/*
 * 服务信息管理
 */
class ServiceController extends CommonController
{

    public function __construct()
    {
        parent::__construct();
    }

    // 服务信息列表
    public function servicelist(){
        $where[id] = array('neq',1);
        $count=M('ty_service')->where($where)->count();
        $Page= new \Think\Page($count,5);
        setPage($Page);
        $show= $Page->show();
        $service = M('ty_service');
        $info = $service->join('ty_doctor ON ty_service.d_id = ty_doctor.id')->field('ty_service.*,ty_service.id as serviceid,ty_service.is_status as ais_status,ty_doctor.*')->limit($Page->firstRow.','.$Page->listRows)->select();
        $id = I('get.id');
        if($id){
            $info =$service->join('ty_doctor ON ty_service.d_id = ty_doctor.id')->field('ty_service.*,ty_service.id as serviceid,ty_service.is_status as ais_status,ty_doctor.*')->where(array('d_id'=> $id))->limit($Page->firstRow.','.$Page->listRows)->select();
        }
        $this->assign('page',$show);
        $this->assign('list',$info);
        $this->display();
    }

    // 修改服务信息
    public function serviceedit($id){
        $service = M('ty_service');
        $info = $service->where(array('id'=> $id))->find();
        if(IS_POST){
            $data = $service->create(I('post.'),2);
            $service->where(array('id'=>$id))->save($data);
        }
        $this->assign('info',$info);
        $this->display();
    }

    // 服务状态更改
    public function servicestatus(){
        $id = I('get.id');
        $server = M('ty_service');
        $info = $server->where(array('id'=> $id))->find();
        $did = $info['d_id'];
        if($info['is_status'] == 1 ){
            $info['is_status'] = 2;
        }else{
            $info['is_status'] = 1;
        }
        $data['is_status'] =  $info['is_status'];
        $row = $server->where(array('id'=> $id))->save($data);
        if($row){
            $this->success('操作成功',U('Service/servicelist',array('id'=> $did)),1);die;
        }else{
            $this->error('操作失败');die;
        }
    }

    // 删除服务信息
    public function servicedel($id){
        $service = M('ty_service');
        if(!empty($id)){
            $data = $service->where(array('id'=>$id))->delete();
            if($data){
                $this->success("数据删除成功", U("Service/servicelist"));
            }
        }
    }

    // 图文信息
    public function imagetextlist(){
        $imagetext = M('ty_imagetext');
        $info = $imagetext->join("ty_doctor ON ty_imagetext.d_id = ty_doctor.id")->field('ty_imagetext.*,ty_doctor.username,ty_doctor.office')->select();
        $id = I('get.id');
        if($id){
            $info = $imagetext->join("ty_doctor ON ty_imagetext.d_id = ty_doctor.id")->field('ty_imagetext.*,ty_doctor.username,ty_doctor.office')->where(array('d_id'=> $id))->select();
        }
        $this->assign('list',$info);
        $this->display();
    }

    // 修改图文信息
    public function imagetextedit($id){
        $imagetext = M('ty_imagetext');
        $info = $imagetext->where(array('id'=> $id))->find();
        if(IS_POST){
            $data = $imagetext->create(I('post.'),2);
            $imagetext->where(array('id'=> $id))->save($data);
        }
        $this->assign('info',$info);
        $this->display();
    }

    // 删除图文信息
    public function imagetextdel($id){
        $imagetext = M('ty_imagetext');
        $imagetext->where(array('id' => $id))->delete();
    }

    // 图文状态变换
    public function imagetextstatus(){
        $id = I('get.id');
        $imagetext = M('ty_imagetext');
        $info = $imagetext->where(array('id'=> $id))->find();
        $did = $info['d_id'];
        if($info['is_status'] == 1 ){
            $info['is_status'] = 2;
        }else{
            $info['is_status'] = 1;
        }
        $data['is_status'] =  $info['is_status'];
        $row = $imagetext->where(array('id'=> $id))->save($data);
        if($row){
            $this->success('操作成功',U('Service/imagetextlist',array('id'=> $did)),1);die;
        }else{
            $this->error('操作失败');die;
        }
    }

    // 预约信息
    public function orderlist(){
        $orderconsult = M('ty_orderconsult');
        $data = $orderconsult->join('ty_doctor ON ty_orderconsult.d_id = ty_doctor.id ')->field('ty_orderconsult.*,ty_doctor.office,ty_doctor.username')->select();
        extract($_GET);
        if(isset($id)){
            $data = $orderconsult->join('ty_doctor ON ty_orderconsult.d_id = ty_doctor.id ')->field('ty_orderconsult.*,ty_doctor.office,ty_doctor.username')->where(array('d_id'=> $id))->select();
        }
        $this->assign('list',$data);
        $this->display();
    }
    // 预约信息修改
    public function orderedit($id){
        $orderconsult = M('ty_orderconsult');
        $info = $orderconsult->where(array('id'=> $id))->find();
        if(IS_GET){
            $data = $orderconsult->create(I('post.'),2);
            $orderconsult->where(array('id'=> $id))->save($data);
        }
        $this->assign('info',$info);
        $this->display();
    }
    // 预约信息删除
    public function orderdel($id){
        $orderconsult = M('ty_orderconsult');
        if(IS_GET){
            $orderconsult->where(array('id'=> $id))->delete();
        }
    }
     // 预约状态更改
    public function orderstatus(){
        $id = I('get.id');
        $order = M('ty_orderconsult');
        $info = $order->where(array('id'=> $id))->find();
        $did = $info['d_id'];
        if($info['is_status'] == 1 ){
            $info['is_status'] = 2;
        }else{
            $info['is_status'] = 1;
        }
        $data['is_status'] =  $info['is_status'];
        $row = $order->where(array('id'=> $id))->save($data);
        if($row){
            $this->success('操作成功',U('Service/orderlist',array('id'=> $did)),1);die;
        }else{
            $this->error('操作失败');die;
        }
    }
    // 预约设置列表
    public function orderset(){
        $orderconsult = M('ty_orderset');
        $info = $orderconsult->join("ty_doctor ON ty_orderset.d_id = ty_doctor.id")->field('ty_orderset.*,ty_doctor.office,ty_doctor.username')->select();
        $this->assign('list',$info);
        $this->display();
    }
    //预约设置修改
    public function ordersetedit($id){
        $orderconsult = M('ty_orderset');
        $info = $orderconsult->where(array('id'=> $id))->find();
//        var_dump($info);die;
        if(IS_GET){
            $data = $orderconsult->create(I('post.'),2);
            $orderconsult->where(array('id'=> $id))->save($data);
        }
        $this->assign('info',$info);
        $this->display();
    }
    //预约设置删除
    public function ordersetdel($id){
        $orderconsult = M('ty_orderset');
        if(IS_GET){
            $orderconsult->where(array('id'=> $id))->delete();
        }
    }

    // 咨询内容列表
    public function consultcontent(){
        $consult = M('ty_consult');
        $data = $consult->select();
        if(IS_GET){
            extract($_GET);
            $data = $consult->where(array('f_id'=> $id))->select();
        }
        $this->assign('info',$data);
        $this->display();
    }


    //用户评论
    public function sickcomment(){
        $comment = M('ty_comment');
        if(IS_GET){
            extract($_GET);
            $info = $comment->where(array('d_id'=> $id ))->select();
            $sick = M('ty_sick');
            foreach($info as &$v){
               $v['s_id'] = $sick->where(array('id'=> $v['s_id']))->field('username')->find();
            }
        }
        $this->assign('list',$info);
        $this->display();
    }

    // 评论状态更改
    public function sickcommentstatus(){
        $id = I('id');
        $comment = M('ty_comment');
        $list = $comment->where(array('id'=>$id))->find();
        if($list['is_status'] == 1){
            $status=2;
        }else{
            $status=1;
        }
        $row = $comment->where(array('id'=> $id))->data(array('is_status'=>$status))->save();
        if($row){
            $this->success('操作成功',U('Service/sickcomment',array('id'=> $list['d_id'])),1);die;
        }else{
            $this->error('操作失败');die;
        }
    }

    // 删除评论
    public function sickcommentdel($id){
        $comment = M('ty_comment');
        if(isset($id)){
            $comment->where(array('id'=> $id))->delete();
        }
    }

    //第三方意见
    public function threeidea(){
        $idea = M('ty_thirdparty');
        $data = $idea->select();
        $id = I('get.id');
        if($id){
            $data = $idea->where(array('f_id'=>$id))->select();
        }
        $doctor = M('ty_doctor');
        foreach($data as &$v){
            $v['d_id'] = $doctor->where(array('id'=> $v['d_id']))->field('username')->find();
        }
//        var_dump($data);die;
        $this->assign('list',$data);
        $this->display();
    }

    // 三方意见修改
    public function threeideaedit(){
        $this->display();
    }
    // 三方意见删除
    public function threeideadel(){
        $id = I('get.id');
        $comment = M('ty_thirdparty');
        if($id){
            $comment->where(array('id'=> $id))->delete();
        }
    }

    // 第三方状态更改
    public function threeideastatus(){
        $id = I('get.id');
        $comment = M('ty_thirdparty');
        $info = $comment->where(array('id'=>$id))->find();
        if($info['is_status'] == 1 ){
            $info['is_status'] = 2;
        }else{
            $info['is_status'] = 1;
        }
        $data['is_status'] = $info['is_status'];
        $result = $comment->where(array('id'=> $id))->save($data);
        if($result){
            $this->success('操作成功',U('Service/threeidea',array('id'=> $info['f_id'])),1);die;
        }else{
            $this->error('操作失败');die;
        }
    }

    // 好友圈
    public function allfriend(){
        $id = I('get.id');
        $friend = M('ty_doctorsick');
        $data = $friend->where(array('d_id'=> $id))->select();
        $doctor = M('ty_doctor');
        $sick = M('ty_sick');
        foreach($data as &$v){
            $v['d_id'] = $doctor->where(array('d_id'=> $v['d_id']))->field('username')->find();
            $v['s_id'] = $sick->where(array('s_id'=> $v['s_id']))->field('username')->find();
        }
        $this->assign('list',$data);
        $this->display();
    }

    //积分表


}








