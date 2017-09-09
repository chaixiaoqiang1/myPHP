<?php
namespace Admin\Controller;
use Think\Controller;

class MerchantController  extends CommonController{
     public function index(){

        $this->display();
    }

    public function add()
    {
        if($_POST){
        }else{
            $this->display();
        }
    }

    public function editor(){
        if(IS_POST){

        }
        $this->display();
    }
    public function delete(){

    }
}
