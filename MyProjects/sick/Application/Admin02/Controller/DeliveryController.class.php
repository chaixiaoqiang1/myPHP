<?php
namespace Admin\Controller;
use Think\Controller;
class DeliveryController extends CommonController
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index(){

        $this->display();
    }
}