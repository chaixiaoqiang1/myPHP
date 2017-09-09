<?php
namespace Admin\Controller;
use Think\Controller;
class MessageController extends CommonController
{
 /*
 *资讯类别列表
 */
 public function classeslist(){
 	$count=M('ty_classeslist')->where($where)->count();
    $Page= new \Think\Page($count,15);
    setPage($Page);
    $show= $Page->show();

 	$list=M('ty_classeslist')->limit($Page->firstRow.','.$Page->listRows)->select();

    $this->assign('page',$show);
    $this->assign('data',$list);
 	  $this->display();
 }

 /*
 *执行咨询类别添加
 */
 public function doaddclass(){
 	if(IS_POST){
         $tmp=array(
           'catename'=>I('post.catename'),
           'status'=>I('post.status'),
           'displayorder'=>I('post.displayorder'),
         	);
        $result=D('ty_classeslist')->add($tmp);

	    if($result){
	       $this->success('添加成功!',U('Message/classeslist'),1);
	    }
 	}else{
 		     $this->error('非法请求!');
 	}
  }
 /*
 *添加资讯信息
 */
 public function addInfo(){
    $category=M('ty_classeslist')->select(); 
    if(IS_POST){
    	$tmp=array(
             'pid' => I('post.pid'),
             'infoname'=>I('post.infoname'),
             'displayorder'=>I('post.displayorder'),
             'status'=>I('post.status'),
             'content'=>html_entity_decode(I('post.content')),
             'createtime'=>time(),
         	);

    	$result=M('ty_messageinfo')->add($tmp);
    	if($result){
           $this->success('添加成功!',U('Message/infolist'));exit;
    	}else{
    		 $this->error('添加失败!');exit;
    	}

    }
    $this->assign('category',$category);
 	  $this->display();
 }
/*
 *资讯类别列表
 */
 public function infolist(){
   $list=D('ty_messageinfo')->field('infoname,status,createtime,displayorder,pid')->select();
   foreach ($list as $key => $value) {
    $list[$key]['infoname']=msubstr($value['infoname'],0,45);
   }
   $this->assign('data',$list);
   $this->display();

 }



 }
