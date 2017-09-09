<?php
namespace Admin\Controller;
use Think\Controller;
class ActivityController extends CommonController
{
/*
 *执行添加活动
 */
 public function addActivity(){
      $category=M('ty_activity')->select(); 
    if(IS_POST){
    	$tmp=array(
             'name'=>I('post.infoname'),
             'displayorder'=>I('post.displayorder'),
             'status'=>I('post.status'),
             'content'=>html_entity_decode(I('post.content')),
             'createtime'=>time(),
         	);
   
    	$result=M('ty_activity')->add($tmp);
    	if($result){
           $this->success('添加成功!',U('Activity/activilist'));exit;
    	}else{
    		 $this->error('添加失败!');exit;
    	}

    }
    $this->assign('category',$category);
 	$this->display();
  }
/*
 *活动列表
 */
 public function activilist(){

   $list=D('ty_activity')->field('name,status,createtime,displayorder')->select();
   foreach ($list as $key => $value) {
    $list[$key]['name']=msubstr($value['name'],0,45);
   }
   $this->assign('data',$list);
   $this->display();

 }






















}