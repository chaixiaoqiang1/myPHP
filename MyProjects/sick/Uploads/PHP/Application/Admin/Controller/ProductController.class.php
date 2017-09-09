<?php
namespace Admin\Controller;
use Think\Controller;
class ProductController extends CommonController
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index(){

        $this->display();
    }



}