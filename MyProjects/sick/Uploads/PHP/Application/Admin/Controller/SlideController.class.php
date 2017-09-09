<?php
namespace Admin\Controller;
use Think\Controller;
class SlideController extends CommonController
{
    public function index(){
        $count=M('Slide')->where($where)->count();
        $Page= new \Think\Page($count,15);
        setPage($Page);
        $show= $Page->show();
        $product_list=M('Slide')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->order("sort desc")->select();
       
        $this->assign('page',$show);
        $this->assign('data',$product_list);
        $this->display();
    }

    public function editor(){
        $id=I('id');
        if(IS_POST){
                $data = I('post.');
                $slide_pic =  M('Slide')->where("id = $id")->getField('slide_pic');
                if($_FILES['slide_pic']['error'] == 0){
                        $p = one_pic($_FILES['slide_pic']);
                        if(!$p){
                        $this->error('轮播图片上传失败');exit;
                        }
                        $img = oneThumb($p,'slide',400,200);
                        $data['slide_pic'] = $img;
                        unlink($slide_pic);
                }
              
                if(M('Slide')->save($data) ){
                     $this->success('修改成功',U('Slide/index'));exit;
                }else{
                     $this->error('修改失败');die;
                }
        }else{
                $list=M('slide')->where(array('id'=>$id))->find();
                $this->assign('data',$list);   
                $this->display();
        }
    }
 
    public function delete(){
        $id=I('id');
        $slide_img=M('slide')->where(array('id'=>$id))->getField('slide_pic');
       
        if( M('slide')->delete($id)){
            unlink($slide_img);
            $this->success('删除成功',U('slide/index'));die;
        }else{
            $this->error('删除失败');die;
        }
    }
    public function add(){
       if(IS_POST){
             $data = I('post.');
             $p = one_pic($_FILES['slide_pic']);
             if(!$p){
                $this->error('商品图片上传失败');exit;
             }
             $img = oneThumb($p,'slide',400,200);
             $data['slide_pic'] = $img;
             if(M('Slide')->add($data) ){
                  $this->success('添加成功',U('Slide/index'));exit;
             }else{
                  $this->error('添加失败');die;
             }
       } else {
            $this->display();
       }
    }

}