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
        $map['is_status'] = array('in','3,4');
        $count=M('ty_service')->where($map)->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $service = M('ty_service');
        $info = $service->where($map)->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $sick = M('ty_sick');
        foreach($info as $k=>&$v){
            $v['s_id'] = $sick->where(array('id'=> $v['s_id']))->getField('username');
        }
//        var_dump($info);die;
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
        $info = $server->where(array('id'=> $id))->getField('is_status');
//        var_dump($info);die;

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
    public function servicedel(){
        $id = I('id');
        $service = M('ty_service');
        $result = $service->where(array('id'=> $id))->delete();
        if($result){
            $this->redirect("Service/servicelist",'',1,"<script>alert('删除成功！')</script>");
        }else{
            $this->redirect("Service/servicelist",'',1,"<script>alert('删除失败！')</script>");
        }
    }
    // 图文信息
    public function imagetextlist(){

        $service = M('ty_service');
        $count = $service->where(array('type'=> 1))->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $info = $service->where(array('type'=> 1))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $sick = M('ty_sick');
        foreach($info as $k=>&$v){
            $v['s_id'] = $sick->where(array('id'=> $v['s_id']))->getField('username');
        }
        $did = I('id');
        if($did){
            $did = I('id');
            $count = $service->where(array('d_id'=>$did,'type'=>1))->count();
            $Page= new \Think\Page($count,15);
            setPage($Page);
            $show= $Page->show();
            $info = $service->where(array('type'=> 1,'d_id'=>$did))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            $sick = M('ty_sick');
            foreach($info as $k=>&$v){
                $v['s_id'] = $sick->where(array('id'=> $v['s_id']))->getField('username');
            }
        }
        $this->assign('page',$show);
        $this->assign('info',$info);
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
        $service = M('ty_service');
        $count = $service->where(array('type'=> 2))->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $info = $service->where(array('type'=> 2))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $sick = M('ty_sick');
        foreach($info as $k=>&$v){
            $v['s_id'] = $sick->where(array('id'=> $v['s_id']))->getField('username');
        }
        $this->assign('page',$show);
        $this->assign('info',$info);
        $this->display();
        /*$orderconsult = M('ty_orderconsult');
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
        }*/
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
        $did = I('id');
//        $did = 3;
        $orderset = M('ty_orderset');

        $info = $orderset->where(array('d_id'=> $did))->order('week asc')->select();
        $renshu = $info[0]['numpeople'];

        $price = M('ty_doctor')->where(array('id'=>$did))->getField('orderprice');
        foreach($info as $k=> &$v){
            $list[$v['week']]['week']=$v['week'];
                $list[$v['week']][]=$v;
        }
        foreach($list as $k=>$v){
            $arr[]=$k;
        }
        $str='1,2,3,4,5,6,7';
        foreach(explode(',',$str) as $k=>$v){
            if(!in_array($v,$arr)){
               $list[$v]=array();
                $list[$v]['week']=$v;
            }
        }
        $fieldArr = array();
        foreach ($list as $k => $v) {
            $fieldArr[$k] = $v['week'];
        }
        array_multisort($fieldArr, SORT_ASC, $list);
        foreach($list as $k=>$v){
               switch($v['week']){
                        case '1':
                            $v['week'] = "星期一";
                            break;
                        case '2':
                            $v['week'] = "星期二";
                            break;
                        case '3':
                            $v['week'] = "星期三";
                            break;
                        case '4':
                            $v['week'] = "星期四";
                            break;
                        case '5':
                            $v['week'] = "星期五";
                            break;
                        case '6':
                            $v['week'] = "星期六";
                            break;
                        default:
                            $v['week'] = "星期七";
                            break;
                    }

            $list[$v['week']]=$v;
            unset( $list[$k]);
        }
        $this->assign('price',$price);
        $this->assign('renshu',$renshu);
        $this->assign('info',$list);
        $this->display();
    }

    //预约设置修改
    public function ordersetedit($id){
        $orderconsult = M('ty_orderset');
        $info = $orderconsult->where(array('id'=> $id))->find();
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

    /*聊天信息*/
    public function liaotian(){
        $get = I('get.');
        $doctor = M('ty_doctor');
        $sick = M('ty_sick');
        $info = M("ty_consult")->where(array('f_id'=> $get['fid']))->select();
        foreach($info as $k=> &$v){
            $v['d_id'] =  $doctor->where(array('id'=>$v['d_id'] ))->getField('image');
            $v['s_id'] =  $sick->where(array('id'=>$v['s_id'] ))->getField('icon');
        }
//        var_dump($info);die;
        $this->assign('info',$info);
        $this->display();
    }


    /*免费咨询*/
    public function free(){
        $service = M('ty_service');
        $count = $service->where(array('type'=> 3))->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $info = $service->where(array('type'=> 3))->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $sick = M('ty_sick');
        foreach($info as $k=>&$v){
            $v['s_id'] = $sick->where(array('id'=> $v['s_id']))->getField('username');
        }
//        var_dump($info);die;
        $this->assign('page',$show);
        $this->assign('info',$info);
        $this->display();
    }

}








