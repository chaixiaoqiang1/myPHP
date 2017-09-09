<?php
namespace Admin\Controller;
use Think\Controller;
class CententController extends CommonController
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index(){
        $title=I('title');
        if($title){
            $where['title']=array('like','%'.$title.'%');
        }
        $count=M('Centent')->where($where)->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $centent_list=M('Centent')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('centent_list',$centent_list);
        $this->display();
    }
    public function add(){
        if(IS_POST){
            $_POST['issue_id']=$_SESSION['666_admin_id'];
            $_POST['issue_name']=$_SESSION['666_admin_name'];
            $_POST['time']=date('Y-m-d H:i:s',time());
            $_POST['status']=1;
            $list=M('Centent')->create($_POST);
            $row=M('Centent')->data($list)->add();
            if($row){
                $this->success('添加成功',U('Centent/index'),1);die;
            }else{
                $this->error('添加失败');die;
            }
        }else{
            $this->display();
        }
    }
    public function editor(){
        $id=I('id');
        $list=M('Centent')->where(array('id'=>$id))->find();
        $this->assign('list',$list);
        $this->display();
    }
    public function update(){
        $post=I('post.');
        $html= htmlspecialchars_decode ($post['content1']);
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$html,$match);
        if(!empty($match[0])){
            $this_notice=M('Centent')->where('id='.$post['id'])->getField('info');
            $this_html= htmlspecialchars_decode ($this_notice);
            preg_match_all($pattern,$this_html,$img_arr);
            foreach($img_arr[1] as $k=>$v){
                if(!in_array($v,$match[1])){
                    unlink('.'.substr($v,5,100));
                }
            }
        }
        $post['info']=$post['content1'];
        $list=M('Centent')->create($post);
        $row=M('Centent')->data($list)->save();
        if($row){
            $this->success('修改成功',U('Centent/index'),1);die;
        }else{
            $this->error('修改失败');die;
        }
    }
    public function see(){
        $id=I('id');
        $list=M('Centent')->where(array('id'=>$id))->find();
        $list['info']=htmlspecialchars_decode($list['info']);
        $this->assign('list',$list);
        $this->display();
    }
    public function delete(){
        $id=I('id');
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        $this_notice=M('Centent')->where('id='.$id)->getField('info');
        $this_html= htmlspecialchars_decode ($this_notice);
        preg_match_all($pattern,$this_html,$img_arr);
        foreach($img_arr[1] as $k=>$v){
            unlink('.'.substr($v,5,100));
        }
        $row=M('Centent')->where(array('id'=>$id))->delete();
        if($row){
            $this->success('删除成功',U('Centent/index'),1);die;
        }else{
            $this->error('删除失败');die;
        }

    }



    public function banner(){
        $Advert = M('Advert');
        $Advert_list = $Advert->select();
        $this->assign("Advert_list" , $Advert_list);
        $this->display();
    }

    public function banner_add(){
        if(IS_POST){

            $up=$this->upload();
            $image_info=$up->uploadOne($_FILES['img_url']);
            if($image_info){
                $img='./Public/'.$image_info['savepath'].$image_info['savename'];
                $images = new \Think\Image();
                $images->open($img);

                $images->thumb(400, 200,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/'.$image_info['savepath'].'thumb_'.$image_info['savename']);
                //处理后图片地址
                $thumb_url='./Public/'.$image_info['savepath'].'thumb_'.$image_info['savename'];
                unlink($img);
                $data['img_url']=$thumb_url;
                $data['url']=$_POST['url'];
                $data['type']=$_POST['type'];
                M("Advert")->add($data);

                redirect(U('Centent/banner'));die;
            }
        }else{
            $this->display();
        }
    }


    public function banner_delete(){
        $id = $_GET["id"];
        $Advert_list=M("Advert")->where(array("id" => $id))->find();
        unlink($Advert_list['img_url']);
        M("Advert")->where(array("id" => $id))->delete();
        redirect(U('Centent/banner'));die;
    }


    public function bbs(){
        $title=I('title');
        if($title){
            $where['title']=array('like','%'.$title.'%');
        }
        $count=M('Bbs')->where($where)->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $Bbs_list=D('Bbs')->where($where)->relation(true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
      //  print_r($Bbs_list);die;
        $this->assign('page',$show);
        $this->assign('Bbs_list',$Bbs_list);
        $this->display();
    }

    public function bbs_editor(){
        $id=I('id');
        $ststus=I('status');
        M('Bbs')->where(array('id'=>$id))->data(array('status'=>$ststus))->save();
        redirect(U('Centent/bbs'));die;
    }

    public function bbs_delete(){
        $id=I('id');
        $list=M('Bbs')->where(array('id'=>$id))->find();
        if(empty($list)){
            $this->error('获取信息失败');
        }
        $img_list=M('Bbs_img')->where('bbs_id='.$id)->field('img_url')->select();
        foreach($img_list as $v){
            unlink($v);
        }
        M('Bbs_comment')->where('bbs_id='.$id)->delete();
        M('Bbs_img')->where('bbs_id='.$id)->delete();
        $row=M('Bbs')->where(array('id'=>$id))->delete();
        if($row){
            redirect(U('Centent/bbs'));die;
        }else{
            $this->error('删除失败');
        }
    }

    public function bbs_see(){
        $id=I('id');
        $list=M('Bbs')->where(array('id'=>$id))->find();
        if(empty($list)){
            $this->error('获取信息失败');
        }
        $list=D('Bbs')->where('id='.$id)->relation(true)->find();
        $com_list=D('Bbs_comment')->where('bbs_id='.$id)->relation(true)->select();
        $this->assign('list',$list);
        $this->assign('com_list',$com_list);
        $this->display();
    }

    public function bbs_banner(){
        $Ad = M('Ad');
        $Ad_list = $Ad->select();
        $this->assign("Ad_list" , $Ad_list);
        $this->display();
    }
    public function bbs_banner_add(){
        if(IS_POST){

            $up=$this->upload();
            $image_info=$up->uploadOne($_FILES['img_url']);
            if($image_info){
                $img='./Public/'.$image_info['savepath'].$image_info['savename'];
                $images = new \Think\Image();
                $images->open($img);

                $images->thumb(400, 200,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/'.$image_info['savepath'].'thumb_'.$image_info['savename']);
                //处理后图片地址
                $thumb_url='./Public/'.$image_info['savepath'].'thumb_'.$image_info['savename'];
                unlink($img);
                $data['img']=$thumb_url;
                M("Ad")->add($data);
                redirect(U('Centent/bbs_banner'));die;
            }
        }else{
            $this->display();
        }
    }


    public function bbs_banner_delete(){
        $id = $_GET["id"];
        $Advert_list=M("Ad")->where(array("id" => $id))->find();
        unlink($Advert_list['img']);
        M("Ad")->where(array("id" => $id))->delete();
        redirect(U('Centent/bbs_banner'));die;
    }


    public function help(){
        $title=I('title');
        if($title){
            $where['title']=array('like','%'.$title.'%');
        }
        $count=M('Help')->where($where)->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $help_list=M('Help')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('help_list',$help_list);
        $this->display();
    }

    public function help_delete(){
        $id=I('id');
        $list=M('Help')->where(array('id'=>$id))->find();
        if(empty($list)){
            $this->error('获取信息失败');
        }
        $row=M('Help')->where(array('id'=>$id))->delete();
        if($row){
            redirect(U('Centent/help'));die;
        }else{
            $this->error('删除失败');
        }
    }

    public function help_see(){
        $id=I('id');
        $list=M('Help')->where(array('id'=>$id))->find();
        if(empty($list)){
            $this->error('获取信息失败');
        }
        $list=M('Help')->where('id='.$id)->find();
        $this->assign('list',$list);
        $this->display();
    }
    public function help_editor(){
        $id=I('id');
        $list=M('Help')->where(array('id'=>$id))->find();
        $this->assign('list',$list);
        $this->display();
    }
    public function help_update(){
        $post=I('post.');

        $html= htmlspecialchars_decode ($post['content1']);
        $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$html,$match);
        if(!empty($match[0])){
            $this_notice=M('Help')->where('id='.$post['id'])->getField('content');
            $this_html= htmlspecialchars_decode ($this_notice);
            preg_match_all($pattern,$this_html,$img_arr);
            foreach($img_arr[1] as $k=>$v){
                if(!in_array($v,$match[1])){
                    unlink('.'.substr($v,5,100));
                }
            }
        }
        $post['content']=$post['content1'];
        $list=M('Help')->create($post);
        $row=M('Help')->data($list)->save();
        if($row){
            $this->success('修改成功',U('Centent/help'),1);die;
        }else{
            $this->error('修改失败');die;
        }
    }


    public function help_add(){
        if(IS_POST){

            $_POST['content']= $_POST['info'];
            $_POST['status']=1;
            $list=M('Help')->create($_POST);
            $row=M('Help')->data($list)->add();
            if($row){
                $this->success('添加成功',U('Centent/help'),1);die;
            }else{
                $this->error('添加失败');die;
            }
        }else{
            $this->display();
        }
    }


    public function search(){
        if(IS_POST){
            $row=M('Search')->save($_POST);
            if($row){
                $this->success('添加成功',U('Centent/search'),1);die;
            }else{
                $this->error('添加失败',U('Centent/search'),1);die;
            }
        }else{
            $list=M('Search')->find();
            $this->assign('list',$list);
            $this->display();
        }

    }
}