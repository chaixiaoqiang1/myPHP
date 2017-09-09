<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extends CommonController
{
    public function index(){
        $category=M('category')->where('pid=0')->select();
       // var_dump($category);

        $this->assign('category',$category);
        $this->display();
    }

    public function addChild(){

        $this->display();
    }
    public function editor(){

        $this->display();

    }

    public function update(){
        if(IS_POST){

        }
    }

    public function addMain(){
        
        $this->display();
    }
    public function addcate(){
        if(IS_POST){

        }
    }
    public function delete(){

    }
}