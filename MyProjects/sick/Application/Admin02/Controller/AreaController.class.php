<?php
namespace Admin\Controller;
use Think\Controller;
class AreaController extends CommonController
{

    public function __construct()
    {
        parent::__construct();

    }
    //首页
    public function index(){
        $this->display();
    }

}