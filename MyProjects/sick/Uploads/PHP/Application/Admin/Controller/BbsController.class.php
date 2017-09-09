<?php
namespace Admin\Controller;
use Think\Controller;
class BbsController extends CommonController
{

    public function __construct()
    {
        parent::__construct();

    }
    //活动首页
    public function index(){
        $this->display();
    }

    //需求首页
    public function need(){

        $this->display();
    }




    //技能首页
    public function skill(){

        $this->display();
    }


}