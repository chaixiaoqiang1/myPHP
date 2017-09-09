<?php
namespace Admin\Controller;
use Think\Controller;
class CommentController extends CommonController
{

    /**
     * 评车图片管理
     */
    public function index()
    {
        $count=M("evaluateImg")->count();
        $Page=new \Think\Page($count,5);
        $comment=M("evaluateImg")->limit($Page->firstRow,$Page->listRows)->select();
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $listShow=$Page->show();
        $this->assign("count",$count);
        $this->assign("comment",$comment);
        $this->assign("listShow",$listShow);
        $this->display();
    }

    /**
     * 评车图片添加
     */
    public function add(){
        $upload=upload();
        $Image=new \Think\Image();
        if($_POST!=NULL){
            $info=$upload->upload();
            $_POST['img']="./Public/".$info['goods_img']['savepath'].$info['goods_img']['savename'];
            $_POST['size']=$_POST['height'].'x'.$_POST['width'];
            $_POST['time']=date("Y-m-d H:i:s",time());
        }
        if($info){
            $Image->open($_POST['img']);
            $Image->thumb(I("post.width"),I("post.height"),\Think\Image::IMAGE_THUMB_FIXED)->save($_POST['img']);
        }
        if($info){
            $result=M("evaluateImg")->add($_POST);
            $this->redirect("Comment/index");
        }
        $this->display();
    }



    public function stop(){
        $id=I("post.id");
        $status=M("evaluateImg")->where("id=$id")->field("status")->find();
        if($status['status']==1){
            $result=M("evaluateImg")->where("id=$id")->data("status=0")->save();
        }else{
            $result=M("evaluateImg")->where("id=$id")->data("status=1")->save();
        }
        if($result){
            echo 1;
        }else{
            echo 0;
        }
    }

    public function delete(){
        $id=I("post.id");
        $result=M("evaluateImg")->where("id=$id")->delete();
        if($result){
            echo "OK";
        }
    }

    public function editor(){
        $id=I("get.id");
        if($_POST!=null){
            $upload=upload();
            $Image=new \Think\Image();
            $info=$upload->upload();
            if($info!=NULL){
                $_POST['img']="./Public/".$info['goods_img']['savepath'].$info['goods_img']['savename'];
            }
            $_POST['size']=$_POST['height'].'x'.$_POST['width'];
            $_POST['time']=date("Y-m-d H:i:s");

            $Image->open($_POST['img']);
            $Image->thumb(I("post.width"), I("post.height"),\Think\Image::IMAGE_THUMB_FIXED)->save($_POST['img']);


            M("evaluateImg")->where("id=$id")->save($_POST);
            $this->redirect("Comment/index");

        }
        $comment=M("evaluateImg")->where("id=$id")->find();
        $size=explode('x',$comment['size']);

        $this->assign("size",$size);
        $this->assign("comment",$comment);
        $this->display();
    }

    /**
     * 评车列表
     */
    public function info(){
        if(IS_POST) {
            if ($_POST['brands']) {
                $where['brands_id'] = $_POST['brands'];
            }
            if ($_POST['demio_id']) {
                $where['demio_id'] = $_POST['demio_id'];
            }
            if ($_POST['model_id']) {
                $where['model_id'] = $_POST['model_id'];
            }
            if ($_POST['time']) {
                $where['add_time'] = array('like', "{$_POST['time']}%");
            }
            $count = M('evaluate')->where($where)->count();
            $Page = new \Think\Page($count, 5);
            setPage($Page);
            $show = $Page->show();
            $info = M('evaluate')->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }else{
            $evalute = M("evaluate");
            $count = $evalute->count();
            $Page = new \Think\Page($count,15);
            setPage($Page);
            $show = $Page->show();
            $info = $evalute->limit($Page->firstRow.','.$Page->listRows)->select();
        }
        $user = M('user');
        $brands = M('brands');
        foreach($info as $k=>&$v){
             $v['user_id'] = $user->where(array('id'=>$v['user_id']))->getField('name');
             $v['brands_id'] = $brands->where(array('id'=>$v['brands_id']))->getField('name');
        }

        $brands_info = $brands->where(array('status'=> 1))->select();
        $this->assign('count',$count);
        $this->assign('page',$show);
        $this->assign('brands_info',$brands_info);
        $this->assign('info',$info);
        $this->display();
    }
    /**
     * 评车列表状态修改
     */
    public function statu_info(){
        $evalute = M("evaluate");
        $id = I('id');
        $status = $evalute->where(array('id' => $id))->getField('status');
        if($status == 1){
            $status = 2;
        }else{
            $status = 1;
        }
        $result = $evalute->where(array('id' => $id))->setField('status',$status);
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Question/info', '', 1, "<script>alert('修改失败')</script>");
        }
    }

    /**
     * 评车列表删除
     */
    public function del_info(){
        $evalute = M("evaluate");
        $id = I('id');
        $result = $evalute->delete($id);
        if($result){
            $this->redirect('Question/info', '', 1, "<script>alert('删除成功')</script>");
        }else{
            $this->redirect('Question/info', '', 1, "<script>alert('删除失败')</script>");
        }
    }

    /**
     * 评车列表批量删除
     */
    public function all_del_info(){
        if(IS_POST){
            $evalute = M("evaluate");
            $ids = I('ids');
            $ids_str = implode(',',$ids);
            $result = $evalute->delete($ids_str);
            if($result){
                $this->redirect('Question/info', '', 1, "<script>alert('批量删除成功')</script>");
            }else{
                $this->redirect('Question/info', '', 1, "<script>alert('批量删除失败')</script>");
            }
        }
    }

    /**
     *  评车项目列表
     */
    public function see(){
        $evaluate_info = M('evaluate_info');
        $id = I('id');
        $count = $evaluate_info->where(array('evaluate_id'=>$id))->count();
        $Page  = new \Think\Page($count,15);
        setPage($Page);
        $show = $Page->show();
        $info = $evaluate_info->where(array('evaluate_id'=>$id))->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('count',$count);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     *  评车项目删除
     */
    public function del_see(){
        $evaluate_info = M('evaluate_info');
        $id = I('id');
        $result = $evaluate_info->delete($id);
        if($result){
            $data = 'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Question/see', '', 1, "<script>alert('删除失败')</script>");
        }
    }

    /**
     *  评车项目状态修改
     */
    public function status_see(){
        $evaluate_info = M('evaluate_info');
        $id = I('id');
        $status = $evaluate_info->where(array('id'=> $id))->getField('status');
        if($status == 1){
            $status = 2;
        }else{
            $status = 1;
        }
        $result = $evaluate_info->where(array('id'=> $id))->setField('status',$status);
        if($result){
            $data =  'ok';
            $this->ajaxReturn($data);
        }else{
            $this->redirect('Question/see', '', 1, "<script>alert('修改失败')</script>");
        }
    }


    /***
     *   评车详情查看
     */
    public function see_info(){
        $evaluate = M('evaluate');
        $evaluate_two = M('evaluate_info');
        $param = M('param');
        $user = M('user');
        $id = I('id');
        $eid = I('eid');
        $grand_info = $evaluate_two->where(array('id'=>$id))->field('grand,content,img')->find();
        $evaluate_info = $evaluate->where(array('id'=> $eid))->find();
        $evaluate_info['user_id'] = $user->where(array('id'=> $evaluate_info['user_id'] ))->field('icon,name')->find();
        $param_info = $param->where(array('id'=> $id))->find();
//        var_dump($evaluate_info);die;
        $this->assign('grand',$grand_info);
        $this->assign('einfo',$evaluate_info);
        $this->assign('info',$param_info);
        $this->display();
    }


    /**
     * 搜索功能
     */
    public function query(){
        if(in_array('',$_POST)){
            echo 0;die;
        }
        if($_POST['type'] == 1){
            $where['brands_id']=$_POST['id'];
            $where['pid']=0;
        }else{
            $where['pid']=$_POST['id'];
        }
        $list=M('demio')->where($where)->select();
        if($list){
            echo json_encode($list);die;
        }else{
            echo 0;die;
        }
    }
}

